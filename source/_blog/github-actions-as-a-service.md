---
extends: _layouts.post
section: content
title: GitHub Actions as a Service
date: 2021-05-20
keywords: github,actions,pr,fixer,automatic,custom
image: /assets/images/blog/github-actions-as-a-service/social.jpg
---

It's no secret that over at [Laravel](https://laravel.com) we host our code on GitHub.
However, because the code to some of our platforms (such as [Forge](https://forge.laravel.com)) is proprietary, we try to be careful regarding which services or integrations have access to that code.
<!--more-->

As a result, we haven't had any services auto-review our PR's for inconsistencies or simple bugs, and primarily had to rely
on offline tools such as [PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) and [ESLint](https://eslint.org/) to get the job done.

Now, while most of these tools are pretty great, they unfortunately don't run automatically when you create a PR.
Of course, you could configure git hooks with tools such as [Husky](https://github.com/typicode/husky), but in my experience those often
tend to get in the way, both because most people configure their machines slightly differently, as well as that they don't give other people
on the team any insights into whether all checks are passing, or whether git hooks were [skipped altogether](https://git-scm.com/docs/git-push#Documentation/git-push.txt---no-verify).

So, naturally, when I joined Laravel, one of the first things I did was find a solution to this problem.
Ideally, it would do all of the following, without user interaction, and without us launching a new first-party service:

- Run whenever a PR is created, and fail the PR if there's style inconsistencies.
- Create a PR that applies the fixes, and target the PR that's under review.
- Show up as a comment to the original PR, so the creator can easily see the relevant Fix PR.
- Re-run when new commits are added, and re-use the existing Fix PR if one is still open.

And about ~30 `WIP` commits later, here's what we ended up with:

![GIF image showcasing the Fixer PR process](/assets/images/blog/github-actions-as-a-service/process.gif)

# The Action Itself

So, let's go over how we've accomplished this, and how you can create any number of these yourself, without the need for too much effort.
First of all, here's the script for a PHP-CS-Fixer in all it's glory:

```yaml
name: php-cs-fixer

on: [push, pull_request]

env:
  PR_NUMBER: "${{ github.event.number }}"
  SOURCE_BRANCH: "$GITHUB_HEAD_REF"
  FIXER_BRANCH: "auto-fixed/$GITHUB_HEAD_REF"
  TITLE: "Apply fixes from PHP-CS-Fixer"
  DESCRIPTION: "This merge request applies PHP code style fixes from an analysis carried out through GitHub Actions."

jobs:
  php-cs-fixer:
    if: github.event_name == 'pull_request' && ! startsWith(github.ref, 'refs/heads/auto-fixed/')
    runs-on: ubuntu-20.04

    name: Run PHP CS Fixer

    steps:
      - name: Checkout Code
        uses: actions/checkout@v2

      - name: Setup PHP
        uses: shivammathur/setup-php@2.7.0
        with:
          php-version: 7.4
          extensions: json, dom, curl, libxml, mbstring
          coverage: none

      - name: Install PHP-CS-Fixer
        run: |
          curl -L https://github.com/FriendsOfPHP/PHP-CS-Fixer/releases/download/v2.18.6/php-cs-fixer.phar -o .github/build/php-cs-fixer
          chmod a+x .github/build/php-cs-fixer

      - name: Prepare Git User
        run: |
          git config --global user.name "github-actions[bot]"
          git config --global user.email "41898282+github-actions[bot]@users.noreply.github.com"
          git checkout -B "${{ env.FIXER_BRANCH }}"

      - name: Apply auto-fixers
        run: php .github/build/php-cs-fixer fix

      - name: Create Fixer PR
        run: |
          if [[ -z $(git status --porcelain) ]]; then
            echo "Nothing to fix.. Exiting."
            exit 0
          fi
          OPEN_PRS=`curl --silent -H "Accept: application/vnd.github.v3+json" -H "Authorization: Bearer ${{ secrets.GITHUB_TOKEN }}" "https://api.github.com/repos/$GITHUB_REPOSITORY/pulls?state=open"`
          OPEN_FIXER_PRS=`echo ${OPEN_PRS} | grep -o "\"ref\": \"${{ env.FIXER_BRANCH }}\"" | wc -l`
          git commit -am "${{ env.TITLE }}"
          git push origin "${{ env.FIXER_BRANCH }}" --force
          if [ ${OPEN_FIXER_PRS} -eq "0" ]; then
            curl -X POST \
              -H "Accept: application/vnd.github.v3+json" \
              -H "Authorization: Bearer ${{ secrets.GITHUB_TOKEN }}" \
              "https://api.github.com/repos/$GITHUB_REPOSITORY/pulls" \
              -d "{ \"head\":\"${{ env.FIXER_BRANCH }}\", \"base\":\"${{ env.SOURCE_BRANCH }}\", \"title\":\"${{ env.TITLE }}\", \"body\":\"${{ env.DESCRIPTION }}\n\nTriggered by #${{ env.PR_NUMBER }}\" }"
          fi
          exit 1
```

# How it works

Now, let's break it down. Starting off, one thing I'll mention is that I'll be be jumping around the script while explaining
instead of just starting at the very top, so you can actually get to understand why things are defined/used the way they are.
I promise that by the end of it, we'll have covered all of it.

## About GitHub Actions
So, first of all, I'm assuming you've already worked with GitHub Actions, and therefore will probably recognize some of the sections.
If not, here's the "generic" bits that you'll need to understand:

```yaml
name: name-of-the-workflow

on: [list,of,events,which,should,trigger,this,workflow]

env:
  environmentKey: value

jobs:
  job-key-unique-to-this-workflow:
    if: some-condition-that-determines-whether-this-job-should-run-or-be-skipped
    runs-on: operating-system-that-the-job-should-run-on

    name: Pretty display name of the job

    steps:
      - name: Name of the first step
        otherThings: Some supported thing to actually execute
```

For a more in depth explanation, I'd recommend you to [check out the syntax here](https://docs.github.com/en/actions/reference/workflow-syntax-for-github-actions).

## The 'if'-statement

So, one thing that we'll see straight away, is that we have a somewhat complex `if`-statement here:

```yaml
jobs:
  php-cs-fixer:
    if: github.event_name == 'pull_request' && ! startsWith(github.ref, 'refs/heads/auto-fixed/')
    runs-on: ubuntu-20.04

    name: Run PHP CS Fixer
```

#### `github.event_name == 'pull_request'`
As you can see, we're explicitly making sure that the `event_name` is a `pull_request`, and the reason that we do this
instead of just removing the `on: ['push']` from the top of the workflow, is to make it re-runs every time we push a new commit to the branch _for which a pull request exists_. 

One downside of this is that you'll always get _two_ 'actions' that are running, one of which is always _skipped_, but as far as I'm aware, this is unfortunately the best we can do for now.

#### `! startsWith(github.ref, 'refs/heads/auto-fixed/')`
Then, there's the second part. This basically ensures that when a Fixer PR is created, the action doesn't run for those by checking that the branch itself starts with `auto-fixed/`.
There's a few reasons we do this:

1. It prevents infinite loops (and also prevents Fixer PR's from creating Fixer PR's)
2. It saves us from wasting precious [GitHub Actions minutes](https://docs.github.com/en/github/setting-up-and-managing-billing-and-payments-on-github/about-billing-for-github-actions)
3. If there's breakage, we'll notice it as soon as we merge in the Fixer PR, as merges are treated as 'push' events.


## Checking out our code

I don't think this needs any explanation, but our first step is to basically checkout / pull the code from the commit that triggered it:

```yaml
- name: Checkout Code
  uses: actions/checkout@v2
```

## Installing PHP & PHP-CS-Fixer

Next, here's one part that you might want to customize depending on the type of fixer you're creating.

```yaml
- name: Setup PHP
  uses: shivammathur/setup-php@2.7.0
  with:
    php-version: 7.4
    extensions: json, dom, curl, libxml, mbstring
    coverage: none

- name: Install PHP-CS-Fixer
  run: |
    curl -L https://github.com/FriendsOfPHP/PHP-CS-Fixer/releases/download/v2.18.6/php-cs-fixer.phar -o .github/build/php-cs-fixer
    chmod a+x .github/build/php-cs-fixer
```

As you can see, we're first setting up PHP 7.4 with a few extensions, using [shivammathur/setup-php](https://github.com/shivammathur/setup-php).
If you haven't used this before, it's surprisingly fast, and in contrast to a normal PHP install, it only takes a few seconds to execute instead of minutes.

Afterwards, we downloading the [PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) itself to `.github/build`, so if you're planning to use this workflow, make sure you create this (non-existent) folder first.
I recommend placing a `.gitignore` file inside of it, with the following contents:

```gitignore
*
!.gitignore
```

This way, the [PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) doesn't constantly commit it's downloaded binaries to your repository through it's Fixer PR's.
Of course, do keep in mind that **you might not need to do this for other types of fixers**, but for this specific example, it's fairly important.

## Setting up the GitHub Actions 'bot' user.

Next, we'll configure `git` and check out our Fixer branch. 

You might wonder where we got the `name` and `email` from, and the answer to that's pretty simple: It's what GitHub uses!
While the name was pretty easy to discover, we only found it's email by going through [GitHub's events API for that user](https://api.github.com/users/github-actions%5Bbot%5D/events/public).

> My colleague [@jbrooksuk](https://twitter.com) did note that the bot might've switched to use `action@github.com`,
but by the time he mentioned this I had already configured this one email, so 🤷‍♂️

```yaml
- name: Prepare Git User
  run: |
    git config --global user.name "github-actions[bot]"
    git config --global user.email "41898282+github-actions[bot]@users.noreply.github.com"
    git checkout -B "${{ env.FIXER_BRANCH }}"
```

Finally, we check out the Fixer branch, using GitHub's `-B` flag. If you're not familiar with this flag, [it either creates or resets the branch if it already exists](https://git-scm.com/docs/git-checkout#Documentation/git-checkout.txt-emgitcheckoutem-b-Bltnewbranchgtltstartpointgt).

## The `env` variables

As you might've noticed, this is also the first time we were referencing the `env` variables, which are configured near the top of the script.
You can only define these once per GitHub Actions workflow file, so let's go over those real quick:

```yaml
env:
  PR_NUMBER: "${{ github.event.number }}"
  SOURCE_BRANCH: "$GITHUB_HEAD_REF"
  FIXER_BRANCH: "auto-fixed/$GITHUB_HEAD_REF"
  TITLE: "Apply fixes from PHP-CS-Fixer"
  DESCRIPTION: "This merge request applies PHP code style fixes from an analysis carried out through GitHub Actions."
```

I believe what we're doing here speaks for itself as well, but one thing I would like to mention is that it apparently isn't possible
to reference environment variables that are already defined. This is why we ended up having to use the `$GITHUB_HEAD_REF` variable twice.

## Running the fixers

Finally, this is the step where our fixers are run. So, let's run our PHP-CS-Fixer:

```yaml
- name: Apply auto-fixers
  run: php .github/build/php-cs-fixer fix
```

## (Possibly) creating a Fix PR

Let's split this script up even further, and step through it section by section, as this is the meat and potatoes of the whole workflow.

```yaml
- name: Create Fixer PR
  run: |
    if [[ -z $(git status --porcelain) ]]; then
      echo "Nothing to fix.. Exiting."
      exit 0
    fi
    OPEN_PRS=`curl --silent -H "Accept: application/vnd.github.v3+json" -H "Authorization: Bearer ${{ secrets.GITHUB_TOKEN }}" "https://api.github.com/repos/$GITHUB_REPOSITORY/pulls?state=open"`
    OPEN_FIXER_PRS=`echo ${OPEN_PRS} | grep -o "\"ref\": \"${{ env.FIXER_BRANCH }}\"" | wc -l`
    git commit -am "${{ env.TITLE }}"
    git push origin "${{ env.FIXER_BRANCH }}" --force
    if [ ${OPEN_FIXER_PRS} -eq "0" ]; then
      curl -X POST \
        -H "Accept: application/vnd.github.v3+json" \
        -H "Authorization: Bearer ${{ secrets.GITHUB_TOKEN }}" \
        "https://api.github.com/repos/$GITHUB_REPOSITORY/pulls" \
        -d "{ \"head\":\"${{ env.FIXER_BRANCH }}\", \"base\":\"${{ env.SOURCE_BRANCH }}\", \"title\":\"${{ env.TITLE }}\", \"body\":\"${{ env.DESCRIPTION }}\n\nTriggered by #${{ env.PR_NUMBER }}\" }"
    fi
   exit 1
```

### Do we have any changes?
 
First, we'll call [`git status --porcelain`](https://git-scm.com/docs/git-status#Documentation/git-status.txt---porcelainltversiongt),
which will just give us a list of files that have changed. If there are no changes, it'll output an empty string. We can then test this using `bash`'s empty string test (`-z`):

```bash
if [[ -z $(git status --porcelain) ]]; then
  echo "Nothing to fix.. Exiting."
  exit 0
fi
```

If we don't have any changes, we'll print that to the console (easier debugging), and exit with a `0` status, meaning that the process finished successfully / without errors.
We have nothing to do, hurray! Good job developer!

GitHub Actions will interpret this exit code as a passing check on the PR. Similarly, exiting with a `1` status would cause the GitHub Actions check to fail.

### Obtaining a list of open PR's

However, if we do have changes (meaning we need a Fix PR), we'll [fetch a list of open PR's using the GitHub API](https://docs.github.com/en/rest/reference/pulls#list-pull-requests), and store the output in a bash variable.
We do this to ensure we only create a PR if one does not already exist:

```bash
OPEN_PRS=`curl --silent -H "Accept: application/vnd.github.v3+json" -H "Authorization: Bearer ${{ secrets.GITHUB_TOKEN }}" "https://api.github.com/repos/$GITHUB_REPOSITORY/pulls?state=open"`
```

The `--silent` flag here makes sure that curl doesn't output any connection details, and the rest is basically the crafting of a a JSON request.
What's interesting/relevant here, is that we're using a few magic variables:

- **`${{ secrets.GITHUB_TOKEN }}`**: This is [an automatically created GitHub secret](https://docs.github.com/en/actions/reference/authentication-in-a-workflow#about-the-github_token-secret) that can be used to authenticate with GitHub.
This is what allows us to both call the API to fetch the open PR's, even on private repositories.

- **`$GITHUB_REPOSITORY`**: This is a [default environment variable](https://docs.github.com/en/actions/reference/environment-variables#default-environment-variables) that's also set by GitHub Actions.
This allows us to not have to hard-code the current repository in the script. Nice!
  
### Does an (open) Fix PR already exist?
 
Next, we'll check whether our `OPEN_PRS` variable contains a PR that originates from our Fixer branch.
To do this, we'll first use [`grep`](https://linux.die.net/man/1/grep) with the `-o` flag, which will print every matching result on a separate line.

```bash
OPEN_FIXER_PRS=`echo ${OPEN_PRS} | grep -o "\"ref\": \"${{ env.FIXER_BRANCH }}\"" | wc -l`
```

We then pipe this output to `wc -l`, which counts the number of lines, and store that number in an `OPEN_FIXER_PRS` variable for future use.

### Committing our changes

With that done, we'll create a commit with our changes, and force push them to our fixer branch.
 
```bash
git commit -am "${{ env.TITLE }}"
git push origin "${{ env.FIXER_BRANCH }}" --force
```

The reason we're using a force push here, is because we really only care about the latest changes/fixes, as well as that it prevents accidental merge conflicts.

### Creating the actual Fix PR

Finally, we'll check whether all we needed to do was push, or whether we need to create a Fix PR as well.
This is done using by checking the number of `OPEN_FIXER_PRS`:

```bash
if [ ${OPEN_FIXER_PRS} -eq "0" ]; then
  curl -X POST \
    -H "Accept: application/vnd.github.v3+json" \
    -H "Authorization: Bearer ${{ secrets.GITHUB_TOKEN }}" \
    "https://api.github.com/repos/$GITHUB_REPOSITORY/pulls" \
    -d "{ \"head\":\"${{ env.FIXER_BRANCH }}\", \"base\":\"${{ env.SOURCE_BRANCH }}\", \"title\":\"${{ env.TITLE }}\", \"body\":\"${{ env.DESCRIPTION }}\n\nTriggered by #${{ env.PR_NUMBER }}\" }"
fi
```

If there's no existing PR's, we'll create one. 

You'll notice that we're creating a JSON payload here, with the Fixer PR's as the source (`head`), the current PR's branch as the target (`base`),
the title that we've configured in the `env` details at the top of the script (to keep things simpler down here), as well as our description (`body`).

As a cherry on the top, we append a few blank lines to the `body` field (our description), and reference the original PR's number.
This will trigger GitHub to create a reference to the Fix PR in the details / on the timeline of the original PR, making it easier for the PR's creator and reviewers to find.

Finally, we'll always fail our check by exiting with an `exit 1` (error) status code, regardless of whether we're creating a new PR or not.
As mentioned before, this causes the GitHub Actions check to fail on the PR.

Our workflow is now complete.

# A very similar Javascript-based Fixer

Another workflow we have is almost identical, but instead fixes Javascript and Vue code style inconsistencies.
To show how easy it is to create a new fixer, we'll first copy the entire workflow from earlier, and swap out the relevant parts:

```diff
-name: php-cs-fixer
+name: js-cs-fixer

 on: [push, pull_request]

 env:
   PR_NUMBER: "${{ github.event.number }}"
   SOURCE_BRANCH: "$GITHUB_HEAD_REF"
   FIXER_BRANCH: "auto-fixed/$GITHUB_HEAD_REF"
-  TITLE: "Apply fixes from PHP-CS-Fixer"
-  DESCRIPTION: "This merge request applies PHP code style fixes from an analysis carried out through GitHub Actions."
+  TITLE: "Apply fixes from JS-CS-Fixer"
+  DESCRIPTION: "This merge request applies JS code style fixes from an analysis carried out through GitHub Actions."

 jobs:
-  php-cs-fixer:
+  js-cs-fixer:
     if: github.event_name == 'pull_request' && ! startsWith(github.ref, 'refs/heads/auto-fixed/')
     runs-on: ubuntu-20.04

-    name: Run PHP CS Fixer
+    name: Run JS CS Fixer

     steps:
       - name: Checkout Code
         uses: actions/checkout@v2

-      - name: Setup PHP
-        uses: shivammathur/setup-php@2.7.0
-        with:
-          php-version: 7.4
-          extensions: json, dom, curl, libxml, mbstring
-          coverage: none
-
-      - name: Install PHP-CS-Fixer
-        run: |
-          curl -L https://github.com/FriendsOfPHP/PHP-CS-Fixer/releases/download/v2.18.6/php-cs-fixer.phar -o .github/build/php-cs-fixer
-          chmod a+x .github/build/php-cs-fixer
+      - name: Set up Node & NPM
+        uses: actions/setup-node@v2
+        with:
+          node-version: '14.x'
+
+      - name: Get yarn cache directory path
+        id: yarn-cache-dir-path
+        run: echo "::set-output name=dir::$(yarn cache dir)"
+ 
+      - uses: actions/cache@v2
+        id: yarn-cache
+        with:
+          path: ${{ steps.yarn-cache-dir-path.outputs.dir }}
+          key: ${{ runner.os }}-yarn-${{ hashFiles('**/yarn.lock') }}
+          restore-keys: ${{ runner.os }}-yarn-
+
+      - name: Install yarn project dependencies
+        run: yarn

       - name: Prepare Git User
         run: |
           git config --global user.name "github-actions[bot]"
           git config --global user.email "41898282+github-actions[bot]@users.noreply.github.com"
           git checkout -B "${{ env.FIXER_BRANCH }}"

       - name: Apply auto-fixers
+        run: yarn fix-code-style
-        run: php .github/build/php-cs-fixer fix

       - name: Create Fixer PR
         run: |
           if [[ -z $(git status --porcelain) ]]; then
             echo "Nothing to fix.. Exiting."
             exit 0
           fi
           OPEN_PRS=`curl --silent -H "Accept: application/vnd.github.v3+json" -H "Authorization: Bearer ${{ secrets.GITHUB_TOKEN }}" "https://api.github.com/repos/$GITHUB_REPOSITORY/pulls?state=open"`
           OPEN_FIXER_PRS=`echo ${OPEN_PRS} | grep -o "\"ref\": \"${{ env.FIXER_BRANCH }}\"" | wc -l`
           git commit -am "${{ env.TITLE }}"
           git push origin "${{ env.FIXER_BRANCH }}" --force
           if [ ${OPEN_FIXER_PRS} -eq "0" ]; then
             curl -X POST \
               -H "Accept: application/vnd.github.v3+json" \
               -H "Authorization: Bearer ${{ secrets.GITHUB_TOKEN }}" \
               "https://api.github.com/repos/$GITHUB_REPOSITORY/pulls" \
               -d "{ \"head\":\"${{ env.FIXER_BRANCH }}\", \"base\":\"${{ env.SOURCE_BRANCH }}\", \"title\":\"${{ env.TITLE }}\", \"body\":\"${{ env.DESCRIPTION }}\n\nTriggered by #${{ env.PR_NUMBER }}\" }"
           fi
           exit 1
```

In this case, instead of installing PHP like before, we install Node.js, as well as our project's dependencies (which in our case includes [Prettier](https://prettier.io/) and [ESLint](https://eslint.org/))

Because installing Node dependencies _can_ take a long time, we also prepare a [yarn](https://yarnpkg.com/) cache up front, that GitHub Actions will automatically re-use on sequential 'builds'.

Finally, you can see we run `yarn fix-code-style`, which is a custom command that's defined within our project's `package.json`. Here's the relevant bits:

```json
{
  "scripts": {
    "fix:eslint": "eslint --ext .js,.vue resources/js/ --fix",
    "fix:prettier": "prettier --write --loglevel warn 'resources/js/**/*.js' 'resources/js/**/*.vue'",
    "fix-code-style": "npm run fix:prettier && npm run fix:eslint"
  },
  "devDependencies": {
    "eslint": "^7.25.0",
    "eslint-plugin-vue": "^7.9.0",
    "prettier": "^2.3.0"
  }
}
```

Setting up these 'scripts' wasn't really necessary, but it gives us an easy way to run both [Prettier](https://prettier.io/) and [ESLint](https://eslint.org/) (in the configured order) locally as well.

Hope this was useful!

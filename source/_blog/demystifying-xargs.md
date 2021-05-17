---
extends: _layouts.post
section: content
title: Demystifying xargs
date: 2020-07-18
keywords: unix,cli,commands,xargs
---

Today I ran into a situation where I needed to run a single command tons of times, and didn't want to sit around
waiting for each one to be done.<!--more-->

I knew `xargs` existed, but because it doesn't just run a single command and call it a day, I always kind of felt
that it was a big scary dangerous tool that I'd be better off just avoiding.

Today, I decided: no more. Let's demystify `xargs`!

---

One thing that I was already aware of, is that `xargs` basically takes the `stdin` input, splits it by
newline characters (as well as all unescaped blanks), and then loops over them, appending the value
to the command given and running that.

For example, the following takes each JSON file in a directory, and essentially loops over it, running
`cat <filename>.json` on each and every one of them:

```bash
ls -1 *.json | xargs cat
```

This wasn't what I needed, but I did need something similar, and I knew `xargs` to be more powerful.
To give a bit more context: I needed to call an API for each filename in a list.

So, after [referencing the manual](https://explainshell.com/explain/1/xargs), I quickly discovered it's ability to
use placeholder values in commands, similar to how translation placeholders work in (web)apps. An example:
```bash
#                        V-- Define the placeholder                V-- This'll get replaced
cat files.txt | xargs -I % http -f POST httpbin.org/post filename="%" token="secret" --ignore-stdin
```

Awesome. That works. It's still executing things one by one (which is actually what I needed it to do), but another
useful thing that I discovered while reading the manual, is that we can actually split the work over multiple
processes, which will then run in parallel. To do this, simply add the `-P 2` flag, and done, now there's two workers!
```bash
#                     VVVV-- Defines the maximum amount of concurrent worker processes
cat files.txt | xargs -P 2 -I % http -f POST httpbin.org/post filename="%" token="secret" --ignore-stdin
```

Looking back, I realized that I could've of course just created a bash file and done a for-loop, but hey,
today I learned an awesome new thing, and it'll definitely stay in my toolkit!

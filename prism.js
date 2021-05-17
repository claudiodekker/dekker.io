const fs = require('fs');
const Prism = require('prismjs')
const loadLanguages = require('prismjs/components/')

const [runtime, file, ...args] = process.argv
const [language] = args
const code = fs.readFileSync(process.stdin.fd, 'utf-8');

loadLanguages([language])

/**
 * First, we'll trim off any unnecessary whitespace-characters from the beginning and end.
 */
const trimmed = code.replace(/^\s+|\s+$/g, '');

/**
 * Next, we'll check whether we have the given language. If not, we'll just return
 * our content, which will cause the output to be displayed as-is.
 */
if (! Prism.languages[language]) {
    process.stdout.write(trimmed)
    process.exit(0)
}

/**
 * Since at this point Prism does have our requested language loaded, we'll run it.
 */
process.stdout.write(Prism.highlight(trimmed, Prism.languages[language], language))
process.exit(0)

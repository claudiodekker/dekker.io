<style>
    .prose > * + * { margin-top: 1.35em; }
    .prose p, .prose li { text-wrap: pretty; }
    .prose h1, .prose h2, .prose h3, .prose h4 { margin-top: 2.2em; line-height: 1.3; font-weight: 600; letter-spacing: -.015em; color: var(--color-ink); }
    .prose h1 { font-size: 26px; }
    .prose h2 { font-size: 22px; }
    .prose h3 { font-size: 19px; }
    .prose h4 { font-size: 17px; }
    .prose a { color: var(--color-accent); text-decoration: underline; text-decoration-thickness: 1px; text-underline-offset: .22em; text-decoration-color: color-mix(in srgb, currentColor 45%, transparent); }
    .prose a:hover { color: var(--color-ink); }
    .prose strong { font-weight: 600; color: var(--color-ink); }
    .prose ul, .prose ol { padding-left: 1.3em; }
    .prose ul { list-style: disc; }
    .prose ol { list-style: decimal; }
    .prose li + li { margin-top: .4em; }
    .prose li::marker { color: var(--color-dim); }
    .prose hr { height: 1px; border: 0; background: var(--color-line); margin: 2.6em 0; }
    .prose blockquote { padding: 2px 0 2px 18px; border-left: 2px solid var(--color-accent); color: var(--color-ink); }
    .prose img { border: 1px solid var(--color-line); border-radius: 8px; }
    .prose table { width: 100%; font-size: 15px; }
    .prose th, .prose td { padding: 8px 12px; border-bottom: 1px solid var(--color-line); text-align: left; }
    .prose th { font-weight: 600; color: var(--color-ink); }

    .prose code { font-size: .86em; font-weight: 400; }
    .prose :not(pre) > code { padding: 2px 6px; border: 1px solid var(--color-line); border-radius: 6px; background: var(--code-inline-surface); color: var(--code-inline); overflow-wrap: anywhere; }
    .prose pre { margin: 1.7em 0; border: 1px solid var(--color-line); border-radius: 8px; background: var(--code-surface); overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .prose pre code { display: block; min-width: max-content; padding: 12px 0; font-size: 13.5px; line-height: 24px; background: none !important; }
    .prose pre .line { padding-right: 18px; }
    .prose pre .line-number, .prose pre .summary-caret { display: inline-block; margin-right: 14px; padding: 0 12px 0 18px; }

    .prose pre { --diff-add: #2ea04326; --diff-add-gutter: #3fb9504d; --diff-add-number: #3fb950; --diff-remove: #f851491a; --diff-remove-gutter: #f851494d; --diff-remove-number: #f85149; }
    .prose pre .diff-add { background: var(--diff-add); }
    .prose pre .diff-add .line-number { background: var(--diff-add-gutter); color: var(--diff-add-number) !important; }
    .prose pre .diff-remove { background: var(--diff-remove); }
    .prose pre .diff-remove .line-number { background: var(--diff-remove-gutter); color: var(--diff-remove-number) !important; }

    @media (prefers-color-scheme: light) {
        .prose pre { --diff-add: #dafbe1; --diff-add-gutter: #aceebb; --diff-add-number: #1a7f37; --diff-remove: #ffebe9; --diff-remove-gutter: #ffcecb; --diff-remove-number: #d1242f; }
        .prose pre code, .prose pre code span { color: var(--phiki-light-color) !important; }
    }

    @media (max-width: 767px) {
        .prose pre { margin-left: -24px; margin-right: -24px; border-left: 0; border-right: 0; border-radius: 0; }
    }
</style>

const path = decodeURIComponent(location.pathname)

if (path !== '/404.html' && path !== '/404') {
    const requested = document.createElement('code')
    requested.className = 'font-mono text-[.9em] text-ink [overflow-wrap:anywhere]'
    requested.textContent = path.length > 48 ? `${path.slice(0, 48)}…` : path

    document.querySelector('[data-missing]').replaceChildren(requested, " isn't a page here.")
}

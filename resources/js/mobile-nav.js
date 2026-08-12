const drawer = document.getElementById('mobile-drawer')
const panel = document.getElementById('mobile-drawer-panel')
const backdrop = document.getElementById('mobile-drawer-backdrop')
const openButton = document.getElementById('mobile-menu-button')
const closeButton = document.getElementById('mobile-menu-close')

if (drawer && panel && backdrop && openButton) {
    let closeTimeout = null

    function openDrawer() {
        if (closeTimeout) {
            clearTimeout(closeTimeout)
            closeTimeout = null
        }

        drawer.classList.remove('hidden')
        document.body.classList.add('overflow-hidden')
        openButton.setAttribute('aria-expanded', 'true')

        // Next frame so the transition animates from the off-screen state.
        requestAnimationFrame(() => {
            panel.classList.remove('translate-x-full')
            backdrop.classList.remove('opacity-0')
        })
    }

    function closeDrawer() {
        panel.classList.add('translate-x-full')
        backdrop.classList.add('opacity-0')
        document.body.classList.remove('overflow-hidden')
        openButton.setAttribute('aria-expanded', 'false')

        // Wait for the slide-out transition before hiding the container.
        closeTimeout = setTimeout(() => {
            drawer.classList.add('hidden')
            closeTimeout = null
        }, 300)
    }

    openButton.addEventListener('click', openDrawer)
    closeButton?.addEventListener('click', closeDrawer)
    backdrop.addEventListener('click', closeDrawer)

    // Close when a real navigation link inside the drawer is tapped.
    drawer.querySelectorAll('a[href]').forEach((link) => {
        if (link.getAttribute('href') === 'javascript:void(0)') return
        link.addEventListener('click', closeDrawer)
    })

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !drawer.classList.contains('hidden')) {
            closeDrawer()
        }
    })

    // Accordion behaviour for the dropdown sections inside the drawer.
    drawer.querySelectorAll('.dropdown-container').forEach((container) => {
        const toggle = container.querySelector('.dropdown-toggle')
        const menu = container.querySelector('.dropdown')
        const chevron = container.querySelector('.dropdown-chevron')

        toggle?.addEventListener('click', () => {
            const isOpen = !menu.classList.contains('hidden')
            menu.classList.toggle('hidden', isOpen)
            chevron?.classList.toggle('rotate-180', !isOpen)
            toggle.setAttribute('aria-expanded', String(!isOpen))
        })
    })
}

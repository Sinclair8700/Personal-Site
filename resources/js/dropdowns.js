function handle_dropdown_open(el){
    el.querySelector('.dropdown').classList.remove('hidden')
    const activator = el.querySelector('.activator')
    if(activator) activator.setAttribute('aria-expanded', 'true')
}

function handle_dropdown_close(el, in_activator, in_dropdown){
    if(in_activator || in_dropdown){
        return
    }
    el.querySelector('.dropdown').classList.add('hidden')
    const activator = el.querySelector('.activator')
    if(activator) activator.setAttribute('aria-expanded', 'false')
}

// Desktop-only hover dropdowns. The mobile drawer copies use a tap accordion
// instead (see mobile-nav.js), so scope this to the desktop nav.
Array.from(document.querySelectorAll('.nav-desktop .dropdown-container')).forEach((el) =>{
    let in_activator = false
    let in_dropdown = false
    let activator_timeout = null
    let dropdown_timeout = null

    // Advertise the submenu to assistive tech and reflect its open state.
    const activator = el.querySelector('.activator')
    if(activator){
        activator.setAttribute('aria-haspopup', 'true')
        activator.setAttribute('aria-expanded', 'false')
    }

    el.querySelector('.activator').addEventListener('mouseover', function(){
        in_activator = true

        if(activator_timeout){
            clearTimeout(activator_timeout)
            activator_timeout = null
        }
        if(dropdown_timeout){
            clearTimeout(dropdown_timeout)
            dropdown_timeout = null
        }
        handle_dropdown_open(el)
    })
    el.querySelector('.dropdown').addEventListener('mouseover', function(){
        in_dropdown = true

        if(activator_timeout){
            clearTimeout(activator_timeout)
            activator_timeout = null
        }
        if(dropdown_timeout){
            clearTimeout(dropdown_timeout)
            dropdown_timeout = null
        }
    })

    el.querySelector('.activator').addEventListener('mouseout', function(){
        in_activator = false
        activator_timeout = setTimeout(() => {
            handle_dropdown_close(el, in_activator, in_dropdown)
        },100)
    })
    el.querySelector('.dropdown').addEventListener('mouseout', function(){
        in_dropdown = false
        dropdown_timeout = setTimeout(() => {
            handle_dropdown_close(el, in_activator, in_dropdown)
        },100)
    })
    window.addEventListener('touchstart', (e) => {
        if (el.querySelector('.dropdown').contains(e.target))
            return
        in_dropdown = false
        dropdown_timeout = setTimeout(() => {
            handle_dropdown_close(el, in_activator, in_dropdown)
        },100)
      });

    // Keyboard support (WCAG 2.1.1): reveal the submenu when focus enters the
    // container (activator or a submenu link) and hide it once focus leaves, so
    // the links aren't reachable by pointer only. Escape closes and returns
    // focus to the activator.
    el.addEventListener('focusin', function(){
        in_activator = true
        if(activator_timeout){ clearTimeout(activator_timeout); activator_timeout = null }
        if(dropdown_timeout){ clearTimeout(dropdown_timeout); dropdown_timeout = null }
        handle_dropdown_open(el)
    })

    el.addEventListener('focusout', function(){
        in_activator = false
        in_dropdown = false
        activator_timeout = setTimeout(() => {
            handle_dropdown_close(el, in_activator, in_dropdown)
        },100)
    })

    el.addEventListener('keydown', function(e){
        if(e.key === 'Escape'){
            handle_dropdown_close(el, false, false)
            const a = el.querySelector('.activator')
            if(a && typeof a.focus === 'function') a.focus()
        }
    })
})
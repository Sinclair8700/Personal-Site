function handle_dropdown_open(el){
    el.querySelector('.dropdown').classList.remove('hidden')
}

function handle_dropdown_close(el, in_activator, in_dropdown){
    if(in_activator || in_dropdown){
        return
    }
    el.querySelector('.dropdown').classList.add('hidden')
}

Array.from(document.querySelectorAll('.dropdown-container')).forEach((el) =>{
    let in_activator = false
    let in_dropdown = false
    let activator_timeout = null
    let dropdown_timeout = null

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


})
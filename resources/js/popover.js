document.querySelectorAll('.popover').forEach(popover => {
    // Store the original popover for reference
    let popover_clone = null;
    let parent = popover.parentElement;
    let in_parent = false;
    let in_popover = false;
    let parent_timeout = null;
    let popover_timeout = null;

    function handle_popover_open() {
        // Create a fresh clone each time to ensure proper state
        if (popover_clone && popover_clone.parentElement) {
            document.querySelector('#body').removeChild(popover_clone);
        }
        
        popover_clone = popover.cloneNode(true);
        popover_clone.classList.remove('hidden');
        popover_clone.classList.add('absolute');
        
        // First append to the DOM so we can get accurate measurements
        document.querySelector('#body').appendChild(popover_clone);
        
        let rect = parent.getBoundingClientRect();
        let scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;
        let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        // Get positioning classes
        const hasTop = popover.classList.contains('popover-top');
        const hasBottom = popover.classList.contains('popover-bottom');
        const hasLeft = popover.classList.contains('popover-left');
        const hasRight = popover.classList.contains('popover-right');
        
        // Extract gap value from class name (default to 0 if not found)
        let gap = 0;
        const gapClass = Array.from(popover.classList).find(cls => cls.startsWith('popover-gap-'));
        if (gapClass) {
            gap = parseInt(gapClass.replace('popover-gap-', ''), 10) || 0;
        }
        
        // Calculate horizontal position
        if (hasLeft && !hasRight) {
            // Left alignment - place popover to the left of the parent with gap
            popover_clone.style.left = `${rect.left + scrollLeft - popover_clone.offsetWidth - gap}px`;
        } else if (hasRight && !hasLeft) {
            // Right alignment - place popover to the right of the parent with gap
            popover_clone.style.left = `${rect.left + scrollLeft + rect.width + gap}px`;
        } else {
            // Center horizontally (default or when both left and right are specified)
            popover_clone.style.left = `${rect.left + scrollLeft + (rect.width / 2) - (popover_clone.offsetWidth / 2)}px`;
        }
        
        // Calculate vertical position
        if (hasTop && !hasBottom) {
            // Top alignment - place popover above the parent with gap
            popover_clone.style.top = `${rect.top + scrollTop - popover_clone.offsetHeight - gap}px`;
        } else if (hasBottom && !hasTop) {
            // Bottom alignment - place popover below the parent with gap
            popover_clone.style.top = `${rect.top + scrollTop + rect.height + gap}px`;
        } else {
            // Center vertically (default or when both top and bottom are specified)
            popover_clone.style.top = `${rect.top + scrollTop + (rect.height / 2) - (popover_clone.offsetHeight / 2)}px`;
        }
        
        // Re-attach event listeners to the new clone
        popover_clone.addEventListener('mouseover', function() {
            in_popover = true;
            
            if(parent_timeout) {
                clearTimeout(parent_timeout);
                parent_timeout = null;
            }
            if(popover_timeout) {
                clearTimeout(popover_timeout);
                popover_timeout = null;
            }
        });
        
        popover_clone.addEventListener('mouseout', function() {
            in_popover = false;
            popover_timeout = setTimeout(() => {
                handle_popover_close(in_parent, in_popover);
            }, 100);
        });
    }

    function handle_popover_close(in_parent, in_popover) {
        if(in_parent || in_popover) {
            return;
        }
        if (popover_clone && popover_clone.parentElement) {
            popover_clone.classList.add('hidden');
            document.querySelector('#body').removeChild(popover_clone);
            popover_clone = null;
        }
    }

    parent.addEventListener('mouseover', function() {
        in_parent = true;

        if(parent_timeout) {
            clearTimeout(parent_timeout);
            parent_timeout = null;
        }
        if(popover_timeout) {
            clearTimeout(popover_timeout);
            popover_timeout = null;
        }
        handle_popover_open();
    });

    parent.addEventListener('mouseout', function() {
        in_parent = false;
        parent_timeout = setTimeout(() => {
            handle_popover_close(in_parent, in_popover);
        }, 100);
    });

    window.addEventListener('touchstart', (e) => {   
        if (popover_clone && popover_clone.contains(e.target))
            return;
        in_popover = false;
        popover_timeout = setTimeout(() => {
            handle_popover_close(in_parent, in_popover);
        }, 100);
    });
});


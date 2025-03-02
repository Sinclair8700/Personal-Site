document.querySelectorAll('.copy-value').forEach(element => {
    element.addEventListener('click', () => {
        // Function to handle visual feedback
        const showFeedback = (success) => {
            const colorClass = success ? 'text-purple' : 'text-red-500';
            
            // Add transition classes
            element.classList.add('transition-colors', 'duration-300', colorClass);
            
            // Handle transition end
            const handleTransitionEnd = () => {
                setTimeout(() => {
                    element.classList.remove(colorClass);
                    // Only remove transition classes after color change is complete
                    setTimeout(() => {
                        element.classList.remove('transition-colors', 'duration-300');
                    }, 50);
                }, 300);
                element.removeEventListener('transitionend', handleTransitionEnd);
            };
            
            element.addEventListener('transitionend', handleTransitionEnd);
        };

        try {
            navigator.clipboard.writeText(element.dataset.value)
                .then(() => {
                    showFeedback(true);
                })
                .catch(() => {
                    console.error('Clipboard write failed');
                    showFeedback(false);
                });
        } catch (error) {
            console.error('Failed to copy text:', error);
            showFeedback(false);
        }
    });
});
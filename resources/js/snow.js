class Snow {
    constructor(element) {
        this.element = element;
        this.canvas = null;
        this.ctx = null;
        this.snowflakes = [];
        this.animationId = null;
        this.init();
    }

    init() {
        // Create canvas element
        this.canvas = document.createElement('canvas');
        this.canvas.style.position = 'absolute';
        this.canvas.style.top = '0';
        this.canvas.style.left = '0';
        this.canvas.style.width = '100%';
        this.canvas.style.height = '100%';
        this.canvas.style.pointerEvents = 'none';
        this.canvas.style.zIndex = '1';
        
        // Make the element positioned for absolute canvas
        if (getComputedStyle(this.element).position === 'static') {
            this.element.style.position = 'relative';
        }
        
        this.element.appendChild(this.canvas);
        this.ctx = this.canvas.getContext('2d');
        
        // Set canvas size
        this.resizeCanvas();
        
        // Create snowflakes
        this.createSnowflakes();
        
        // Start animation
        this.animate();
        
        // Handle resize
        window.addEventListener('resize', () => this.resizeCanvas());
    }

    resizeCanvas() {
        const rect = this.element.getBoundingClientRect();
        this.canvas.width = rect.width;
        this.canvas.height = rect.height;
    }

    createSnowflakes() {
        const density = Math.max((this.canvas.height + this.canvas.height) / 100, 3); // Adjust density based on width
        
        for (let i = 0; i < density; i++) {
            this.snowflakes.push({
                x: Math.random() * this.canvas.width,
                y: Math.random() * this.canvas.height,
                radius: Math.random() * 2 + 1,
                speed: Math.random() * 1 + 0.5,
                drift: Math.random() * 0.5 - 0.25,
                opacity: Math.random() * 0.6 + 0.3
            });
        }
    }

    animate() {
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        
        // Update and draw each snowflake
        this.snowflakes.forEach((flake) => {
            // Update position
            flake.y += flake.speed;
            flake.x += flake.drift;
            
            // Reset snowflake if it goes off screen
            if (flake.y > this.canvas.height) {
                flake.y = -10;
                flake.x = Math.random() * this.canvas.width;
            }
            
            if (flake.x > this.canvas.width) {
                flake.x = 0;
            } else if (flake.x < 0) {
                flake.x = this.canvas.width;
            }
            
            // Draw snowflake
            this.ctx.beginPath();
            this.ctx.arc(flake.x, flake.y, flake.radius, 0, Math.PI * 2);
            this.ctx.fillStyle = `rgba(255, 255, 255, ${flake.opacity})`;
            this.ctx.fill();
        });
        
        this.animationId = requestAnimationFrame(() => this.animate());
    }

    destroy() {
        if (this.animationId) {
            cancelAnimationFrame(this.animationId);
        }
        if (this.canvas && this.canvas.parentNode) {
            this.canvas.parentNode.removeChild(this.canvas);
        }
    }
}

// Initialize snow effect on all elements with snow class
document.addEventListener('DOMContentLoaded', () => {
    const snowElements = document.querySelectorAll('.snow');
    
    snowElements.forEach(element => {
        new Snow(element);
    });
});


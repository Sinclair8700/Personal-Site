class GapSnake {
    constructor(element) {
        this.element = element;
        this.canvas = null;
        this.ctx = null;
        this.occupiedSpaces = [];
        this.recentWaypoints = []; // Track recent waypoints to avoid repetition
        this.snake = {
            x: 0,
            y: 0,
            vx: 1.5,
            vy: 1.5,
            trail: [],
            maxTrailLength: 30, // Back to 30 for performance
            size: 3,
            visitedCells: new Map(), // Track visited areas
            exploreRadius: 50, // How close we consider "same area"
            stuckCounter: 0, // Track if we're stuck
            lastPosition: { x: 0, y: 0 },
            directionChangeTimer: 0, // Prevent rapid direction changes
            targetWaypoint: null, // Goal point to navigate towards
            waypointTimer: 0, // Time until we pick a new waypoint
            waypointRefreshTime: 3600 // Time until we refresh the waypoint
        };
        this.animationId = null;
        this.debugMode = false; // ENABLE TO SEE WHAT'S BEING DETECTED
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
        this.canvas.style.zIndex = '10';
        
        // Make the element positioned for absolute canvas
        if (getComputedStyle(this.element).position === 'static') {
            this.element.style.position = 'relative';
        }
        
        this.element.appendChild(this.canvas);
        this.ctx = this.canvas.getContext('2d');
        
        // Set canvas size
        this.resizeCanvas();
        
        // Find all occupied spaces
        this.findOccupiedSpaces();
        
        // Initialize snake position in empty space
        this.initializeSnakePosition();
        
        // Start animation
        this.animate();
        
        // Handle resize
        window.addEventListener('resize', () => {
            this.resizeCanvas();
            this.findOccupiedSpaces();
        });
    }

    resizeCanvas() {
        const rect = this.element.getBoundingClientRect();
        this.canvas.width = rect.width;
        this.canvas.height = rect.height;
    }
    
    checkElementForText(element, containerRect, markedBorderedContainers) {
        // Check for direct text nodes in this element (not in children)
        let hasDirectTextNodes = false;
        for (const node of element.childNodes) {
            if (node.nodeType === Node.TEXT_NODE && node.textContent.trim().length > 0) {
                hasDirectTextNodes = true;
                break;
            }
        }
        
        if (hasDirectTextNodes) {
            const rect = element.getBoundingClientRect();
            const computedStyle = getComputedStyle(element);
            
            // Skip if not visible
            if (computedStyle.display === 'none' || 
                computedStyle.visibility === 'hidden' || 
                computedStyle.opacity === '0' ||
                rect.width === 0 || 
                rect.height === 0) {
                return;
            }
            
            const buffer = 5; // Small buffer for root text
            this.occupiedSpaces.push({
                x: rect.left - containerRect.left - buffer,
                y: rect.top - containerRect.top - buffer,
                width: rect.width + buffer * 2,
                height: rect.height + buffer * 2,
                element: element.tagName,
                text: element.textContent.trim().substring(0, 20),
                reason: 'root-text'
            });
        }
    }

    findOccupiedSpaces() {
        this.occupiedSpaces = [];
        const containerRect = this.element.getBoundingClientRect();
        const markedBorderedContainers = new Set(); // Track bordered boxes we've marked
        
        // First check the root element itself for direct text nodes
        this.checkElementForText(this.element, containerRect, markedBorderedContainers);
        
        // Get ALL descendant elements
        const allElements = this.element.getElementsByTagName('*');
        
        Array.from(allElements).forEach(child => {
            // Skip the canvas itself
            if (child === this.canvas) {
                return;
            }
            
            // Skip if this element is a descendant of an already-marked bordered container
            let isInsideBorderedContainer = false;
            for (const borderedContainer of markedBorderedContainers) {
                if (borderedContainer.contains(child) && borderedContainer !== child) {
                    isInsideBorderedContainer = true;
                    break;
                }
            }
            if (isInsideBorderedContainer) {
                return; // Skip descendants of bordered containers
            }
            
            const rect = child.getBoundingClientRect();
            const computedStyle = getComputedStyle(child);
            
            // Skip if element is not visible or has no size
            if (computedStyle.display === 'none' || 
                computedStyle.visibility === 'hidden' || 
                computedStyle.opacity === '0' ||
                rect.width === 0 || 
                rect.height === 0) {
                return;
            }
            
            // Skip if element is inside an inactive swiper slide
            let parent = child.parentElement;
            while (parent && parent !== this.element) {
                if (parent.classList && parent.classList.contains('swiper-slide')) {
                    // Check if this slide is active or next (visible)
                    const isActive = parent.classList.contains('swiper-slide-active');
                    const isNext = parent.classList.contains('swiper-slide-next');
                    const isPrev = parent.classList.contains('swiper-slide-prev');
                    const isDuplicate = parent.classList.contains('swiper-slide-duplicate');
                    
                    // Skip if it's a duplicate or not active/next/prev
                    if (isDuplicate || (!isActive && !isNext && !isPrev)) {
                        return; // Element is in an inactive swiper slide
                    }
                }
                parent = parent.parentElement;
            }
            
            // Check for VISIBLE borders first (all the ways a border can be created)
            const borderTop = parseFloat(computedStyle.borderTopWidth) || 0;
            const borderRight = parseFloat(computedStyle.borderRightWidth) || 0;
            const borderBottom = parseFloat(computedStyle.borderBottomWidth) || 0;
            const borderLeft = parseFloat(computedStyle.borderLeftWidth) || 0;
            const hasBorder = (borderTop + borderRight + borderBottom + borderLeft) > 0 &&
                            computedStyle.borderStyle !== 'none' &&
                            computedStyle.borderStyle !== 'hidden';
            const hasVisibleBorder = hasBorder && (borderTop + borderRight + borderBottom + borderLeft) > 2;
            
            // Check for text content
            const allText = child.textContent || '';
            const hasAnyText = allText.trim().length > 0;
            const visibleText = child.innerText || '';
            const hasVisibleText = visibleText.trim().length > 0;
            
            // Skip if this is a plain container (no border, no text)
            const isPlainContainer = child.children.length > 1 && !hasAnyText && !hasVisibleBorder;
            
            if (isPlainContainer) {
                return; // Skip plain layout containers
            }
            
            // Skip if this is a large container BUT KEEP if it has a visible border
            const relativeWidth = rect.width / containerRect.width;
            const relativeHeight = rect.height / containerRect.height;
            const isLargeContainer = (relativeWidth > 0.4 || relativeHeight > 0.4);
            
            // Large containers are skipped UNLESS they have a border or text
            if (isLargeContainer && child.children.length > 0 && !hasAnyText && !hasVisibleBorder) {
                return;
            }
            
            // Check outline
            const outlineWidth = parseFloat(computedStyle.outlineWidth) || 0;
            const hasOutline = outlineWidth > 0 && 
                             computedStyle.outlineStyle !== 'none' &&
                             computedStyle.outlineColor !== 'transparent';
            
            // Check box-shadow (creates visible border effect)
            const hasBoxShadow = computedStyle.boxShadow && 
                               computedStyle.boxShadow !== 'none';
            
            // Check background (color or image)
            const hasBackgroundColor = computedStyle.backgroundColor !== 'rgba(0, 0, 0, 0)' &&
                                      computedStyle.backgroundColor !== 'transparent';
            
            const hasBackgroundImage = computedStyle.backgroundImage !== 'none';
            
            const hasVisibleBackground = hasBackgroundColor || hasBackgroundImage;
            
            // Direct text nodes
            const hasDirectTextNodes = Array.from(child.childNodes).some(node => 
                node.nodeType === Node.TEXT_NODE && node.textContent.trim().length > 0
            );
            
            // Text element tags - HEADINGS ARE PRIORITY
            const isHeading = ['H1', 'H2', 'H3', 'H4', 'H5', 'H6'].includes(child.tagName);
            const isTextElement = ['P', 'SPAN', 'LABEL', 'LI', 'A', 'STRONG', 'EM', 'B', 'I'].includes(child.tagName);
            
            // Interactive elements
            const isInteractiveElement = ['BUTTON', 'INPUT', 'SELECT', 'TEXTAREA', 'A', 'IMG', 'VIDEO', 'CANVAS', 'SVG'].includes(child.tagName);
            
            const isLeaf = child.children.length === 0;
            
            // HEADINGS WITH TEXT MUST ALWAYS BE MARKED (but only if they're leaf nodes or have direct text)
            if (isHeading && (isLeaf || hasDirectTextNodes) && hasAnyText) {
                const buffer = 5; // Small buffer for headings
                this.occupiedSpaces.push({
                    x: rect.left - containerRect.left - buffer,
                    y: rect.top - containerRect.top - buffer,
                    width: rect.width + buffer * 2,
                    height: rect.height + buffer * 2,
                    element: child.tagName,
                    text: allText.substring(0, 20),
                    reason: 'heading'
                });
                return; // Don't process further
            }
            
            // Mark as occupied if:
            // 1. Has a visible border with children (these are section boxes - mark whole thing)
            // 2. Interactive elements (always)
            // 3. Has direct text nodes or is a text element with text (leaf only)
            // 4. Other visual properties on leaf elements only
            
            const isBorderedContainer = hasVisibleBorder && child.children.length > 0;
            
            const shouldMarkAsOccupied = isBorderedContainer || // Bordered sections - mark the whole box (EVEN IF LARGE)
                                        isInteractiveElement ||
                                        hasDirectTextNodes || // Direct text in this element
                                        (isTextElement && hasAnyText && isLeaf) || // Text elements (P, SPAN, etc.) with text as leaf
                                        (hasVisibleBorder && isLeaf) || // Leaf borders
                                        (hasOutline && isLeaf) ||
                                        (hasBoxShadow && isLeaf) ||
                                        (hasVisibleBackground && isLeaf);
            
            // For bordered containers, IGNORE the large container check
            if (shouldMarkAsOccupied && (!isLargeContainer || isBorderedContainer)) {
                // If this is a bordered container, remember it so we skip its descendants
                if (isBorderedContainer) {
                    markedBorderedContainers.add(child);
                }
                
                // Different buffer sizes:
                // - Bordered containers: 2px (tiny - just the border)
                // - Text/Headings: 5px (small - tight around text)
                // - Everything else: 3px
                const buffer = isBorderedContainer ? 2 : // Bordered section boxes
                              (hasDirectTextNodes || isTextElement) ? 5 : // Text
                              3; // Everything else
                              
                this.occupiedSpaces.push({
                    x: rect.left - containerRect.left - buffer,
                    y: rect.top - containerRect.top - buffer,
                    width: rect.width + buffer * 2,
                    height: rect.height + buffer * 2,
                    element: child.tagName,
                    text: hasAnyText ? allText.substring(0, 20) : '',
                    reason: isBorderedContainer ? 'bordered-container' :
                           isInteractiveElement ? 'interactive' :
                           hasDirectTextNodes ? 'direct-text' :
                           (isTextElement && hasAnyText) ? 'text-element' :
                           hasVisibleBorder ? 'border' :
                           hasOutline ? 'outline' :
                           hasBoxShadow ? 'boxShadow' :
                           'background'
                });
            }
        });
        
        console.log('Occupied spaces found:', this.occupiedSpaces.length);
        if (this.occupiedSpaces.length > 0) {
            console.log('Sample occupied spaces:', this.occupiedSpaces.slice(0, 5));
        }
    }

    initializeSnakePosition() {
        // Find a position in empty space
        const maxAttempts = 200;
        for (let i = 0; i < maxAttempts; i++) {
            const x = Math.random() * (this.canvas.width - 20) + 10;
            const y = Math.random() * (this.canvas.height - 20) + 10;
            
            if (!this.isInOccupiedSpace(x, y)) {
                this.snake.x = x;
                this.snake.y = y;
                
                // Set random initial direction
                const angle = Math.random() * Math.PI * 2;
                const speed = 1.5;
                this.snake.vx = Math.cos(angle) * speed;
                this.snake.vy = Math.sin(angle) * speed;
                return;
            }
        }
        
        // If we couldn't find empty space, just start at center
        this.snake.x = this.canvas.width / 2;
        this.snake.y = this.canvas.height / 2;
    }

    isInOccupiedSpace(x, y) {
        return this.occupiedSpaces.some(space => 
            x > space.x && 
            x < space.x + space.width && 
            y > space.y && 
            y < space.y + space.height
        );
    }
    
    canSnakePassThrough(x, y) {
        // Check if snake can fit through - use smaller radius for gaps
        const checkRadius = this.snake.size - 1; // Smaller than actual size to allow tight squeezes
        const checkPoints = 4; // Check 4 points around the snake
        
        for (let i = 0; i < checkPoints; i++) {
            const angle = (Math.PI * 2 * i) / checkPoints;
            const checkX = x + Math.cos(angle) * checkRadius;
            const checkY = y + Math.sin(angle) * checkRadius;
            
            if (this.isInOccupiedSpace(checkX, checkY)) {
                return false; // Can't fit if any edge hits occupied space
            }
        }
        return true; // Center is checked in the calling code
    }

    findInterestingWaypoint() {
        // Find an interesting point to navigate towards - PRIORITIZE SMALL GAPS
        const margin = 20;
        const candidates = [];
        const gridSize = 15; // Even smaller grid to find more gaps
        
        // Sample points across the canvas
        for (let x = margin; x < this.canvas.width - margin; x += gridSize) {
            for (let y = margin; y < this.canvas.height - margin; y += gridSize) {
                // Skip if center is in occupied space OR if snake can't fit through
                if (this.isInOccupiedSpace(x, y) || !this.canSnakePassThrough(x, y)) continue;
                
                let score = 0;
                
                // Check if this point is in a narrow gap/corridor OR in wide open space
                let nearbyObstacles = 0;
                let closestObstacle = Infinity;
                const checkAngles = 8;
                const checkRadius = 80; // DOUBLED - check much further to detect open space
                
                for (let i = 0; i < checkAngles; i++) {
                    const angle = (Math.PI * 2 * i) / checkAngles;
                    const checkX = x + Math.cos(angle) * checkRadius;
                    const checkY = y + Math.sin(angle) * checkRadius;
                    if (this.isInOccupiedSpace(checkX, checkY)) {
                        nearbyObstacles++;
                        // Also check how close the obstacle is
                        for (let dist = 10; dist < checkRadius; dist += 10) {
                            const testX = x + Math.cos(angle) * dist;
                            const testY = y + Math.sin(angle) * dist;
                            if (this.isInOccupiedSpace(testX, testY)) {
                                closestObstacle = Math.min(closestObstacle, dist);
                                break;
                            }
                        }
                    }
                }
                
                // MASSIVELY prioritize narrow gaps that snake can fit through
                if (nearbyObstacles >= 3 && nearbyObstacles <= 6) {
                    score += 350; // MUCH higher bonus for gaps to compete with abundant open space
                    // Extra bonus if obstacles are very close (narrow gap!)
                    if (closestObstacle < 25) {
                        score += 250; // Big bonus for tight gaps
                    }

                }
                
                // Good bonus for corridors (2-3 obstacles)
                if (nearbyObstacles >= 2 && nearbyObstacles < 3) {
                    score += 180; // Higher bonus for corridors
                }
                
                // Moderate bonus for open space
                if (nearbyObstacles === 0) {
                    score += 200; // Base score for open areas
                    // Extra bonus for VERY open space (far from all obstacles)
                    if (closestObstacle === Infinity || closestObstacle > 60) {
                        score += 150; // Bonus for wide open space
                    }
                }
                
                // Small bonus for near walls
                if (nearbyObstacles === 1) {
                    score += 50;
                }
                
                // Small bonus for near walls
                if (nearbyObstacles === 1) {
                    score += 20;
                }
                
                // Check if it's far from current position (prefer exploration)
                const distance = Math.sqrt(
                    Math.pow(x - this.snake.x, 2) + 
                    Math.pow(y - this.snake.y, 2)
                );
                score += distance / 10; // Bonus for distance
                
                // Add LARGE random factor to ensure variety - makes scores overlap significantly!
                score += Math.random() * 300; // Random 0-300 points - huge variation for mixing
                
                // Check if it's not recently visited
                const cellX = Math.floor(x / 30);
                const cellY = Math.floor(y / 30);
                const cellKey = `${cellX},${cellY}`;
                if (this.snake.visitedCells.has(cellKey)) {
                    score -= this.snake.visitedCells.get(cellKey).count * 10; // Reduced penalty
                }
                
                // STRONG penalty if this is similar to recent waypoints
                for (const recentWp of this.recentWaypoints) {
                    const distToRecent = Math.sqrt(
                        Math.pow(x - recentWp.x, 2) + Math.pow(y - recentWp.y, 2)
                    );
                    if (distToRecent < 150) { // Larger radius
                        score -= 100; // HUGE penalty to prevent loops
                    }
                }
                
                // Check if there's some path clearness - MORE LENIENT for gaps
                let pathClearness = 0;
                const steps = 10; // Even fewer steps
                const dx = (x - this.snake.x) / steps;
                const dy = (y - this.snake.y) / steps;
                for (let step = 1; step <= steps; step++) {
                    const checkX = this.snake.x + dx * step;
                    const checkY = this.snake.y + dy * step;
                    if (!this.isInOccupiedSpace(checkX, checkY)) {
                        pathClearness++;
                    }
                }
                // Only need 30% clear path - very lenient
                if (pathClearness < steps * 0.3) {
                    score -= 10; // Small penalty
                }
                
                if (score > -50) { // Much lower threshold - accept more candidates
                    candidates.push({ x, y, score });
                }
            }
        }
        
        // Pick from a wider range of candidates for variety
        if (candidates.length > 0) {
            // Filter out candidates that are too similar to recent waypoints
            const filteredCandidates = candidates.filter(c => {
                for (const recentWp of this.recentWaypoints) {
                    const dist = Math.sqrt(
                        Math.pow(c.x - recentWp.x, 2) + Math.pow(c.y - recentWp.y, 2)
                    );
                    if (dist < 100) return false; // Skip if too close to recent
                }
                return true;
            });
            
            // Use filtered list if available, otherwise fall back to all candidates
            const finalCandidates = filteredCandidates.length > 0 ? filteredCandidates : candidates;
            
            // CATEGORIZE candidates into gaps vs open space
            const gapCandidates = []; // Has nearby obstacles (3-6)
            const openCandidates = []; // No nearby obstacles
            const corridorCandidates = []; // 2-3 obstacles
            
            for (const candidate of finalCandidates) {
                // Recount obstacles for categorization
                let nearbyObstacles = 0;
                const checkRadius = 80;
                for (const space of this.occupiedSpaces) {
                    const dx = Math.max(space.x - candidate.x, candidate.x - (space.x + space.width), 0);
                    const dy = Math.max(space.y - candidate.y, candidate.y - (space.y + space.height), 0);
                    const dist = Math.sqrt(dx * dx + dy * dy);
                    if (dist < checkRadius) {
                        nearbyObstacles++;
                    }
                }
                
                if (nearbyObstacles >= 3 && nearbyObstacles <= 6) {
                    gapCandidates.push(candidate);
                } else if (nearbyObstacles >= 2 && nearbyObstacles < 3) {
                    corridorCandidates.push(candidate);
                } else if (nearbyObstacles === 0) {
                    openCandidates.push(candidate);
                }
            }
            
            // RANDOMLY decide which type to target (50/50 split between gaps and open)
            // If we don't have the chosen type, fall back to the other
            let chosenPool = [];
            const targetType = Math.random();
            
            if (targetType < 0.5) {
                // Try gaps first
                if (gapCandidates.length > 0) {
                    chosenPool = gapCandidates;
                } else if (corridorCandidates.length > 0) {
                    chosenPool = corridorCandidates;
                } else {
                    chosenPool = openCandidates;
                }
            } else {
                // Try open space first
                if (openCandidates.length > 0) {
                    chosenPool = openCandidates;
                } else if (corridorCandidates.length > 0) {
                    chosenPool = corridorCandidates;
                } else {
                    chosenPool = gapCandidates;
                }
            }
            
            // Sort the chosen pool and pick from top candidates
            if (chosenPool.length > 0) {
                chosenPool.sort((a, b) => b.score - a.score);
                
                const numCandidates = Math.min(5, chosenPool.length); // Top 5 from chosen type
                const topCandidates = chosenPool.slice(0, numCandidates);
                
                // Weight towards better scores
                const weights = topCandidates.map((_, i) => numCandidates - i);
                const totalWeight = weights.reduce((a, b) => a + b, 0);
                let random = Math.random() * totalWeight;
                
                for (let i = 0; i < topCandidates.length; i++) {
                    random -= weights[i];
                    if (random <= 0) {
                        const chosen = topCandidates[i];
                        // Track this waypoint
                        this.recentWaypoints.push({ x: chosen.x, y: chosen.y });
                        if (this.recentWaypoints.length > 8) {
                            this.recentWaypoints.shift();
                        }
                        return chosen;
                    }
                }
                
                const chosen = topCandidates[0];
                this.recentWaypoints.push({ x: chosen.x, y: chosen.y });
                if (this.recentWaypoints.length > 8) {
                    this.recentWaypoints.shift();
                }
                return chosen;
            }
            
            // Fallback: just pick from all candidates
            finalCandidates.sort((a, b) => b.score - a.score);
            const chosen = finalCandidates[0];
            this.recentWaypoints.push({ x: chosen.x, y: chosen.y });
            if (this.recentWaypoints.length > 8) {
                this.recentWaypoints.shift();
            }
            return chosen;
        }
        
        return null;
    }

    updateSnake() {
        // Add current position to trail
        this.snake.trail.push({ x: this.snake.x, y: this.snake.y });
        
        // Limit trail length
        if (this.snake.trail.length > this.snake.maxTrailLength) {
            this.snake.trail.shift();
        }
        
        // Decrement timers
        if (this.snake.directionChangeTimer > 0) {
            this.snake.directionChangeTimer--;
        }
        if (this.snake.waypointTimer > 0) {
            this.snake.waypointTimer--;
        }
        
        // Pick a new waypoint periodically or if we don't have one
        if (!this.snake.targetWaypoint || this.snake.waypointTimer === 0) {
            this.snake.targetWaypoint = this.findInterestingWaypoint();
            this.snake.waypointTimer = this.snake.waypointRefreshTime; // MUCH longer - ~10 seconds per waypoint
        }
        
        // Check if we reached the waypoint
        if (this.snake.targetWaypoint) {
            const distToWaypoint = Math.sqrt(
                Math.pow(this.snake.x - this.snake.targetWaypoint.x, 2) +
                Math.pow(this.snake.y - this.snake.targetWaypoint.y, 2)
            );
            if (distToWaypoint < 20) { // Reached waypoint
                // Reached waypoint, get a new one immediately
                this.snake.targetWaypoint = this.findInterestingWaypoint();
                this.snake.waypointTimer = this.snake.waypointRefreshTime;
            }
        }
        
        // Check if we're stuck (not moving much OR bouncing back and forth)
        const distanceMoved = Math.sqrt(
            Math.pow(this.snake.x - this.snake.lastPosition.x, 2) +
            Math.pow(this.snake.y - this.snake.lastPosition.y, 2)
        );
        
        // Also track if we're bouncing in the same area
        if (this.snake.trail.length > 30) {
            const recentTrail = this.snake.trail.slice(-30);
            const avgX = recentTrail.reduce((sum, p) => sum + p.x, 0) / recentTrail.length;
            const avgY = recentTrail.reduce((sum, p) => sum + p.y, 0) / recentTrail.length;
            const variance = recentTrail.reduce((sum, p) => {
                return sum + Math.pow(p.x - avgX, 2) + Math.pow(p.y - avgY, 2);
            }, 0) / recentTrail.length;
            
            // If variance is very low, we're stuck in a small area
            if (variance < 400) {
                this.snake.stuckCounter += 3; // Count as more stuck
            }
        }
        
        if (distanceMoved < 0.5) {
            this.snake.stuckCounter++;
        } else {
            this.snake.stuckCounter = Math.max(0, this.snake.stuckCounter - 1); // Slowly decrease
        }
        
        this.snake.lastPosition = { x: this.snake.x, y: this.snake.y };
        
        // Track visited cells (grid-based to avoid infinite memory)
        const cellSize = 30;
        const cellX = Math.floor(this.snake.x / cellSize);
        const cellY = Math.floor(this.snake.y / cellSize);
        const cellKey = `${cellX},${cellY}`;
        const currentTime = Date.now();
        
        if (!this.snake.visitedCells.has(cellKey)) {
            this.snake.visitedCells.set(cellKey, { count: 0, lastVisit: 0 });
        }
        const cellData = this.snake.visitedCells.get(cellKey);
        cellData.count++;
        cellData.lastVisit = currentTime;
        
        // Clear old visited cells (older than 4 seconds) - faster forgetting
        for (const [key, data] of this.snake.visitedCells.entries()) {
            if (currentTime - data.lastVisit > 4000) { // Reduced from 5000
                this.snake.visitedCells.delete(key);
            }
        }
        
        const speed = Math.sqrt(this.snake.vx * this.snake.vx + this.snake.vy * this.snake.vy);
        const margin = 10;
        
        // If stuck for too long, force a random direction and new waypoint
        if (this.snake.stuckCounter > 30) { // Much higher threshold - be very patient
            const randomAngle = Math.random() * Math.PI * 2;
            this.snake.vx = Math.cos(randomAngle) * speed;
            this.snake.vy = Math.sin(randomAngle) * speed;
            this.snake.stuckCounter = 0;
            this.snake.directionChangeTimer = 50; // Lock in direction even longer
            this.snake.targetWaypoint = this.findInterestingWaypoint(); // Get new target
            this.snake.waypointTimer = this.snake.waypointRefreshTime; // Reset waypoint timer
        }
        
        // Look ahead to see if we need to turn - MUCH FURTHER
        const lookAheadDistance = 30; // Look much further ahead
        let futureX = this.snake.x + this.snake.vx * lookAheadDistance / speed;
        let futureY = this.snake.y + this.snake.vy * lookAheadDistance / speed;
        
        // Check if our future path is blocked OR if we're revisiting too much
        let needsNewDirection = false;
        
        if (futureX < margin || futureX > this.canvas.width - margin ||
            futureY < margin || futureY > this.canvas.height - margin ||
            this.isInOccupiedSpace(futureX, futureY)) {
            needsNewDirection = true;
        }
        
        // If we've been in this cell too many times recently, explore elsewhere
        if (cellData.count > 4) { // Increased from 2 - be more tolerant
            needsNewDirection = true;
        }
        
        // Random exploration (ALMOST NEVER)
        if (!needsNewDirection && Math.random() < 0.001) { // Reduced from 0.005
            needsNewDirection = true;
        }
        
        // ANTI-JITTER: Don't change direction too frequently - VERY STRICT
        if (needsNewDirection && this.snake.directionChangeTimer > 0 && this.snake.stuckCounter < 8) {
            needsNewDirection = false; // Just keep going!
        }
        
        if (needsNewDirection) {
            // Set timer to prevent rapid changes - VERY LONG
            this.snake.directionChangeTimer = 60; // Increased from 40
            
            // Generate candidate directions
            const directions = [];
            const numDirections = 16;
            
            for (let i = 0; i < numDirections; i++) {
                const angle = (Math.PI * 2 * i) / numDirections;
                directions.push({
                    angle: angle,
                    vx: Math.cos(angle) * speed,
                    vy: Math.sin(angle) * speed
                });
            }
            
            // Score each direction
            let bestDirection = null;
            let bestScore = -Infinity;
            
            for (const dir of directions) {
                let score = 0;
                let canMove = false;
                let visitedPenalty = 0;
                
                // Test how far we can go in this direction - use snake size awareness
                for (let step = 1; step <= 60; step++) {
                    const testX = this.snake.x + dir.vx * step;
                    const testY = this.snake.y + dir.vy * step;
                    
                    if (testX < margin || testX > this.canvas.width - margin ||
                        testY < margin || testY > this.canvas.height - margin ||
                        this.isInOccupiedSpace(testX, testY) ||
                        !this.canSnakePassThrough(testX, testY)) {
                        break;
                    }
                    score++;
                    if (step > 5) canMove = true;
                    
                    // Check if we've been to this area before
                    if (step % 5 === 0) {
                        const checkCellX = Math.floor(testX / cellSize);
                        const checkCellY = Math.floor(testY / cellSize);
                        const checkKey = `${checkCellX},${checkCellY}`;
                        if (this.snake.visitedCells.has(checkKey)) {
                            visitedPenalty += this.snake.visitedCells.get(checkKey).count;
                        }
                    }
                }
                
                // Penalize directions that lead to visited areas
                score -= visitedPenalty * 2; // Reduced from 3
                
                // WAYPOINT SEEKING: ABSOLUTELY DOMINANT bonus - gaps must be prioritized
                if (this.snake.targetWaypoint) {
                    const toWaypointAngle = Math.atan2(
                        this.snake.targetWaypoint.y - this.snake.y,
                        this.snake.targetWaypoint.x - this.snake.x
                    );
                    const angleDiffToWaypoint = Math.abs(dir.angle - toWaypointAngle);
                    const normalizedDiffToWaypoint = Math.min(angleDiffToWaypoint, Math.PI * 2 - angleDiffToWaypoint);
                    const waypointBonus = (Math.PI - normalizedDiffToWaypoint) / Math.PI * 300; // EXTREME - totally dominates
                    score += waypointBonus;
                }
                
                // Minimal continuity bonus - gaps are everything
                const currentAngle = Math.atan2(this.snake.vy, this.snake.vx);
                const angleDiff = Math.abs(currentAngle - dir.angle);
                const normalizedDiff = Math.min(angleDiff, Math.PI * 2 - angleDiff);
                const continuityBonus = this.snake.stuckCounter > 3 ? 
                    (Math.PI - normalizedDiff) / Math.PI * 3 :
                    (Math.PI - normalizedDiff) / Math.PI * 10; // Very minimal
                score += continuityBonus;
                
                // Almost no random factor
                const randomFactor = this.snake.stuckCounter > 3 ? 5 : 1;
                score += Math.random() * randomFactor;
                
                if (score > bestScore && canMove) {
                    bestScore = score;
                    bestDirection = dir;
                }
            }
            
            if (bestDirection) {
                this.snake.vx = bestDirection.vx;
                this.snake.vy = bestDirection.vy;
            } else {
                // Emergency: try ANY direction
                let foundEscape = false;
                for (let i = 0; i < 32; i++) {
                    const angle = (Math.PI * 2 * i) / 32;
                    const testVx = Math.cos(angle) * speed;
                    const testVy = Math.sin(angle) * speed;
                    const testX = this.snake.x + testVx * 3;
                    const testY = this.snake.y + testVy * 3;
                    
                    if (!this.isInOccupiedSpace(testX, testY) &&
                        testX >= margin && testX <= this.canvas.width - margin &&
                        testY >= margin && testY <= this.canvas.height - margin) {
                        this.snake.vx = testVx;
                        this.snake.vy = testVy;
                        foundEscape = true;
                        break;
                    }
                }
                
                if (!foundEscape) {
                    const randomAngle = Math.random() * Math.PI * 2;
                    this.snake.vx = Math.cos(randomAngle) * speed;
                    this.snake.vy = Math.sin(randomAngle) * speed;
                }
            }
        }
        
        // Calculate next position
        let nextX = this.snake.x + this.snake.vx;
        let nextY = this.snake.y + this.snake.vy;
        
        // Final safety check - use snake size awareness
        if (!this.isInOccupiedSpace(nextX, nextY) &&
            this.canSnakePassThrough(nextX, nextY) &&
            nextX >= margin && nextX <= this.canvas.width - margin &&
            nextY >= margin && nextY <= this.canvas.height - margin) {
            this.snake.x = nextX;
            this.snake.y = nextY;
        }
        
        // Path straightening
        if (Math.random() < 0.015 && this.snake.stuckCounter === 0 && this.snake.directionChangeTimer === 0) {
            const angle = Math.atan2(this.snake.vy, this.snake.vx);
            const snapAngle = Math.round(angle / (Math.PI / 4)) * (Math.PI / 4);
            
            const angleChange = Math.abs(angle - snapAngle);
            if (angleChange < Math.PI / 8) {
                let clearPath = true;
                for (let step = 1; step <= 25; step++) {
                    const testX = this.snake.x + Math.cos(snapAngle) * speed * step;
                    const testY = this.snake.y + Math.sin(snapAngle) * speed * step;
                    if (this.isInOccupiedSpace(testX, testY) ||
                        testX < margin || testX > this.canvas.width - margin ||
                        testY < margin || testY > this.canvas.height - margin) {
                        clearPath = false;
                        break;
                    }
                }
                
                if (clearPath) {
                    this.snake.vx = Math.cos(snapAngle) * speed;
                    this.snake.vy = Math.sin(snapAngle) * speed;
                }
            }
        }
    }

    drawSnake() {
        // Simplified rendering for performance - indigo-600: rgb(79, 70, 229)
        
        // Draw trail - simple approach
        for (let i = 0; i < this.snake.trail.length; i++) {
            const point = this.snake.trail[i];
            const progress = (i + 1) / this.snake.trail.length;
            const opacity = progress * 0.8;
            const size = this.snake.size * progress;
            
            // Simple glow
            this.ctx.beginPath();
            this.ctx.arc(point.x, point.y, size * 2, 0, Math.PI * 2);
            this.ctx.fillStyle = `rgba(79, 70, 229, ${opacity * 0.2})`;
            this.ctx.fill();
            
            // Core
            this.ctx.beginPath();
            this.ctx.arc(point.x, point.y, size, 0, Math.PI * 2);
            this.ctx.fillStyle = `rgba(129, 140, 248, ${opacity})`;
            this.ctx.fill();
        }
        
        // Draw snake head - simplified
        // Outer glow
        this.ctx.beginPath();
        this.ctx.arc(this.snake.x, this.snake.y, this.snake.size * 3, 0, Math.PI * 2);
        this.ctx.fillStyle = 'rgba(79, 70, 229, 0.3)';
        this.ctx.fill();
        
        // Inner glow
        this.ctx.beginPath();
        this.ctx.arc(this.snake.x, this.snake.y, this.snake.size * 1.8, 0, Math.PI * 2);
        this.ctx.fillStyle = 'rgba(129, 140, 248, 0.8)';
        this.ctx.fill();
        
        // Bright center
        this.ctx.beginPath();
        this.ctx.arc(this.snake.x, this.snake.y, this.snake.size, 0, Math.PI * 2);
        this.ctx.fillStyle = 'rgba(224, 231, 255, 1)';
        this.ctx.fill();
    }

    animate() {
        // Clear canvas completely
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        
        // Draw debug occupied spaces if enabled
        if (this.debugMode) {
            this.ctx.fillStyle = 'rgba(255, 0, 0, 0.15)';
            this.ctx.strokeStyle = 'rgba(255, 0, 0, 0.4)';
            this.ctx.lineWidth = 1;
            this.occupiedSpaces.forEach(space => {
                this.ctx.fillRect(space.x, space.y, space.width, space.height);
                this.ctx.strokeRect(space.x, space.y, space.width, space.height);
            });
            
            // Draw waypoint if exists
            if (this.snake.targetWaypoint) {
                this.ctx.fillStyle = 'rgba(0, 255, 0, 0.5)';
                this.ctx.beginPath();
                this.ctx.arc(this.snake.targetWaypoint.x, this.snake.targetWaypoint.y, 8, 0, Math.PI * 2);
                this.ctx.fill();
                
                // Draw line to waypoint
                this.ctx.strokeStyle = 'rgba(0, 255, 0, 0.3)';
                this.ctx.lineWidth = 2;
                this.ctx.beginPath();
                this.ctx.moveTo(this.snake.x, this.snake.y);
                this.ctx.lineTo(this.snake.targetWaypoint.x, this.snake.targetWaypoint.y);
                this.ctx.stroke();
            }
        }
        
        this.updateSnake();
        this.drawSnake();
        
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

// Initialize gap snake effect on all elements with gap-snake class
document.addEventListener('DOMContentLoaded', () => {
    const gapSnakeElements = document.querySelectorAll('.gap-snake');
    
    gapSnakeElements.forEach(element => {
        new GapSnake(element);
    });
});


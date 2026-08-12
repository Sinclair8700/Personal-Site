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
            heading: undefined, // set lazily from velocity on first update
            baseSpeed: 1.15,    // slower, more deliberate than before
            speed: 1.15,
            trail: [],
            maxTrailLength: 44, // number of body points (x trailSpacing = body length)
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

        // Animation + effects state
        this.frame = 0;
        this.clock = 0;          // frame-rate–independent time (advances ~1 per 60fps frame)
        this._lastT = 0;
        this._posHist = [];      // recent positions, for stuck detection
        this.trailSpacing = 3.2; // px between body points (x maxTrailLength = body length ~140px)
        this.wave = { amp: 1.8, freq: 7.0, speed: 0.22 }; // travelling serpentine wave down the body

        // Steering / movement feel (the knobs that make it a smooth, sneaky glider)
        this.steer = { maxTurn: 0.06, maxTurnUrgent: 0.16, probe: 42 };
        this.slither = { amp: 0.33, freq: 0.055 }; // gentle side-to-side weave of the path itself
        this.escapeTimer = 0;
        this.escapeHeading = 0;

        this.mouse = { x: 0, y: 0, active: false };
        this.curiousRadius = 400; // how close the cursor must be for the snake to notice it
        this.cursorCooldown = 0;
        this._curious = false;

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

        // Track the cursor so the snake can get curious and come say hi
        this.onMouseMove = (e) => {
            const rect = this.element.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            if (x >= 0 && y >= 0 && x <= rect.width && y <= rect.height) {
                this.mouse.x = x;
                this.mouse.y = y;
                this.mouse.active = true;
            } else {
                this.mouse.active = false;
            }
        };
        window.addEventListener('mousemove', this.onMouseMove, { passive: true });
        this.element.addEventListener('mouseleave', () => { this.mouse.active = false; });
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

    findFreePointNear(mx, my) {
        // Find the nearest gap the snake can occupy around a target (e.g. the cursor)
        const margin = 12;
        mx = Math.min(Math.max(mx, margin), this.canvas.width - margin);
        my = Math.min(Math.max(my, margin), this.canvas.height - margin);

        for (let r = 0; r <= 140; r += 14) {
            const steps = r === 0 ? 1 : Math.max(6, Math.round(r / 6));
            for (let k = 0; k < steps; k++) {
                const a = (Math.PI * 2 * k) / steps;
                const x = mx + Math.cos(a) * r;
                const y = my + Math.sin(a) * r;
                if (x < margin || x > this.canvas.width - margin ||
                    y < margin || y > this.canvas.height - margin) {
                    continue;
                }
                if (!this.isInOccupiedSpace(x, y) && this.canSnakePassThrough(x, y)) {
                    return { x, y };
                }
            }
        }
        return null;
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

    pickRoamWaypoint() {
        // Prefer waypoints that are far away and roughly ahead of us, so the snake
        // prowls onward instead of doubling back and coiling on a nearby target.
        let best = null;
        for (let i = 0; i < 6; i++) {
            const wp = this.findInterestingWaypoint();
            if (!wp) continue;
            const dx = wp.x - this.snake.x;
            const dy = wp.y - this.snake.y;
            const dist = Math.hypot(dx, dy) || 1;
            const ahead = this.snake.heading === undefined ? 0 :
                (Math.cos(this.snake.heading) * dx + Math.sin(this.snake.heading) * dy) / dist;
            const score = dist + ahead * 180; // far + ahead wins
            if (!best || score > best.score) best = { wp, score };
            if (dist > 200 && ahead > -0.1) break; // already a good prowl target
        }
        return best ? best.wp : null;
    }

    updateSnake() {
        // Sample the trail by DISTANCE, not per-frame, so the body is a consistent
        // length no matter the speed or refresh rate (evenly spaced points also make
        // the ribbon + travelling wave render cleanly).
        const lastPt = this.snake.trail[this.snake.trail.length - 1];
        if (!lastPt || Math.hypot(this.snake.x - lastPt.x, this.snake.y - lastPt.y) >= this.trailSpacing) {
            this.snake.trail.push({ x: this.snake.x, y: this.snake.y });
            if (this.snake.trail.length > this.snake.maxTrailLength) {
                this.snake.trail.shift();
            }
        }

        // Frame-rate–independent time step (baseline 60fps) so the snake travels at
        // the same real-world pace on 60Hz and 120Hz displays instead of racing.
        const nowT = (typeof performance !== 'undefined' && performance.now) ? performance.now() : Date.now();
        let dt = this._lastT ? (nowT - this._lastT) / 16.667 : 1;
        this._lastT = nowT;
        dt = Math.min(Math.max(dt, 0.5), 2.5);
        this.clock += dt;

        // Decrement timers
        if (this.snake.directionChangeTimer > 0) {
            this.snake.directionChangeTimer--;
        }
        if (this.snake.waypointTimer > 0) {
            this.snake.waypointTimer--;
        }
        
        // Pick a new waypoint periodically or if we don't have one
        if (!this.snake.targetWaypoint || this.snake.waypointTimer === 0) {
            this.snake.targetWaypoint = this.pickRoamWaypoint();
            this.snake.waypointTimer = this.snake.waypointRefreshTime; // MUCH longer - ~10 seconds per waypoint
        }
        
        // Check if we reached the waypoint
        if (this.snake.targetWaypoint) {
            const distToWaypoint = Math.sqrt(
                Math.pow(this.snake.x - this.snake.targetWaypoint.x, 2) +
                Math.pow(this.snake.y - this.snake.targetWaypoint.y, 2)
            );
            // Consider it "reached" from a bit of a distance: trying to pin a target
            // closer than our minimum turn radius just makes the snake orbit it.
            if (distToWaypoint < 34) {
                this.snake.targetWaypoint = this.pickRoamWaypoint();
                this.snake.waypointTimer = this.snake.waypointRefreshTime;
            }
        }

        // Cursor curiosity: when the pointer is nearby, the snake breaks off to sneak
        // over and investigate it. The steering below aims straight at the cursor while
        // this flag is set; here we just detect it and keep a gap-safe fallback target.
        this._curious = false;
        if (this.cursorCooldown > 0) this.cursorCooldown--;
        if (this.mouse.active) {
            const dm = Math.hypot(this.mouse.x - this.snake.x, this.mouse.y - this.snake.y);
            if (dm < this.curiousRadius) {
                this._curious = true;
                this.snake.stuckCounter = 0; // don't let the stuck-escape yank it away mid-visit
                if (this.cursorCooldown === 0) {
                    const target = this.findFreePointNear(this.mouse.x, this.mouse.y);
                    if (target) {
                        this.snake.targetWaypoint = target;
                        this.snake.waypointTimer = this.snake.waypointRefreshTime;
                        this.cursorCooldown = 18; // don't re-target every single frame
                    }
                }
            }
        }

        // Stuck detection: over the last ~second of travel, are we actually getting
        // anywhere? Works at any speed or frame-rate (no fragile variance threshold).
        this._posHist.push({ x: this.snake.x, y: this.snake.y, c: this.clock });
        while (this._posHist.length && this.clock - this._posHist[0].c > 60) {
            this._posHist.shift();
        }
        if (this._posHist.length > 20) {
            const p0 = this._posHist[0];
            const net = Math.hypot(this.snake.x - p0.x, this.snake.y - p0.y);
            if (net < 18) {
                this.snake.stuckCounter += 1;
            } else {
                this.snake.stuckCounter = Math.max(0, this.snake.stuckCounter - 1);
            }
        }
        
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
        
        const margin = 10;
        const W = this.canvas.width;
        const H = this.canvas.height;

        // Seed the heading from our current velocity the first time through
        if (this.snake.heading === undefined) {
            this.snake.heading = Math.atan2(this.snake.vy, this.snake.vx);
        }
        let heading = this.snake.heading;

        // Clear distance along a heading (stops at walls, occupied space or edges)
        const probe = this.steer.probe;
        const clearDist = (ang) => {
            const cx = Math.cos(ang), cy = Math.sin(ang);
            for (let d = 6; d <= probe; d += 4) {
                const px = this.snake.x + cx * d;
                const py = this.snake.y + cy * d;
                if (px < margin || px > W - margin || py < margin || py > H - margin ||
                    this.isInOccupiedSpace(px, py) || !this.canSnakePassThrough(px, py)) {
                    return d;
                }
            }
            return probe;
        };

        // If we've genuinely got stuck, aim at the most open direction and commit to
        // escaping it for a beat before resuming normal seeking.
        if (this.snake.stuckCounter > 22 && this.escapeTimer <= 0) {
            let bestA = heading, bestC = -1;
            for (let deg = 0; deg < 360; deg += 15) {
                const a = deg * Math.PI / 180;
                const c = clearDist(a);
                if (c > bestC) { bestC = c; bestA = a; }
            }
            heading = this.snake.heading = bestA;
            this.escapeHeading = bestA;
            this.escapeTimer = 28;
            this.snake.stuckCounter = 0;
            this.snake.targetWaypoint = this.pickRoamWaypoint();
            this.snake.waypointTimer = this.snake.waypointRefreshTime;
        }

        // Desired heading: toward the waypoint, with a gentle serpentine weave so it
        // slithers even on a straight run. (During an escape, just head for open space.)
        let desired;
        if (this.escapeTimer > 0) {
            this.escapeTimer -= dt;
            desired = this.escapeHeading;
        } else if (this._curious) {
            // Sneak straight at the cursor (with a lighter weave). It can't pin a point
            // tighter than its turn radius, so it naturally circles the pointer.
            desired = Math.atan2(this.mouse.y - this.snake.y, this.mouse.x - this.snake.x);
            desired += Math.sin(this.clock * this.slither.freq) * this.slither.amp * 0.4;
        } else {
            const wp = this.snake.targetWaypoint;
            desired = wp
                ? Math.atan2(wp.y - this.snake.y, wp.x - this.snake.x)
                : heading;
            desired += Math.sin(this.clock * this.slither.freq) * this.slither.amp;
        }

        // Obstacle avoidance: if the desired way isn't clear, slide toward the nearest
        // heading that is — so it hugs edges and corners instead of ricocheting off them.
        if (clearDist(desired) < probe) {
            let best = desired, bestScore = -Infinity;
            for (let deg = 12; deg <= 165; deg += 12) {
                for (let s = -1; s <= 1; s += 2) {
                    const a = desired + s * deg * Math.PI / 180;
                    const score = clearDist(a) - deg * 0.14; // most clearance, least detour
                    if (score > bestScore) { bestScore = score; best = a; }
                }
            }
            desired = best;
        }

        // Ease the heading toward desired at a capped turn rate (sharper only when a
        // wall is right in front). This gradual turning is what kills the jitter.
        const front = clearDist(heading);
        const maxTurn = (front < 16 ? this.steer.maxTurnUrgent : this.steer.maxTurn) * dt;
        let dh = desired - heading;
        while (dh > Math.PI) dh -= Math.PI * 2;
        while (dh < -Math.PI) dh += Math.PI * 2;
        const turned = Math.max(-maxTurn, Math.min(maxTurn, dh));
        heading += turned;
        this.snake.heading = heading;

        // Speed: sneaky and deliberate. Open up in clear space, ease into tight gaps,
        // and slow down through sharp turns.
        const openness = Math.min(1, front / probe);
        let targetSpeed = this.snake.baseSpeed * (0.5 + openness * 0.85);
        targetSpeed *= 1 - Math.min(Math.abs(turned) / (maxTurn || 1), 1) * 0.45;
        if (this._curious) targetSpeed *= 1.2;
        this.snake.speed += (targetSpeed - this.snake.speed) * 0.08 * dt;

        // Integrate. Glide forward, or if the way is blocked, curl toward the clearer
        // side and try again next frame (so it can never freeze against an edge).
        const step = this.snake.speed * dt;
        const nextX = this.snake.x + Math.cos(heading) * step;
        const nextY = this.snake.y + Math.sin(heading) * step;
        if (!this.isInOccupiedSpace(nextX, nextY) && this.canSnakePassThrough(nextX, nextY) &&
            nextX >= margin && nextX <= W - margin && nextY >= margin && nextY <= H - margin) {
            this.snake.x = nextX;
            this.snake.y = nextY;
        } else {
            const left = clearDist(heading + Math.PI / 2);
            const right = clearDist(heading - Math.PI / 2);
            this.snake.heading = heading + (left >= right ? 1 : -1) * this.steer.maxTurnUrgent * 2 * dt;
            this.snake.stuckCounter += 2;
        }

        // Keep velocity in sync so the head renders pointing where it travels.
        this.snake.vx = Math.cos(this.snake.heading) * this.snake.speed;
        this.snake.vy = Math.sin(this.snake.heading) * this.snake.speed;
    }
    drawSnake() {
        // Neon serpentine ribbon - indigo palette on black
        const ctx = this.ctx;
        const t = this.clock;

        // Body points run tail -> head (head is the live position, not yet in the trail)
        const pts = this.snake.trail.concat([{ x: this.snake.x, y: this.snake.y }]);
        const n = pts.length;
        const headHalf = this.snake.size * 1.05; // body half-width at the head

        if (n >= 3) {
            // Build a smoothed centerline with a travelling serpentine wave laid over it
            const center = new Array(n);
            for (let i = 0; i < n; i++) {
                const prev = pts[Math.max(0, i - 1)];
                const next = pts[Math.min(n - 1, i + 1)];
                let tx = next.x - prev.x;
                let ty = next.y - prev.y;
                const tl = Math.hypot(tx, ty) || 1;
                tx /= tl; ty /= tl;
                const nx = -ty; // unit normal
                const ny = tx;
                const f = i / (n - 1); // 0 at tail, 1 at head
                const envelope = Math.sin(Math.PI * f); // fades the wave to 0 at both ends
                const wave = Math.sin(f * this.wave.freq - t * this.wave.speed) * this.wave.amp * envelope;
                center[i] = { x: pts[i].x + nx * wave, y: pts[i].y + ny * wave, nx, ny, f };
            }

            const hw = (i) => headHalf * Math.pow(center[i].f, 0.65) + 0.4; // taper thin -> full

            // Filled body with a bloom and a tail->head gradient
            const head = center[n - 1];
            const tail = center[0];
            const grad = ctx.createLinearGradient(tail.x, tail.y, head.x, head.y);
            grad.addColorStop(0, 'rgba(79, 70, 229, 0)');
            grad.addColorStop(0.35, 'rgba(99, 102, 241, 0.55)');
            grad.addColorStop(1, 'rgba(165, 180, 252, 0.95)');

            ctx.save();
            ctx.lineJoin = 'round';
            ctx.lineCap = 'round';
            ctx.shadowColor = 'rgba(99, 102, 241, 0.85)';
            ctx.shadowBlur = 12;
            ctx.beginPath();
            ctx.moveTo(center[0].x + center[0].nx * hw(0), center[0].y + center[0].ny * hw(0));
            for (let i = 1; i < n; i++) {
                ctx.lineTo(center[i].x + center[i].nx * hw(i), center[i].y + center[i].ny * hw(i));
            }
            for (let i = n - 1; i >= 0; i--) {
                ctx.lineTo(center[i].x - center[i].nx * hw(i), center[i].y - center[i].ny * hw(i));
            }
            ctx.closePath();
            ctx.fillStyle = grad;
            ctx.fill();
            ctx.restore();

            // Bright spine highlight along the front ~60% of the body
            ctx.save();
            ctx.lineJoin = 'round';
            ctx.lineCap = 'round';
            ctx.shadowColor = 'rgba(199, 210, 254, 0.9)';
            ctx.shadowBlur = 6;
            const start = Math.max(0, n - Math.floor(n * 0.6));
            ctx.beginPath();
            ctx.moveTo(center[start].x, center[start].y);
            for (let i = start + 1; i < n; i++) {
                ctx.lineTo(center[i].x, center[i].y);
            }
            ctx.strokeStyle = 'rgba(224, 231, 255, 0.75)';
            ctx.lineWidth = Math.max(1, this.snake.size * 0.5);
            ctx.stroke();
            ctx.restore();
        }

        // Living, directional head with a gentle breathing pulse
        const heading = Math.atan2(this.snake.vy, this.snake.vx);
        const pulse = 1 + 0.12 * Math.sin(t * 0.12);
        const eager = this._curious ? 1.25 : 1; // brightens when chasing the cursor
        const s = this.snake.size;

        ctx.save();
        ctx.translate(this.snake.x, this.snake.y);
        ctx.rotate(heading);

        // Outer glow
        ctx.shadowColor = 'rgba(99, 102, 241, 0.9)';
        ctx.shadowBlur = 16 * eager;
        ctx.beginPath();
        ctx.ellipse(0, 0, s * 2.4 * pulse, s * 1.7 * pulse, 0, 0, Math.PI * 2);
        ctx.fillStyle = 'rgba(79, 70, 229, 0.35)';
        ctx.fill();

        // Inner glow, nudged forward so the head reads as leading
        ctx.shadowBlur = 8 * eager;
        ctx.beginPath();
        ctx.ellipse(s * 0.3, 0, s * 1.5 * pulse, s * 1.15 * pulse, 0, 0, Math.PI * 2);
        ctx.fillStyle = 'rgba(129, 140, 248, 0.9)';
        ctx.fill();

        // Bright core
        ctx.shadowBlur = 0;
        ctx.beginPath();
        ctx.arc(s * 0.5, 0, s * 0.8 * pulse, 0, Math.PI * 2);
        ctx.fillStyle = 'rgba(224, 231, 255, 1)';
        ctx.fill();

        ctx.restore();
    }

    animate() {
        this.frame++;
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
        if (this.onMouseMove) {
            window.removeEventListener('mousemove', this.onMouseMove);
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


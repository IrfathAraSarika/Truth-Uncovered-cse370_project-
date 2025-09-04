    <?php
  session_start();
include 'DBconnect.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ✅ Handle category selection on the same page
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_id'], $_POST['category_name'])) {
    $_SESSION['category_id'] = $_POST['category_id'];
    $_SESSION['category_name'] = $_POST['category_name'];

    // Redirect to categories.php after storing
    header("Location: case.php");
    exit();
}
  
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Truth Uncovered - Crime Categories</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 25%, #16213e 50%, #0f3460 75%, #1e3a8a 100%);
            position: relative;
            overflow-x: hidden;
            color: #ffffff;
        }

        /* Background Image Layer */
        .background-image {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?auto=format&fit=crop&w=1920&q=80') center/cover;
            opacity: 0.15;
            z-index: 0;
        }

        /* Animated Background Orbs */
        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .floating-orb {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(45deg, rgba(59, 130, 246, 0.2), rgba(147, 51, 234, 0.2));
            filter: blur(1px);
            animation: float 8s ease-in-out infinite;
        }

        .orb1 { 
            width: 400px; 
            height: 400px; 
            top: -10%; 
            left: 80%; 
            animation-delay: 0s;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.3), transparent);
        }
        
        .orb2 { 
            width: 300px; 
            height: 300px; 
            top: 70%; 
            left: -15%; 
            animation-delay: 3s;
            background: radial-gradient(circle, rgba(147, 51, 234, 0.3), transparent);
        }
        
        .orb3 { 
            width: 250px; 
            height: 250px; 
            top: 30%; 
            left: 20%; 
            animation-delay: 5s;
            background: radial-gradient(circle, rgba(236, 72, 153, 0.2), transparent);
        }

        @keyframes float {
            0%, 100% { 
                transform: translate(0, 0) rotate(0deg) scale(1); 
            }
            33% { 
                transform: translate(30px, -30px) rotate(120deg) scale(1.1); 
            }
            66% { 
                transform: translate(-20px, 20px) rotate(240deg) scale(0.9); 
            }
        }

        /* Container */
        .container {
            position: relative;
            z-index: 10;
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 60px;
            animation: slideUp 0.8s ease-out;
        }

        .logo-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
        }

        .logo-icon::after {
            content: '🔍';
            font-size: 40px;
        }

        .main-title {
            font-size: 3rem;
            font-weight: 800;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
        }

        .subtitle {
            color: #94a3b8;
            font-size: 1.2rem;
            margin-bottom: 30px;
        }

        .mission-statement {
            max-width: 800px;
            margin: 0 auto;
            font-size: 1.1rem;
            line-height: 1.8;
            color: #e2e8f0;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Categories Grid */
        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-top: 60px;
        }

        .category-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 30px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            cursor: pointer;
            animation: slideUp 0.8s ease-out;
        }

        .category-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, transparent, rgba(59, 130, 246, 0.1), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: -1;
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            border-color: rgba(59, 130, 246, 0.5);
        }

        .category-card:hover::before {
            opacity: 1;
        }

        .category-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin-bottom: 20px;
            position: relative;
            box-shadow: 0 8px 25px rgba(239, 68, 68, 0.3);
        }

        .category-card:nth-child(2) .category-icon {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.3);
        }

        .category-card:nth-child(3) .category-icon {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.3);
        }

        .category-card:nth-child(4) .category-icon {
            background: linear-gradient(135deg, #06b6d4, #0891b2);
            box-shadow: 0 8px 25px rgba(6, 182, 212, 0.3);
        }

        .category-card:nth-child(5) .category-icon {
            background: linear-gradient(135deg, #10b981, #059669);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
        }

        .category-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 15px;
        }

        .category-description {
            color: #cbd5e1;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .category-stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 1.4rem;
            font-weight: 700;
            color: #3b82f6;
        }

        .stat-label {
            font-size: 0.8rem;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .vision-section {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 12px;
            padding: 20px;
            border-left: 4px solid #3b82f6;
        }

        .vision-title {
            font-size: 1rem;
            font-weight: 600;
            color: #3b82f6;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .vision-text {
            color: #e2e8f0;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .explore-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 20px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .explore-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
        }

        /* Interactive Features */
        .interactive-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.9), rgba(139, 92, 246, 0.9));
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
            border-radius: 20px;
        }

        .category-card:hover .interactive-overlay {
            opacity: 1;
        }

        .overlay-content {
            text-align: center;
            color: white;
        }

        .overlay-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .overlay-text {
            font-size: 1rem;
            opacity: 0.9;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .main-title {
                font-size: 2rem;
            }

            .categories-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .category-card {
                padding: 25px;
            }

            .container {
                padding: 30px 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Background Image Layer -->
    <div class="background-image"></div>

    <!-- Animated Background -->
    <div class="bg-animation">
        <div class="floating-orb orb1"></div>
        <div class="floating-orb orb2"></div>
        <div class="floating-orb orb3"></div>
    </div>

    <!-- Main Container -->
    <div class="container">
        <!-- Header Section -->
        <div class="header"    onclick="window.location.href='index.php'" >
            <div class="logo-icon"></div>
            <h1 class="main-title">TRUTH UNCOVERED</h1>
            <p class="subtitle">Fighting Crime Through Community Investigation</p>
            
            <div class="mission-statement">
                <strong>Our Mission:</strong> Truth Uncovered is Bangladesh's premier digital platform dedicated to combating crime through community-driven investigation and transparent reporting. We empower citizens to report, track, and collectively address the most pressing criminal activities affecting our society. Together, we build a safer Bangladesh through the power of truth and transparency.
            </div>
        </div>

        <!-- Categories Grid -->
        <div class="categories-grid">
            <!-- Corruption Category -->
            <div class="category-card" onclick="exploreCategory('corruption')">
                <div class="category-icon">🏛️</div>
                <h2 class="category-title">Corruption</h2>
                <p class="category-description">
                    Corruption remains one of Bangladesh's most pervasive challenges, affecting everything from government services to business operations. Our platform tracks bribery, embezzlement, nepotism, and misuse of public resources.
                </p>
                
                <div class="category-stats">
                    <div class="stat-item">
                        <div class="stat-number">2,847</div>
                        <div class="stat-label">Reports Filed</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">1,203</div>
                        <div class="stat-label">Under Review</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">845</div>
                        <div class="stat-label">Resolved</div>
                    </div>
                </div>

                <div class="vision-section">
                    <h3 class="vision-title">Our Vision</h3>
                    <p class="vision-text">
                        Create a corruption-free Bangladesh by establishing transparent reporting mechanisms, supporting whistleblowers, and ensuring accountability at all levels of governance and business.
                    </p>
                </div>

            <form method="post" style="display:inline;">
                <input type="hidden" name="category_id" value="1">
                <input type="hidden" name="category_name" value="Corruption">
                <button type="submit" class="explore-btn">Explore Corruption Cases </button>
            </form>


            </div>

            <!-- Antisocial Category -->
            <div class="category-card" onclick="exploreCategory('antisocial')">
                <div class="category-icon">⚠️</div>
                <h2 class="category-title">Antisocial Behavior</h2>
                <p class="category-description">
                    From street harassment to public disorder, antisocial behaviors disrupt community harmony. We document incidents of public nuisance, vandalism, drug abuse, and activities that threaten social cohesion.
                </p>
                
                <div class="category-stats">
                    <div class="stat-item">
                        <div class="stat-number">1,956</div>
                        <div class="stat-label">Reports Filed</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">723</div>
                        <div class="stat-label">Under Review</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">1,089</div>
                        <div class="stat-label">Resolved</div>
                    </div>
                </div>

                <div class="vision-section">
                    <h3 class="vision-title">Our Vision</h3>
                    <p class="vision-text">
                        Build stronger communities by addressing antisocial behaviors through community engagement, education, and coordinated response with local authorities and social organizations.
                    </p>
                </div>

<form method="post" style="display:inline;">
    <input type="hidden" name="category_id" value="2">
    <input type="hidden" name="category_name" value="Antisocial Behavior">
    <button type="submit" class="explore-btn">Explore Antisocial Cases</button>
</form>


                <!-- <div class="interactive-overlay">
                    <div class="overlay-content">
                        <h3 class="overlay-title">Restore Order</h3>
                        <p class="overlay-text">Document • Address • Heal</p>
                    </div>
                </div> -->
            </div>

            <!-- Hazard Category -->
            <div class="category-card" onclick="exploreCategory('hazard')">
                <div class="category-icon">🚨</div>
                <h2 class="category-title">Public Hazards</h2>
                <p class="category-description">
                    Safety hazards in public spaces, workplaces, and infrastructure pose serious risks to citizens. We track unsafe buildings, environmental hazards, traffic violations, and industrial safety breaches.
                </p>
                
                <div class="category-stats">
                    <div class="stat-item">
                        <div class="stat-number">3,421</div>
                        <div class="stat-label">Reports Filed</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">1,567</div>
                        <div class="stat-label">Under Review</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">1,654</div>
                        <div class="stat-label">Resolved</div>
                    </div>
                </div>

                <div class="vision-section">
                    <h3 class="vision-title">Our Vision</h3>
                    <p class="vision-text">
                        Ensure public safety by identifying and addressing hazardous conditions before they cause harm, working with authorities to implement proper safety measures and regulations.
                    </p>
                </div>

                            <form method="post" style="display:inline;">
    <input type="hidden" name="category_id" value="3">
    <input type="hidden" name="category_name" value="Public Hazards">
    <button type="submit" class="explore-btn">Explore Hazard Cases</button>
</form>
                   



            </div>

            <!-- Harassment Category -->
            <div class="category-card" onclick="exploreCategory('harassment')">
                <div class="category-icon">🛡️</div>
                <h2 class="category-title">Harassment</h2>
                <p class="category-description">
                    Harassment in all its forms - workplace, sexual, cyber, or psychological - undermines human dignity. We provide a safe platform for reporting and addressing harassment incidents across Bangladesh.
                </p>
                
                <div class="category-stats">
                    <div class="stat-item">
                        <div class="stat-number">1,634</div>
                        <div class="stat-label">Reports Filed</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">892</div>
                        <div class="stat-label">Under Review</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">542</div>
                        <div class="stat-label">Resolved</div>
                    </div>
                </div>

                <div class="vision-section">
                    <h3 class="vision-title">Our Vision</h3>
                    <p class="vision-text">
                        Create a harassment-free society where everyone can live and work with dignity, providing support to victims and ensuring perpetrators are held accountable.
                    </p>
                </div>

                
                <form method="post" style="display:inline;">
    <input type="hidden" name="category_id" value="4">
    <input type="hidden" name="category_name" value="Harassment">
    <button type="submit" class="explore-btn">Explore Harassment Cases</button>
</form>

              
            </div>

            <!-- Dowry Category -->
            <div class="category-card" onclick="exploreCategory('dowry')">
                <div class="category-icon">💔</div>
                <h2 class="category-title">Dowry Violence</h2>
                <p class="category-description">
                    Despite legal prohibitions, dowry-related violence continues to harm families across Bangladesh. We track dowry demands, related violence, and work to support victims while pursuing justice.
                </p>
                
                <div class="category-stats">
                    <div class="stat-item">
                        <div class="stat-number">896</div>
                        <div class="stat-label">Reports Filed</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">445</div>
                        <div class="stat-label">Under Review</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">312</div>
                        <div class="stat-label">Resolved</div>
                    </div>
                </div>

                <div class="vision-section">
                    <h3 class="vision-title">Our Vision</h3>
                    <p class="vision-text">
                        Eliminate dowry practices through education, legal action, and social awareness campaigns, ensuring every marriage is based on mutual respect rather than financial transactions.
                    </p>
                </div>


            <form method="post" style="display:inline;">
    <input type="hidden" name="category_id" value="5">
    <input type="hidden" name="category_name" value="Dowry Violence">
    <button type="submit" class="explore-btn">Explore Dowry Cases</button>
</form>




            </div>
        </div>
    </div>

    <script>
        // Interactive category exploration
        function exploreCategory(categoryType) {
            console.log(`Exploring ${categoryType} category`);
            
            // Add visual feedback
            const cards = document.querySelectorAll('.category-card');
            cards.forEach(card => {
                card.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    card.style.transform = 'scale(1)';
                }, 200);
            });
            
            // Simulate navigation to category details page
            setTimeout(() => {
                alert(`Loading ${categoryType} investigation dashboard...`);
                // In real implementation: window.location.href = `category-details.php?type=${categoryType}`;
            }, 500);
        }

        // Interactive animations
        document.querySelectorAll('.category-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Simulate real-time updates to statistics
        function updateStats() {
            const statNumbers = document.querySelectorAll('.stat-number');
            statNumbers.forEach(stat => {
                const currentValue = parseInt(stat.textContent.replace(/,/g, ''));
                const increment = Math.floor(Math.random() * 5);
                if (increment > 0) {
                    const newValue = currentValue + increment;
                    stat.textContent = newValue.toLocaleString();
                    stat.style.color = '#22c55e';
                    setTimeout(() => {
                        stat.style.color = '#3b82f6';
                    }, 2000);
                }
            });
        }

        // Update stats every 30 seconds
        setInterval(updateStats, 30000);

        // Add scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.category-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(card);
        });

        // Add particle effect for interactions
        document.addEventListener('click', function(e) {
            if (e.target.closest('.explore-btn') || e.target.closest('.category-card')) {
                createParticles(e.clientX, e.clientY);
            }
        });

        function createParticles(x, y) {
            for (let i = 0; i < 8; i++) {
                const particle = document.createElement('div');
                particle.style.cssText = `
                    position: fixed;
                    width: 4px;
                    height: 4px;
                    background: linear-gradient(135deg, #3b82f6, #8b5cf6);
                    border-radius: 50%;
                    pointer-events: none;
                    z-index: 9999;
                    left: ${x}px;
                    top: ${y}px;
                `;
                document.body.appendChild(particle);

                const angle = (Math.PI * 2 * i) / 8;
                const velocity = 2 + Math.random() * 2;
                const lifetime = 800;
                const startTime = Date.now();

                const animate = () => {
                    const elapsed = Date.now() - startTime;
                    const progress = elapsed / lifetime;

                    if (progress < 1) {
                        const distance = velocity * elapsed;
                        const currentX = x + Math.cos(angle) * distance;
                        const currentY = y + Math.sin(angle) * distance - (progress * 40);
                        
                        particle.style.left = currentX + 'px';
                        particle.style.top = currentY + 'px';
                        particle.style.opacity = 1 - progress;
                        
                        requestAnimationFrame(animate);
                    } else {
                        particle.remove();
                    }
                };
                
                requestAnimationFrame(animate);
            }
        }
    </script>


</body>
</html>
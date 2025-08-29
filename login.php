<?php
session_start();
include 'DBconnect.php'; 

$message = "";

// Your query (fetch all users once)
$sql = "SELECT User_ID, Name, Email, Password FROM Users";
$result = $conn->query($sql);
$users = $result->fetch_all(MYSQLI_ASSOC);

// Debug: log all users to console
echo "<script>console.log(" . json_encode($users) . ");</script>";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    $found = false;

    foreach ($users as $user) {
        if ($email === $user['Email']) {
            // If password is hashed, verify with password_verify
            if (!empty($user['Password']) && password_verify($password, $user['Password'])) {
                $_SESSION['user_id'] = $user['User_ID'];
                $_SESSION['email']   = $user['Email'];
                  $_SESSION['username'] = $user['Name'];
                header("Location: index.php");
                exit;
            }
            // In case some accounts still have plain text (not recommended)
            elseif ($password === $user['Password']) {
                $_SESSION['user_id'] = $user['User_ID'];
                $_SESSION['email']   = $user['Email'];
                  $_SESSION['username'] = $user['Name'];
                header("Location: index.php");
                exit;
            }
        }
    }

    // If no match found
    $message = "❌ Invalid email or password.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Truth Uncovered - Login Portal</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 25%, #16213e 50%, #0f3460 75%, #1e3a8a 100%);
            position: relative;
            overflow: hidden;
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
            left: 70%; 
            animation-delay: 0s;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.3), transparent);
        }
        
        .orb2 { 
            width: 300px; 
            height: 300px; 
            top: 60%; 
            left: -10%; 
            animation-delay: 3s;
            background: radial-gradient(circle, rgba(147, 51, 234, 0.3), transparent);
        }
        
        .orb3 { 
            width: 250px; 
            height: 250px; 
            top: 20%; 
            left: 30%; 
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

        /* Main Login Container */
        .login-container {
            position: relative;
            z-index: 10;
            animation: slideUp 0.6s ease-out;
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

        /* Glass Login Box */
        .login-box {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            padding: 50px 40px;
            width: 420px;
            box-shadow: 
                0 8px 32px 0 rgba(0, 0, 0, 0.37),
                inset 0 0 0 1px rgba(255, 255, 255, 0.08);
            position: relative;
            overflow: hidden;
        }

        /* Animated Border Gradient */
        .login-box::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, #3b82f6, #8b5cf6, #ec4899, #3b82f6);
            background-size: 400% 400%;
            border-radius: 24px;
            z-index: -1;
            opacity: 0;
            transition: opacity 0.3s ease;
            animation: gradientShift 3s ease infinite;
        }

        .login-box:hover::before {
            opacity: 0.7;
        }

        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        /* Logo Section */
        .logo-section {
            text-align: center;
            margin-bottom: 35px;
        }

        .logo-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 15px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            box-shadow: 0 10px 30px rgba(59, 130, 246, 0.3);
        }

        .logo-icon::after {
            content: '🔍';
            font-size: 36px;
            filter: brightness(0) invert(1);
        }

        .logo-text {
            font-size: 1.8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
        }

        .tagline {
            color: #94a3b8;
            font-size: 0.9rem;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            display: block;
            color: #e2e8f0;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-input {
            width: 100%;
            padding: 14px 16px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: #ffffff;
            font-size: 1rem;
            outline: none;
            transition: all 0.3s ease;
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .form-input:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(59, 130, 246, 0.5);
            box-shadow: 
                0 0 0 3px rgba(59, 130, 246, 0.1),
                inset 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        /* Remember Me & Forgot Password */
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .checkbox-wrapper input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #3b82f6;
            cursor: pointer;
        }

        .checkbox-wrapper label {
            color: #94a3b8;
            font-size: 0.9rem;
            cursor: pointer;
        }

        .forgot-link {
            color: #3b82f6;
            font-size: 0.9rem;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .forgot-link:hover {
            color: #8b5cf6;
            text-decoration: underline;
        }

        /* Login Button */
        .login-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .login-btn:hover::before {
            left: 100%;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(59, 130, 246, 0.4);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            margin: 30px 0;
            gap: 15px;
        }

        .divider-line {
            flex: 1;
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
        }

        .divider-text {
            color: #64748b;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Social Login */
        .social-login {
            display: flex;
            gap: 15px;
        }

        .social-btn {
            flex: 1;
            padding: 12px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: #e2e8f0;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .social-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        /* Sign Up Link */
        .signup-link {
            text-align: center;
            margin-top: 25px;
            color: #94a3b8;
            font-size: 0.9rem;
        }

        .signup-link a {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .signup-link a:hover {
            color: #8b5cf6;
            text-decoration: underline;
        }

        /* Error/Success Messages */
        .message {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            animation: slideDown 0.3s ease;
        }

        .message.error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }

        .message.success {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #86efac;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 480px) {
            .login-box {
                width: 90%;
                padding: 35px 25px;
            }

            .logo-text {
                font-size: 1.5rem;
            }

            .social-login {
                flex-direction: column;
            }
        }

        /* Loading State */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
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

    <!-- Login Container -->
    <div class="login-container">
        <div class="login-box">
            <!-- Logo Section -->
            <div class="logo-section">
                <div class="logo-icon"></div>
                <h1 class="logo-text">TRUTH UNCOVERED</h1>
                <p class="tagline">Pixel Investigation Portal</p>
            </div>

            <!-- Message Display (PHP Integration Point) -->
            <?php if (!empty($message)): ?>
                <div class="message <?= $messageType ?? 'error' ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input 
                        type="email" 
                        class="form-input" 
                        id="email" 
                        name="email" 
                        placeholder="Enter your email"
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input 
                        type="password" 
                        class="form-input" 
                        id="password" 
                        name="password" 
                        placeholder="Enter your password"
                        required
                    >
                </div>

                <div class="form-options">
                    <div class="checkbox-wrapper">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Remember me</label>
                    </div>
                    <a href="#" class="forgot-link">Forgot password?</a>
                </div>

                <button type="submit" class="login-btn">
                    <span class="btn-text">ACCESS TRUTH</span>
                </button>
            </form>

            <!-- Divider -->
            <div class="divider">
                <div class="divider-line"></div>
                <span class="divider-text">OR</span>
                <div class="divider-line"></div>
            </div>

            <!-- Social Login -->
            <div class="social-login">
                <button type="button" class="social-btn">
                    <span>🔐</span> Secure ID
                </button>
                <button type="button" class="social-btn">
                    <span>🛡️</span> Agent Login
                </button>
            </div>

            <!-- Sign Up Link -->
            <div class="signup-link">
                New investigator? <a href="signup.php">Request Access</a>
            </div>
        </div>
    </div>

    <script>
        // Particle effect on click
        document.addEventListener('click', function(e) {
            if (e.target.closest('.login-btn')) {
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
                const lifetime = 1000;
                const startTime = Date.now();

                const animate = () => {
                    const elapsed = Date.now() - startTime;
                    const progress = elapsed / lifetime;

                    if (progress < 1) {
                        const distance = velocity * elapsed;
                        const currentX = x + Math.cos(angle) * distance;
                        const currentY = y + Math.sin(angle) * distance - (progress * 50);
                        
                        particle.style.left = currentX + 'px';
                        particle.style.top = currentY + 'px';
                        particle.style.opacity = 1 - progress;
                        particle.style.transform = `scale(${1 - progress * 0.5})`;
                        
                        requestAnimationFrame(animate);
                    } else {
                        particle.remove();
                    }
                };
                
                requestAnimationFrame(animate);
            }
        }

        // Form validation enhancement
        const form = document.querySelector('form');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');

        form.addEventListener('submit', function(e) {
            // Add loading state to button
            const btn = form.querySelector('.login-btn');
            const btnText = btn.querySelector('.btn-text') || btn;
            
            if (!btn.disabled) {
                btnText.innerHTML = '<span class="loading"></span>';
                btn.disabled = true;
                btn.style.opacity = '0.7';
            }
        });

        // Input animation
        const inputs = document.querySelectorAll('.form-input');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
    </script>
</body>
</html>
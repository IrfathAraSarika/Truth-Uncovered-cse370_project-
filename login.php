<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Truth Uncovered - Empowering Bangladesh Through Transparency</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #006a4e 0%, #228b22 30%, #32cd32 70%, #90ee90 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
        }

        .background-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(2deg); }
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 
                0 32px 64px rgba(0, 0, 0, 0.2),
                0 16px 32px rgba(0, 0, 0, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.8);
            padding: 3rem;
            width: 100%;
            max-width: 480px;
            position: relative;
            z-index: 10;
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: slideIn 0.8s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .logo-section {
            text-align: center;
            margin-bottom: 2.5rem;
            position: relative;
        }

        .logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #006a4e, #228b22);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            box-shadow: 
                0 16px 32px rgba(0, 106, 78, 0.3),
                0 8px 16px rgba(0, 106, 78, 0.2);
            position: relative;
            overflow: hidden;
        }

        .logo::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transform: rotate(45deg);
            animation: logoShine 3s ease-in-out infinite;
        }

        @keyframes logoShine {
            0%, 100% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            50% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }

        .logo-icon {
            font-size: 2.5rem;
            color: white;
            font-weight: bold;
            z-index: 1;
        }

        .brand-name {
            font-size: 2.2rem;
            font-weight: 800;
            background: linear-gradient(135deg, #006a4e, #228b22, #32cd32);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
            letter-spacing: -1px;
        }

        .tagline {
            color: #666;
            font-size: 1rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .mission-text {
            color: #888;
            font-size: 0.85rem;
            line-height: 1.4;
            max-width: 400px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 1.8rem;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.6rem;
            color: #333;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .input-wrapper {
            position: relative;
        }

        .form-group input {
            width: 100%;
            padding: 1rem 1.2rem;
            border: 2px solid #e1e5e9;
            border-radius: 16px;
            font-size: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
        }

        .form-group input:focus {
            outline: none;
            border-color: #006a4e;
            box-shadow: 
                0 0 0 4px rgba(0, 106, 78, 0.1),
                0 8px 16px rgba(0, 106, 78, 0.1);
            transform: translateY(-2px);
        }

        .form-group input:hover {
            border-color: #228b22;
            transform: translateY(-1px);
        }

        .login-button {
            width: 100%;
            padding: 1.2rem 2rem;
            background: linear-gradient(135deg, #006a4e 0%, #228b22 50%, #32cd32 100%);
            color: white;
            border: none;
            border-radius: 16px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 1.5rem;
        }

        .login-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.6s;
        }

        .login-button:hover::before {
            left: 100%;
        }

        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 
                0 16px 32px rgba(0, 106, 78, 0.4),
                0 8px 16px rgba(0, 106, 78, 0.2);
        }

        .login-button:active {
            transform: translateY(0);
        }

        .divider {
            text-align: center;
            margin: 2rem 0;
            position: relative;
            color: #666;
            font-size: 0.9rem;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, #ddd, transparent);
        }

        .divider span {
            background: rgba(255, 255, 255, 0.95);
            padding: 0 1.5rem;
            position: relative;
        }

        .links-section {
            text-align: center;
            margin-top: 1.5rem;
        }

        .links-section a {
            color: #006a4e;
            text-decoration: none;
            font-weight: 600;
            margin: 0 1rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            position: relative;
        }

        .links-section a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #006a4e, #32cd32);
            transition: width 0.3s ease;
        }

        .links-section a:hover::after {
            width: 100%;
        }

        .links-section a:hover {
            color: #228b22;
            transform: translateY(-1px);
        }

        .security-note {
            background: rgba(0, 106, 78, 0.05);
            border: 1px solid rgba(0, 106, 78, 0.1);
            border-radius: 12px;
            padding: 1rem;
            margin-top: 1.5rem;
            text-align: center;
        }

        .security-note .shield-icon {
            font-size: 1.5rem;
            color: #006a4e;
            margin-bottom: 0.5rem;
        }

        .security-note p {
            color: #006a4e;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 0.3rem;
        }

        .security-note small {
            color: #666;
            font-size: 0.75rem;
            line-height: 1.4;
        }

        .floating-elements {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .floating-element {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(255, 255, 255, 0.6);
            border-radius: 50%;
            animation: floatUpDown 8s ease-in-out infinite;
        }

        .floating-element:nth-child(1) { top: 20%; left: 10%; animation-delay: 0s; }
        .floating-element:nth-child(2) { top: 60%; left: 85%; animation-delay: 2s; }
        .floating-element:nth-child(3) { top: 80%; left: 20%; animation-delay: 4s; }
        .floating-element:nth-child(4) { top: 30%; left: 70%; animation-delay: 6s; }

        @keyframes floatUpDown {
            0%, 100% { transform: translateY(0) rotate(0deg); opacity: 0.6; }
            50% { transform: translateY(-30px) rotate(180deg); opacity: 1; }
        }

        @media (max-width: 640px) {
            .login-container {
                margin: 1rem;
                padding: 2rem;
                max-width: 95%;
            }
            
            .brand-name {
                font-size: 1.8rem;
            }
            
            .logo {
                width: 60px;
                height: 60px;
            }
            
            .logo-icon {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="background-pattern"></div>
    
    <div class="floating-elements">
        <div class="floating-element"></div>
        <div class="floating-element"></div>
        <div class="floating-element"></div>
        <div class="floating-element"></div>
    </div>

    <div class="login-container">
        <div class="logo-section">
            <div class="logo">
                <div class="logo-icon">🛡️</div>
            </div>
            <h1 class="brand-name">Truth Uncovered</h1>
            <p class="tagline">Empowering Bangladesh Through Transparency</p>
            <p class="mission-text">A secure platform for citizens to report corruption, hazards, and human rights violations safely and anonymously.</p>
        </div>

        <form id="loginForm">
            <div class="form-group">
                <label for="email">Email or National ID</label>
                <div class="input-wrapper">
                    <input type="text" id="email" name="email" placeholder="Enter your email or National ID" required>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" placeholder="Enter your secure password" required>
                </div>
            </div>

            <button type="submit" class="login-button">
                Secure Login
            </button>
        </form>

        <div class="divider">
            <span>New to Truth Uncovered?</span>
        </div>

        <div class="links-section">
            <a href="#" onclick="showRegister()">Create Account</a>
            <a href="#" onclick="showForgotPassword()">Forgot Password?</a>
        </div>

        <div class="security-note">
            <div class="shield-icon">🔒</div>
            <p>Your Identity is Protected</p>
            <small>We use advanced encryption and anonymous reporting features to ensure your safety. All reports are handled with strict confidentiality following Bangladeshi privacy laws.</small>
        </div>
    </div>

    <script>
        // Form handling and animations
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            if (email && password) {
                // Add loading state
                const button = document.querySelector('.login-button');
                const originalText = button.textContent;
                button.textContent = 'Authenticating...';
                button.style.background = 'linear-gradient(135deg, #004d3a, #1a6b1a)';
                
                // Simulate authentication
                setTimeout(() => {
                    alert('Login successful! Redirecting to dashboard...');
                    button.textContent = originalText;
                    button.style.background = 'linear-gradient(135deg, #006a4e 0%, #228b22 50%, #32cd32 100%)';
                }, 2000);
            }
        });

        function showRegister() {
            alert('Registration page would open here. New users can create accounts with National ID verification for secure access to the reporting platform.');
        }

        function showForgotPassword() {
            alert('Password recovery would be initiated here with secure email verification to protect user accounts.');
        }

        // Add input focus effects
        const inputs = document.querySelectorAll('input');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.parentElement.style.transform = 'scale(1.02)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.parentElement.style.transform = 'scale(1)';
            });
        });

        // Add floating animation on scroll/movement
        document.addEventListener('mousemove', function(e) {
            const container = document.querySelector('.login-container');
            const x = (e.clientX / window.innerWidth - 0.5) * 20;
            const y = (e.clientY / window.innerHeight - 0.5) * 20;
            
            container.style.transform = `perspective(1000px) rotateX(${y * 0.1}deg) rotateY(${x * 0.1}deg)`;
        });

        // Reset transform when mouse leaves
        document.addEventListener('mouseleave', function() {
            const container = document.querySelector('.login-container');
            container.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg)';
        });
    </script>
</body>
</html>
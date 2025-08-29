<?php
include 'DBconnect.php';

// Insert user function
function registerUser($pdo, $data) {
echo "<script>console.log(" . json_encode($data) . ")</script>";
  
    $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("
        INSERT INTO users 
        (Name, Email, Phone, DOB, National_ID, Gender, Role, Street, City, Region, Postal_Code, Sub_SMS, Sub_Email, Sub_Blog_Following, Password) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    return $stmt->execute([
        $data['name'],
        $data['email'],
        $data['phone'],
        $data['dob'],
        $data['national_id'],
        $data['gender'],
        $data['role'],
        $data['street'],
        $data['city'],
        $data['region'],
        $data['postal_code'],
        isset($data['sub_sms']) ? 1 : 0,
        isset($data['sub_email']) ? 1 : 0,
        isset($data['sub_blog']) ? 1 : 0,
        $hashed_password
    ]);
}

// Handle form submit
$success = $error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (registerUser($conn, $_POST)) {
        $success = "✅ Account created successfully!";
    } else {
        $error = "❌ Something went wrong. Try again.";
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Truth Uncovered - Join the Investigation</title>
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
            overflow-x: hidden;
            padding: 20px;
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

        .orb4 { 
            width: 200px; 
            height: 200px; 
            top: 10%; 
            right: 30%; 
            animation-delay: 2s;
            background: radial-gradient(circle, rgba(34, 197, 94, 0.2), transparent);
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

        /* Main Signup Container */
        .signup-container {
            position: relative;
            z-index: 10;
            animation: slideUp 0.8s ease-out;
            width: 100%;
            max-width: 900px;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Glass Signup Box */
        .signup-box {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            padding: 40px;
            box-shadow: 
                0 8px 32px 0 rgba(0, 0, 0, 0.37),
                inset 0 0 0 1px rgba(255, 255, 255, 0.08);
            position: relative;
            overflow: hidden;
        }

        /* Animated Border Gradient */
        .signup-box::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, #3b82f6, #8b5cf6, #ec4899, #22c55e, #3b82f6);
            background-size: 400% 400%;
            border-radius: 24px;
            z-index: -1;
            opacity: 0;
            transition: opacity 0.3s ease;
            animation: gradientShift 4s ease infinite;
        }

        .signup-box:hover::before {
            opacity: 0.5;
        }

        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        /* Header Section */
        .header-section {
            text-align: center;
            margin-bottom: 35px;
        }

        .logo-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 15px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
        }

        .logo-icon::after {
            content: '🔍';
            font-size: 32px;
            filter: brightness(0) invert(1);
        }

        .logo-text {
            font-size: 1.6rem;
            font-weight: 800;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 6px;
        }

        .tagline {
            color: #94a3b8;
            font-size: 0.85rem;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        /* Form Grid */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px 30px;
            margin-bottom: 30px;
        }

        /* Form Groups */
        .form-group {
            position: relative;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        .form-label {
            display: block;
            color: #e2e8f0;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-input, .form-select {
            width: 100%;
            padding: 14px 16px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: #ffffff;
            font-size: 1rem;
            outline: none;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .form-input:focus, .form-select:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(59, 130, 246, 0.5);
            box-shadow: 
                0 0 0 3px rgba(59, 130, 246, 0.1),
                inset 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .form-select {
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="12" height="6" viewBox="0 0 12 6"><path fill="%23ffffff" d="M6 6L0 0h12z"/></svg>');
            background-repeat: no-repeat;
            background-position: right 16px center;
            background-size: 12px;
            appearance: none;
        }

        .form-select option {
            background: #1e293b;
            color: #ffffff;
        }

        /* Subscription Checkboxes */
        .subscription-group {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #e2e8f0;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .checkbox-item:hover {
            color: #3b82f6;
        }

        .checkbox-item input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #3b82f6;
            cursor: pointer;
            margin: 0;
        }

        /* Submit Button */
        .signup-btn {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            margin-top: 20px;
        }

        .signup-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .signup-btn:hover::before {
            left: 100%;
        }

        .signup-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(59, 130, 246, 0.4);
        }

        .signup-btn:active {
            transform: translateY(0);
        }

        /* Login Link */
        .login-link {
            text-align: center;
            margin-top: 25px;
            color: #94a3b8;
            font-size: 0.9rem;
        }

        .login-link a {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .login-link a:hover {
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

        /* Responsive Design */
        @media (max-width: 768px) {
            .signup-box {
                padding: 30px 25px;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .form-group.full-width {
                grid-column: span 1;
            }

            .subscription-group {
                flex-direction: column;
                gap: 15px;
            }

            .logo-text {
                font-size: 1.4rem;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 15px;
            }

            .signup-box {
                padding: 25px 20px;
            }

            .form-grid {
                gap: 15px;
            }
        }

        /* Input Focus Effects */
        .form-group {
            transition: transform 0.3s ease;
        }

        .form-input:focus {
            transform: scale(1.02);
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
        <div class="floating-orb orb4"></div>
    </div>

    <!-- Signup Container -->
    <div class="signup-container">
        <div class="signup-box">
            <!-- Header Section -->
            <div class="header-section">
                <div class="logo-icon"></div>
                <h1 class="logo-text">TRUTH UNCOVERED</h1>
                <p class="tagline">Join the Investigation Network</p>
            </div>

            <!-- Message Display -->
            <div id="messageContainer" style="display: none;"></div>

            <!-- Signup Form -->
            <form method="POST" action="" id="signupForm">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label" for="name">Full Name</label>
                        <input 
                            type="text" 
                            class="form-input" 
                            id="name" 
                            name="name" 
                            placeholder="Enter your full name"
                            required
                        >
                    </div>

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
                        <label class="form-label" for="phone">Phone Number</label>
                        <input 
                            type="tel" 
                            class="form-input" 
                            id="phone" 
                            name="phone" 
                            placeholder="Enter your phone number"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="dob">Date of Birth</label>
                        <input 
                            type="date" 
                            class="form-input" 
                            id="dob" 
                            name="dob"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="national_id">National ID</label>
                        <input 
                            type="text" 
                            class="form-input" 
                            id="national_id" 
                            name="national_id" 
                            placeholder="Enter your National ID"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="street">Street Address</label>
                        <input 
                            type="text" 
                            class="form-input" 
                            id="street" 
                            name="street" 
                            placeholder="Enter your street address"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="city">City</label>
                        <input 
                            type="text" 
                            class="form-input" 
                            id="city" 
                            name="city" 
                            placeholder="Enter your city"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="region">Region</label>
                        <input 
                            type="text" 
                            class="form-input" 
                            id="region" 
                            name="region" 
                            placeholder="Enter your region/state"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="postal_code">Postal Code</label>
                        <input 
                            type="text" 
                            class="form-input" 
                            id="postal_code" 
                            name="postal_code" 
                            placeholder="Enter postal code"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="gender">Gender</label>
                        <select class="form-select" id="gender" name="gender" required>
                            <option value="" disabled selected>Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="role">Investigation Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="" disabled selected>Select Role</option>
                            <option value="Citizen">Citizen Investigator</option>
                            <option value="Admin">Administrator</option>
                            <option value="NGO_Partner">NGO Partner</option>
                            <option value="Govt_Officer">Government Officer</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <input 
                            type="password" 
                            class="form-input" 
                            id="password" 
                            name="password" 
                            placeholder="Create a strong password"
                            required 
                            minlength="6"
                        >
                    </div>

                    <!-- Subscription Preferences -->
                    <div class="form-group full-width">
                        <label class="form-label">Notification Preferences</label>
                        <div class="subscription-group">
                            <div class="checkbox-item">
                                <input type="checkbox" id="sub_sms" name="sub_sms">
                                <label for="sub_sms">📱 SMS Updates</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" id="sub_email" name="sub_email" checked>
                                <label for="sub_email">📧 Email Alerts</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" id="sub_blog" name="sub_blog">
                                <label for="sub_blog">📰 Investigation Blog</label>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="signup-btn">
                    <span class="btn-text">JOIN THE INVESTIGATION</span>
                </button>
            </form>

            <!-- Login Link -->
            <div class="login-link">
                Already an investigator? <a href="#">Access Portal</a>
            </div>
        </div>
    </div>

    <script>
        // Particle effect on button click
        document.addEventListener('click', function(e) {
            if (e.target.closest('.signup-btn')) {
                createParticles(e.clientX, e.clientY);
            }
        });

        function createParticles(x, y) {
            for (let i = 0; i < 12; i++) {
                const particle = document.createElement('div');
                particle.style.cssText = `
                    position: fixed;
                    width: 6px;
                    height: 6px;
                    background: linear-gradient(135deg, #3b82f6, #8b5cf6, #ec4899);
                    border-radius: 50%;
                    pointer-events: none;
                    z-index: 9999;
                    left: ${x}px;
                    top: ${y}px;
                `;
                document.body.appendChild(particle);

                const angle = (Math.PI * 2 * i) / 12;
                const velocity = 3 + Math.random() * 3;
                const lifetime = 1200;
                const startTime = Date.now();

                const animate = () => {
                    const elapsed = Date.now() - startTime;
                    const progress = elapsed / lifetime;

                    if (progress < 1) {
                        const distance = velocity * elapsed;
                        const currentX = x + Math.cos(angle) * distance;
                        const currentY = y + Math.sin(angle) * distance - (progress * 60);
                        
                        particle.style.left = currentX + 'px';
                        particle.style.top = currentY + 'px';
                        particle.style.opacity = 1 - progress;
                        particle.style.transform = `scale(${1 - progress * 0.3})`;
                        
                        requestAnimationFrame(animate);
                    } else {
                        particle.remove();
                    }
                };
                
                requestAnimationFrame(animate);
            }
        }

        // Enhanced form validation and submission
        const form = document.getElementById('signupForm');
        const messageContainer = document.getElementById('messageContainer');

        form.addEventListener('submit', function(e) {
       
            
            // Add loading state to button
            const btn = form.querySelector('.signup-btn');
            const btnText = btn.querySelector('.btn-text');
            
            if (!btn.disabled) {
                btnText.innerHTML = '<span class="loading"></span> PROCESSING...';
                btn.disabled = true;
                btn.style.opacity = '0.7';

                // Simulate form processing
                setTimeout(() => {
                    showMessage('Account created successfully! Welcome to the investigation network.', 'success');
                    btn.disabled = false;
                    btn.style.opacity = '1';
                    btnText.innerHTML = 'JOIN THE INVESTIGATION';
                }, 2000);
            }
        });

        function showMessage(text, type) {
            messageContainer.innerHTML = `<div class="message ${type}">${text}</div>`;
            messageContainer.style.display = 'block';
            setTimeout(() => {
                messageContainer.style.display = 'none';
            }, 5000);
        }

        // Input animation effects
        const inputs = document.querySelectorAll('.form-input, .form-select');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.closest('.form-group').style.transform = 'scale(1.02)';
            });
            
            input.addEventListener('blur', function() {
                this.closest('.form-group').style.transform = 'scale(1)';
            });
        });

        // Enhanced input validation feedback
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');

        emailInput.addEventListener('blur', function() {
            if (this.value && !this.checkValidity()) {
                this.style.borderColor = 'rgba(239, 68, 68, 0.5)';
            } else if (this.value) {
                this.style.borderColor = 'rgba(34, 197, 94, 0.5)';
            }
        });

        passwordInput.addEventListener('input', function() {
            const strength = getPasswordStrength(this.value);
            updatePasswordStrength(strength);
        });

        function getPasswordStrength(password) {
            let score = 0;
            if (password.length >= 8) score++;
            if (/[A-Z]/.test(password)) score++;
            if (/[0-9]/.test(password)) score++;
            if (/[^A-Za-z0-9]/.test(password)) score++;
            return score;
        }

        function updatePasswordStrength(strength) {
            const colors = ['#ef4444', '#f97316', '#eab308', '#22c55e'];
            const passwordInput = document.getElementById('password');
            if (passwordInput.value.length > 0) {
                passwordInput.style.borderColor = `rgba(${strength === 0 ? '239, 68, 68' : 
                    strength === 1 ? '249, 115, 22' :
                    strength === 2 ? '234, 179, 8' : '34, 197, 94'}, 0.5)`;
            }
        }
    </script>
</body>
</html>
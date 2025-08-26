<?php
/*
 * DATABASE SETUP INSTRUCTIONS:
 * 
 * 1. Open phpMyAdmin in your browser (http://localhost/phpmyadmin)
 * 2. Create a new database called 'truth_uncovered'
 * 3. Run this SQL query to create the users table:
 * 
 * CREATE TABLE users (
 *     User_ID INT AUTO_INCREMENT PRIMARY KEY,
 *     name VARCHAR(255) NOT NULL,
 *     email VARCHAR(255) UNIQUE NOT NULL,
 *     phone VARCHAR(20) NOT NULL,
 *     dob DATE NOT NULL,
 *     national_id VARCHAR(17) UNIQUE NOT NULL,
 *     gender ENUM('Male', 'Female', 'Other', 'Prefer not to say') NOT NULL,
 *     role ENUM('Citizen', 'NGO_Partner', 'Govt_Officer') NOT NULL,
 *     street VARCHAR(500) NOT NULL,
 *     city VARCHAR(100) NOT NULL,
 *     region VARCHAR(100) NOT NULL,
 *     postal_code VARCHAR(10),
 *     password VARCHAR(255) NOT NULL,
 *     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
 * );
 * 
 * 4. Update the database credentials below if needed
 */

session_start();

// Database configuration - UPDATE THESE VALUES
$host = 'localhost';
$dbname = 'truth_uncovered';
$db_username = 'root';  // Default XAMPP username
$db_password = '';      // Default XAMPP password (empty)

// Initialize variables
$errors = [];
$success_message = '';
$pdo = null;

// Create database connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_username, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} else ($_SERVER['REQUEST_METHOD'] == 'POST' && $pdo === null) {
    $errors[] = "Database connection is not available. Please try again later.";
} catch(PDOException $e) {
    $errors[] = "Database connection failed. Please try again later.";
    error_log("Database connection error: " . $e->getMessage());
    
    // For development only - remove this in production
    if (isset($_GET['debug'])) {
        $errors[] = "Debug info: " . $e->getMessage();
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $pdo !== null) {
    // Sanitize and validate input data
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $dob = $_POST['dob'];
    $national_id = trim($_POST['national_id']);
    $gender = $_POST['gender'];
    $role = $_POST['role'];
    $street = trim($_POST['street']);
    $city = trim($_POST['city']);
    $region = $_POST['region'];
    $postal_code = trim($_POST['postal_code']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }
    
    if (empty($phone) || !preg_match('/^[0-9+\-\s()]+$/', $phone)) {
        $errors[] = "Valid phone number is required";
    }
    
    if (empty($dob)) {
        $errors[] = "Date of birth is required";
    } else {
        // Age verification (must be 18+)
        $today = new DateTime();
        $birth_date = new DateTime($dob);
        $age = $today->diff($birth_date)->y;
        if ($age < 18) {
            $errors[] = "You must be 18 years or older to register";
        }
    }
    
    if (empty($national_id) || !preg_match('/^[0-9]{10,17}$/', $national_id)) {
        $errors[] = "Valid National ID is required (10-17 digits)";
    }
    
    if (empty($gender)) {
        $errors[] = "Gender is required";
    }
    
    if (empty($role)) {
        $errors[] = "Role is required";
    }
    
    if (empty($street)) {
        $errors[] = "Street address is required";
    }
    
    if (empty($city)) {
        $errors[] = "City is required";
    }
    
    if (empty($region)) {
        $errors[] = "Region is required";
    }
    
    if (empty($password) || strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    
    // Check if email or national_id already exists
    if (empty($errors)) {
        try {
            $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ? OR national_id = ?");
            $check_stmt->execute([$email, $national_id]);
            if ($check_stmt->fetchColumn() > 0) {
                $errors[] = "Email or National ID already exists";
            }
        } catch(PDOException $e) {
            $errors[] = "Database error occurred. Please try again.";
            error_log("Database check error: " . $e->getMessage());
        }
    }
    
    // If no errors, insert user into database
    if (empty($errors)) {
        try {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Prepare and execute insert statement
            $stmt = $pdo->prepare("
                INSERT INTO users (name, email, phone, dob, national_id, gender, role, street, city, region, postal_code, password, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $name, $email, $phone, $dob, $national_id, 
                $gender, $role, $street, $city, $region, 
                $postal_code, $hashed_password
            ]);
            
            $success_message = "Account created successfully! You can now login.";
            
            // Clear form data
            $_POST = [];
            
        } catch(PDOException $e) {
            $errors[] = "Registration failed. Please try again.";
            error_log("Registration error: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Truth Uncovered - Join the Movement</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #e74c3c;
            --primary-dark: #c0392b;
            --secondary-color: #3498db;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --dark-color: #2c3e50;
            --light-color: #ecf0f1;
            --text-color: #34495e;
            --shadow-light: 0 5px 15px rgba(0,0,0,0.1);
            --shadow-medium: 0 10px 30px rgba(0,0,0,0.15);
            --shadow-heavy: 0 20px 50px rgba(0,0,0,0.2);
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-accent: linear-gradient(135deg, #e74c3c, #c0392b);
            --border-radius: 15px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--gradient-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>') repeat;
            pointer-events: none;
            opacity: 0.3;
        }

        .floating-shapes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
            z-index: 1;
        }

        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 20s infinite linear;
        }

        .shape:nth-child(1) { width: 80px; height: 80px; left: 10%; animation-delay: 0s; }
        .shape:nth-child(2) { width: 60px; height: 60px; left: 20%; animation-delay: 5s; }
        .shape:nth-child(3) { width: 100px; height: 100px; left: 70%; animation-delay: 10s; }
        .shape:nth-child(4) { width: 40px; height: 40px; left: 80%; animation-delay: 15s; }

        @keyframes float {
            0% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateY(-100px) rotate(360deg); opacity: 0; }
        }

        .signup-container {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 25px;
            box-shadow: var(--shadow-heavy);
            padding: 50px;
            max-width: 700px;
            width: 100%;
            position: relative;
            overflow: hidden;
            z-index: 2;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .signup-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #e74c3c, #f39c12, #27ae60, #3498db, #9b59b6);
            border-radius: 25px 25px 0 0;
            animation: shimmer 3s ease-in-out infinite;
        }

        @keyframes shimmer {
            0%, 100% { transform: translateX(-100%); }
            50% { transform: translateX(100%); }
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
        }

        .logo-container {
            position: relative;
            display: inline-block;
            margin-bottom: 25px;
        }

        .logo {
            width: 100px;
            height: 100px;
            background: var(--gradient-accent);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 42px;
            font-weight: 900;
            box-shadow: var(--shadow-medium);
            position: relative;
            animation: logoFloat 3s ease-in-out infinite;
        }

        .logo::before {
            content: '';
            position: absolute;
            inset: -5px;
            background: conic-gradient(from 0deg, transparent, rgba(231, 76, 60, 0.4), transparent);
            border-radius: 50%;
            animation: rotate 4s linear infinite;
            z-index: -1;
        }

        @keyframes logoFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        h1 {
            color: var(--dark-color);
            font-size: 2.8em;
            margin-bottom: 15px;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -1px;
        }

        .subtitle {
            color: var(--text-color);
            font-size: 1.2em;
            margin-bottom: 35px;
            line-height: 1.6;
            font-weight: 400;
            opacity: 0.8;
        }

        .progress-bar {
            width: 100%;
            height: 6px;
            background: var(--light-color);
            border-radius: 3px;
            margin-bottom: 30px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: var(--gradient-accent);
            width: 0%;
            transition: width 0.5s ease;
            border-radius: 3px;
        }

        .alert {
            padding: 20px;
            border-radius: var(--border-radius);
            margin-bottom: 25px;
            border: 1px solid transparent;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .alert-error {
            background: linear-gradient(135deg, #fee, #fdd);
            border-color: #f5c6cb;
            color: #721c24;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4f5d4, #c8f2c8);
            border-color: #c3e6cb;
            color: #155724;
        }

        .alert-icon {
            font-size: 1.2em;
            margin-top: 2px;
        }

        .form-section {
            margin-bottom: 35px;
        }

        .section-title {
            color: var(--dark-color);
            font-size: 1.3em;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-icon {
            color: var(--primary-color);
            font-size: 1.1em;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 25px;
        }

        .form-group {
            position: relative;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            margin-bottom: 10px;
            color: var(--dark-color);
            font-weight: 600;
            font-size: 1em;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .label-icon {
            color: var(--secondary-color);
            font-size: 0.9em;
        }

        .required {
            color: var(--danger-color);
            font-size: 0.8em;
        }

        .input-container {
            position: relative;
        }

        input, select {
            width: 100%;
            padding: 18px 20px;
            border: 2px solid var(--light-color);
            border-radius: var(--border-radius);
            font-size: 1.05em;
            transition: var(--transition);
            background: white;
            color: var(--text-color);
            font-weight: 500;
        }

        input:focus, select:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 4px rgba(52, 152, 219, 0.15);
            transform: translateY(-2px);
        }

        input:valid {
            border-color: var(--success-color);
        }

        .input-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-color);
            opacity: 0.5;
        }

        .password-container {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-color);
            cursor: pointer;
            font-size: 1em;
            opacity: 0.7;
            transition: var(--transition);
        }

        .password-toggle:hover {
            opacity: 1;
            color: var(--primary-color);
        }

        .password-strength {
            margin-top: 10px;
            height: 4px;
            background-color: var(--light-color);
            border-radius: 2px;
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            transition: var(--transition);
            border-radius: 2px;
            width: 0%;
        }

        .strength-weak { background: var(--danger-color); width: 25%; }
        .strength-fair { background: var(--warning-color); width: 50%; }
        .strength-good { background: var(--secondary-color); width: 75%; }
        .strength-strong { background: var(--success-color); width: 100%; }

        .strength-text {
            font-size: 0.85em;
            margin-top: 5px;
            font-weight: 600;
        }

        .age-verification {
            background: linear-gradient(135deg, #fff8e1, #fff3c4);
            border-left: 5px solid var(--warning-color);
            padding: 20px;
            border-radius: var(--border-radius);
            margin: 25px 0;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: var(--shadow-light);
        }

        .warning-icon {
            color: var(--warning-color);
            font-size: 1.8em;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .signup-btn {
            width: 100%;
            padding: 20px;
            background: var(--gradient-accent);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1.2em;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 30px;
            position: relative;
            overflow: hidden;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .signup-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.6s ease;
        }

        .signup-btn:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-heavy);
        }

        .signup-btn:hover::before {
            left: 100%;
        }

        .signup-btn:active {
            transform: translateY(-1px);
        }

        .login-link {
            text-align: center;
            margin-top: 25px;
            color: var(--text-color);
            font-size: 1.05em;
        }

        .login-link a {
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: 700;
            transition: var(--transition);
        }

        .login-link a:hover {
            color: var(--primary-color);
            text-decoration: underline;
        }

        .security-badge {
            background: linear-gradient(135deg, #e8f5e8, #d4f5d4);
            border-left: 5px solid var(--success-color);
            padding: 20px;
            border-radius: var(--border-radius);
            margin-top: 25px;
            font-size: 0.95em;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: var(--shadow-light);
        }

        .security-icon {
            color: var(--success-color);
            font-size: 1.5em;
        }

        @media (max-width: 768px) {
            .signup-container {
                padding: 30px 25px;
                margin: 10px;
                border-radius: 20px;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            h1 {
                font-size: 2.2em;
            }
            
            .logo {
                width: 80px;
                height: 80px;
                font-size: 32px;
            }
        }

        @media (max-width: 480px) {
            .signup-container {
                padding: 25px 20px;
            }
            
            h1 {
                font-size: 1.9em;
            }
        }

        .loading {
            opacity: 0.7;
            pointer-events: none;
        }

        .loading .signup-btn {
            background: #bdc3c7;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <div class="signup-container">
        <div class="header">
            <div class="logo-container">
                <div class="logo">T</div>
            </div>
            <h1>Truth Uncovered</h1>
            <p class="subtitle">Join Bangladesh's premier digital civic platform to expose corruption, report injustice, and drive positive change in your community.</p>
        </div>

        <div class="progress-bar">
            <div class="progress-fill" id="progressFill"></div>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-triangle alert-icon"></i>
                <div>
                    <strong>Please fix the following errors:</strong>
                    <ul style="margin-top: 10px; margin-left: 20px;">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle alert-icon"></i>
                <div>
                    <strong>Success!</strong> <?php echo htmlspecialchars($success_message); ?>
                </div>
            </div>
        <?php endif; ?>

        <form method="POST" action="" id="signupForm">
            <div class="form-section">
                <div class="section-title">
                    <i class="fas fa-user section-icon"></i>
                    Personal Information
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">
                            <i class="fas fa-user label-icon"></i>
                            Full Name <span class="required">*</span>
                        </label>
                        <div class="input-container">
                            <input type="text" id="name" name="name" required 
                                   value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                                   placeholder="Enter your full name">
                            <i class="fas fa-user input-icon"></i>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">
                            <i class="fas fa-envelope label-icon"></i>
                            Email Address <span class="required">*</span>
                        </label>
                        <div class="input-container">
                            <input type="email" id="email" name="email" required
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                   placeholder="your.email@example.com">
                            <i class="fas fa-envelope input-icon"></i>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">
                            <i class="fas fa-phone label-icon"></i>
                            Phone Number <span class="required">*</span>
                        </label>
                        <div class="input-container">
                            <input type="tel" id="phone" name="phone" required
                                   value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                                   placeholder="+880 XXXX XXXXXX">
                            <i class="fas fa-phone input-icon"></i>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="dob">
                            <i class="fas fa-calendar label-icon"></i>
                            Date of Birth <span class="required">*</span>
                        </label>
                        <div class="input-container">
                            <input type="date" id="dob" name="dob" required
                                   value="<?php echo htmlspecialchars($_POST['dob'] ?? ''); ?>">
                            <i class="fas fa-calendar input-icon"></i>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="national_id">
                            <i class="fas fa-id-card label-icon"></i>
                            National ID <span class="required">*</span>
                        </label>
                        <div class="input-container">
                            <input type="text" id="national_id" name="national_id" required
                                   pattern="[0-9]{10,17}" title="National ID should be 10-17 digits"
                                   value="<?php echo htmlspecialchars($_POST['national_id'] ?? ''); ?>"
                                   placeholder="10-17 digit National ID">
                            <i class="fas fa-id-card input-icon"></i>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="gender">
                            <i class="fas fa-venus-mars label-icon"></i>
                            Gender <span class="required">*</span>
                        </label>
                        <select id="gender" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="Male" <?php echo (($_POST['gender'] ?? '') == 'Male') ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo (($_POST['gender'] ?? '') == 'Female') ? 'selected' : ''; ?>>Female</option>
                            <option value="Other" <?php echo (($_POST['gender'] ?? '') == 'Other') ? 'selected' : ''; ?>>Other</option>
                            <option value="Prefer not to say" <?php echo (($_POST['gender'] ?? '') == 'Prefer not to say') ? 'selected' : ''; ?>>Prefer not to say</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="role">
                            <i class="fas fa-user-tag label-icon"></i>
                            Role <span class="required">*</span>
                        </label>
                        <select id="role" name="role" required>
                            <option value="">Select Role</option>
                            <option value="Citizen" <?php echo (($_POST['role'] ?? '') == 'Citizen') ? 'selected' : ''; ?>>Citizen</option>
                            <option value="NGO_Partner" <?php echo (($_POST['role'] ?? '') == 'NGO_Partner') ? 'selected' : ''; ?>>NGO Partner</option>
                            <option value="Govt_Officer" <?php echo (($_POST['role'] ?? '') == 'Govt_Officer') ? 'selected' : ''; ?>>Government Officer</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="section-title">
                    <i class="fas fa-map-marker-alt section-icon"></i>
                    Address Information
                </div>
                
                <div class="form-group full-width">
                    <label for="street">
                        <i class="fas fa-road label-icon"></i>
                        Street Address <span class="required">*</span>
                    </label>
                    <div class="input-container">
                        <input type="text" id="street" name="street" required
                               value="<?php echo htmlspecialchars($_POST['street'] ?? ''); ?>"
                               placeholder="House/Building, Road, Area">
                        <i class="fas fa-road input-icon"></i>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="city">
                            <i class="fas fa-city label-icon"></i>
                            City <span class="required">*</span>
                        </label>
                        <div class="input-container">
                            <input type="text" id="city" name="city" required
                                   value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>"
                                   placeholder="Enter your city">
                            <i class="fas fa-city input-icon"></i>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="region">
                            <i class="fas fa-globe-asia label-icon"></i>
                            Region/Division <span class="required">*</span>
                        </label>
                        <select id="region" name="region" required>
                            <option value="">Select Division</option>
                            <option value="Dhaka" <?php echo (($_POST['region'] ?? '') == 'Dhaka') ? 'selected' : ''; ?>>Dhaka</option>
                            <option value="Chittagong" <?php echo (($_POST['region'] ?? '') == 'Chittagong') ? 'selected' : ''; ?>>Chittagong</option>
                            <option value="Rajshahi" <?php echo (($_POST['region'] ?? '') == 'Rajshahi') ? 'selected' : ''; ?>>Rajshahi</option>
                            <option value="Sylhet" <?php echo (($_POST['region'] ?? '') == 'Sylhet') ? 'selected' : ''; ?>>Sylhet</option>
                            <option value="Barisal" <?php echo (($_POST['region'] ?? '') == 'Barisal') ? 'selected' : ''; ?>>Barisal</option>
                            <option value="Khulna" <?php echo (($_POST['region'] ?? '') == 'Khulna') ? 'selected' : ''; ?>>Khulna</option>
                            <option value="Rangpur" <?php echo (($_POST['region'] ?? '') == 'Rangpur') ? 'selected' : ''; ?>>Rangpur</option>
                            <option value="Mymensingh" <?php echo (($_POST['region'] ?? '') == 'Mymensingh') ? 'selected' : ''; ?>>Mymensingh</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="postal_code">
                            <i class="fas fa-mail-bulk label-icon"></i>
                            Postal Code
                        </label>
                        <div class="input-container">
                            <input type="text" id="postal_code" name="postal_code"
                                   value="<?php echo htmlspecialchars($_POST['postal_code'] ?? ''); ?>"
                                   placeholder="Optional postal code">
                            <i class="fas fa-mail-bulk input-icon"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="section-title">
                    <i class="fas fa-lock section-icon"></i>
                    Security Settings
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="password">
                            <i class="fas fa-key label-icon"></i>
                            Password <span class="required">*</span>
                        </label>
                        <div class="password-container">
                            <input type="password" id="password" name="password" required minlength="8"
                                   placeholder="Minimum 8 characters">
                            <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="password-strength">
                            <div class="password-strength-bar" id="strengthBar"></div>
                        </div>
                        <div class="strength-text" id="strengthText"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">
                            <i class="fas fa-check-double label-icon"></i>
                            Confirm Password <span class="required">*</span>
                        </label>
                        <div class="password-container">
                            <input type="password" id="confirm_password" name="confirm_password" required
                                   placeholder="Re-enter your password">
                            <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="age-verification">
                <div class="warning-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div>
                    <strong>Age Verification Required:</strong> You must be 18 years or older to register for Truth Uncovered. This platform handles sensitive civic and legal matters that require mature judgment and legal responsibility.
                </div>
            </div>

            <button type="submit" class="signup-btn" id="submitBtn">
                <i class="fas fa-user-plus"></i>
                Create Account & Join the Movement
            </button>

            <div class="security-badge">
                <i class="fas fa-shield-alt security-icon"></i>
                <div>
                    <strong>Your security is our priority:</strong> All data is encrypted end-to-end. Your identity can be kept anonymous when reporting. We follow strict security protocols to protect whistleblowers and citizen reporters.
                </div>
            </div>

            <div class="login-link">
                Already have an account? <a href="login.php">Sign in here</a>
            </div>
        </form>
    </div>

    <script>
        // Form progress tracking
        function updateProgress() {
            const form = document.getElementById('signupForm');
            const inputs = form.querySelectorAll('input[required], select[required]');
            let filledInputs = 0;
            
            inputs.forEach(input => {
                if (input.value.trim() !== '') {
                    filledInputs++;
                }
            });
            
            const progress = (filledInputs / inputs.length) * 100;
            document.getElementById('progressFill').style.width = progress + '%';
        }

        // Password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('strengthBar');
            const strengthText = document.getElementById('strengthText');
            const strength = calculatePasswordStrength(password);
            
            strengthBar.className = 'password-strength-bar';
            
            if (strength >= 4) {
                strengthBar.classList.add('strength-strong');
                strengthText.textContent = 'Strong password';
                strengthText.style.color = 'var(--success-color)';
            } else if (strength >= 3) {
                strengthBar.classList.add('strength-good');
                strengthText.textContent = 'Good password';
                strengthText.style.color = 'var(--secondary-color)';
            } else if (strength >= 2) {
                strengthBar.classList.add('strength-fair');
                strengthText.textContent = 'Fair password';
                strengthText.style.color = 'var(--warning-color)';
            } else if (strength >= 1) {
                strengthBar.classList.add('strength-weak');
                strengthText.textContent = 'Weak password';
                strengthText.style.color = 'var(--danger-color)';
            } else {
                strengthText.textContent = '';
            }
            
            updateProgress();
        });

        function calculatePasswordStrength(password) {
            let score = 0;
            if (password.length >= 8) score++;
            if (/[A-Z]/.test(password)) score++;
            if (/[a-z]/.test(password)) score++;
            if (/\d/.test(password)) score++;
            if (/[^A-Za-z0-9]/.test(password)) score++;
            return score;
        }

        // Password confirmation validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (confirmPassword && password !== confirmPassword) {
                this.style.borderColor = 'var(--danger-color)';
                this.style.boxShadow = '0 0 0 4px rgba(231, 76, 60, 0.15)';
            } else if (confirmPassword && password === confirmPassword) {
                this.style.borderColor = 'var(--success-color)';
                this.style.boxShadow = '0 0 0 4px rgba(39, 174, 96, 0.15)';
            } else {
                this.style.borderColor = 'var(--light-color)';
                this.style.boxShadow = 'none';
            }
            
            updateProgress();
        });

        // Password toggle functionality
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const toggle = field.nextElementSibling.querySelector('i');
            
            if (field.type === 'password') {
                field.type = 'text';
                toggle.classList.remove('fa-eye');
                toggle.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                toggle.classList.remove('fa-eye-slash');
                toggle.classList.add('fa-eye');
            }
        }

        // Real-time validation
        document.querySelectorAll('input[required], select[required]').forEach(input => {
            input.addEventListener('input', updateProgress);
            input.addEventListener('change', updateProgress);
        });

        // Form submission with loading state
        document.getElementById('signupForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            const container = document.querySelector('.signup-container');
            
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Account...';
            container.classList.add('loading');
        });

        // Initialize progress on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateProgress();
        });
    </script>
</body>
</html>
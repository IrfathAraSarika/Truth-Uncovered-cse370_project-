<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - TrustHub</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.6;
            color: #1a1a1a;
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 25%, #16213e 50%, #0f3460 75%, #1e3a8a 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Animated Background */
        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .floating-orb {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(45deg, rgba(59, 130, 246, 0.1), rgba(147, 51, 234, 0.1));
            animation: float 6s ease-in-out infinite;
        }

        .orb1 { width: 300px; height: 300px; top: 10%; left: 80%; animation-delay: 0s; }
        .orb2 { width: 200px; height: 200px; top: 70%; left: 10%; animation-delay: 2s; }
        .orb3 { width: 150px; height: 150px; top: 30%; left: 20%; animation-delay: 4s; }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(30px, -30px) rotate(120deg); }
            66% { transform: translate(-20px, 20px) rotate(240deg); }
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header Styles */
        header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .back-button {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ffffff;
            padding: 0.6rem 1.2rem;
            border-radius: 12px;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .back-button:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }

        /* Profile Header */
        .profile-header {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(25px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 2.5rem;
            margin: 2rem 0;
            text-align: center;
            position: relative;
            overflow: hidden;
            animation: slideInUp 0.8s ease-out;
        }

        .profile-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .profile-content {
            position: relative;
            z-index: 2;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
            margin: 0 auto 1.5rem;
            border: 4px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .profile-name {
            font-size: 2.5rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 0.5rem;
        }

        .profile-role {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(139, 92, 246, 0.2));
            backdrop-filter: blur(10px);
            border: 1px solid rgba(59, 130, 246, 0.3);
            color: #3b82f6;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 1rem;
        }

        .profile-id {
            color: #94a3b8;
            font-size: 0.9rem;
        }

        /* Profile Grid */
        .profile-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin: 2rem 0;
        }

        .profile-section {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 20px;
            padding: 2rem;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .profile-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
        }

        .profile-section:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.12);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
        }

        .section-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .section-icon {
            font-size: 1.5rem;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
        }

        /* Field Styles */
        .field-group {
            margin-bottom: 1.5rem;
        }

        .field-label {
            display: block;
            font-weight: 600;
            color: #cbd5e1;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .field-value {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            padding: 1rem;
            color: #ffffff;
            font-weight: 500;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .field-value:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .field-value.editable {
            cursor: pointer;
        }

        .field-value.editable:hover {
            border-color: rgba(59, 130, 246, 0.5);
        }

        /* Toggle Switches for Subscriptions */
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 30px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: 0.4s;
            border-radius: 30px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 4px;
            bottom: 3px;
            background: white;
            transition: 0.4s;
            border-radius: 50%;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        input:checked + .toggle-slider {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        input:checked + .toggle-slider:before {
            transform: translateX(30px);
        }

        .subscription-field {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            margin-bottom: 1rem;
        }

        .subscription-label {
            color: #ffffff;
            font-weight: 500;
        }

        /* Action Buttons */
        .profile-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin: 2rem 0;
        }

        .action-button {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ffffff;
            padding: 1rem 2rem;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .action-button:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .primary-button {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            border: none;
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
        }

        .primary-button:hover {
            background: linear-gradient(135deg, #2563eb, #7c3aed);
            box-shadow: 0 12px 35px rgba(59, 130, 246, 0.4);
        }

        .danger-button {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            border: none;
            box-shadow: 0 8px 25px rgba(239, 68, 68, 0.3);
        }

        .danger-button:hover {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            box-shadow: 0 12px 35px rgba(239, 68, 68, 0.4);
        }

        /* Security Section */
        .security-banner {
            background: rgba(16, 185, 129, 0.15);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #ffffff;
            padding: 1.5rem;
            border-radius: 16px;
            text-align: center;
            margin: 2rem 0;
            position: relative;
            overflow: hidden;
        }

        .security-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, #10b981, #059669, #10b981);
            animation: shimmer 2s ease-in-out infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .security-icon {
            font-size: 1.8rem;
            margin-right: 0.8rem;
        }

        /* Animations */
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-delay {
            animation: fadeInDelay 1s ease-out;
        }

        @keyframes fadeInDelay {
            0% { opacity: 0; transform: translateY(20px); }
            60% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(25px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 2rem;
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            position: relative;
        }

        .close-button {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #6b7280;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .close-button:hover {
            background: rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #1f2937;
        }

        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .profile-grid {
                grid-template-columns: 1fr;
            }
            
            .profile-actions {
                flex-direction: column;
            }
            
            .nav-actions {
                flex-direction: column;
                gap: 0.5rem;
            }
        }

        /* Status Indicators */
        .status-indicator {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            backdrop-filter: blur(10px);
        }

        .status-verified {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }

        .status-pending {
            background: rgba(251, 191, 36, 0.2);
            color: #fbbf24;
            border: 1px solid rgba(251, 191, 36, 0.3);
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="bg-animation">
        <div class="floating-orb orb1"></div>
        <div class="floating-orb orb2"></div>
        <div class="floating-orb orb3"></div>
    </div>

    <header>
        <div class="container">
            <nav>
                <div class="logo">TrustHub</div>
                <div class="nav-actions">
                    <a href="index.php" class="back-button">← Back to Home Page</a>
                </div>
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
            <!-- Profile Header -->
            <div class="profile-header">
                <div class="profile-content">
                    <div class="profile-avatar">👤</div>
                    <h1 class="profile-name" id="userName">John Doe</h1>
                    <div class="profile-role" id="userRole">Citizen</div>
                    <div class="profile-id">User ID: <span id="userId">#001</span></div>
                </div>
            </div>

            <!-- Profile Information Grid -->
            <div class="profile-grid">
                <!-- Personal Information -->
                <div class="profile-section">
                    <h2 class="section-title">
                        <span class="section-icon">👨‍💼</span>
                        Personal Information
                    </h2>
                    
                    <div class="field-group">
                        <label class="field-label">Full Name</label>
                        <div class="field-value editable" data-field="name">John Doe</div>
                    </div>
                    
                    <div class="field-group">
                        <label class="field-label">Email Address</label>
                        <div class="field-value editable" data-field="email">john.doe@email.com</div>
                    </div>
                    
                    <div class="field-group">
                        <label class="field-label">Phone Number</label>
                        <div class="field-value editable" data-field="phone">+1 (555) 123-4567</div>
                    </div>
                    
                    <div class="field-group">
                        <label class="field-label">Date of Birth</label>
                        <div class="field-value editable" data-field="dob">January 15, 1990</div>
                    </div>
                    
                    <div class="field-group">
                        <label class="field-label">National ID</label>
                        <div class="field-value editable" data-field="national_id">ID-123456789</div>
                    </div>
                    
                    <div class="field-group">
                        <label class="field-label">Gender</label>
                        <div class="field-value editable" data-field="gender">Male</div>
                    </div>
                </div>

                <!-- Address Information -->
                <div class="profile-section">
                    <h2 class="section-title">
                        <span class="section-icon">🏠</span>
                        Address Information
                    </h2>
                    
                    <div class="field-group">
                        <label class="field-label">Street Address</label>
                        <div class="field-value editable" data-field="street">123 Main Street, Apt 4B</div>
                    </div>
                    
                    <div class="field-group">
                        <label class="field-label">City</label>
                        <div class="field-value editable" data-field="city">Dhaka</div>
                    </div>
                    
                    <div class="field-group">
                        <label class="field-label">Region/Division</label>
                        <div class="field-value editable" data-field="region">Dhaka Division</div>
                    </div>
                    
                    <div class="field-group">
                        <label class="field-label">Postal Code</label>
                        <div class="field-value editable" data-field="postal_code">1000</div>
                    </div>
                </div>

                <!-- Account Settings -->
                <div class="profile-section">
                    <h2 class="section-title">
                        <span class="section-icon">⚙️</span>
                        Account Settings
                    </h2>
                    
                    <div class="field-group">
                        <label class="field-label">Account Status</label>
                        <div class="status-indicator status-verified">
                            ✓ Verified Account
                        </div>
                    </div>
                    
                    <div class="field-group">
                        <label class="field-label">User Role</label>
                        <div class="field-value" id="roleDisplay">Citizen</div>
                    </div>
                    
                    <div class="field-group">
                        <label class="field-label">Password</label>
                        <div class="field-value">••••••••••••</div>
                        <button class="action-button" style="margin-top: 0.5rem;" onclick="changePassword()">
                            🔑 Change Password
                        </button>
                    </div>
                </div>

                <!-- Notification Preferences -->
                <div class="profile-section">
                    <h2 class="section-title">
                        <span class="section-icon">🔔</span>
                        Notification Preferences
                    </h2>
                    
                    <div class="subscription-field">
                        <span class="subscription-label">SMS Notifications</span>
                        <label class="toggle-switch">
                            <input type="checkbox" id="smsToggle" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    
                    <div class="subscription-field">
                        <span class="subscription-label">Email Notifications</span>
                        <label class="toggle-switch">
                            <input type="checkbox" id="emailToggle" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    
                    <div class="subscription-field">
                        <span class="subscription-label">Blog Following Updates</span>
                        <label class="toggle-switch">
                            <input type="checkbox" id="blogToggle">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Security Banner -->
            <div class="security-banner">
                <span class="security-icon">🔒</span>
                Your personal information is encrypted and secure. We follow strict data protection protocols.
            </div>

            <!-- Action Buttons -->
            <div class="profile-actions">
                <button class="action-button primary-button" onclick="editProfile()">
                    ✏️ Edit Profile
                </button>
                <button class="action-button" onclick="downloadData()">
                    📥 Download My Data
                </button>
                <button class="action-button danger-button" onclick="deleteAccount()">
                    🗑️ Delete Account
                </button>
            </div>
        </div>
    </main>

    <!-- Edit Profile Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <button class="close-button" onclick="closeModal()">×</button>
            <h3 style="margin-bottom: 1.5rem; color: #1f2937; font-weight: 700;">Edit Profile Information</h3>
            
            <div class="form-group">
                <label class="form-label">Full Name</label>
                <input type="text" class="form-input" id="editName" value="John Doe">
            </div>
            
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" class="form-input" id="editEmail" value="john.doe@email.com">
            </div>
            
            <div class="form-group">
                <label class="form-label">Phone Number</label>
                <input type="tel" class="form-input" id="editPhone" value="+1 (555) 123-4567">
            </div>
            
            <div class="form-group">
                <label class="form-label">Date of Birth</label>
                <input type="date" class="form-input" id="editDob" value="1990-01-15">
            </div>
            
            <div class="form-group">
                <label class="form-label">Gender</label>
                <select class="form-select" id="editGender">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                    <option value="Prefer not to say">Prefer not to say</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Street Address</label>
                <textarea class="form-textarea" id="editStreet" rows="3">123 Main Street, Apt 4B</textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label">City</label>
                <input type="text" class="form-input" id="editCity" value="Dhaka">
            </div>
            
            <div class="form-group">
                <label class="form-label">Region/Division</label>
                <input type="text" class="form-input" id="editRegion" value="Dhaka Division">
            </div>
            
            <div class="form-group">
                <label class="form-label">Postal Code</label>
                <input type="text" class="form-input" id="editPostalCode" value="1000">
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button class="primary-button" onclick="saveProfile()" style="flex: 1;">Save Changes</button>
                <button class="action-button" onclick="closeModal()" style="flex: 1;">Cancel</button>
            </div>
        </div>
    </div>

    <!-- Password Change Modal -->
    <div id="passwordModal" class="modal">
        <div class="modal-content">
            <button class="close-button" onclick="closePasswordModal()">×</button>
            <h3 style="margin-bottom: 1.5rem; color: #1f2937; font-weight: 700;">Change Password</h3>
            
            <div class="form-group">
                <label class="form-label">Current Password</label>
                <input type="password" class="form-input" id="currentPassword">
            </div>
            
            <div class="form-group">
                <label class="form-label">New Password</label>
                <input type="password" class="form-input" id="newPassword">
            </div>
            
            <div class="form-group">
                <label class="form-label">Confirm New Password</label>
                <input type="password" class="form-input" id="confirmPassword">
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button class="primary-button" onclick="updatePassword()" style="flex: 1;">Update Password</button>
                <button class="action-button" onclick="closePasswordModal()" style="flex: 1;">Cancel</button>
            </div>
        </div>
    </div>

    <script>
        // Profile data object to store user information
        const profileData = {
            id: 1,
            name: "John Doe",
            email: "john.doe@email.com",
            phone: "+1 (555) 123-4567",
            dob: "1990-01-15",
            national_id: "ID-123456789",
            gender: "Male",
            role: "Citizen",
            street: "123 Main Street, Apt 4B",
            city: "Dhaka",
            region: "Dhaka Division",
            postal_code: "1000",
            sub_sms: true,
            sub_email: true,
            sub_blog_following: false
        };

        // Initialize page with profile data
        function initializePage() {
            document.getElementById('userName').textContent = profileData.name;
            document.getElementById('userRole').textContent = profileData.role;
            document.getElementById('userId').textContent = `#${profileData.id.toString().padStart(3, '0')}`;
            document.getElementById('roleDisplay').textContent = profileData.role;
            
            // Set toggle states
            document.getElementById('smsToggle').checked = profileData.sub_sms;
            document.getElementById('emailToggle').checked = profileData.sub_email;
            document.getElementById('blogToggle').checked = profileData.sub_blog_following;
            
            updateFieldValues();
        }

        // Update field values in the display
        function updateFieldValues() {
            const fields = document.querySelectorAll('.field-value[data-field]');
            fields.forEach(field => {
                const fieldName = field.getAttribute('data-field');
                if (profileData[fieldName]) {
                    if (fieldName === 'dob') {
                        const date = new Date(profileData[fieldName]);
                        field.textContent = date.toLocaleDateString('en-US', { 
                            year: 'numeric', 
                            month: 'long', 
                            day: 'numeric' 
                        });
                    } else {
                        field.textContent = profileData[fieldName];
                    }
                }
            });
        }

        // Edit Profile Modal Functions
        function editProfile() {
            // Populate form fields with current data
            document.getElementById('editName').value = profileData.name;
            document.getElementById('editEmail').value = profileData.email;
            document.getElementById('editPhone').value = profileData.phone;
            document.getElementById('editDob').value = profileData.dob;
            document.getElementById('editGender').value = profileData.gender;
            document.getElementById('editStreet').value = profileData.street;
            document.getElementById('editCity').value = profileData.city;
            document.getElementById('editRegion').value = profileData.region;
            document.getElementById('editPostalCode').value = profileData.postal_code;
            
            document.getElementById('editModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        function saveProfile() {
            // Update profile data with form values
            profileData.name = document.getElementById('editName').value;
            profileData.email = document.getElementById('editEmail').value;
            profileData.phone = document.getElementById('editPhone').value;
            profileData.dob = document.getElementById('editDob').value;
            profileData.gender = document.getElementById('editGender').value;
            profileData.street = document.getElementById('editStreet').value;
            profileData.city = document.getElementById('editCity').value;
            profileData.region = document.getElementById('editRegion').value;
            profileData.postal_code = document.getElementById('editPostalCode').value;
            
            // Update display
            updateFieldValues();
            document.getElementById('userName').textContent = profileData.name;
            
            // Show success message
            showNotification('Profile updated successfully!', 'success');
            closeModal();
        }

        // Password Change Functions
        function changePassword() {
            document.getElementById('passwordModal').style.display = 'flex';
        }

        function closePasswordModal() {
            document.getElementById('passwordModal').style.display = 'none';
            // Clear password fields
            document.getElementById('currentPassword').value = '';
            document.getElementById('newPassword').value = '';
            document.getElementById('confirmPassword').value = '';
        }

        function updatePassword() {
            const currentPassword = document.getElementById('currentPassword').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (!currentPassword || !newPassword || !confirmPassword) {
                showNotification('Please fill in all password fields', 'error');
                return;
            }
            
            if (newPassword !== confirmPassword) {
                showNotification('New passwords do not match', 'error');
                return;
            }
            
            if (newPassword.length < 8) {
                showNotification('Password must be at least 8 characters long', 'error');
                return;
            }
            
            // Simulate password update
            showNotification('Password updated successfully!', 'success');
            closePasswordModal();
        }

        // Notification Settings
        document.addEventListener('DOMContentLoaded', function() {
            // Handle toggle switches
            document.getElementById('smsToggle').addEventListener('change', function() {
                profileData.sub_sms = this.checked;
                showNotification(`SMS notifications ${this.checked ? 'enabled' : 'disabled'}`, 'info');
            });
            
            document.getElementById('emailToggle').addEventListener('change', function() {
                profileData.sub_email = this.checked;
                showNotification(`Email notifications ${this.checked ? 'enabled' : 'disabled'}`, 'info');
            });
            
            document.getElementById('blogToggle').addEventListener('change', function() {
                profileData.sub_blog_following = this.checked;
                showNotification(`Blog following updates ${this.checked ? 'enabled' : 'disabled'}`, 'info');
            });
        });

        // Other Action Functions
        function downloadData() {
            // Create downloadable JSON file with user data
            const dataStr = JSON.stringify(profileData, null, 2);
            const dataBlob = new Blob([dataStr], {type: 'application/json'});
            const url = URL.createObjectURL(dataBlob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `${profileData.name.replace(' ', '_')}_profile_data.json`;
            link.click();
            URL.revokeObjectURL(url);
            showNotification('Profile data downloaded successfully!', 'success');
        }

        function deleteAccount() {
            if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
                if (confirm('This will permanently delete all your data. Are you absolutely sure?')) {
                    showNotification('Account deletion initiated. You will receive a confirmation email.', 'warning');
                    // In a real app, this would redirect to a deletion confirmation page
                }
            }
        }

        // Notification System
        function showNotification(message, type = 'info') {
            // Remove existing notifications
            const existingNotif = document.querySelector('.notification');
            if (existingNotif) {
                existingNotif.remove();
            }
            
            const notification = document.createElement('div');
            notification.className = 'notification';
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 1rem 1.5rem;
                border-radius: 12px;
                color: white;
                font-weight: 600;
                z-index: 3000;
                backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.2);
                animation: slideInRight 0.3s ease-out;
                max-width: 400px;
            `;
            
            switch(type) {
                case 'success':
                    notification.style.background = 'rgba(34, 197, 94, 0.9)';
                    break;
                case 'error':
                    notification.style.background = 'rgba(239, 68, 68, 0.9)';
                    break;
                case 'warning':
                    notification.style.background = 'rgba(251, 191, 36, 0.9)';
                    break;
                default:
                    notification.style.background = 'rgba(59, 130, 246, 0.9)';
            }
            
            notification.textContent = message;
            document.body.appendChild(notification);
            
            // Auto remove after 4 seconds
            setTimeout(() => {
                notification.style.animation = 'slideOutRight 0.3s ease-in';
                setTimeout(() => notification.remove(), 300);
            }, 4000);
        }

        // Close modals when clicking outside
        window.addEventListener('click', function(event) {
            const editModal = document.getElementById('editModal');
            const passwordModal = document.getElementById('passwordModal');
            
            if (event.target === editModal) {
                closeModal();
            }
            if (event.target === passwordModal) {
                closePasswordModal();
            }
        });

        // Initialize page on load
        document.addEventListener('DOMContentLoaded', initializePage);

        // Add notification animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            
            @keyframes slideOutRight {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(100%);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
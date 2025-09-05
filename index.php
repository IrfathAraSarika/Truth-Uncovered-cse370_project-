<?php
session_start();
include 'DBconnect.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch all users (or just the logged-in user)
$sql = "SELECT User_ID, Name, Email, Password, Role FROM Users";
$result = $conn->query($sql);
$users = $result->fetch_all(MYSQLI_ASSOC);

// Get logged-in user's role
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    foreach ($users as $user) {
        if ($user['User_ID'] == $user_id) {
            $_SESSION['role'] = $user['Role']; 
            break;
        }
    }
}

// Check if logout link was clicked
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php"); // redirect after logout
    exit();
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Truth Uncovered - Digital Civic Platform</title>
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
            max-width: 1400px;
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
            cursor:pointer;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .notification-badge {
            position: relative;
            background: rgba(59, 130, 246, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(59, 130, 246, 0.3);
            color: #3b82f6;
            border-radius: 12px;
            width: 44px;
            height: 44px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .notification-badge:hover {
            background: rgba(59, 130, 246, 0.3);
            transform: scale(1.05);
        }

        .badge-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .user-info {
            text-align: right;
            color: #e2e8f0;
            cursor:pointer;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.95rem;
        }

        .user-role {
            font-size: 0.8rem;
            color: #94a3b8;
        }

        /* Main Content */
        main {
            padding: 2rem 0;
        }

        /* About Section - First Thing Users See */
        .about-section {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(25px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 3rem;
            margin-bottom: 3rem;
            text-align: center;
            position: relative;
            overflow: hidden;
            animation: slideInUp 0.8s ease-out;
        }

        .about-section::before {
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

        .about-content {
            position: relative;
            z-index: 2;
        }

        .about-title {
            font-size: 3.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #ffffff, #e2e8f0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1.5rem;
            letter-spacing: -0.02em;
        }

        .about-subtitle {
            font-size: 1.4rem;
            color: #cbd5e1;
            margin-bottom: 2rem;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
            font-weight: 300;
        }

        .about-description {
            font-size: 1.1rem;
            color: #e2e8f0;
            max-width: 900px;
            margin: 0 auto 2.5rem;
            line-height: 1.8;
        }

        .platform-features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }

        .feature-item {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 16px;
            padding: 1.5rem;
            transition: all 0.4s ease;
            cursor: pointer;
        }

        .feature-item:hover {
            transform: translateY(-8px);
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            display: block;
        }

        .feature-title {
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 0.5rem;
        }

        .feature-desc {
            color: #cbd5e1;
            font-size: 0.9rem;
        }

        .explore-button {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            color: white;
            border: none;
            padding: 1rem 3rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.4s ease;
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
            margin-top: 1rem;
        }

        .explore-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(59, 130, 246, 0.4);
            background: linear-gradient(135deg, #2563eb, #7c3aed);
        }
        .button-width{
            text-align:center;
            width: 50%;
        }

        /* Glass Cards Section */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin: 3rem 0;
        }

        .glass-widget {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 20px;
            padding: 2rem;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .glass-widget::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
        }

        .glass-widget:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.12);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
        }

        .widget-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        /* Categories Grid */
        .categories-section {
            margin: 4rem 0;
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
        }

        .category-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 18px;
            padding: 2rem 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .category-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.6s ease;
        }

        .category-card:hover::before {
            left: 100%;
        }

        .category-card:hover {
            transform: translateY(-8px) scale(1.02);
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .category-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            display: block;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
        }

        .category-title {
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }

        .category-count {
            color: #94a3b8;
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* Recent Reports Widget */
        .report-item {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1.2rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .report-item:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateX(8px);
        }

        .report-title {
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 0.5rem;
        }

        .report-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9rem;
            color: #cbd5e1;
        }

        .status-badge {
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            backdrop-filter: blur(10px);
        }

        .status-pending { 
            background: rgba(251, 191, 36, 0.2); 
            color: #fbbf24; 
            border: 1px solid rgba(251, 191, 36, 0.3);
        }
        .status-verified { 
            background: rgba(34, 197, 94, 0.2); 
            color: #22c55e; 
            border: 1px solid rgba(34, 197, 94, 0.3);
        }
        .status-action-taken { 
            background: rgba(59, 130, 246, 0.2); 
            color: #3b82f6; 
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        /* Analytics Widget */
        .analytics-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-top: 1rem;
        }

        .metric-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .metric-card:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: scale(1.05);
        }

        .metric-value {
            font-size: 2.2rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 0.5rem;
        }

        .metric-label {
            font-size: 0.9rem;
            color: #94a3b8;
            font-weight: 500;
        }

        /* Institution Rankings */
        .ranking-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            margin-bottom: 0.8rem;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .ranking-item:hover {
            background: rgba(255, 255, 255, 0.12);
            transform: translateX(8px);
        }

        .trust-score {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }

        /* Impact Stories Carousel */
        .story-carousel {
            display: flex;
            gap: 1.5rem;
            overflow-x: auto;
            padding: 1rem 0;
            scroll-behavior: smooth;
        }

        .story-card {
            min-width: 320px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            padding: 2rem;
            transition: all 0.4s ease;
            cursor: pointer;
        }

        .story-card:hover {
            transform: translateY(-10px) scale(1.02);
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
        }

        .story-title {
            font-weight: 700;
            margin-bottom: 1rem;
            color: #ffffff;
            font-size: 1.1rem;
        }

        .story-outcome {
            color: #cbd5e1;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        .story-date {
            color: #94a3b8;
            font-size: 0.8rem;
            font-weight: 500;
        }

        /* Security Banner */
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
            cursor:pointer;
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

        /* Action Buttons */
        .action-button {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ffffff;
            padding: 0.8rem 1.5rem;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
            width: 100%;
            margin-top: 1rem;
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

        /* Evidence Upload */
        .upload-zone {
            border: 2px dashed rgba(59, 130, 246, 0.4);
            border-radius: 16px;
            padding: 2.5rem;
            text-align: center;
            background: rgba(59, 130, 246, 0.05);
            backdrop-filter: blur(10px);
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .upload-zone:hover {
            background: rgba(59, 130, 246, 0.1);
            border-color: rgba(59, 130, 246, 0.6);
            transform: scale(1.02);
        }

        .upload-icon {
            font-size: 3rem;
            color: #3b82f6;
            margin-bottom: 1rem;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.3));
        }

        .upload-text {
            color: #e2e8f0;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .upload-subtext {
            color: #94a3b8;
            font-size: 0.9rem;
        }

        /* Blog Grid */
        .blog-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .blog-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 16px;
            padding: 1.5rem;
            transition: all 0.4s ease;
            cursor: pointer;
        }

        .blog-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.12);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .blog-title {
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 0.8rem;
            line-height: 1.4;
        }

        .blog-meta {
            color: #94a3b8;
            font-size: 0.9rem;
        }

        /* Case Timeline */
        .timeline {
            position: relative;
            padding-left: 2rem;
            margin-top: 1rem;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(180deg, #3b82f6, #8b5cf6);
        }

        .timeline-item {
            position: relative;
            margin-bottom: 2rem;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -1.5rem;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #3b82f6;
            border: 3px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }

        .timeline-title {
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 0.3rem;
        }

        .timeline-date {
            color: #94a3b8;
            font-size: 0.9rem;
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

        /* Responsive Design */
        @media (max-width: 768px) {
            .about-title {
                font-size: 2.5rem;
            }
            
            .about-subtitle {
                font-size: 1.2rem;
            }
            
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            
            .categories-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .platform-features {
                grid-template-columns: 1fr;
            }
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
        .logout-btn {
    margin-left: 15px;
    font-size: 22px;
    color: #3b82f6;
    transition: transform 0.2s, color 0.2s;
}

.logout-btn:hover {
    color: #e63946; /* red on hover */
    transform: scale(1.15);
}
.button-container {
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:10px;
}

   .toast {
    position: fixed;
    top: 20px;
    right: 20px;
    background-color: #22c55e; /* green for success */
    color: #fff;
    padding: 15px 25px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    opacity: 0;
    transform: translateY(-50px);
    transition: all 0.5s ease;
    z-index: 10000;
    font-weight: 600;
}

.toast.show {
    opacity: 1;
    transform: translateY(0);
}

/* Optional: different color for error */
.toast.error {
    background-color: #ef4444;
}
    nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
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

    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="bg-animation">
        <div class="floating-orb orb1"></div>
        <div class="floating-orb orb2"></div>
        <div class="floating-orb orb3"></div>
    </div>

    <!-- Header -->
<header>
    <div class="container"> 
    <nav >
        <div class="logo"  >🔍 TruthUncovered</div>
        
        <div class="user-profile">

            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'Admin'): ?>
                <div class="nav-actions">
                    <a href="analytic.php" class="back-button">Analytics Dashboard</a>
                </div>
            <?php endif; ?>

            <button class="notification-badge" onclick="toggleNotifications()">
                🔔
                <span class="badge-count" id="notificationCount">3</span>
            </button>
            
            <div class="user-info"  onclick="window.location.href='profile.php';">
                <div class="user-name" id="userName">
                    <?php 
                        echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : "Guest";
                    ?>
                </div>
                <div class="user-role" id="userRole">
        <?php echo isset($_SESSION['role']) ? htmlspecialchars($_SESSION['role']) : "Citizen"; ?>
    </div>
    
            </div>
              <!-- logout icon  -->
         <span>
<?php if (isset($_SESSION['user_id'])): ?>
    <a href="?logout=1" class="logout-btn" title="Logout">
        <i class="fa-solid fa-arrow-right-from-bracket"></i>
    </a>
<?php endif; ?>
</span>
        </div>

<!-- notification report  -->



    </nav>
</div>
</header>

    <!-- Main Content -->
    <main class="container">

    <?php 
// Show session notification as toast if exists
if (!empty($_SESSION['notification'])): ?>
    <div id="toast" class="toast success"><?= $_SESSION['notification'] ?></div>
    <?php unset($_SESSION['notification']); ?>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div id="toast" class="toast error"><?= $error ?></div>
<?php endif; ?>
        <!-- About Section - First Thing Users See -->
        <section class="about-section">
            <div class="about-content">
                <h1 class="about-title">Truth Uncovered</h1>
                <p class="about-subtitle">Empowering Bangladeshi Citizens Through Digital Civic Engagement</p>
                <p class="about-description">
                    Truth Uncovered is a digital civic platform designed to empower Bangladeshi citizens to report and expose antisocial activities, corruption, hazards, and human rights violations in a safe and structured way. We believe transparency and accountability are the foundations of a just society.
                </p>
                
                <div class="platform-features">
                    <div class="feature-item" onclick="window.location.href='report.php?mode=anonymous'" > 
                        <span class="feature-icon">🔒</span>
                        <div class="feature-title">Anonymous Reporting</div>
                        <div class="feature-desc">Complete privacy protection with end-to-end encryption</div>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">⚡</span>
                        <div class="feature-title">Fast Response</div>
                        <div class="feature-desc">24-hour average response time to all reports</div>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">🛡️</span>
                        <div class="feature-title">Legal Protection</div>
                        <div class="feature-desc">Whistleblower protection under Bangladesh law</div>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">📈</span>
                        <div class="feature-title">Real Impact</div>
                        <div class="feature-desc">89% resolution rate with measurable outcomes</div>
                    </div>
                </div>
              
     <div class="button-container">
        
           <button class="explore-button button-width" 
        title="Go to the blog creation page" 
        onclick="window.location.href='blogposts.php'" 
        style="display: flex; align-items: center; justify-content:center; gap: 0.5rem; padding: 0.8rem 1.5rem; font-size: 1rem; border-radius: 12px; border: none; background: linear-gradient(135deg, #10b981, #059669); color: #fff; cursor: pointer;">
    <!-- SVG icon: pencil/edit for creating a post -->
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20" height="20">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 20h9M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z" />
    </svg>
    Start Writing Your Blog
</button>
<button class="explore-button button-width" 
                title="Go to the report submission page" 
                onclick="window.location.href='report.php'" 
                style="display: flex; align-items: center; justify-content:center; gap: 0.5rem; padding: 0.8rem 1.5rem; font-size: 1rem; border-radius: 12px; border: none; background: linear-gradient(135deg, #3b82f6, #8b5cf6); color: #fff; cursor: pointer;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20" height="20">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 20h9M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z" />
                </svg>
                Submit a Report
            </button>
              </div>   
            </div>
        </section>

        <!-- Security Banner -->
        <div class="security-banner fade-in-delay"    onclick="window.location.href='categories.php'" >
            <span class="security-icon">🛡️</span>
          <strong>Explore All Five Categories</strong>
        </div>

        <!-- Quick Categories -->
        <!-- <section class="categories-section" id="categoriesSection">
            <h2 class="section-title">📋 Report Categories</h2>
            <div class="categories-grid">
                <div class="category-card" onclick="selectCategory('corruption')">
                    <span class="category-icon">💰</span>
                    <div class="category-title">Corruption</div>
                    <div class="category-count">142 active cases</div>
                </div>
                <div class="category-card" onclick="selectCategory('harassment')">
                    <span class="category-icon">🚫</span>
                    <div class="category-title">Harassment</div>
                    <div class="category-count">89 active cases</div>
                </div>
                <div class="category-card" onclick="selectCategory('safety')">
                    <span class="category-icon">⚠️</span>
                    <div class="category-title">Safety Hazard</div>
                    <div class="category-count">56 active cases</div>
                </div>
                <div class="category-card" onclick="selectCategory('fraud')">
                    <span class="category-icon">🔍</span>
                    <div class="category-title">Fraud</div>
                    <div class="category-count">73 active cases</div>
                </div>
                <div class="category-card" onclick="selectCategory('misconduct')">
                    <span class="category-icon">⚖️</span>
                    <div class="category-title">Misconduct</div>
                    <div class="category-count">34 active cases</div>
                </div>
                <div class="category-card" onclick="selectCategory('environment')">
                    <span class="category-icon">🌍</span>
                    <div class="category-title">Environmental</div>
                    <div class="category-count">28 active cases</div>
                </div>
            </div>
        </section> -->

        <!-- Dashboard Widgets -->
        <div class="dashboard-grid fade-in-delay">
            <!-- Recent Reports -->
            <div class="glass-widget">
                <h3 class="widget-title">📄 Your Recent Reports</h3>
                <div class="report-item">
                    <div class="report-title">Unauthorized Construction in Protected Area</div>
                    <div class="report-meta">
                        <span>Aug 25, 2025</span>
                        <span class="status-badge status-verified">Verified</span>
                    </div>
                </div>
                <div class="report-item">
                    <div class="report-title">Public Fund Misuse in Education Sector</div>
                    <div class="report-meta">
                        <span>Aug 20, 2025</span>
                        <span class="status-badge status-pending">Under Review</span>
                    </div>
                </div>
                <div class="report-item">
                    <div class="report-title">Workplace Harassment at Government Office</div>
                    <div class="report-meta">
                        <span>Aug 15, 2025</span>
                        <span class="status-badge status-action-taken">Action Taken</span>
                    </div>
                </div>
                <button class="action-button primary-button">View All Reports</button>
            </div>

            <!-- Analytics Dashboard -->
            <div class="glass-widget">
                <h3 class="widget-title">📊 Platform Analytics</h3>
                <div class="analytics-grid">
                    <div class="metric-card">
                        <div class="metric-value">1,247</div>
                        <div class="metric-label">Total Reports</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-value">89%</div>
                        <div class="metric-label">Resolution Rate</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-value">156</div>
                        <div class="metric-label">This Month</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-value">24h</div>
                        <div class="metric-label">Avg Response</div>
                    </div>
                </div>
                <div style="margin-top: 1.5rem; padding: 1.2rem; background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.15);">
                    <strong style="color: #ffffff;">🗺️ Regional Activity Heatmap:</strong><br>
                    <div style="margin-top: 0.8rem; font-size: 0.9rem; color: #cbd5e1;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.3rem;">
                            <span>Dhaka Division:</span> <span style="color: #ef4444; font-weight: 600;">High Activity</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.3rem;">
                            <span>Chittagong:</span> <span style="color: #f59e0b; font-weight: 600;">Medium</span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span>Sylhet:</span> <span style="color: #10b981; font-weight: 600;">Low</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Institution Rankings -->
            <div class="glass-widget">
                <h3 class="widget-title">🏢 Institution Trust Rankings</h3>
                <div class="ranking-item">
                    <div>
                        <div style="font-weight: 600; color: #ffffff;">Anti-Corruption Commission</div>
                        <div style="font-size: 0.9rem; color: #94a3b8;">Government Agency</div>
                    </div>
                    <div class="trust-score">8.5/10</div>
                </div>
                <div class="ranking-item">
                    <div>
                        <div style="font-weight: 600; color: #ffffff;">Bangladesh Police</div>
                        <div style="font-size: 0.9rem; color: #94a3b8;">Law Enforcement</div>
                    </div>
                    <div class="trust-score">6.2/10</div>
                </div>
                <div class="ranking-item">
                    <div>
                        <div style="font-weight: 600; color: #ffffff;">Local Government</div>
                        <div style="font-size: 0.9rem; color: #94a3b8;">Municipal Services</div>
                    </div>
                    <div class="trust-score">5.8/10</div>
                </div>
                <button class="action-button"   onclick="window.location.href='institutionranking.php'" >View Full Rankings</button>
            </div>

        

      

        
        </div>

        <!-- Impact Stories Section -->
        <section class="glass-widget" style="margin: 3rem 0;">
            <h3 class="widget-title">🌟 Success Stories & Real Impact</h3>
            <div class="story-carousel">
                <div class="story-card">
                    <div class="story-title">Hospital Corruption Exposed</div>
                    <div class="story-outcome">
                        A citizen's report led to the investigation of a major hospital corruption scandal. 
                        Five officials were arrested, $2.1M in misappropriated funds recovered, and hospital 
                        services improved for over 50,000 patients in the Dhaka region.
                    </div>
                    <div class="story-date">Published: Aug 15, 2025</div>
                </div>
                <div class="story-card">
                    <div class="story-title">Environmental Violation Stopped</div>
                    <div class="story-outcome">
                        Industrial waste dumping into the Buriganga River was halted after a whistleblower's 
                        evidence. The company faced legal action, cleanup operations began, and 12 communities 
                        now have access to cleaner water.
                    </div>
                    <div class="story-date">Published: Aug 10, 2025</div>
                </div>
                <div class="story-card">
                    <div class="story-title">Education Fund Recovery</div>
                    <div class="story-outcome">
                        School board corruption exposed through our platform resulted in the recovery of 
                        ৳15 lakh in education funds. A new oversight committee was established to prevent 
                        future misconduct.
                    </div>
                    <div class="story-date">Published: Aug 5, 2025</div>
                </div>
                <div class="story-card">
                    <div class="story-title">Workplace Safety Improved</div>
                    <div class="story-outcome">
                        Anonymous report about unsafe working conditions led to factory inspection and 
                        mandatory safety upgrades, protecting 300+ garment workers from potential hazards.
                    </div>
                    <div class="story-date">Published: Jul 28, 2025</div>
                </div>
            </div>
        </section>



  



 

        <!-- Advanced Case Management -->
        <section class="glass-widget">
            <h3 class="widget-title">📋 Case Management Dashboard</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                <!-- Active Cases -->
                <div>
                    <h4 style="color: #ffffff; margin-bottom: 1rem; font-weight: 600;">🔄 Active Cases</h4>
                    <div class="report-item">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                            <div class="report-title">Case #CR-2025-089</div>
                            <span class="status-badge status-verified">Investigating</span>
                        </div>
                        <div style="color: #cbd5e1; font-size: 0.9rem;">Construction permit violation</div>
                        <div style="margin-top: 0.8rem;">
                            <div style="display: flex; justify-content: space-between; font-size: 0.8rem; color: #94a3b8; margin-bottom: 0.3rem;">
                                <span>Progress</span>
                                <span>75%</span>
                            </div>
                            <div style="background: rgba(255, 255, 255, 0.2); border-radius: 10px; overflow: hidden;">
                                <div style="background: linear-gradient(135deg, #10b981, #059669); height: 6px; width: 75%; border-radius: 10px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="report-item">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                            <div class="report-title">Case #CR-2025-076</div>
                            <span class="status-badge status-pending">Pending Review</span>
                        </div>
                        <div style="color: #cbd5e1; font-size: 0.9rem;">Educational fund misuse</div>
                        <div style="margin-top: 0.8rem;">
                            <div style="display: flex; justify-content: space-between; font-size: 0.8rem; color: #94a3b8; margin-bottom: 0.3rem;">
                                <span>Progress</span>
                                <span>25%</span>
                            </div>
                            <div style="background: rgba(255, 255, 255, 0.2); border-radius: 10px; overflow: hidden;">
                                <div style="background: linear-gradient(135deg, #f59e0b, #d97706); height: 6px; width: 25%; border-radius: 10px;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Case Status Updates -->
                <div>
                    <h4 style="color: #ffffff; margin-bottom: 1rem; font-weight: 600;">📢 Recent Updates</h4>
                    <div style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 12px; padding: 1rem; margin-bottom: 1rem;">
                        <div style="font-weight: 600; color: #10b981; margin-bottom: 0.5rem;">✅ Case Resolved</div>
                        <div style="color: #cbd5e1; font-size: 0.9rem;">Hospital corruption case led to arrests and fund recovery</div>
                        <div style="color: #94a3b8; font-size: 0.8rem; margin-top: 0.5rem;">2 hours ago</div>
                    </div>
                    <div style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 12px; padding: 1rem; margin-bottom: 1rem;">
                        <div style="font-weight: 600; color: #3b82f6; margin-bottom: 0.5rem;">🔍 Investigation Started</div>
                        <div style="color: #cbd5e1; font-size: 0.9rem;">Environmental violation report assigned to investigation team</div>
                        <div style="color: #94a3b8; font-size: 0.8rem; margin-top: 0.5rem;">5 hours ago</div>
                    </div>
                    <div style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 12px; padding: 1rem;">
                        <div style="font-weight: 600; color: #f59e0b; margin-bottom: 0.5rem;">⏳ Under Review</div>
                        <div style="color: #cbd5e1; font-size: 0.9rem;">New workplace harassment case submitted for initial review</div>
                        <div style="color: #94a3b8; font-size: 0.8rem; margin-top: 0.5rem;">1 day ago</div>
                    </div>
                </div>
            </div>
            <button class="action-button primary-button">View Detailed Case Dashboard</button>
        </section>

    

        <!-- Regional Performance & Institution Rankings -->
        <!-- <section class="glass-widget">
            <h3 class="widget-title">🌍 Regional Performance & Institution Rankings</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
                Regional Performance -->
                <!-- <div>
                    <h4 style="color: #ffffff; margin-bottom: 1rem; font-weight: 600;">📍 Regional Performance</h4>
                    <div style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 12px; padding: 1.2rem; margin-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.8rem;">
                            <span style="color: #ffffff; font-weight: 600;">Dhaka Division</span>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <div style="background: #ef4444; width: 12px; height: 12px; border-radius: 50%;"></div>
                                <span style="color: #ef4444; font-weight: 600;">High Activity</span>
                            </div>
                        </div>
                        <div style="background: rgba(255, 255, 255, 0.2); border-radius: 10px; overflow: hidden; margin-bottom: 0.5rem;">
                            <div style="background: linear-gradient(135deg, #ef4444, #dc2626); height: 6px; width: 85%;"></div>
                        </div>
                        <div style="color: #cbd5e1; font-size: 0.8rem;">342 reports • 78% resolution rate</div>
                    </div>
                    
                    <div style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 12px; padding: 1.2rem; margin-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.8rem;">
                            <span style="color: #ffffff; font-weight: 600;">Chittagong Division</span>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <div style="background: #f59e0b; width: 12px; height: 12px; border-radius: 50%;"></div>
                                <span style="color: #f59e0b; font-weight: 600;">Medium Activity</span>
                            </div>
                        </div>
                        <div style="background: rgba(255, 255, 255, 0.2); border-radius: 10px; overflow: hidden; margin-bottom: 0.5rem;">
                            <div style="background: linear-gradient(135deg, #f59e0b, #d97706); height: 6px; width: 60%;"></div>
                        </div>
                        <div style="color: #cbd5e1; font-size: 0.8rem;">198 reports • 82% resolution rate</div>
                    </div>
                    
                    <div style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 12px; padding: 1.2rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.8rem;">
                            <span style="color: #ffffff; font-weight: 600;">Sylhet Division</span>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <div style="background: #10b981; width: 12px; height: 12px; border-radius: 50%;"></div>
                                <span style="color: #10b981; font-weight: 600;">Low Activity</span>
                            </div>
                        </div>
                        <div style="background: rgba(255, 255, 255, 0.2); border-radius: 10px; overflow: hidden; margin-bottom: 0.5rem;">
                            <div style="background: linear-gradient(135deg, #10b981, #059669); height: 6px; width: 35%;"></div>
                        </div>
                        <div style="color: #cbd5e1; font-size: 0.8rem;">87 reports • 91% resolution rate</div>
                    </div>
                </div> -->

                <!-- Enhanced Institution Rankings -->
                <!-- <div>
                    <h4 style="color: #ffffff; margin-bottom: 1rem; font-weight: 600;">🏛️ Institution Trust Scores</h4>
                    <div class="ranking-item">
                        <div>
                            <div style="font-weight: 600; color: #ffffff;">Anti-Corruption Commission</div>
                            <div style="font-size: 0.8rem; color: #94a3b8;">Government Agency • 2,341 interactions</div>
                            <div style="display: flex; gap: 0.5rem; margin-top: 0.3rem;">
                                <span style="background: rgba(16, 185, 129, 0.2); color: #10b981; padding: 0.2rem 0.5rem; border-radius: 8px; font-size: 0.7rem;">Responsive</span>
                                <span style="background: rgba(59, 130, 246, 0.2); color: #3b82f6; padding: 0.2rem 0.5rem; border-radius: 8px; font-size: 0.7rem;">Transparent</span>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div class="trust-score">8.5/10</div>
                            <div style="color: #10b981; font-size: 0.8rem; margin-top: 0.3rem;">↗️ +0.3</div>
                        </div>
                    </div>
                    
                    <div class="ranking-item">
                        <div>
                            <div style="font-weight: 600; color: #ffffff;">Bangladesh Police</div>
                            <div style="font-size: 0.8rem; color: #94a3b8;">Law Enforcement • 1,876 interactions</div>
                            <div style="display: flex; gap: 0.5rem; margin-top: 0.3rem;">
                                <span style="background: rgba(239, 68, 68, 0.2); color: #ef4444; padding: 0.2rem 0.5rem; border-radius: 8px; font-size: 0.7rem;">Slow Response</span>
                                <span style="background: rgba(245, 158, 11, 0.2); color: #f59e0b; padding: 0.2rem 0.5rem; border-radius: 8px; font-size: 0.7rem;">Improving</span>
             
</section> -->
    <!-- Whistleblower Protection Center -->
        <section class="glass-widget">
        
            <div style="margin-top: 1.5rem; padding: 1.2rem; background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 12px;">
                <div style="color: #ffffff; font-weight: 600; margin-bottom: 0.8rem;">📞 Emergency Support</div>
                <div style="color: #cbd5e1; font-size: 0.9rem;">
                    If you face retaliation or need immediate protection:<br>
                    <strong style="color: #ef4444;">Hotline: +880-1234-567890</strong> (24/7)<br>
                    <strong style="color: #3b82f6;">Email: protection@truthuncovered.bd</strong>
                </div>
            </div>
        </section>
    </main>

    <!-- Notification Panel -->
    <div id="notificationPanel" style="display: none; position: fixed; top: 80px; right: 20px; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(25px); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 16px; box-shadow: 0 25px 50px rgba(0,0,0,0.3); padding: 1.5rem; min-width: 320px; z-index: 1000;">
        <h4 style="margin-bottom: 1rem; color: #1f2937; display: flex; align-items: center; gap: 0.5rem;">
            🔔 Recent Notifications
        </h4>
        <div style="border-bottom: 1px solid rgba(0,0,0,0.1); padding: 1rem 0;">
            <div style="font-weight: 600; color: #1f2937;">Case Update</div>
            <div style="font-size: 0.9rem; color: #6b7280; margin-top: 0.3rem;">Your report #CR-2025-089 has been verified and forwarded to authorities</div>
            <div style="font-size: 0.8rem; color: #9ca3af; margin-top: 0.5rem;">2 hours ago</div>
        </div>
        <div style="border-bottom: 1px solid rgba(0,0,0,0.1); padding: 1rem 0;">
            <div style="font-weight: 600; color: #1f2937;">New Blog Post</div>
            <div style="font-size: 0.9rem; color: #6b7280; margin-top: 0.3rem;">Understanding Your Legal Rights as a Whistleblower</div>
            <div style="font-size: 0.8rem; color: #9ca3af; margin-top: 0.5rem;">1 day ago</div>
        </div>
        <div style="padding: 1rem 0;">
            <div style="font-weight: 600; color: #1f2937;">System Update</div>
            <div style="font-size: 0.9rem; color: #6b7280; margin-top: 0.3rem;">Enhanced security features and improved anonymization</div>
            <div style="font-size: 0.8rem; color: #9ca3af; margin-top: 0.5rem;">3 days ago</div>
        </div>
        <button style="width: 100%; padding: 0.8rem; background: linear-gradient(135deg, #3b82f6, #8b5cf6); color: white; border: none; border-radius: 12px; margin-top: 1rem; cursor: pointer; font-weight: 500;">View All Notifications</button>
    </div>
<!-- 

    </div> -->

    <!-- Emergency Report Modal -->
    <div id="emergencyModal" class="modal">
        <div class="modal-content" style="border: 2px solid rgba(239, 68, 68, 0.3);">
            <button class="close-button" onclick="closeModal('emergencyModal')">&times;</button>
            <h3 style="margin-bottom: 1rem; color: #ef4444; font-size: 1.5rem; font-weight: 700;">🚨 Emergency Report</h3>
            <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: 12px; padding: 1rem; margin-bottom: 1.5rem; color: #1f2937;">
                <strong>⚠️ Important:</strong> For immediate life-threatening situations, please contact emergency services (999) first, then submit this report.
            </div>
            <form>
                <div class="form-group">
                    <label class="form-label">Emergency Type:</label>
                    <select class="form-select">
                        <option value="">Select emergency type...</option>
                        <option value="safety">🚨 Immediate Safety Threat</option>
                        <option value="violence">⚔️ Violence or Threat</option>
                        <option value="environmental">🌊 Environmental Disaster</option>
                        <option value="corruption">💰 Large-scale Corruption</option>
                        <option value="rights">⚖️ Human Rights Violation</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Immediate Description:</label>
                    <textarea class="form-textarea" rows="4" placeholder="Describe the emergency situation briefly but clearly..."></textarea>
                </div>
                <button type="submit" style="width: 100%; padding: 1rem; background: linear-gradient(135deg, #ef4444, #dc2626); color: white; border: none; border-radius: 12px; font-weight: 600; cursor: pointer;">
                    🚨 Submit Emergency Report
                </button>
            </form>
        </div>
    </div>

    <script>
        // Smooth animations and interactions
window.addEventListener('DOMContentLoaded', () => {
    const toast = document.getElementById('toast');
    if(toast) {
        toast.classList.add('show'); // slide in

        // Hide after 4 seconds
        setTimeout(() => {
            toast.classList.remove('show');
        }, 4000);
    }
});


        function toggleNotifications() {
            const panel = document.getElementById('notificationPanel');
            panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
            
            if (panel.style.display === 'block') {
                panel.style.animation = 'slideInFromRight 0.3s ease-out';
            }
        }

        function showReportModal() {
            const modal = document.getElementById('reportModal');
            modal.style.display = 'flex';
            modal.style.animation = 'fadeIn 0.3s ease-out';
        }

        function showEmergencyModal() {
            const modal = document.getElementById('emergencyModal');
            modal.style.display = 'flex';
            modal.style.animation = 'fadeIn 0.3s ease-out';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        function selectCategory(category) {
            // Animate category selection
            event.target.style.animation = 'pulse 0.6s ease-out';
            
            // Show report modal with pre-selected category
            showReportModal();
            setTimeout(() => {
                const select = document.getElementById('reportCategory');
                select.value = category;
                select.style.background = 'rgba(59, 130, 246, 0.1)';
            }, 300);
        }

        function scrollToCategories() {
            document.getElementById('categoriesSection').scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }

        function triggerUpload() {
            // Simulate file upload trigger
            const uploadZone = event.target;
            uploadZone.style.background = 'rgba(59, 130, 246, 0.15)';
            uploadZone.style.borderColor = 'rgba(59, 130, 246, 0.8)';
            
            setTimeout(() => {
                uploadZone.style.background = 'rgba(59, 130, 246, 0.05)';
                uploadZone.style.borderColor = 'rgba(59, 130, 246, 0.4)';
            }, 500);
            
            alert('File upload interface would open here. In the actual implementation, this would trigger the file picker.');
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const reportModal = document.getElementById('reportModal');
            const emergencyModal = document.getElementById('emergencyModal');
            const notificationPanel = document.getElementById('notificationPanel');
            
            if (event.target === reportModal) {
                reportModal.style.display = 'none';
            }
            if (event.target === emergencyModal) {
                emergencyModal.style.display = 'none';
            }
            if (!event.target.closest('.notification-badge') && !event.target.closest('#notificationPanel')) {
                notificationPanel.style.display = 'none';
            }
        }

        // Add smooth scroll behavior and intersection observer for animations
        document.addEventListener('DOMContentLoaded', function() {
            const widgets = document.querySelectorAll('.glass-widget');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.animation = 'slideInUp 0.6s ease-out';
                    }
                });
            }, { threshold: 0.1 });

            widgets.forEach(widget => {
                observer.observe(widget);
            });
        });

        // Dynamic role-based content
        function updateDashboardForRole(role) {
            const userName = document.getElementById('userName');
            const userRole = document.getElementById('userRole');
            
            // This would be populated from actual user data
            const roleConfig = {
                'Citizen': {
                    name: 'Rahim Ahmed',
                    features: ['submit reports', 'track cases', 'view impact stories']
                },
                'NGO Partner': {
                    name: 'Sarah Khan',
                    features: ['access analytics', 'institution rankings', 'impact data']
                },
                'Government Officer': {
                    name: 'Dr. Mohammad Hassan',
                    features: ['case dashboards', 'institutional analytics', 'policy insights']
                },
                'Admin': {
                    name: 'System Administrator',
                    features: ['manage users', 'moderate content', 'system analytics']
                }
            };
        }

        // Simulate real-time updates
        setInterval(() => {
            const count = document.getElementById('notificationCount');
            const currentCount = parseInt(count.textContent);
            
            // Randomly update notification count (simulation)
            if (Math.random() < 0.1) { // 10% chance every 5 seconds
                count.textContent = currentCount + 1;
                count.style.animation = 'bounce 0.6s ease-out';
            }
        }, 5000);

        // Add CSS animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideInFromRight {
                from { opacity: 0; transform: translateX(100%); }
                to { opacity: 1; transform: translateX(0); }
            }
            
            @keyframes bounce {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.3); }
            }
            
            @keyframes pulse {
                0% { transform: scale(1); }
                50% { transform: scale(1.05); }
                100% { transform: scale(1); }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>

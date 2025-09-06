<?php
include 'DBconnect.php';

// Function to get all institutions
function getAllInstitutions($conn) {
    $result = $conn->query("SELECT * FROM institutions ORDER BY Name ASC");
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to get institution rankings with feedback - SIMPLIFIED
function getInstitutionRankings($conn, $institution_id) {
    $stmt = $conn->prepare("
        SELECT r.*, i.Name as Institution_Name 
        FROM rankings r 
        JOIN institutions i ON r.Institution_ID = i.Institution_ID 
        WHERE r.Institution_ID = ? 
        ORDER BY r.Created_At DESC
    ");
    $stmt->bind_param("i", $institution_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to get average scores for an institution - UPDATED FOR DYNAMIC RANKINGS
function getAverageScores($conn, $institution_id) {
    $stmt = $conn->prepare("
        SELECT 
            AVG(CASE WHEN Category = 'Corruption' THEN Score END) as avg_corruption,
            AVG(CASE WHEN Category = 'Harassment' THEN Score END) as avg_harassment,
            AVG(CASE WHEN Category = 'Public Hazards' THEN Score END) as avg_public_hazards,
            AVG(CASE WHEN Category = 'Dowry' THEN Score END) as avg_dowry,
            AVG(CASE WHEN Category = 'Antisocial Behavior' THEN Score END) as avg_antisocial,
            COUNT(*) as total_ratings
        FROM rankings 
        WHERE Institution_ID = ?
    ");
    $stmt->bind_param("i", $institution_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Function to submit a ranking
function submitRanking($conn, $data) {
    $stmt = $conn->prepare("
        INSERT INTO rankings 
        (Institution_ID, User_ID, Score, Category, Description, Created_At) 
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    
    return $stmt->execute([
        $data['institution_id'],
        $data['user_id'],
        $data['score'],
        $data['category'],
        $data['description']
    ]);
}

// Handle form submission - ENHANCED FOR DYNAMIC FEEDBACK
$success = $error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_POST['user_id'] = 1; // Replace with actual user session
    
    if (submitRanking($conn, $_POST)) {
        $success = "✅ Your ranking has been submitted successfully! The rankings have been updated.";
    } else {
        $error = "❌ Something went wrong. Please try again.";
    }
}

$institutions = getAllInstitutions($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Truth Uncovered - Institution Rankings</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
     
        }
        .container {
            max-width:1200px;
            margin:0 auto;
        }
        
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

        .main-container {
            position: relative;
            z-index: 10;
            max-width: 1200px;
            margin: 0 auto;
            animation: slideUp 0.8s ease-out;
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

        .header-section {
            text-align: center;
            margin-bottom: 40px;
            margin-top:20px;
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
            content: '🏛️';
            font-size: 36px;
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
        }

        .page-subtitle {
            color: #94a3b8;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            padding: 40px;
            box-shadow: 
                0 8px 32px 0 rgba(0, 0, 0, 0.37),
                inset 0 0 0 1px rgba(255, 255, 255, 0.08);
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }

        .glass-card::before {
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

        .glass-card:hover::before {
            opacity: 0.5;
        }

        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        .tab-container {
            display: flex;
            margin-bottom: 30px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 6px;
        }

        .tab-button {
            flex: 1;
            padding: 12px 20px;
            background: transparent;
            border: none;
            border-radius: 8px;
            color: #94a3b8;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .tab-button.active {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            color: white;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
            margin-bottom: 30px;
        }

        .form-group {
            position: relative;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        .form-label {
            display: block;
            color: #e2e8f0;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-input, .form-select, .form-textarea {
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

        .form-textarea {
            resize: vertical;
            min-height: 120px;
        }

        .form-input::placeholder, .form-textarea::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(59, 130, 246, 0.5);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
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

        .score-slider {
            margin: 20px 0;
        }

        .slider-container {
            position: relative;
            margin: 15px 0;
        }

        .slider {
            width: 100%;
            height: 8px;
            border-radius: 4px;
            background: rgba(255, 255, 255, 0.1);
            outline: none;
            appearance: none;
        }

        .slider::-webkit-slider-thumb {
            appearance: none;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
        }

        .slider::-moz-range-thumb {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            cursor: pointer;
            border: none;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
        }

        .score-display {
            text-align: center;
            margin-top: 10px;
            font-size: 1.2rem;
            font-weight: 700;
            color: #3b82f6;
        }

        .score-labels {
            display: flex;
            justify-content: space-between;
            margin-top: 8px;
            font-size: 0.8rem;
            color: #94a3b8;
        }

        .submit-btn {
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
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .submit-btn:hover::before {
            left: 100%;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(59, 130, 246, 0.4);
        }

        .institution-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 20px;
        }

        .institution-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            padding: 25px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }

        .institution-card:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }

        .institution-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .institution-name {
            font-size: 1.2rem;
            font-weight: 700;
            color: #e2e8f0;
            margin-bottom: 5px;
        }

        .institution-type {
            font-size: 0.9rem;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .institution-region {
            font-size: 0.8rem;
            color: #64748b;
        }

        .score-badges {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 8px;
            margin-top: 15px;
            min-height: 60px;
        }

        .score-badge {
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-align: center;
            background: rgba(59, 130, 246, 0.1);
            color: #93c5fd;
            border: 1px solid rgba(59, 130, 246, 0.2);
        }

        .corruption-score { background: rgba(239, 68, 68, 0.2); color: #fca5a5; border-color: rgba(239, 68, 68, 0.3); }
        .harassment-score { background: rgba(251, 146, 60, 0.2); color: #fdba74; border-color: rgba(251, 146, 60, 0.3); }
        .hazard-score { background: rgba(234, 179, 8, 0.2); color: #fde047; border-color: rgba(234, 179, 8, 0.3); }
        .dowry-score { background: rgba(168, 85, 247, 0.2); color: #c4b5fd; border-color: rgba(168, 85, 247, 0.3); }
        .antisocial-score { background: rgba(236, 72, 153, 0.2); color: #f9a8d4; border-color: rgba(236, 72, 153, 0.3); }

        /* NEW CSS FOR DYNAMIC RANKINGS */
        .no-ratings-badge {
            background: rgba(100, 116, 139, 0.1);
            color: #94a3b8;
            border: 1px solid rgba(100, 116, 139, 0.2);
            padding: 8px 12px;
            border-radius: 12px;
            font-size: 0.8rem;
            text-align: center;
            font-style: italic;
            grid-column: span 2;
        }

        .institution-card.loading {
            opacity: 0.7;
            pointer-events: none;
        }

        .institution-card.loading::after {
            content: '🔄 Updating...';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(59, 130, 246, 0.9);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            z-index: 10;
        }

        .category-group {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 15px 0;
        }

        .category-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #e2e8f0;
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            transition: background 0.2s ease;
        }

        .category-item:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .category-item input[type="radio"] {
            width: 18px;
            height: 18px;
            accent-color: #3b82f6;
            cursor: pointer;
        }

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

        @media (max-width: 768px) {
            .glass-card {
                padding: 25px 20px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full-width {
                grid-column: span 1;
            }

            .page-title {
                font-size: 2rem;
            }

            .tab-container {
                flex-direction: column;
                gap: 5px;
            }

            .category-group {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .institution-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
          <header>
        <div class="container">
            <nav>
                <div class="logo"   onclick="window.location.href='index.php'">🔍 TruthUncovered</div>
                <div class="nav-actions">
                    <a href="index.php" class="back-button"> ←  Back to Home Page</a>
                </div>
            </nav>
        </div>
         </header>
    <div class="background-image"></div>

    <div class="bg-animation">
        <div class="floating-orb orb1"></div>
        <div class="floating-orb orb2"></div>
        <div class="floating-orb orb3"></div>
        <div class="floating-orb orb4"></div>
    </div>

    <div class="main-container">
     
   
        <div class="header-section">
            <div class="logo-icon"></div>
            <h1 class="page-title">INSTITUTION RANKINGS</h1>
            <p class="page-subtitle">Rate institutions on corruption, harassment, and public safety issues</p>
        </div>

        <div class="glass-card">
            <div class="tab-container">
                <button class="tab-button active" onclick="switchTab('rank')">📝 Rate Institution</button>
                <button class="tab-button" onclick="switchTab('view')">📊 View Rankings</button>
            </div>

            <?php if ($success): ?>
                <div class="message success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="message error"><?php echo $error; ?></div>
            <?php endif; ?>

            <div id="rank-tab" class="tab-content active">
                <form method="POST" action="" id="rankingForm">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label" for="institution_id">Select Institution</label>
                            <select class="form-select" id="institution_id" name="institution_id" required>
                                <option value="" disabled selected>Choose an institution</option>
                                <option value="1">Anti-Corruption Commission (Government)</option>
                                <option value="2">Dhaka Metropolitan Police (Police)</option>
                                <option value="3">Bangladesh Police Headquarters (Police)</option>
                                <option value="4">Ministry of Home Affairs (Government)</option>
                                <option value="5">Ministry of Women and Children Affairs (Government)</option>
                                <option value="6">Bangladesh Bank (Government)</option>
                                <option value="7">National Board of Revenue (Government)</option>
                                <option value="8">Rapid Action Battalion - RAB (Police)</option>
                                <option value="9">Detective Branch - DB (Police)</option>
                                <option value="10">Traffic Police (Police)</option>
                                <option value="11">Transparency International Bangladesh (NGO)</option>
                                <option value="12">BRAC (NGO)</option>
                                <option value="13">Ain o Salish Kendra (NGO)</option>
                                <option value="14">Bangladesh Legal Aid and Services Trust (NGO)</option>
                                <option value="15">Manusher Jonno Foundation (NGO)</option>
                                <option value="16">Odhikar (NGO)</option>
                                <option value="17">Bangladesh Environmental Lawyers Association (NGO)</option>
                                <option value="18">Dhaka Ahsania Mission (NGO)</option>
                                <option value="19">Proshika (NGO)</option>
                                <option value="20">ActionAid Bangladesh (NGO)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Category</label>
                            <div class="category-group">
                                <div class="category-item">
                                    <input type="radio" id="corruption" name="category" value="Corruption" required>
                                    <label for="corruption">🔴 Corruption</label>
                                </div>
                                <div class="category-item">
                                    <input type="radio" id="harassment" name="category" value="Harassment" required>
                                    <label for="harassment">🟠 Harassment</label>
                                </div>
                                <div class="category-item">
                                    <input type="radio" id="public_hazards" name="category" value="Public Hazards" required>
                                    <label for="public_hazards">🟡 Public Hazards</label>
                                </div>
                                <div class="category-item">
                                    <input type="radio" id="dowry" name="category" value="Dowry" required>
                                    <label for="dowry">🟣 Dowry Issues</label>
                                </div>
                                <div class="category-item">
                                    <input type="radio" id="antisocial" name="category" value="Antisocial Behavior" required>
                                    <label for="antisocial">🔵 Antisocial Behavior</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group full-width">
                            <label class="form-label" for="score">Score (0-10)</label>
                            <div class="score-slider">
                                <input type="range" class="slider" id="score" name="score" min="0" max="10" value="5" required>
                                <div class="score-display">Score: <span id="scoreValue">5</span>/10</div>
                                <div class="score-labels">
                                    <span>Poor (0)</span>
                                    <span>Average (5)</span>
                                    <span>Excellent (10)</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group full-width">
                            <label class="form-label" for="description">Your Experience & Opinion</label>
                            <textarea 
                                class="form-textarea" 
                                id="description" 
                                name="description" 
                                placeholder="Share your experience or opinion about this institution regarding the selected category. Be specific and constructive in your feedback."
                                required
                            ></textarea>
                        </div>
                    </div>

                    <button type="submit" class="submit-btn">
                        Submit Your Rating
                    </button>
                </form>
            </div>

            <!-- UPDATED VIEW TAB WITH DYNAMIC RANKINGS -->
            <div id="view-tab" class="tab-content">
                <div class="institution-grid">
                    <?php 
                    $predefined_institutions = [
                        ['id' => 1, 'name' => 'Anti-Corruption Commission', 'type' => 'Government', 'region' => 'Dhaka'],
                        ['id' => 2, 'name' => 'Dhaka Metropolitan Police', 'type' => 'Police', 'region' => 'Dhaka'],
                        ['id' => 3, 'name' => 'Bangladesh Police Headquarters', 'type' => 'Police', 'region' => 'Dhaka'],
                        ['id' => 4, 'name' => 'Ministry of Home Affairs', 'type' => 'Government', 'region' => 'Dhaka'],
                        ['id' => 5, 'name' => 'Ministry of Women and Children Affairs', 'type' => 'Government', 'region' => 'Dhaka'],
                        ['id' => 6, 'name' => 'Bangladesh Bank', 'type' => 'Government', 'region' => 'Dhaka'],
                        ['id' => 7, 'name' => 'National Board of Revenue', 'type' => 'Government', 'region' => 'Dhaka'],
                        ['id' => 8, 'name' => 'Rapid Action Battalion - RAB', 'type' => 'Police', 'region' => 'Dhaka'],
                        ['id' => 9, 'name' => 'Detective Branch - DB', 'type' => 'Police', 'region' => 'Dhaka'],
                        ['id' => 10, 'name' => 'Traffic Police', 'type' => 'Police', 'region' => 'Dhaka'],
                        ['id' => 11, 'name' => 'Transparency International Bangladesh', 'type' => 'NGO', 'region' => 'Dhaka'],
                        ['id' => 12, 'name' => 'BRAC', 'type' => 'NGO', 'region' => 'Dhaka'],
                        ['id' => 13, 'name' => 'Ain o Salish Kendra', 'type' => 'NGO', 'region' => 'Dhaka'],
                        ['id' => 14, 'name' => 'Bangladesh Legal Aid and Services Trust', 'type' => 'NGO', 'region' => 'Dhaka'],
                        ['id' => 15, 'name' => 'Manusher Jonno Foundation', 'type' => 'NGO', 'region' => 'Dhaka'],
                        ['id' => 16, 'name' => 'Odhikar', 'type' => 'NGO', 'region' => 'Dhaka'],
                        ['id' => 17, 'name' => 'Bangladesh Environmental Lawyers Association', 'type' => 'NGO', 'region' => 'Dhaka'],
                        ['id' => 18, 'name' => 'Dhaka Ahsania Mission', 'type' => 'NGO', 'region' => 'Dhaka'],
                        ['id' => 19, 'name' => 'Proshika', 'type' => 'NGO', 'region' => 'Dhaka'],
                        ['id' => 20, 'name' => 'ActionAid Bangladesh', 'type' => 'NGO', 'region' => 'Dhaka']
                    ];

                    foreach ($predefined_institutions as $institution): 
                        // Get real average scores from database - DYNAMIC RANKINGS
                        $real_scores = getAverageScores($conn, $institution['id']);
                    ?>
                        <div class="institution-card" onclick="viewInstitutionDetails(<?php echo $institution['id']; ?>)">
                            <div class="institution-header">
                                <div>
                                    <div class="institution-name"><?php echo htmlspecialchars($institution['name']); ?></div>
                                    <div class="institution-type"><?php echo htmlspecialchars($institution['type']); ?></div>
                                    <div class="institution-region">📍 <?php echo htmlspecialchars($institution['region']); ?></div>
                                </div>
                            </div>
                            
                            <div class="score-badges">
                                <?php if ($real_scores['avg_corruption'] !== null): ?>
                                    <div class="score-badge corruption-score">
                                        Corruption: <?php echo number_format($real_scores['avg_corruption'], 1); ?>/10
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($real_scores['avg_harassment'] !== null): ?>
                                    <div class="score-badge harassment-score">
                                        Harassment: <?php echo number_format($real_scores['avg_harassment'], 1); ?>/10
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($real_scores['avg_public_hazards'] !== null): ?>
                                    <div class="score-badge hazard-score">
                                        Hazards: <?php echo number_format($real_scores['avg_public_hazards'], 1); ?>/10
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($real_scores['avg_dowry'] !== null): ?>
                                    <div class="score-badge dowry-score">
                                        Dowry: <?php echo number_format($real_scores['avg_dowry'], 1); ?>/10
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($real_scores['avg_antisocial'] !== null): ?>
                                    <div class="score-badge antisocial-score">
                                        Antisocial: <?php echo number_format($real_scores['avg_antisocial'], 1); ?>/10
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($real_scores['total_ratings'] == 0): ?>
                                    <div class="no-ratings-badge">
                                        No ratings yet - be the first to rate!
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div style="margin-top: 15px; color: #94a3b8; font-size: 0.9rem;">
                                📊 <?php echo ($real_scores['total_ratings'] > 0) ? $real_scores['total_ratings'] : 'No'; ?> ratings
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active');
            });
            
            document.getElementById(tabName + '-tab').classList.add('active');
            event.target.classList.add('active');
        }

        const scoreSlider = document.getElementById('score');
        const scoreValue = document.getElementById('scoreValue');

        scoreSlider.addEventListener('input', function() {
            scoreValue.textContent = this.value;
            
            const score = parseInt(this.value);
            if (score < 3) {
                scoreValue.style.color = '#ef4444';
            } else if (score < 7) {
                scoreValue.style.color = '#f59e0b';
            } else {
                scoreValue.style.color = '#22c55e';
            }
        });

        document.getElementById('rankingForm').addEventListener('submit', function(e) {
            const description = document.getElementById('description').value.trim();
            
            if (description.length < 20) {
                e.preventDefault();
                alert('Please provide a more detailed explanation (at least 20 characters).');
                return;
            }
            
            // Show loading states for dynamic update feedback
            document.querySelectorAll('.institution-card').forEach(card => {
                card.classList.add('loading');
            });
        });

        function viewInstitutionDetails(institutionId) {
            console.log('View details for institution:', institutionId);
        }

        document.querySelectorAll('input[name="category"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const slider = document.getElementById('score');
                
                if (this.value === 'Corruption' || this.value === 'Harassment') {
                    slider.style.background = 'linear-gradient(to right, #22c55e 0%, #f59e0b 50%, #ef4444 100%)';
                } else {
                    slider.style.background = 'linear-gradient(to right, #ef4444 0%, #f59e0b 50%, #22c55e 100%)';
                }
            });
        });

        function createParticles(rect) {
            for (let i = 0; i < 15; i++) {
                const particle = document.createElement('div');
                particle.style.cssText = `
                    position: fixed;
                    width: 6px;
                    height: 6px;
                    background: linear-gradient(135deg, #3b82f6, #8b5cf6, #ec4899);
                    border-radius: 50%;
                    pointer-events: none;
                    z-index: 9999;
                    left: ${rect.left + rect.width / 2}px;
                    top: ${rect.top + rect.height / 2}px;
                `;
                document.body.appendChild(particle);

                const angle = (Math.PI * 2 * i) / 15;
                const velocity = 3 + Math.random() * 4;
                const lifetime = 1500;
                const startTime = Date.now();

                const animate = () => {
                    const elapsed = Date.now() - startTime;
                    const progress = elapsed / lifetime;

                    if (progress < 1) {
                        const distance = velocity * elapsed;
                        const currentX = rect.left + rect.width / 2 + Math.cos(angle) * distance;
                        const currentY = rect.top + rect.height / 2 + Math.sin(angle) * distance - (progress * 80);
                        
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

        const textarea = document.getElementById('description');
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 200) + 'px';
        });

        document.querySelectorAll('.form-input, .form-select, .form-textarea').forEach(input => {
            input.addEventListener('focus', function() {
                this.style.transform = 'scale(1.02)';
                this.style.transition = 'all 0.3s ease';
            });
            
            input.addEventListener('blur', function() {
                this.style.transform = 'scale(1)';
            });
        });

        function updateFormProgress() {
            const requiredFields = document.querySelectorAll('#rankingForm [required]');
            const filledFields = Array.from(requiredFields).filter(field => {
                if (field.type === 'radio') {
                    return document.querySelector(`input[name="${field.name}"]:checked`);
                }
                return field.value.trim() !== '';
            });
            
            const progress = (filledFields.length / requiredFields.length) * 100;
            console.log('Form progress:', progress + '%');
        }

        document.querySelectorAll('#rankingForm input, #rankingForm select, #rankingForm textarea').forEach(field => {
            field.addEventListener('input', updateFormProgress);
            field.addEventListener('change', updateFormProgress);
        });

        updateFormProgress();

        function smoothScrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', smoothScrollToTop);
        });

        document.querySelectorAll('.institution-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px) scale(1.02)';
                this.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        const descriptionField = document.getElementById('description');
        const minChars = 20;

        const counterDiv = document.createElement('div');
        counterDiv.style.cssText = `
            color: #94a3b8;
            font-size: 0.8rem;
            margin-top: 5px;
            text-align: right;
        `;
        descriptionField.parentNode.appendChild(counterDiv);

        descriptionField.addEventListener('input', function() {
            const currentLength = this.value.length;
            counterDiv.textContent = `${currentLength} characters (minimum ${minChars})`;
            
            if (currentLength < minChars) {
                counterDiv.style.color = '#ef4444';
                this.style.borderColor = 'rgba(239, 68, 68, 0.5)';
            } else {
                counterDiv.style.color = '#22c55e';
                this.style.borderColor = 'rgba(34, 197, 94, 0.5)';
            }
        });

        descriptionField.dispatchEvent(new Event('input'));

        document.getElementById('rankingForm').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('.submit-btn');
            
            if (!submitBtn.disabled) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<div class="loading-spinner"></div> Submitting...';
                submitBtn.style.opacity = '0.7';
                
                const style = document.createElement('style');
                style.textContent = `
                    .loading-spinner {
                        display: inline-block;
                        width: 16px;
                        height: 16px;
                        border: 2px solid rgba(255, 255, 255, 0.3);
                        border-radius: 50%;
                        border-top-color: white;
                        animation: spin 1s linear infinite;
                        margin-right: 8px;
                    }
                    @keyframes spin {
                        to { transform: rotate(360deg); }
                    }
                `;
                document.head.appendChild(style);
            }
        });

        <?php if ($success): ?>
        setTimeout(() => {
            document.getElementById('rankingForm').reset();
            document.getElementById('scoreValue').textContent = '5';
            document.getElementById('score').value = '5';
            updateFormProgress();
            
            // Auto-switch to view tab to show updated rankings
            setTimeout(() => {
                document.querySelector('[onclick="switchTab(\'view\')"]').click();
            }, 2000);
        }, 1000);
        <?php endif; ?>

        document.addEventListener('keydown', function(e) {
            if (e.altKey && e.key === '1') {
                e.preventDefault();
                document.querySelector('[onclick="switchTab(\'rank\')"]').click();
            }
            
            if (e.altKey && e.key === '2') {
                e.preventDefault();
                document.querySelector('[onclick="switchTab(\'view\')"]').click();
            }
            
            if (e.key === 'Escape') {
                document.activeElement.blur();
            }
        });

        // Enhanced functionality for real-time ranking updates
        function updateRankingsRealTime() {
            console.log('Rankings updated - displaying new averages');
        }
    </script>
</body>
</html>


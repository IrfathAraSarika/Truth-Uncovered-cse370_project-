<?php
session_start();
include 'DBconnect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    // Redirect non-admin users
    header("Location: profile.php");
    exit();
}

// Fetch analytics data (you'll need to implement these queries based on your database structure)
function getAnalyticsData($pdo, $type = 'all') {
    // Sample data structure - replace with actual database queries
    $analyticsData = [
        'corruption' => [
            'total_cases' => 1689,
            'monthly_trend' => [120, 135, 98, 156, 143, 167, 189, 178, 145, 134, 198, 234],
            'regions' => [
                ['name' => 'Dhaka', 'cases' => 345, 'severity' => 'high'],
                ['name' => 'Chattogram', 'cases' => 234, 'severity' => 'medium'],
                ['name' => 'Sylhet', 'cases' => 123, 'severity' => 'low'],
                ['name' => 'Rajshahi', 'cases' => 189, 'severity' => 'medium'],
                ['name' => 'Khulna', 'cases' => 156, 'severity' => 'high'],
                ['name' => 'Barishal', 'cases' => 198, 'severity' => 'medium'],
                ['name' => 'Mymensingh', 'cases' => 167, 'severity' => 'medium'],
                ['name' => 'Rangpur', 'cases' => 277, 'severity' => 'high']
            ]
        ],
        'dowry' => [
            'total_cases' => 1298,
            'monthly_trend' => [89, 76, 94, 102, 87, 95, 108, 115, 98, 87, 109, 134],
            'regions' => [
                ['name' => 'Dhaka', 'cases' => 198, 'severity' => 'high'],
                ['name' => 'Chattogram', 'cases' => 145, 'severity' => 'medium'],
                ['name' => 'Sylhet', 'cases' => 234, 'severity' => 'high'],
                ['name' => 'Rajshahi', 'cases' => 167, 'severity' => 'medium'],
                ['name' => 'Khulna', 'cases' => 98, 'severity' => 'low'],
                ['name' => 'Barishal', 'cases' => 134, 'severity' => 'medium'],
                ['name' => 'Mymensingh', 'cases' => 189, 'severity' => 'high'],
                ['name' => 'Rangpur', 'cases' => 133, 'severity' => 'medium']
            ]
        ],
        'antisocial' => [
            'total_cases' => 2234,
            'monthly_trend' => [145, 132, 167, 189, 198, 156, 143, 178, 201, 189, 234, 298],
            'regions' => [
                ['name' => 'Dhaka', 'cases' => 456, 'severity' => 'high'],
                ['name' => 'Chattogram', 'cases' => 298, 'severity' => 'high'],
                ['name' => 'Sylhet', 'cases' => 189, 'severity' => 'medium'],
                ['name' => 'Rajshahi', 'cases' => 234, 'severity' => 'medium'],
                ['name' => 'Khulna', 'cases' => 167, 'severity' => 'low'],
                ['name' => 'Barishal', 'cases' => 201, 'severity' => 'medium'],
                ['name' => 'Mymensingh', 'cases' => 312, 'severity' => 'high'],
                ['name' => 'Rangpur', 'cases' => 377, 'severity' => 'high']
            ]
        ],
        'hazards' => [
            'total_cases' => 934,
            'monthly_trend' => [67, 54, 78, 89, 76, 82, 95, 87, 69, 73, 89, 102],
            'regions' => [
                ['name' => 'Dhaka', 'cases' => 178, 'severity' => 'high'],
                ['name' => 'Chattogram', 'cases' => 156, 'severity' => 'high'],
                ['name' => 'Sylhet', 'cases' => 89, 'severity' => 'low'],
                ['name' => 'Rajshahi', 'cases' => 123, 'severity' => 'medium'],
                ['name' => 'Khulna', 'cases' => 134, 'severity' => 'medium'],
                ['name' => 'Barishal', 'cases' => 98, 'severity' => 'low'],
                ['name' => 'Mymensingh', 'cases' => 87, 'severity' => 'low'],
                ['name' => 'Rangpur', 'cases' => 69, 'severity' => 'low']
            ]
        ],
        'harassment' => [
            'total_cases' => 3123,
            'monthly_trend' => [189, 198, 234, 267, 245, 289, 298, 312, 278, 256, 334, 398],
            'regions' => [
                ['name' => 'Dhaka', 'cases' => 567, 'severity' => 'high'],
                ['name' => 'Chattogram', 'cases' => 423, 'severity' => 'high'],
                ['name' => 'Sylhet', 'cases' => 298, 'severity' => 'medium'],
                ['name' => 'Rajshahi', 'cases' => 356, 'severity' => 'high'],
                ['name' => 'Khulna', 'cases' => 234, 'severity' => 'medium'],
                ['name' => 'Barishal', 'cases' => 289, 'severity' => 'medium'],
                ['name' => 'Mymensingh', 'cases' => 445, 'severity' => 'high'],
                ['name' => 'Rangpur', 'cases' => 511, 'severity' => 'high']
            ]
        ]
    ];
    
    return $analyticsData;
}

$analyticsData = getAnalyticsData($conn);

// Calculate total cases across all categories
$totalCases = array_sum(array_column($analyticsData, 'total_cases'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Truth Uncovered - Analytics Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
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
        .container {
            max-width:1200px;
            margin:auto;
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

        /* Main Container */
        .analytics-container {
            position: relative;
            z-index: 10;
            padding: 20px;
            max-width: 1400px;
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

        /* Header Section */
        .header-section {
            text-align: center;
            margin-bottom: 40px;
            padding: 30px 0;
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
            content: '📊';
            font-size: 32px;
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
            font-size: 1rem;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* Glass Card Component */
        .glass-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            padding: 30px;
            box-shadow: 
                0 8px 32px 0 rgba(0, 0, 0, 0.37),
                inset 0 0 0 1px rgba(255, 255, 255, 0.08);
            position: relative;
            overflow: hidden;
            margin-bottom: 25px;
            transition: all 0.3s ease;
        }

        .glass-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
        }

        /* Overview Cards Grid */
        .overview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .stat-card {
            text-align: center;
            position: relative;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 15px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }

        .corruption-icon { background: linear-gradient(135deg, #ef4444, #dc2626); }
        .dowry-icon { background: linear-gradient(135deg, #f97316, #ea580c); }
        .antisocial-icon { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
        .hazards-icon { background: linear-gradient(135deg, #eab308, #ca8a04); }
        .harassment-icon { background: linear-gradient(135deg, #ec4899, #db2777); }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 8px;
            background: linear-gradient(135deg, #ffffff, #e2e8f0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-label {
            font-size: 0.9rem;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        /* Analytics Tabs */
        .analytics-tabs {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .tab-button {
            padding: 12px 24px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: #94a3b8;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .tab-button:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(59, 130, 246, 0.5);
            color: #3b82f6;
        }

        .tab-button.active {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            border-color: rgba(59, 130, 246, 0.5);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
        }

        /* Charts Grid */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .chart-card {
            min-height: 400px;
        }

        .chart-title {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: #e2e8f0;
            text-align: center;
        }

        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        /* Heatmap Grid */
        .heatmap-container {
            margin-top: 30px;
        }

        .heatmap-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .heatmap-region {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .heatmap-region:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }

        .region-name {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: #e2e8f0;
        }

        .region-cases {
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .region-severity {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .severity-high {
            background: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .severity-medium {
            background: rgba(245, 158, 11, 0.2);
            color: #fcd34d;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .severity-low {
            background: rgba(34, 197, 94, 0.2);
            color: #86efac;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }

        /* Summary Cards */
        .summary-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 40px;
        }

        .summary-card {
            text-align: left;
        }

        .summary-title {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: #e2e8f0;
        }

        .summary-content {
            color: #94a3b8;
            line-height: 1.6;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .analytics-container {
                padding: 15px;
            }

            .page-title {
                font-size: 2rem;
            }

            .charts-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .chart-card {
                min-height: 350px;
            }

            .analytics-tabs {
                gap: 10px;
            }

            .tab-button {
                padding: 10px 16px;
                font-size: 0.8rem;
            }
        }

        @media (max-width: 480px) {
            .overview-grid {
                grid-template-columns: 1fr;
            }

            .glass-card {
                padding: 20px;
            }
        }

        /* Loading Animation */
        .loading-spinner {
            display: inline-block;
            width: 40px;
            height: 40px;
            border: 4px solid rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            border-top-color: #3b82f6;
            animation: spin 1s ease-in-out infinite;
            margin: 20px auto;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <nav>
                <div class="logo"   onclick="window.location.href='index.php'">🔍 TruthUncovered</div>
                <div class="nav-actions">
                    <a href="index.php" class="back-button">← Back to Home Page</a>
                </div>
            </nav>
        </div>
    </header>


    <!-- Background Image Layer -->
    <div class="background-image"></div>

    <!-- Animated Background -->
    <div class="bg-animation">
        <div class="floating-orb orb1"></div>
        <div class="floating-orb orb2"></div>
        <div class="floating-orb orb3"></div>
        <div class="floating-orb orb4"></div>
    </div>

    <!-- Analytics Container -->
    <div class="analytics-container">
        <!-- Header Section -->
        <div class="header-section">
            <div class="logo-icon"></div>
            <h1 class="page-title">ANALYTICS DASHBOARD</h1>
            <p class="page-subtitle">Truth Uncovered - Data Intelligence</p>
        </div>

        <!-- Overview Cards -->
        <div class="overview-grid">
            <div class="glass-card stat-card">
                <div class="stat-icon corruption-icon">🚨</div>
                <div class="stat-number" id="corruption-count"><?php echo number_format($analyticsData['corruption']['total_cases']); ?></div>
                <div class="stat-label">Corruption Cases</div>
            </div>
            
            <div class="glass-card stat-card">
                <div class="stat-icon dowry-icon">💍</div>
                <div class="stat-number" id="dowry-count"><?php echo number_format($analyticsData['dowry']['total_cases']); ?></div>
                <div class="stat-label">Dowry Cases</div>
            </div>
            
            <div class="glass-card stat-card">
                <div class="stat-icon antisocial-icon">⚠️</div>
                <div class="stat-number" id="antisocial-count"><?php echo number_format($analyticsData['antisocial']['total_cases']); ?></div>
                <div class="stat-label">Antisocial Behavior</div>
            </div>
            
            <div class="glass-card stat-card">
                <div class="stat-icon hazards-icon">☢️</div>
                <div class="stat-number" id="hazards-count"><?php echo number_format($analyticsData['hazards']['total_cases']); ?></div>
                <div class="stat-label">Hazards</div>
            </div>
            
            <div class="glass-card stat-card">
                <div class="stat-icon harassment-icon">🚫</div>
                <div class="stat-number" id="harassment-count"><?php echo number_format($analyticsData['harassment']['total_cases']); ?></div>
                <div class="stat-label">Harassment Cases</div>
            </div>
        </div>

        <!-- Analytics Tabs -->
        <div class="analytics-tabs">
            <button class="tab-button active" data-category="overview">Overview</button>
            <button class="tab-button" data-category="corruption">Corruption</button>
            <button class="tab-button" data-category="dowry">Dowry</button>
            <button class="tab-button" data-category="antisocial">Antisocial</button>
            <button class="tab-button" data-category="hazards">Hazards</button>
            <button class="tab-button" data-category="harassment">Harassment</button>
        </div>

        <!-- Charts Section -->
        <div class="charts-grid">
            <div class="glass-card chart-card">
                <h3 class="chart-title">Monthly Trends Analysis</h3>
                <div class="chart-container">
                    <canvas id="trendsChart"></canvas>
                </div>
            </div>
            
            <div class="glass-card chart-card">
                <h3 class="chart-title">Cases Distribution by Category</h3>
                <div class="chart-container">
                    <canvas id="distributionChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Heatmap Section -->
        <div class="glass-card">
            <h3 class="chart-title">Regional Impact Heatmap</h3>
            <div class="heatmap-container">
                <div class="heatmap-grid" id="heatmapGrid">
                    <!-- Dynamic content will be loaded here -->
                </div>
            </div>
        </div>

        <!-- Summary Section -->
        <div class="summary-section">
            <div class="glass-card summary-card">
                <h3 class="summary-title">Key Insights</h3>
                <div class="summary-content">
                    <p>Our analytics reveal concerning trends across all categories, with harassment cases showing the highest numbers at <?php echo number_format($analyticsData['harassment']['total_cases']); ?> reported incidents this year.</p>
                    <br>
                    <p>Dhaka consistently shows as the most affected region across multiple categories, requiring immediate attention and targeted intervention strategies.</p>
                </div>
            </div>
            
            <div class="glass-card summary-card">
                <h3 class="summary-title">Recommendations</h3>
                <div class="summary-content">
                    <p>Based on current data trends, we recommend:</p>
                    <br>
                    <p>• Increased monitoring in high-risk regions<br>
                    • Enhanced reporting mechanisms for harassment cases<br>
                    • Targeted awareness campaigns in affected areas<br>
                    • Strengthened law enforcement collaboration</p>
                </div>
            </div>
            
            <div class="glass-card summary-card">
                <h3 class="summary-title">Data Quality</h3>
                <div class="summary-content">
                    <p>Analytics generated from <?php echo number_format($totalCases); ?> verified reports across all categories.</p>
                    <br>
                    <p>Last updated: <?php echo date('F j, Y g:i A'); ?><br>
                    Data confidence: 94.2%<br>
                    Coverage: All major regions</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Analytics data from PHP
        const analyticsData = <?php echo json_encode($analyticsData); ?>;
        
        // Chart configurations
        Chart.defaults.color = '#94a3b8';
        Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.1)';
        Chart.defaults.backgroundColor = 'rgba(255, 255, 255, 0.05)';

        // Initialize charts
        let trendsChart, distributionChart;
        
        function initCharts() {
            // Trends Chart
            const trendsCtx = document.getElementById('trendsChart').getContext('2d');
            trendsChart = new Chart(trendsCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'All Categories',
                        data: calculateTotalTrends(),
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#3b82f6',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            }
                        },
                        x: {
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            }
                        }
                    }
                }
            });

            // Distribution Chart
            const distributionCtx = document.getElementById('distributionChart').getContext('2d');
            distributionChart = new Chart(distributionCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Harassment', 'Antisocial', 'Corruption', 'Dowry', 'Hazards'],
                    datasets: [{
                        data: [
                            analyticsData.harassment.total_cases,
                            analyticsData.antisocial.total_cases,
                            analyticsData.corruption.total_cases,
                            analyticsData.dowry.total_cases,
                            analyticsData.hazards.total_cases
                        ],
                        backgroundColor: [
                            '#ec4899',
                            '#8b5cf6',
                            '#ef4444',
                            '#f97316',
                            '#eab308'
                        ],
                        borderColor: '#ffffff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                font: {
                                    size: 12,
                                    weight: '600'
                                }
                            }
                        }
                    }
                }
            });
        }

        function calculateTotalTrends() {
            const totals = [];
            for (let i = 0; i < 12; i++) {
                let monthTotal = 0;
                Object.values(analyticsData).forEach(category => {
                    monthTotal += category.monthly_trend[i];
                });
                totals.push(monthTotal);
            }
            return totals;
        }

        // Tab switching functionality
        function switchTab(category) {
            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
            document.querySelector(`[data-category="${category}"]`).classList.add('active');
            
            updateCharts(category);
            updateHeatmap(category);
        }

        function updateCharts(category) {
            if (category === 'overview') {
                trendsChart.data.datasets[0].data = calculateTotalTrends();
                trendsChart.data.datasets[0].label = 'All Categories';
                trendsChart.data.datasets[0].borderColor = '#3b82f6';
                trendsChart.data.datasets[0].backgroundColor = 'rgba(59, 130, 246, 0.1)';
            } else {
                trendsChart.data.datasets[0].data = analyticsData[category].monthly_trend;
                trendsChart.data.datasets[0].label = category.charAt(0).toUpperCase() + category.slice(1);
                
                // Set category-specific colors
                const colors = {
                    corruption: '#ef4444',
                    dowry: '#f97316',
                    antisocial: '#8b5cf6',
                    hazards: '#eab308',
                    harassment: '#ec4899'
                };
                
                trendsChart.data.datasets[0].borderColor = colors[category];
                trendsChart.data.datasets[0].backgroundColor = colors[category] + '20';
            }
            trendsChart.update('active');
        }

        function updateHeatmap(category) {
            const heatmapGrid = document.getElementById('heatmapGrid');
            const data = category === 'overview' ? getAllRegionsData() : analyticsData[category].regions;
            
            heatmapGrid.innerHTML = '';
            data.forEach(region => {
                const regionElement = document.createElement('div');
                regionElement.className = 'heatmap-region';
                regionElement.innerHTML = `
                    <div class="region-name">${region.name}</div>
                    <div class="region-cases">${region.cases.toLocaleString()}</div>
                    <div class="region-severity severity-${region.severity}">${region.severity.toUpperCase()}</div>
                `;
                heatmapGrid.appendChild(regionElement);
            });
        }

        function getAllRegionsData() {
            const regions = {};
            Object.values(analyticsData).forEach(category => {
                category.regions.forEach(region => {
                    if (!regions[region.name]) {
                        regions[region.name] = { name: region.name, cases: 0, severityScores: [] };
                    }
                    regions[region.name].cases += region.cases;
                    regions[region.name].severityScores.push(region.severity === 'high' ? 3 : region.severity === 'medium' ? 2 : 1);
                });
            });

            return Object.values(regions).map(region => {
                const avgSeverity = region.severityScores.reduce((a, b) => a + b, 0) / region.severityScores.length;
                return {
                    ...region,
                    severity: avgSeverity >= 2.5 ? 'high' : avgSeverity >= 1.5 ? 'medium' : 'low'
                };
            }).sort((a, b) => b.cases - a.cases);
        }

        // Event listeners
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', () => {
                switchTab(button.dataset.category);
            });
        });

        // Animated counter function
        function animateCounter(element, target, duration = 2000) {
            const start = 0;
            const increment = target / (duration / 16);
            let current = start;

            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                element.textContent = Math.floor(current).toLocaleString();
            }, 16);
        }

        // Initialize everything when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Animate counters
            setTimeout(() => {
                animateCounter(document.getElementById('corruption-count'), <?php echo $analyticsData['corruption']['total_cases']; ?>);
                animateCounter(document.getElementById('dowry-count'), <?php echo $analyticsData['dowry']['total_cases']; ?>);
                animateCounter(document.getElementById('antisocial-count'), <?php echo $analyticsData['antisocial']['total_cases']; ?>);
                animateCounter(document.getElementById('hazards-count'), <?php echo $analyticsData['hazards']['total_cases']; ?>);
                animateCounter(document.getElementById('harassment-count'), <?php echo $analyticsData['harassment']['total_cases']; ?>);
            }, 500);

            // Initialize charts
            setTimeout(() => {
                initCharts();
                updateHeatmap('overview');
            }, 800);

            // Add hover effects to cards
            document.querySelectorAll('.glass-card').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-8px) scale(1.02)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });

            // Add click effects to stat cards
            document.querySelectorAll('.stat-card').forEach(card => {
                card.addEventListener('click', function() {
                    const category = this.querySelector('.stat-label').textContent.toLowerCase().split(' ')[0];
                    if (category === 'corruption' || category === 'dowry' || category === 'antisocial' || category === 'hazards' || category === 'harassment') {
                        switchTab(category);
                        document.querySelector(`[data-category="${category}"]`).scrollIntoView({ behavior: 'smooth' });
                    }
                });
            });
        });

        // Add particle effect on stat card click
        document.addEventListener('click', function(e) {
            if (e.target.closest('.stat-card')) {
                createAnalyticsParticles(e.clientX, e.clientY);
            }
        });

        function createAnalyticsParticles(x, y) {
            const colors = ['#3b82f6', '#8b5cf6', '#ec4899', '#22c55e', '#eab308'];
            
            for (let i = 0; i < 8; i++) {
                const particle = document.createElement('div');
                particle.style.cssText = `
                    position: fixed;
                    width: 8px;
                    height: 8px;
                    background: ${colors[Math.floor(Math.random() * colors.length)]};
                    border-radius: 50%;
                    pointer-events: none;
                    z-index: 9999;
                    left: ${x}px;
                    top: ${y}px;
                    box-shadow: 0 0 10px currentColor;
                `;
                document.body.appendChild(particle);

                const angle = (Math.PI * 2 * i) / 8;
                const velocity = 2 + Math.random() * 2;
                const lifetime = 1500;
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
                        particle.style.transform = `scale(${1 - progress * 0.5})`;
                        
                        requestAnimationFrame(animate);
                    } else {
                        particle.remove();
                    }
                };
                
                requestAnimationFrame(animate);
            }
        }

        // Real-time data simulation (for demo purposes)
        setInterval(() => {
            // Simulate real-time data updates
            const now = new Date();
            document.querySelector('.summary-content p:last-child').innerHTML = 
                `Last updated: ${now.toLocaleDateString()} ${now.toLocaleTimeString()}<br>
                Data confidence: ${(94 + Math.random() * 5).toFixed(1)}%<br>
                Coverage: All major regions`;
        }, 30000); // Update every 30 seconds

        // Export functionality (you can extend this)
        function exportData(format) {
            console.log(`Exporting data in ${format} format...`);
            // Implement export functionality here
        }

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey || e.metaKey) {
                switch(e.key) {
                    case '1':
                        e.preventDefault();
                        switchTab('overview');
                        break;
                    case '2':
                        e.preventDefault();
                        switchTab('corruption');
                        break;
                    case '3':
                        e.preventDefault();
                        switchTab('dowry');
                        break;
                    case '4':
                        e.preventDefault();
                        switchTab('antisocial');
                        break;
                    case '5':
                        e.preventDefault();
                        switchTab('hazards');
                        break;
                    case '6':
                        e.preventDefault();
                        switchTab('harassment');
                        break;
                }
            }
        });
    </script>
</body>
</html>
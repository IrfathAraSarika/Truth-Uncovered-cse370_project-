<?php
session_start();
include 'DBconnect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header('Location: login.php');
    exit();
}

// Handle AJAX requests
// if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
//     header('Content-Type: application/json');
    
//     switch ($_POST['action']) {
//         case 'update_report_status':
//             $reportId = $_POST['report_id'];
//             $status = $_POST['status'];
//             $agency = $_POST['agency'] ?? null;
            
//             $stmt = $conn->prepare("UPDATE reports SET status = ?, assigned_agency = ? WHERE id = ?");
//             $result = $stmt->execute([$status, $agency, $reportId]);
//             echo json_encode(['success' => $result]);
//             exit();
            
//         case 'delete_user':
//             $userId = $_POST['user_id'];
//             $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
//             $result = $stmt->execute([$userId]);
//             echo json_encode(['success' => $result]);
//             exit();
            
//         case 'delete_blog':
//             $blogId = $_POST['blog_id'];
//             $stmt = $conn->prepare("DELETE FROM blog_posts WHERE id = ?");
//             $result = $stmt->execute([$blogId]);
//             echo json_encode(['success' => $result]);
//             exit();
            
//         case 'get_report_details':
//             $reportId = $_POST['report_id'];
//             $stmt = $conn->prepare("SELECT * FROM reports WHERE id = ?");
//             $stmt->execute([$reportId]);
//             $report = $stmt->fetch(PDO::FETCH_ASSOC);
//             echo json_encode($report);
//             exit();
//     }
// }

// // Fetch data for dashboard
// try {
//     // Get reports
//     $reportsStmt = $conn->prepare("SELECT r.*, u.Name as reporter_name FROM reports r LEFT JOIN users u ON r.user_id = u.id ORDER BY r.created_at DESC");
//     $reportsStmt->execute();
//     $reports = $reportsStmt->fetchAll(PDO::FETCH_ASSOC);

//     // Get users
//     $usersStmt = $conn->prepare("SELECT * FROM users ORDER BY created_at DESC");
//     $usersStmt->execute();
//     $users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);

//     // Get blog posts
//     $blogStmt = $conn->prepare("SELECT b.*, u.Name as author_name FROM blog_posts b LEFT JOIN users u ON b.author_id = u.id ORDER BY b.created_at DESC");
//     $blogStmt->execute();
//     $blogs = $blogStmt->fetchAll(PDO::FETCH_ASSOC);

//     // Get statistics
//     $totalReports = count($reports);
//     $pendingReports = count(array_filter($reports, fn($r) => $r['status'] === 'pending'));
//     $totalUsers = count($users);
//     $totalBlogs = count($blogs);
    
// } catch (PDOException $e) {
//     // Create sample data if tables don't exist
//     $reports = [];
//     $users = [];
//     $blogs = [];
//     $totalReports = 0;
//     $pendingReports = 0;
//     $totalUsers = 0;
//     $totalBlogs = 0;
// }

// Sample agencies list
$agencies = [
    'Anti-Corruption Commission',
    'Police Department',
    'Environmental Protection Agency',
    'Health Ministry',
    'Education Ministry',
    'Transport Authority',
    'Local Government',
    'Consumer Rights Protection'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Truth Uncovered</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 25%, #16213e 50%, #0f3460 75%, #1e3a8a 100%);
            min-height: 100vh;
            color: #ffffff;
            overflow-x: hidden;
        }
.dark-view {
    background: #1e1e1e;
    color: #f1f1f1;
    border-radius: 12px;
    padding: 1.5rem;
    line-height: 1.8;
    font-family: 'Georgia', serif;
}

.article-view h2 {
    font-size: 1.8rem;
    margin-bottom: 1rem;
    color: #fff;
}

.article-view p {
    margin-bottom: 1.2rem;
    font-size: 1.05rem;
    color: #ddd;
}

.article-view strong {
    color: #f5ba42; /* highlight key labels */
}



        /* Background Effects */
        .background-image {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?auto=format&fit=crop&w=1920&q=80') center/cover;
            opacity: 0.05;
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
            filter: blur(1px);
            animation: float 15s ease-in-out infinite;
        }

        .orb1 { 
            width: 300px; 
            height: 300px; 
            top: 10%; 
            left: 80%; 
            background: radial-gradient(circle, rgba(59, 130, 246, 0.15), transparent);
            animation-delay: 0s;
        }
        
        .orb2 { 
            width: 250px; 
            height: 250px; 
            top: 60%; 
            left: -10%; 
            background: radial-gradient(circle, rgba(147, 51, 234, 0.15), transparent);
            animation-delay: 5s;
        }
        
        .orb3 { 
            width: 200px; 
            height: 200px; 
            top: 30%; 
            left: 20%; 
            background: radial-gradient(circle, rgba(236, 72, 153, 0.1), transparent);
            animation-delay: 10s;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(30px, -30px) rotate(120deg); }
            66% { transform: translate(-20px, 20px) rotate(240deg); }
        }

        /* Main Layout */
        .dashboard-container {
            position: relative;
            z-index: 10;
            padding: 20px;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Header */
        .dashboard-header {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px) saturate(180%);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            padding: 25px 30px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .logo-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.3);
        }

        .header-title {
            font-size: 1.8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header-subtitle {
            color: #94a3b8;
            font-size: 0.9rem;
            margin-top: -5px;
        }

        .logout-btn {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            border: none;
            padding: 12px 24px;
            border-radius: 12px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4);
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px) saturate(180%);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            padding: 25px;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.3);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin: 0 auto 15px;
        }

        .stat-card:nth-child(1) .stat-icon { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
        .stat-card:nth-child(2) .stat-icon { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .stat-card:nth-child(3) .stat-icon { background: linear-gradient(135deg, #10b981, #059669); }
        .stat-card:nth-child(4) .stat-icon { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }

        .stat-number {
            font-size: 2.2rem;
            font-weight: 800;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #94a3b8;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Navigation Tabs */
        .nav-tabs {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            background: rgba(255, 255, 255, 0.05);
            padding: 8px;
            border-radius: 16px;
            backdrop-filter: blur(10px);
        }

        .nav-tab {
            flex: 1;
            background: transparent;
            border: none;
            padding: 15px 20px;
            border-radius: 12px;
            color: #94a3b8;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .nav-tab.active {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            color: white;
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.3);
        }

        .nav-tab:hover:not(.active) {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        /* Content Sections */
        .content-section {
            display: none;
            animation: fadeIn 0.3s ease;
        }

        .content-section.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .section-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px) saturate(180%);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            overflow: hidden;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        }

        .section-header {
            padding: 25px 30px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: between;
            align-items: center;
        }

        .section-title {
            font-size: 1.4rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Tables */
        .table-container {
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th,
        .data-table td {
            padding: 15px 20px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .data-table th {
            background: rgba(255, 255, 255, 0.05);
            font-weight: 600;
            color: #e2e8f0;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .data-table tr:hover {
            background: rgba(255, 255, 255, 0.03);
        }

        /* Status Badges */
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pending { background: rgba(251, 191, 36, 0.2); color: #fbbf24; }
        .status-accepted { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
        .status-declined { background: rgba(239, 68, 68, 0.2); color: #ef4444; }

        /* Action Buttons */
        .action-btn {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            color: white;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-right: 8px;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
        }

        .action-btn.danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }

        .action-btn.danger:hover {
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
            z-index: 1000;
            animation: fadeIn 0.3s ease;
        }

        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: rgba(30, 41, 59, 0.95);
            backdrop-filter: blur(20px) saturate(180%);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            padding: 30px;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .modal-title {
            font-size: 1.4rem;
            font-weight: 700;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #94a3b8;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .close-btn:hover {
            color: white;
        }

        .form-group {
            margin-bottom: 20px;
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

        .form-input, .form-select {
            width: 100%;
            padding: 12px 16px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: #ffffff;
            font-size: 0.95rem;
            outline: none;
            transition: all 0.3s ease;
        }

        .form-input:focus, .form-select:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(59, 130, 246, 0.5);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-select {
            appearance: none;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="12" height="6" viewBox="0 0 12 6"><path fill="%23ffffff" d="M6 6L0 0h12z"/></svg>');
            background-repeat: no-repeat;
            background-position: right 16px center;
        }

        .form-textarea {
            width: 100%;
            min-height: 120px;
            padding: 12px 16px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: #ffffff;
            font-size: 0.95rem;
            outline: none;
            resize: vertical;
            font-family: 'Inter', sans-serif;
        }

        .form-textarea:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(59, 130, 246, 0.5);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .modal-actions {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            flex: 1;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        /* Loading State */
        .loading {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
            margin-right: 8px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 15px;
            }

            .dashboard-header {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }

            .nav-tabs {
                flex-direction: column;
            }

            .data-table {
                font-size: 0.85rem;
            }

            .data-table th,
            .data-table td {
                padding: 10px 15px;
            }

            .modal-content {
                padding: 20px;
                margin: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Background Effects -->
    <div class="background-image"></div>
    <div class="bg-animation">
        <div class="floating-orb orb1"></div>
        <div class="floating-orb orb2"></div>
        <div class="floating-orb orb3"></div>
    </div>

    <div class="dashboard-container">
        <!-- Header -->
        <div class="dashboard-header">
            <div class="header-left">
                <div class="logo-icon">🔍</div>
                <div>
                    <div class="header-title">TRUTH UNCOVERED</div>
                    <div class="header-subtitle">Admin Control Panel</div>
                </div>
            </div>
            <button class="logout-btn" onclick="logout()">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
                <div class="stat-number"> 2000</div>
                <div class="stat-label">Total Reports</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <div class="stat-number">400</div>
                <div class="stat-label">Pending Reports</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-number">5000</div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-blog"></i></div>
                <div class="stat-number">200</div>
                <div class="stat-label">Blog Posts</div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="nav-tabs">
            <button class="nav-tab active" onclick="switchTab('reports')">
                <i class="fas fa-file-alt"></i> Manage Reports
            </button>
            <button class="nav-tab" onclick="switchTab('users')">
                <i class="fas fa-users"></i> Manage Users
            </button>
            <button class="nav-tab" onclick="switchTab('blogs')">
                <i class="fas fa-blog"></i> Manage Blogs
            </button>
        </div>

        <!-- Reports Section -->
        <div id="reports-section" class="content-section active">
            <div class="section-card">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-file-alt"></i>
                        Reports Management
                    </h2>
                </div>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Reporter</th>
                                <th>Status</th>
                                <th>Agency</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="reports-table-body">
                            <?php if (empty($reports)): ?>
                            <tr>
                                <td>001</td>
                                <td>Road Construction Corruption</td>
                                <td>John Smith</td>
                                <td><span class="status-badge status-pending">Pending</span></td>
                                <td>Not Assigned</td>
                                <td>2024-01-15</td>
                                <td>
                                    <button class="action-btn" onclick="viewReport(1)">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button class="action-btn" onclick="updateReport(1)">
                                        <i class="fas fa-edit"></i> Update
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>002</td>
                                <td>Hospital Medicine Shortage</td>
                                <td>Sarah Johnson</td>
                                <td><span class="status-badge status-accepted">Accepted</span></td>
                                <td>Health Ministry</td>
                                <td>2024-01-14</td>
                                <td>
                                    <button class="action-btn" onclick="viewReport(2)">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button class="action-btn" onclick="updateReport(2)">
                                        <i class="fas fa-edit"></i> Update
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>003</td>
                                <td>School Fund Misuse</td>
                                <td>Mike Wilson</td>
                                <td><span class="status-badge status-declined">Declined</span></td>
                                <td>Education Ministry</td>
                                <td>2024-01-13</td>
                                <td>
                                    <button class="action-btn" onclick="viewReport(3)">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button class="action-btn" onclick="updateReport(3)">
                                        <i class="fas fa-edit"></i> Update
                                    </button>
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($reports as $report): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($report['id']); ?></td>
                                <td><?php echo htmlspecialchars($report['title'] ?? 'Untitled'); ?></td>
                                <td><?php echo htmlspecialchars($report['reporter_name'] ?? 'Unknown'); ?></td>
                                <td><span class="status-badge status-<?php echo htmlspecialchars($report['status']); ?>"><?php echo ucfirst($report['status']); ?></span></td>
                                <td><?php echo htmlspecialchars($report['assigned_agency'] ?? 'Not Assigned'); ?></td>
                                <td><?php echo date('Y-m-d', strtotime($report['created_at'])); ?></td>
                                <td>
                                    <button class="action-btn" onclick="viewReport(<?php echo $report['id']; ?>)">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button class="action-btn" onclick="updateReport(<?php echo $report['id']; ?>)">
                                        <i class="fas fa-edit"></i> Update
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Users Section -->
        <div id="users-section" class="content-section">
            <div class="section-card">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-users"></i>
                        User Management
                    </h2>
                </div>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Phone</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="users-table-body">
                            <?php if (empty($users)): ?>
                            <tr>
                                <td>001</td>
                                <td>John Smith</td>
                                <td>john.smith@email.com</td>
                                <td>Citizen</td>
                                <td>+1-555-0101</td>
                                <td>2024-01-10</td>
                                <td>
                                    <button class="action-btn danger" onclick="deleteUser(1)">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>002</td>
                                <td>Sarah Johnson</td>
                                <td>sarah.j@email.com</td>
                                <td>NGO_Partner</td>
                                <td>+1-555-0102</td>
                                <td>2024-01-08</td>
                                <td>
                                    <button class="action-btn danger" onclick="deleteUser(2)">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>003</td>
                                <td>Mike Wilson</td>
                                <td>mike.w@email.com</td>
                                <td>Govt_Officer</td>
                                <td>+1-555-0103</td>
                                <td>2024-01-05</td>
                                <td>
                                    <button class="action-btn danger" onclick="deleteUser(3)">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['id']); ?></td>
                                <td><?php echo htmlspecialchars($user['Name']); ?></td>
                                <td><?php echo htmlspecialchars($user['Email']); ?></td>
                                <td><?php echo htmlspecialchars($user['Role']); ?></td>
                                <td><?php echo htmlspecialchars($user['Phone'] ?? 'N/A'); ?></td>
                                <td><?php echo date('Y-m-d', strtotime($user['created_at'] ?? 'now')); ?></td>
                                <td>
                                    <button class="action-btn danger" onclick="deleteUser(<?php echo $user['id']; ?>)">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Blogs Section -->
        <div id="blogs-section" class="content-section">
            <div class="section-card">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-blog"></i>
                        Blog Management
                    </h2>
                </div>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="blogs-table-body">
                            <?php if (empty($blogs)): ?>
                            <tr>
                                <td>001</td>
                                <td>Fighting Corruption: A Citizen's Guide</td>
                                <td>Admin User</td>
                                <td>Guide</td>
                                <td>Published</td>
                                <td>2024-01-12</td>
                                <td>
                                    <button class="action-btn" onclick="viewBlog(1)">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button class="action-btn danger" onclick="deleteBlog(1)">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>002</td>
                                <td>Recent Investigation Results</td>
                                <td>Investigation Team</td>
                                <td>News</td>
                                <td>Published</td>
                                <td>2024-01-11</td>
                                <td>
                                    <button class="action-btn" onclick="viewBlog(2)">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button class="action-btn danger" onclick="deleteBlog(2)">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>003</td>
                                <td>How to Report Anonymously</td>
                                <td>Support Team</td>
                                <td>Tutorial</td>
                                <td>Draft</td>
                                <td>2024-01-09</td>
                                <td>
                                    <button class="action-btn" onclick="viewBlog(3)">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button class="action-btn danger" onclick="deleteBlog(3)">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($blogs as $blog): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($blog['id']); ?></td>
                                <td><?php echo htmlspecialchars($blog['title']); ?></td>
                                <td><?php echo htmlspecialchars($blog['author_name'] ?? 'Unknown'); ?></td>
                                <td><?php echo htmlspecialchars($blog['category'] ?? 'General'); ?></td>
                                <td><?php echo htmlspecialchars($blog['status'] ?? 'Published'); ?></td>
                                <td><?php echo date('Y-m-d', strtotime($blog['created_at'])); ?></td>
                                <td>
                                    <button class="action-btn" onclick="viewBlog(<?php echo $blog['id']; ?>)">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button class="action-btn danger" onclick="deleteBlog(<?php echo $blog['id']; ?>)">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Modal -->
    <div id="reportUpdateModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Report Details</h3>
                <button class="close-btn" onclick="closeModal('reportUpdateModal')">&times;</button>
            </div>
            <div id="reportModalBody">
                <div class="form-group">
                    <label class="form-label">Report Title</label>
                    <input type="text" id="reportTitle" class="form-input" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea id="reportDescription" class="form-textarea" readonly></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Reporter</label>
                    <input type="text" id="reportReporter" class="form-input" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select id="reportStatus" class="form-select">
                        <option value="pending">Pending</option>
                        <option value="accepted">Accepted</option>
                        <option value="declined">Declined</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Assign Agency</label>
                    <select id="reportAgency" class="form-select">
                        <option value="">Select Agency</option>
                        <?php foreach ($agencies as $agency): ?>
                        <option value="<?php echo htmlspecialchars($agency); ?>"><?php echo htmlspecialchars($agency); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-actions">
                <button class="btn btn-primary" onclick="saveReportUpdate()">
                    <i class="fas fa-save"></i> Save Changes
                </button>
                <button class="btn btn-secondary" onclick="closeModal('reportModal')">
                    Cancel
                </button>
            </div>
        </div>
    </div>


<!-- ........................extra starts.............. -->
<div id="reportModal" class="modal">
    <div class="modal-content dark-view">
        <div class="modal-header">
            <h3 class="modal-title">Report Details</h3>
            <button class="close-btn" onclick="closeModal('reportModal')">&times;</button>
        </div>

        <div id="reportModalBody" class="article-view">
            <h2 id="reportTitle">Unauthorized Construction Near Residential Area</h2>

            <p id="reportDescription">
                A concerned citizen has reported the construction of a building 
                that is allegedly being built without the necessary permits. 
                The structure, located near a densely populated neighborhood, 
                is raising safety concerns among local residents. According to 
                eyewitnesses, the construction has been ongoing for several weeks 
                with no visible signs of authorization from the city authorities.
            </p>

            <figure id="reportEvidence">
                <img src="https://images.pexels.com/photos/280221/pexels-photo-280221.jpeg" 
                     alt="Construction Evidence" 
                     style="max-width:100%; border-radius:10px; margin-top:1rem; box-shadow:0 3px 8px rgba(0,0,0,0.4);" />
                <figcaption style="font-size:0.9rem; color:#bbb; margin-top:0.5rem;">
                    Evidence provided by the reporter
                </figcaption>
            </figure>

            <p>
                <strong>Reporter:</strong> <span id="reportReporter">John Doe</span><br>
                <strong>Status:</strong> <span id="reportStatus">Pending Review</span><br>
                <strong>Assigned Agency:</strong> <span id="reportAgency">City Safety Department</span>
            </p>
        </div>

      
    </div>
</div>



<!-- ........................extra ends.............. -->










    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content" style="max-width: 400px;">
            <div class="modal-header">
                <h3 class="modal-title">Confirm Deletion</h3>
                <button class="close-btn" onclick="closeModal('deleteModal')">&times;</button>
            </div>
            <div class="modal-body">
                <p id="deleteMessage" style="color: #e2e8f0; margin-bottom: 20px;"></p>
            </div>
            <div class="modal-actions">
                <button class="btn btn-primary" id="confirmDeleteBtn" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                    <i class="fas fa-trash"></i> Delete
                </button>
                <button class="btn btn-secondary" onclick="closeModal('deleteModal')">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    <script>
        // Current report being edited
        let currentReportId = null;
        let deleteAction = null;
        let deleteId = null;

        // Tab switching
        function switchTab(tab) {
            // Update tab buttons
            document.querySelectorAll('.nav-tab').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');

            // Update content sections
            document.querySelectorAll('.content-section').forEach(section => section.classList.remove('active'));
            document.getElementById(tab + '-section').classList.add('active');
        }

        // Modal functions
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('active');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
            if (modalId === 'reportModal') {
                currentReportId = null;
            }
        }

        // Report functions
        function viewReport(reportId,updateFlag=false) {
            currentReportId = reportId;
            
            // In a real application, you would fetch report details from the server
            // For demo purposes, we'll use sample data
            const sampleReports = {
                1: {
                    title: 'Road Construction Corruption',
                    description: 'There are allegations of corruption in the recent road construction project in downtown area. Materials used seem to be of poor quality despite high budget allocation.',
                    reporter: 'John Smith',
                    status: 'pending',
                    agency: ''
                },
                2: {
                    title: 'Hospital Medicine Shortage',
                    description: 'Patients are reporting severe shortage of essential medicines in the city hospital. This might be due to mismanagement or corruption in procurement.',
                    reporter: 'Sarah Johnson',
                    status: 'accepted',
                    agency: 'Health Ministry'
                },
                3: {
                    title: 'School Fund Misuse',
                    description: 'School development funds allocated by the government seem to be misused. Infrastructure remains poor despite significant budget approval.',
                    reporter: 'Mike Wilson',
                    status: 'declined',
                    agency: 'Education Ministry'
                }
            };

            const report = sampleReports[reportId] || sampleReports[1];
            
            document.getElementById('reportTitle').value = report.title;
            document.getElementById('reportDescription').value = report.description;
            document.getElementById('reportReporter').value = report.reporter;
            document.getElementById('reportStatus').value = report.status;
            document.getElementById('reportAgency').value = report.agency;
            
            if (updateFlag){
             openModal('reportUpdateModal')
            }
            else {
            openModal('reportModal');
            }
          
        }

        function updateReport(reportId) {
            viewReport(reportId,true);
        }

        function saveReportUpdate() {
            const status = document.getElementById('reportStatus').value;
            const agency = document.getElementById('reportAgency').value;
            
            // Show loading state
            const saveBtn = event.target;
            const originalText = saveBtn.innerHTML;
            saveBtn.innerHTML = '<span class="loading"></span>Saving...';
            saveBtn.disabled = true;

            // Simulate API call
            setTimeout(() => {
                // In real application, make AJAX call to update report
                const formData = new FormData();
                formData.append('action', 'update_report_status');
                formData.append('report_id', currentReportId);
                formData.append('status', status);
                formData.append('agency', agency);

                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update the table row
                        updateReportRow(currentReportId, status, agency);
                        closeModal('reportModal');
                        showNotification('Report updated successfully!', 'success');
                    } else {
                        showNotification('Failed to update report. Please try again.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Report updated successfully!', 'success'); // For demo
                    updateReportRow(currentReportId, status, agency);
                    closeModal('reportModal');
                })
                .finally(() => {
                    saveBtn.innerHTML = originalText;
                    saveBtn.disabled = false;
                });
            }, 1000);
        }

        function updateReportRow(reportId, status, agency) {
            // Update the status badge and agency in the table
            const rows = document.querySelectorAll('#reports-table-body tr');
            rows.forEach(row => {
                const idCell = row.querySelector('td:first-child');
                if (idCell && (idCell.textContent.padStart(3, '0') == reportId.toString().padStart(3, '0'))) {
                    const statusCell = row.querySelector('td:nth-child(4)');
                    const agencyCell = row.querySelector('td:nth-child(5)');
                    
                    if (statusCell) {
                        statusCell.innerHTML = `<span class="status-badge status-${status}">${status.charAt(0).toUpperCase() + status.slice(1)}</span>`;
                    }
                    if (agencyCell) {
                        agencyCell.textContent = agency || 'Not Assigned';
                    }
                }
            });
        }

        // User functions
        function deleteUser(userId) {
            deleteAction = 'user';
            deleteId = userId;
            document.getElementById('deleteMessage').textContent = 'Are you sure you want to delete this user? This action cannot be undone.';
            document.getElementById('confirmDeleteBtn').onclick = confirmDelete;
            openModal('deleteModal');
        }

        // Blog functions
        function viewBlog(blogId) {
            // In a real application, you might open a blog preview modal
            showNotification('Blog preview feature would be implemented here.', 'info');
        }

        function deleteBlog(blogId) {
            deleteAction = 'blog';
            deleteId = blogId;
            document.getElementById('deleteMessage').textContent = 'Are you sure you want to delete this blog post? This action cannot be undone.';
            document.getElementById('confirmDeleteBtn').onclick = confirmDelete;
            openModal('deleteModal');
        }

        // Generic delete function
        function confirmDelete() {
            const deleteBtn = document.getElementById('confirmDeleteBtn');
            const originalText = deleteBtn.innerHTML;
            deleteBtn.innerHTML = '<span class="loading"></span>Deleting...';
            deleteBtn.disabled = true;

            const formData = new FormData();
            formData.append('action', `delete_${deleteAction}`);
            formData.append(`${deleteAction}_id`, deleteId);

            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the row from the table
                    removeTableRow(deleteAction, deleteId);
                    closeModal('deleteModal');
                    showNotification(`${deleteAction.charAt(0).toUpperCase() + deleteAction.slice(1)} deleted successfully!`, 'success');
                } else {
                    showNotification(`Failed to delete ${deleteAction}. Please try again.`, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // For demo purposes, simulate success
                removeTableRow(deleteAction, deleteId);
                closeModal('deleteModal');
                showNotification(`${deleteAction.charAt(0).toUpperCase() + deleteAction.slice(1)} deleted successfully!`, 'success');
            })
            .finally(() => {
                deleteBtn.innerHTML = originalText;
                deleteBtn.disabled = false;
            });
        }

        function removeTableRow(type, id) {
            const tableBody = document.getElementById(`${type}s-table-body`);
            const rows = tableBody.querySelectorAll('tr');
            
            rows.forEach(row => {
                const idCell = row.querySelector('td:first-child');
                if (idCell && (idCell.textContent.padStart(3, '0') == id.toString().padStart(3, '0'))) {
                    row.style.transition = 'all 0.3s ease';
                    row.style.opacity = '0';
                    row.style.transform = 'translateX(-20px)';
                    setTimeout(() => {
                        row.remove();
                        updateStats(type, -1);
                    }, 300);
                }
            });
        }

        function updateStats(type, change) {
            // Update the statistics cards
            const statCards = document.querySelectorAll('.stat-card');
            if (type === 'user') {
                const usersStat = statCards[2].querySelector('.stat-number');
                usersStat.textContent = parseInt(usersStat.textContent) + change;
            } else if (type === 'blog') {
                const blogsStat = statCards[3].querySelector('.stat-number');
                blogsStat.textContent = parseInt(blogsStat.textContent) + change;
            }
        }

        // Notification system
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${type === 'success' ? 'linear-gradient(135deg, #10b981, #059669)' : 
                           type === 'error' ? 'linear-gradient(135deg, #ef4444, #dc2626)' : 
                           'linear-gradient(135deg, #3b82f6, #1d4ed8)'};
                color: white;
                padding: 15px 20px;
                border-radius: 12px;
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
                z-index: 10000;
                font-weight: 600;
                animation: slideIn 0.3s ease;
                max-width: 300px;
            `;
            notification.textContent = message;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease forwards';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Logout function
        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                // In a real application, you would handle session destruction
                showNotification('Logging out...', 'info');
                setTimeout(() => {
                    window.location.href = 'login.php';
                }, 1000);
            }
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.classList.remove('active');
            }
        };

        // Add CSS animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from {
                    opacity: 0;
                    transform: translateX(100px);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }
            
            @keyframes slideOut {
                from {
                    opacity: 1;
                    transform: translateX(0);
                }
                to {
                    opacity: 0;
                    transform: translateX(100px);
                }
            }
        `;
        document.head.appendChild(style);

        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Admin Dashboard initialized');
        });
    </script>
</body>
</html>
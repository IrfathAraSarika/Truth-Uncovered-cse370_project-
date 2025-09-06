<?php
session_start();
include 'DBconnect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header('Location: login.php');
    exit();
}

// Handle message submission
$messageSent = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = $conn->real_escape_string($_POST['message']);
    if (!empty($message)) {
        $sql = "INSERT INTO admin_messages (admin_message, created_at) VALUES ('$message', NOW())";
        if ($conn->query($sql) === TRUE) {
            $messageSent = "Message sent successfully!";
        } else {
            $messageSent = "Error: " . $conn->error;
        }
    } else {
        $messageSent = "Please enter a message.";
    }
}


// Fetch all users (or just the logged-in user)
$sql = "SELECT * FROM reports";
$result = $conn->query($sql);
$reports = $result->fetch_all(MYSQLI_ASSOC);




//Fetch all user details
$sql_users = "SELECT * FROM users"; 
$result_users = $conn->query($sql_users);
  $users = $result_users->fetch_all(MYSQLI_ASSOC);

//Fetc  
//Fetch all Blogpost details
$sql_blogs = "SELECT * FROM blogposts"; 
$result_blogs = $conn->query($sql_blogs);
  $blogs = $result_blogs->fetch_all(MYSQLI_ASSOC);

//Update action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_report_status') {

    // Sanitize inputs
    $report_id = intval($_POST['report_id']);
    $status = $_POST['status'] ?? '';
    $agency = $_POST['agency'] ?? null;


    // Prepare and execute update
    $stmt = $conn->prepare("UPDATE reports SET Status = ?, AssignedAgency = ? WHERE Report_ID = ?");
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
        error_log("Prepare failed: " . $conn->error);
        exit;
    }

    $stmt->bind_param("ssi", $status, $agency, $report_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
        error_log("Report updated successfully: report_id=$report_id");
    } else {
        echo json_encode(['success' => false, 'message' => 'Database update failed: ' . $stmt->error]);
        error_log("Execute failed: " . $stmt->error);
    }

}


//User delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteUserId'])) {
    $userId = intval($_POST['deleteUserId']); // sanitize input

    $sql = "DELETE FROM users WHERE User_ID = $userId LIMIT 1";
    if ($conn->query($sql)) {
        echo "User deleted successfully.";
    } else {
        echo "Error deleting user: " . $conn->error;
    }
    exit;
}

        
//blog delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteBlogId'])) {
    $blogId = intval($_POST['deleteBlogId']); // sanitize input

    $sql = "DELETE FROM blogposts WHERE Post_ID =   $blogId LIMIT 1";
    if ($conn->query($sql)) {
        echo "Blog deleted successfully.";
    } else {
        echo "Error deleting blog: " . $conn->error;
    }
    exit;
}

// Sample agencies list
$agencies = [
    ['id' => 1,  'name' => 'Anti-Corruption Commission (Government)'],
    ['id' => 7,  'name' => 'National Board of Revenue (Government)'],
    ['id' => 6,  'name' => 'Bangladesh Bank (Government)'],
    ['id' => 11, 'name' => 'Transparency International Bangladesh (NGO)'],
    ['id' => 2,  'name' => 'Dhaka Metropolitan Police (Police)'],
    ['id' => 3,  'name' => 'Bangladesh Police Headquarters (Police)'],
    ['id' => 4,  'name' => 'Ministry of Home Affairs (Government)'],
    ['id' => 8,  'name' => 'Rapid Action Battalion - RAB (Police)'],
    ['id' => 9,  'name' => 'Detective Branch - DB (Police)'],
    ['id' => 10, 'name' => 'Traffic Police (Police)'],
    ['id' => 17, 'name' => 'Bangladesh Environmental Lawyers Association (NGO)'],
    ['id' => 18, 'name' => 'Dhaka Ahsania Mission (NGO)'],
    ['id' => 5,  'name' => 'Ministry of Women and Children Affairs (Government)'],
    ['id' => 20, 'name' => 'ActionAid Bangladesh (NGO)'],
    ['id' => 12, 'name' => 'BRAC (NGO)'],
    ['id' => 13, 'name' => 'Ain o Salish Kendra (NGO)'],
    ['id' => 14, 'name' => 'Bangladesh Legal Aid and Services Trust (NGO)'],
    ['id' => 15, 'name' => 'Manusher Jonno Foundation (NGO)'],
    ['id' => 16, 'name' => 'Odhikar (NGO)'],
    ['id' => 19, 'name' => 'Proshika (NGO)'],
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
        .button-wrapper {
                display: flex;
            justify-content: space-between;
            align-items: center;
            gap:10px;
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



        .send-btn {
    background: linear-gradient(135deg, #3b82f6, #8b5cf6);
    color: #fff;
    padding: 12px 25px;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
 
    width:100%;
    transition: all 0.3s ease;
}

.send-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(59,130,246,0.5);
}

/* Popup Overlay */
.popup-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(15,15,35,0.8);
    backdrop-filter: blur(10px);
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

/* Popup Content */
.popup-content {
    background: rgba(30,30,30,0.95);
    padding: 30px 40px;
    border-radius: 20px;
    width: 400px;
    text-align: center;
    position: relative;
    box-shadow: 0 8px 40px rgba(0,0,0,0.5);
    animation: slideDown 0.4s ease;
}

@keyframes slideDown {
    from { transform: translateY(-20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

/* Close Button */
.close-btn {
    position: absolute;
    top: 15px;
    right: 20px;
    font-size: 24px;
    cursor: pointer;
    color: #fff;
}

/* Input Field */
#messageInput {
    width: 100%;
    padding: 12px 15px;
    margin: 20px 0;
    border-radius: 12px;
    border: none;
    outline: none;
    background: rgba(255,255,255,0.05);
    color: #fff;
    font-size: 1rem;
    transition: 0.3s;
}

#messageInput:focus {
    box-shadow: 0 0 10px rgba(59,130,246,0.6);
    background: rgba(59,130,246,0.05);
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
            padding: 30px;
     
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

        .search-container {
  margin-bottom: 1rem;
  padding: 10px;
  text-align: center; /* centers the input if fixed width */
}

#reportSearch {
  width: 500px; /* fixed width */
  padding: 0.6rem;
  border: none;
  border-bottom: 2px solid #ccc; /* only bottom border */
  background: transparent;
  color: #fff;
  font-size: 1rem;

  outline: none; /* remove default blue outline */
  transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

#reportSearch:focus {
  border-bottom: 2px solid #3b82f6; /* blue bottom border */
  box-shadow: 0 2px 6px -2px rgba(59, 130, 246, 0.7); /* subtle glow */
}

#userSearch {
  width: 500px; /* fixed width */
  padding: 0.6rem;
  border: none;
  border-bottom: 2px solid #ccc; /* only bottom border */
  background: transparent;
  color: #fff;
  font-size: 1rem;

  outline: none; /* remove default blue outline */
  transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

#userSearch:focus {
  border-bottom: 2px solid #3b82f6; /* blue bottom border */
  box-shadow: 0 2px 6px -2px rgba(59, 130, 246, 0.7); /* subtle glow */
}





#blogSearch {
  width: 500px; /* fixed width */
  padding: 0.6rem;
  border: none;
  border-bottom: 2px solid #ccc; /* only bottom border */
  background: transparent;
  color: #fff;
  font-size: 1rem;

  outline: none; /* remove default blue outline */
  transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

#blogSearch:focus {
  border-bottom: 2px solid #3b82f6; /* blue bottom border */
  box-shadow: 0 2px 6px -2px rgba(59, 130, 246, 0.7); /* subtle glow */
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
        .form-select option {
      background: rgba(31, 29, 29, 0.95); /* grayish background */
     color: #fff;      /* white text for options */
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
      <header>
        <div class="container">
            <nav>
                <div class="logo"   onclick="window.location.href='index.php'">🔍 TruthUncovered</div>
                <div class="button-wrapper">
<div class="nav-actions">
                    <a href="analytic.php" class="back-button">Analytics </a>
                </div>
                <div class="nav-actions">
                    <a href="index.php" class="back-button">← Back to Home Page</a>
                </div>
                </div>
                
            </nav>
        </div>
    </header>

    <div class="dashboard-container">
        <!-- Header -->
       

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
                <div class="stat-number"> <?php echo count($reports); ?></div>
                <div class="stat-label">Total Reports</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <div class="stat-number"><div class="stat-number">
    <?= $conn->query("SELECT COUNT(*) FROM reports WHERE Status = 'pending'")->fetch_row()[0] ?>
</div>
</div>
                <div class="stat-label">Pending Reports</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-number"><?= $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0] ?></div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-blog"></i></div>
                <div class="stat-number"><?= $conn->query("SELECT COUNT(*) FROM blogposts")->fetch_row()[0] ?></div>
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
              <button class="nav-tab" onclick="switchTab('message')">
                <i class="fas fa-message"></i> Send Message
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
            <!-- 🔎 Search bar -->
<div class="search-container">
  <input type="text" id="reportSearch" placeholder="Search reports...">
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
                            <td colspan="7" style="text-align:center;">No reports found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($reports as $report): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($report['Report_ID']); ?></td>
                            <td><?php echo htmlspecialchars($report['Title']); ?></td>
                        <td>
    <?= !empty($report['User_ID']) 
        ? ($u = $conn->query("SELECT Name FROM users WHERE User_ID = {$report['User_ID']}")->fetch_assoc()) 
            ? htmlspecialchars($u['Name']) 
            : "Anonymous" 
        : "Anonymous" ?>
</td>

                            <td>
                                <span class="status-badge status-<?php echo strtolower($report['Status']); ?>">
                                    <?php echo htmlspecialchars($report['Status']); ?>
                                </span>
                            </td>
                            <td>
                              <?= (isset($report['AssignedAgency']) && trim($report['AssignedAgency']) !== '' && strtolower(trim($report['AssignedAgency'])) !== 'null') 
    ? $report['AssignedAgency'] 
    : "Not Assigned" ?>

                            </td>
                            <td>
                                <?php echo !empty($report['Date_Submitted']) ? $report['Date_Submitted'] : "-"; ?>
                            </td>
                            <td>
                                <button class="action-btn" onclick="viewReport(<?php echo $report['Report_ID']; ?>)">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                <button class="action-btn" onclick="updateReport(<?php echo $report['Report_ID']; ?>)">
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
                 <div class="search-container">
  <input type="text" id="userSearch" placeholder="Search users...">
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
                                <td><?php echo htmlspecialchars($user['User_ID']); ?></td>
                                <td><?php echo htmlspecialchars($user['Name']); ?></td>
                                <td><?php echo htmlspecialchars($user['Email']); ?></td>
                                <td><?php echo htmlspecialchars($user['Role']); ?></td>
                                <td><?php echo htmlspecialchars($user['Phone'] ?? 'N/A'); ?></td>
                                <td><?php echo date('Y-m-d', strtotime($user['created_at'] ?? 'now')); ?></td>
                                <td>
                                    <button class="action-btn danger" onclick="deleteUser(<?php echo $user['User_ID']; ?>)">
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
 <div class="search-container">
  <input type="text" id="blogSearch" placeholder="Search blogs...">
</div>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Author ID</th>
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
                                <td><?php echo htmlspecialchars($blog['Post_ID']); ?></td>
                                <td><?php echo htmlspecialchars($blog['Title']); ?></td>
                                <td><?php echo htmlspecialchars($blog['Author_ID'] ?? 'Unknown'); ?></td>
                                <td><?php echo htmlspecialchars($blog['Category'] ?? 'General'); ?></td>
                                <td><?php echo htmlspecialchars($blog['Status'] ?? 'Published'); ?></td>
                                <td><?php echo date('Y-m-d', strtotime($blog['Published_At'])); ?></td>
                                <td>
                                    <button class="action-btn" onclick="viewBlog(<?php echo $blog['Post_ID']; ?>)">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button class="action-btn danger" onclick="deleteBlog(<?php echo $blog['Post_ID']; ?>)">
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

   <!-- message section  -->

  <div id="message-section" class="content-section">
    <div class="section-card">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-message"></i>
                Send Message
            </h2>
        </div>
       <div style="padding:20px;">
<button id="openMessagePopup" class="send-btn">Create Message</button>
       </div>
        <!-- Send Message Button -->
         <!-- Display feedback after submission -->

    </div>


    </div>




    <!-- Popup Modal -->
    <div id="messagePopup" class="popup-overlay">
        <div class="popup-content">
            <span class="close-btn" id="closePopup">&times;</span>
            <h3>Send a New Message</h3>
            <form method="post" action="">
            <input type="text" name ="message" id="messageInput" placeholder="Type your message..." />
            <button type="submit"  id="sendMessage" class="send-btn">Send</button>
    </form>
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
                    <label class="form-label">Status</label>
                    <select id="reportUpdateStatus" class="form-select">
                        <option value="pending">Pending</option>
                        <option value="accepted">Accepted</option>
                        <option value="declined">Declined</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Assign Agency</label>
                    <select id="reportUpdateAgency" class="form-select">                  
                    <option value="" disabled selected>Choose an institution</option>
                    <?php foreach ($agencies as $agency): ?>
                        <option value="<?php echo $agency['name']; ?>">
                            <?php echo htmlspecialchars($agency['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>


                </div>
            </div>
            <div class="modal-actions">
                <button class="btn btn-primary" onclick="saveReportUpdate()">
                    <i class="fas fa-save"></i> Save Changes
                </button>
                <button class="btn btn-secondary" onclick="closeModal('reportUpdateModal')">
                    Cancel
                </button>
            </div>
        </div>
    </div>


<!-- ........................View  Modal starts.............. -->
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
                <strong>Author:</strong> <span id="reportReporter">John Doe</span><br>
                <strong>Status:</strong> <span id="reportStatus">Pending Review</span><br>
                <strong>Assigned Agency:</strong> <span id="reportAgency">City Safety Department</span>
            </p>
        </div>

      
    </div>
</div>


<!-- ........................View Modal  ends.............. -->


         <!-- Blog v modal starts here  -->
       
<div id="blogReportModal" class="modal">
    <div class="modal-content dark-view">
        <div class="modal-header">
            <h3 class="modal-title">Blog Details</h3>
            <button class="close-btn" onclick="closeModal('blogReportModal')">&times;</button>
        </div>

        <div id="reportModalBody" class="article-view">
            <h2 id="blogReportTitle">Unauthorized Construction Near Residential Area</h2>

            <p id="blogReportDescription">
                A concerned citizen has reported the construction of a building 
                that is allegedly being built without the necessary permits. 
                The structure, located near a densely populated neighborhood, 
                is raising safety concerns among local residents. According to 
                eyewitnesses, the construction has been ongoing for several weeks 
                with no visible signs of authorization from the city authorities.
            </p>

            <p>
                <strong>Author :</strong> <span id="blogReportReporter">John Doe</span><br>
                <strong>Status:</strong> <span id="blogReportStatus">Pending Review</span><br>
        
            </p>
        </div>

      
    </div>
</div>
      <!-- Blog view modal starts here  -->
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
            // Pass PHP reports array to JS
    const reportsData = <?php echo json_encode($reports); ?>;
    const blogsData=<?php echo json_encode($blogs); ?>;
        let currentReportId = null;
        let deleteAction = null;
        let deleteId = null;
        let seletedReportId=null;
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

        //report search 
        document.getElementById("reportSearch").addEventListener("keyup", function () {
  let value = this.value.toLowerCase();
  let rows = document.querySelectorAll("#reports-table-body tr");

  rows.forEach(row => {
    let text = row.innerText.toLowerCase();
    row.style.display = text.includes(value) ? "" : "none";
  });
});
        

//blog search
     //report search 
        document.getElementById("blogSearch").addEventListener("keyup", function () {
  let value = this.value.toLowerCase();
  let rows = document.querySelectorAll("#blogs-table-body tr");

  rows.forEach(row => {
    let text = row.innerText.toLowerCase();
    row.style.display = text.includes(value) ? "" : "none";
  });
});
//user search
        document.getElementById("userSearch").addEventListener("keyup", function () {
  let value = this.value.toLowerCase();
  let rows = document.querySelectorAll("#users-table-body tr");

  rows.forEach(row => {
    let text = row.innerText.toLowerCase();
    row.style.display = text.includes(value) ? "" : "none";
  });
});


        // Report functions
function viewReport(reportId, updateFlag = false) {
    currentReportId = reportId;

    // Find the report in the reportsData array
    const report = reportsData.find(r => parseInt(r.Report_ID) === parseInt(reportId));

    console.log("report data:",report)
    if (!report) {
        alert("Report not found");
        return;
    }

    // Populate modal content
   document.getElementById('reportTitle').innerText = report.Title || 'Untitled';
    document.getElementById('reportDescription').innerText = report.Description || 'No description provided';
    document.getElementById('reportReporter').innerText = report.User_ID || 'Anonymous'; // Or map to username
    document.getElementById('reportStatus').innerText = report.Status || 'N/A';
    document.getElementById('reportAgency').innerText = report.AssignedAgency || 'Not Assigned';

    // Populate evidence image dynamically
    const evidence = document.getElementById('reportEvidence');
    if (report.file_path) {
        evidence.innerHTML = `
            <img src="${report.file_path}" 
                 alt="Report Evidence" 
                 style="max-width:100%; border-radius:10px; margin-top:1rem; box-shadow:0 3px 8px rgba(0,0,0,0.4);" />
            <figcaption style="font-size:0.9rem; color:#bbb; margin-top:0.5rem;">
                Evidence provided by the reporter
            </figcaption>`;
    } else {
        evidence.innerHTML = `<p style="color:#bbb;">No evidence provided</p>`;
    }

    // Open the correct modal
    if (updateFlag) {
        openModal('reportUpdateModal');
    } else {
        openModal('reportModal');
    }
}


        function updateReport(reportId) {
            seletedReportId=reportId;
         openModal('reportUpdateModal');
        }

        function saveReportUpdate() {
            // console.log("seletedReportId",seletedReportId)
                const report = reportsData.find(r => parseInt(r.Report_ID) === parseInt(seletedReportId));
            const status =   document.getElementById('reportUpdateStatus').value || report.Status;
            const agency =  document.getElementById('reportUpdateAgency').value || report.AssignedAgency ;
            
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
                formData.append('report_id', seletedReportId);
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
                        updateReportRow(seletedReportId, status, agency);
                       
                        showNotification('Report updated successfully!', 'success');
                            location.reload();
                         closeModal('reportUpdateModal');
                    } else {
                        
                        showNotification('Failed to update report. Please try again.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Report updated successfully!', 'success'); // For demo
                    updateReportRow(seletedReportId, status, agency);

                    closeModal('reportUpdateModal');
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
    document.getElementById('deleteMessage').textContent =
        'Are you sure you want to delete this user? This action cannot be undone.';

    document.getElementById('confirmDeleteBtn').onclick = function() {
        // Send AJAX POST request back to the same page
        fetch(window.location.href, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'deleteUserId=' + encodeURIComponent(userId)
        })
        .then(response => response.text())
        .then(data => {
            showNotification('Report deleted successfully!', 'success');
            location.reload();
        })
        .catch(error => console.error('Error:', error));
    };

    openModal('deleteModal');
}


        // Blog functions
 function viewBlog(blogId) {
    currentBlogId = blogId;

    // Find the report in the reportsData array
    const report = blogsData.find(r => parseInt(r.Post_ID) === parseInt(currentBlogId));
    console.log("report data:",report)
    if (!report) {
        alert("Report not found");
        return;
    }
    // Populate modal content
   document.getElementById('blogReportTitle').innerText = report.Title || 'Untitled';
    document.getElementById('blogReportDescription').innerText = report.Content || 'No description provided';
    document.getElementById('blogReportReporter').innerText = report.Author_ID || 'Anonymous'; // Or map to username
    document.getElementById('blogReportStatus').innerText = report.Status || 'N/A';
     openModal('blogReportModal');
    
        }

  function deleteBlog(blogId) {
            deleteAction = 'blog';
            deleteId = blogId;
            document.getElementById('deleteMessage').textContent = 'Are you sure you want to delete this blog post?';
             document.getElementById('confirmDeleteBtn').onclick = function() {
        // Send AJAX POST request back to the same page
        fetch(window.location.href, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'deleteBlogId=' + encodeURIComponent(deleteId)
        })
        .then(response => response.text())
        .then(data => {
            showNotification('Report deleted successfully!', 'success');
            location.reload();
        })
        .catch(error => console.error('Error:', error));
    };
           
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


       //send message
       // Open popup
document.getElementById("openMessagePopup").onclick = function() {
    document.getElementById("messagePopup").style.display = "flex";
}

// Close popup
document.getElementById("closePopup").onclick = function() {
    document.getElementById("messagePopup").style.display = "none";
}

// Send Message (example alert)

<?php if(!empty($messageSent)): ?>
    // Call your notification function
    showNotification("<?php echo addslashes($messageSent); ?>");
<?php endif; ?>



    </script>
</body>
</html>
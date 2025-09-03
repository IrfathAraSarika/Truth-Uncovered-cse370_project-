<?php
session_start();
include 'DBconnect.php';

// Insert Report function
function createReport($pdo, $data, $files) {
    $stmt = $pdo->prepare("
        INSERT INTO reports 
        (Title, Description, Date_Submitted, Status, Incident_Longitude, Incident_Latitude, Incident_Address, Incident_Date, Incident_Time, Category_ID, User_ID) 
        VALUES (?, ?, NOW(), 'Draft', ?, ?, ?, ?, ?, ?, ?)
    ");

    $success = $stmt->execute([
        $data['title'],
        $data['description'],
        $data['longitude'],
        $data['latitude'],
        $data['address'],          // ✅ now maps to Incident_Address
        $data['incident_date'],
        $data['incident_time'],
        $data['category'],
        $data['user_id']
    ]);

    // Handle evidence uploads
    if ($success && !empty($files['evidence']['name'][0])) {
        $reportId = $pdo->lastInsertId();
        foreach ($files['evidence']['tmp_name'] as $key => $tmpName) {
            $filename = basename($files['evidence']['name'][$key]);
            $target = "uploads/" . time() . "_" . $filename;
            if (move_uploaded_file($tmpName, $target)) {
                $stmtFile = $pdo->prepare("
                    INSERT INTO evidence (Report_ID, Type, File_Link) VALUES (?, ?, ?)
                ");
                $fileType = mime_content_type($target);
                $stmtFile->execute([$reportId, $fileType, $target]);
            }
        }
    }

    return $success;
}

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (createReport($conn, $_POST, $_FILES)) {
        // Save notification in session
        $_SESSION['notification'] = "✅ Thank you for your submission. Your report has been received and is under review. You will be updated soon.";
        
        // Redirect to homepage
        header("Location: index.php");
        exit();
    } else {
        $error = "❌ Could not create report. Try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Submit Report - Truth Uncovered</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #0f0f23, #1a1a2e, #16213e, #0f3460, #1e3a8a);
      color: #fff;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 30px;
    }
    .report-box {
      background: rgba(255,255,255,0.08);
      padding: 30px;
      border-radius: 20px;
      width: 100%;
      max-width: 800px;
      backdrop-filter: blur(15px);
    }
    h1 {
      text-align: center;
      margin-bottom: 20px;
      background: linear-gradient(135deg, #3b82f6, #8b5cf6);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    .form-group {
      margin-bottom: 20px;
    }
    label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      font-size: 0.9rem;
    }
    input, select, textarea {
      width: 100%;
      padding: 12px;
      border-radius: 10px;
      border: none;
      outline: none;
      font-size: 1rem;
      background: rgba(255,255,255,0.1);
      color: #fff;
    }
    input::placeholder, textarea::placeholder {
      color: rgba(255,255,255,0.5);
    }
    button {
      width: 100%;
      padding: 15px;
      background: linear-gradient(135deg, #3b82f6, #8b5cf6);
      border: none;
      border-radius: 12px;
      font-size: 1.1rem;
      font-weight: 600;
      cursor: pointer;
      color: #fff;
    }
    .message {
      margin-bottom: 20px;
      padding: 12px;
      border-radius: 8px;
    }
    .error { background: rgba(239,68,68,0.2); color: #fecaca; }
  </style>
</head>
<body>
  <div class="report-box">
    <h1>Submit a Report</h1>

    <?php if (!empty($error)): ?>
      <div class="message error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
      <div class="form-group">
        <label for="title">Report Title</label>
        <input type="text" name="title" id="title" required placeholder="Enter report title">
      </div>
      <div class="form-group">
        <label for="description">Description</label>
        <textarea name="description" id="description" rows="5" required placeholder="Describe the incident"></textarea>
      </div>
      <div class="form-group">
        <label for="category">Category</label>
        <select name="category" id="category" required>
          <option value="">Select a category</option>
          <option value="1">Corruption</option>
          <option value="2">Antisocial</option>
          <option value="3">Hazard</option>
          <option value="4">Harassment</option>
          <option value="5">Dowry</option>
        </select>
      </div>
      <div class="form-group">
        <label>Incident Location</label>
        <input type="text" name="longitude" placeholder="Longitude">
        <input type="text" name="latitude" placeholder="Latitude" style="margin-top:10px;">
        <input type="text" name="address" placeholder="Incident Address" style="margin-top:10px;">
      </div>
      <div class="form-group">
        <label for="incident_date">Incident Date</label>
        <input type="date" name="incident_date" id="incident_date" required>
      </div>
      <div class="form-group">
        <label for="incident_time">Incident Time</label>
        <input type="time" name="incident_time" id="incident_time" required>
      </div>
      <div class="form-group">
        <label for="evidence">Evidence (Photos, Videos, Docs)</label>
        <input type="file" name="evidence[]" id="evidence" multiple>
      </div>
      <input type="hidden" name="user_id" value="1"><!-- Replace with session user ID -->
      <button type="submit">Submit Report</button>
    </form>
  </div>
</body>
</html>

<?php
session_start();
include 'DBconnect.php'; 
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Insert Report function
function createReport($conn, $data, $files) {
    $file_paths = []; // array to store all uploaded file paths

    // Handle multiple file uploads
    if (isset($files['report_file'])) {
        foreach ($files['report_file']['tmp_name'] as $key => $tmpName) {
            if ($files['report_file']['error'][$key] === 0) {
                $uploadDir = "uploads/";
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

                $fileName = basename($files['report_file']['name'][$key]);
                $targetFile = $uploadDir . time() . "_" . $fileName;

                if (move_uploaded_file($tmpName, $targetFile)) {
                    $file_paths[] = $targetFile; // save path
                }
            }
        }
    }

    // Convert array of paths to comma-separated string
    $file_path_str = implode(",", $file_paths);


$mode = $_GET['mode'] ?? null;
    // Insert into reports (added file_path column)

if ($mode) {
    // Insert with Mode
    $stmt = $conn->prepare("
        INSERT INTO reports 
        (Title, Description, Date_Submitted, Status, Incident_Address, Incident_Date, Incident_Time, Category_ID, User_ID, file_path, Mode) 
        VALUES (?, ?, NOW(), 'Draft', ?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) die("Prepare failed: " . $conn->error);

    $stmt->bind_param(
        "sssssiiss",
        $data['title'],
        $data['description'],
        $data['address'],
        $data['incident_date'],
        $data['incident_time'],
        $data['category'],
        $data['user_id'],
        $file_path_str,
        $mode
    );
} else {
    // Normal insert (no Mode column)
    $stmt = $conn->prepare("
        INSERT INTO reports 
        (Title, Description, Date_Submitted, Status, Incident_Address, Incident_Date, Incident_Time, Category_ID, User_ID, file_path) 
        VALUES (?, ?, NOW(), 'Draft', ?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) die("Prepare failed: " . $conn->error);

    $stmt->bind_param(
        "sssssiis",
        $data['title'],
        $data['description'],
        $data['address'],
        $data['incident_date'],
        $data['incident_time'],
        $data['category'],
        $data['user_id'],
        $file_path_str
    );
}

    $success = $stmt->execute();
    if (!$success) die("Execute failed: " . $stmt->error);

    $stmt->close();
    return $success;
}

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (createReport($conn, $_POST, $_FILES)) {
      $_SESSION['notification'] = "✅ Report submitted successfully!";
        $_POST = [];
        $_FILES = [];
        // Redirect if needed
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

  <h1>
    Submit a Report
    <?php if (isset($_GET['mode'])): ?>
        - <?= htmlspecialchars($_GET['mode']) ?>
    <?php endif; ?>
</h1>




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
        <label for="report_file">Evidence (Photos, Videos, Docs)</label>
        <input type="file" name="report_file[]" id="report_file" multiple>
      </div>
<input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">

      <button type="submit">Submit Report</button>
    </form>
  </div>
</body>
</html>



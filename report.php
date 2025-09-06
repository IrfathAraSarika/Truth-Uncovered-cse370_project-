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
    
    // Insert into reports (updated with separate address and division fields)
    if ($mode) {
        // Insert with Mode
        $stmt = $conn->prepare("
            INSERT INTO reports 
            (Title, Description, Date_Submitted, Status, Incident_Address, Incident_Division, Incident_Date, Incident_Time, Category_ID, User_ID, file_path, Mode) 
            VALUES (?, ?, NOW(), 'Draft', ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        if (!$stmt) die("Prepare failed: " . $conn->error);

        $stmt->bind_param(
            "sssssssiiss",
            $data['title'],
            $data['description'],
            $data['address'],
            $data['division'],
            $data['incident_date'],
            $data['incident_time'],
            $data['category'],
            $data['user_id'],
            $file_path_str,
            $mode
        );
    } else {
        // Normal insert (no Mode column) - FIXED: Corrected bind_param
        $stmt = $conn->prepare("
            INSERT INTO reports 
            (Title, Description, Date_Submitted, Status, Incident_Address, Incident_Division, Incident_Date, Incident_Time, Category_ID, User_ID, file_path) 
            VALUES (?, ?, NOW(), 'Draft', ?, ?, ?, ?, ?, ?, ?)
        ");

        if (!$stmt) die("Prepare failed: " . $conn->error);

        // FIXED: Changed from "sssssssiis" (10 chars) to "ssssssiis" (9 chars)
        // to match the 9 placeholders in the SQL query
        $stmt->bind_param(
            "ssssssiis",
            $data['title'],
            $data['description'],
            $data['address'],
            $data['division'],
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
    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 40px;
    }
    
    @media (max-width: 600px) {
      .form-row {
        grid-template-columns: 1fr;
      }
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
    select option {
      background: #1e293b;
      color: #fff;
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
      gap:10px
    }
    button:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
      transition: all 0.3s ease;
    }
    .message {
      margin-bottom: 20px;
      padding: 12px;
      border-radius: 8px;
    }
    .error { 
      background: rgba(239,68,68,0.2); 
      color: #fecaca; 
      border: 1px solid rgba(239,68,68,0.4);
    }
    .success {
      background: rgba(34,197,94,0.2);
      color: #bbf7d0;
      border: 1px solid rgba(34,197,94,0.4);
    }
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

    <?php if (isset($error)): ?>
        <div class="message error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
      <div class="form-group">
        <label for="title">Report Title *</label>
        <input type="text" name="title" id="title" required placeholder="Enter report title" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
      </div>
      
      <div class="form-group">
        <label for="description">Description *</label>
        <textarea name="description" id="description" rows="5" required placeholder="Describe the incident in detail"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
      </div>
      
      <div class="form-group">
        <label for="category">Category *</label>
        <select name="category" id="category" required>
          <option value="">Select a category</option>
          <option value="1" <?= ($_POST['category'] ?? '') == '1' ? 'selected' : '' ?>>Corruption</option>
          <option value="2" <?= ($_POST['category'] ?? '') == '2' ? 'selected' : '' ?>>Antisocial</option>
          <option value="3" <?= ($_POST['category'] ?? '') == '3' ? 'selected' : '' ?>>Hazard</option>
          <option value="4" <?= ($_POST['category'] ?? '') == '4' ? 'selected' : '' ?>>Harassment</option>
          <option value="5" <?= ($_POST['category'] ?? '') == '5' ? 'selected' : '' ?>>Dowry</option>
        </select>
      </div>

      <!-- Updated Location Section with Two Fields -->
      <div class="form-row">
        <div class="form-group">
          <label for="division">Division *</label>
          <select name="division" id="division" required>
            <option value="">Select Division</option>
            <option value="Dhaka" <?= ($_POST['division'] ?? '') == 'Dhaka' ? 'selected' : '' ?>>Dhaka</option>
            <option value="Chittagong" <?= ($_POST['division'] ?? '') == 'Chittagong' ? 'selected' : '' ?>>Chittagong</option>
            <option value="Khulna" <?= ($_POST['division'] ?? '') == 'Khulna' ? 'selected' : '' ?>>Khulna</option>
            <option value="Rajshahi" <?= ($_POST['division'] ?? '') == 'Rajshahi' ? 'selected' : '' ?>>Rajshahi</option>
            <option value="Sylhet" <?= ($_POST['division'] ?? '') == 'Sylhet' ? 'selected' : '' ?>>Sylhet</option>
            <option value="Barishal" <?= ($_POST['division'] ?? '') == 'Barishal' ? 'selected' : '' ?>>Barishal</option>
            <option value="Rangpur" <?= ($_POST['division'] ?? '') == 'Rangpur' ? 'selected' : '' ?>>Rangpur</option>
            <option value="Mymensingh" <?= ($_POST['division'] ?? '') == 'Mymensingh' ? 'selected' : '' ?>>Mymensingh</option>
          </select>
        </div>
        
        <div class="form-group">
          <label for="address">Exact Address *</label>
          <input type="text" name="address" id="address" required placeholder="Street address, area, landmarks" value="<?= htmlspecialchars($_POST['address'] ?? '') ?>">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="incident_date">Incident Date *</label>
          <input type="date" name="incident_date" id="incident_date" required value="<?= htmlspecialchars($_POST['incident_date'] ?? '') ?>">
        </div>
        
        <div class="form-group">
          <label for="incident_time">Incident Time *</label>
          <input type="time" name="incident_time" id="incident_time" required value="<?= htmlspecialchars($_POST['incident_time'] ?? '') ?>">
        </div>
      </div>
      
      <div class="form-group">
        <label for="report_file">Evidence (Photos, Videos, Documents)</label>
        <input type="file" name="report_file[]" id="report_file" multiple accept="image/*,video/*,.pdf,.doc,.docx,.txt">
        <small style="color: rgba(255,255,255,0.6); font-size: 0.8rem; margin-top: 5px; display: block;">
          Supported formats: Images, Videos, PDF, Word documents. Max 5MB per file.
        </small>
      </div>

      <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
      
      <button type="submit">Submit Report</button>
      
      <div style="text-align: center; margin-top: 15px;">
        <a href="index.php" style="color: rgba(255,255,255,0.7); text-decoration: none; font-size: 0.9rem;">
          ← Back to Dashboard
        </a>
      </div>
    </form>
  </div>

  <script>
    // Set max date to today for incident date
    document.getElementById('incident_date').setAttribute('max', new Date().toISOString().split('T')[0]);
    
    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
      const requiredFields = this.querySelectorAll('[required]');
      let isValid = true;
      
      requiredFields.forEach(field => {
        if (!field.value.trim()) {
          field.style.borderColor = '#ef4444';
          isValid = false;
        } else {
          field.style.borderColor = 'transparent';
        }
      });
      
      if (!isValid) {
        e.preventDefault();
        alert('Please fill in all required fields.');
      }
    });
  </script>
</body>
</html>
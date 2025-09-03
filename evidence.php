<?php
session_start();
include 'DBconnect.php';

// Insert Evidence function
function addEvidence($pdo, $data, $files) {
    $reportId = $data['report_id'];
    $type = $data['type'];

    $target = null;
    $watermarkedCopy = null;

    // Only handle file if type != None and a file is uploaded
    if ($type !== "None") {
    // Check if a file was actually uploaded
    if (isset($files['file']) && $files['file']['error'] === UPLOAD_ERR_OK && !empty($files['file']['name'])) {
        $filename = basename($files['file']['name']);
        $target = "uploads/" . time() . "_" . $filename;

        if (move_uploaded_file($files['file']['tmp_name'], $target)) {
            // Fake watermark copy path (in real case, process watermark)
            $watermarkedCopy = "uploads/watermarked_" . $filename;
            copy($target, $watermarkedCopy);
        }
    } else {
        // No file uploaded even though type is not "None"
        $target = null;
        $watermarkedCopy = null;
    }
} else {
    // Type is "None", skip file
    $target = null;
    $watermarkedCopy = null;
}

   


    $stmt = $pdo->prepare("
        INSERT INTO evidence (Report_ID, Type, File_Link, Watermarked_Copy, GPS_Latitude, GPS_Longitude)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    return $stmt->execute([
        $reportId,
        $type,
        $target,
        $watermarkedCopy,
        $data['latitude'],
        $data['longitude']
    ]);
}

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (addEvidence($conn, $_POST, $_FILES)) {
        $_SESSION['notification'] = "✅ Evidence submitted successfully and linked to your report.";
        // Redirect back to report page
        header("Location: report.php");
        exit();
    } else {
        $error = "❌ Could not save evidence. Try again.";
    }
}

// Get report_id from query string (optional)
$reportId = $_GET['report_id'] ?? null;

// If report_id is missing, fallback to 0
if (!$reportId) {
    $reportId = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Evidence - Truth Uncovered</title>
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
    .evidence-box {
      background: rgba(255,255,255,0.08);
      padding: 30px;
      border-radius: 20px;
      width: 100%;
      max-width: 700px;
      backdrop-filter: blur(15px);
    }
    h1 {
      text-align: center;
      margin-bottom: 20px;
      background: linear-gradient(135deg, #3b82f6, #8b5cf6);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    .form-group { margin-bottom: 20px; }
    label { display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.9rem; }
    input, select {
      width: 100%;
      padding: 12px;
      border-radius: 10px;
      border: none;
      outline: none;
      font-size: 1rem;
      background: rgba(255,255,255,0.1);
      color: #fff;
    }
    input::placeholder { color: rgba(255,255,255,0.5); }
    button {
      width: 100%;
      padding: 15px;
      background: linear-gradient(135deg, #10b981, #059669);
      border: none;
      border-radius: 12px;
      font-size: 1.1rem;
      font-weight: 600;
      cursor: pointer;
      color: #fff;
      margin-top: 10px;
    }
    .message { margin-bottom: 20px; padding: 12px; border-radius: 8px; }
    .error { background: rgba(239,68,68,0.2); color: #fecaca; }
  </style>
</head>
<body>
  <div class="evidence-box">
    <h1>Submit Evidence</h1>

    <?php if (!empty($error)): ?>
      <div class="message error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="report_id" value="<?= htmlspecialchars($reportId) ?>">

      <div class="form-group">
        <label for="type">Evidence Type</label>
        <select name="type" id="type" required onchange="toggleFileInput(this.value)">
          <option value="">Select type</option>
          <option value="Photo">📷 Photo</option>
          <option value="Video">🎥 Video</option>
          <option value="Document">📄 Document</option>
          <option value="None">🚫 No File</option>
        </select>
      </div>

      <div class="form-group" id="fileUploadGroup">
        <label for="file">Upload File</label>
        <input type="file" name="file" id="file">
      </div>

      <div class="form-group">
        <label>GPS Location</label>
        <input type="text" name="latitude" placeholder="Latitude">
        <input type="text" name="longitude" placeholder="Longitude" style="margin-top:10px;">
      </div>

      <button type="submit">Save Evidence</button>
    </form>
  </div>

  <script>
    function toggleFileInput(type) {
        const fileInput = document.getElementById("file");
        const fileGroup = document.getElementById("fileUploadGroup");

        if (type === "None") {
            fileInput.value = "";        // <-- Clear any previously selected file
            fileInput.required = false;
            fileInput.disabled = true;
            fileGroup.style.opacity = "0.5"; // visually show disabled
        } else {
            fileInput.disabled = false;
            fileInput.required = true;
            fileGroup.style.opacity = "1";



  
      }
    }
  </script>
</body>
</html>

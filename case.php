<?php
session_start();
include 'DBconnect.php';

// ✅ Create Case
function createCase($pdo, $data) {
    $stmt = $pdo->prepare("
        INSERT INTO cases (Report_ID, Status, Assigned_Agency, Timeline) 
        VALUES (?, 'Received', ?, ?)
    ");

    $timeline = json_encode([
        ["status" => "Received", "date" => date("Y-m-d H:i:s"), "note" => "Case created by admin"]
    ]);

    return $stmt->execute([
        $data['report_id'],
        $data['assigned_agency'],
        $timeline
    ]);
}

// ✅ Update Case
function updateCase($pdo, $data) {
    $stmt = $pdo->prepare("SELECT Timeline FROM cases WHERE Case_ID = ?");
    $stmt->execute([$data['case_id']]);
    $row = $stmt->fetch();

    $timeline = $row ? json_decode($row['Timeline'], true) : [];

    $timeline[] = [
        "status" => $data['status'],
        "date" => date("Y-m-d H:i:s"),
        "note" => $data['note']
    ];

    $update = $pdo->prepare("UPDATE cases SET Status = ?, Timeline = ? WHERE Case_ID = ?");
    return $update->execute([
        $data['status'],
        json_encode($timeline),
        $data['case_id']
    ]);
}

// ✅ Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_case'])) {
        if (createCase($conn, $_POST)) {
            $_SESSION['notification'] = "✅ Case created successfully.";
        } else {
            $_SESSION['notification'] = "❌ Failed to create case.";
        }
    }

    if (isset($_POST['update_case'])) {
        if (updateCase($conn, $_POST)) {
            $_SESSION['notification'] = "✅ Case updated successfully.";
        } else {
            $_SESSION['notification'] = "❌ Failed to update case.";
        }
    }

    header("Location: case.php");
    exit();
}

// ✅ Fetch all cases (no Created_At, so order by Case_ID)
$result = $conn->query("SELECT * FROM cases ORDER BY Case_ID DESC");
$cases = $result->fetch_all(MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Case Management - Truth Uncovered</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #0f0f23, #1a1a2e, #16213e, #0f3460, #1e3a8a);
      color: #fff;
      min-height: 100vh;
      padding: 30px;
    }
    .container { max-width: 1200px; margin: auto; }
    h1 { text-align: center; margin-bottom: 20px; }
    .message { padding: 12px; border-radius: 8px; margin-bottom: 20px; }
    .success { background: rgba(34,197,94,0.2); color: #bbf7d0; }
    .error { background: rgba(239,68,68,0.2); color: #fecaca; }
    .card {
      background: rgba(255,255,255,0.08);
      padding: 20px;
      margin-bottom: 20px;
      border-radius: 16px;
      backdrop-filter: blur(12px);
    }
    select, input, textarea, button {
      width: 100%; padding: 10px; margin-top: 6px;
      border-radius: 8px; border: none; outline: none;
      font-family: 'Inter', sans-serif;
    }
    button {
      background: linear-gradient(135deg, #3b82f6, #8b5cf6);
      color: white; font-weight: bold; cursor: pointer;
    }
    button:hover { opacity: 0.9; }
    .case-list { margin-top: 40px; }
    .timeline { font-size: 0.9rem; margin-top: 10px; }
    .timeline div { margin-bottom: 5px; }
  </style>
</head>
<body>
<div class="container">
  <h1>Case Management</h1>

  <?php if (!empty($_SESSION['notification'])): ?>
    <div class="message success"><?= $_SESSION['notification'] ?></div>
    <?php unset($_SESSION['notification']); ?>
  <?php endif; ?>

  <!-- Create Case -->
  <div class="card">
    <h2>Create New Case</h2>
    <form method="POST">
      <input type="hidden" name="create_case" value="1">

      <label>Report ID</label>
      <input type="number" name="report_id" required>

      <label>Assign Agency</label>
      <select name="assigned_agency" required>
        <option value="">Select Agency</option>
        <optgroup label="NGOs">
          <option value="Transparency International Bangladesh (TIB)">Transparency International Bangladesh (TIB)</option>
          <option value="BLAST">Bangladesh Legal Aid and Services Trust (BLAST)</option>
          <option value="Odhikar">Odhikar</option>
          <option value="Manusher Jonno Foundation">Manusher Jonno Foundation</option>
          <option value="Ain o Salish Kendra (ASK)">Ain o Salish Kendra (ASK)</option>
          <option value="Bangladesh Mahila Parishad">Bangladesh Mahila Parishad</option>
          <option value="Naripokkho">Naripokkho</option>
          <option value="BRAC">BRAC (Gender Justice & Diversity)</option>
          <option value="BNWLA">Bangladesh National Women Lawyers’ Association (BNWLA)</option>
          <option value="Acid Survivors Foundation (ASF)">Acid Survivors Foundation (ASF)</option>
          <option value="Bangladesh Mahila Samity">Bangladesh Mahila Samity</option>
          <option value="Steps Towards Development">Steps Towards Development</option>
          <option value="Shokhi Network">Shokhi Network</option>
          <option value="BELA">Bangladesh Environmental Lawyers’ Association (BELA)</option>
          <option value="Red Crescent">Bangladesh Red Crescent Society</option>
          <option value="BDPC">Bangladesh Disaster Preparedness Centre (BDPC)</option>
          <option value="Dhaka Community Hospital Trust">Dhaka Community Hospital Trust</option>
          <option value="WaterAid Bangladesh">WaterAid Bangladesh</option>
          <option value="Nijera Kori">Nijera Kori</option>
          <option value="Shakti Foundation">Shakti Foundation</option>
        </optgroup>
        <optgroup label="Government Authorities">
          <option value="Anti-Corruption Commission">Anti-Corruption Commission</option>
          <option value="Ministry of Finance">Ministry of Finance (Audit & Accounts)</option>
          <option value="Comptroller and Auditor General">Comptroller and Auditor General’s Office</option>
          <option value="Ministry of Women and Children Affairs">Ministry of Women and Children Affairs</option>
          <option value="Department of Women Affairs">Department of Women Affairs</option>
          <option value="National Human Rights Commission">National Human Rights Commission</option>
          <option value="Ministry of Law, Justice">Ministry of Law, Justice and Parliamentary Affairs</option>
          <option value="Family Courts">Family Courts</option>
          <option value="Ministry of Disaster Management">Ministry of Disaster Management and Relief</option>
          <option value="Department of Environment (DoE)">Department of Environment (DoE)</option>
          <option value="Fire Service & Civil Defence">Fire Service & Civil Defence</option>
          <option value="Ministry of Home Affairs">Ministry of Home Affairs</option>
          <option value="National Board of Revenue">National Board of Revenue (NBR)</option>
          <option value="Land Ministry">Land Ministry</option>
        </optgroup>
        <optgroup label="Police">
          <option value="CID (Economic Crimes Unit)">CID (Economic Crimes Unit)</option>
          <option value="Metropolitan Police (Financial Crimes)">Metropolitan Police (Financial Crimes)</option>
          <option value="Women Support Division">Women Support & Investigation Division</option>
          <option value="Victim Support Centre">Victim Support Centre</option>
          <option value="Dowry Suppression Cells">Family Violence & Dowry Suppression Cells</option>
          <option value="District Women Police">District Women Police Units</option>
          <option value="Disaster Cell">Disaster Cell (Local Police)</option>
          <option value="Highway Police">Highway Police</option>
          <option value="Detective Branch (DB)">Detective Branch (DB)</option>
          <option value="Rapid Action Battalion (RAB)">Rapid Action Battalion (RAB)</option>
          <option value="Local Thana">Local Thana</option>
        </optgroup>
      </select>

      <button type="submit">Create Case</button>
    </form>
  </div>

  <!-- List Cases -->
  <div class="case-list">
    <h2>Existing Cases</h2>
    <?php foreach ($cases as $case): ?>
      <div class="card">
        <h3>Case #<?= $case['Case_ID'] ?> (Report #<?= $case['Report_ID'] ?>)</h3>
        <p><b>Status:</b> <?= $case['Status'] ?></p>
        <p><b>Assigned Agency:</b> <?= $case['Assigned_Agency'] ?></p>
        <div class="timeline">
          <b>Timeline:</b>
          <?php 
          $timeline = json_decode($case['Timeline'], true);
          if ($timeline) {
              foreach ($timeline as $t) {
                  echo "<div>📌 {$t['status']} — {$t['date']} ({$t['note']})</div>";
              }
          }
          ?>
        </div>

        <!-- Update Form -->
        <form method="POST" style="margin-top:15px;">
          <input type="hidden" name="update_case" value="1">
          <input type="hidden" name="case_id" value="<?= $case['Case_ID'] ?>">

          <label>Update Status</label>
          <select name="status" required>
            <option value="Received">Received</option>
            <option value="Verified">Verified</option>
            <option value="Under Investigation">Under Investigation</option>
            <option value="Action Taken">Action Taken</option>
            <option value="Closed">Closed</option>
          </select>

          <label>Note</label>
          <textarea name="note" placeholder="Add a note..."></textarea>

          <button type="submit">Update Case</button>
        </form>
      </div>
    <?php endforeach; ?>
  </div>
</div>
</body>
</html>

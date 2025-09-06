
<?php
session_start();
include 'DBconnect.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
// Include the database connection or set it up
function h($v) { 
    return htmlspecialchars($v ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); 
}

// Category options
$defaults = [
    'Corruption' => 'Corruption & Bribery',
    'Harassment' => 'Harassment & Abuse',
    'Public Hazards' => 'Public Hazards',
    'Antisocial Behavior' => 'Antisocial Behavior',
    'Dowry Violence' => 'Dowry Violence',
 
];

// Create the table if it doesn't exist (add Category column)
$conn->query("CREATE TABLE IF NOT EXISTS blogposts (
    Post_ID INT AUTO_INCREMENT PRIMARY KEY,
    Title VARCHAR(255) NOT NULL,
    Slug VARCHAR(300) GENERATED ALWAYS AS (REPLACE(LOWER(Title), ' ', '-')) STORED,
    Content MEDIUMTEXT NOT NULL,
    Category VARCHAR(50) NOT NULL,
    Date_Published DATE NOT NULL,
    Author_ID INT NULL,
    Status ENUM('draft', 'published') DEFAULT 'published',
    Published_At DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX (Slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

if (empty($_SESSION['csrf_token'])) { $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); }

$error = '';
$success = false;
$published_post = null;

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        $error = 'Invalid form submission. Please refresh and try again.';
    } else {
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $category = $_POST['category'] ?? '';
        $author_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 1;
        $date_published = date('Y-m-d');

        $errs = [];
        if ($title === '') { $errs[] = 'Please enter a blog title.'; }
        if (mb_strlen($title) > 255) { $errs[] = 'Title must not exceed 255 characters.'; }
        if ($content === '') { $errs[] = 'Please enter the blog content.'; }
        if (mb_strlen($content) > 20000) { $errs[] = 'Content seems too long (max 20,000 characters).'; }
        if (!isset($defaults[$category])) { $errs[] = 'Please select a valid category.'; }

        if (!empty($errs)) {
            $error = implode(' ', $errs);
        } else {
            $sql = "INSERT INTO blogposts (Title, Content, Category, Date_Published, Author_ID, Status, Published_At) VALUES (?,?,?,?,?,'published', NOW())";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param('ssssi', $title, $content, $category, $date_published, $author_id);
                if ($stmt->execute()) {
                    $new_id = $conn->insert_id;
                    
                    // Fetch the published post details
                    $fetch_sql = "SELECT * FROM blogposts WHERE Post_ID = ?";
                    $fetch_stmt = $conn->prepare($fetch_sql);
                    $fetch_stmt->bind_param('i', $new_id);
                    $fetch_stmt->execute();
                    $fetch_result = $fetch_stmt->get_result();
                    $published_post = $fetch_result->fetch_assoc();
                    $fetch_stmt->close();
                    
                    $success = true;
                } else {
                    $error = 'Failed to publish blog post: ' . h($stmt->error);
                }
                $stmt->close();
            } else {
                $error = 'Failed to prepare statement: ' . h($conn->error);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Create & Publish Blog - Truth Uncovered</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body {
      
      font-family:'Inter', -apple-system, BlinkMacSystemFont, sans-serif; line-height:1.6; color:#e5e7eb;
    background: linear-gradient(135deg, #0f0f23, #1a1a2e, #16213e, #0f3460, #1e3a8a);
      color: #fff;
           min-height:100vh; overflow-x:hidden;
          
          
          }
    .bg-animation { position:fixed; inset:0; pointer-events:none; z-index:-1; }
    .floating-orb { position:absolute; border-radius:50%; background:linear-gradient(45deg, rgba(59,130,246,0.1), rgba(147,51,234,0.1)); animation: float 6s ease-in-out infinite; }
    .orb1 { width: 300px; height: 300px; top: 10%; left: 80%; animation-delay: 0s; }
    .orb2 { width: 200px; height: 200px; top: 70%; left: 10%; animation-delay: 2s; }
    .orb3 { width: 150px; height: 150px; top: 30%; left: 20%; animation-delay: 4s; }
    @keyframes float { 0%,100%{transform:translate(0,0) rotate(0)} 33%{transform:translate(30px,-30px) rotate(120deg)} 66%{transform:translate(-20px,20px) rotate(240deg)} }

    header { background: rgba(255,255,255,0.05); backdrop-filter: blur(20px); border-bottom:1px solid rgba(255,255,255,0.1); position:sticky; top:0; z-index:100; }
    nav { display:flex; justify-content:space-between; align-items:center; padding:1rem 1.2rem; }
    .logo {cursor:pointer; font-size:1.4rem; font-weight:800; background: linear-gradient(135deg, #3b82f6, #8b5cf6); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }
    .container { max-width:800px; margin:0 auto; padding:1.2rem; }

    .glass { background: rgba(255,255,255,0.1); backdrop-filter: blur(15px); border:1px solid rgba(255,255,255,0.15); border-radius:16px; padding:1.5rem; margin:1.2rem 0; color:#e2e8f0; }
    .title { color:#fff; font-weight:800; font-size:1.4rem; margin-bottom:.3rem; }
    .muted { color:#94a3b8; font-size:.95rem; }

    .form { display:grid; gap:1.5rem; }
    .form-label { display:block; margin-bottom:.5rem; color:#e2e8f0; font-weight:600; font-size: 1rem; }
    .input, .textarea, 
    .select { width:100%; padding:1rem; border-radius:12px; border:1px solid rgba(255,255,255,0.2);
       background: rgba(255,255,255,0.1); color:#fff; outline:none; font-size: 1rem;
       appearance: none; }
        .select:focus { border-color: rgba(59,130,246,0.5); box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
        .select option {
     background: rgba(31, 29, 29, 0.95); /* grayish background */
  color: #fff;      /* white text for options */
}
    .textarea { min-height:300px; resize:vertical; font-family: inherit; }
    .input::placeholder, .textarea::placeholder { color:#cbd5e1; }
    .input:focus, .textarea:focus, 
   

    .primary-btn { padding:1rem 2rem; background: linear-gradient(135deg, #10b981, #059669); color:#fff; border:none; border-radius:12px; cursor:pointer; font-weight:700; font-size: 1rem; transition: all 0.2s ease; }
    .primary-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(16,185,129,0.3); }

    .success { background: rgba(16,185,129,0.15); border:1px solid rgba(16,185,129,0.3); color:#ecfdf5; padding:1.2rem; border-radius:12px; margin-bottom:1.5rem; }
    .error { background: rgba(239,68,68,0.15); border:1px solid rgba(239,68,68,0.3); color:#fee2e2; padding:1.2rem; border-radius:12px; margin-bottom:1.5rem; }
    
    .published-post { background: rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.2); border-radius:12px; padding:1.5rem; margin-top:1.5rem; }
    .published-post .post-title { font-size: 1.3rem; font-weight: 700; color: #10b981; margin-bottom: 1rem; }
    .published-post .post-meta { color: #94a3b8; margin-bottom: 1rem; font-size: 0.9rem; }
    .published-post .post-content { color: #cbd5e1; line-height: 1.7; }
    
    .reset-notice { background: rgba(59,130,246,0.1); border:1px solid rgba(59,130,246,0.3); color:#dbeafe; padding:1rem; border-radius:8px; margin-top:1rem; font-size: 0.9rem; }
  </style>
</head>
<body>
  <div class="bg-animation">
    <div class="floating-orb orb1"></div>
    <div class="floating-orb orb2"></div>
    <div class="floating-orb orb3"></div>
  </div>

  <header>
    <nav class="container">
      <div class="logo" onclick="window.location.href='index.php'">TRUTHUNCOVERED</div>
 <div style="color:#cbd5e1; font-size:.95rem; cursor:pointer" onclick="window.location.href='profile.php'">
    <?php if (isset($_SESSION['username'])): ?>
        Welcome, <strong style="color:#fff;"><?= htmlspecialchars($_SESSION['username']) ?></strong>
    <?php endif; ?>
</div>

    </nav>
  </header>

  <main class="container">
    <section class="glass">
      <div class="title">✍️ Write & Publish Blog</div>
      <p class="muted" style="margin-bottom:1.5rem;">Create and instantly publish your blog post to Truth Uncovered.</p>

      <?php if ($success && $published_post): ?>
        <div class="success">🎉 <strong>Blog post published successfully!</strong> Your content is now live.</div>
      <?php endif; ?>

      <?php if (!empty($error)): ?>
        <div class="error">❌ <?= h($error) ?></div>
      <?php endif; ?>

      <?php if (!$success): ?>
        <form class="form" method="post" action="">
          <div>
            <label class="form-label" for="title">📝 Blog Title</label>
            <input id="title" name="title" type="text" class="input" maxlength="255" 
                   placeholder="Enter your blog title here..." 
                   value="<?= !empty($error) ? h($_POST['title'] ?? '') : '' ?>" required />
          </div>

          <div>
            <label class="form-label" for="category">📂 Category</label>
            <select id="category" name="category" class="select" required>
              <option value="">Select a category</option>
              <?php foreach ($defaults as $slug => $label): ?>
                <option value="<?= h($slug) ?>" <?= (!empty($_POST['category']) && $_POST['category'] === $slug) ? 'selected' : '' ?>>
                  <?= h($label) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="form-label" for="content">📄 Blog Content</label>
         <textarea id="content" name="content" class="textarea" 
          placeholder="Write your blog content here..." required><?= isset($_POST['content']) ? h($_POST['content']) : '' ?></textarea>

          </div>

          <input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_token']) ?>" />

          <div>
            <button type="submit" class="primary-btn">🚀 Publish Blog Post</button>
          </div>
        </form>
      <?php endif; ?>

      <?php if ($success && $published_post): ?>
        <div class="published-post">
          <div class="post-title">✅ Published: <?= h($published_post['Title']) ?></div>
          <div class="post-meta">
            <strong>Post ID:</strong> <?= (int)$published_post['Post_ID'] ?> | 
            <strong>Category:</strong> <?= h($defaults[$published_post['Category']] ?? $published_post['Category']) ?> | 
            <strong>Published:</strong> <?= date('F j, Y \a\t g:i A', strtotime($published_post['Published_At'])) ?> | 
            <strong>Status:</strong> Published
          </div>
          <div class="post-content">
            <?= nl2br(h($published_post['Content'])) ?>
          </div>
          <div class="reset-notice">
            ✨ <strong>Ready for next post:</strong> <a href="<?= h($_SERVER['PHP_SELF']) ?>" style="color: #93c5fd; text-decoration: none;">Click here to write another blog post</a>
          </div>
        </div>
      <?php endif; ?>
    </section>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Auto-focus on title field when page loads (if form is visible)
      const titleField = document.getElementById('title');
      if (titleField) {
        titleField.focus();
      }
      
      // Add submit confirmation
      const form = document.querySelector('form');
      if (form) {
        form.addEventListener('submit', function(e) {
          const title = document.getElementById('title').value.trim();
          const content = document.getElementById('content').value.trim();
          const category = document.getElementById('category').value.trim();
          
          if (title && content && category) {
              // Show loading state
              const submitBtn = this.querySelector('button[type="submit"]');
              submitBtn.style.opacity = '0.6';
              submitBtn.innerHTML = '⏳ Publishing...';
        
        });
      }
    });
  </script>
</body>
</html>


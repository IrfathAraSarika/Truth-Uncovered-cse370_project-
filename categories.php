
<?php
session_start();

include 'DBconnect.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
mysqli_report(MYSQLI_REPORT_OFF);

// Function to escape special characters for safety
function h($v) {
    return htmlspecialchars($v ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Category defaults (for label lookup)
$defaults = [
    'corruption' => 'Corruption & Bribery',
    'harassment' => 'Harassment & Abuse',
    'infrastructure' => 'Infrastructure Issues',
    'education' => 'Education System',
    'healthcare' => 'Healthcare',
    'environment' => 'Environmental Issues',
    'success-story' => 'Success Story',
    'awareness' => 'Public Awareness',
    'other' => 'Other',
];

// Image map keyed by category slug (DB doesn't store image URLs)
$imageMap = [
    'corruption' => 'https://example.com/images/corruption.jpg',
    'harassment' => 'https://example.com/images/harassment.jpg',
    'infrastructure' => 'https://example.com/images/infrastructure.jpg',
    'education' => 'https://example.com/images/education.jpg',
    'healthcare' => 'https://example.com/images/healthcare.jpg',
    'environment' => 'https://example.com/images/environment.jpg',
    'success-story' => 'https://example.com/images/success_story.jpg',
    'awareness' => 'https://example.com/images/awareness.jpg',
    'other' => 'https://example.com/images/other.jpg',
];

// Placeholder image if category image not found
$placeholderImg = 'https://images.unsplash.com/photo-1520607162513-77705c0f0d4a?q=80&w=1200&auto=format&fit=crop';

// Load categories from DB (id, slug, name)
$categories = [];
$res = $conn->query("SELECT id, slug, name FROM categories ORDER BY name");

if ($res) {
    while ($row = $res->fetch_assoc()) {
        $slug = (string)$row['slug'];
        $categories[$slug] = [
            'id' => (int)$row['id'],
            'slug' => $slug,
            'label' => (string)$row['name'],
            'image' => $imageMap[$slug] ?? $placeholderImg,
        ];
    }
}

// Fallback if table is empty (default categories)
if (empty($categories)) {
    foreach ($defaults as $slug => $label) {
        $categories[$slug] = [
            'id' => null,
            'slug' => $slug,
            'label' => $label,
            'image' => $imageMap[$slug] ?? $placeholderImg,
        ];
    }
}

// Get selected category from URL query string
$selected = trim($_GET['category'] ?? '');
$selectedInfo = $selected && isset($categories[$selected]) ? $categories[$selected] : null;

// Fetch blogposts for the selected category
$blogposts = [];
if ($selectedInfo) {
    $stmt = $conn->prepare("SELECT Post_ID, Title, Content, Author_ID, Date_Published, Published_At, Status FROM blogposts WHERE Category = ? AND Status = 'published' ORDER BY Published_At DESC");
    $stmt->bind_param('s', $selected);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $blogposts[] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Categories - Truth Uncovered</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; line-height: 1.6; color: #e5e7eb; background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 25%, #16213e 50%, #0f3460 75%, #1e3a8a 100%); min-height: 100vh; overflow-x: hidden; }
        .bg-animation { position: fixed; inset: 0; pointer-events: none; z-index: -1; }
        .floating-orb { position: absolute; border-radius: 50%; background: linear-gradient(45deg, rgba(59,130,246,.1), rgba(147,51,234,.1)); animation: float 6s ease-in-out infinite; }
        .orb1 { width: 300px; height: 300px; top: 10%; left: 80%; animation-delay: 0s; }
        .orb2 { width: 200px; height: 200px; top: 70%; left: 10%; animation-delay: 2s; }
        .orb3 { width: 150px; height: 150px; top: 30%; left: 20%; animation-delay: 4s; }
        @keyframes float { 0%, 100% { transform: translate(0, 0) rotate(0); } 33% { transform: translate(30px, -30px) rotate(120deg); } 66% { transform: translate(-20px, 20px) rotate(240deg); } }
        header { background: rgba(255,255,255,.05); backdrop-filter: blur(20px); border-bottom: 1px solid rgba(255,255,255,.1); position: sticky; top: 0; z-index: 100; }
        nav { display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.2rem; }
        .logo { font-size: 1.4rem; font-weight: 800; background: linear-gradient(135deg, #3b82f6, #8b5cf6); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .container { max-width: 1100px; margin: 0 auto; padding: 1.2rem; }
        .glass { background: rgba(255,255,255,.1); backdrop-filter: blur(15px); border: 1px solid rgba(255,255,255,.15); border-radius: 16px; padding: 1.4rem; margin: 1.2rem 0; color: #e2e8f0; }
        .title { color: #fff; font-weight: 800; font-size: 1.4rem; margin-bottom: .3rem; }
        .muted { color: #94a3b8; font-size: .95rem; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 1rem; }
        .card { background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); border-radius: 14px; overflow: hidden; cursor: pointer; transition: .25s transform ease, .25s box-shadow ease; }
        .card:hover { transform: translateY(-2px); box-shadow: 0 10px 26px rgba(0,0,0,.25); }
        .img { width: 100%; height: 140px; object-fit: cover; display: block; }
        .label { padding: .9rem 1rem; text-align: center; font-weight: 700; color: #93c5fd; }
        .selector { display: flex; gap: .6rem; flex-wrap: wrap; margin: .8rem 0; }
        .input, .select { width: 100%; padding: .8rem 1rem; border-radius: 12px; border: 1px solid rgba(255,255,255,.2); background: rgba(255,255,255,.1); color: #fff; outline: none; }
        .primary-btn { padding: .8rem 1.1rem; background: linear-gradient(135deg, #3b82f6, #8b5cf6); color: #fff; border: none; border-radius: 12px; cursor: pointer; font-weight: 700; }
        .link-btn { padding: .8rem 1.1rem; background: rgba(255,255,255,.12); color: #fff; border-radius: 12px; text-decoration: none; display: inline-block; }
        .highlight { padding: 1rem; border-left: 4px solid #3b82f6; background: rgba(59,130,246,.12); border-radius: 10px; }
        .blog-list { margin-top: 2rem; }
        .blog-card { background: rgba(255,255,255,0.07); border: 1px solid rgba(59,130,246,0.13); border-radius: 12px; margin-bottom: 1.2rem; padding: 1.2rem 1.2rem 1rem 1.2rem; }
        .blog-title { color: #38bdf8; font-size: 1.15rem; font-weight: 700; margin-bottom: .5rem; }
        .blog-meta { color: #a5b4fc; font-size: .93rem; margin-bottom: .7rem; }
        .blog-content { color: #e0e7ef; font-size: 1rem; line-height: 1.7; }
        .no-posts { color: #fca5a5; background: rgba(239,68,68,0.08); border-radius: 8px; padding: 1rem; margin-top: 1.5rem; }
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
        <div class="logo">TRUTH UNCOVERED</div>
        <div style="color:#cbd5e1;font-size:.95rem;">
            <?php if (isset($_SESSION['username'])): ?>
                Welcome, <strong style="color:#fff;"><?= h($_SESSION['username']) ?></strong>
            <?php elseif (isset($_SESSION['email'])): ?>
                Logged in as <strong style="color:#fff;"><?= h($_SESSION['email']) ?></strong>
            <?php else: ?>
                <a href="login.php" style="color:#93c5fd;text-decoration:none;">Login</a>
            <?php endif; ?>
            &nbsp;|&nbsp; <a href="index.php" style="color:#a78bfa;text-decoration:none;">Dashboard</a>
            &nbsp;|&nbsp; <a href="report_submission.php" style="color:#93c5fd;text-decoration:none;">Submit Report</a>
            &nbsp;|&nbsp; <a href="blogposts.php" style="color:#93c5fd;text-decoration:none;">Write Blog</a>
        </div>
    </nav>
</header>

<main class="container">
    <section class="glass">
        <div class="title">📚 Browse Categories</div>
        <p class="muted">Choose a category to explore related reports and posts.</p>

        <form class="selector" method="get" action="">
            <select name="category" class="select" required>
                <option value="">Select a category</option>
                <?php foreach ($categories as $slug => $cat): ?>
                    <option value="<?= h($slug) ?>" <?= $selected === $slug ? 'selected' : '' ?>><?= h($cat['label']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="primary-btn">Explore</button>
            <a class="link-btn" href="<?= h($_SERVER['PHP_SELF']) ?>">Reset</a>
        </form>

        <?php if ($selectedInfo): ?>
            <div class="glass" style="margin:.8rem 0;">
                <div class="title" style="font-size:1.1rem;"><?= h($selectedInfo['label']) ?></div>
                <div class="highlight" style="margin-top:.6rem;">
                    Explore all reports and blogs related to <strong><?= h($selectedInfo['label']) ?></strong>.
                </div>
                <img src="<?= h($selectedInfo['image']) ?>" alt="<?= h($selectedInfo['label']) ?>" style="width:100%;border-radius:12px;margin-top:12px;"/>
                <div style="margin-top:.8rem;display:flex;gap:.6rem;flex-wrap:wrap;">
                    <a class="primary-btn" href="report_submission.php?prefill_category=<?= h($selectedInfo['slug']) ?>">Submit a Report</a>
                    <a class="link-btn" href="category_posts.php?category=<?= h($selectedInfo['slug']) ?>">View Posts</a>
                </div>
            </div>

            <div class="blog-list">
                <div class="title" style="font-size:1.1rem;margin-bottom:.7rem;">📝 Blog Posts in "<?= h($selectedInfo['label']) ?>"</div>
                <?php if (count($blogposts) > 0): ?>
                    <?php foreach ($blogposts as $post): ?>
                        <div class="blog-card">
                            <div class="blog-title"><?= h($post['Title']) ?></div>
                            <div class="blog-meta">
                                Published: <?= date('F j, Y', strtotime($post['Published_At'])) ?>
                                <?php if (!empty($post['Author_ID'])): ?>
                                    | Author ID: <?= (int)$post['Author_ID'] ?>
                                <?php endif; ?>
                                | Status: <?= h(ucfirst($post['Status'])) ?>
                            </div>
                            <div class="blog-content"><?= nl2br(h(mb_strimwidth($post['Content'], 0, 400, '...'))) ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-posts">No blog posts found in this category.</div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="grid">
            <?php foreach ($categories as $slug => $cat): ?>
                <div class="card" onclick="window.location='?category=<?= h($slug) ?>'">
                    <img class="img" src="<?= h($cat['image']) ?>" alt="<?= h($cat['label']) ?>">
                    <div class="label"><?= h($cat['label']) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>
</body>


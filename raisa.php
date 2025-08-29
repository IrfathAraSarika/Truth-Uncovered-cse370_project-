<?php
// Categories data with image URLs
$categories = [
    "corruption" => ["label" => "Corruption & Bribery", "image" => "https://example.com/images/corruption.jpg"],
    "harassment" => ["label" => "Harassment & Abuse", "image" => "https://example.com/images/harassment.jpg"],
    "infrastructure" => ["label" => "Infrastructure Issues", "image" => "https://example.com/images/infrastructure.jpg"],
    "education" => ["label" => "Education System", "image" => "https://example.com/images/education.jpg"],
    "healthcare" => ["label" => "Healthcare", "image" => "https://example.com/images/healthcare.jpg"],
    "environment" => ["label" => "Environmental Issues", "image" => "https://example.com/images/environment.jpg"],
    "success-story" => ["label" => "Success Story", "image" => "https://example.com/images/success_story.jpg"],
    "awareness" => ["label" => "Public Awareness", "image" => "https://example.com/images/awareness.jpg"],
    "other" => ["label" => "Other", "image" => "https://example.com/images/other.jpg"],
];

$selected_category = isset($_GET['category']) ? $_GET['category'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Colorful Blog Categories</title>
<style>
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #fafafa;
    margin: 0; padding: 40px 20px;
    display: flex; justify-content: center;
  }
  .container {
    width: 100%;
    max-width: 960px;
  }
  h1 {
    text-align: center;
    color: #222;
    margin-bottom: 45px;
    font-size: 2.5rem;
  }
  form {
    display: flex;
    justify-content: center;
    margin-bottom: 40px;
    gap: 16px;
  }
  select {
    padding: 12px 18px;
    font-size: 1rem;
    border-radius: 10px;
    border: 2px solid #ddd;
    outline: none;
    transition: border-color 0.3s ease;
    min-width: 240px;
  }
  select:focus {
    border-color: #0078d4;
    box-shadow: 0 0 10px #0078d4b0;
  }
  button {
    padding: 12px 30px;
    background: #0078d4;
    border: none;
    border-radius: 10px;
    color: white;
    font-weight: 700;
    font-size: 1.1rem;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }
  button:hover {
    background: #005a9e;
  }
  .grid {
    display: grid;
    grid-template-columns: repeat(auto-fill,minmax(260px,1fr));
    gap: 30px;
  }
  .category-card {
    background: white;
    border-radius: 14px;
    box-shadow: 0 5px 18px rgba(0,0,0,0.1);
    overflow: hidden;
    cursor: pointer;
    transition: 0.3s box-shadow ease;
  }
  .category-card:hover {
    box-shadow: 0 9px 26px rgba(0,0,0,0.18);
  }
  .category-image {
    width: 100%;
    height: 160px;
    object-fit: cover;
    display: block;
  }
  .category-label {
    padding: 18px 15px;
    font-size: 1.2rem;
    font-weight: 700;
    color: #0078d4;
    text-align: center;
  }
  .selected-category-info {
    max-width: 700px;
    margin: 0 auto 50px;
    padding: 25px 20px;
    border-left: 5px solid #0078d4;
    background-color: #e6f0fa;
    color: #004a80;
    font-size: 1.1rem;
  }
</style>
</head>
<body>
  <div class="container">
    <h1>Select a Category</h1>
    <form method="get" action="">
      <select name="category" required>
        <option value="">Choose a category</option>
        <?php foreach ($categories as $slug => $cat): ?>
          <option value="<?php echo htmlspecialchars($slug); ?>" <?php if ($selected_category == $slug) echo "selected"; ?>>
            <?php echo htmlspecialchars($cat['label']); ?>
          </option>
        <?php endforeach; ?>
      </select>
      <button type="submit">Explore</button>
    </form>

    <?php if ($selected_category && isset($categories[$selected_category])): ?>
      <div class="selected-category-info">
        <h2><?php echo htmlspecialchars($categories[$selected_category]['label']); ?></h2>
        <p>Explore all reports and blogs related to <strong><?php echo htmlspecialchars($categories[$selected_category]['label']); ?></strong>.</p>
        <img src="<?php echo htmlspecialchars($categories[$selected_category]['image']); ?>" alt="<?php echo htmlspecialchars($categories[$selected_category]['label']); ?>" style="width:100%; border-radius: 12px; margin-top: 15px;" />
      </div>
    <?php endif; ?>

    <div class="grid">
      <?php foreach ($categories as $slug => $cat): ?>
        <div class="category-card" onclick="window.location.href='?category=<?php echo htmlspecialchars($slug); ?>'">
          <img src="<?php echo htmlspecialchars($cat['image']); ?>" alt="<?php echo htmlspecialchars($cat['label']); ?>" class="category-image" />
          <div class="category-label"><?php echo htmlspecialchars($cat['label']); ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</body>
</html>

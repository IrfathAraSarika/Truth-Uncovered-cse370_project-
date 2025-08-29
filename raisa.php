<?php
// Database connection setup
$servername = "localhost";  // Typically localhost
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "truth_uncovered";  // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);

    if (!empty($title) && !empty($content)) {
        $sql = "INSERT INTO blog_posts (title, content) VALUES ('$title', '$content')";
        if ($conn->query($sql) === TRUE) {
            $message = "New blog post created successfully!";

        } else {
            $message = "Error: " . $conn->error;
        }
    } else {
        $message = "Please fill in both title and content.";
    }
}

// Close connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Create a Blog Post</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f9f9f9;
            margin: 0; padding: 0;
            display: flex;
            justify-content: center;
            padding: 40px 20px;
        }
        .container {
            background: white;
            width: 100%;
            max-width: 640px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 30px 40px;
        }
        h1 {
            text-align: center;
            color: #222;
            margin-bottom: 30px;
            font-weight: 700;
            letter-spacing: 1.2px;
        }
        form label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #444;
            font-size: 16px;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 12px 15px;
            border-radius: 8px;
            border: 1.5px solid #ddd;
            font-size: 15px;
            font-family: inherit;
            resize: vertical;
            transition: border-color 0.3s ease;
        }
        input[type="text"]:focus,
        textarea:focus {
            outline: none;
            border-color: #4a90e2;
            box-shadow: 0 0 6px #4a90e2b3;
            background: #f0f7ff;
        }
        input[type="submit"] {
            background: #4a90e2;
            color: white;
            border: none;
            padding: 14px 20px;
            margin-top: 20px;
            border-radius: 8px;
            width: 100%;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background: #357ab8;
        }
        p.message {
            margin: 20px 0;
            text-align: center;
            font-size: 16px;
            color: #555;
            padding: 12px 20px;
            border-radius: 8px;
            background-color: #e3f2fd;
            border: 1px solid #90caf9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Create a Blog Post</h1>
        <?php if(!empty($message)): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <form method="post" action="">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required placeholder="Enter your blog title" />

            <label for="content">Content:</label>
            <textarea id="content" name="content" rows="10" cols="50" required placeholder="Write your blog content here..."></textarea>

            <input type="submit" value="Post Blog" />
        </form>
    </div>
</body>
</html>

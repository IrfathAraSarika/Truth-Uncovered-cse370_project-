<?php
session_start();
include 'DBconnect.php'; 

$message = "";

// Your query (fetch all users once)
$sql = "SELECT User_ID, Name, Email, Password FROM Users";
$result = $conn->query($sql);
$users = $result->fetch_all(MYSQLI_ASSOC);

// Debug: log all users to console
echo "<script>console.log(" . json_encode($users) . ");</script>";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    $found = false;

    foreach ($users as $user) {
        if ($email === $user['Email']) {
            // If password is hashed, verify with password_verify
            if (!empty($user['Password']) && password_verify($password, $user['Password'])) {
                $_SESSION['user_id'] = $user['User_ID'];
                $_SESSION['email']   = $user['Email'];
                header("Location: index.php");
                exit;
            }
            // In case some accounts still have plain text (not recommended)
            elseif ($password === $user['Password']) {
                $_SESSION['user_id'] = $user['User_ID'];
                $_SESSION['email']   = $user['Email'];
                header("Location: index.php");
                exit;
            }
        }
    }

    // If no match found
    $message = "❌ Invalid email or password.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>The Pixel Truth: Uncovered</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
<style>
    /* Reset & body */
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
        font-family: 'Montserrat', sans-serif;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        background: url('https://images.unsplash.com/photo-1522199710521-72d69614c702?auto=format&fit=crop&w=1350&q=80') no-repeat center center/cover;
    }

    /* Glass container */
    .login-box {
        backdrop-filter: blur(12px) saturate(180%);
        -webkit-backdrop-filter: blur(12px) saturate(180%);
        background-color: rgba(255, 255, 255, 0.15);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        padding: 40px 30px;
        width: 360px;
        box-shadow: 0 8px 32px 0 rgba(0,0,0,0.37);
        color: #fff;
    }

    /* Title */
    .login-box h2 {
        text-align: center;
        font-size: 1.8rem;
        margin-bottom: 30px;
        font-weight: 600;
        letter-spacing: 1px;
        color: #fff;
        text-shadow: 1px 1px 10px rgba(0,0,0,0.5);
    }

    /* Form */
    .form-group { margin-bottom: 20px; }
    label {
        display: block;
        font-size: 0.9rem;
        margin-bottom: 5px;
        color: rgba(255,255,255,0.8);
    }
    input {
        width: 100%;
        padding: 12px 15px;
        border-radius: 12px;
        border: none;
        background-color: rgba(255,255,255,0.2);
        color: #fff;
        font-size: 1rem;
        outline: none;
        box-shadow: inset 2px 2px 10px rgba(255,255,255,0.2),
                    inset -2px -2px 10px rgba(0,0,0,0.2);
        transition: background 0.3s;
    }
    input:focus { background-color: rgba(255,255,255,0.35); }

    /* Button */
    .login-btn {
        width: 100%;
        padding: 14px;
        border-radius: 12px;
        border: none;
        font-size: 1rem;
        font-weight: 600;
        background: rgba(255, 255, 255, 0.25);
        color: #fff;
        cursor: pointer;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s ease;
        backdrop-filter: blur(5px);
    }
    .login-btn:hover {
        background: rgba(255,255,255,0.4);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    }

    /* Message */
    .message {
        text-align: center;
        margin-bottom: 15px;
        font-weight: bold;
        color: #ff6b6b;
        text-shadow: 1px 1px 4px rgba(0,0,0,0.4);
    }

    /* Responsive */
    @media (max-width: 400px) {
        .login-box { width: 90%; padding: 30px 20px; }
    }
</style>
</head>
<body>
    <div class="login-box">
        <h2>The Pixel Truth: Uncovered</h2>

        <?php if (!empty($message)): ?>
            <div class="message"><?= $message; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="text" name="email" id="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>
            <button type="submit" class="login-btn">Login</button>
        </form>
    </div>
</body>
</html>

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
    <title>Login</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; background: #f2f2f2; }
        .login-box { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 300px; }
        .login-box h2 { text-align: center; margin-bottom: 15px; }
        .form-group { margin-bottom: 12px; }
        label { font-size: 0.9rem; display: block; margin-bottom: 5px; }
        input { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 6px; }
        .login-btn { width: 100%; padding: 10px; background: #006a4e; border: none; color: #fff; border-radius: 6px; font-weight: bold; cursor: pointer; }
        .message { text-align: center; margin-bottom: 10px; font-weight: bold; color: red; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Login</h2>
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

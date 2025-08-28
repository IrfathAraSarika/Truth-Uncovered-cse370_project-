<?php
include 'DBconnect.php';

// Insert user function
function registerUser($pdo, $data) {
  
    $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("
        INSERT INTO users 
        (Name, Email, Phone, DOB, National_ID, Gender, Role, Street, City, Region, Postal_Code, Sub_SMS, Sub_Email, Sub_Blog_Following, Password) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    return $stmt->execute([
        $data['name'],
        $data['email'],
        $data['phone'],
        $data['dob'],
        $data['national_id'],
        $data['gender'],
        $data['role'],
        $data['street'],
        $data['city'],
        $data['region'],
        $data['postal_code'],
        isset($data['sub_sms']) ? 1 : 0,
        isset($data['sub_email']) ? 1 : 0,
        isset($data['sub_blog']) ? 1 : 0,
        $hashed_password
    ]);
}

// Handle form submit
$success = $error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (registerUser($pdo, $_POST)) {
        $success = "✅ Account created successfully!";
    } else {
        $error = "❌ Something went wrong. Try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Signup - Frosted Glass</title>
<style>
/* Body & background */
body {
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1950&q=80') no-repeat center center fixed;
    background-size: cover;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}

/* Glass Card */
.card {
    backdrop-filter: blur(20px);
    background: rgba(255,255,255,0.12);
    border-radius: 20px;
    padding: 50px 60px;
    max-width: 1000px;
    width: 100%;
    box-shadow: 0 20px 50px rgba(0,0,0,0.5);
    position: relative;
    overflow: hidden;
    animation: fadeIn 1s ease forwards;
}

/* Form grid - 2 columns */
form {
    display: grid;
    grid-template-columns: repeat(2, 1fr); /* 3 equal columns */
   
    gap: 25px 40px;
}

/* Form Group */
.form-group {
    position: relative;
}

input, select {
    width: 100%;
    padding: 15px 12px;
    border-radius: 12px;
    border: 1px solid rgba(255,255,255,0.3);
    background: rgba(255,255,255,0.15);
    color: #fff;
    font-size: 14px;
    transition: all 0.3s ease;
}

input:focus, select:focus {
    border-color: #fff;
    box-shadow: 0 0 20px rgba(255,255,255,0.4);
}

/* Floating label animation */
label {
    position: absolute;
    top: 50%;
    left: 15px;
    transform: translateY(-50%);
    color: rgba(255,255,255,0.7);
    font-size: 14px;
    font-weight: 500;
    pointer-events: none;
    transition: all 0.3s ease;
    opacity: 0;
    animation: labelFlyIn 0.6s forwards;
}

input:focus + label,
input:not(:placeholder-shown) + label,
select:focus + label,
select:not([value=""]) + label {
    top: -10px;
    left: 12px;
    font-size: 12px;
    color: #fff;
}

/* Checkbox group container */
.checkbox-group {
    display: flex;
    flex-wrap: wrap;        /* allow checkboxes to wrap on smaller screens */
    gap: 20px;              /* space between checkboxes */
    margin-top: 10px;
}

/* Individual checkbox labels */
.checkbox-group label {
    display: flex;
    align-items: center;    /* vertically center checkbox and text */
    gap: 5px;               /* space between checkbox and label text */
    color: #fff;            /* make label visible on frosted glass */
    font-size: 14px;
    cursor: pointer;
}

/* Checkbox input styling */
.checkbox-group input[type="checkbox"] {
    width: 18px;
    height: 18px;
    accent-color: #fff;
    cursor: pointer;
    margin: 0;
}

.checkbox-group input[type="checkbox"]:hover {
    transform: scale(1.2);
}
.subscription {
  display:flex;
  justify-content:center;
  align-items:center;
  gap:10px;
}
/* Button */
button {
    grid-column: span 2;
    padding: 16px;
    border: none;
    border-radius: 12px;
    background: rgba(255,255,255,0.25);
    color: #fff;
    font-weight: bold;
    font-size: 16px;
    cursor: pointer;
    backdrop-filter: blur(5px);
    transition: all 0.3s ease;
    box-shadow: 0 0 15px rgba(255,255,255,0.3);
}
.custom-class {
  display:flex;
  gap:30px;
  width:100%;
}
button:hover {
    box-shadow: 0 0 30px rgba(255,255,255,0.6);
    transform: scale(1.05);
}

/* Animations */
@keyframes fadeIn {
    0% { opacity: 0; transform: translateY(20px);}
    100% { opacity: 1; transform: translateY(0);}
}

@keyframes labelFlyIn {
    0% { opacity: 0; transform: translateY(20px);}
    100% { opacity: 1; transform: translateY(-50%);}
}

/* Responsive */
@media (max-width: 700px) {
    form {
        grid-template-columns: 2fr;
        gap: 15px 0;
    }
    button { grid-column: span 1; }
}
</style>
</head>
<body>

<div class="card">
    <h2 style="color:white; text-align:center; margin-bottom:40px;">Sign Up</h2>

    <form method="POST" action="">
        <div class="form-group">
            <input type="text" name="name" placeholder=" " required>
            <label>Full Name</label>
        </div>

        <div class="form-group">
            <input type="email" name="email" placeholder=" " required>
            <label>Email Address</label>
        </div>

        <div class="form-group">
            <input type="text" name="phone" placeholder=" ">
            <label>Phone</label>
        </div>

        <div class="form-group">
            <input type="date" name="dob" placeholder=" ">
            <label>Date of Birth</label>
        </div>

        <div class="form-group">
            <input type="text" name="national_id" placeholder=" ">
            <label>National ID</label>
        </div>

        <div class="form-group">
            <input type="text" name="street" placeholder=" ">
            <label>Street</label>
        </div>

        <div class="form-group">
            <input type="text" name="city" placeholder=" ">
            <label>City</label>
        </div>

        <div class="form-group">
            <input type="text" name="region" placeholder=" ">
            <label>Region</label>
        </div>

        <div class="form-group">
            <input type="text" name="postal_code" placeholder=" ">
            <label>Postal Code</label>
        </div>

        <div class="form-group">
            <select name="gender" required>
                <option value="" disabled selected hidden>Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>
            <label>Gender</label>
        </div>

        <div class="form-group">
            <select name="role" required>
                <option value="" disabled selected hidden>Select Role</option>
                <option value="Citizen">Citizen</option>
                <option value="Admin">Admin</option>
                <option value="NGO_Partner">NGO Partner</option>
                <option value="Govt_Officer">Government Officer</option>
            </select>
            <label>Role</label>
        </div>
  <div class="form-group">
            <input type="password" name="password" placeholder=" " required minlength="6">
            <label>Password</label>
        </div>
    
  <div class="form-group">
         
           
            <div class="custom-class">
                <label>SMS</label><input type="checkbox" name="sub_sms"> 
                  <label> Email</label><input type="checkbox" name="sub_email">
                <label> Blog Following</label><input type="checkbox" name="sub_blog"> 
             
            </div>
        </div>
      

        <button type="submit">Create Account</button>
    </form>
</div>

</body>
</html>



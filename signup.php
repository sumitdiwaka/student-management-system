<?php
    session_start();
    include_once 'database.php';

    $error = '';
    if (isset($_POST['submit'])) {
        $fname            = $_POST['fname'];
        $lname            = $_POST['lname'];
        $email            = $_POST['email'];
        $password         = md5($_POST['password']);
        $confirm_password = md5($_POST['confirm_password']);
        $role             = $_POST['role'];

        if (empty($fname) || empty($lname) || empty($email) || empty($password) || empty($confirm_password)) {
            $error = "All fields are required!";
        } elseif ($password !== $confirm_password) {
            $error = "Passwords don't match!";
        } else {
            $check_email = "SELECT * FROM user WHERE email = '$email'";
            $result      = $conn->query($check_email);

            if ($result->num_rows > 0) {
                $error = "Email already exists!";
            } else {
                $sql = "INSERT INTO user (role, email, password) VALUES ('$role', '$email', '$password')";

                if ($conn->query($sql)) {
                    if ($role == 'Student') {
                        $sid         = 'ST' . time();
                        $insert_role = "INSERT INTO student (sid, fname, lname, email) VALUES ('$sid', '$fname', '$lname', '$email')";
                    } elseif ($role == 'Teacher') {
                        $tid         = 'TC' . time();
                        $insert_role = "INSERT INTO teacher (tid, fname, lname, email) VALUES ('$tid', '$fname', '$lname', '$email')";
                    } elseif ($role == 'Parent') {
                        $pid         = time();
                        $insert_role = "INSERT INTO parent (pid, fname, lname, email) VALUES ('$pid', '$fname', '$lname', '$email')";
                    }

                    if ($conn->query($insert_role)) {
                        $success = "Registration successful! You can now login.";
                        $_SESSION['signup_success'] = $success;
                        header("Location: login.php");
                        exit();
                    } else {
                        $error = "Error creating user profile: " . $conn->error;
                        $conn->query("DELETE FROM user WHERE email = '$email'");
                    }
                } else {
                    $error = "Error: " . $sql . "<br>" . $conn->error;
                }
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up | Student Management System</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: Arial, sans-serif;
    }
    
    body {
      background-color: #f5f5f5;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }
    
    .container {
      background-color: white;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      width: 400px;
      padding: 30px;
      text-align: center;
    }
    
    h1 {
      color: #333;
      margin-bottom: 20px;
      font-size: 24px;
    }
    
    h2 {
      color: #555;
      margin-bottom: 30px;
      font-size: 18px;
    }
    
    .form-group {
      margin-bottom: 20px;
      text-align: left;
    }
    
    label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
      color: #555;
    }
    
    input, select {
      width: 100%;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 16px;
    }
    
    button {
      background-color: #4361ee;
      color: white;
      border: none;
      padding: 12px 20px;
      border-radius: 4px;
      cursor: pointer;
      font-size: 16px;
      width: 100%;
      transition: background-color 0.3s;
    }
    
    button:hover {
      background-color: #3a56d4;
    }
    
    .error {
      color: #f44336;
      margin-bottom: 15px;
      padding: 10px;
      background-color: #ffebee;
      border-radius: 4px;
    }
    
    .success {
      color: #4CAF50;
      margin-bottom: 15px;
    }
    
    .login-link {
      margin-top: 20px;
      color: #666;
    }
    
    .login-link a {
      color: #4361ee;
      text-decoration: none;
    }
    
    .login-link a:hover {
      text-decoration: underline;
    }
    
    .footer {
      margin-top: 30px;
      color: #999;
      font-size: 12px;
    }
    
    .divider {
      border-top: 1px solid #eee;
      margin: 20px 0;
      position: relative;
    }
    
    .divider::before {
      content: "or";
      position: absolute;
      top: -10px;
      left: 50%;
      transform: translateX(-50%);
      background: white;
      padding: 0 10px;
      color: #999;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Student Management System</h1>
    <h2>Sign Up Form</h2>
    
    <?php if ($error): ?>
      <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="post">
      <div class="form-group">
        <label for="role">Role</label>
        <select id="role" name="role" required>
          <option value="">Select Your Role</option>
          <option value="Student">Student</option>
          <option value="Teacher">Teacher</option>
          <option value="Parent">Parent</option>
        </select>
      </div>
      
      <div class="form-group">
        <label for="fname">First Name</label>
        <input type="text" id="fname" name="fname" placeholder="Enter your first name" required>
      </div>
      
      <div class="form-group">
        <label for="lname">Last Name</label>
        <input type="text" id="lname" name="lname" placeholder="Enter your last name" required>
      </div>
      
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Enter your email" required>
      </div>
      
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Enter your password" required>
      </div>
      
      <div class="form-group">
        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
      </div>
      
      <button type="submit" name="submit">Create Account</button>
    </form>
    
    <div class="divider"></div>
    
    <div class="login-link">
      Already have an account? <a href="login.php">Sign In</a>
    </div>
    
    <div class="footer">
      ©2025 All Rights Reserved. Student management system
    </div>
  </div>
</body>
</html>
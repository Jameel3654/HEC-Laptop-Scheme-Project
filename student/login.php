<?php
session_start();
require_once "../database/db.php";

$error = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $q = mysqli_query($conn, "SELECT * FROM hec_users WHERE email='$email' LIMIT 1");

    if(mysqli_num_rows($q) === 1){

        $user = mysqli_fetch_assoc($q);

        if(password_verify($password, $user['password'])){

            if($user['is_verified'] == 1){

                $_SESSION['email']      = $user['email'];
                $_SESSION['student_id'] = $user['student_id'];
                $_SESSION['name']       = $user['name'];

                header("Location: application.php");
                exit;

            } else {
                $error = "Please verify your email first.";
            }

        } else {
            $error = "Incorrect password.";
        }

    } else {
        $error = "Email not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            
            background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('../assets/images/a1.jpg');
            background-size: cover;
            background-position: right center; 
            background-repeat: no-repeat;
            background-attachment: fixed;
            height: 100vh;
            display: flex;
            align-items: center;
            margin: 0;
        }

        .login-card {
           
            background: rgba(255, 255, 255, 0.6); 
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(12px); 
        }

        .form-control {
            background: rgba(255, 255, 255, 0.7);
            border: none;
        }

        .btn-primary {
            padding: 12px;
            font-weight: bold;
            border-radius: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-start ps-md-5">
        <div class="col-md-4">

            <div class="card login-card shadow-lg">
                <div class="card-body p-5">

                    <div class="text-center mb-4">
                        <h3 class="fw-bold text-dark">Student Login</h3>
                        <p class="text-dark opacity-75">Welcome back! Please login</p>
                    </div>

                    <?php if($error): ?>
                        <div class="alert alert-danger text-center py-2" style="font-size: 0.9rem;">
                            <?= $error ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" autocomplete="off">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control" 
                                   placeholder="Enter your email" required autocomplete="off">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Password</label>
                            <input type="password" name="password" class="form-control" 
                                   placeholder="Enter your password" required autocomplete="new-password">
                        </div>

                        <button type="submit" class="btn btn-primary w-100 shadow-sm">Login</button>
                    </form>

                    <div class="text-center mt-4">
                        <p class="mb-0 text-dark">Don't have an account? <a href="signup.php" class="text-decoration-none fw-bold text-primary">Sign Up</a></p>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
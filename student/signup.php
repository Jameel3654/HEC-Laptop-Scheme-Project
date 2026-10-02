<?php
session_start();
include "../database/db.php";
include "../mailer.php"; 

$msg = "";
$name = "";
$email = "";

if(isset($_POST['signup'])){
    $name         = mysqli_real_escape_string($conn, $_POST['name']);
    $email        = mysqli_real_escape_string($conn, $_POST['email']);
    $password     = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];

    $preserveName = $name;
    $preserveEmail = $email;

    if($password !== $confirm_pass){
        $msg = "Passwords do not match!";
    } else {
        $dupCheck = mysqli_query($conn,"SELECT * FROM hec_users WHERE email='$email'");
        if(mysqli_num_rows($dupCheck) > 0){
            $msg = "An account with this email already exists!";
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $verification_code = rand(100000,999999);

            $sql = "INSERT INTO hec_users (name, email, password, is_verified, verification_code)
                    VALUES ('$name','$email','$passwordHash',0,'$verification_code')";

            if(mysqli_query($conn,$sql)){
                $subject = "HEC Laptop Scheme Email Verification";
                $body = "<h3>Hello $name</h3><p>Your verification code is: <strong>$verification_code</strong></p>";
                sendEmail($email, $subject, $body);

                $_SESSION['email_to_verify'] = $email;
                $_SESSION['name_to_verify']  = $name;
                header("Location: verify_email.php");
                exit();
            } else {
                $msg = "Signup Failed: " . mysqli_error($conn);
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
<title>Student Signup</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    body {
        
        background-image: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('../assets/images/a1.jpg');
        background-size: cover;
        background-position: right center; 
        background-repeat: no-repeat;
        background-attachment: fixed;
        height: 100vh;
        display: flex;
        align-items: center;
        margin: 0;
        font-family: 'Inter', 'Segoe UI', Roboto, sans-serif;
    }

    .signup-card {
        background: rgba(255,255,255,0.6); 
        border-radius: 20px;
        border: 1px solid rgba(255,255,255,0.3);
        backdrop-filter: blur(12px);
        padding: 40px 30px;
    }

    .form-control {
        background: rgba(255,255,255,0.7);
        border: none;
    }

    .btn-primary {
        padding: 12px;
        font-weight: bold;
        border-radius: 10px;
    }

    .text-link a {
        text-decoration: none;
        font-weight: bold;
        color: #0d6efd;
    }

    .text-link a:hover {
        text-decoration: underline;
    }

    @media(max-width:768px){
        body {
            background-position: center;
        }
    }
</style>
</head>
<body>

<div class="container">
    <div class="row justify-content-start ps-md-5">
        <div class="col-md-4">

            <div class="signup-card shadow-lg">
                <div class="text-center mb-4">
                    <h3 class="fw-bold text-dark">Student Signup</h3>
                    <p class="text-dark opacity-75">Create your account</p>
                </div>

                <?php if($msg!=""){ echo "<div class='alert alert-danger text-center py-2' style='font-size:0.9rem;'>$msg</div>"; } ?>

                <form method="POST" autocomplete="off">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Full Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter your full name" required value="<?= htmlspecialchars($preserveName ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Enter your email" required value="<?= htmlspecialchars($preserveEmail ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control" placeholder="Confirm your password" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 shadow-sm" name="signup">Sign Up</button>
                </form>

                <div class="text-center mt-4 text-link">
                    <p class="mb-0 text-dark">Already have an account? <a href="login.php">Login</a></p>
                </div>

            </div>

        </div>
    </div>
</div>

</body>
</html>

<?php
session_start();
require_once "../database/db.php";

$error = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $q = mysqli_query($conn, "SELECT * FROM admin WHERE username='$username' LIMIT 1");

    if(mysqli_num_rows($q) === 1){
        $admin = mysqli_fetch_assoc($q);
        if(md5($password) === $admin['password']){
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_id']       = $admin['admin_id'] ?? null;
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "Admin account not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login - HEC Portal</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    body, html {
        height: 100%;
        margin: 0;
        font-family: 'Inter', 'Segoe UI', Roboto, sans-serif;
        overflow-x: hidden;
    }

   
    .bg-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
        background: linear-gradient(rgba(0,0,0,0.45), rgba(0,0,0,0.45)),
                    url('../assets/images/a11.jpg') no-repeat center center;
        background-size: cover;
        filter: blur(5px);
        transform: scale(1.05);
    }

    .login-wrapper {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    /* Glassmorphism Card */
    .admin-card {
        background: rgba(255,255,255,0.75);
        border-radius: 12px;
        backdrop-filter: blur(15px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        transition: all 0.3s ease;
        width: 100%;
        max-width: 380px;
        padding: 40px 30px;
        border: 1px solid rgba(255,255,255,0.2);
    }

    .admin-card:hover {
        background: rgba(255,255,255,0.85);
        border: 1px solid rgba(255,255,255,0.5);
        box-shadow: 0 15px 40px rgba(0,0,0,0.3);
    }

    .login-title {
        font-weight: 700;
        font-size: 1.5rem;
        color: #111;
        text-align: center;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 30px;
    }

    .form-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #444;
        margin-bottom: 6px;
        display: block;
    }

    .form-control {
        border: 1px solid #ced4da;
        padding: 12px;
        border-radius: 5px;
        margin-bottom: 20px;
        font-size: 0.95rem;
    }

    .form-control:focus {
        border-color: #000;
        box-shadow: none;
    }

    .btn-login {
        background: #212529;
        color: #fff;
        padding: 12px;
        font-weight: 700;
        border-radius: 6px;
        text-transform: uppercase;
        width: 100%;
        border: none;
        transition: 0.3s;
    }

    .btn-login:hover {
        background: #000;
    }

    .back-link {
        display: block;
        text-align: center;
        margin-top: 20px;
        font-size: 0.85rem;
        color: #666;
        text-decoration: none;
    }

    .back-link:hover {
        color: #000;
        text-decoration: underline;
    }

    /* Responsive */
    @media (max-width: 480px){
        .admin-card {
            padding: 30px 20px;
        }
        .login-title {
            font-size: 1.3rem;
        }
    }
</style>
</head>
<body>

<div class="bg-container"></div>

<div class="login-wrapper">
    <div class="admin-card">
        <h2 class="login-title">Admin Login</h2>

        <?php if($error): ?>
            <div class="alert alert-danger py-2 text-center" style="font-size:0.85rem; border-radius:4px;">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" autocomplete="off">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" placeholder="Admin ID" required>

            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" placeholder="••••••••" required>

            <button type="submit" class="btn btn-login">Login</button>
        </form>

        <a href="../index.php" class="back-link">Return to Home Portal</a>
    </div>
</div>

</body>
</html>

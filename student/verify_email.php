<?php
session_start();
include "../database/db.php";

$msg = "";
$email = $_SESSION['email_to_verify'] ?? '';

if(!$email){
    header("Location: signup.php");
    exit;
}

if(isset($_POST['verify'])){
    $code = mysqli_real_escape_string($conn, $_POST['verification_code']);

   
    $q = mysqli_query($conn,"SELECT * FROM hec_users WHERE email='$email' AND verification_code='$code'");

    if(mysqli_num_rows($q) > 0){
        $user = mysqli_fetch_assoc($q);
        
        
        mysqli_query($conn,"UPDATE hec_users SET is_verified=1, verification_code=NULL WHERE email='$email'");

        
        $_SESSION['email'] = $user['email'];
        $_SESSION['name']  = $user['name'];
        $_SESSION['student_id'] = $user['student_id'];

        
        unset($_SESSION['email_to_verify'], $_SESSION['name_to_verify']);

        header("Location: application.php");
        exit;
    } else {
        $msg = "Invalid verification code!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify Email</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; }
        .card { border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border: none; }
    </style>
</head>
<body>
<div class="container mt-5" style="max-width:450px;">
    <div class="card p-4 mt-5">
        <h4 class="text-center mb-4">Email Verification</h4>
        <?php if($msg) echo "<div class='alert alert-danger text-center'>$msg</div>"; ?>
        <form method="POST">
            <p class="text-center text-muted">A 6-digit code was sent to:<br><strong><?= htmlspecialchars($email) ?></strong></p>
            <input type="text" name="verification_code" class="form-control mb-3 text-center" placeholder="123456" maxlength="6" required>
            <button name="verify" class="btn btn-primary w-100 py-2">Verify & Continue</button>
        </form>
        <div class="text-center mt-3">
            <a href="signup.php" class="small text-decoration-none text-muted">Back to Signup</a>
        </div>
    </div>
</div>
</body>
</html>
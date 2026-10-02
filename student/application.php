<?php
session_start();
include "../database/db.php";
include "../mailer.php";


if(!isset($_SESSION['email'])){
    header("Location: login.php");
    exit;
}

$email = $_SESSION['email'];


$userRes = mysqli_query($conn, "SELECT * FROM hec_users WHERE email='$email' LIMIT 1");
if(mysqli_num_rows($userRes) == 0){
    header("Location: logout.php");
    exit;
}

$user = mysqli_fetch_assoc($userRes);
$name = $user['name'];
$msg  = "";


$check = mysqli_query($conn,"SELECT * FROM hec_applications WHERE email='$email'");
$alreadyApplied = mysqli_num_rows($check) > 0;

$app = null;
if($alreadyApplied){
    $app = mysqli_fetch_assoc($check);
    $student_id = $app['student_id'];
} else {
    $student_id = "";
}


if($_SERVER["REQUEST_METHOD"] == "POST" && !$alreadyApplied){

    $student_id_input = mysqli_real_escape_string($conn, $_POST['student_id']);
    $father     = mysqli_real_escape_string($conn, $_POST['father_name']);
    $cnic       = mysqli_real_escape_string($conn, $_POST['cnic']);
    $semester   = intval($_POST['semester']);
    $cgpa       = floatval($_POST['cgpa']);

    $dupCheck = mysqli_query(
        $conn,
        "SELECT * FROM hec_applications 
         WHERE student_id='$student_id_input' OR cnic='$cnic'"
    );

    if(mysqli_num_rows($dupCheck) > 0){
        $msg = "Error: Student ID or CNIC already exists.";
    } else {

        
        $sql = "INSERT INTO hec_applications 
                (email, student_id, father_name, cnic, semester, cgpa, status)
                VALUES 
                ('$email', '$student_id_input', '$father', '$cnic', '$semester', '$cgpa', 'Pending')";

        if(mysqli_query($conn,$sql)){

            mysqli_query(
                $conn,
                "UPDATE hec_users SET student_id='$student_id_input' WHERE email='$email'"
            );

            $subject = "HEC Laptop Scheme Application Submitted";
            $body = "<h3>Hello $name</h3>
                     <p>Your application has been submitted successfully.</p>";

            sendEmail($email,$subject,$body);

            header("Location: application.php");
            exit;

        } else {
            $msg = "ERROR: ".mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>HEC Student Portal</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: linear-gradient(135deg, #f0f2f5, #dfe9f3);
    min-height: 100vh;
}
.card {
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}
.header {
    background-color:#0d6efd;
    color:#fff;
    padding:15px;
    border-radius: 12px 12px 0 0;
    margin-bottom:20px;
    text-align:center;
}
.form-control:read-only {
    background-color: #e9ecef;
    cursor: not-allowed;
}
</style>
</head>

<body>

<div style="position: fixed; top: 20px; right: 20px; z-index: 1000;">
    <a href="logout.php" class="btn btn-danger">Logout</a>
</div>

<div class="container mt-5">
<div class="row justify-content-center">
<div class="col-md-7">

<div class="card">

<div class="header">
    <h4>HEC Laptop Scheme Portal</h4>
    <?php if($alreadyApplied): ?>
        <p class="mb-0">
            <strong>Name:</strong> <?= htmlspecialchars($name) ?> |
            <strong>Student ID:</strong> <?= htmlspecialchars($student_id) ?>
        </p>
    <?php else: ?>
        <p class="mb-0">Please fill in your academic details</p>
    <?php endif; ?>
</div>

<div class="p-4">

<?php if($msg!=""){ ?>
    <div class="alert alert-danger text-center"><?= $msg ?></div>
<?php } ?>

<?php if(!$alreadyApplied): ?>

<form method="POST">
    <input type="text" class="form-control mb-2"
           value="<?= htmlspecialchars($name) ?>" readonly>

    <input type="text" name="student_id" class="form-control mb-2"
           placeholder="Student ID" required>

    <input type="text" name="father_name" class="form-control mb-2"
           placeholder="Father Name" required>

    <input type="text" name="cnic" id="cnic" class="form-control mb-2"
           placeholder="CNIC (xxxxx-xxxxxxx-x)" maxlength="15" required>

    <input type="number" name="semester" class="form-control mb-2"
           placeholder="Current Semester" min="1" required>

    <input type="number" step="0.01" name="cgpa" class="form-control mb-3"
           placeholder="CGPA" min="0" max="4" required>

    <button class="btn btn-primary w-100">Submit Application</button>
</form>

<?php else:

$statusColor = match($app['status']){
    'Selected'   => 'text-success',
    'Rejected'   => 'text-danger',
    'Low_Merit'  => 'text-warning',
    'Verified'   => 'text-primary',
    default      => 'text-secondary'
};
?>

<h5 class="text-center mb-3">Application Status</h5>

<table class="table table-bordered">
<tr><th>Status</th><td class="fw-bold <?= $statusColor ?>"><?= $app['status'] ?></td></tr>
<tr><th>Remarks</th><td><?= $app['remarks'] ?? 'Under Review' ?></td></tr>
<tr><th>Semester</th><td><?= $app['semester'] ?></td></tr>
<tr><th>CGPA</th><td><?= $app['cgpa'] ?></td></tr>
<tr><th>CNIC</th><td><?= $app['cnic'] ?></td></tr>
</table>

<?php endif; ?>

</div>
</div>

</div>
</div>
</div>

<script>
document.getElementById("cnic")?.addEventListener("input", function (e) {
    let v = e.target.value.replace(/\D/g,"");
    if(v.length > 13) v = v.slice(0,13);
    let f = v;
    if(v.length > 5){
        f = v.slice(0,5) + "-" + v.slice(5,12);
        if(v.length > 12) f += "-" + v.slice(12);
    }
    e.target.value = f;
});
</script>

</body>
</html>

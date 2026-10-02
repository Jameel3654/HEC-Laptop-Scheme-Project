<?php
include "../database/db.php";

$msg = "";
$id = $_GET['id'] ?? '';
if(!$id) header("Location: university.php");


$studentQ = mysqli_query($conn, "SELECT * FROM university_students WHERE student_id='$id'");
if(mysqli_num_rows($studentQ) == 0){
    header("Location: university.php");
}
$student = mysqli_fetch_assoc($studentQ);


if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $name         = mysqli_real_escape_string($conn, $_POST['name']);
    $father_name  = mysqli_real_escape_string($conn, $_POST['father_name']);
    $cnic         = mysqli_real_escape_string($conn, $_POST['cnic']);
    $dob          = mysqli_real_escape_string($conn, $_POST['dob']);
    $gender       = mysqli_real_escape_string($conn, $_POST['gender']);
    $email        = mysqli_real_escape_string($conn, $_POST['email']);
    $phone        = mysqli_real_escape_string($conn, $_POST['phone']);
    $semester     = intval($_POST['semester']);
    $department   = mysqli_real_escape_string($conn, $_POST['department']);
    $cgpa         = floatval($_POST['cgpa']);
    $address      = mysqli_real_escape_string($conn, $_POST['address']);

    $sql = "UPDATE university_students SET
        name='$name', father_name='$father_name', cnic='$cnic', dob='$dob', gender='$gender',
        email='$email', phone='$phone', semester='$semester', department='$department', cgpa='$cgpa', address='$address'
        WHERE student_id='$id'";

    if(mysqli_query($conn,$sql)){
        header("Location: university.php");
        exit;
    } else {
        $msg = "Error: ".mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Student</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
<div class="row justify-content-center">
<div class="col-md-6">

<div class="card shadow">
<div class="card-body">
<h4 class="text-center mb-3">Edit Student</h4>

<?php if($msg): ?>
<div class="alert alert-danger"><?= $msg ?></div>
<?php endif; ?>

<form method="POST">
    <input type="text" name="name" class="form-control mb-2" placeholder="Name" value="<?= htmlspecialchars($student['name']) ?>" required>
    <input type="text" name="father_name" class="form-control mb-2" placeholder="Father Name" value="<?= htmlspecialchars($student['father_name']) ?>" required>
    <input type="text" name="cnic" class="form-control mb-2" placeholder="CNIC" value="<?= htmlspecialchars($student['cnic']) ?>" required>
    <input type="date" name="dob" class="form-control mb-2" value="<?= htmlspecialchars($student['dob']) ?>" required>
    <select name="gender" class="form-select mb-2" required>
        <option value="Male" <?= $student['gender']=='Male'?'selected':'' ?>>Male</option>
        <option value="Female" <?= $student['gender']=='Female'?'selected':'' ?>>Female</option>
    </select>
    <input type="email" name="email" class="form-control mb-2" placeholder="Email" value="<?= htmlspecialchars($student['email']) ?>">
    <input type="text" name="phone" class="form-control mb-2" placeholder="Phone" value="<?= htmlspecialchars($student['phone']) ?>">
    <input type="number" name="semester" class="form-control mb-2" placeholder="Semester" min="1" value="<?= htmlspecialchars($student['semester']) ?>" required>
    <input type="text" name="department" class="form-control mb-2" placeholder="Department" value="<?= htmlspecialchars($student['department']) ?>" required>
    <input type="number" step="0.01" name="cgpa" class="form-control mb-2" placeholder="CGPA" min="0" max="4" value="<?= htmlspecialchars($student['cgpa']) ?>">
    <textarea name="address" class="form-control mb-3" placeholder="Address"><?= htmlspecialchars($student['address']) ?></textarea>

    <button class="btn btn-primary w-100">Update Student</button>
</form>

</div>
</div>
</div>
</div>
</div>
</body>
</html>

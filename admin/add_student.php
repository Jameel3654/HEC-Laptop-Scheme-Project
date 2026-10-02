<?php
include "../database/db.php";

$msg = "";

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $student_id   = mysqli_real_escape_string($conn, $_POST['student_id']);
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
    $enroll_date  = date('Y-m-d H:i:s');

    $check = mysqli_query($conn, "SELECT * FROM university_students WHERE student_id='$student_id' OR cnic='$cnic'");
    if(mysqli_num_rows($check) > 0){
        $msg = "Student ID or CNIC already exists!";
    } else {
        $sql = "INSERT INTO university_students
            (student_id,name,father_name,cnic,dob,gender,email,phone,semester,department,cgpa,address,enrollment_date)
            VALUES
            ('$student_id','$name','$father_name','$cnic','$dob','$gender','$email','$phone','$semester','$department','$cgpa','$address','$enroll_date')";

        if(mysqli_query($conn,$sql)){
            header("Location: university.php");
            exit;
        } else {
            $msg = "Error: ".mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Student</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
<div class="row justify-content-center">
<div class="col-md-6">

<div class="card shadow">
<div class="card-body">
<h4 class="text-center mb-3">Add New Student</h4>

<?php if($msg): ?>
<div class="alert alert-danger"><?= $msg ?></div>
<?php endif; ?>

<form method="POST">
    <input type="text" name="student_id" class="form-control mb-2" placeholder="Student ID (e.g. BSE-001)" required>
    <input type="text" name="name" class="form-control mb-2" placeholder="Name" required>
    <input type="text" name="father_name" class="form-control mb-2" placeholder="Father Name" required>
    
    <input type="text" name="cnic" id="cnic" class="form-control mb-2" placeholder="CNIC (00000-0000000-0)" required>
    
    <input type="date" name="dob" class="form-control mb-2" required>
    <select name="gender" class="form-select mb-2" required>
        <option value="">Select Gender</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
    </select>
    <input type="email" name="email" class="form-control mb-2" placeholder="Email" required>
    <input type="text" name="phone" class="form-control mb-2" placeholder="Phone" required>
    <input type="number" name="semester" class="form-control mb-2" placeholder="Semester" min="1" required>

    <select name="department" class="form-select mb-2" required>
        <option value="">Select Department</option>
        <option value="Computer Science">Computer Science</option>
        <option value="Artificial Intelligence">Artificial Intelligence</option>
        <option value="Cyber Security">Cyber Security</option>
        <option value="Data Science">Data Science</option>
        <option value="Information Technology">Information Technology</option>
        <option value="Software Engineering">Software Engineering</option>
    </select>

    <input type="number" step="0.01" name="cgpa" class="form-control mb-2" placeholder="CGPA" min="0" max="4">
    <textarea name="address" class="form-control mb-3" placeholder="Address"></textarea>

    <button class="btn btn-success w-100">Add Student</button>
</form>

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
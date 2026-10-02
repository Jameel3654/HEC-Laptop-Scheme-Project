<?php
include "../database/db.php";

if(isset($_GET['delete'])){
    $id = mysqli_real_escape_string($conn, $_GET['delete']);
    mysqli_query($conn, "DELETE FROM university_students WHERE student_id='$id'");
    header("Location: university.php");
    exit;
}

$filter = isset($_GET['department']) ? $_GET['department'] : 'All';

$query = "SELECT * FROM university_students";
if($filter != 'All'){
    $query .= " WHERE student_id LIKE '$filter-%'";
}
$query .= " ORDER BY student_id ASC";

$result = mysqli_query($conn,$query);
if(!$result) die("Query Failed: ".mysqli_error($conn));
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>University Database</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
.table thead th { position: sticky; top: 0; background: #343a40; color: #fff; z-index: 2; }
</style>
</head>

<body>
<div class="container-fluid">
<div class="row">

    <?php include "sidebar.php"; ?>

    <div class="col-md-10 p-4">
        <h3 class="mb-3">University Students</h3>

        <form method="GET" class="mb-3 d-flex align-items-center">
            <label for="department" class="form-label me-2">Department:</label>
            <select name="department" id="department" class="form-select w-auto" onchange="this.form.submit()">
                <option value="All" <?= $filter=='All'?'selected':'' ?>>All Records</option>
                <option value="CSC" <?= $filter=='CSC'?'selected':'' ?>>Computer Science</option>
                <option value="BAI" <?= $filter=='BAI'?'selected':'' ?>>Artificial Intelligence</option>
                <option value="BCY" <?= $filter=='BCY'?'selected':'' ?>>Cyber Security</option>
                <option value="BDS" <?= $filter=='BDS'?'selected':'' ?>>Data Science</option>
                <option value="BIT" <?= $filter=='BIT'?'selected':'' ?>>Information Technology</option>
                <option value="BSE" <?= $filter=='BSE'?'selected':'' ?>>Software Engineering</option>
            </select>

            <input type="text" id="searchInput" class="form-control ms-3" placeholder="Search Name, ID, CNIC..." style="max-width:300px;">
            <a href="add_student.php" class="btn btn-success ms-3">Add Student</a>
        </form>

        <table class="table table-bordered table-striped table-hover w-100" id="studentTable">
            <thead class="table-dark">
                <tr>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Father Name</th>
                    <th>CNIC</th>
                    <th>DOB</th>
                    <th>Gender</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Semester</th>
                    <th>Department</th>
                    <th>CGPA</th>
                    <th>Address</th>
                    <th>Enrollment Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row=mysqli_fetch_assoc($result)){ ?>
                <tr>
                    <td><?= htmlspecialchars($row['student_id']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['father_name']) ?></td>
                    <td><?= htmlspecialchars($row['cnic']) ?></td>
                    <td><?= htmlspecialchars($row['dob']) ?></td>
                    <td><?= htmlspecialchars($row['gender']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                    <td><?= htmlspecialchars($row['semester']) ?></td>
                    <td><?= htmlspecialchars($row['department']) ?></td>
                    <td><?= htmlspecialchars($row['cgpa']) ?></td>
                    <td><?= htmlspecialchars($row['address']) ?></td>
                    <td><?= htmlspecialchars($row['enrollment_date']) ?></td>
                    <td>
                        <a href="edit_student.php?id=<?= urlencode($row['student_id']) ?>" class="btn btn-primary btn-sm">Edit</a>
                        <a href="?delete=<?= urlencode($row['student_id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this student?')">Delete</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

    </div>

</div>
</div>

<script>
document.getElementById('searchInput').addEventListener('input', function(){
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#studentTable tbody tr');

    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});
</script>

</body>
</html>
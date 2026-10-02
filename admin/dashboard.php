<?php
include "../database/db.php";

$total_students = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM university_students"));
$total_apps     = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM hec_applications"));
$selected       = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM hec_applications WHERE status='Selected'"));
$rejected       = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM hec_applications WHERE status='Rejected'"));
$low_merit      = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM hec_applications WHERE status='Low_Merit'"));
$pending        = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM hec_applications WHERE status='Pending'"));
$verified       = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM hec_applications WHERE status='Verified'"));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    </style>
</head>

<body>
<div class="container-fluid">
    <div class="row">

        
        <?php include "sidebar.php"; ?>

       
        <div class="col-md-10 p-4">
            <h3>Dashboard Overview</h3>

            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card shadow text-center">
                        <div class="card-body">
                            <h6>Total University Students</h6>
                            <h3><?= $total_students ?></h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card shadow text-center">
                        <div class="card-body">
                            <h6>Total Applications</h6>
                            <h3><?= $total_apps ?></h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="card shadow text-success text-center">
                        <div class="card-body">
                            <h6>Selected</h6>
                            <h3><?= $selected ?></h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="card shadow text-danger text-center">
                        <div class="card-body">
                            <h6>Rejected</h6>
                            <h3><?= $rejected ?></h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="card shadow text-warning text-center">
                        <div class="card-body">
                            <h6>Low Merit</h6>
                            <h3><?= $low_merit ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5 mb-3">
                <label for="chartType" class="form-label">Select Chart Type:</label>
                <select id="chartType" class="form-select w-25">
                    <option value="bar">Bar Chart</option>
                    <option value="pie">Pie Chart</option>
                </select>
            </div>

            <div class="mt-3">
                <canvas id="statusChart" style="max-height:400px;"></canvas>
            </div>

        </div>
    </div>
</div>

<script>
const ctx = document.getElementById('statusChart').getContext('2d');
let statusChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Selected','Rejected','Low Merit','Verified','Pending'],
        datasets: [{
            label: 'Applications',
            data: [<?= $selected ?>, <?= $rejected ?>, <?= $low_merit ?>, <?= $verified ?>, <?= $pending ?>],
            backgroundColor: ['#198754','#dc3545','#ffc107','#0d6efd','#6c757d']
        }]
    },
    options: { responsive: true }
});

document.getElementById('chartType').addEventListener('change', function(){
    statusChart.destroy();
    statusChart = new Chart(ctx, {
        type: this.value,
        data: {
            labels: ['Selected','Rejected','Low Merit','Verified','Pending'],
            datasets: [{
                label: 'Applications',
                data: [<?= $selected ?>, <?= $rejected ?>, <?= $low_merit ?>, <?= $verified ?>, <?= $pending ?>],
                backgroundColor: ['#198754','#dc3545','#ffc107','#0d6efd','#6c757d']
            }]
        },
        options: { responsive: true }
    });
});
</script>
</body>
</html>

<?php

$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="col-md-2 text-white p-3 sidebar" style="background:#212529; min-height:100vh;">
    <h5 class="border-bottom pb-2 mb-4">Admin Panel</h5>
    <a href="dashboard.php" class="text-white d-block p-2 rounded mb-1 text-decoration-none  <?= $current_page=='dashboard.php'?'bg-primary':'' ?>" >Dashboard</a>
    <a href="university.php" class="text-white d-block p-2 rounded mb-1 text-decoration-none <?= $current_page=='university.php'?'bg-primary':'' ?>">University</a>
    <a href="hec.php" class="text-white d-block p-2 rounded mb-1 text-decoration-none <?= $current_page=='hec.php'?'bg-primary':'' ?>">HEC Applications</a>
    <a href="logout.php" class="text-white d-block p-2 rounded mt-3 text-decoration-none">Logout</a>
</div>

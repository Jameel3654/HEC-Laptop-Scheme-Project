<?php
include "../database/db.php";


function handleSelectionRipple($conn, $old_status, $new_status, $current_id) {
    
    if($old_status == 'Selected' && $new_status != 'Selected'){
        $promote = mysqli_query($conn, "SELECT application_id FROM hec_applications WHERE status='Low_Merit' ORDER BY cgpa DESC, application_id ASC LIMIT 1");
        if(mysqli_num_rows($promote) > 0){
            $p = mysqli_fetch_assoc($promote);
            $p_id = $p['application_id'];
            mysqli_query($conn, "UPDATE hec_applications SET status='Selected', remarks='Promoted to Selection' WHERE application_id=$p_id");
            return ['type' => 'promoted', 'id' => $p_id]; 
        }
    }

    
    if($old_status != 'Selected' && $new_status == 'Selected'){
        $demote = mysqli_query($conn, "SELECT application_id FROM hec_applications WHERE status='Selected' AND application_id != $current_id ORDER BY cgpa ASC, application_id DESC LIMIT 1");
        if(mysqli_num_rows($demote) > 0){
            $d = mysqli_fetch_assoc($demote);
            $d_id = $d['application_id'];
            mysqli_query($conn, "UPDATE hec_applications SET status='Low_Merit', remarks='Bumped by Manual Selection' WHERE application_id=$d_id");
            return ['type' => 'demoted', 'id' => $d_id];
        }
    }
    return null;
}


if(isset($_POST['export_excel'])){
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=HEC_Shortlisted_Candidates.xls");
    echo "HEC Laptop Shortlisted Candidates\n\n";
    echo "Student ID\tName\tCGPA\tStatus\tRemarks\n";
    $list = mysqli_query($conn, "SELECT a.student_id, u.name, a.cgpa, a.status, a.remarks FROM hec_applications a LEFT JOIN hec_users u ON a.email = u.email ORDER BY (a.status='Selected') DESC, a.cgpa DESC");
    while($row = mysqli_fetch_assoc($list)){
        echo "{$row['student_id']}\t{$row['name']}\t{$row['cgpa']}\t{$row['status']}\t{$row['remarks']}\n";
    }
    exit();
}


if(isset($_POST['reset'])){
    mysqli_query($conn,"UPDATE hec_applications SET status='Pending', remarks=NULL");
    header("Location: hec.php"); exit();
}

// --- VERIFY DATA ---
if(isset($_POST['verify'])){
    $apps = mysqli_query($conn,"SELECT * FROM hec_applications");
    while($app = mysqli_fetch_assoc($apps)){
        $uni = mysqli_query($conn,"SELECT * FROM university_students WHERE student_id='".$app['student_id']."'");
        if(mysqli_num_rows($uni) > 0){
            $u = mysqli_fetch_assoc($uni);
            mysqli_query($conn, "UPDATE hec_applications SET cgpa='".mysqli_real_escape_string($conn,$u['cgpa'])."', status='Verified', remarks='Data Synced' WHERE application_id=".$app['application_id']);
            mysqli_query($conn, "UPDATE hec_users SET name='".mysqli_real_escape_string($conn,$u['name'])."' WHERE email='".$app['email']."'");
        } else {
            mysqli_query($conn,"UPDATE hec_applications SET status='Rejected', remarks='Student Not Found' WHERE application_id=".$app['application_id']);
        }
    }
    header("Location: hec.php"); exit();
}


if(isset($_POST['select'])){
    $topN = intval($_POST['topN']);
    if($topN > 0){
        mysqli_query($conn, "UPDATE hec_applications SET status='Verified' WHERE status IN ('Selected', 'Low_Merit')");
        $verified = mysqli_query($conn,"SELECT * FROM hec_applications WHERE status='Verified' ORDER BY cgpa DESC, application_id ASC");
        $i = 0;
        while($row = mysqli_fetch_assoc($verified)){
            $i++;
            $status = ($i <= $topN) ? 'Selected' : 'Low_Merit';
            $remarks = ($i <= $topN) ? 'Selected on Merit' : 'Below Merit';
            mysqli_query($conn,"UPDATE hec_applications SET status='$status', remarks='$remarks' WHERE application_id=".$row['application_id']);
        }
    }
    header("Location: hec.php"); exit();
}


if(isset($_POST['ajax_save'])){
    $id = intval($_POST['app_id']);
    $new_sid = mysqli_real_escape_string($conn, $_POST['student_id']);
    $new_name = mysqli_real_escape_string($conn, $_POST['name']);
    $new_cgpa = mysqli_real_escape_string($conn, $_POST['cgpa']);
    $new_status = $_POST['status'];
    $new_remarks = mysqli_real_escape_string($conn, $_POST['remarks']);

    $old_res = mysqli_query($conn, "SELECT status, email FROM hec_applications WHERE application_id=$id");
    $old_data = mysqli_fetch_assoc($old_res);
    
    mysqli_query($conn, "UPDATE hec_applications SET student_id='$new_sid', cgpa='$new_cgpa', status='$new_status', remarks='$new_remarks' WHERE application_id=$id");
    mysqli_query($conn, "UPDATE hec_users SET name='$new_name' WHERE email='".$old_data['email']."'");

    $change = handleSelectionRipple($conn, $old_data['status'], $new_status, $id);

    echo json_encode(['success'=>true, 'change'=>$change]);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>HEC Applications Management</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body { background:#f0f2f5; font-family: 'Segoe UI', sans-serif; }
    .status-Pending { color: #6c757d !important; font-weight: bold; }
    .status-Verified { color: #0d6efd !important; font-weight: bold; }
    .status-Selected { color: #198754 !important; font-weight: bold; }
    .status-Rejected { color: #dc3545 !important; font-weight: bold; }
    .status-Low_Merit { color: #fd7e14 !important; font-weight: bold; }
    .edit-ui { display: none; }
    .edit-mode .view-ui { display: none; }
    .edit-mode .edit-ui { display: block; }
    .table-responsive { max-height: 70vh; overflow-y: auto; }

    @media print {
        body * { visibility: hidden; }
        #printable, #printable * { visibility: visible; }
        #printable { position: absolute; top: 0; left: 0; width: 100%; }
        .no-print { display: none !important; }
    }
</style>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <?php include "sidebar.php"; ?>
        <div class="col-md-10 p-4" id="mainWrapper">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="no-print">HEC Laptop Scheme</h3>
                <div class="d-flex gap-2">
                    <div class="dropdown no-print">
                        <button class="btn btn-dark btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">Download</button>
                        <ul class="dropdown-menu">
                            <li><button class="dropdown-item" onclick="window.print()">Download PDF</button></li>
                            <li><form method="POST"><button name="export_excel" class="dropdown-item">Download Excel</button></form></li>
                        </ul>
                    </div>
                    <button class="btn btn-warning btn-sm no-print" id="toggleEdit">Edit Details</button>
                    <form method="POST" class="d-inline no-print">
                        <button name="reset" class="btn btn-outline-danger btn-sm" onclick="return confirm('Reset all?')">Reset</button>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm mb-4 no-print">
                <div class="card-body row">
                    <div class="col-md-4 border-end">
                        <label class="form-label fw-bold small">Step 1: Verification</label>
                        <form method="POST"><button name="verify" class="btn btn-primary w-100">Verify All</button></form>
                    </div>
                    <div class="col-md-8 ps-4">
                        <label class="form-label fw-bold small">Step 2: Selection (Set Quota)</label>
                        <form method="POST" class="d-flex">
                            <input type="number" name="topN" class="form-control me-2" placeholder="Quota Amount" required>
                            <button name="select" class="btn btn-success px-4">Finalize</button>
                        </form>
                    </div>
                </div>
            </div>

            <div id="printable" class="card shadow-sm">
                <h3 class="p-3">HEC Laptop Shortlisted Candidates</h3>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="appsTable">
                        <thead class="table-light">
                            <tr>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th>CGPA</th>
                                <th>Status</th>
                                <th>Remarks</th>
                                <th class="edit-ui">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $r = mysqli_query($conn, "SELECT a.*, u.name FROM hec_applications a LEFT JOIN hec_users u ON a.email = u.email ORDER BY (a.status='Selected') DESC, a.cgpa DESC");
                        while($row = mysqli_fetch_assoc($r)){
                            $status = $row['status'];
                            $statusClass = str_replace(' ', '_', $status);
                        ?>
                        <tr>
                            <td>
                                <span class="view-ui"><?= htmlspecialchars($row['student_id']) ?></span>
                                <input type="text" class="form-control form-control-sm edit-ui student_id" value="<?= htmlspecialchars($row['student_id']) ?>">
                            </td>
                            <td>
                                <span class="view-ui"><?= htmlspecialchars($row['name']) ?></span>
                                <input type="text" class="form-control form-control-sm edit-ui name" value="<?= htmlspecialchars($row['name']) ?>">
                            </td>
                            <td>
                                <span class="view-ui fw-bold"><?= htmlspecialchars($row['cgpa']) ?></span>
                                <input type="text" class="form-control form-control-sm edit-ui cgpa" value="<?= htmlspecialchars($row['cgpa']) ?>">
                            </td>
                            <td class="status-cell status-<?= $statusClass ?>">
                                <span class="view-ui status-text"><?= str_replace('_', ' ', $status) ?></span>
                                <select class="form-select form-select-sm edit-ui status-select">
                                    <option value="Selected" <?= $status=='Selected'?'selected':'' ?>>Selected</option>
                                    <option value="Rejected" <?= $status=='Rejected'?'selected':'' ?>>Rejected</option>
                                    <?php if($status != 'Selected' && $status != 'Rejected'): ?>
                                        <option value="<?= $status ?>" selected><?= str_replace('_', ' ', $status) ?></option>
                                    <?php endif; ?>
                                </select>
                            </td>
                            <td>
                                <span class="view-ui text-muted small remarks-text"><?= htmlspecialchars($row['remarks'] ?? '') ?></span>
                                <input type="text" class="form-control form-control-sm edit-ui remarks-input" value="<?= htmlspecialchars($row['remarks'] ?? '') ?>">
                            </td>
                            <td class="edit-ui">
                                <button class="btn btn-sm btn-dark save-btn" data-id="<?= $row['application_id'] ?>">Save</button>
                            </td>
                        </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function(){
    var isChanged = false;

    function updateRowUI(row) {
        row.find(".view-ui").each(function(){
            var targetInput = $(this).siblings(".edit-ui");
            if(targetInput.length == 0) return;
            
            var val = targetInput.val();
            if(targetInput.is("select")) {
                val = targetInput.find("option:selected").text();
                var cell = row.find(".status-cell");
                cell.removeClass (function (index, className) {
                    return (className.match (/(^|\s)status-\S+/g) || []).join(' ');
                });
                cell.addClass("status-" + targetInput.val().replace(' ', '_'));
            }
            $(this).text(val);
        });
    }

    $("#toggleEdit").click(function() {
        var wrapper = $("#mainWrapper");
        wrapper.toggleClass('edit-mode');
        if(wrapper.hasClass('edit-mode')) {
            $(this).text("Back").removeClass("btn-warning").addClass("btn-secondary");
        } else {
            $(this).text("Edit Details").removeClass("btn-secondary").addClass("btn-warning");
            if(isChanged) location.reload();
        }
    });

    $(".save-btn").click(function(){
        var btn = $(this);
        var row = btn.closest("tr");
        var data = {
            ajax_save: true,
            app_id: btn.data("id"),
            student_id: row.find(".student_id").val(),
            name: row.find(".name").val(),
            cgpa: row.find(".cgpa").val(),
            status: row.find(".status-select").val(),
            remarks: row.find(".remarks-input").val()
        };
        $.post("hec.php", data, function(res){
            try {
                var result = JSON.parse(res);
                updateRowUI(row);
                
                if(result.change){
                    var changedRow = $("button.save-btn[data-id='" + result.change.id + "']").closest("tr");
                    
                   
                    if(result.change.type === 'promoted'){
                        changedRow.find(".status-select").append('<option value="Selected">Selected</option>');
                        changedRow.find(".status-select").val("Selected");
                        changedRow.find(".remarks-input").val("Promoted to Selection");
                    } else if(result.change.type === 'demoted'){
                        
                        changedRow.find(".status-select").append('<option value="Low_Merit">Low Merit</option>');
                        changedRow.find(".status-select").val("Low_Merit");
                        changedRow.find(".remarks-input").val("Bumped by Manual Selection");
                    }
                    updateRowUI(changedRow);
                }
                alert("Status updated! Merit balance maintained.");
                isChanged = true;
            } catch(e){ alert("Update failed!"); }
        });
    });
});
</script>
</body>
</html>
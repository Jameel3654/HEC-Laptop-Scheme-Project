<?php
include "../database/db.php";

if(isset($_POST['ajax_save'])){
    $id = intval($_POST['app_id']);
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $cgpa = mysqli_real_escape_string($conn, $_POST['cgpa']);
    $status = $_POST['status'];
    $remarks = mysqli_real_escape_string($conn, $_POST['remarks']);

    
    $old_res = mysqli_query($conn, "SELECT email FROM hec_applications WHERE application_id=$id");
    $old_row = mysqli_fetch_assoc($old_res);
    $email = $old_row['email'];

    
    $update_app = mysqli_query($conn, "
        UPDATE hec_applications SET 
        student_id='$student_id',
        cgpa='$cgpa', 
        status='$status', 
        remarks='$remarks' 
        WHERE application_id=$id
    ");

    
    $update_user = mysqli_query($conn, "UPDATE hec_users SET name='$name' WHERE email='$email'");

    if($update_app && $update_user){
        echo "success";
    } else {
        echo "error";
    }
}
?>

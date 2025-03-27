<?php 

session_start();
include 'connect.php';

$username = mysqli_real_escape_string($conn, $_POST['username']);
$academicRanks = mysqli_real_escape_string($conn, $_POST['academicRanks']);
$firstName = mysqli_real_escape_string($conn, $_POST['firstName']);
$lastName = mysqli_real_escape_string($conn, $_POST['lastName']);
$gender = mysqli_real_escape_string($conn, $_POST['gender']);
$dateOfBirth = mysqli_real_escape_string($conn, $_POST['dateOfBirth']);
$position = mysqli_real_escape_string($conn, $_POST['position']);
$tel = mysqli_real_escape_string($conn, $_POST['tel']);
$password = mysqli_real_escape_string($conn, $_POST['password']);

if(!empty($username) && !empty($academicRanks) && !empty($firstName) && !empty($lastName) && !empty($gender) && !empty($dateOfBirth) && !empty($position) && !empty($tel) && !empty($password)) {
    $query = mysqli_query($conn, "INSERT INTO tb_member 
    (member_id, academic_ranks, first_name, last_name, gender, date_of_birth, position_id, phone_number, password) 
    VALUES ('{$username}', '{$academicRanks}', '{$firstName}', '{$lastName}', '{$gender}', '{$dateOfBirth}', '{$position}', '{$tel}', '{$password}')") or die('query failed');

    if($query) {
        $_SESSION['message'] = "ลงทะเบียนสำเร็จ";
        header("Location: {$base_url}/login.php");
    } else {
        $_SESSION['message'] = "ลงทะเบียนไม่สำเร็จ";
        header("Location: {$base_url}/register.php");
    }
} else {
    $_SESSION['message'] = "กรุณากรอกข้อมูลให้ครบ";
    header("Location: {$base_url}/register.php");
}

?>
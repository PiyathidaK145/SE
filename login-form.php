<?php

session_start();
include dirname(__FILE__) . '/../connet/connect.php';

$username = mysqli_real_escape_string($conn, $_POST['username']);
$password = mysqli_real_escape_string($conn, $_POST['password']);

if (!empty($username) && !empty($password)) {
    $query = mysqli_query($conn, "SELECT * FROM tb_member WHERE member_id = '{$username}' AND password = '{$password}'") or die('query failed');
    $row = mysqli_num_rows($query);

    if ($row === 1) {
        $user = mysqli_fetch_assoc($query);
        if ($password === $user['password']) {
            $_SESSION[WP . 'checklogin'] = true;
            $_SESSION[WP . 'member_id'] = $user['member_id'];
            $_SESSION[WP . 'position_id'] = $user['position_id'];

            // เปลี่ยน path ไปหน้าของแต่ละ role
            if ($user['position_id'] == 1101) {
                header("Location: {$base_url}/home_1101.php");
            } else if ($user['position_id'] == 1102) {
                header("Location: {$base_url}/home_1102.php");
            } else {
                header("Location: {$base_url}/home_proff.php");
            }
            exit();
        }
    }
} 

// กรณีเข้าสู่ระบบไม่สำเร็จ
$_SESSION['message'] = "ข้อมูลไม่ถูกต้อง";
header("Location: {$base_url}/login.php");
exit();

?>
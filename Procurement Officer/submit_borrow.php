<?php
include dirname(__FILE__) . '/../connet/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $durable_articles_id = mysqli_real_escape_string($conn, $_POST['durable_articles_id']);
  $member_id = mysqli_real_escape_string($conn, $_POST['member_id']);
  $room_id = mysqli_real_escape_string($conn, $_POST['room_id']);
  $time_now = date('Y-m-d H:i:s');

  $sql = "INSERT INTO tb_borrowing (member_id, durable_articles_id, status_of_use, room_id, time_borrow)
          VALUES ('$member_id', '$durable_articles_id', 'Borrowed', '$room_id', '$time_now')";

  if (mysqli_query($conn, $sql)) {
    echo "<script>alert('บันทึกการยืมเรียบร้อย'); window.location.href='asset-table.php';</script>";
  } else {
    echo "<script>alert('เกิดข้อผิดพลาดในการบันทึก'); window.history.back();</script>";
  }
}
?>

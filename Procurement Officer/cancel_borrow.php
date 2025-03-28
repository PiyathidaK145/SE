<?php

include dirname(__FILE__) . '/../connet/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['borrowing_id'])) {
    $borrowing_id = mysqli_real_escape_string($conn, $_POST['borrowing_id']);
    $member_id = mysqli_real_escape_string($conn, $_POST['member_id']);

  // ดึงข้อมูลรายการยืมล่าสุด
  $query = "SELECT * FROM tb_borrowing WHERE borrowing_id = '$borrowing_id'";
  $result = mysqli_query($conn, $query);

  if (!$result) {
    die("Query Error: " . mysqli_error($conn));
  }

  if ($result && mysqli_num_rows($result) > 0) {
    $borrow = mysqli_fetch_assoc($result);

  if ($borrow) {
    $durable_articles_id = $borrow['durable_articles_id'];
    $room_id = $borrow['room_id'];
    $time_now = date('Y-m-d H:i:s');

    // เพิ่มประวัติใหม่ว่าคืนแล้ว
    $insert = "
      INSERT INTO tb_borrowing (member_id, durable_articles_id, status_of_use, room_id, time_borrow)
      VALUES ('$member_id', '$durable_articles_id', 'Free', '$room_id', '$time_now')
    ";

    if (mysqli_query($conn, $insert)) {
        echo "<script>alert('ยกเลิกการยืมสำเร็จ'); window.location.href='asset-table.php';</script>";
      } else {
        echo "<script>alert('เกิดข้อผิดพลาดในการบันทึกประวัติ'); window.history.back();</script>";
      }
    } else {
      echo "<script>alert('ไม่พบรายการยืมนี้'); window.history.back();</script>";
    }
    }
  }
?>

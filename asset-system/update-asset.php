<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // รับค่าและป้องกันค่าว่าง
  $id = $_POST['id'] ?? null;
  $name = trim($_POST['name'] ?? '');
  $brand = trim($_POST['brand'] ?? '');
  $series = trim($_POST['series'] ?? '');
  $number = trim($_POST['durable_articles_number'] ?? '');
  $serial = trim($_POST['serial_number'] ?? '');
  $location = trim($_POST['description'] ?? '');
  $condition = trim($_POST['condition_of_use'] ?? '');
  $status = trim($_POST['status'] ?? '');
  $year = trim($_POST['year_of_purchase'] ?? '');
  $note = trim($_POST['note'] ?? '');

  // กรณีถูกยืม
  $borrower = $status === 'ถูกยืม' ? trim($_POST['borrower'] ?? '') : null;
  $borrowDate = $status === 'ถูกยืม' ? ($_POST['borrowDate'] ?? null) : null;
  $borrowTime = $status === 'ถูกยืม' ? ($_POST['borrowTime'] ?? null) : null;

  // ตรวจสอบความครบถ้วนเบื้องต้น
  if (!$id || $name === '' || $brand === '' || $series === '' || $number === '') {
    echo "<script>alert('กรุณากรอกข้อมูลให้ครบถ้วน'); history.back();</script>";
    exit;
  }

  // เตรียมคำสั่ง SQL
  $sql = "UPDATE tb_durable_articles SET 
            name=?, brand=?, series=?, durable_articles_number=?, `serial number`=?, 
            description=?, condition_of_use=?, status=?, borrower=?, borrowDate=?, borrowTime=?, 
            year_of_purchase=?, note=? 
          WHERE durable_articles_id=?";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param(
    "ssssssssssssi",
    $name, $brand, $series, $number, $serial,
    $location, $condition, $status, $borrower, $borrowDate, $borrowTime,
    $year, $note, $id
  );

  // บันทึกข้อมูล
  if ($stmt->execute()) {
    echo "<script>alert('อัปเดตข้อมูลเรียบร้อย'); window.location='asset-table.php';</script>";
  } else {
    echo "เกิดข้อผิดพลาด: " . $stmt->error;
  }

  $stmt->close();
  $conn->close();
}
?>

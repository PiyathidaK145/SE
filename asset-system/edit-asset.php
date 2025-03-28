<?php
include 'db.php';
$id = $_GET['id'] ?? 0;

$sql = "SELECT * FROM tb_durable_articles WHERE durable_articles_id = $id";
$data = $conn->query($sql)->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>แก้ไขข้อมูล</title>
  <link rel="stylesheet" href="edit-asset.css">
</head>
<body>
  <div class="container">
    <h2>แก้ไขข้อมูลครุภัณฑ์</h2>
    <form method="POST" action="update-asset.php">
      <input type="hidden" name="id" value="<?= $id ?>">

      <div class="form-group">
        <label>ชื่อ</label>
        <input type="text" name="name" value="<?= $data['name'] ?>">
      </div>

      <div class="form-group">
        <label>ยี่ห้อ</label>
        <input type="text" name="brand" value="<?= $data['brand'] ?>">
      </div>

      <div class="form-group">
        <label>รุ่น</label>
        <input type="text" name="series" value="<?= $data['series'] ?>">
      </div>

      <div class="form-group">
        <label>หมายเลขครุภัณฑ์</label>
        <input type="text" name="durable_articles_number" value="<?= $data['durable_articles_number'] ?>">
      </div>

      <div class="form-group">
        <label>หมายเลขเครื่อง</label>
        <input type="text" name="serial_number" value="<?= $data['serial number'] ?>">
      </div>

      <div class="form-group">
        <label>ตำแหน่งใช้งาน</label>
        <input type="text" name="description" value="<?= $data['description'] ?>">
      </div>

      <div class="form-group">
        <label>สภาพการใช้งาน</label>
        <select name="condition_of_use">
          <option <?= $data['condition_of_use'] == 'ใช้งานได้' ? 'selected' : '' ?>>ใช้งานได้</option>
          <option <?= $data['condition_of_use'] == 'ชำรุด' ? 'selected' : '' ?>>ชำรุด</option>
          <option <?= $data['condition_of_use'] == 'เสียหาย' ? 'selected' : '' ?>>เสียหาย</option>
          <option <?= $data['condition_of_use'] == 'จำหน่ายแล้ว' ? 'selected' : '' ?>>จำหน่ายแล้ว</option>
        </select>
      </div>

      <div class="form-group">
        <label>สถานะการใช้งาน</label>
        <select name="status" id="statusSelect">
        <option value="ว่าง" <?= $data['status'] == 'ว่าง' ? 'selected' : '' ?>>ว่าง</option>
          <option <?= $data['status'] == 'ไม่พร้อมใช้งาน' ? 'selected' : '' ?>>ไม่พร้อมใช้งาน</option>
          <option <?= $data['status'] == 'ถูกยืม' ? 'selected' : '' ?>>ถูกยืม</option>
        </select>
      </div>

      <div class="borrow-info" id="borrowFields">
        <div class="form-group">
          <label>ชื่อผู้ยืม</label>
          <input type="text" name="borrower" id="borrower" value="<?= $data['borrower'] ?>">
        </div>
        <div class="form-group">
          <label>วันที่ยืม</label>
          <input type="date" name="borrowDate" id="borrowDate" value="<?= $data['borrowDate'] ?>">
        </div>
        <div class="form-group">
          <label>เวลาที่ยืม</label>
          <input type="time" name="borrowTime" id="borrowTime" value="<?= $data['borrowTime'] ?>">
        </div>
      </div>

      <div class="form-group">
        <label>ปีที่ซื้อ</label>
        <input type="text" name="year_of_purchase" value="<?= $data['year_of_purchase'] ?>">
      </div>

      <div class="form-group">
        <label>หมายเหตุ</label>
        <input type="text" name="note" value="<?= $data['note'] ?>">
      </div>

      <button type="submit">บันทึกข้อมูล</button>
    </form>
  </div>

  <script>
    const statusSelect = document.getElementById('statusSelect');
    const borrowFields = document.getElementById('borrowFields');

    function toggleBorrowFields() {
      if (statusSelect.value === 'ถูกยืม') {
        borrowFields.style.display = 'flex';
      } else {
        borrowFields.style.display = 'none';
      }
    }

    statusSelect.addEventListener('change', toggleBorrowFields);
    window.onload = toggleBorrowFields;
  </script>
</body>
</html>

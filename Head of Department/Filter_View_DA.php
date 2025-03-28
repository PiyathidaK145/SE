<?php
// ควรมี include 'connet.php'; หากใช้แยกไฟล์
include dirname(__FILE__) . '/../connet/connect.php';

// ดึงตำแหน่งห้องจากทั้ง r และ r2
$room_query = mysqli_query($conn, "
    SELECT DISTINCT number FROM tb_room
");

// ดึงปีที่ซื้อ
$year_query = mysqli_query($conn, "
    SELECT DISTINCT year_of_purchase FROM tb_durable_articles ORDER BY year_of_purchase DESC
");

// ดึงสถานะการใช้งานล่าสุด
$status_query = mysqli_query($conn, "
    SELECT DISTINCT status_of_use FROM tb_borrowing
");

// ดึงคสภาพการใช้งาน
$condition_query = mysqli_query($conn, "
    SELECT DISTINCT condition_of_use FROM tb_durable_articles
");
?>

<form method="GET" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">

  <!-- Room Filter -->
  <select name="room">
    <option value="">ตำแหน่งปัจจุบัน</option>
    <?php while ($row = mysqli_fetch_assoc($room_query)): ?>
      <option value="<?= $row['number'] ?>" <?= ($_GET['room'] ?? '') == $row['number'] ? 'selected' : '' ?>>
        <?= $row['number'] ?>
      </option>
    <?php endwhile; ?>
  </select>

  <!-- Year of Purchase Filter -->
  <select name="year_of_purchase">
    <option value="">ปีที่ซื้อ</option>
    <?php while ($row = mysqli_fetch_assoc($year_query)): ?>
      <option value="<?= $row['year_of_purchase'] ?>" <?= ($_GET['year_of_purchase'] ?? '') == $row['year_of_purchase'] ? 'selected' : '' ?>>
        <?= $row['year_of_purchase'] ?>
      </option>
    <?php endwhile; ?>
  </select>

  <!-- Status Filter -->
  <select name="status_of_use">
    <option value="">สถานะการใช้งาน</option>
    <?php while ($row = mysqli_fetch_assoc($status_query)): ?>
      <?php 
        // เปลี่ยนค่าที่ดึงมาจากฐานข้อมูล
        switch($row['status_of_use']) {
          case 'Borrowed':
            $status_label = 'ถูกยืม';
            break;
          case 'Free':
            $status_label = 'ว่าง';
            break;
          case 'Unavailable':
            $status_label = 'ไม่สามารถใช้งานได้';
            break;
          default:
            $status_label = $row['status_of_use']; // ค่าเดิมหากไม่ตรงกับเงื่อนไขที่กำหนด
        }
      ?>
      <option value="<?= $row['status_of_use'] ?>" <?= ($_GET['status_of_use'] ?? '') == $row['status_of_use'] ? 'selected' : '' ?>>
        <?= $status_label ?>
      </option>
    <?php endwhile; ?>
  </select>

  <!-- Condition Filter -->
  <select name="condition_of_use">
    <option value="">สภาพการใช้งาน</option>
    <?php while ($row = mysqli_fetch_assoc($condition_query)): ?>
      <?php 
        // เปลี่ยนค่าที่ดึงมาจากฐานข้อมูล
        switch($row['condition_of_use']) {
          case 'Working':
            $condition_label = 'ใช้งานได้';
            break;
          case 'Damaged':
            $condition_label = 'เสียหาย';
            break;
          case 'Broken':
            $condition_label = 'ชำรุด';
            break;
          case 'Sold':
            $condition_label = 'จำหน่ายแล้ว';
            break;
          default:
            $condition_label = $row['condition_of_use']; // ค่าเดิมหากไม่ตรงกับเงื่อนไขที่กำหนด
        }
      ?>
      <option value="<?= $row['condition_of_use'] ?>" <?= ($_GET['condition_of_use'] ?? '') == $row['condition_of_use'] ? 'selected' : '' ?>>
        <?= $condition_label ?>
      </option>
    <?php endwhile; ?>
  </select>

  <!-- Search Bar -->
  <input type="text" name="search" placeholder="ค้นหาชื่อ, ยี่ห้อ, รุ่น..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" style="height: 40px; padding: 0 10px;" />

  <!-- Submit Button -->
  <button type="submit">ค้นหา</button>
</form>

<!-- Script to submit the form when a filter is selected -->
<script>
  document.querySelectorAll("select").forEach(select => {
    select.addEventListener("change", function() {
      this.form.submit();
    });
  });
</script>

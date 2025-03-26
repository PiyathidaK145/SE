<?php
// ควรมี include 'connet.php'; หากใช้แยกไฟล์
include 'connet.php';

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
?>

<form method="GET" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
  
  <!-- ตำแหน่งห้อง -->
  <select name="room" style="width: 200px; height: 40px">
    <option value="">ตำแหน่งปัจจุบัน</option>
    <?php while ($row = mysqli_fetch_assoc($room_query)): ?>
      <option value="<?= $row['number'] ?>" <?= ($_GET['room'] ?? '') == $row['number'] ? 'selected' : '' ?>>
        <?= $row['number'] ?>
      </option>
    <?php endwhile; ?>
  </select>

  <!-- ปีที่ซื้อ -->
  <select name="year" style="width: 200px; height: 40px">
    <option value="">ปีที่ซื้อ</option>
    <?php while ($row = mysqli_fetch_assoc($year_query)): ?>
      <option value="<?= $row['year_of_purchase'] ?>" <?= ($_GET['year'] ?? '') == $row['year_of_purchase'] ? 'selected' : '' ?>>
        <?= $row['year_of_purchase'] ?>
      </option>
    <?php endwhile; ?>
  </select>

  <!-- สถานะการใช้งาน -->
  <!-- สถานะการใช้งาน -->
  <select name="status" style="width: 200px; height: 40px">
    <option value="">สถานะการใช้งาน</option>
    <option value="Borrowed" <?= ($_GET['status'] ?? '') == 'Borrowed' ? 'selected' : '' ?>>ถูกยืม</option>
    <option value="Free" <?= ($_GET['status'] ?? '') == 'Free' ? 'selected' : '' ?>>ว่าง</option>
    <option value="Unavailable" <?= ($_GET['status'] ?? '') == 'Unavailable' ? 'selected' : '' ?>>ไม่พร้อมใช้งาน</option>
  </select>
  <input type="text" name="search" placeholder="ค้นหาชื่อ, ยี่ห้อ, รุ่น..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" style="height: 40px; padding: 0 10px;" />
  <button type="submit" style="width: 50px; height: 40px; background-color: white; border-radius: 10px; border: none; ">🔎</button>
</form>


<script>
  document.querySelectorAll("select").forEach(select => {
    select.addEventListener("change", function() {
      this.form.submit();
    });
  });
</script>
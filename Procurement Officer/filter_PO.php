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

// ดึงสภาพการใช้งาน
$condition_query = mysqli_query($conn, "
    SELECT DISTINCT condition_of_use FROM tb_durable_articles
");
?>

<form method="GET" style="display: flex; gap: 10px; align-items: center; white-space: nowrap; overflow-x: auto; flex-wrap: nowrap;">

    <!-- ตำแหน่งห้อง -->
    <select name="room" style="width: 175px">
        <option value="">ตำแหน่งปัจจุบัน</option>
        <?php while ($row = mysqli_fetch_assoc($room_query)): ?>
            <option value="<?= $row['number'] ?>" <?= ($_GET['room'] ?? '') == $row['number'] ? 'selected' : '' ?>>
                <?= $row['number'] ?>
            </option>
        <?php endwhile; ?>
    </select>

    <!-- ปีที่ซื้อ -->
    <select name="year" style="width: 175px">
        <option value="">ปีที่ซื้อ</option>
        <?php while ($row = mysqli_fetch_assoc($year_query)): ?>
            <option value="<?= $row['year_of_purchase'] ?>" <?= ($_GET['year'] ?? '') == $row['year_of_purchase'] ? 'selected' : '' ?>>
                <?= $row['year_of_purchase'] ?>
            </option>
        <?php endwhile; ?>
    </select>

    <!-- สภาพการใช้งาน -->
    <select name="condition" style="width: 175px">
        <option value="">สภาพการใช้งาน</option>

        <option value="Working" <?= ($_GET['condition'] ?? '') == 'Working' ? 'selected' : '' ?>>ใช้งานได้</option>
        <option value="Broken" <?= ($_GET['condition'] ?? '') == 'Broken' ? 'selected' : '' ?>>ชำรุด</option>
        <option value="Damaged" <?= ($_GET['condition'] ?? '') == 'Damaged' ? 'selected' : '' ?>>เสียหาย</option>
        <option value="Sold" <?= ($_GET['condition'] ?? '') == 'Sold' ? 'selected' : '' ?>>จำหน่ายแล้ว</option>
    </select>
    <input type="text" name="search" placeholder="ค้นหาชื่อ, ยี่ห้อ, รุ่น..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" style="height: 40px; padding: 0 10px;" />
    <button type="submit">ค้นหา</button>
</form>


<script>
    document.querySelectorAll("select").forEach(select => {
        select.addEventListener("change", function() {
            this.form.submit();
        });
    });
</script>
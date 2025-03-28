<?php
include dirname(__FILE__) . '/../connet/connect.php'; // ปรับ path ให้ตรงกับของคุณ

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    $sql = "DELETE FROM tb_durable_articles WHERE durable_articles_id = '$id'";

    if (mysqli_query($conn, $sql)) {
        // ✅ แสดง alert แล้ว redirect กลับไป Final.php
        echo "<script>
                alert('✅ ลบข้อมูลเรียบร้อยแล้ว');
                window.location.href = 'duration_details.php';
              </script>";
        exit();
    } else {
        echo "<script>
                alert('❌ ลบข้อมูลไม่สำเร็จ: " . mysqli_error($conn) . "');
                window.location.href = 'duration_details.php';
              </script>";
        exit();
    }
} else {
    echo "<script>
            alert('ไม่พบ ID ที่ต้องการลบ');
            window.location.href = 'duration_details.php';
          </script>";
    exit();
}
?>

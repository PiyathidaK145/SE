<?php
include dirname(__FILE__) . '/../connet/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    $series = mysqli_real_escape_string($conn, $_POST['series']);
    $durable_articles_number = mysqli_real_escape_string($conn, $_POST['durable_articles_number']);
    $room_id = mysqli_real_escape_string($conn, $_POST['room_id']);
    $serial_number = mysqli_real_escape_string($conn, $_POST['serial_number']);
    $condition_th = mysqli_real_escape_string($conn, $_POST['condition']);
    $condition_map = [
        'ใช้งานได้' => 'Working',
        'ชำรุด' => 'Broken',
        'เสียหาย' => 'Damaged',
        'จำหน่ายแล้ว' => 'Sold',
    ];
    $condition = $condition_map[$condition_th] ?? 'Working';

    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $year = mysqli_real_escape_string($conn, $_POST['year']);
    $warranty = mysqli_real_escape_string($conn, $_POST['warranty']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $note = mysqli_real_escape_string($conn, $_POST['note']);

    $sql = "UPDATE tb_durable_articles SET
            name = '$name',
            brand = '$brand',
            series = '$series',
            durable_articles_number = '$durable_articles_number',
            serial_number = '$serial_number',
            condition_of_use = '$condition',
            price = '$price',
            year_of_purchase = '$year',
            annual_warranty = '$warranty',
            description = '$description',
            note = '$note'
        WHERE durable_articles_id = '$id'";

if (mysqli_query($conn, $sql)) {

    // 2. ตรวจสอบว่าเคยมี borrowing หรือไม่
    $check_borrow = "
        SELECT borrowing_id 
        FROM tb_borrowing 
        WHERE durable_articles_id = '$id'
        ORDER BY time_borrow DESC
        LIMIT 1
    ";
    $borrow_result = mysqli_query($conn, $check_borrow);

    if (mysqli_num_rows($borrow_result) > 0) {
        $borrow = mysqli_fetch_assoc($borrow_result);
        $borrow_id = $borrow['borrowing_id'];

        // อัปเดต room_id ของ borrowing ล่าสุด
        mysqli_query($conn, "
            UPDATE tb_borrowing 
            SET room_id = '$room_id' 
            WHERE borrowing_id = '$borrow_id'
        ");
    } else {
        // เพิ่มข้อมูลใหม่ใน borrowing ถ้าไม่เคยมีมาก่อน
        mysqli_query($conn, "
            INSERT INTO tb_borrowing (durable_articles_id, room_id, status_of_use)
            VALUES ('$id', '$room_id', 'Free')
        ");
    }

    echo "<script>
            alert('✅ แก้ไขข้อมูลเรียบร้อยแล้ว');
            window.location.href = 'duration_details.php';
          </script>";
    exit();
}

    echo "<script>
            alert('❌ ไม่สามารถแก้ไขข้อมูลได้: {$error_msg}');
            window.history.back();
          </script>";
    exit();
}

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $sql = "
        SELECT d.*, b.room_id 
        FROM tb_durable_articles d
        LEFT JOIN tb_borrowing b ON d.durable_articles_id = b.durable_articles_id
        WHERE d.durable_articles_id = '$id'
        ORDER BY b.time_borrow DESC
        LIMIT 1
    ";

    $result = mysqli_query($conn, $sql);

    if (!$result || mysqli_num_rows($result) === 0) {
        echo "<script>
                alert('ไม่พบข้อมูล');
                window.location.href = 'duration_details.php';
              </script>";
        exit();
    }

    $data = mysqli_fetch_assoc($result);
} else {
    echo "<script>
            alert('ไม่พบข้อมูล');
            window.location.href = 'duration_details.php';
          </script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>แก้ไขข้อมูลครุภัณฑ์</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px 40px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        input[type="text"],
        input[type="date"],
        input[type="time"],
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>แก้ไขข้อมูลครุภัณฑ์</h2>
        <?php $rooms_result = mysqli_query($conn, "SELECT room_id, number FROM tb_room ORDER BY number ASC"); ?>
        <form method="post">
            <input type="hidden" name="id" value="<?= $data['durable_articles_id'] ?>">

            <div class="form-group">
                <label>รายการครุภัณฑ์</label>
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
                <input type="text" name="serial_number" value="<?= $data['serial_number'] ?>">
            </div>

            <div class="form-group">
                <label>ตำแหน่งปัจจุบัน</label>
                <select name="room_id">
                    <option value="">-- เลือกห้อง --</option>
                    <?php while ($room = mysqli_fetch_assoc($rooms_result)): ?>
                        <option value="<?= $room['room_id'] ?>"
                            <?= isset($data['room_id']) && $data['room_id'] == $room['room_id'] ? 'selected' : '' ?>>
                            <?= $room['number'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>สภาพการใช้งาน</label>
                <?php
                $condition_options = [
                    'Working' => 'ใช้งานได้',
                    'Broken' => 'ชำรุด',
                    'Damaged' => 'เสียหาย',
                    'Sold' => 'จำหน่ายแล้ว',
                ]; ?>
                <select name="condition">
                    <?php foreach ($condition_options as $eng => $thai): ?>
                        <option value="<?= $thai ?>" <?= $data['condition_of_use'] === $eng ? 'selected' : '' ?>>
                            <?= $thai ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>ราคาต่อหน่วย</label>
                <input type="text" name="price" value="<?= $data['price'] ?>">
            </div>

            <div class="form-group">
                <label>ปีที่ซื้อ</label>
                <input type="text" name="year" value="<?= $data['year_of_purchase'] ?>">
            </div>

            <div class="form-group">
                <label>รับประกัน</label>
                <input type="text" name="warranty" value="<?= $data['annual_warranty'] ?>">
            </div>

            <div class="form-group">
                <label>เพิ่มเติม</label>
                <input type="text" name="description" value="<?= $data['description'] ?>">
            </div>

            <div class="form-group">
                <label>หมายเหตุ</label>
                <input type="text" name="note" value="<?= $data['note'] ?>">
            </div>

            <button type="submit">บันทึกข้อมูล</button>
        </form>
    </div>
</body>

</html>
<?php
include dirname(__FILE__) . '/../connet/connect.php';

$successCount = 0;
$duplicateCount = 0;
$errorCount = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file']['tmp_name'];

    if (($handle = fopen($file, 'r')) !== false) {
        fgetcsv($handle); // ข้ามหัวตาราง

        while (($data = fgetcsv($handle)) !== false) {
            foreach ($data as &$field) {
                $field = trim($field);
            }

            if (count($data) < 11) {
                $errorCount++;
                continue;
            }

            $name = mysqli_real_escape_string($conn, $data[0]);
            $brand = mysqli_real_escape_string($conn, $data[1]);
            $series = mysqli_real_escape_string($conn, $data[2]);
            $durable_articles_number = mysqli_real_escape_string($conn, $data[3]);
            $serial_number = mysqli_real_escape_string($conn, $data[4]);

            $condition_th = trim($data[5]);
            switch ($condition_th) {
                case 'เสียหาย':
                case 'ชำรุด':
                case 'ใช้ไม่ได้':
                    $condition_of_use = 'Broken';
                    break;
                case 'ใช้งานได้':
                case 'ใช้ได้':
                case 'ปกติ':
                    $condition_of_use = 'Working';
                    break;
                default:
                    $condition_of_use = 'Unknown';
            }
            $condition_of_use = mysqli_real_escape_string($conn, $condition_of_use);

            $price = mysqli_real_escape_string($conn, $data[6]);
            $year_of_purchase = mysqli_real_escape_string($conn, $data[7]);
            $annual_warranty = mysqli_real_escape_string($conn, $data[8]);
            $description = mysqli_real_escape_string($conn, $data[9]);
            $note = mysqli_real_escape_string($conn, $data[10]);

            // ตรวจสอบว่ามีข้อมูลนี้อยู่หรือยัง
            $check_sql = "SELECT * FROM tb_durable_articles WHERE durable_articles_number = '$durable_articles_number' AND `serial number` = '$serial_number'";
            $check_result = mysqli_query($conn, $check_sql);

            if (mysqli_num_rows($check_result) > 0) {
                $existing = mysqli_fetch_assoc($check_result);

                $current_data = [
                    "name" => $name,
                    "brand" => $brand,
                    "series" => $series,
                    "durable_articles_number" => $durable_articles_number,
                    "serial number" => $serial_number,
                    "condition_of_use" => $condition_of_use,
                    "price" => $price,
                    "year_of_purchase" => $year_of_purchase,
                    "annual_warranty" => $annual_warranty,
                    "description" => $description,
                    "note" => $note
                ];

                $existing_hash = md5(implode("|", array_map('trim', [
                    $existing["name"], $existing["brand"], $existing["series"], $existing["durable_articles_number"],
                    $existing["serial number"], $existing["condition_of_use"], $existing["price"], $existing["year_of_purchase"],
                    $existing["annual_warranty"], $existing["description"], $existing["note"]
                ])));

                $current_hash = md5(implode("|", array_map('trim', array_values($current_data))));

                if ($existing_hash === $current_hash) {
                    $duplicateCount++;
                } else {
                    $update_sql = "UPDATE tb_durable_articles SET 
                        name = '$name',
                        brand = '$brand',
                        series = '$series',
                        condition_of_use = '$condition_of_use',
                        price = '$price',
                        year_of_purchase = '$year_of_purchase',
                        annual_warranty = '$annual_warranty',
                        description = '$description',
                        note = '$note'
                        WHERE durable_articles_number = '$durable_articles_number' AND `serial number` = '$serial_number'";
                    if (mysqli_query($conn, $update_sql)) {
                        $successCount++;
                    } else {
                        $errorCount++;
                    }
                }
            } else {
                $insert_sql = "INSERT INTO tb_durable_articles 
                    (name, brand, series, durable_articles_number, `serial number`, condition_of_use, price, year_of_purchase, annual_warranty, description, note)
                    VALUES 
                    ('$name', '$brand', '$series', '$durable_articles_number', '$serial_number', '$condition_of_use', '$price', '$year_of_purchase', '$annual_warranty', '$description', '$note')";

                if (mysqli_query($conn, $insert_sql)) {
                    $successCount++;
                } else {
                    $errorCount++;
                }
            }
        }

        fclose($handle);
    }

    // echo "✅ สำเร็จ: $successCount รายการ | ";
    // echo "❌ ล้มเหลว: $errorCount รายการ | ";
    // echo "⚠️ ซ้ำ : $duplicateCount รายการ";
}

// ✅ ปรับปรุง SQL Query
$sql = "
SELECT 
    d.durable_articles_id,
    d.name,
    d.brand,
    d.series,
    d.durable_articles_number,
    d.`serial number`,
    d.year_of_purchase,
    r.number AS current_location, 
    COALESCE(b.status_of_use, d.condition_of_use) AS usage_condition,
    d.price,
    d.annual_warranty,
    d.description,
    d.note
FROM tb_durable_articles d
LEFT JOIN tb_borrowing b 
    ON d.durable_articles_id = b.durable_articles_id
    AND b.time_borrow = (
        SELECT MAX(time_borrow)
        FROM tb_borrowing
        WHERE durable_articles_id = d.durable_articles_id
    )
LEFT JOIN tb_room r ON b.room_id = r.room_id
ORDER BY d.condition_of_use ASC;
";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <title>แสดงครุภัณธ์</title>
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Prompt&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<style>
    .action-btn {
            display: inline-block;
            padding: 8px;
            border-radius: 50%;
            text-align: center;
            transition: background 0.2s;
        }

        .action-btn:hover {
            background: #f0f0f0;
        }
</style>
<body>
    <div class="sidebar">
        <div class="logo">
            <img src="../image/logo.jpg" alt="" style="width: 200px;">
        </div>
        <div class="profile">
            <img src="https://i.pravatar.cc/50?img=3" alt="Profile" />
            <div>
                <h4>David Grey. H</h4>
                <span>Project Manager</span>
            </div>
        </div>
        <ul class="menu">
            <button class="logout">logout</button>
        </ul>
    </div>

    <div class="main">
        <div class="upload-form">
            <form action="" method="post" enctype="multipart/form-data">
                <label for="csv_file">📥 อัปโหลดไฟล์ CSV:</label>
                <input type="file" name="csv_file" id="csv_file" accept=".csv" required>
                <button type="submit">นำเข้า</button>
            </form>
            <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'  ): ?>
                <div class="msg">
                    ✅ สำเร็จ: <?= $successCount ?> รายการ |
                    ❌ ล้มเหลว: <?= $errorCount ?> รายการ |
                    ⚠️ ซ้ำ : <?= $duplicateCount ?> รายการ    
                </div>
            <?php endif; ?>
        </div>

        <div class="table">
            <table>
                <thead>
                    <tr>
                        <th>ลำดับ</th>
                        <th>รายการครุภัณธ์</th>
                        <th>ยี่ห้อ</th>
                        <th>รุ่น</th>
                        <th>หมายเลขครุภัณธ์</th>
                        <th>หมายเลขเครื่อง</th>
                        <th>ตำแหน่งปัจจุบัน</th>
                        <th>สภาพการใช้งาน</th>
                        <th>ราคาต่อหน่วย</th>
                        <th>ปีที่ซื้อ</th>
                        <th>รับประกัน</th>
                        <th>เพิ่มเติม</th>
                        <th>หมายเหตุ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $count = 1;
                    while ($row = mysqli_fetch_assoc($result)) {
                        switch ($row['usage_condition']) {
                            case 'Working': $status_th = 'ใช้งานได้'; break;
                            case 'Broken':  $status_th = 'ชำรุด'; break;
                            case 'Damaged': $status_th = 'เสียหาย'; break;
                            case 'Unknown': $status_th = 'ไม่ทราบ'; break;
                            default: $status_th = '-';
                        }
                        echo "<tr>";
                        echo "<td>{$count}</td>";
                        echo "<td>{$row['name']}</td>";
                        echo "<td>{$row['brand']}</td>";
                        echo "<td>{$row['series']}</td>";
                        echo "<td>{$row['durable_articles_number']}</td>";
                        echo "<td>{$row['serial number']}</td>";
                        echo "<td>" . ($row['current_location'] ?? '-') . "</td>";
                        echo "<td>{$status_th}</td>";
                        echo "<td>" . number_format($row['price'], 2) . "</td>";
                        echo "<td>{$row['year_of_purchase']}</td>";
                        echo "<td>{$row['annual_warranty']}</td>";
                        echo "<td>{$row['description']}</td>";
                        echo "<td>{$row['note']}</td>";
                        echo "<td style='white-space: nowrap;'>
                                <a href='edit.php?id={$row['durable_articles_id']}' class='action-btn' title='Edit'>
                                    <i class='fas fa-edit' style='color: #4CAF50;'></i>
                                </a>
                                <a href='delete.php?id={$row['durable_articles_id']}' class='action-btn' title='Delete' onclick='return confirm(\"Are you sure you want to delete this item?\");'>
                                    <i class='fas fa-trash-alt' style='color: #f44336;'></i>
                                </a>
                            </td>";
                        echo "</tr>";
                        $count++;
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

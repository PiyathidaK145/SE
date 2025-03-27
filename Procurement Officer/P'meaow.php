<?php

include dirname(__FILE__) . '/../connet/connect.php';



$search = $_GET['search'] ?? '';
$where = [];

if (!empty($search)) {
    $like = mysqli_real_escape_string($conn, $search);
    $where[] = "
        (
            d.name LIKE '%$like%' OR
            d.brand LIKE '%$like%' OR
            d.series LIKE '%$like%' OR
            d.durable_articles_number LIKE '%$like%' OR
            d.`serial number` LIKE '%$like%'
        )
    ";
}
if (!empty($_GET['room'])) {
    $room = mysqli_real_escape_string($conn, $_GET['room']);
    $where[] = "r.number = '$room'";
}

if (!empty($_GET['year'])) {
    $year = mysqli_real_escape_string($conn, $_GET['year']);
    $where[] = "d.year_of_purchase = '$year'";
}
if (!empty($_GET['status'])) {
    $status = mysqli_real_escape_string($conn, $_GET['status']);

    if ($status === 'Free') {
        // กรณี Free ต้องรวมทั้ง status_of_use = 'Free' และ NULL
        $where[] = "(b.status_of_use = 'Free' OR b.status_of_use IS NULL)";
    } else {
        $where[] = "b.status_of_use = '$status'";
    }
}

$where_sql = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

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
    b.status_of_use AS usage_condition,
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
$where_sql
";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Purple Dashboard</title>
    <link rel="stylesheet" href="../css/style_mem.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Prompt&display=swap" rel="stylesheet">
</head>

<body>
    <div class="sidebar">
        <div class="logo">
            <img src="../image/logo.jpg" alt="" style="width: 200px;">
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
        <div class="topbar">
            <?php include 'flutter.php'; ?>
        </div>

        <div class="table">
            <table>
                <thead>
                    <tr>
                        <th>ลำดับ </th>
                        <th>รายการครุภัณฑ์</th>
                        <th>ยี่ห้อ</th>
                        <th>รุ่น</th>
                        <th>หมายเลขครุภัณฑ์</th>
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
                    <?php
                    $count = 1;
                    while ($row = mysqli_fetch_assoc($result)) {
                        $modalId = "modal_" . $row['durable_articles_id']; // สร้าง ID เฉพาะของ modal

                        echo "<tr>";
                        echo "<td>" . $count++ . "</td>";
                        echo "<td>" . $row['name'] . "</td>";  
                        echo "<td>" . $row['brand'] . "</td>";  
                        echo "<td>" . $row['series'] . "</td>";  
                        echo "<td>" . $row['durable_articles_number'] . "</td>";  
                        echo "<td>" . $row['serial number'] . "</td>";  
                        echo "<td>" . $row['current_location'] . "</td>";  
                        echo "<td>" . $row['usage_condition'] . "</td>";  
                        echo "<td>" . number_format($row['price'], 2) . "</td>";  
                        echo "<td>" . $row['year_of_purchase'] . "</td>";  
                        echo "<td>" . $row['annual_warranty'] . "</td>";  
                        echo "<td>" . $row['description'] . "</td>";  
                        echo "<td>" . $row['note'] . "</td>";  

                        echo "</tr>";

                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div>
</body>
</html>
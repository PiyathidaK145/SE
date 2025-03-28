<?php

include dirname(__FILE__) . '/../connet/connect.php';

// ดึงตำแหน่งทั้งหมดจาก tb_room
$room_sql = "SELECT DISTINCT number FROM tb_room ORDER BY number";
$room_result = mysqli_query($conn, $room_sql);

// ดึงปีที่ซื้อจาก tb_durable_articles
$year_sql = "SELECT DISTINCT year_of_purchase FROM tb_durable_articles ORDER BY year_of_purchase DESC";
$year_result = mysqli_query($conn, $year_sql);

// ดึงสถานะการใช้งานจาก tb_borrowing (หรืออาจจะใช้ค่าคงที่ที่มีในฐานข้อมูล)
$status_sql = "SELECT DISTINCT status_of_use FROM tb_borrowing";
$status_result = mysqli_query($conn, $status_sql);

// ดึงสภาพการใช้งานจาก tb_durable_articles
$condition_sql = "SELECT DISTINCT condition_of_use FROM tb_durable_articles";
$condition_result = mysqli_query($conn, $condition_sql);

// ฟิลเตอร์ที่เลือก
$selected_room = isset($_GET['room']) ? $_GET['room'] : '';
$selected_year = isset($_GET['year_of_purchase']) ? $_GET['year_of_purchase'] : '';
$selected_status = isset($_GET['status_of_use']) ? $_GET['status_of_use'] : '';
$search_query = isset($_GET['search']) ? $_GET['search'] : ''; // รับค่าการค้นหาจาก search-bar
$selected_condition = isset($_GET['condition_of_use']) ? $_GET['condition_of_use'] : '';


// จำนวนรายการที่จะแสดงต่อหน้า
$items_per_page = 10;

// ตรวจสอบหน้าปัจจุบัน ถ้าไม่มีให้เป็นหน้า 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

// Query สำหรับดึงข้อมูล durable_articles โดยใช้ฟิลเตอร์และคำค้นหา
$sql = "SELECT 
            tb_borrowing.*, 
            tb_durable_articles.*, 
            tb_member.*, 
            tb_room.* 
        FROM 
            tb_borrowing
        LEFT JOIN 
            tb_durable_articles ON tb_borrowing.durable_articles_id = tb_durable_articles.durable_articles_id
        LEFT JOIN 
            tb_member ON tb_borrowing.member_id = tb_member.member_id
        LEFT JOIN 
            tb_room ON tb_borrowing.room_id = tb_room.room_id
        WHERE 1";

// การตรวจสอบฟิลเตอร์และเพิ่มเงื่อนไขการค้นหา
if ($selected_room) {
    $sql .= " AND tb_room.number = '$selected_room'";
}
if ($selected_year) {
    $sql .= " AND tb_durable_articles.year_of_purchase = '$selected_year'";
}
if ($selected_status) {
    $sql .= " AND tb_borrowing.status_of_use = '$selected_status'";
}
if ($selected_condition) {
    $sql .= " AND tb_durable_articles.condition_of_use = '$selected_condition'";
}
if ($search_query) {
    $sql .= " AND (tb_durable_articles.name LIKE '%$search_query%' OR tb_durable_articles.durable_articles_number LIKE '%$search_query%')";
}

$sql .= " LIMIT $items_per_page OFFSET $offset";

$result = $conn->query($sql);

// Query เพื่อนับจำนวนแถวทั้งหมดที่ตรงกับเงื่อนไข
$count_sql = "SELECT COUNT(*) FROM tb_borrowing
            LEFT JOIN tb_durable_articles ON tb_borrowing.durable_articles_id = tb_durable_articles.durable_articles_id
            LEFT JOIN tb_room ON tb_borrowing.room_id = tb_room.room_id
            WHERE 1";

if ($selected_room) {
    $count_sql .= " AND tb_room.number = '$selected_room'";
}
if ($selected_year) {
    $count_sql .= " AND tb_durable_articles.year_of_purchase = '$selected_year'";
}
if ($selected_status) {
    $count_sql .= " AND tb_borrowing.status_of_use = '$selected_status'";
}
if ($selected_condition) {
    $count_sql .= " AND tb_durable_articles.condition_of_use = '$selected_condition'";
}
if ($search_query) {
    $count_sql .= " AND (tb_durable_articles.name LIKE '%$search_query%' OR tb_durable_articles.durable_articles_number LIKE '%$search_query%')";
}

$count_result = mysqli_query($conn, $count_sql);
$total_rows = mysqli_fetch_array($count_result)[0];
$total_pages = ceil($total_rows / $items_per_page);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ดูตำแหน่งครุภัณฑ์</title>
    <link rel="stylesheet" href="../css/View_DA.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Prompt&display=swap" rel="stylesheet">
</head>

<body>
    <div class="sidebar">
        <div class="logo">
            <img src="image/logo.jpg" alt="" style="width: 200px;">
            <div class="profile">
                <img src="https://i.pravatar.cc/50?img=3" alt="Profile" />
                <div>
                    <h4>David Grey. H</h4>
                    <span>Project Manager</span>
                </div>
            </div>
            <ul class="menu">
                <li>หน้าหลัก</li>
                <li class="active">ดูตำแหน่งครุภัณฑ์</li>
                <li>รายละเอียดครุภัณฑ์</li>
            </ul>
        </div>
    </div>

    <div class="main">
        <div class="topbar">
            <?php include 'Filter_View_DA.php'; ?>
        </div>

        <div class="table">
            <table>
                <thead>
                    <tr>
                        <th>ลำดับ </th>
                        <th>ชื่อ</th>
                        <th>ยี่ห้อ</th>
                        <th>รุ่น</th>
                        <th>หมายเลขครุภัณฑ์</th>
                        <th>หมายเลขเครื่อง</th>
                        <th>ตำแหน่งปัจจุบัน</th>
                        <th>ปีที่ซื้อ</th>
                        <th>สภาพการใช้งาน</th>
                        <th>หมายเหตุ</th>
                        <th>สถานะการใช้งาน</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $count = 1;
                    while ($row = mysqli_fetch_assoc($result)) {
                        $status_display = $row['status_of_use'] === 'Borrowed' ? 'ถูกยืม' : 'ว่าง';
                        $modalId = "modal_" . $row['durable_articles_id']; // สร้าง ID เฉพาะของ modal
                        $Date = empty($row['time_borrow']) ? '-' : ($row['time_borrow']);
                        $borrower = $row['academic_ranks'] . $row['first_name'] . " " . $row['last_name'];

                        echo "<tr>";
                        echo "<td>" . $count++ . "</td>";
                        echo "<td>" . $row['name'] . "</td>";
                        echo "<td>" . $row['brand'] . "</td>";
                        echo "<td>" . $row['series'] . "</td>";
                        echo "<td>" . $row['durable_articles_number'] . "</td>";
                        echo "<td>" . $row['serial_number'] . "</td>";
                        echo "<td>" . ($row['number'] ?? '-') . "</td>";
                        echo "<td>" . $row['year_of_purchase'] . "</td>";
                        if (!empty($row['condition_of_use'])) {
                            if ($row['condition_of_use'] == 'Working') {
                                echo "<td>ใช้งานได้</td>";
                            } elseif ($row['condition_of_use'] == 'Broken') {
                                echo "<td>ชำรุด</td>";
                            } elseif ($row['condition_of_use'] == 'Damaged') {
                                echo "<td>เสียหาย</td>";
                            } elseif ($row['condition_of_use'] == 'Sold') {
                                echo "<td>จำหน่ายแล้ว</td>";
                            } else {
                                echo "<td>" . $row['condition_of_use'] . "</td>";  // ถ้าไม่ตรงกับเงื่อนไขใดๆ ก็แสดงค่าที่มี
                            }
                        } else {
                            echo "<td>ไม่พบข้อมูล</td>";  // ถ้าไม่มีค่า
                        }
                        echo "<td>" . (!empty($row['note']) ? $row['note'] : '-') . "</td>";

                        if ($row['status_of_use'] === 'Borrowed') {
                            echo "<td style='color: #d9241a; cursor: pointer;' onclick=\"document.getElementById('$modalId').style.display='block'\">" . $status_display . "</td>";
                        } else {
                            echo "<td style='color: #3bd636; cursor: default;'>" . $status_display . "</td>";
                        }

                        echo "</tr>";

                        // ✅ Modal แสดงนอกแถว แต่ภายในลูป
                        if ($row['status_of_use'] === 'Borrowed') {
                            echo "
                            <div id='$modalId' style='display:none; position:fixed; z-index:1; left:0; top:0; width:100%; height:100%; background-color:rgba(0,0,0,0.5)'>
                                <div style='background-color:white; margin:15% auto; padding:20px; width:350px; border-radius:10px;'>
                                    <span style='float:right; cursor:pointer;' onclick=\"document.getElementById('$modalId').style.display='none'\">&times;</span>
                                    <h3>📄 รายละเอียดการยืม</h3>
                                    <p></p>
                                    <p>👨‍🦱 ผู้ยืม : $borrower</p>
                                    <p>⏰ วัน/เวลาที่ยืม : $Date</p>
                                </div>
                            </div>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div>
</body>

</html>

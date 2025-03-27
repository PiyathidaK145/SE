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

// ฟิลเตอร์ที่เลือก
$selected_room = isset($_GET['room']) ? $_GET['room'] : '';
$selected_year = isset($_GET['year_of_purchase']) ? $_GET['year_of_purchase'] : '';
$selected_status = isset($_GET['status_of_use']) ? $_GET['status_of_use'] : '';
$search_query = isset($_GET['search']) ? $_GET['search'] : ''; // รับค่าการค้นหาจาก search-bar

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
    <title>Purple Dashboard</title>
    <link rel="stylesheet" href="../css/View_DA.css?v=1.0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>

<body>
    <div class="sidebar">
        <div class="logo">
            <img src="image/logo.jpg" alt="" style="width: 200px;">
        </div>
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

    <div class="main">
        <div class="topbar">
            <form method="GET" action="">
                <div class="filters">
                    <div class="header-row">
                        <!-- Dropdown ฟิลเตอร์ตำแหน่งปัจจุบัน -->
                        <select name="room" onchange="this.form.submit()">
                            <option selected disabled hidden>ตำแหน่งปัจจุบัน</option>
                            <?php
                            if (mysqli_num_rows($room_result) > 0) {
                                while ($row = mysqli_fetch_assoc($room_result)) {
                                    $selected = ($row['number'] == $selected_room) ? 'selected' : '';
                                    echo "<option value='" . $row['number'] . "' $selected>" . $row['number'] . "</option>";
                                }
                            }
                            ?>
                        </select>

                        <!-- Dropdown ฟิลเตอร์ปีที่ซื้อ -->
                        <select name="year_of_purchase" onchange="this.form.submit()">
                            <option selected disabled hidden>ปีที่ซื้อ</option>
                            <?php
                            if (mysqli_num_rows($year_result) > 0) {
                                while ($row = mysqli_fetch_assoc($year_result)) {
                                    $selected = ($row['year_of_purchase'] == $selected_year) ? 'selected' : '';
                                    echo "<option value='" . $row['year_of_purchase'] . "' $selected>" . $row['year_of_purchase'] . "</option>";
                                }
                            }
                            ?>
                        </select>

                        <!-- Dropdown ฟิลเตอร์สถานะการใช้งาน -->
                        <select name="status_of_use" onchange="this.form.submit()">
                            <option selected disabled hidden>สถานะการใช้งาน</option>
                            <?php
                            if (mysqli_num_rows($status_result) > 0) {
                                while ($row = mysqli_fetch_assoc($status_result)) {
                                    $status_display = ($row['status_of_use'] == 'Borrowed') ? 'ถูกยืม' : 'ว่าง';
                                    $selected = ($row['status_of_use'] == $selected_status) ? 'selected' : '';
                                    echo "<option value='" . $row['status_of_use'] . "' $selected>" . $status_display . "</option>";
                                }
                            }
                            ?>
                        </select>

                        <div class="search-bar">
                            <!-- ฟิลด์ค้นหาครุภัณฑ์ -->
                            <input type="text" name="search" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>" placeholder="ค้นหาชื่อ,หมายเลขครุภัณฑ์...">
                            <button type="submit">ค้นหา</button>
                            <!-- <button><a href="View_DA.php">Reset</a></button> -->
                        </div>
                    </div>


                </div>
            </form>
        </div>
        <div class="content">
            <table>
                <thead>
                    <tr>
                        <th>no</th>
                        <th>ชื่อ</th>
                        <th>หมายเลขครุภัณฑ์</th>
                        <th>หมายเลขเครื่อง</th>
                        <th>ตำแหน่งปัจจุบัน</th>
                        <th>สภาพการใช้งาน</th>
                        <th>สถานะการใช้งาน</th>
                        <th>ปีที่ซื้อ</th>
                        <th>หมายเหตุ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . (!empty($row['durable_articles_id']) ? $row['durable_articles_id'] : 'ไม่พบข้อมูล') . "</td>";
                            echo "<td>" . (!empty($row['name']) ? $row['name'] : 'ไม่พบข้อมูล') . "</td>";
                            echo "<td>" . (!empty($row['durable_articles_number']) ? $row['durable_articles_number'] : 'ไม่พบข้อมูล') . "</td>";
                            echo "<td>" . (!empty($row['serial number']) ? $row['serial number'] : 'ไม่พบข้อมูล') . "</td>";
                            echo "<td>" . (!empty($row['number']) ? $row['number'] : 'ไม่พบตำแหน่ง') . "</td>";
                            if (!empty($row['condition_of_use'])) {
                                if ($row['condition_of_use'] == 'Working') {
                                    echo "<td>ใช้งานได้</td>";
                                } elseif ($row['condition_of_use'] == 'Broken') {
                                    echo "<td>ชำรุด</td>";
                                } elseif ($row['condition_of_use'] == 'Damage') {
                                    echo "<td>เสียหาย</td>";
                                } elseif ($row['condition_of_use'] == 'Sold') {
                                    echo "<td>จำหน่ายแล้ว</td>";
                                } else {
                                    echo "<td>" . $row['condition_of_use'] . "</td>";  // ถ้าไม่ตรงกับเงื่อนไขใดๆ ก็แสดงค่าที่มี
                                }
                            } else {
                                echo "<td>ไม่พบข้อมูล</td>";  // ถ้าไม่มีค่า
                            }

                            // แก้ไขส่วนของสถานะการใช้งานให้เป็นปุ่ม pop-up

                            if (!empty($row['status_of_use']) && $row['status_of_use'] == 'Borrowed') {
                                // ถ้า status_of_use เป็น "Borrowed"
                                echo "<td><button style=\"background: none; border: none; text-decoration: underline; color: inherit; cursor: pointer;\" onclick=\"showPopup('" . $row['academic_ranks'] . "', '" . $row['first_name'] . "', '" . $row['last_name'] . "', '" . $row['time_borrow'] . "')\">ถูกยืม</button></td>";
                            } else {
                                // ถ้า status_of_use ไม่ใช่ "Borrowed"
                                echo "<td>ว่าง</td>";
                            }


                            echo "<td>" . (!empty($row['year_of_purchase']) ? $row['year_of_purchase'] : 'ไม่พบข้อมูล') . "</td>";
                            echo "<td>" . (!empty($row['note']) ? $row['note'] : '-') . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9' style='text-align:center;'>ไม่พบข้อมูลในฐานข้อมูล</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <!-- Pop-up -->
            <div id="popup" class="popup" style="display:none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); padding: 20px; background-color: white; border: 1px solid black;">
                <span id="closePopup" style="cursor: pointer; float: right;">&times;</span>
                <h3>รายละเอียดการยืม</h3>
                <p><strong>ผู้ยืม:</strong> <span id="borrowerRole"></span> <span id="borrowerName"></span> <span id="borrowerLastName"></span></p>
                <p><strong>วันเวลาที่ยืม:</strong> <span id="borrowDate"></span></p>
            </div>

            <!-- แสดงปุ่มนำทางหน้า -->
            <div class="pagination">
                <?php
                if ($total_pages > 1) {
                    // ปุ่มย้อนกลับ
                    if ($page > 1) {
                        echo '<a href="?page=' . ($page - 1) . '">ย้อนกลับ</a>';
                    }

                    // ปุ่มหน้าปัจจุบันและหน้าทั้งหมด
                    for ($i = 1; $i <= $total_pages; $i++) {
                        if ($i == $page) {
                            echo '<span class="current-page">' . $i . '</span>';
                        } else {
                            echo '<a href="?page=' . $i . '">' . $i . '</a>';
                        }
                    }

                    // ปุ่มถัดไป
                    if ($page < $total_pages) {
                        echo '<a href="?page=' . ($page + 1) . '">ถัดไป</a>';
                    }
                }
                ?>
            </div>
        </div>
    </div>
</body>

</html>
<script>
    // ฟังก์ชันสำหรับแสดงป๊อปอัป
    function showPopup(role, firstName, lastName, borrowDate) {
        document.getElementById("borrowerRole").innerText = role;
        document.getElementById("borrowerName").innerText = firstName;
        document.getElementById("borrowerLastName").innerText = lastName;
        document.getElementById("borrowDate").innerText = borrowDate;
        document.getElementById("popup").style.display = "block";
    }

    // ฟังก์ชันสำหรับปิดป๊อปอัป
    document.getElementById("closePopup").onclick = function() {
        document.getElementById("popup").style.display = "none";
    }

    // ปิดป๊อปอัปเมื่อคลิกภายนอก
    window.onclick = function(event) {
        var popup = document.getElementById("popup");
        if (event.target == popup) {
            popup.style.display = "none";
        }
    }
</script>

<?php

session_start();
include dirname(__FILE__) . '/../connet/connect.php';
if (empty($_SESSION[WP . 'checklogin'])) {
    $_SESSION['message']  = "ยังไม่ได้เข้าสู่ระบบ";
    header("Location: {$base_url}/login.php");
}

$member_id = $_SESSION[WP . 'member_id'];
$query = mysqli_query($conn, "SELECT * FROM tb_member WHERE member_id = '{$member_id}'") or die('query failed');
$user = mysqli_fetch_assoc($query);

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
            d.`serial_number` LIKE '%$like%'
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
        // ✅ เพิ่มเงื่อนไขไม่เอา Broken หรือ Damaged
        $where[] = "( (b.status_of_use = 'Free' OR b.status_of_use IS NULL) AND d.condition_of_use = 'Working' )";
    } elseif ($status === 'Borrowed') {
        $where[] = "b.status_of_use = 'Borrowed'";
    } elseif ($status === 'Unavailable') {
        $where[] = "(d.condition_of_use IN ('Broken', 'Damaged'))";
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
    d.`serial_number`,
    d.year_of_purchase,
    d.condition_of_use,
    r.number,
    b.status_of_use,
    b.time_borrow,
    b.member_id,
    m.academic_ranks,
    m.first_name,
    m.last_name
FROM tb_durable_articles d
LEFT JOIN tb_borrowing b
    ON d.durable_articles_id = b.durable_articles_id
    AND b.time_borrow = (
        SELECT MAX(time_borrow)
        FROM tb_borrowing
        WHERE durable_articles_id = d.durable_articles_id
    )
LEFT JOIN tb_room r ON b.room_id = r.room_id
LEFT JOIN tb_member m ON b.member_id = m.member_id
$where_sql
";
$result = mysqli_query($conn, $sql);
$status_display = "";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Purple Dashboard</title>
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/style_dash.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Prompt&display=swap" rel="stylesheet">
    <script src="../js/sort.js"></script>
</head>

<body>
    <div class="sidebar">
        <div class="logo">
            <img src="../image/logo.jpg" alt="" style="width: 200px;">
        </div>

        <div class="profile">
            <div>
                <h4><?php echo $user['first_name']; ?> <?php echo $user['last_name']; ?></h4><span>บุคลากรในภาควิชา</span>
                <a class="logout" href="<?php echo $base_url . '/logout.php'; ?>">Logout</a>
            </div>
        </div>
        <ul class="menu">
            <li class="active">ดูตำแหน่งครุภัณฑ์</li> <br>
        </ul>
    </div>

    <div class="main">
        <div class="topbar">
            <?php include 'filter.php'; ?>
        </div>

        <div class="table">
            <table id="durableArticlesTable">
                <thead>
                    <tr>
                        <th>ลำดับ </th>
                        <th>ชื่อ</th>
                        <th>ยี่ห้อ</th>
                        <th>รุ่น</th>
                        <th>หมายเลขครุภัณฑ์</th>
                        <th>หมายเลขเครื่อง</th>
                        <th>ตำแหน่งปัจจุบัน</th>
                        <th class="sortable" onclick="sortTable()">ปีที่ซื้อ <i class="fa fa-sort"></i></th>
                        <th>สถานะการใช้งาน</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $count = 1;
                    $status_display = '';
                    while ($row = mysqli_fetch_assoc($result)) {
                        if ($row['status_of_use'] === 'Borrowed') {
                            $status_display = 'ถูกยืม';
                        } elseif (in_array($row['condition_of_use'], ['Broken', 'Damaged'])) {
                            $status_display = 'ไม่พร้อมใช้งาน';
                        } elseif ($row['condition_of_use'] === 'Working') {
                            $status_display = 'ว่าง';
                        }

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

                        if ($row['status_of_use'] === 'Borrowed') {
                            echo "<td style='color: red; cursor: pointer; text-decoration: underline;' onclick=\"document.getElementById('$modalId').style.display='block'\">" . $status_display . "</td>";
                        } elseif (in_array($row['condition_of_use'], ['Broken', 'Damaged', 'Sold'])) {
                            echo "<td style='color: #aaa; cursor: default;'>" . $status_display . "</td>";
                        } elseif ($row['condition_of_use'] === 'Working' and $row['status_of_use'] === 'Free') {
                            echo "<td style='color: green; cursor: pointer;'>" . $status_display . "</td>";
                        } else {
                            echo "<td style='color: green; cursor: pointer;'>" . $status_display . "</td>";
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
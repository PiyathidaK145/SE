<?php

include dirname(__FILE__) . '/../connet/connect.php';
include_once 'borrow_modal.php';


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
            d.serial_number LIKE '%$like%'
        )
    ";
}

//ตำแหน่งห้อง
if (!empty($_GET['room'])) {
  $room = mysqli_real_escape_string($conn, $_GET['room']);
  $where[] = "r.number = '$room'";
}

//ปีที่ซื้อ
if (!empty($_GET['year'])) {
  $year = mysqli_real_escape_string($conn, $_GET['year']);
  $where[] = "d.year_of_purchase = '$year'";
}

//สภาพการใช้งาน
if (!empty($_GET['condition'])) {
  $condition = mysqli_real_escape_string($conn, $_GET['condition']);

  $where[] = "d.condition_of_use = '$condition'";
}

//หมายเหตุ
if (!empty($_GET['note'])) {
  $note = mysqli_real_escape_string($conn, $_GET['note']);

  $where[] = "d.note = '$note'";
}
//สถานะการใช้งาน
if (!empty($_GET['status'])) {
  $status = mysqli_real_escape_string($conn, $_GET['status']);

  if ($status === 'Free') {
    $where[] = "( (b.status_of_use = 'Free' OR b.status_of_use IS NULL) AND d.condition_of_use = 'Working' )";
  } elseif ($status === 'Borrowed') {
    $where[] = "b.status_of_use = 'Borrowed'";
  } elseif ($status === 'Unavailable') {
    $where[] = "(d.condition_of_use IN ('Broken', 'Damaged', 'Sold'))";
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
    d.serial_number,
    d.condition_of_use,
    d.year_of_purchase,
    d.condition_of_use,
    r.number, 
    b.status_of_use,
    d.note,
    b.time_borrow,
    b.borrowing_id,
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ดูตำแหน่งครุภัณฑ์</title>
  <link rel="stylesheet" href="../css/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Prompt&display=swap" rel="stylesheet">
</head>

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
      <li class="active">ดูตำแหน่งครุภัณฑ์</li>
    </ul>
  </div>

  <div class="main">
    <div class="topbar">
      <?php include 'filter.php'; ?>
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
          $status_display = '';
          $condition_display = '';
          while ($row = mysqli_fetch_assoc($result)) {
            if ($row['status_of_use'] === 'Borrowed') {
              $status_display = 'ถูกยืม';
            } elseif (in_array($row['condition_of_use'], ['Broken', 'Damaged', 'Sold'])) {
              $status_display = 'ไม่พร้อมใช้งาน';
            } elseif ($row['condition_of_use'] === 'Working' and $row['status_of_use'] === 'Free') {
              $status_display = 'ว่าง';
            } else {
              $status_display = 'ว่าง';
            }

            if ($row['condition_of_use'] === 'Broken') {
              $condition_display = 'ชำรุด';
            } elseif ($row['condition_of_use'] === 'Damaged') {
              $condition_display = 'เสียหาย';
            } elseif ($row['condition_of_use'] === 'Working') {
              $condition_display = 'ใช้งานได้';
            } elseif ($row['condition_of_use'] === 'Sold') {
              $condition_display = 'จำหน่ายแล้ว';
            }

            if ($row['note'] === null) {
              $note_display = '-';
            } else {
              $note_display = $row['note'];
            }

            $modalId = "modal_" . $row['durable_articles_id']; // สร้าง ID เฉพาะของ modal
            $borrowModalId = "borrowModal_" . $row['durable_articles_id']; // สร้าง ID เฉพาะของ modal
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
            echo "<td>" . $condition_display . "</td>";
            echo "<td>" . $note_display . "</td>";

            if ($row['status_of_use'] === 'Borrowed') {
              echo "<td style='color: red; cursor: pointer; text-decoration: underline;' onclick=\"document.getElementById('$modalId').style.display='block'\">" . $status_display . "</td>";
            } elseif (in_array($row['condition_of_use'], ['Broken', 'Damaged', 'Sold'])) {
              echo "<td style='color: #aaa; cursor: default;'>" . $status_display . "</td>";
            } elseif ($row['condition_of_use'] === 'Working' and $row['status_of_use'] === 'Free') {
              echo "<td style='color: green; cursor: pointer; text-decoration: underline;' onclick=\"document.getElementById('$borrowModalId').style.display='block'\">" . $status_display . "</td>";
            } else {
              echo "<td style='color: green; cursor: pointer; text-decoration: underline;' onclick=\"document.getElementById('$borrowModalId').style.display='block'\">" . $status_display . "</td>";
            }

            echo "</tr>";

            if ($row['status_of_use'] === 'Borrowed') {
              renderBorrowModal($modalId, $borrower, $Date, $row['borrowing_id'], $row['member_id']);
            }
            
            if ($status_display === 'ว่าง') {
              renderBorrowFormModal($borrowModalId, $row['durable_articles_id']);
            }
          }
          ?>
        </tbody>
      </table>
    </div>

  </div>
</body>

</html>
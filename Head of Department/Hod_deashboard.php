<?php
include dirname(__FILE__) . '/../connet/connect.php';

// ตั้งค่าการเข้ารหัสข้อมูลรองรับภาษาไทย
$conn->set_charset("utf8");

// ดึงข้อมูลสถานะครุภัณฑ์
$sql = "SELECT condition_of_use, COUNT(*) as count FROM tb_durable_articles GROUP BY condition_of_use";
$result = $conn->query($sql);

// ตั้งค่าข้อมูลเริ่มต้น
$data = ["Working" => 0, "Broken" => 0, "Damaged" => 0, "Sold" => 0];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[$row["condition_of_use"]] = $row["count"];
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/style_mem.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Prompt&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            width: 80%;
            max-width: 800px;
            margin: auto;
        }
    </style>
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
            <button class="logout">Logout</button>
        </ul>
    </div>
        <h2>แดชบอร์ดครุภัณฑ์</h2>
        <div class="chart-container">
            <canvas id="barChart"></canvas>
        </div>
        <div class="chart-container">
            <canvas id="pieChart"></canvas>
        </div>
        <script>
            const chartData = <?php echo json_encode(array_values($data)); ?>;
        </script>
        <script src="../js/dashboardCharts.js"></script>
    </div>
</body>
</html>
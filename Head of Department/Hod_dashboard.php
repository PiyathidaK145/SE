<?php
include dirname(__FILE__) . '/../connet/connect.php';

// ตั้งค่าการเข้ารหัส
$conn->set_charset("utf8");

// กำหนดปีที่ต้องการดึงข้อมูล
$year = $_GET['year'] ?? date('Y');

// ดึงปีทั้งหมดที่มีในฐานข้อมูล
$years = [];
$yearQuery = "SELECT DISTINCT year_of_purchase as year FROM tb_durable_articles ORDER BY year DESC";
$yearResult = $conn->query($yearQuery);
while ($row = $yearResult->fetch_assoc()) {
    $years[] = $row['year'];
}
$selectedYear = $_GET['year'] ?? ($years[0] ?? date('Y')); 

// ดึง `condition_of_use` ที่มีทั้งหมด
$conditions = ["Working" => 0, "Broken" => 0, "Damaged" => 0, "Sold" => 0];

// ดึงข้อมูลตามปีที่เลือก
$sql = "SELECT condition_of_use, COUNT(*) as count FROM tb_durable_articles WHERE year_of_purchase = ? GROUP BY condition_of_use";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $selectedYear);
$stmt->execute();
$result = $stmt->get_result();

// อัปเดตข้อมูล
$conditions = ["Working" => 0, "Broken" => 0, "Damaged" => 0, "Sold" => 0];
while ($row = $result->fetch_assoc()) {
    $conditions[$row["condition_of_use"]] = (int) $row["count"];
}

$sql = "SELECT condition_of_use, COUNT(*) as count FROM tb_durable_articles GROUP BY condition_of_use";
$result = $conn->query($sql);

$data = ["Working" => 0, "Broken" => 0, "Damaged" => 0, "Sold" => 0];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $key = $row["condition_of_use"];
        if (array_key_exists($key, $data)) {
            $data[$key] = (int) $row["count"];
        }
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
        /* Dashboard Main Section */
        h2 {
            margin-top: 60px;
            font-size: 24px;
            text-align: center;
        }

        /* Data Summary */
        .data-summary {
            display: flex;
            justify-content: space-around;
            margin-top: 30px;
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }

        .data-card {
            width: 23%;
            padding: 15px;
            background-color: #ecf0f1;
            border-radius: 5px;
            text-align: center;
            flex: 1;
            padding: 20px;
            border-radius: 12px;
            color: white;
        }

        .data-card h3 {
            font-size: 20px;
            color: #2c3e50;
        }

        .data-card p {
            font-size: 16px;
            color: rgb(0, 0, 0);
        }

        /* Charts */
        .charts-container {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .chart-box {
            width: 100%;
            max-width: 600px;
            height: 400px;
            margin: auto;
        }
    </style>
</head>

<body>
    <aside class="sidebar">
        <div class="logo"><img src="../image/logo.jpg" alt="Company Logo" style="width: 200px;"></div>
        <div class="profile">
            <img src="https://i.pravatar.cc/50?img=3" alt="Profile Picture" title="David Grey. H" />
            <div><h4>David Grey. H</h4><span>Project Manager</span></div>
        </div>
        <ul class="menu">
            <button class="logout">Logout</button>
            <li class="active">Dashboard</li>
            <li >ดูตำแหน่งครุภัณฑ์</li>
        </ul>
    </aside>

    <div class="main">
    <section class="data-summary">
            <div class="data-card working">
                <h3>ใช้งานได้</h3>
                <p><?php echo $data["Working"]; ?> รายการ</p>
            </div>
            <div class="data-card broken">
                <h3>ชำรุด</h3>
                <p><?php echo $data["Broken"]; ?> รายการ</p>
            </div>
            <div class="data-card damaged">
                <h3>เสียหาย</h3>
                <p><?php echo $data["Damaged"]; ?> รายการ</p>
            </div>
            <div class="data-card sold">
                <h3>จำหน่ายแล้ว</h3>
                <p><?php echo $data["Sold"]; ?> รายการ</p>
            </div>
        </section>

        <form>
            <label for="year">เลือกปี:</label>
            <select name="year" id="year">
                <?php foreach ($years as $year) { ?>
                    <option value="<?= $year ?>" <?= ($year == $selectedYear) ? 'selected' : '' ?>><?= $year ?></option>
                <?php } ?>
            </select>
        </form>

        <section class="charts-container">
            <div class="chart-box"><canvas id="barChart"></canvas></div>
            <div class="chart-box"><canvas id="pieChart"></canvas></div>
        </section>
    </div>

    <script>
        document.getElementById("year").addEventListener("change", function () {
    window.location.href = "?year=" + this.value;
});

const chartData = <?php echo json_encode(array_values($conditions)); ?>;
const labels = ["ใช้งานได้", "ชำรุด", "เสียหาย", "จำหน่ายแล้ว"];
const colors = ['#4caf50', '#f44336', '#ff9800', '#2196f3'];

// Bar Chart
const ctxBar = document.getElementById('barChart').getContext('2d');
new Chart(ctxBar, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            data: chartData,
            backgroundColor: colors,
            borderColor: colors,
            borderWidth: 1,
            borderRadius: 5, // ทำให้แท่งมีมุมโค้ง
            barThickness: 40  // ปรับขนาดแท่งให้พอดี
        }]
    },
    options: {
        responsive: true,
        scales: { 
            y: { beginAtZero: true } 
        },
        plugins: {
            legend: { display: false } // ไม่แสดง label ใน bar chart
        }
    }
});

// Pie Chart
const ctxPie = document.getElementById('pieChart').getContext('2d');
new Chart(ctxPie, {
    type: 'pie',
    data: {
        labels: labels,
        datasets: [{
            data: chartData,
            backgroundColor: colors
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom', // เลื่อน labels ไปอยู่ด้านล่าง
                labels: {
                    usePointStyle: true, // ให้ labels เป็นจุดแทนสี่เหลี่ยม
                    pointStyle: 'circle' // ใช้จุดเป็นวงกลม
                }
            }
        }
    }
});

    </script>
</body>
</html>

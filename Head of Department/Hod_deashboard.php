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
            color:rgb(0, 0, 0);
        }

        /* Charts */
        .charts-container {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }

        .chart-box {
            width: 48%;
        }

        /* Make it responsive */
        @media screen and (max-width: 768px) {
            .data-summary {
                flex-direction: column;
            }

            .data-card {
                width: 100%;
                margin-bottom: 20px;
            }

            .charts-container {
                flex-direction: column;
            }

            .chart-box {
                width: 100%;
                margin-bottom: 20px;
            }
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo">
            <img src="../image/logo.jpg" alt="Company Logo" style="width: 200px;">
        </div>
        <div class="profile">
            <img src="https://i.pravatar.cc/50?img=3" alt="Profile Picture" title="David Grey. H" />
            <div>
                <h4>David Grey. H</h4>
                <span>Project Manager</span>
            </div>
        </div>
        <ul class="menu">
            <button class="logout">Logout</button>
        </ul>
    </aside>
    <div class="main">
        <!-- กล่องข้อมูลครุภัณฑ์ (เรียงเป็นแนวนอน) -->
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

        <!-- Charts (อยู่กลางจอและข้างกัน) -->
        <section class="charts-container">
            <div class="chart-box">
                <canvas id="barChart"></canvas>
            </div>
            <div class="chart-box">
                <canvas id="pieChart"></canvas>
            </div>
        </section>
    </div>

    <script>
        const chartData = <?php echo json_encode(array_values($data)); ?>;

        // กราฟ Bar
        const ctxBar = document.getElementById('barChart').getContext('2d');
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: ["ใช้งานได้", "ชำรุด", "เสียหาย", "จำหน่ายแล้ว"], // ใส่ชื่อสถานะครุภัณฑ์
                datasets: [{
                    label: 'จำนวนรายการ',
                    data: chartData, // ข้อมูลที่ได้รับจาก PHP
                    backgroundColor: ['#4caf50', '#f44336', '#ff9800', '#2196f3'], // สีพื้นหลัง
                    borderColor: ['#4caf50', '#f44336', '#ff9800', '#2196f3'], // สีขอบ
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // กราฟ Pie
        const ctxPie = document.getElementById('pieChart').getContext('2d');
        new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: ["ใช้งานได้", "ชำรุด", "เสียหาย", "จำหน่ายแล้ว"], // ใส่ชื่อสถานะครุภัณฑ์
                datasets: [{
                    label: 'จำนวนรายการ',
                    data: chartData, // ข้อมูลที่ได้รับจาก PHP
                    backgroundColor: ['#4caf50', '#f44336', '#ff9800', '#2196f3'], // สีพื้นหลัง
                }]
            },
            options: {
                responsive: true
            }
        });
    </script>
</body>

</html>

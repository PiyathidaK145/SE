<?php
session_start();
include dirname(__FILE__) . '/../connet/connect.php';
if(empty($_SESSION[WP . 'checklogin'])){
    $_SESSION['message']  = "ยังไม่ได้เข้าสู่ระบบ";
    header("Location: {$base_url}/login.php");
}

$member_id= $_SESSION[WP . 'member_id'];
$query = mysqli_query($conn, "SELECT * FROM tb_member WHERE member_id = '{$member_id}'") or die('query failed');
$user = mysqli_fetch_assoc($query);

$conn->set_charset("utf8");

// ดึงปีทั้งหมดจากฐานข้อมูล
$years = [];
$yearQuery = "SELECT DISTINCT year_of_purchase as year FROM tb_durable_articles ORDER BY year DESC";
$yearResult = $conn->query($yearQuery);
while ($row = $yearResult->fetch_assoc()) {
    $years[] = $row['year'];
}

// กำหนดค่าปีที่เลือก
$startYear = $_GET['start_year'] ?? ($years[count($years) - 1] ?? date('Y'));
$endYear = $_GET['end_year'] ?? ($years[0] ?? date('Y'));

// ดึงเงื่อนไขที่เลือก
$selectedCondition = $_GET['condition'] ?? "";

// คำนวณสรุปจำนวน
$conditions = ["Working" => 0, "Broken" => 0, "Damaged" => 0, "Sold" => 0];
$sql = "SELECT condition_of_use, COUNT(*) as count FROM tb_durable_articles WHERE year_of_purchase BETWEEN ? AND ? GROUP BY condition_of_use";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $startYear, $endYear);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    if (isset($conditions[$row["condition_of_use"]])) {
        $conditions[$row["condition_of_use"]] = (int) $row["count"];
    }
}

// ดึงข้อมูลแสดงในตาราง
$sql = "SELECT * FROM tb_durable_articles WHERE year_of_purchase BETWEEN ? AND ?";
if ($selectedCondition) {
    $sql .= " AND condition_of_use = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $startYear, $endYear, $selectedCondition);
} else {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $startYear, $endYear);
}
$stmt->execute();
$articleResult = $stmt->get_result();

$yearsDiff = $endYear - $startYear;

$conn->close();
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/style.css" />
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

        /* สไตล์ปุ่ม Dashboard */
        .dashboard {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
            gap: 20px;
        }

        .dashboard-button {
            flex: 1;
            padding: 20px;
            border-radius: 12px;
            color: white;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            cursor: pointer;
            transition: 0.3s;
        }

        .dashboard-button:hover {
            filter: brightness(1.1);
        }

        .working {
            background-color: #28a745;
        }

        /* สีเขียว */
        .broken {
            background-color: #ffc107;
        }

        /* สีเหลือง */
        .damaged {
            background-color: #dc3545;
        }

        /* สีแดง */
        .sold {
            background-color: #17a2b8;
        }

        /* สีฟ้า */

        /* ตาราง */
        .table-container {
            width: 100%;
            margin-top: 20px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border-radius: 10px;
        }

        th,
        td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        thead {
            background-color: #ea9227;
            color: white;
        }

        tbody tr:hover {
            background-color: #fdf1e7;
        }

        .sortable {
            cursor: pointer;
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
        /* ปรับแต่งฟอร์ม */
    form {
        font-size: 18px;
        font-family: Arial, sans-serif;
        margin-bottom: 15px;
    }

    /* ปรับแต่ง dropdown */
    select {
        font-size: 16px;
        padding: 8px;
        border-radius: 10px; /* ทำให้มุมมน */
        border: 1px solid #ccc;
        background-color: #fff;
    }

    /* ปรับแต่งปุ่มตกลง */
    button {
        font-size: 16px;
        padding: 10px 20px;
        border-radius: 25px; /* ทำให้เป็นวงรี */
        border: none;
        background-color: #007bff;
        color: white;
        cursor: pointer;
        transition: 0.3s;
    }

    /* เปลี่ยนสีเมื่อ hover ปุ่ม */
    button:hover {
        background-color: #0056b3;
    }

    /* ปรับแต่งข้อความย้อนหลัง */
    p {
        font-size: 20px;
        font-weight: bold;
        color: #333;
    }
        


    </style>
</head>

<body>
    <aside class="sidebar">
        <div class="logo"><img src="../image/logo.jpg" alt="Company Logo" style="width: 200px;"></div>
        <div class="profile">
            <div>
                <h4><?php echo $user['first_name']; ?> <?php echo $user['last_name']; ?></h4><span>หัวหน้าภาควิชา</span>
            </div>
        </div>
        <ul class="menu">
            <li class="active" onclick="window.location.href='Hod_dashboard.php'">Dashboard</li>
            <li onclick="window.location.href='View_DA.php'">ดูตำแหน่งครุภัณฑ์</li> <br>
            <a href="<?php echo $base_url . '/logout.php'; ?>">Logout</a>
        </ul>
    </aside>

    <div class="main">
        <!-- ปุ่ม Dashboard -->
        <section class="dashboard">
            <div class="dashboard-button working" onclick="filterData('Working')">
                ใช้งานได้ <br> <?= $conditions["Working"] ?> รายการ
            </div>
            <div class="dashboard-button broken" onclick="filterData('Broken')">
                ชำรุด <br> <?= $conditions["Broken"] ?> รายการ
            </div>
            <div class="dashboard-button damaged" onclick="filterData('Damaged')">
                เสียหาย <br> <?= $conditions["Damaged"] ?> รายการ
            </div>
            <div class="dashboard-button sold" onclick="filterData('Sold')">
                จำหน่ายแล้ว <br> <?= $conditions["Sold"] ?> รายการ
            </div>
        </section>

        <form>
            <label for="start_year">เลือกปีเริ่มต้น:</label>
            <select name="start_year" id="start_year">
                <?php foreach ($years as $year) { ?>
                    <option value="<?= $year ?>" <?= ($year == $startYear) ? 'selected' : '' ?>><?= $year ?></option>
                <?php } ?>
            </select>
            <label for="end_year">เลือกปีสิ้นสุด:</label>
            <select name="end_year" id="end_year">
                <?php foreach ($years as $year) { ?>
                    <option value="<?= $year ?>" <?= ($year == $endYear) ? 'selected' : '' ?>><?= $year ?></option>
                <?php } ?>
            </select>
            <button type="submit">ตกลง</button>
        </form>
        <p>ย้อนหลัง: <?= abs($yearsDiff) ?> ปี</p>

        <section class="charts-container">
            <div class="chart-box"><canvas id="barChart"></canvas></div>
        </section>

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
                        <th class="sortable">ปีที่ซื้อ <i class="fa fa-sort"></i></th>
                        <th>สภาพการใช้งาน</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $count = 1;
                    while ($row = $articleResult->fetch_assoc()) { ?>
                        <tr>
                            <td><?= $count++ ?></td>
                            <td><?= $row['name'] ?></td>
                            <td><?= $row['brand'] ?></td>
                            <td><?= $row['series'] ?></td>
                            <td><?= $row['durable_articles_number'] ?></td>
                            <td><?= $row['serial_number'] ?></td>
                            <td><?= $row['year_of_purchase'] ?></td>
                            <td><?= $row['condition_of_use'] ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <script>
            function filterData(condition) {
            const startYear = document.getElementById("start_year").value;
            const endYear = document.getElementById("end_year").value;
            window.location.href = `?start_year=${startYear}&end_year=${endYear}&condition=${condition}`;
        }
            function updateURL() {
                const startYear = document.getElementById("start_year").value;
                const endYear = document.getElementById("end_year").value;
                window.location.href = `?start_year=${startYear}&end_year=${endYear}`;
            }

            document.addEventListener("DOMContentLoaded", function() {
                document.querySelectorAll("th.sortable").forEach(header => {
                    header.addEventListener("click", function() {
                        const table = document.getElementById("durableArticlesTable");
                        const tbody = table.querySelector("tbody");
                        const headers = Array.from(header.parentElement.children);
                        const columnIndex = headers.indexOf(header);
                        sortTable(tbody, columnIndex);
                    });
                });
            });

            function sortTable(tbody, columnIndex) {
                const rows = Array.from(tbody.querySelectorAll("tr"));

                // ตรวจสอบสถานะการเรียงลำดับ
                const isAscending = tbody.getAttribute("data-sort") === columnIndex.toString();
                tbody.setAttribute("data-sort", isAscending ? "" : columnIndex.toString());

                rows.sort((rowA, rowB) => {
                    const cellA = rowA.cells[columnIndex].textContent.trim();
                    const cellB = rowB.cells[columnIndex].textContent.trim();

                    // ตรวจสอบหากเป็นตัวเลข
                    const a = isNaN(cellA) ? cellA : parseInt(cellA);
                    const b = isNaN(cellB) ? cellB : parseInt(cellB);

                    return isAscending ? b - a : a - b;
                });

                // ล้างและเพิ่ม `<tr>` กลับเข้าไปใน `<tbody>` ใหม่
                tbody.innerHTML = "";
                rows.forEach(row => tbody.appendChild(row));
            }

            const chartData = <?php echo json_encode(array_values($conditions)); ?>;
            const labels = ["ใช้งานได้", "ชำรุด", "เสียหาย", "จำหน่ายแล้ว"];
            const colors = ['#28a745', '#ffc107', '#dc3545', '#17a2b8'];

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
                        barThickness: 40 // ปรับขนาดแท่งให้พอดี
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        </script>
</body>

</html>
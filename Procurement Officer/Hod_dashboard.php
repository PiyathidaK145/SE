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

$conn->set_charset("utf8");

// เช็คการเชื่อมต่อฐานข้อมูล
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ดึงปีทั้งหมดจากฐานข้อมูล
$years = [];
$yearQuery = "SELECT DISTINCT year_of_purchase as year FROM tb_durable_articles ORDER BY year DESC";
$yearResult = $conn->query($yearQuery);
if (!$yearResult) {
    die("Error fetching years: " . $conn->error);
}
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
if (!$result) {
    die("Error fetching conditions: " . $stmt->error);
}
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
if (!$articleResult) {
    die("Error fetching articles: " . $stmt->error);
}

$yearsDiff = $endYear - $startYear;

$conn->close();
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/style_dash.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Prompt&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../js/dashboardCharts.js"></script>
</head>

<body>
    <aside class="sidebar">
        <div class="logo"><img src="../image/logo.jpg" alt="Company Logo" style="width: 200px;"></div>
        <div class="profile">
            <div>
                <h4><?php echo $user['first_name']; ?> <?php echo $user['last_name']; ?></h4><span>นักวิชาการพัสดุ</span>
                <a class="logout" href="<?php echo $base_url . '/logout.php'; ?>">Logout</a>
            </div>
        </div>
        <ul class="menu">
            <li class="active"onclick="window.location.href='Hod_dashboard.php'">Dashboard</li>
            <li onclick="window.location.href='asset-table.php'">ดูตำแหน่งครุภัณฑ์</li>
            <li onclick="window.location.href='duration_details.php'">รายละเอียดครุภัณฑ์</li><br>
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
                        <th class="sortable" onclick="sortTable()">ปีที่ซื้อ <i class="fa fa-sort"></i></th>
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
        <!-- เพิ่ม div สำหรับส่งข้อมูล -->
        <div id="chartData" data-conditions='<?php echo json_encode(array_values($conditions)); ?>'></div>
</body>

</html>
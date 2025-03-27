<?php

include dirname(__FILE__) . '/../connet/connect.php';

if (isset($_POST['upload'])) {
    $file = $_FILES['file']['tmp_name'];
    if ($file) {
        $handle = fopen($file, "r");
        fgetcsv($handle); // Skip header row
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $sql = "INSERT INTO tb_durable_articles (name, brand, series, durable_articles_number, `serial number`, condition_of_use, price, year_of_purchase, annual_warranty, description, note) 
                    VALUES ('" . implode("','", array_map('mysqli_real_escape_string', array($conn, ...$data))) . "')";
            mysqli_query($conn, $sql);
        }
        fclose($handle);
    }
}

$sql = "SELECT * FROM tb_durable_articles";
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
        <h2>Upload CSV File</h2>
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="file" required />
            <button type="submit" name="upload">Upload</button>
        </form>

        <h2>Durable Articles List</h2>
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>รายการครุภัณฑ์</th>
                    <th>ยี่ห้อ</th>
                    <th>รุ่น</th>
                    <th>หมายเลขครุภัณฑ์</th>
                    <th>หมายเลขเครื่อง</th>
                    <th>สภาพการใช้งาน</th>
                    <th>ราคาต่อหน่วย</th>
                    <th>ปีที่ซื้อ</th>
                    <th>รับประกัน</th>
                    <th>เพิ่มเติม</th>
                    <th>หมายเหตุ</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?= $row['durable_articles_id'] ?></td>
                        <td><?= $row['name'] ?></td>
                        <td><?= $row['brand'] ?></td>
                        <td><?= $row['series'] ?></td>
                        <td><?= $row['durable_articles_number'] ?></td>
                        <td><?= $row['serial number'] ?></td>
                        <td><?= $row['condition_of_use'] ?></td>
                        <td><?= $row['price'] ?></td>
                        <td><?= $row['year_of_purchase'] ?></td>
                        <td><?= $row['annual_warranty'] ?></td>
                        <td><?= $row['description'] ?></td>
                        <td><?= $row['note'] ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
</body>

</html>
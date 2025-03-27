<?php
include dirname(__FILE__) . '/../connet/connect.php';

if (isset($_POST['upload'])) {
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($fileTmpPath, 'r');

        if ($handle !== FALSE) {
            fgetcsv($handle); // ข้าม Header แถวแรก
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $name = mysqli_real_escape_string($conn, $data[0]);
                $brand = mysqli_real_escape_string($conn, $data[1]);
                $series = mysqli_real_escape_string($conn, $data[2]);
                $durable_articles_number = mysqli_real_escape_string($conn, $data[3]);
                $serial_number = mysqli_real_escape_string($conn, $data[4]);
                $condition_of_use = mysqli_real_escape_string($conn, $data[5]);
                $price = mysqli_real_escape_string($conn, $data[6]);
                $year_of_purchase = mysqli_real_escape_string($conn, $data[7]);
                $annual_warranty = mysqli_real_escape_string($conn, $data[8]);
                $description = mysqli_real_escape_string($conn, $data[9]);
                $note = mysqli_real_escape_string($conn, $data[10]);

                $sql = "INSERT INTO tb_durable_articles 
                        (name, brand, series, durable_articles_number, `serial number`, condition_of_use, price, year_of_purchase, annual_warranty, description, note) 
                        VALUES 
                        ('$name', '$brand', '$series', '$durable_articles_number', '$serial_number', '$condition_of_use', '$price', '$year_of_purchase', '$annual_warranty', '$description', '$note')";
                mysqli_query($conn, $sql);
            }
            fclose($handle);
        }
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
    <div class="container">
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
    </div>

    <div class="main">
        <form action="" method="post" enctype="multipart/form-data">
            <input type="file" name="csv_file" required>
            <button type="submit" name="upload">Upload CSV</button>
        </form>

        <div class="table-container">
            <table>
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
                    <?php 
                    $count = 1;
                    while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo $count++; ?></td>
                            <td><?php echo $row['durable_articles_id']; ?></td>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['brand']; ?></td>
                            <td><?php echo $row['series']; ?></td>
                            <td><?php echo $row['durable_articles_number']; ?></td>
                            <td><?php echo $row['serial number']; ?></td>
                            <td><?php echo $row['condition_of_use']; ?></td>
                            <td><?php echo $row['price']; ?></td>
                            <td><?php echo $row['year_of_purchase']; ?></td>
                            <td><?php echo $row['annual_warranty']; ?></td>
                            <td><?php echo $row['description']; ?></td>
                            <td><?php echo $row['note']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

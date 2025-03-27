<?php
session_start();
include 'connect.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

</head>

<body>
    <div class="container">
        <?php if (!empty($_SESSION['message'])) : ?>
            <div class="alert alert-danger alert-dismissible fade show my-5" role="alert">
                <?php echo $_SESSION['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['message']) ?>
        <?php endif; ?>
        <div class="row d-flex justify-content-center align-items-center" style="height: 75vh;">
            <div class="col-sm-5">
                <h1 class="text-center">Register</h1>
                <form action="<?php echo $base_url . '/register-form.php'; ?>" method="post" class="p-3 border rounded p-5">
                    <div class="mb-3">
                        <label class="form-label" require>Username</label>
                        <input type="text" name="username" class="form-control" require>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Academic Ranks</label>
                        <select class="form-select" name="academicRanks" require>
                            <option selected>เลือกตำแหน่งทางวิชาการ</option>
                            <option value="อ.">อ.</option>
                            <option value="ดร.">ดร.</option>
                            <option value="รศ.">รศ.</option>
                            <option value="ผศ.">ผศ.</option>
                            <option value="">ไม่มี</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">First Name</label>
                        <input type="text" name="firstName" class="form-control" require>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="lastName" class="form-control" require>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gender</label>
                        <select class="form-select" name="gender" require>
                            <option selected>เลือกเพศ</option>
                            <option value="หญิง">หญิง</option>
                            <option value="ชาย">ชาย</option>
                            <option value="LGBTQ+">LGBTQ+</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="dateOfBirth" class="form-control" require>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Position</label>
                        <select class="form-select" name="position" require>
                            <option selected>เลือกตำแหน่ง</option>
                            <option value="1101">หัวหน้าภาค</option>
                            <option value="1102">เจ้าหน้าที่พัสดุ</option>
                            <option value="1103">รองหัวหน้าภาควิชา</option>
                            <option value="1104">ผู้ช่วยหัวหน้าภาควิชา</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="tel" class="form-control" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" require>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" require>
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-center">
                        <button type="submit" class="btn btn-success ">ลงทะเบียน</button>
                        <a href="<?php echo $base_url . '/login.php'; ?>" class="btn btn-secondary">ยกเลิก</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Purple Dashboard</title>
  <link rel="stylesheet" href="style_mem.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
</head>
<body>
  <div class="sidebar">
    <h2 class="logo">Purple</h2>
    <div class="profile">
      <img src="https://i.pravatar.cc/50?img=3" alt="Profile" />
      <div>
        <h4>David Grey. H</h4>
        <span>Project Manager</span>
      </div>
    </div>
    <ul class="menu">
      <li class="active">Dashboard</li>
      <li>Page Layouts</li>
    </ul>
  </div>

  <div class="main">

    <div class="topbar">
      <select name="location" id="" style="width: 250px; height: 40px">
        <option value="#" disabled selected>ตำแหน่งปัจจุบัน</option>
        <option value="">ทั้งหมด</option>
      </select>
      <select name="bought-year" id="" style="width: 250px; height: 40px">
        <option value="#">ปีที่ซื้อ</option>
      </select>
      <select name="used-status" id="" style="width: 250px; height: 40px">
        <option value="#">สถานะการใช้งานครุภัณฑ์</option>
      </select>
      <input type="text" placeholder="Search projects"/>
      <div class="top-icons">
        <i class="fas fa-power-off"></i>
      </div>
    </div>

    <div class="table">
      <table>
        <thead>
          <tr>
            <th>ลำดับที่</th>
            <th>ชื่อ</th>
            <th>ยี่ห้อ</th>
            <th>รุ่น</th>
            <th>หมายเลขครุภัณฑ์</th>
            <th>หมายเลขเครื่อง</th>
            <th>ตำแหน่งปัจจุบัน</th>
            <th>ปีที่ซื้อ</th>
            <th>สถานะการใช้งาน</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1</td>
            <td>คอมพิวเตอร์</td>
            <td>Asus</td>
            <td>Pro Tower 280 G9</td>
            <td>001</td>
            <td>cd-123</td>
            <td>SC2-412</td>
            <td>2567</td>
            <td style="color: blue; cursor: pointer;" onclick="document.getElementById('myModal').style.display='block'">ถูกยืม</td>
            
            <div id="myModal" style="display:none; position:fixed; z-index:1; left:0; top:0; width:100%; height:100%; background-color:rgba(0,0,0,0.5)">
              <div style="background-color:white; margin:15% auto; padding:20px; width:300px; border-radius:10px;">
                <span style="float:right; cursor:pointer;" onclick="document.getElementById('myModal').style.display='none'">&times;</span>
                <h3>รายละเอียดการยืม</h3>
                <p>ผู้ยืม: นายสมชาย</p>
                <p>วันที่ยืม: 20 มี.ค. 2565</p>
              </div>
            </div>
          </tr>
        </tbody>
      </table>
    </div>

  </div>
</body>
</html>
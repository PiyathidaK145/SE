<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>ดูตำแหน่งครุภัณฑ์</title>
  <link rel="stylesheet" href="asset-table.css">
</head>
<body>
  <div class="sidebar">
    <h2 class="logo">Menu</h2>
    <div class="profile">
      <img src="https://i.pravatar.cc/50?img=3" alt="Profile" />
      <div>
        <h4>David Grey. H</h4>
        <span>Project Manager</span>
      </div>
    </div>
    <ul class="menu">
      <li>หน้าหลัก</li>
      <li class="active">ดูตำแหน่งครุภัณฑ์</li>
      <li>รายละเอียดครุภัณฑ์</li>
    </ul>
  </div>

  <div class="content">
    <div class="filter-bar">
      <input list="categoryList" id="categoryInput" placeholder="หมวดหมู่">
      <datalist id="categoryList">
        <option value="คอมพิวเตอร์">
        <option value="เครื่องใช้สำนักงาน">
        <option value="เฟอร์นิเจอร์">
      </datalist>

      <input list="yearList" id="yearInput" placeholder="ปีที่ซื้อ">
      <datalist id="yearList">
        <option value="2565"><option value="2566"><option value="2567">
      </datalist>

      <input list="statusList" id="statusInput" placeholder="สถานะการใช้งานครุภัณฑ์">
      <datalist id="statusList">
        <option value="ว่าง">
        <option value="ไม่พร้อมใช้งาน">
        <option value="ถูกยืม">
      </datalist>

      <input type="text" id="searchInput" placeholder="ค้นหาครุภัณฑ์...">
      <button onclick="filterTable()">🔍</button>
    </div>

    <h2>📋 รายการครุภัณฑ์</h2>
    <table>
      <thead>
        <tr>
          <th>ที่</th>
          <th>ชื่อ</th>
          <th>ยี่ห้อ</th>
          <th>รุ่น</th>
          <th>หมายเลขครุภัณฑ์</th>
          <th>หมายเลขเครื่อง</th>
          <th>ตำแหน่งใช้งาน</th>
          <th>สภาพ</th>
          <th>สถานะ</th>
          <th>ปีที่ซื้อ</th>
          <th>หมายเหตุ</th>
          <th>จัดการ</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $sql = "SELECT * FROM tb_durable_articles";
        $result = $conn->query($sql);
        $i = 1;
        while ($row = $result->fetch_assoc()) {
          $statusClass = "";
          if ($row['status'] == 'ถูกยืม') $statusClass = "status-borrowed";
          elseif ($row['status'] == 'ไม่พร้อมใช้งาน') $statusClass = "status-unavailable";

          echo "<tr>
            <td>{$i}</td>
            <td>{$row['name']}</td>
            <td>{$row['brand']}</td>
            <td>{$row['series']}</td>
            <td>{$row['durable_articles_number']}</td>
            <td>{$row['serial number']}</td>
            <td>{$row['description']}</td>
            <td>{$row['condition_of_use']}</td>
            <td class='{$statusClass}'>{$row['status']}</td>
            <td>{$row['year_of_purchase']}</td>
            <td>{$row['note']}</td>
            <td class='action-box'>
              <a class='edit' href='edit-asset.php?id={$row['durable_articles_id']}'>แก้ไข</a>
              <a class='delete' href='delete-asset.php?id={$row['durable_articles_id']}' onclick='return confirm(\"ลบรายการนี้ใช่ไหม?\")'>ลบ</a>
            </td>
          </tr>";
          $i++;
        }
        ?>
      </tbody>
    </table>
  </div>

  <script>
    function filterTable() {
      const category = document.getElementById('categoryInput').value.toLowerCase();
      const status = document.getElementById('statusInput').value.toLowerCase();
      const year = document.getElementById('yearInput').value.toLowerCase();
      const keyword = document.getElementById('searchInput').value.toLowerCase();

      const rows = document.querySelectorAll("tbody tr");

      rows.forEach(row => {
        const cells = row.querySelectorAll("td");
        const name = cells[1].textContent.toLowerCase();
        const brand = cells[2].textContent.toLowerCase();
        const statusVal = cells[8].textContent.toLowerCase();
        const yearVal = cells[9].textContent.toLowerCase();

        const matchCategory = category === "" || brand.includes(category);
        const matchStatus = status === "" || statusVal.includes(status);
        const matchYear = year === "" || yearVal.includes(year);
        const matchKeyword = keyword === "" || name.includes(keyword);

        row.style.display = (matchCategory && matchStatus && matchYear && matchKeyword) ? "" : "none";
      });
    }
  </script>
</body>
</html>

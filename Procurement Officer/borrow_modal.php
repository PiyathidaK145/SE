<?php
function renderBorrowModal($modalId, $borrower, $date, $borrowing_id, $member_id)
{
    echo "
    <div id='$modalId' style='display:none; position:fixed; z-index:1; left:0; top:0; width:100%; height:100%; background-color:rgba(0,0,0,0.5)'>
      <div style='background-color:white; margin:15% auto; padding:20px; width:350px; border-radius:10px;'>
        <span style='float:right; cursor:pointer; font-size:20px;' onclick=\"document.getElementById('$modalId').style.display='none'\">&times;</span>
        <h3>รายละเอียดการยืม</h3>
        <p>👨‍🦱 ผู้ยืม : $borrower</p>
        <p>⏰ วัน/เวลาที่ยืม : $date</p>

        <div style='text-align: right; margin-top: 15px;'>
          <form action='cancel_borrow.php' method='post' style='display: inline-block;'>
            <input type='hidden' name='borrowing_id' value='$borrowing_id'>
            <input type='hidden' name='member_id' value='$member_id'>
            <button type='submit' style='background-color: red; color: white; padding: 8px 12px; border: none; border-radius: 5px; cursor: pointer;'>ยกเลิกการยืม</button>
          </form>
        </div>
      </div>
    </div>
    ";
}

function renderBorrowFormModal($borrowModalId, $durable_articles_id)
{
    include dirname(__FILE__) . '/../connet/connect.php';

    $roomOptions = "";
    $query = "SELECT room_id, number FROM tb_room ORDER BY number ASC";
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $room_id = $row['room_id'];
        $room_number = $row['number'];
        $roomOptions .= "<option value='$room_id'>$room_number</option>";
    }

    echo "
    <div id='$borrowModalId' style='display:none; position:fixed; z-index:1; left:0; top:0; width:100%; height:100%; background-color:rgba(0,0,0,0.5)'>
      <div style='background-color:white; margin:15% auto; padding:20px; width:350px; border-radius:10px;'>
        <span style='float:right; cursor:pointer; font-size:20px;' onclick=\"document.getElementById('$borrowModalId').style.display='none'\">&times;</span>
        <h3>ฟอร์มการยืมครุภัณฑ์</h3>

        <form action='submit_borrow.php' method='post' style='margin-top: 15px;'>
          <input type='hidden' name='durable_articles_id' value='$durable_articles_id'>

          <label for='member_id'>👨‍🦱 ผู้ยืม (รหัสสมาชิก):</label><br>
          <input type='text' name='member_id' id='member_id' required style='width: 100%;padding: 6px;margin-bottom: 10px;/* border-radius: 5px; */border-radius: 10px;border: 1px solid #ccc;'><br>

          <label for='room_id'>🏠 ห้องที่จะนำไปใช้:</label><br>
          <select name='room_id' id='room_id' required style='width: 100%;padding: 6px;margin-bottom: 10px;/* border-radius: 5px; */border-radius: 10px;border: 1px solid #ccc;'>
            <option value='' disabled selected>-- กรุณาเลือกห้อง --</option>
            $roomOptions
          </select><br>

          <div style='text-align: right;'>
            <button type='submit' style='background-color: green; color: white; padding: 8px 12px; border: none; border-radius: 5px; cursor: pointer;'>ยืนยันการยืม</button>
          </div>
        </form>
      </div>
    </div>
    ";
}
?>



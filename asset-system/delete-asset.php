<?php
include 'db.php';

$id = $_GET['id'] ?? 0;

if ($id) {
  $conn->query("DELETE FROM tb_durable_articles WHERE durable_articles_id = $id");
  echo "<script>alert('ลบรายการเรียบร้อย'); window.location='asset-table.php';</script>";
} else {
  echo "<script>alert('ไม่พบ ID ที่ต้องการลบ'); window.location='asset-table.php';</script>";
}

// ตรวจสอบข้อมูลที่ได้จาก PHP
console.log(chartData);

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

document.addEventListener("DOMContentLoaded", function () {
    const labels = ['ใช้งานได้', 'ชำรุด', 'เสียหาย', 'จำหน่ายแล้ว'];
    const colors = ['#4CAF50', '#FFC107', '#F44336', '#00BCD4'];

    // Bar Chart
    new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'จำนวนครุภัณฑ์',
                data: chartData,
                backgroundColor: colors,
                borderColor: '#333',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            aspectRatio: 2,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Pie Chart
    new Chart(document.getElementById('pieChart'), {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: chartData,
                backgroundColor: colors,
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            aspectRatio: 2,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});

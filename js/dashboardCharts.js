function filterData(condition) {
    const startYear = document.getElementById("start_year").value;
    const endYear = document.getElementById("end_year").value;
    window.location.href = `?start_year=${startYear}&end_year=${endYear}&condition=${condition}`;
}

function updateURL() {
    const startYear = document.getElementById("start_year").value;
    const endYear = document.getElementById("end_year").value;
    window.location.href = `?start_year=${startYear}&end_year=${endYear}`;
}
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll("th.sortable").forEach(header => {
        header.addEventListener("click", function () {
            const table = document.getElementById("durableArticlesTable");
            const tbody = table.querySelector("tbody");
            const headers = Array.from(header.parentElement.children);
            const columnIndex = headers.indexOf(header);
            
            // อ่านค่าการเรียงจาก `data-order` (true = ASC, false = DESC)
            const isAscending = header.getAttribute("data-order") === "asc";
            
            // สลับค่าการเรียงลำดับ
            header.setAttribute("data-order", isAscending ? "desc" : "asc");

            sortTable(tbody, columnIndex, isAscending);
        });
    });
});

function sortTable(tbody, columnIndex, isAscending) {
    const rows = Array.from(tbody.querySelectorAll("tr"));

    rows.sort((rowA, rowB) => {
        const cellA = rowA.cells[columnIndex].textContent.trim();
        const cellB = rowB.cells[columnIndex].textContent.trim();

        // ตรวจสอบหากเป็นตัวเลข
        const a = isNaN(cellA) ? cellA.toLowerCase() : parseInt(cellA);
        const b = isNaN(cellB) ? cellB.toLowerCase() : parseInt(cellB);

        return isAscending ? a - b : b - a;
    });

    // ล้างและเพิ่ม `<tr>` กลับเข้าไปใน `<tbody>` ใหม่
    tbody.innerHTML = "";
    rows.forEach(row => tbody.appendChild(row));
}

document.addEventListener("DOMContentLoaded", function() {
    // ดึงข้อมูลจาก data-conditions ที่ใส่ไว้ใน div
    const chartData = JSON.parse(document.getElementById('chartData').getAttribute('data-conditions'));

    const labels = ["ใช้งานได้", "ชำรุด", "เสียหาย", "จำหน่ายแล้ว"];
    const colors = ['#28a745', '#ffc107', '#dc3545', '#17a2b8'];

    // Bar Chart
    const ctxBar = document.getElementById('barChart').getContext('2d');
    new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                data: chartData,
                backgroundColor: colors,
                borderColor: colors,
                borderWidth: 1,
                borderRadius: 5, // ทำให้แท่งมีมุมโค้ง
                barThickness: 40 // ปรับขนาดแท่งให้พอดี
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});


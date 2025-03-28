document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll("th.sortable").forEach(header => {
        header.addEventListener("click", function () {
            const table = document.getElementById("durableArticlesTable");
            const tbody = table.querySelector("tbody");
            const headers = Array.from(header.parentElement.children);
            const columnIndex = headers.indexOf(header);
            sortTable(tbody, columnIndex);
        });
    });
});

function sortTable(tbody, columnIndex) {
    const rows = Array.from(tbody.querySelectorAll("tr"));

    // ตรวจสอบสถานะการเรียงลำดับ
    const isAscending = tbody.getAttribute("data-sort") === columnIndex.toString();
    tbody.setAttribute("data-sort", isAscending ? "" : columnIndex.toString());

    rows.sort((rowA, rowB) => {
        const cellA = rowA.cells[columnIndex].textContent.trim();
        const cellB = rowB.cells[columnIndex].textContent.trim();

        // ตรวจสอบหากเป็นตัวเลข
        const a = isNaN(cellA) ? cellA : parseInt(cellA);
        const b = isNaN(cellB) ? cellB : parseInt(cellB);

        return isAscending ? b - a : a - b;
    });

    // ล้างและเพิ่ม `<tr>` กลับเข้าไปใน `<tbody>` ใหม่
    tbody.innerHTML = "";
    rows.forEach(row => tbody.appendChild(row));
}
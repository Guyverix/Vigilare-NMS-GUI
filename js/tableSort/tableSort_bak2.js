/*
  This has been enhanced to supposedly show event details as well
*/

function enableTableEnhancements(tableId, rowsPerPage = 10) {
  const table = document.getElementById(tableId);
  if (!table) return;

  const tbody = table.querySelector("tbody");
  const allRows = Array.from(tbody.querySelectorAll("tr:not(.detail-row)"));
  const pagination = document.getElementById("pagination-" + tableId);
  if (!pagination) return;

  let currentPage = 1;

  function paginate(page) {
    const start = (page - 1) * rowsPerPage;
    const end = start + rowsPerPage;

    let visibleCount = 0;
    allRows.forEach(row => {
      const index = visibleCount;
      const detailRow = document.getElementById("detail-" + row.dataset.index);
      if (index >= start && index < end) {
        row.style.display = "";
        if (detailRow && detailRow.style.display !== "none") {
          detailRow.style.display = ""; // Keep visible if expanded
        }
        visibleCount++;
      } else {
        row.style.display = "none";
        if (detailRow) detailRow.style.display = "none";
        visibleCount++;
      }
    });

    Array.from(pagination.children).forEach(li => li.classList.remove("active"));
    if (pagination.children[page - 1]) pagination.children[page - 1].classList.add("active");
    currentPage = page;
  }

  function buildPagination() {
    pagination.innerHTML = "";
    const pageCount = Math.ceil(allRows.length / rowsPerPage);
    for (let i = 1; i <= pageCount; i++) {
      const li = document.createElement("li");
      li.className = "page-item";
      li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
      li.addEventListener("click", e => {
        e.preventDefault();
        paginate(i);
      });
      pagination.appendChild(li);
    }
  }

  function sortTable(columnIndex, order) {
    const sorted = [...allRows].sort((a, b) => {
      const valA = a.cells[columnIndex]?.textContent.trim() ?? "";
      const valB = b.cells[columnIndex]?.textContent.trim() ?? "";

      const parsedA = Date.parse(valA) || valA.toLowerCase();
      const parsedB = Date.parse(valB) || valB.toLowerCase();

      if (parsedA < parsedB) return order === "asc" ? -1 : 1;
      if (parsedA > parsedB) return order === "asc" ? 1 : -1;
      return 0;
    });

    sorted.forEach(row => {
      const detailRow = document.getElementById("detail-" + row.dataset.index);
      tbody.appendChild(row);
      if (detailRow) tbody.appendChild(detailRow);
    });

    paginate(1);
  }

  // Add sorting functionality to headers
  table.querySelectorAll("th.sortable").forEach((th, index) => {
    th.style.cursor = "pointer";
    th.addEventListener("click", () => {
      const currentOrder = th.dataset.order || "desc";
      const newOrder = currentOrder === "asc" ? "desc" : "asc";
      th.dataset.order = newOrder;

      table.querySelectorAll("th.sortable").forEach(other => {
        if (other !== th) other.dataset.order = "";
      });

      sortTable(index, newOrder);
    });
  });

  buildPagination();
  paginate(1);

  // Handle expandable detail rows
  table.querySelectorAll(".clickable-row").forEach(row => {
    row.addEventListener("click", () => {
      const detail = document.getElementById("detail-" + row.dataset.index);
      if (detail) {
        const isHidden = detail.style.display === "none";
        detail.style.display = isHidden ? "" : "none";
      }
    });
  });
}

function enableTableEnhancements(tableId, rowsPerPage = 10) {
  const table = document.getElementById(tableId);
  const tbody = table.querySelector("tbody");
  const headers = table.querySelectorAll("th.sortable");
  const pagination = document.getElementById(`pagination-${tableId}`);
  let currentPage = 1;

  let allRows = Array.from(tbody.querySelectorAll("tr.clickable-row"));

  function sortTable(columnIndex, order) {
    allRows.sort((a, b) => {
      const aCell = a.children[columnIndex];
      const bCell = b.children[columnIndex];

      const aVal = aCell.getAttribute("data-sort") || aCell.textContent.trim();
      const bVal = bCell.getAttribute("data-sort") || bCell.textContent.trim();

      const aParsed = parseFloat(aVal) || aVal.toLowerCase();
      const bParsed = parseFloat(bVal) || bVal.toLowerCase();

      if (aParsed < bParsed) return order === "asc" ? -1 : 1;
      if (aParsed > bParsed) return order === "asc" ? 1 : -1;
      return 0;
    });

    currentPage = 1;
    updateTable();
  }

  function updateTable() {
    tbody.innerHTML = "";
    const start = (currentPage - 1) * rowsPerPage;
    const paginated = allRows.slice(start, start + rowsPerPage);
    paginated.forEach(row => tbody.appendChild(row));
    setupRowClicks();
    setupPagination();
  }

  function setupPagination() {
    if (!pagination) return;
    pagination.innerHTML = "";
    const pageCount = Math.ceil(allRows.length / rowsPerPage);
    for (let i = 1; i <= pageCount; i++) {
      const li = document.createElement("li");
      li.className = "page-item" + (i === currentPage ? " active" : "");
      const a = document.createElement("a");
      a.className = "page-link";
      a.textContent = i;
      a.addEventListener("click", () => {
        currentPage = i;
        updateTable();
      });
      li.appendChild(a);
      pagination.appendChild(li);
    }
  }

  function setupRowClicks() {
    const rows = table.querySelectorAll("tr.clickable-row");
    rows.forEach(row => {
      row.addEventListener("click", () => {
        const data = row.getAttribute("data-detail");
        if (!data) return;
        try {
          const details = JSON.parse(data);
          const contentDiv = document.getElementById("detailModalContent");
          contentDiv.innerHTML = "";

          const dl = document.createElement("dl");
          for (const [key, value] of Object.entries(details)) {
            const dt = document.createElement("dt");
            dt.textContent = key;
            const dd = document.createElement("dd");
            dd.textContent = value;
            dl.appendChild(dt);
            dl.appendChild(dd);
          }
          contentDiv.appendChild(dl);

          const modal = new bootstrap.Modal(document.getElementById("detailModal"));
          modal.show();
        } catch (e) {
          console.error("Failed to parse event details:", e);
        }
      });
    });
  }

  headers.forEach((header, columnIndex) => {
    header.style.cursor = "pointer";
    header.addEventListener("click", () => {
      const ascending = !header.classList.contains("asc");
      headers.forEach(h => h.classList.remove("asc", "desc"));
      header.classList.add(ascending ? "asc" : "desc");
      sortTable(columnIndex, ascending ? "asc" : "desc");
    });
  });

  updateTable();
}

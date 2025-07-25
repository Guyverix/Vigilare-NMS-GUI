function enableTableEnhancements(tableId, rowsPerPage = 10) {
  const table = document.getElementById(tableId);
  const tbody = table.querySelector("tbody");
  const headers = table.querySelectorAll("th.sortable");
  const pagination = document.getElementById(`pagination-${tableId}`);
  let currentPage = 1;
  let rows = Array.from(tbody.querySelectorAll("tr.clickable-row"));

  // Sorting
  headers.forEach((header, columnIndex) => {
    header.style.cursor = "pointer";
    header.addEventListener("click", () => {
      const ascending = !header.classList.contains("asc");
      headers.forEach(h => h.classList.remove("asc", "desc"));
      header.classList.add(ascending ? "asc" : "desc");
      rows.sort((a, b) => {
        const aText = a.children[columnIndex].textContent.trim();
        const bText = b.children[columnIndex].textContent.trim();
        return ascending
          ? aText.localeCompare(bText)
          : bText.localeCompare(aText);
      });
      updateTable();
    });
  });

  function updateTable() {
    tbody.innerHTML = "";
    const start = (currentPage - 1) * rowsPerPage;
    const paginated = rows.slice(start, start + rowsPerPage);
    paginated.forEach(row => tbody.appendChild(row));
    setupRowClicks();
    setupPagination();
  }

  function setupPagination() {
    if (!pagination) return;
    pagination.innerHTML = "";
    const pageCount = Math.ceil(rows.length / rowsPerPage);
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

      console.log("Raw detail data:", data);
      const details = JSON.parse(data);
      const contentDiv = document.getElementById("detailModalContent");
      contentDiv.innerHTML = "";

      const dl = document.createElement("dl");
      for (const [key, value] of Object.entries(details)) {
        const dt = document.createElement("dt");
        dt.textContent = key;

        const dd = document.createElement("dd");

        if (Array.isArray(value)) {
          // Pretty-print arrays as bullet lists
          const ul = document.createElement("ul");
          value.forEach(item => {
            const li = document.createElement("li");
            li.textContent = item;
            ul.appendChild(li);
          });
          dd.appendChild(ul);
        } else if (typeof value === "object" && value !== null) {
          // Pretty-print nested objects as key: value pairs
          const pre = document.createElement("pre");
          pre.textContent = JSON.stringify(value, null, 2);
          dd.appendChild(pre);
        } else {
          dd.textContent = value;
        }

        dl.appendChild(dt);
        dl.appendChild(dd);
      }

      contentDiv.appendChild(dl);

      const modal = new bootstrap.Modal(document.getElementById("detailModal"));
      modal.show();
    });
  });
}

  updateTable();
}

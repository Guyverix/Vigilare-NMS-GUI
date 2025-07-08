function enableTableEnhancements(tableId, rowsPerPage = 10) {
  const table = document.getElementById(tableId);
  const tbody = table.querySelector("tbody");
  const headers = table.querySelectorAll("th.sortable");
  const pagination = document.getElementById(`pagination-${tableId}`);
  let currentPage = 1;

  // Pair up each clickable-row with its detail-row
  let allRowPairs = [];
  const allRows = Array.from(tbody.querySelectorAll("tr.clickable-row"));

  allRows.forEach(row => {
    const idx = row.dataset.index;
    const detail = tbody.querySelector(`#detail-${idx}`);
    allRowPairs.push({ main: row, detail });
  });

  // Sorting logic
  headers.forEach((header, columnIndex) => {
    header.style.cursor = "pointer";
    header.addEventListener("click", () => {
      const ascending = !header.classList.contains("asc");
      headers.forEach(h => h.classList.remove("asc", "desc"));
      header.classList.add(ascending ? "asc" : "desc");

      allRowPairs.sort((a, b) => {
        const aText = a.main.children[columnIndex].textContent.trim();
        const bText = b.main.children[columnIndex].textContent.trim();
        return ascending
          ? aText.localeCompare(bText)
          : bText.localeCompare(aText);
      });

      updateTable();
    });
  });

  function setupRowClicks() {
    tbody.querySelectorAll(".clickable-row").forEach(row => {
      row.addEventListener("click", () => {
        const idx = row.dataset.index;
        const detailRow = tbody.querySelector(`#detail-${idx}`);
        if (detailRow) {
          const visible = detailRow.style.display !== "none";
          detailRow.style.display = visible ? "none" : "table-row";
        }
      });
    });
  }

  function updateTable() {
    tbody.innerHTML = "";
    const start = (currentPage - 1) * rowsPerPage;
    const end = start + rowsPerPage;

    allRowPairs.slice(start, end).forEach(pair => {
      tbody.appendChild(pair.main);
      if (pair.detail) {
        // pair.detail.style.display = "none";  // Start collapsed

  const col = pair.detail.querySelector("td");
  const raw = col?.textContent?.trim();

  try {
    const parsed = JSON.parse(raw);

    if (typeof parsed === "object" && parsed !== null) {
      const pretty = Object.entries(parsed).map(
        ([key, value]) => `<strong>${key}</strong>: ${Array.isArray(value) ? value.join(", ") : value}`
      ).join("<br>");
      col.innerHTML = pretty;
    }
  } catch (e) {
    // If it's not JSON, just leave as-is
  }
        tbody.appendChild(pair.detail);
      }
    });

    setupRowClicks();
    setupPagination();
  }

  function setupPagination() {
    if (!pagination) return;

    pagination.innerHTML = "";
    const pageCount = Math.ceil(allRowPairs.length / rowsPerPage);
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

function setupModalClickHandlers(tableId) {
  const table = document.getElementById(tableId);
  const rows = table.querySelectorAll("tr.clickable-row");

  rows.forEach(row => {
    row.addEventListener("click", () => {
      const data = row.getAttribute("data-detail");
      if (!data) return;

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
    });
  });
}


  // Initial render
  updateTable();
}

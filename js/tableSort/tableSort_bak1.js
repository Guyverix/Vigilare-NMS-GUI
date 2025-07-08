/*
  This is pretty generic, but likely will be needed as more
  tables are displayed.  Might as well get it out of the main
  php file and be able to use it generically

Calling from the main PHP page where it is being used:

<!-- Load the shared table functionality -->
<script src="/js/tableSort/tableSort.js"></script>

++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

The <table id="activeTable" must be defined so the js knows what to look at
You must have a <thead> with <th class="sortable">NAME</th> so it can sort correcctly
+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

This is after </table>  This must match the table name pagination-NAME

<ul class="pagination justify-content-center" id="pagination-historyTable"></ul>

++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
<script>
  document.addEventListener("DOMContentLoaded", function () {
    enableTableEnhancements("activeTable", 5);
  });
</script>

Per chatGPT you can have N+1 enableTableEnhancements defined above in the PHP
This makes life easier?  Will have to test.  Can still do one at a time.
*/

function enableTableEnhancements(tableId, rowsPerPage = 10) {
  const table = document.getElementById(tableId);
  if (!table) return;

  const tbody = table.querySelector("tbody");
  const allRows = Array.from(tbody.rows);
  const pagination = document.getElementById("pagination-" + tableId);
  if (!pagination) return;


console.log("Found table: ", table);
console.log("Rows found: ", allRows.length);
console.log("Headers: ", table.querySelectorAll("th.sortable"));


  let currentPage = 1;

  function paginate(page) {
    const start = (page - 1) * rowsPerPage;
    const end = start + rowsPerPage;
    allRows.forEach((row, i) => {
      row.style.display = (i >= start && i < end) ? "" : "none";
    });

    // Update active page
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
      const valA = a.cells[columnIndex].textContent.trim();
      const valB = b.cells[columnIndex].textContent.trim();

      const numA = Date.parse(valA) || valA.toLowerCase();
      const numB = Date.parse(valB) || valB.toLowerCase();

      if (numA < numB) return order === "asc" ? -1 : 1;
      if (numA > numB) return order === "asc" ? 1 : -1;
      return 0;
    });

    sorted.forEach(row => tbody.appendChild(row));
    paginate(1);
  }

  // Hook up sorting headers
  table.querySelectorAll("th.sortable").forEach((th, index) => {
    th.style.cursor = "pointer";
    th.addEventListener("click", () => {
      const currentOrder = th.dataset.order || "desc";
      const newOrder = currentOrder === "asc" ? "desc" : "asc";
      th.dataset.order = newOrder;

      // Reset other headers
      table.querySelectorAll("th.sortable").forEach(other => {
        if (other !== th) other.dataset.order = "";
      });

      sortTable(index, newOrder);
    });
  });

  buildPagination();
  paginate(1);
}


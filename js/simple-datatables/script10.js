window.addEventListener('DOMContentLoaded', event => {
    // Simple-DataTables
    // https://github.com/fiduswriter/Simple-DataTables/wiki
    const datatablesSimple = document.getElementById("datatablesSimple");
    if (datatablesSimple) {
        new simpleDatatables.DataTable("#datatablesSimple", {
        searchable: true,
        sortable: true,
        storable: true,
        perPage: 10,
        perPageSelect:[10,25,50,100,200],
        });
    }
//    $(document).ready(function () {
//      InitOverviewDataTable();
//      setTimeout(function(){AutoReload();}, 30000);
//    });
});

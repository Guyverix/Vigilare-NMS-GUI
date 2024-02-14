<?php
  /*
    We are going to search through a list for reporting templates
  */

  echo "<br><br><br>";
  require_once(__DIR__ . '/../../functions/generalFunctions.php');
  checkCookie($_COOKIE);  // disable check here to test 401 responses elsewhere due to expired stuff

  // Load local vars for use (urls, ports, etc)
  require_once __DIR__ . "/../../config/api.php";

  $reportName = $_GET['template'];

  $headers = array();
  $headers[] = 'Authorization: Bearer ' . $_COOKIE['token'];

  $post = array();  // We can use post, so give it an empty array to post with
  $post = ['id' => $_POST['id']];
  $rawReportingList = callApiPost("/reporting/viewComplete", $post , $headers);
  if (! is_array($rawReportingList)) {
    $rawReportingList = json_decode($rawReportingList, true);
  }
  $reportingList = json_decode($rawReportingList['response'], true);
  $reporting = $reportingList['data'];
  $quitEarly = 0;


  $postArray = json_encode($reporting,1);
  // debugger($reportingList);
  // Sanity check your results
  switch ($reportingList['statusCode']) {
   case 403:
     load4XX();
     $quitEarly = 1;
   case 200:
     break;
   default:
     decideResponse($reportingList['statusCode']);
     $quitEarly = 1;
  }
  if ($quitEarly == 0) {
    // Add a download CSV link
    echo '<form id="downloadCsv" method="POST" action="/reporting/downloadCsv.php?&template=' . $reportName . '">';
    echo '<input type="hidden" name="data" value="' . htmlspecialchars($postArray) . '">';
    echo '
      <style>
        td {
         text-align: center;
        }
      </style>';
    echo '
      <center><h1>View Completed report for ';
    echo $reportName . '</h1></center>';
    echo '<center><button form="downloadCsv" type="submit" class="btn btn-default btn-info btn-sm"><span class="glyphicon glyphicon-off"></span>Download CSV</button> &nbsp</center> ';
    echo '</form>';
    echo '
      <div class="container-lg">
      <table id="dt-reporting" class="table table-striped table-hover bg-dark table-dark" data-loading-template="loadingTemplate" style="white-space: nowrap;">
    ';
    // Create a table legend so we know what the values mean
    echo '<thead>';
    foreach($reporting[0] as $k1 => $v1) {
        echo '<th><center>' . $k1 . '</center></th>';
    }
    echo '</thead>';

    // Now loop through our data
    //debugger($reporting);
    foreach ($reporting as $report) {
      $arrayCount = count($report);
      //      echo "arrayCount " . $arrayCount . "<br>";
      //      debugger($report);
      echo '<tr>';
        //      for ( $i = 0; $i < $arrayCount; $i++) {
        foreach ($report as $k => $v) {
          //echo "KEY " . $k . " VALUE " . $v . "<br>";
          echo '<td>' . $v . '</td>';
        }
        //      }
      echo '</tr>';
    }
    echo '
      </table>
      </div>
  <!-- datatables not loaded with footer, add it now -->
  <script> window.addEventListener("DOMContentLoaded", event => {
    const datatablesSimple = document.getElementById("dt-reporting");
    if (datatablesSimple) {
      new simpleDatatables.DataTable("#dt-reporting", {
        searchable: true,
        sortable: true,
        storable: true,
        paging: true,
        perPage: 50,
        perPageSelect:[25,50,100,200],
        labels: {
          placeholder: "Search reports..."
        }
        });
      }
    });
  </script>
  <script src="/js/simple-datatables/simple-datatables.js"></script>
  ';

  }
  else {
    // Something went very wrong with the API call, but keep the layout clean...
    loadUnknown("API calls failed in an unexpected way.  Please reload");
  }
?>

<?php
  /*
    We are going to search through a list for reporting templates
  */

  echo "<br><br><br>";
  require_once(__DIR__ . '/../../functions/generalFunctions.php');
  checkCookie($_COOKIE);  // disable check here to test 401 responses elsewhere due to expired stuff

  // Load local vars for use (urls, ports, etc)
  require_once __DIR__ . "/../../config/api.php";

  $headers = array();
  $headers[] = 'Authorization: Bearer ' . $_COOKIE['token'];

  $post = array();  // We can use post, so give it an empty array to post with

  $rawReportingList = callApiGet("/reporting/searchComplete" , $headers);
  if (! is_array($rawReportingList)) {
    $rawReportingList = json_decode($rawReportingList, true);
  }
  $reportingList = json_decode($rawReportingList['response'], true);
  $reporting = $reportingList['data'];
  $quitEarly = 0;

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
  // debugger($reporting);
  if ($quitEarly == 0) {
    echo '
      <style>
        td {
         text-align: center;
        }
      </style>';
    echo '
      <center><h1>Search all completed reports</h1></center>
      <div class="container-lg">
      <table id="dt-reporting" class="table table-striped table-hover bg-dark table-dark" data-loading-template="loadingTemplate" style="white-space: nowrap;">
      <thead>
        <tr>
          <th><center>Template Name</center></th>
          <th><center>Report Id</center></th>
          <th><center>Report Date</center></th>
          <th><center>Report Status</center></th>
          <th></th>
        </tr>
      </thead>
    ';
    foreach ($reporting as $report) {
      echo '<tr><td>' . $report['reportName'] .'</td>';
      echo '<td>' . $report['id'] . '</td>';
      echo '<td>' . $report['reportDate'] . '</td>';
      echo '<td>' . $report['status'] . '</td>';
      echo '<td>';

      echo '<form id="runTemplate_' . $report['id'] . '" method="POST" action="/reporting/index.php?&page=viewReport.php&template=' . $report['reportName'] . '&reportDate=' . $report['reportDate'] . '">';
      echo '<input type="hidden" name="id" value="' . $report['id'] . '">';
      echo '<button type="submit" class="btn btn-sm btn-outline-primary" name="runTemplate" form="runTemplate_' . $report['id'] . '">View <i class="fas fa-play"></i></button> </form></td><td>';

      echo '</td></tr></div>';
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
        perPage: 25,
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























?>

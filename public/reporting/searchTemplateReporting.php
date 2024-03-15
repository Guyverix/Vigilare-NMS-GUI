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

  $rawReportingList = callApiGet("/reporting/searchTemplate" , $headers);
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
      <center><h1>Search all report templates</h1></center>
      <div class="container-lg">
      <table id="dt-reporting" class="table table-striped table-hover bg-dark table-dark" data-loading-template="loadingTemplate" style="white-space: nowrap;">
      <thead>
        <tr>
          <th><center>Template Name</center></th>
          <th colspan=2><center>Action</center></th>
        </tr>
      </thead>
    ';
    foreach ($reporting as $report) {
      echo '<tr><td>' . $report['template'] .'</td>';
      echo '<td>';

      echo '<form id="runTemplate_' . $report['template'] . '" method="POST" action="/reporting/index.php?&page=runReport.php">';
      echo '<input type="hidden" name="template" value="' .htmlspecialchars(json_encode($report['template'],1)) . '">';
      echo '<input type="hidden" name="templateArgs" value="' .htmlspecialchars(json_encode($report,1)) . '">';
      echo '<button type="submit" class="btn btn-sm btn-outline-primary" name="useTemplate" form="runTemplate_' . $report['template'] . '">Run Now <i class="fas fa-play"></i></button> </form></td><td>';

      echo '<form id="findReport_' . $report['template'] . '" method="POST" action="/reporting/index.php?&page=searchExistingReporting.php">';
      echo '<input type="hidden" name="template" value="' . $report['template'] . '">';
      echo '<input type="hidden" name="templateArgs" value="' .htmlspecialchars(json_encode($report,1)) . '">';
      echo '<button type="submit" class="btn btn-sm btn-outline-primary" name="searchTemplate" form="findReport_' . $report['template'] . '">Find Completed <i class="fas fa-eye"></i></button> </form>';

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
          placeholder: "Search templates..."
        }
        });
      }
    });
  </script>
  <script src="/js/simple-datatables/simple-datatables.js"></script>
  ';
  }























?>

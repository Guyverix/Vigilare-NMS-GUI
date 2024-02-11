<?php
  /*
    Primary landing page to display host and host information.

    Static info that does NOT change across pages
    This is actually raw HTML with nothing special in it.
  */

  // We now have to be authenticated to see stuff.
  // We use cookies to validate access is visible.
  // No cookie redirect.  We check expired cookies elsewhere (for now)
  echo "<br><br><br>";
  require_once(__DIR__ . '/../../functions/generalFunctions.php');
  checkCookie($_COOKIE);  // disable check here to test 401 responses elsewhere due to expired stuff

  // begin loading page since we have valid cookies
  readfile(__DIR__ . '/includes/topNavBar.php');

  // Load local vars for use (urls, ports, etc)
  require_once __DIR__ . "/../../config/api.php";

  if ( ! isset($_SESSION)) {
    //  Later version of UI will already have a session set, so this part can go away then
    session_start();
    $timezone = $_SESSION['time'];
    $raw=explode( ' ', $timezone);
    //    echo "TIME " . print_r($raw) . "\n"; // DEBUG
    $offset=$raw[1];
    $localOffset=( $offset * 3600);
  }
  //$timezone = $_SESSION['time'];
  //var_dump($_SESSION);
  //exit();
  //echo "timezone " . $timezone;
//  readfile(__DIR__ . '/includes/leftVerticalMenu.php');
/*
echo '
<style>
   table-dark tbody tr.selected {
        color: white;
        background-color: #99ccff;
        a:link{ color: black; }
        a:visited { color: black; }
    }
</style>';
*/

?>

    <link rel="stylesheet" href="/css/styles.css" />
    <link rel="stylesheet" href="/js/dataTable/DataTables-1.13.7/jquery.dataTables.min.css" />
    <script type="text/javascript" src="/js/dataTable/jQuery-3.6.0/jquery-3.6.0.min.js"></script>

    <link rel="stylesheet" href="/js/dataTable/Select-1.7.0/select.dataTables.min.css" />
    <script type="text/javascript" src="/js/dataTable/DataTables-1.13.7/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="/js/dataTable/Select-1.7.0/dataTables.select.min.js"></script>


<!-- Have to manually specify select color for datatable since we messed with it (somehow) TODO  -->
<script>
  $(document).ready( function () {
    if("<?php echo $timezone; ?>".length==0){
        var visitortime = new Date();
        var visitortimezone = "GMT " + -visitortime.getTimezoneOffset()/60;
        $.ajax({
          type: "GET",
          url: "/event/timezone.php",
          data: 'time='+ visitortimezone,
          success: function(){
          // alert(visitortimezone);  // DEBUG
          location.reload();
          }
        });
      }

    var rows_selected = [];
    var table = $('#ajaxEvents').DataTable({
      stateSave: true,
      lengthChange: true,
      dom: 'Bflrtip',
      autoWidth: true,
      ajax: {
        url: <?php echo "'" . $apiUrl . ':' . $apiPort . '/debug/events/view/eventSeverity/DESC/order' . "'";?>,
        dataSrc: "data",
        cache: false,
        crossDomain: true,
        xhrFields: {
          withCredentials: true
        },
        // headers: { 'Authorization': 'token: <?php echo $_COOKIE['token']; ?>'},
        headers: { 'Authorization': '<?php echo "Bearer " . $_COOKIE['token']; ?>'},
        error:  function(xhr, textStatus) {
          console.log(arguments);
          console.log("Status Code " + xhr.status);
          if ( xhr.status === 401 || xhr.status === 403) {
            // var htmlLink = '<a href="<?php echo $uiUrl; ?>/login/login.php">login </a>';
            // alert ("Login expired.  Please <a href=\"<?php echo $uiUrl; ?> /login/login.php\">login </a> again")
            // alert ("<div class=\"alert alert-danger\"><a href='<?php echo $uiUrl; ?> /login/login.php'>login </a></div>")
            // alert (htmlLink);
            if (window.confirm("Your login has expired.  Please re-login")) {
              window.location.replace("/login/login.php", "Thanks for Visiting!");
            }
          }
        },
        complete: function(xhr, textStatus) {
          console.log(arguments);
          console.log("Status Code " + xhr.status);
          if ( xhr.status === 200 || xhr.status === 201) {
            console.log("Status Code " + xhr.status);
          }
        }
      },
      "columnDefs": [
        {
          orderable: true,
          searchable: true,
          defaultContent: '',
          targets: 0,
          "render": function( data, type, row, meta) {
            return <?php echo "'" . "<a href=\"" . $uiUrl . "/host/hostnameDetails.php?id=' + data + '\">' + data + '</a>'"; ?>;
          }
        },
        {
          orderable: true,
          searchable: true,
          defaultContent: '',
          targets: [1],
          "render": function( data, type, row, meta) {
            // return '<p class="link-primary" data-bs-toggle="modal" data-bs-target="#detailModal-' + row[3] + '"><u> ' + data + '</u></p>';
            // <?php echo "'" . "<a href=\"" . $uiUrl . "/host/hostnameDetails.php?id=' + data + '\">' + data + '</a>'"; ?>;
            return data;
          }
        },
        {
          orderable: true,
          searchable: true,
          defaultContent: '',
          targets: [2]
        },
        {
          orderable: true,
          searchable: false,
          defaultContent: '',
          targets: [4],
          "render": function( data, type, row, meta) {
            return data;
          }
        },
        {
          targets: [7],
          visible: false
        },
        {
          targets: [8],
          visible: false,
          "render": function (data, type, row, meta) {
             return "Moved to history by <?php echo $_COOKIE['realName'] . " user id " . $_COOKIE['userid'] ; ?>" ;
          }
        },
      ],
      'select': {
         'style': 'multi'
      },
      order: [[6, 'desc']],
      columns: [
        { data: 'device', name: 'device' },
        { data: 'eventName', name: 'eventName' },
        { data: 'eventSummary', name: 'eventSummary' },
        { data: 'startEvent', name: 'startEvent' },
        { data: 'stateChange', name: 'stateChange' },
        { data: 'eventCounter', name: 'eventCounter' },
        { data: 'eventSeverity', name: 'eventSeverity' },
        { data: 'evid', name: 'evid' },
        { data: 'reason', name: 'reason' }
      ],
      processing: true,
      serverSide: false,
      lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
      "pageLength": 25
//    });  // end var table
    }

);  // end var table

    setInterval( function () {
     // table.ajax.reload();  // dumb page reload to page 1
      table.ajax.reload( null, false ); // paging is not reset
    }, 60000 );

    // Shows what would have actally been submitted
    // $('button').on('click', function (e) {
    //   e.preventDefault();
    //   var data = table.$('input, select').serialize();
    //   alert(
    //     'The following data would have been submitted to the server: \n\n' +
    //     data.substr(0, 120) +
    //     '...'
    //   );
    // });

    $('#myForm').on('submit', function(e) {
      e.preventDefault();                                           // Donno, suppressing some kind of default action on submission

      var table = $('#ajaxEvents').DataTable();                     // Pull from table named ajaxEvents
      var selectedData = table.rows('.selected').data().toArray();  // Convert raw JSON to an array?
      var dataToPost = {
        selectedRows: selectedData                                  // Assigning keyname "selectedRows" to the data to post
      };
      var formData = new FormData();                                // Some kind of builtin jqery thingie to make a normal POST

      selectedData.forEach(function(rowData, index) {               // Appears to be adding each row as array[###]
        formData.append('selectedRows[' + index + ']', JSON.stringify(rowData));
      });

      alert ( JSON.stringify(selectedData) );                     // just show what we are posting

      $.ajax({                                                    // All this work for a @!#$!@ POST?!? ( I hate JS )
        type: 'POST',
        url: <?php echo "'" . $apiUrl . ':' . $apiPort . '/debug/get/post' . "'" ?>,
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          // Handle success
         console.log('Success:', response),
          alert("success");
        },
        error: function(xhr, status, error, response) {
          // Handle error
          console.log('Error:', xhr.statusText),
          console.log('Error:', response),
          alert("supposed error");
        }
      }); // end .ajax
    });   // end #myForm
    $('#myForm2').on('submit2', function(e) {
      e.preventDefault();                                           // Donno, suppressing some kind of default action on submission

      var table = $('#ajaxEvents').DataTable();                     // Pull from table named ajaxEvents
      var selectedData = table.rows('.selected').data().toArray();  // Convert raw JSON to an array?
      var dataToPost = {
        selectedRows: selectedData                                  // Assigning keyname "selectedRows" to the data to post
      };
      var formData = new FormData();                                // Some kind of builtin jqery thingie to make a normal POST

      selectedData.forEach(function(rowData, index) {               // Appears to be adding each row as array[###]
        formData.append('selectedRows[' + index + ']', JSON.stringify(rowData));
      });

      alert ( JSON.stringify(selectedData) );                     // just show what we are posting

      $.ajax({                                                    // All this work for a @!#$!@ POST?!? ( I hate JS )
        type: 'POST',
        url: <?php echo "'" . $apiUrl . ':' . $apiPort . '/debug/get/post' . "'" ?>,
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          // Handle success
         console.log('Success:', response),
          alert("success");
        },
        error: function(xhr, status, error, response) {
          // Handle error
          console.log('Error:', xhr.statusText),
          console.log('Error:', response),
          alert("supposed error");
        }
      }); // end .ajax
    });   // end #myForm2
  });     // end document read
</script>

    <div class="container-fluid">
      <?php $localTime = (strtotime("now") + $localOffset); echo '<p class="text-end">Last Refresh: ' . date('Y-m-d H:i:s',$localTime) . "&nbsp&nbsp  </p>";  ?>
      <div id="content">
        <form id="myForm" class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
        <form id="myForm2" class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
        <input type="hidden" id="reason"  name="reason" value="Moved to history by <?php echo $_COOKIE['realName'] . " user id " . $_COOKIE['userid'] ; ?>" >
        <table id="ajaxEvents" class="dataTable-light cell-border compact stripe" style="white-space: nowrap;" border=1 >
          <thead>
            <tr>
              <th>Device</th>
              <th>Event Name</th>
              <th>Event Summary</th>
              <th>Start Event UTC</th>
              <th>Last Update UTC</th>
              <th>Counter</th>
              <th>Severity</th>
              <th><center>Event Id</center></th>
            </tr>
          </thead>
        </table>
       <button type="submit" class="btn btn-primary" name="submit" id="submit">History</button>
       <button type="submit" class="btn btn-primary" name="submit2" id="submit2">Ticket</button>
      </div>
  </div>

<?php
echo "<br>";
require_once( __DIR__ .'/includes/bottomFooter.php'); 
?>

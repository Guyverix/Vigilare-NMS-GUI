<?php
  /*
    Not a lot of error correction, since if someone messes up it is not going to damage stuff.
    They can retry the link on failure.
  */

  //require_once(__DIR__ . '/../../functions/generalFunctions.php');
  //echo debugger($_POST);


$fileName=$_GET['template'];
$dataArray=$_POST['data'];
$dataArray = json_decode($dataArray, true);


// Set headers to prompt for download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $fileName . '.csv"');

// Open the output stream
$fh = fopen('php://output', 'w');

// Loop over the data array and output each row
foreach ($dataArray as $row) {
    fputcsv($fh, $row);
}

// Close the file handle
fclose($fh);
exit;

?>

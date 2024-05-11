<?php

  require_once __DIR__ . "/../../config/api.php";


  // this should get changed later to use a function call
  $ch=curl_init();
  curl_setopt($ch, CURLOPT_URL, $apiUrl .":" . $apiPort . "/events/view/eventSeverity/DESC/order");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $output = curl_exec($ch) ;
  $output = json_decode($output, true);
  if ( $output['statusCode'] == 200) {
    $eventCount= count($output);
    $results1 = $output['data'];
  }
  else {
    $eventCount = 0;
    $results1 = array();
  }

$results = json_encode($results1,1);
//echo var_dump($results);
echo $results;

?>

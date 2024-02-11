<?php

// Retrieve the available Graphite URLs for the host
$options=['job' => 'source'];
$type='post';
$rawTableResultGraphite=retrieveGraphiteList($rawHostname,$type,$options);

$tableListGraphite=array();
$splitListGraphite=array();

// echo "<pre>" . print_r($rawTableResultGraphite['data']) . "</pre>";
foreach ($rawTableResultGraphite['data'] as $loopGraphite) {
  $tableListGraphite[] =  $loopGraphite['text'];
}

// print_r($tableListGraphite);
// Array ( [0] => cpu [1] => drive [2] => general [3] => interfaces [4] => lmsensors [5] => load [6] => memory [7] => nrpePoller [8] => pingPoller )


if (! empty ($rawTableResult)) {
  foreach ($rawTableResult as $lists) {
    //print_r($lists);
    foreach($lists as $list) {
    $splitList      = explode("$safeHostname.", $list);
    $splitRemaining = explode('.', $splitList[1]);
    $splitSource    = $splitRemaining[0];  // nrpePoooler, drive, interfaces, etc
    $splitCheck     = $splitRemaining[1];   // check_ping, enp2s0, etc
    $splitMetric    = preg_replace('/[,)].*/','', $splitRemaining[2]);  // rta, someName, randomResult, etc...
    //    $splitDisplay["$splitSource"] = ["$splitCheck" => "$splitMetric"];
    $splitDisplay["$splitSource"]["$splitCheck"][] = "$splitMetric" ;
    //echo $splitList[1] . "<br>";
    //echo "SRC " . $splitSource . " CHK " . $splitCheck . " MET " . $splitMetric . "<br>";
    //echo $list . "<br>\n<br>\n";
      switch ($list) {
        case strpos($list, '.lmsensors' ) !== false:
          $filterList = preg_replace('/.*..lmsensors./', '', $list);
          $sensorsPath = explode('.', $filterList);
          $sensorList = $sensorsPath[0];
          $sensorValue = preg_replace('/,.*/','', $sensorsPath[1]);
          $sensorValue = preg_replace('/\).*/','', $sensorsPath[1]);
          $tableList['lmsensors']["$sensorList"] = "$sensorValue";
          break;
        case strpos($list, '.load' ) !== false:
          $filterList = preg_replace('/.*.\.load\./', '', $list);
          $filterList = preg_replace('/.*.\.nrpePoller\./', '', $filterList);  // NRPE checks for load also hit this, dammit
          $loadsPath = explode('.', $filterList);
          $loadList = $loadsPath[0];
          $loadValue = preg_replace('/[,)].*/','', $loadsPath[1]);  // strip out all after seeing , or ) character
          $tableList['load']["$loadList"] = "$loadValue";
          break;
        case strpos($list, '.interfaces' ) !== false:
          $filterList=preg_replace('/.*..interfaces./', '', $list);
          $interfacePath=explode('.', $filterList);
          $interfaceList=$interfacePath[0];
          $interfaceValue=preg_replace('/,.*/','', $interfacePath[1]);
          $tableList['interfaces']["$interfaceList"] = "$interfaceValue";
          break;
        case strpos($list, '.nrpePoller' ) !== false:
          $filterList = preg_replace('/.*..nrpePoller./', '', $list);
          $nrpePath = explode('.', $filterList);
          $nrpeList = $nrpePath[0];
          $nrpeValue = preg_replace('/[,)].*/','', $nrpePath[1]);
          $tableList['nrpePoller']["$nrpeList"] = "$nrpeValue";
          break;
        default:
          // this is a best effort attempt to show anything else that remains
          $defaultPath = explode('.', $list);
          $defaultList = $defaultPath[2];
          $defaultValue = preg_replace('/[,)].*/','', $defaultPath[3]);
          $tableList['default']["$defaultList"] = "$defaultValue";
          break;
      }  // end switch
//      }  // end switch
    }  // end foreach
  }  // end foreach
} // end if
echo "<pre>";
print_r($splitDisplay);
echo "</pre>";
?>

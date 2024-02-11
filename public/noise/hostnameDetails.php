<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <link rel="stylesheet" href="../css/default2.css" type="text/css" />
  <style type="text/css"></style>
  <title>Host Details</title>
<style>
table.tableBgImg
{
//   background-image: url("./tableTest.png");
background-image: linear-gradient(blue, white, blue, white);
   background-size: cover;
   background-repeat:no-repeat;
   background-size:100% 100vh;
   height: "100%";
   width: "100%";
}
</style>

</head>
<body>

<?php
////////////////////////////////////////////////
//  Define any necessary constants here
//  defined as URL?id=FQDN
///////////////////////////////////////////////
$rawHostname=$_GET[id];
$osImg="images/debris.png";
$img="class=tableBgImg";


/*
get id, hostname, ip       from host          by FQDN
get component, name, value from hostAttribute by FQDN
get checkName date, values from performance   by FQDN
get checkName              from shellPoller   by FQDN
get oid lastRun            from snmpPoller    by id  <--- This should be changed to match the others
get checkName              from nrpePoller    by FQDN
*/

/*
switch: graphite vs javascript display

table: fqdn, ip, env, location, coordinates
table: events
table: history
table: ports active
table: nrpe
table: shell
table: snmp
table: interface
table: drive
table: lmsensors
*/

//  Initiate curl
$ch = curl_init();
$url = 'http://127.0.0.1:8002/host/find';

// Will return the response, if false it print the response
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "hostname=$rawHostname");

// Execute
$rawResult=curl_exec($ch);

// Closing
curl_close($ch);
//print_r($result);

$result=json_decode($rawResult, true);
// print_r($result['data']);

// There will only EVER be a single array returned so index [0] always

foreach( $result as $hostResults) {
  // print_r($hostResults);
  $id=$hostResults[0]['id'];
  $hostname=$hostResults[0]['hostname'];
  $ipAddress=$hostResults[0]['address'];
  $firstSeen=$hostResults[0]['firstSeen'];
  $monitor=$hostResults[0]['monitor'];
}
// Set defaults for host table to load
$hostHeader="<div>Host Name:<table $img border=1><tbody>";
$hostBody='';
$hostFooter="</tbody></table></div>";





if (empty($result['data'])) {
  //  echo "empty result<br>";
  header("HTTP/1.0 404 Not Found");
  include("404-host.html");
}


// Next pulling attributes
$ch2 = curl_init();
$url2 = 'http://127.0.0.1:8002/host/attribute';
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_URL,$url2);
curl_setopt($ch2, CURLOPT_POST, 1);
curl_setopt($ch2, CURLOPT_POSTFIELDS, "hostname=$rawHostname");
$rawResult2=curl_exec($ch2);
curl_close($ch2);
$result2=json_decode($rawResult2, true);
//print_r($rawResult2);
//$img="style='background-image: url(./tableTest.png)' width='100%' height='100%'";
//$img="style='background-image: url(./tableTest.png)' no-repeat center center fixed; background-size:cover";

if ( ! empty($result2)) {
  $attributeHeader="<div class=divBgImg>Host Attributes<table $img border=1><tbody><tr><td>Component</td><td>Name</td><td>Value</td></tr>";
  $attributeFooter="</tbody></table></div>";
  // echo "<div>Host Attributes<table border=1><tbody><tr><td>Component</td><td>Name</td><td>Value</td></tr>";
  foreach( $result2 as $attributeResults1) {
    foreach( $attributeResults1 as $attributeResults) {
      // print_r($attributeResults);
      $isName=strtolower($attributeResults['name']);  // lower case this so we can do a string compare easily
      $isPass=strpos($isName, 'pass');                // do not caps this, as we already lower-cased the name
      if ( $isPass !== false ) {
        $attributeResults['value']=preg_replace("|.|","*",$attributeResults['value']);
      }
      $attributeBody .="<tr><td>" . $attributeResults['component'] . "</td><td>". $attributeResults['name'] . "</td><td>" . $attributeResults['value'] . "</td></tr>";
      // echo "<tr><td>" . $attributeResults['component'] . "</td><td>". $attributeResults['name'] . "</td><td>" . $attributeResults['value'] . "</td></tr>";
    }
  }
}

// Next Pulling performance
$ch3 = curl_init();
$url3 = 'http://127.0.0.1:8002/host/performance';
curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch3, CURLOPT_URL,$url3);
curl_setopt($ch3, CURLOPT_POST, 1);
curl_setopt($ch3, CURLOPT_POSTFIELDS, "hostname=$rawHostname");
$rawResult3=curl_exec($ch3);
curl_close($ch3);
$result3=json_decode($rawResult3, true);
//print_r($rawResult3);
//print_r($result3);

foreach($result3 as $performanceResults1) {
  foreach ($performanceResults1 as $performanceResults) {
    switch ($performanceResults['checkName']) {
      case "1.3.6.1.2.1.1.1.0":
        $hostOses=$performanceResults['value'];
        // echo "<br>HostOS to parse " . $hostOses . "<br>";
        switch (true) {
          case strpos($hostOses, 'Dell' ) !== false:
            $osImg="images/debris.png";
            break;
          case strpos($hostOses, 'Ubuntu' ) !== false:
            $osImg="images/ubuntu.png";
            break;
          case strpos($hostOses, 'Red Hat' ) !== false:
            $osImg="images/rh.png";
             break;
          case strpos($hostOses, 'Mint' ) !== false:
            $osImg="images/mint.png";
            break;
          case strpos($hostOses, 'Debian' ) !== false:
            $osImg="images/debian.png";
            break;
          case strpos($hostOses, 'Windows' ) !== false:
            $osImg="images/windows.png";
            break;
          case strpos($hostOses, 'PopOS' ) !== false:
            $osImg="images/popos.png";
            break;
          case strpos($hostOses, 'Arch' ) !== false:
            $osImg="images/arch.png";
            break;
        }
        // echo "<br>OS Image: " . $osImg . "<br>";
        break;
      case "1.3.6.1.2.1.6.13.1.5":
        // listening ports
        $portBody='';
        $ports=json_decode($performanceResults['value'], true);
        $portHeader="<div>Port Table " . $performanceResults['date'] . "<table $img border=1><tbody><tr><td>Bind Address</td><td>Port</td></tr>";
        $portFooter="</tbody></table></div>";
        foreach ($ports as $port) {
          $portBody .="<tr><td>" . $port['address'] . "</td><td>" .  $port['port'] . "</td></tr>";
        }
        break;
      case "1.3.6.1.2.1.25.1":
        // boot info
        $boots=json_decode($performanceResults['value'], true);
        echo "<br>boot " . $performanceResults['date'] . "<br>";
        foreach ($boots as $boot) {
          print_r($boot);
          echo "<BR>";
        }
        break;
      case "1.3.6.1.2.1.1.6.0":
        // syslocation
        $syslocation = $performanceResults['value'];
        //echo "<br>sysLocation " . $syslocation . "<br>";
        break;
      case "1.3.6.1.2.1.1.3.0":
        // daemon uptime
        $uptime = $performanceResults['value'];
        echo "<br>uptime in seconds " . $uptime . "<br>";
        break;
      case "1.3.6.1.4.1.2021.10":
        // load metrics
        $loads=json_decode($performanceResults['value'], true);
        echo "<br>Load Metrics " . $performanceResults['date'] . "<br>";
        print_r($loads);
        foreach ($loads as $load) {
          print_r($load);
          echo "<br>";
        }
        break;
      case "1.3.6.1.4.1.2021.11":
        // systemStats
        $systemStats=json_decode($performanceResults['value'], true);
        echo "<br>System Statistics " . $performanceResults['date'] . "<br>";
        foreach ($systemStats as $systemStat) {
          print_r($systemStat);
          echo "<br>";
        }
        break;
      case "1.3.6.1.4.1.2021.13.16":
        // lmsensors
        $lmsensors=json_decode($performanceResults['value'], true);
        echo "<br>LM-SENOSRS " . $performanceResults['date'] . "<br>";
        foreach ($lmsensors as $lmsensor) {
          print_r($lmsensor);
          echo "<br>";
        }
        break;
      case "1.3.6.1.4.1.2021.4":
        // memory
        $memorys=json_decode($performanceResults['value'], true);
        echo "<br>Memory " . $performanceResults['date'] . "<br>";
        foreach ($memorys as $memory) {
          print_r($memory);
          echo "<br>";
        }
        break;
      default:
        $defaultBody='';
        $defaultHeader="<div>Catch Unmapped Table<table $img border=1 style=\"width:900\"><tbody><tr><td>Check Name</td><td>Last Check</td><td>Check Result</td></tr>";
        $defaultFooter="</tbody></table></div>";
        $defaultBody .= "<tr><td>" . $performanceResults['checkName'] . "</td><td>" . $performanceResults['date'] . "</td><td>" . $performanceResults['value'] . "</td></tr>";
        break;
    } // end switch
  } // end foreach
}  // end foreach

/* Values above CAN change the host values, so only set the table info now! */
$hostBody="<tbody><tr><td rowspan='2'><img src=" . $osImg . "></img></td><td>" . $hostname . "</td><td>" . $ipAddress . "</td></tr><tr><td><center>First Seen:<br>" . $firstSeen . "</center></td><td><center>Monitor State:<br>" . $monitor . "</center></td></tr>";


// Set a unique name for table css LATER!
echo $hostHeader;
echo $hostBody;
echo $hostFooter;


if ( ! empty($syslocation)) {
  if ( ! empty($attributeBody)) {
    // Dont bother adding rice if the table does not exist :)
    $attributeBody .="<tr><td>SNMP</td><td>sysLocation</td><td>" . $syslocation . "</td></tr>";
  }
}

if ( ! empty($attributeHeader)) {
  echo "<br>";
//  echo "<div style='background-image: url(./tableTest.png)'>";
  echo $attributeHeader;
  echo $attributeBody;
  echo $attributeFooter;
//  echo "</div>";
}

if ( ! empty($portHeader)) {
  echo "<br>";
  echo $portHeader;
  echo $portBody;
  echo $portFooter;
}

if ( ! empty( $defaultHeader)) {
  echo "<br>";
  echo $defaultHeader;
  echo $defaultBody;
  echo $defaultFooter;
}
echo "<br>";

?>

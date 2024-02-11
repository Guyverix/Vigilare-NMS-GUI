<!DOCTYPE html>
<html lang="en">
<!-- <META HTTP-EQUIV=Refresh CONTENT="10">  -->
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="" />
  <meta name="author" content="" />

  <title>Infrastructure UI</title>

  <link href="/js/bootstrap-5/css/bootstrap.min.css" rel="stylesheet" />
  <link href="/css/styles.css" rel="stylesheet" />
  <script src="/js/treeMenu/showCategory.js"></script>
</head>

<body onLoad="nokids();">


<?php

$children = 0;
$no_children = array();
require_once __DIR__ . '/../../config/api.php';

function display_children($parent, $level) {
  global $children;
  global $no_children;
  global $apiHostname;
  global $apiPort;

  $ch=curl_init();
  curl_setopt($ch, CURLOPT_URL, $apiHostname.':'.$apiPort."/infrastructure/findChildren");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, "parent=$parent");
  $output = curl_exec($ch) ;
  $output = json_decode($output, true);
  curl_close($ch);


  // if this is a sub category nest the list
  if($level > 0) {
    echo "<ul id='$parent' style='display:none;'>\n";
  }
  $list_id='';
  $row1 = $output['data'];

  // List each child
  foreach ( $row1 as $row ) {
    $children++;
    $list_id = 'list_' . $children;
    echo '<li id="' . $list_id . ' " class="list-group-item list-group-item-primary" >';
    echo '<a href="#" onClick="show(' . $row['category_id'] .', \'\')">';
    //    echo '<a href="#" onClick="show(' . $row['category_id'] .', \'' . $HTTP_PATH . '\')">';
    echo '<img src="/images/recursion/images/c.gif" title="expand" border="0" id="img_' . $row['category_id'] . '">';
    echo '</a>&nbsp;&nbsp;';
    echo $row['category_name'];
    echo '</li>';

    // Call function again to display childs children
    display_children($row['category_id'], $level+1);
  }


  $child_product = display_products($parent);

  //if this is a sub category nest the list...
  if($level > 0) {
    echo "</ul>\n";
    $no_children[] =  'list_'.$children;

    //if the category has at least one product in it allow us to expand and see that product...
    if($child_product) {
      array_pop($no_children);
    }
  } // end if
}  // end function call



function display_products($parent) {
  global $apiHostname;
  global $apiPort;
  $child_product = false;

  // retrieve all children of $parent
  $ch=curl_init();
  curl_setopt($ch, CURLOPT_URL, $apiHostname.':'.$apiPort."/infrastructure/findChildrenOfParent");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, "category_id=$parent");
  $output = curl_exec($ch) ;
  $result = json_decode($output, true);
  curl_close($ch);

  $row1=$result['data'];
  foreach($row1 as $row) {
    echo '<li class="list-group-item list-group-item-action list-group-item-primary">';
    echo '<img src="/images/recursion/images/new.gif" title="product" border="0" id="img_'.$row['category_id'].'" style="margin-top: 5px;">';
    echo '&nbsp;&nbsp;';
    // echo $row['product_name'];  // No links, just hosts named..
    echo '<a href="/host/hostnameDetails.php?id=' . $row['product_name'] . '" target="_blank" ' . ' > ' .  $row['product_name'] . ' </a>';
    echo '</li>';
    $child_product = true;
  }  // end foreach
  return $child_product;
}  // end function


/* Main actual start of function calls */
echo "<ul style='list-style:none;'>";
display_children('',0);
echo "</ul>";
?>


<!-- Cannot use in js, due to inline PHP.  Keep it in the main page -->
<script language="javascript" type="text/javascript">
  function nokids() {
    var kidnot;
    kidnot = new Array();
    <?php
      for($i=0; $i<count($no_children); $i++) {
        print("kidnot.push(\"".$no_children[$i]."\");\n");
      }
    ?>
    for(i in kidnot){
      var theid;
      theid = kidnot[i];
      document.getElementById(kidnot[i]).style.color = "red";
    }
  }
</script>

</body>
</html>

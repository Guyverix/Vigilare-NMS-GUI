<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Tree Menu</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<style>
  li{
    list-style:none;
  }
</style>

<script language="javascript">
  function show(subCategory, path) {
    var thesub;
    var theimage;
    var imageid;
    var testingdiv;
    thesub = document.getElementById(subCategory);
    imageid = "img_"+subCategory;
    theimage = document.getElementById(imageid);
    testingdiv = document.getElementById("testing");
    if(thesub.style.display == 'block') {
      //collapse...
      thesub.style.display = 'none';
      theimage.src = path+'/images/recursion/images/c.gif';
    }
    else {
      thesub.style.display = 'block';
      theimage.src = path+'/images/recursion/images/e.gif';
    }
  }
</script>
</head>
<body onLoad="nokids();">


<?php
define('HTTP_PATH', 'http://larvel01:82/images/recursion/');


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


  // if this is a sub caategory nest the list
  if($level > 0) {
    echo "<ul id='$parent' style='display:none;'>\n";
  }
  $list_id='';
  $row1 = $output['data'];

  // Display each child
  foreach ( $row1 as $row ) {
    $children++;
    $list_id = 'list_' . $children;
    echo '<li id="'.$list_id.'">';
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
  }

}



function display_products($parent) {
   global $apiHostname;
   global $apiPort;
   global $HTTP_PATH;
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
     echo '<li>';
     echo '<img src="/images/recursion/images/new.gif" title="product" border="0" id="img_'.$row['category_id'].'" style="margin-top: 5px;">';
     echo '&nbsp;&nbsp;';
     echo $row['product_name'];
     echo '</li>';
     $child_product = true;
   }
   return $child_product;
}


/* Main actual start of function calls */

echo "<ul style='list-style:none;'>";
display_children('',0);
echo "</ul>";

?>

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
      document.getElementById(kidnot[i]).style.color = "green";
    }
  }
</script>

</body>
</html>

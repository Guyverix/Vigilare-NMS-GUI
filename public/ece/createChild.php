<?php
/*
  Create an ECE child
*/


// Only needed for debugging and bypassing security, etc
require_once(__DIR__ . '/../../functions/generalFunctions.php');
checkCookie($_COOKIE);  // disable check here to test 401 responses elsewhere due to expired stuff

// Load local vars for use (urls, ports, etc)
require_once __DIR__ . "/../../config/api.php";

// Grab our POSSIBLE values so users can choose what they change
$headers = array();
$headers[] = 'Authorization: Bearer ' . $_COOKIE['token'];
$post = array();  // We are using post, so give it an empty array to post with
$quitEarly = 0;
echo "<br>\n<br>\n<br>\n";

if (isset($_POST['categoryName'])) {
  // Time to update the API
  $post = $_POST;
  // debugger($post);
  $rawMakeParent = callApiPost("/ece/create/parent", $post, $headers);
  $makeParent = json_decode($rawMakeParent['response'], true);
  $responseCode = $makeParent['statusCode'];

  // Disable refresh rePOSTing
  echo '<script type="text/javascript">' . "\n";
  echo 'if ( window.history.replaceState ) {' . "\n";
  echo '  window.history.replaceState( null, null, window.location.href );' . "\n";
  echo '}' . "\n";
  echo '</script>' . "\n";

  // debugger($makeParent);
  if ($responseCode !== 200 && $responseCode !== 403) {    // Anything other than a 200 OK is an issue
    $responseString = '';
    if ( isset($makeParent['error'])) {
      foreach ($makeParent['error'] as $errors) {
        $responseString .= $errors . "<br>";
      }
    }
    else {
      foreach ($makeParent['data'] as $errors) {
        $responseString .= $errors . "<br>";
      }
    }
    decideResponse($responseCode, $responseString );
  }
  elseif ( $responseCode == 403) {
    load403Warn("Expired access credentials");
  }
  else {
    // After a successful update, wait and then reload the page
    successMessage('Child creation was successful.');
  }
}

$rawCurrentParents = callApiPost("/ece/family",$post, $headers);
$currentParents = json_decode($rawCurrentParents['response'], true);
// debugger($currentParents);

$rawEceList = callApiPost("/ece/list", $post, $headers);
$eceList = json_decode($rawEceList['response'], true);
//debugger($eceList['data']);

foreach($currentParents['data'] as $parent) {
  if (empty($parent['parentId'])) {
    $existingParents[$parent['categoryName']] = $parent['categoryId'];
  }
}
// debugger($existingParents);

foreach($currentParents['data'] as $parent2) {
  if ( ! array_key_exists('children', $parent2)) {
    $noChildren[$parent2['categoryName']] = $parent2['categoryId'];
  }
}
// debugger($noChildren);

if ( $quitEarly == 0 ) {
  echo "<div class='container-fluid'>\n";
  echo "<div class='row justify-content-center'>\n";
  echo "<div class='col-sm-2'>\n";
  echo "<div class='card shadow-lg border-0 rounded-lg mt-5'>\n";
  echo "<div class='card-header'>\n<h3 class='text-center font-weight-light my-4'>Known Root Parents</h3>\n</div>\n";
  echo "<div class='card-body'>\n";
  echo "<table id='parent-list' class='table table-striped bg-dark table-dark'><b>All existing root parents</b>\n";
  echo "<tr><th>Name</th><th>Parent ID</th></tr>\n";
  foreach ($existingParents as $k => $v) {
    echo "<tr class='table-success'><td>" . $k . "</td><td>" . $v . "</td><tr>\n";
  }
  echo "</table>\n";

  echo "<table id='no-children-list' class='table table-striped bg-dark table-dark'><b>Root Parents with no children</b>\n";
  echo "<tr><th>Name</th><th>Parent ID</th></tr>\n";
  if ( empty($noChildren)) {
    echo "<tr><td colspan=2>All defined parents have at least one child</td></tr>\n";
  }
  else {
    foreach ($noChildren as $k => $v) {
      echo "<tr class='table-danger'><td>" . $k . "</td><td>" . $v . "</td></tr>\n";
    }
  }
  echo "</table>\n";
  echo "</div>\n";
  echo "</div>\n";
  echo "</div>";
  // End column
  echo "<div class='col-sm-2'>";
  echo "<div class='card shadow-lg border-0 rounded-lg mt-5'>\n";
  echo "<div class='card-header'>\n<h3 class='text-center font-weight-light my-4'>All Children</h3>\n</div>\n";
  echo "<div class='card-body'>\n";
  echo "<table id='child-list' class='table table-striped bg-dark table-dark'><b>All existing children that are not root</b>\n";
  echo "<tr><th>Name</th><th>Child ID</th><th>Direct Parent</th></tr>\n";
  if (empty($eceList['data'])) {
    echo "<tr><td colspan=3>There are no defined children anywhere.</td></tr>\n";
  }
  else {
    foreach ($eceList['data'] as $existingChildren) {
      if ( ! empty($existingChildren['parentId'])) {
        echo "<tr class='table-success'><td>" . $existingChildren['categoryName'] . "</td><td>" . $existingChildren['categoryId'] . "</td><td>" . $existingChildren['parentId'] . "</td></tr>\n";
      }
    }
  }
  echo "</table>\n";
  echo "</div>\n";
  echo "</div>\n";
  echo "</div>";

?>
<div class="col-sm-1">
</div>
<div class="col-lg-5">
  <div class="card shadow-lg border-0 rounded-lg mt-5">
    <div class="card-header"><h3 class="text-center font-weight-light my-4">Create new ECE Child</h3><b>Children can also be children of other children too</b></div>
      <div class="card-body">
        <!-- action '' means this will post to itself -->
        <form action="" method="POST">
          <div class="form-floating mb-3">
            <input class="form-control" id="categoryName" name="categoryName" type="text" placeholder="parentName" />
            <label for="parentName">A generic name for this child</label>
          </div>
          <div class="form-floating mb-3">
            <input class="form-control" id="parentId" name="parentId" type="text" placeholder="parentId" />
            <label for="parentName">The id number of the parent</label>
          </div>
          <div class="form-floating mb-3">
            <input class="form-control" id="associatedHost" name="associatedHost" type="text" placeholder="associatedHosts" />
            <label for="associatedHosts">Host ids as 1 ,2, 3 (optional)</label>
          </div>
          <div class="form-floating mb-3">
            <input class="form-control" id="associatedCheck" name="associatedCheck" type="text" placeholder="associatedChecks" />
            <label for="associatedChecks">Service checks as checkFoo, checkBar, checkBaz (optional)</label>
          </div>
          <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
            <button type="submit" class="btn btn-primary">Submit</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  </div>
</div>



<?php
}
else {
  // API calls failed somewhere
  loadUnknown("API calls failed in an unexpected way.  Please reload");
}
?>

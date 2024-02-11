<?php
  /*
    This is used for the form submit with the create monitor details.
    Success, we will simply show a banner of success, timer it and
    unset the $_POST vars with a page reload.  On failure
    we need to give a reason and then trigger a reload on click?
  */

  /*
    We need to make some API calls, so load defaults to do so
  */
  $headers = array();
  $headers[] = 'Authorization: Bearer ' . $_COOKIE['token'];
  // $headers[] = 'Content-length: 0';
  // $headers[] = 'Content-type: application/json';


  if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // $headers = array();
    // $headers[] = 'Authorization: Bearer ' . $_COOKIE['token'];

    $postData = $_POST;
    $callApi = callApiPost('/monitors/createMonitor', $postData, $headers);  // returns an array
    $parentDecode = json_decode($callApi['response'], 1);
    $responseCode = $parentDecode['statusCode'];
    $childDecode = json_decode($parentDecode['result'], 1);
    $responseString = json_encode($childDecode, 1);

    if ( $responseCode !== 200 && $responseCode !== 403) {    // Anything other than a 200 OK is an issue
      echo "<br><br><br>";
      decideResponse($responseCode, $responseString );
    }
    elseif ( $responseCode == 403) {
      load403Warn("Expired access credentials");
    }
    else {
      // After a successful creation, wait and then reload the page
      echo "<br><br><br>";
      successMessage('Monitor creation is successful.');
      $_SERVER['REQUEST_METHOD'] = '';   // Unset our POST before reloading the page :)
      /*  This is not needed.  the page loads fine even after creating a new monitor.
      echo '<script>
              window.setTimeout(function() {
              window.location.reload();
              }, 2000);
           </script>';
     */
    }
  }

  $post = array();  // We are using post, so give it an empty array to post with
  // we SHOULD have gotten an array.... but if not convert it back to one
  $rawIteration = callApiPost("/monitors/findMonitorIteration" , $post, $headers);
  if (! is_array($rawIteration)) {
    $rawIteration = json_decode($rawIteration, true);
  }

  $rawType = callApiPost("/monitors/findMonitorType",$post, $headers);
  if (! is_array($rawType)) {
    $rawType = json_decode($rawType, true);
  }

  $rawStorage = callApiPost("/monitors/findMonitorStorage", $post, $headers);
  if (! is_array($rawStorage)) {
    $rawStorage = json_decode($rawStorage, true);
  }

  // all vars are as $var['data'] for the returned information
  $iteration = json_decode($rawIteration['response'], true);
  $type = json_decode($rawType['response'], true);
  $storage = json_decode($rawStorage['response'],true);
  $quitEarly = 0;

  // Sanity check your results
  switch ($iteration['statusCode']) {
   case 403:
     echo "<br><br><br><br>";
     load4XX();
     $quitEarly = 1;
   case 200:
     break;
   default:
     echo "<br><br><br><br>";
     decideResponse($iteration['statusCode']);
     $quitEarly = 1;
  }

  switch ($type['statusCode']) {
   case 403:
     echo "<br><br><br><br>";
     load4XX();
     $quitEarly = 1;
   case 200:
     break;
   default:
     echo "<br><br><br><br>";
     decideResponse($type['statusCode']);
     $quitEarly = 1;
  }

  switch ($storage['statusCode']) {
   case 403:
     echo "<br><br><br><br>";
     load4XX();
     $quitEarly = 1;
   case 200:
     break;
   default:
     echo "<br><br><br><br>";
     decideResponse($storage['statusCode']);
     $quitEarly = 1;
  }
  /*
    We know we are going to need 3 clean arrays of values to use in the page.  Create them now to consume later
    I know the var names suck.  Make your own :P
  */
  $selectIteration = array();
  $selectType = array();
  $selectStorage = array();
  //echo "<br><br><br>";
  foreach ( $iteration['data']['result'] as $iter) {
    $selectIteration[] = $iter['iteration'];
  }
  asort($selectIteration);

  foreach ($type['data']['result'] as $typ) {
    $selectType[] = $typ['type'];
  }
  asort($selectType);

  foreach ($storage['data']['result'] as $stor) {
    $selectStorage[] = $stor['storage'];
  }
  asort($selectStorage);

  // 0 means all good, 1 means API calls failed and we should stop working on this page
  if ( $quitEarly == 0 ) {
  /*
    This is going to get REALLY fugly now.  We are going to mix
    raw HTML with PHP.  It looks bad, but since we will not be
    changing oddball stuff, it actually will make it simpler although ugly
  */

echo '
<div class="container">
  <div class=" text-center mt-5 ">
    <h1>Create New Monitor</h1>
  </div>
  <div class="row ">
    <div class="col-lg-7 mx-auto">
      <div class="card mt-2 mx-auto p-4 bg-light">
        <div class="card-body bg-light">
          <div class = "container">
            <form id="create-monitor-form" role="form" action="" method="POST">
            <div class="controls">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="form_checkName">Check Name *</label>
                    <input id="form_checkName" type="text" name="checkName" class="form-control" placeholder="stronglyEncourageCamelCase *" required="required" data-error="A unique check name is required.">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="form_storage">Storage *</label>
                    <select id="form_storage" name="storage" class="form-control" required="required" data-error="Please specify your storage type.">
                    <option value="" selected disabled>--Select metric storage type--</option>
';
                    foreach ($selectStorage as $finalStorage) {
                      echo "<option value=\"" . $finalStorage . "\">" . $finalStorage . "</option>";
                    }
echo '
                    </select>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="form_iteration">Iteration *</label>
                    <select id="form_iteration" name="iteration" class="form-control" required="required" data-error="Please specify your iteration cycle.">
                    <option value="" selected disabled>--Select Iteration cycle--</option>
';
                    foreach ($selectIteration as $finalIteration) {
                      echo "<option value=\"" . $finalIteration . "\">" . $finalIteration . "</option>";
                    }
echo '
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="form_type">Type of check *</label>
                    <select id="form_type" name="type" class="form-control" required="required" data-error="Please specify your monitor type.">
                      <option value="" selected disabled>--Select Monitor Type--</option>
';
                    foreach ($selectType as $finalType) {
                      echo "<option value=\"" . $finalType . "\">" . $finalType . "</option>";
                    }
echo '
                    </select>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label for="form_checkAction">Check Action *</label>
                    <textarea id="form_checkAction" name="checkAction" class="form-control" placeholder="Drop your oid value, or command to run for the service check here." rows="4" required="required" data-error="something in this field choked."></textarea>
                  </div>
                </div>
                <div class="col-md-12">
                  <input type="submit" class="btn btn-success btn-send  pt-2 btn-block" value="Create Monitor" >
                </div>
              </div> <!-- row -->

            </div> <!-- controls -->
         </form>
        </div> <!-- container -->
      </div> <!-- card -->
    </div> <!-- col-lg-7 -->
  </div> <!-- row -->
</div> <!-- container -->
';
  }
  else {
    // Our API did not give us usable information.  May be transient, or API server is borked.
    loadUnknown("API calls failed in an unexpected way.  Please reload");
  }
?>


<?php
  /*
    We need to make some API calls, so load our class to do so
  */
  require_once(__DIR__ . '/../../functions/generalFunctions.php');
  require_once __DIR__ . "/../../config/api.php";

  $headers = array();
  $headers[] = 'Content-length: 0';
  $headers[] = 'Content-type: application/json';
  $headers[] = 'Authorization: Bearer ' . $_COOKIE['token'];
  $rawIteration = callApiGet("/monitors/findMonitorIteration", $headers);

  // we SHOULD have gotten an array.... but if not convert it back to one
  if (! is_array($rawIteration)) {
    $rawIteration = json_decode($rawIteration, true);
  }

  $rawType = callApiGet("/monitors/findMonitorType", $headers);
  if (! is_array($rawType)) {
    $rawType = json_decode($rawType, true);
  }

  $rawStorage = callApiGet("/monitors/findMonitorStorage", $headers);
  if (! is_array($rawStorage)) {
    $rawStorage = json_decode($rawStorage, true);
  }
//  $iteration=
//  $type=
//  $storage=
//echo var_dump($rawIteration);
//echo var_dump($rawType);
//echo var_dump($rawStorage);
echo "<BR><BR>here";
exit();

?>

<div class="container">]
  <div class=" text-center mt-5 ">
    <h1>Create New Monitor</h1>
  </div>
  <div class="row ">
    <div class="col-lg-7 mx-auto">
      <div class="card mt-2 mx-auto p-4 bg-light">
        <div class="card-body bg-light">
          <div class = "container">
            <form id="contact-form" role="form">
            <div class="controls">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="form_name">Check Name *</label>
                    <input id="form_name" type="text" name="name" class="form-control" placeholder="stronglyEncourageCamelCase *" required="required" data-error="Firstname is required.">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="form_lastname">Storage *</label>
                    <input id="form_lastname" type="text" name="surname" class="form-control" placeholder="Choose a storage class for metrics *" required="required" data-error="Lastname is required.">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="form_email">Iteration *</label>
                    <input id="form_email" type="email" name="email" class="form-control" placeholder="Set your iteration cycle in seconds *" required="required" data-error="Valid email is required.">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="form_need">Type of check *</label>
                    <select id="form_need" name="need" class="form-control" required="required" data-error="Please specify your need.">
                      <option value="" selected disabled>--Select Your Issue--</option>
                      <option >get</option>
                      <option >walk</option>
                      <option >shell</option>
                      <option >nrpe</option>
                      <option >template</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label for="form_message">Check Action *</label>
                    <textarea id="form_message" name="message" class="form-control" placeholder="Write your message here." rows="4" required="required" data-error="Please, leave us a message."></textarea>
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

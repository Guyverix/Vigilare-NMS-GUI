<?php

require(__DIR__ . '/../../functions/generalFunctions.php');

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the 'tpw' 'id' 'password' fields are set in the POST data with actual data!
    if (isset($_POST['tpw'], $_POST['id'], $_POST['password']) && ! empty($_POST['tpw']) && ! empty($_POST['id']) && ! empty($_POST['password'])) {
        // Form data to be sent to the remote site
        $postData = [
            'tpw' => $_POST['tpw'],
            'id' => $_POST['id'],
            'password' => $_POST['password'],
        ];
        $callApi = callApiPost('/account/setPassword', $postData);
        $responseCode = $callApi['code'];
        $jsonResponse = json_decode($callApi['response'],1);
        $responseString = $jsonResponse['error']['description'];

        if ( $responseCode != 200) {
          // Anything other than a 200 OK is an issue
          $res = decideResponse($responseCode, $responseString);
        }
        else {
          // return a success with a message
          successMessage($responseString);
          echo '<script>
                  window.setTimeout(function() {
                  window.location = "/login/login.php";
                  }, 4000);
                </script>';
    //          sleep(5);
    //          header('Location: /login/login.php', true, 302);
    //          exit();
        }
    }
    else {
        // Notify that manditory parameters are missing
        if (empty($responseString)) { $responseString = null; }
        loadUnknown($responseString);
    }
}

// This is the initial page load, and we must pull the GET data from the URL provided
if (isset($_GET['tpw']) && isset($_GET['id'])) {
  $tpw=$_GET['tpw'];
  $id=$_GET['id'];
}
else {
  /*
     Hacking attempt, old URL, bored ops guys.  In any case, this would be a legit time
     to say GET STUFFED!  If this page ever loads without these values set(right or wrong)
     we are going to give a go-to-hell response.
  */
  show418();
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="Chris Hubbard" />
        <title>Register - NMS Account Creation</title>
        <link href="../css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>

        <style>
          body {
            background-image: url("../../images/background/background.jpg");
            background-repeat: repeat-n;
            background-size: cover;
          }
       </style>
    </head>
    <body>
        <div id="layoutAuthentication">
            <div id="layoutAuthentication_content">
                <main>
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-5">
                                <div class="card shadow-lg border-0 rounded-lg mt-5">
                                    <div class="card-header"><h3 class="text-center font-weight-light my-4">Create Initial Password</h3></div>
                                    <div class="card-body">
                                        <form action="" method="POST">
                                                <p class="card-text"><center><b>DaRulez:</b></center></p>
                                                <p class="card-text"><center>  Greater than 6 characters.<br></center></p>
                                                <p class="card-text"><center>  One or more special characters on US keyboard.  (Dont get cute)</center></p>
                                                <p class="card-text"><center>  IE: !@#$%^&*() </center></p>
                                            <div class="form-floating mb-3">
                                                <input type="hidden" id="id" name="id" type="text" value="<?php echo $id; ?>" />
                                                <input type="hidden" id="tpw" name="tpw" type="text" value="<?php echo $tpw; ?>" />
                                                <input class="form-control" id="password" name="password" type="password" placeholder="" />
                                                <label for="password">your new password</label>
                                            </div>
                                            <div class="d-flex justify-content-center">
                                                <button type="submit" class="btn btn-primary">Create Password</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
<?php
  // Attempt to standardize across the pages
  include __DIR__ . ("/../shared/footer.php");
?>

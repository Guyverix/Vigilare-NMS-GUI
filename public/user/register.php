<?php
require(__DIR__ . '/../../functions/generalFunctions.php');

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the 'userName' and 'password' fields are set in the POST data with actual data!
    if (isset($_POST['userName'], $_POST['email']) && ! empty($_POST['userName']) && ! empty($_POST['email'])) {
        // Form data to be sent to the remote site
        $postData = [
            'userName' => $_POST['userName'],
            'realName' => $_POST['realName'],
            'email' => $_POST['email']
        ];
        $callApi = callApiPost($postData,'/account/register');
        $responseCode = $callApi['code'];
        $jsonResponse = json_decode($callApi['response'],1);
        $responseString = $jsonResponse['error']['description'];

        if ( $responseCode != 200) {    // Anything other than a 200 OK is an issue
          $res = decideResponse($responseCode, $responseString);
        }
        else {
          showWaitForEmail();
          // After waiting 10 seconds go to login page for the user
          echo '<script>
                  window.setTimeout(function() {
                  window.location = "/login/login.php";
                  }, 10000);
                </script>';
        }
    }
    else {
        loadIncomplete();  // Notify that manditory parameters are missing
    }
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
                                    <div class="card-header"><h3 class="text-center font-weight-light my-4">Create Account</h3></div>
                                    <div class="card-body">
                                        <form action="" method="POST">
                                            <div class="form-floating mb-3">
                                                <input class="form-control" id="userName" name="userName" type="text" placeholder="userName" />
                                                <label for="userName">Your Username</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" id="realName" name="realName" type="text" placeholder="realName" />
                                                <label for="userName">Real Name</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" id="email" name="email" type="email" placeholder="Email" />
                                                <label for="email">email@address.com</label>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                                <button type="submit" class="btn btn-primary">Create Account</button>
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

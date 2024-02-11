<?php
/*
  Lets use PHP to control the POST stuff so we can control
  what happens after the request.  Either send to an error
  page, or load the main page.

  Most of this stuff will get rolled up into functions
  once we have usable pages.
*/
include_once(__DIR__ . '/../../functions/generalFunctions.php');
include(__DIR__ . '/../../config/api.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the 'username' and 'password' fields are set in the POST data with actual data!
    if (isset($_POST['username'], $_POST['password']) && ! empty($_POST['username']) && ! empty($_POST['password'])) {
        // Form data to be sent to the remote site
        $postData = [
            'username' => $_POST['username'],
            'password' => $_POST['password']
        ];
        // URL of the remote site where you want to submit the form
        $remoteUrl = $apiUrl . ':' . $apiPort . '/auth/access_token';

        // Initialize cURL session
        $ch = curl_init($remoteUrl);

        // Set cURL options
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute the cURL request
        $response = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

        // Close cURL session
        curl_close($ch);

        if ( $responseCode >= 400) {
          load4XX();  // If we fail to get auth, notify user with an alert
        }
        else {
          // We are going to set cookies from the response
          $response = json_decode($response, true);
          $expire = strtotime($response['data']['user']['expire'], time());
          $convert = $expire - time();
          $options = array(
            'expires' => time() + $convert,
            'path' => '/',
            'domain' => '',
            'secure' => false,
            'httponly' => false,
            'samesite' => 'Lax'
         );
          setcookie("token", $response['data']['token'] , $options);
          setcookie("id",  $response['data']['user']['id'], $options );
          setcookie("userId", $response['data']['user']['userId'], $options);
          setcookie("email", $response['data']['user']['email'], $options);
          setcookie("realName", $response['data']['user']['realName'], $otions);
          setcookie("apiServer", $response['data']['user']['apiServer'], $options);
          header('Location: /index.php');  // After we have saved the cookies go to main page
        }
    }
    else {
        loadIncomplete();  // Notify that userid or password was not set for post
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
        <meta name="author" content="" />
        <title>Login - NMS Authentication</title>
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
                                    <div class="card-header"><h3 class="text-center font-weight-light my-4">Login</h3></div>
                                    <div class="card-body">
                                        <form action="" method="POST"> 
                                            <div class="form-floating mb-3">
                                                <input class="form-control" id="username" name="username" type="text" placeholder="userName" />
                                                <label for="username">Your User Id</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" id="password" name="password" type="password" placeholder="Password" />
                                                <label for="password">Password</label>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                                <a class="small" href="password.html">Forgot Password? Too bad</a>
                                                <button type="submit" class="btn btn-primary">Login</button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="card-footer text-center py-3">
                                        <div class="small"><a href="register.html">Need an account? Sign up!  Good Luck!</a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
            <div id="layoutAuthentication_footer">
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; Your Website 2023</div>
                            <div>
                                <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
    </body>
</html>

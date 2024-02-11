<?php
/*
  Lets use PHP to control the POST stuff so we can control
  what happens after the request.  Either send to an error
  page, or load the main page.

  Most of this stuff will get rolled up into functions
  once we have usable pages.
*/
include_once(__DIR__ . '/../../functions/generalFunctions.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the 'username' and 'password' fields are set in the POST data with actual data!
    if (isset($_POST['username']) && ! empty($_POST['username'])) {
        // Form data to be sent to the remote site
        $postData = [
            'username' => $_POST['username']
        ];

        $callApi = callApiPost('/account/resetPassword', $postData);
        $responseCode = $callApi['code'];
        $jsonResponse = json_decode($callApi['response'],1);
        $responseString = $jsonResponse['error']['description'];

        if ( $responseCode !== 200) {    // Anything other than a 200 continue is an issue
          decideResponse($responseCode, $responseString );
        }
        else {
         load200("Check your email associated with user name " . $_POST['username']);
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
        <title>Login - NMS Forgot Password</title>
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
                                    <div class="card-header"><h3 class="text-center font-weight-light my-4">Reset your password</h3></div>
                                    <div class="card-body">
                                        <form action="" method="POST"> 
                                            <div class="form-floating mb-3">
                                                <input class="form-control" id="username" name="username" type="text" placeholder="userName" />
                                                <label for="username">Your User Id</label>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                                <button type="submit" class="btn btn-primary">Reset Password</button>
                                            </div>
                                        </form>
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

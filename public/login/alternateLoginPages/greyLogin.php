<!-- Why the hell is this the only way to get a client timezone!??  -->
<!DOCTYPE html>
<META Http-Equiv="Cache-Control" Content="no-cache"/>
<META Http-Equiv="Pragma" Content="no-cache"/>
<META Http-Equiv="Expires" Content="0"/>

<script src="/js/jquery/jquery-1.7.1.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        if("<?php echo $timezone; ?>".length==0){
            var visitortime = new Date();
            var visitortimezone = "GMT " + -visitortime.getTimezoneOffset()/60;
            $.ajax({
                type: "GET",
                url: "/shared/timezone.php",
                data: 'time='+ visitortimezone,
                success: function(){
                // location.reload();
                }
            });
        }
    });
</script>

<?php
/*
  Lets use PHP to control the POST stuff so we can control
  what happens after the request.  Either send to an error
  page, or load the main page.

  Most of this stuff will get rolled up into functions
  once we have usable pages.
*/
include_once(__DIR__ . '/../../functions/generalFunctions.php');

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the 'username' and 'password' fields are set in the POST data with actual data!
    if (isset($_POST['username'], $_POST['password']) && ! empty($_POST['username']) && ! empty($_POST['password']) ) {
        // Form data to be sent to the remote site
        $postData = [
            'username' => $_POST['username'],
            'password' => $_POST['password']
        ];
        $callApi = callApiPost('/auth/access_token', $postData);
        $responseCode = $callApi['code'];
        $jsonResponse = json_decode($callApi['response'],1);
        $responseString = $jsonResponse['error']['description'];

        if ( $responseCode !== 201) {    // Anything other than a 201 continue is an issue
          decideResponse($responseCode, $responseString );
        }
        else {
          // At this point we are going to ATTEMPT to set the local timezone based
          // on what we know
          //if ( ! isset($_SESSION)) {
          //  session_start();
          //}
          session_abort();
          session_start();
          $timezone = $_SESSION['time'];

          // We are going to set cookies from the response
          // This should only happen on a 201, but we are testing now :)
          $response = $jsonResponse;
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
          setcookie("expire",        $expire , $options);
          setcookie("Authorization", $response['data']['token'] , $options);
          setcookie("token",         $response['data']['token'] , $options);
          setcookie("id",            $response['data']['user']['id'], $options );
          setcookie("realName",      $response['data']['user']['realName'], $options);
          setcookie("userId",        $response['data']['user']['userId'], $options);
          setcookie("email",         $response['data']['user']['email'], $options);
          setcookie("apiServer",     $response['data']['user']['apiServer'], $options);
          setcookie("clientTimezone",$timezone, $options);
          // After setting the cookies, show a login success message
          load200('Login successful');
          echo '<script>
                  window.setTimeout(function() {
                  window.location = "/index.php";
                  }, 2000);
                </script>';
          // header('Location: /index.php');  // After we have saved the cookies go to main page
        }
    }
    else {
      load403warn("Login failure.");  // Notify that userid or password was not set for post
      echo '<script> setTimeout(function() { bootstrap.Alert.getOrCreateInstance(document.querySelector(".alert")).close(); }, 3000) </script>';

//        $(document).ready(function() {
        // show the alert
//        setTimeout(function() {
 //         $(".alert").alert("close");
  //      }, 2000);
   //     });

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
          /* https://www.cssmatic.com/gradient-generator */
          body {
            background: rgba(115,115,115,1);
            background: -moz-linear-gradient(top, rgba(115,115,115,1) 15%, rgba(110,110,110,1) 34%, rgba(109,109,109,1) 42%, rgba(105,105,105,0.79) 61%, rgba(128,128,128,0.58) 79%, rgba(128,128,128,0.54) 83%);
            background: -webkit-gradient(left top, left bottom, color-stop(15%, rgba(115,115,115,1)), color-stop(34%, rgba(110,110,110,1)), color-stop(42%, rgba(109,109,109,1)), color-stop(61%, rgba(105,105,105,0.79)), color-stop(79%, rgba(128,128,128,0.58)), color-stop(83%, rgba(128,128,128,0.54)));
            background: -webkit-linear-gradient(top, rgba(115,115,115,1) 15%, rgba(110,110,110,1) 34%, rgba(109,109,109,1) 42%, rgba(105,105,105,0.79) 61%, rgba(128,128,128,0.58) 79%, rgba(128,128,128,0.54) 83%);
            background: -o-linear-gradient(top, rgba(115,115,115,1) 15%, rgba(110,110,110,1) 34%, rgba(109,109,109,1) 42%, rgba(105,105,105,0.79) 61%, rgba(128,128,128,0.58) 79%, rgba(128,128,128,0.54) 83%);
            background: -ms-linear-gradient(top, rgba(115,115,115,1) 15%, rgba(110,110,110,1) 34%, rgba(109,109,109,1) 42%, rgba(105,105,105,0.79) 61%, rgba(128,128,128,0.58) 79%, rgba(128,128,128,0.54) 83%);
            background: linear-gradient(to bottom, rgba(115,115,115,1) 15%, rgba(110,110,110,1) 34%, rgba(109,109,109,1) 42%, rgba(105,105,105,0.79) 61%, rgba(128,128,128,0.58) 79%, rgba(128,128,128,0.54) 83%);
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#737373', endColorstr='#808080', GradientType=0 );
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
                                    <div class="card-header"><h3 class="text-center font-weight-light my-4">Vigilare Login</h3></div>
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

<?php
  // Attempt to standardize across the pages
  include __DIR__ . ("/../shared/bottomFooter.php");
?>

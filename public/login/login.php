<?php
// -------------------- server-side prelude --------------------
include_once(__DIR__ . '/../../functions/generalFunctions.php');

// Resolve theme from cookie (fallback to dark)
$theme = $_COOKIE['theme'] ?? 'darl';
if ($theme !== 'dark' && $theme !== 'light') {
    $theme = 'dark';
}

// Handle POST (unchanged logic, just tidied a bit)
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['username'], $_POST['password']) && $_POST['username'] !== '' && $_POST['password'] !== '') {

        $postData = [
            'username' => $_POST['username'],
            'password' => $_POST['password']
        ];

        $callApi       = callApiPost('/auth/access_token', $postData);
        $responseCode  = $callApi['code'] ?? 0;
        $jsonResponse  = json_decode($callApi['response'] ?? '', true) ?: [];
        $responseString = $jsonResponse['error']['description'] ?? 'Unknown error';

        if ($responseCode !== 201) {
            decideResponse($responseCode, $responseString);
        } else {
            // Session/timezone handling (kept as in your original)
            session_abort();
            session_start();
            $timezone = $_SESSION['time'] ?? '';

            // Set cookies from API response
            $response = $jsonResponse;
            $expireTs = strtotime($response['data']['user']['expire'] ?? '+2 hours', time());
            $ttl      = max(0, $expireTs - time());
            $options = [
                'expires'  => time() + $ttl,
                'path'     => '/',
                'domain'   => '',
                'secure'   => false,
                'httponly' => false,
                'samesite' => 'Lax'
            ];

            setcookie('expire',        $expireTs,                            $options);
            setcookie('Authorization', $response['data']['token'] ?? '',     $options);
            setcookie('token',         $response['data']['token'] ?? '',     $options);
            setcookie('id',            $response['data']['user']['id'] ?? '',           $options);
            setcookie('realName',      $response['data']['user']['realName'] ?? '',     $options);
            setcookie('userId',        $response['data']['user']['userId'] ?? '',       $options);
            setcookie('email',         $response['data']['user']['email'] ?? '',        $options);
            setcookie('apiServer',     $response['data']['user']['apiServer'] ?? '',    $options);

            // Theme + timezone
            setcookie('theme',          $theme,     $options);
            setcookie('clientTimezone', $timezone,  $options);

            loadCenteredSuccess('success');
            echo '<script>setTimeout(function(){ window.location="/index.php"; }, 1200);</script>';
        }
    } else {
        load403warn("Login failure.");
        echo '<script>
          setTimeout(function(){
            var el=document.querySelector(".alert");
            if(el){var bsAlert=bootstrap.Alert.getOrCreateInstance(el); bsAlert.close();}
          }, 3000);
        </script>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Basic meta -->
  <meta charset="utf-8" />
  <meta http-equiv="Cache-Control" content="no-cache"/>
  <meta http-equiv="Pragma" content="no-cache"/>
  <meta http-equiv="Expires" content="0"/>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Vigilare Authentication</title>

  <!-- Bootstrap CSS (local copy as per your setup) -->
  <link id="bootstrap-css" rel="stylesheet" href="/css/bootstrap/bootstrap.min.css">

  <!-- Theme CSS via cookie-based subdir -->
  <link id="<?php echo $theme; ?>-theme-css" rel="stylesheet" href="/css/<?php echo $theme; ?>/vigilare-dashboard.css">

  <!-- Optional: your login page extras (if you still want them) -->
  <link rel="stylesheet" href="/login/animatedLogin.css">

  <!-- (Optional) Font Awesome; remove if unused -->
  <!-- <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script> -->
</head>
<body class="d-flex flex-column min-vh-100">

  <main class="d-flex flex-grow-1 justify-content-center align-items-center">
    <div class="container">
    <div class="row justify-content-center">
      <div class="col-sm-10 col-md-8 col-lg-6 col-xl-5">
        <div class="card shadow border-0 rounded-3">
          <div class="card-header text-center">
            <h3 class="my-2 mb-0 fw-semibold">Vigilare Login</h3>
          </div>
          <div class="card-body p-4">
            <form action="" method="POST" novalidate>
              <!-- Username -->
              <div class="mb-3">
                <label for="username" class="form-label">User ID</label>
                <input
                  type="text"
                  class="form-control"
                  id="username"
                  name="username"
                  placeholder="userid"
                  required
                  autocomplete="username"
                  inputmode="email"
                >
              </div>

              <!-- Password -->
              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input
                  type="password"
                  class="form-control"
                  id="password"
                  name="password"
                  placeholder="••••••••"
                  required
                  autocomplete="current-password"
                >
              </div>

              <div class="d-flex align-items-center justify-content-between">
                <a class="small" href="/password.html">Forgot Password?</a>
                <button type="submit" class="btn btn-primary">Login</button>
              </div>
            </form>
          </div>
          <div class="card-footer text-center py-3">
            <div class="small">
              <a href="/register.html">Need an account? Sign up!</a>
            </div>
          </div>
        </div>
        </div>
      </div>
    </div>
  </main>

  <?php include __DIR__ . ("/includes/bottomFooter.php"); ?>

  <!-- Bootstrap JS (local) -->
<!--  <script src="/js/bootstrap-5/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>   -->

  <!-- Minimal vanilla JS timezone ping (replaces old jQuery) -->
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // If PHP didn't already have a timezone, send client's offset (kept from your original behavior)
      var phpTimezone = "<?php echo isset($timezone) ? (string)$timezone : ''; ?>";
      if (!phpTimezone) {
        var visitortime = new Date();
        var visitortimezone = "GMT " + (-visitortime.getTimezoneOffset()/60);
        try {
          // GET request to /shared/timezone.php?time=...
          var url = "/shared/timezone.php?time=" + encodeURIComponent(visitortimezone);
          // keepalive so it won't block navigation if user submits quickly
          fetch(url, { method: "GET", mode: "same-origin", keepalive: true })
            .catch(function(){ /* no-op */ });
        } catch(e) { /* no-op */ }
      }
    });
  </script>
</body>
</html>

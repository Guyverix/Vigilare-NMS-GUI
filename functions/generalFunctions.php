
<?php
/*
  This is the beginnings of the utilities functions for the frontend UI.
  All of the generic or boilerplate functions should go here.
*/

require __DIR__ . ("/../config/api.php");


/**
 * ---- Helpers ----
 */

/**
 * Accepts many possible shapes and returns a numerically indexed list of events.
 * - $raw can be an array with 'response' (string or array) or a raw JSON string, etc.
 * - Ensures the final return is an array of associative event arrays.
 */
function extractEventsList($raw): array {
    // If the whole payload is a JSON string, decode it
    if (is_string($raw)) {
        $raw = json_decode($raw, true) ?? [];
    }

    // If there is a 'response' key, it might be a JSON string or an array
    if (isset($raw['response'])) {
        $resp = $raw['response'];
        if (is_string($resp)) {
            $resp = json_decode($resp, true) ?? [];
        }
        // Prefer fields from decoded response; keep originals as fallback
        $raw = array_merge($raw, $resp);
    }

    // Some clients use 'body' instead of 'response'
    if (isset($raw['body'])) {
        $body = $raw['body'];
        if (is_string($body)) {
            $body = json_decode($body, true) ?? [];
        }
        $raw = array_merge($raw, $body);
    }

    // Now pull out 'data' if present, else assume $raw is the list/object
    $data = $raw['data'] ?? $raw;

    // If 'data' is still a JSON string, decode it
    if (is_string($data)) {
        $decoded = json_decode($data, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $data = $decoded;
        } else {
            $data = [];
        }
    }

    // Ensure we return a numerically indexed list
    if (!is_array($data)) {
        return [];
    }
    $isList = array_keys($data) === range(0, count($data) - 1);
    return $isList ? $data : [$data];
}

// Format timestamp down to "YYYY-MM-DD HH:MM"
function formatEventTime(?string $ts): string {
    if (!$ts || $ts === '0000-00-00 00:00:00') {
        return '';
    }
    $dt = DateTime::createFromFormat('Y-m-d H:i:s', $ts);
    return $dt ? $dt->format('Y-m-d H:i') : $ts; // fallback to raw string
}

// See if we have a JWT token and if not send to login page
function checkCookie($COOKIE) {
  if ( ! isset($COOKIE['token'])) { $COOKIE['token'] = ''; }
  $jwt = $COOKIE['token'];
  $loginUrl='/login/login.php';

  if (empty($jwt)) {
    header("HTTP/1.1 301 Moved Permanently");
    header("Cache-Control: max-age=0,no-cache,no-store,post-check=0,pre-check=0");
    header("Location: " . $loginUrl);
    die();
  }
  else {
    return 0;
  }
}

function checkTimer($COOKIE) {
  // Nice to have, but does not break if missing
  if (! isset($COOKIE['expire'])) {
    return 0;
  }
  else {
    $lifeTime = $COOKIE['expire'];
    $timeNow = time();
    $timeLook = $timeNow + 3600;
    if ( $timeLook > $lifeTime ) {
     //   loadWarning("now: " . $timeNow . " future " . $timeLook . " expires " . $lifeTime);
     loadWarning("<br><br>Your login session ends in less than 60 minutes");
    }
  }
}

function setCookieSimple($name, $value, $storePath,  $timer = null) {
  // timer is in seconds..
  if (is_null($timer)) { $timer = 86400; }
  $convert = $timer;
  $options = array(
    'expires' => time() + $convert,
    'path' => $storePath,
    'domain' => '',
    'secure' => false,
    'httponly' => false,
    'samesite' => 'Lax'
  );
  setcookie($name, $value, $options);
}


/*
  The following few pages are actually going to be closer to toast or flash
  as we do not know always if we have the access for stuff we are asking for
*/

// A generic authentication error page.  This does NOT account for
// authenticated but not enough access for what they tried to do
function load400($message){
  if ( $message == '' ) { $message = "Bad Request sent"; }
  echo '<div class="alert alert-danger" role="alert"><center>' . $message . '</center></div>';
}

function load200($message){
  if ( $message == '' ) { $message = "success"; }
  echo '<div class="alert alert-info" role="alert"><center>' . $message . '</center></div>';
}

function load401($message){
  if ( $message == '' ) { $message = "Username or password is incorrect."; }
  echo '<div class="alert alert-danger" role="alert"><center>' . $message . '</center></div>';
}

function load405($message){
  if ( $message == '' ) { $message = "Method Not Allowed"; }
  echo '<div class="alert alert-danger" role="alert"><center>' . $message . '</center></div>';
}

function load451($message){
  if ( $message == '' ) { $message = "Unavailable due to access level."; } // 451 is UA due to legal reasons.  Not enough access fits this logic
  echo '<div class="alert alert-danger" role="alert"><center>' . $message . '</center></div>';
}

function load4XX() {
  if ( $message == '' ) { $message = "Call returned a 4XX response code from the API.  Access is not enough, or token has expired."; }
  echo '<div class="alert alert-danger" role="alert"><center>' . $message . '</center></div>';
}

function loadIncomplete($message) {
  if ( $message == '' ) { $message = "Missing part of your credentials or manditory parameters."; }
  echo '<div class="alert alert-danger" role="alert"><center>' . $message . '</center></div>';
}

// A special page related to API testing.  Regular users should not hit
// this unless they are doing weird things
function load418($message) {
  if ( $message == '' ) { $message = "Call returned a 418 response from the API.  A call was done for something that the UI would not normally call."; }
  echo '<div class="alert alert-warning" role="alert"><center>' . $message . '</center></div>';
}

// A general 5XX error page
function load5XX($message) {
  if ( $message == '' ) { $message = "Call returned a 5XX ISE response code from the API.  Attempt to note the call, and any additional information if this is seen."; }
  echo '<div class="alert alert-info" role="alert"><center>' . $message . '</center></div>';
}

// This will ahve special meaning.  In general a 666 response code is a known
// edge case that is a PITA to track down.
function load666($message) {
  if ( $message == '' ) { $message = "Call returned a 666 response from the API.  Start pulling the API server logs if this is seen.  Someone did something stupid!"; }
  echo '<div class="alert alert-primary" role="alert"><center>' . $message . '</center></div>';
}

function loadUnknown($message) {
  if ( $message == '' ) { $message = "Unexpected response from API.  Note path called for investigation."; }
//  echo '<div class="alert alert-primary" role="alert"><center>' . $message . '</center></div>';
  echo '<div class="container-sm">';
  echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
  echo '<center><i class="fas fa-exclamation-triangle fa-lg"></i>';
  if ( is_array($message)) {
    echo "  " . print_r($message, true);
  }
  else {
    echo "  " . $message;
  }
  echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><span aria-hidden="true"></span></button>';
  echo '</center>';
  echo '</div></div>';

}

function loadCenteredSuccess($message){
  if ( $message == '' ) { $message = "success"; }
  echo '<div id="loginSuccess" class="alert alert-success text-center ribbon-alert" role="alert">';
  echo '  Login ' . $message . ' — redirecting…';
  echo '</div>';
  echo '
<style>
.ribbon-alert {
  position: fixed;
  top: 50%;
  left: 0;
  right: 0;
  transform: translateY(-50%);  /* center vertically */
  z-index: 1055;               /* above card/backdrop */
  border-radius: 0;             /* full-width strip */
  background-color: var(--bs-success) !important;  /* sets as a transparent look, dont use for login ribbon */
  color: var(--bs-white) !important;
  background-color: #198754 !important; /* solid Bootstrap "success" green */
  opacity: 1;
}
</style>';
}


function loadWarning($message) {
  echo '<div class="alert alert-primary" role="alert"><center>' . $message . '</center></div>';
}

function load403() {  // An absolute login required
  $loginUrl='/login/login.php';
  header("HTTP/1.1 301 Moved Permanently");
  header("Cache-Control: max-age=0,no-cache,no-store,post-check=0,pre-check=0");
  header("Location: " . $loginUrl);
  die();
}

function load403Warn($message) {
  if ( $message == '' ) { $message = "Expired credentials"; }
  echo '<div class="alert alert-primary" role="alert"><center>' . $message . '</center></div>';
}

function show404() {
  header("HTTP/1.0 404 Not Found");
  //include_once __DIR__ . "/errors/404.php";
  readfile( __DIR__ . "/../public/error/404.html");
  exit();
}

function show418() {
  header("HTTP/1.0 418 I am a teapot");
  //include_once __DIR__ . "/errors/418.php";
  readfile (__DIR__ . "/../public/error/418.html");
  exit();
}

function showWaitForEmail() {
  echo '<div class="alert alert-info" role="alert"><center>Check your email to continue</center></div>';
}

// Nice, has a check img, and a dismiss for the alert
function successMessage($message) {
  if ( $message == '' || empty($message) ) { $message = 'success'; }
  echo '<div class="container-sm">';
  echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
  echo '<center><i class="fas fa-check-circle fa-lg"></i>';
  echo "  " . $message;
  echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><span aria-hidden="true"></span></button>';
  echo '</center>';
  echo '</div></div>';
}


// Generic way to decide if we need an alert shown on the page
// https://getbootstrap.com/docs/4.0/components/alerts/
function decideResponse($responseCode, $message = null) {
  if ( is_null($message)) { $message=''; }
  switch ($responseCode) {
    case ($responseCode == 400) !== false:
      load400($message);
      break;
    case ($responseCode == 401) !== false:
      load401($message);
      break;
    case ($responseCode == 403) !== false:
      load403($message);
      break;
    case ($responseCode == 404) !== false:
      show404();
      break;
    case ($responseCode == 405) !== false:
      load405($message);
      break;
    case ($responseCode == 418) !== false:
      load418($message);
      break;
    case ($responseCode == 666) !== false:
      load666($message);
      break;
    case ($responseCode <= 499) && ($responseCode >= 400) !== false:
      load4XX($message);
      break;
    case ($responseCode <= 599) && ($responseCode >= 500) !== false:
      load5XX($message);
      break;
    default:
      loadUnknown($message); // catchall for unexpected stuff
      break;
  }
}

/*
  Generic functions to call the API server.  Note that headers SHOULD be used, but
  may not always be necessary (IE when user is not logged in)
*/


//function callApiPost(array $postData, string $remotePath, array $headers = null) {
function callApiPost(string $remotePath, array $postData, array $headers = null) {
  $result = array();
  // All of these are from the require at the top
  global $apiHostname;
  global $apiPort;
  global $apiUrl;
  global $apiHttp;
  //  $remoteUrl = $apiUrl;
  $remoteUrl = $apiHttp . $apiHostname . ":" . $apiPort . $remotePath;
  $ch = curl_init($remoteUrl);
  if ( ! is_null($headers)) {
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
  }
  curl_setopt($ch, CURLOPT_POST, true);
  //curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
  curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $result['response'] = curl_exec($ch);
  $result['code'] = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
  $result['info'] = curl_getinfo($ch);                                           // Mainly useful for debugging
  $result['debug']['remoteUrl'] = $remoteUrl;
  $result['debug']['headers'] = $headers;
  $result['debug']['postData'] = $postData;
  //$result['debug']['buildQuery'] = http_build_query($postData);
  curl_close($ch);
  return $result;
}

function callApiGet($remotePath, $headers = null) {
  $result = array();
  // All of these are from the require at the top
  global $apiHostname;
  global $apiPort;
  global $apiUrl;
  global $apiHttp;
  $remoteUrl = $apiHttp . $apiHostname . ":" . $apiPort . $remotePath;
  $ch = curl_init($remoteUrl);
  if ( ! is_null($headers)) {
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
  }
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $result['response'] = curl_exec($ch);
  $result['code'] = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
  $result['info'] = curl_getinfo($ch);                                           // Mainly useful for debugging
  $result['debug']['remoteUrl'] = $remoteUrl;
  $result['debug']['headers'] = $headers;
  curl_close($ch);
  return $result;
}

function callUrlGet($remoteUrl, $headers = null) {
  $result = array();
  $ch = curl_init($remoteUrl);
  if ( ! is_null($headers)) {
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
  }
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $result['response'] = curl_exec($ch);
  $result['code'] = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
  $result['info'] = curl_getinfo($ch);                                           // Mainly useful for debugging
  $result['debug']['remoteUrl'] = $remoteUrl;
  $result['debug']['headers'] = $headers;
  curl_close($ch);
  return $result;
}

function callUrlPost( string $remoteUrl, array $postData, array $headers = null) {
  $ch = curl_init($remoteUrl);
  if ( ! is_null($headers)) {
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
  }
  curl_setopt($ch, CURLOPT_POST, true);
  //curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
  curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $result['response'] = curl_exec($ch);
  $result['code'] = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
  $result['info'] = curl_getinfo($ch);                                           // Mainly useful for debugging
  $result['debug']['remoteUrl'] = $remoteUrl;
  $result['debug']['headers'] = $headers;
  $result['debug']['postData'] = $postData;
  //$result['debug']['buildQuery'] = http_build_query($postData);
  curl_close($ch);
  return $result;
}




// https://stackoverflow.com/questions/4315271/how-to-pass-arguments-to-an-included-file
function includeHead($title) {
  // This will ONLY contain $title for the include, nothing else from the page
  include __DIR__ . ("/../public/shared/head.php");
}

// Dump whatever array we are given for debugging
function debugger($values) {
  echo "<pre>";
  echo "print_r result\n " . print_r($values, true);
  echo "\n";
  echo "var_dump result\n ";
  var_dump($values);
  echo "</pre>";
}

function showModal($title, $body) {
echo '<div class="modal" tabindex="-1" id="deviceGroupModal">';
echo '  <div class="modal-dialog">';
echo '    <div class="modal-content">';
echo '      <div class="modal-header">';
echo '        <h5 class="modal-title">' . $title . '</h5>';
echo '        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
echo '      </div>';
echo '      <div class="modal-body">';
echo '        <p>' .  $body . '</p>';
echo '      </div>';
echo '      <div class="modal-footer">';
echo '      </div>';
echo '    </div>';
echo '  </div>';
echo '</div>';
}





?>


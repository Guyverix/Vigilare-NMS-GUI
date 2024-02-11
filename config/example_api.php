<?php

/*
  For use in building GUI URL's etc..
  If you are using haproxy or something like that
  this is where you would put the proxy URL
  Things like login need to be explict.  Other
  URL's can be a little more loosy-goosey.
*/
$uiHostname="FQDN of GUI server";
$uiUrl="https://" . $uiHostname;                      // Strongly discourage use of http for GUI
$uiPort=443;


/*
  Set initial values for talking to the API server.

  Do NOT EVER use localhost or 127.0.0.1 for the API.
  CORS crap will make you cry.
  And we will not even talk about SSL headaches from it.
*/

$apiHttp='https://';                                  // valid to make http as well
$apiHostname="FQDN of API server";                    // Caution, DNS failures will make this choke, add to /etc/hosts if reasonable to do so
$apiUrl= $apiHttp . $apiHostname;                     // Complete URL minus port number
$apiPort=8002;                                        // Port API is listening on.  Do not use less than 1024 due to scanners

/*
  These should not be needed for V1
  V2 may have the ability to "bless" UI servers
  for access to the API server as a first line
  of defence against attacks.

$apiKey ='1324-deaf-5689-dead-0123';                  // Created with uuidgen :)  Should match api key in api if same host
$apiUser="apiUser";                                   // Alternate auth if key is borked or redis oos (maybe?)
$apiPass="apiPass";                                   // alternate auth if key is borked or redis oos (maybe?)
*/

?>

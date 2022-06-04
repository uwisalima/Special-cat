<?php
if (!session_id()) {
    session_start();
}

require_once 'OAuth/Github_OAuth_Client.php';

//Configuration and setup GitHub API
$clientID         = 'ebe932fa743a97612eb5';
$clientSecret     = 'a2c20cf4ad10444607a8c9c9b7b2312d60f83df6';
$redirectURL     = 'http://localhost/special-cat/index.php';

$gitClient = new Github_OAuth_Client(array(
    'client_id' => $clientID,
    'client_secret' => $clientSecret,
    'redirect_uri' => $redirectURL,
));


// Try to get the access token
if (isset($_SESSION['access_token'])) {
    $accessToken = $_SESSION['access_token'];
}

<?php
require_once 'gitConfig.php';

require_once 'User.class.php';

$user = new User();

if (isset($accessToken)) {
    // Get the user profile info from Github
    $gitUser = $gitClient->apiRequest($accessToken);

    if (!empty($gitUser)) {
        // User profile data
        $gitUserData = array();
        $gitUserData['oauth_provider'] = 'github';
        $gitUserData['oauth_uid'] = !empty($gitUser->id) ? $gitUser->id : '';
        $gitUserData['name'] = !empty($gitUser->name) ? $gitUser->name : '';
        $gitUserData['username'] = !empty($gitUser->login) ? $gitUser->login : '';
        $gitUserData['email'] = !empty($gitUser->email) ? $gitUser->email : '';
        $gitUserData['location'] = !empty($gitUser->location) ? $gitUser->location : '';
        $gitUserData['picture'] = !empty($gitUser->avatar_url) ? $gitUser->avatar_url : '';
        $gitUserData['link'] = !empty($gitUser->html_url) ? $gitUser->html_url : '';

        // Insert or update user data to the database
        $userData = $user->checkUser($gitUserData);

        // Put user data into the session
        $_SESSION['userData'] = $userData;

        // Render Github profile data
        $output  = '<h2>Github Profile Details</h2>';
        $output .= '<img src="' . $userData['picture'] . '" />';
        $output .= '<p>ID: ' . $userData['oauth_uid'] . '</p>';
        $output .= '<p>Name: ' . $userData['name'] . '</p>';
        $output .= '<p>Login Username: ' . $userData['username'] . '</p>';
        $output .= '<p>Email: ' . $userData['email'] . '</p>';
        $output .= '<p>Location: ' . $userData['location'] . '</p>';
        $output .= '<p>Profile Link :  <a href="' . $userData['link'] . '" target="_blank">Click to visit GitHub page</a></p>';
        $output .= '<p>Logout from <a href="logout.php">GitHub</a></p>';
    } else {
        $output = '<h3 style="color:red">Some problem occurred, please try again.</h3>';
    }
} elseif (isset($_GET['code'])) {
    // Verify the state matches the stored state
    if (!$_GET['state'] || $_SESSION['state'] != $_GET['state']) {
        header("Location: " . $_SERVER['PHP_SELF']);
    }

    // Exchange the auth code for a token
    $accessToken = $gitClient->getAccessToken($_GET['state'], $_GET['code']);

    $_SESSION['access_token'] = $accessToken;

    header('Location: ./');
} else {
    // Generate a random hash and store in the session for security
    $_SESSION['state'] = hash('sha256', microtime(TRUE) . rand() . $_SERVER['REMOTE_ADDR']);

    // Remove access token from the session
    unset($_SESSION['access_token']);

    // Get the URL to authorize
    $loginURL = $gitClient->getAuthorizeURL($_SESSION['state']);

    // Render Github login button
    $output = '<a href="' . htmlspecialchars($loginURL) . '">

        <div class="bg-dark text-white d-flex ps-5 position-relative rounded mx-5 mt-5 mt-5 px-3 pt-2 border-2 shadow" style="top: 40px; text-decoration: none;">
            <i class="fab fa-github text-white position-absolute" style="left: 12px; top: 8px; font-size: 23px;" aria-hidden="true"></i>
            <h6>Continue with GitHub</h6>
        </div>

    </a>';
}
?>

<!DOCTYPE html>
<html lang="en-US">

<head>
    <title>Login with GitHub using PHP by CodexWorld</title>
    <meta charset="utf-8">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
    <link rel="stylesheet" href="bootstrap/js/bootstrap.min.js">
    <!-- Latest compiled and minified CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Latest compiled JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>

    <div class="bg-dark text-white text-center py-1">
        <h6>IREMBO | Special CAT</h6>
    </div>

    <div class="body text-center my-5 mx-5" style="display: grid; place-items: center;">
        <div class="card border-2 shadow" style="height: 400px;">
            <div class="card-body">
                <h6 class="pb-5">Connect to IREMBO</h6>
                <div class="container">
                    <!-- Display GitHub profile information -->
                    <div class="wrapper"><?php echo $output; ?></div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
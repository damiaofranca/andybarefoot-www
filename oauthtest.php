<?php
if (isset($_GET["error"]))
{
    echo("<pre>OAuth Error: " . $_GET["error"]."\n");
    echo('<a href="oauthtest.php">Retry</a></pre>');
    die;
}

$authorizeUrl = 'https://accounts.google.com/o/oauth2/auth';
$accessTokenUrl = 'https://accounts.google.com/o/oauth2/token';
$clientId = '106686193515140242276';
$clientSecret = 'AIzaSyAePBRf0_ur64H8Z0T6HWuHF47RGn7in9A';
$userAgent = 'ChangeMeClient/0.1 by YourUsername';

$redirectUrl = "REDIRECT_URI";

require("../includes/OAuth2/Client.php");
require("../includes/OAuth2/GrantType/IGrantType.php");
require("../includes/OAuth2/GrantType/AuthorizationCode.php");

$client = new OAuth2\Client($clientId, $clientSecret, OAuth2\Client::AUTH_TYPE_AUTHORIZATION_BASIC);
$client->setCurlOption(CURLOPT_USERAGENT,$userAgent);

if (!isset($_GET["code"]))
{
    $authUrl = $client->getAuthenticationUrl($authorizeUrl, $redirectUrl, array("scope" => "identity", "state" => "SomeUnguessableValue"));
    header("Location: ".$authUrl);
    die("Redirect");
}
else
{
    $params = array("code" => $_GET["code"], "redirect_uri" => $redirectUrl);
    $response = $client->getAccessToken($accessTokenUrl, "authorization_code", $params);

    $accessTokenResult = $response["result"];
    $client->setAccessToken($accessTokenResult["access_token"]);
    $client->setAccessTokenType(OAuth2\Client::ACCESS_TOKEN_BEARER);

    $response = $client->fetch("https://oauth.reddit.com/api/v1/me.json");

    echo('<strong>Response for fetch me.json:</strong><pre>');
    print_r($response);
    echo('</pre>');
}
?>
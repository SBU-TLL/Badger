<?php
require("credlyAPI.php");

// Credentials come from the runtime environment, never hardcoded.
$userInfo = (object) array(
    "email"    => getenv('CREDLY_ACCOUNT_EMAIL')    ?: '',
    "password" => getenv('CREDLY_ACCOUNT_PASSWORD') ?: '',
);

getToken($userInfo);

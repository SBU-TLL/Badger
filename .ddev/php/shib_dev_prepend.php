<?php
// Hardened local-dev Shibboleth auth mock for the Badger-family apps. Fenced: under
// .ddev/ (not deployed), fail-closed unless APP_ENV=local. Seeds $_SERVER attrs only when absent.
$appEnv = getenv('APP_ENV'); if ($appEnv === false || $appEnv === '') { $appEnv = $_SERVER['APP_ENV'] ?? 'production'; }
if (!in_array(strtolower($appEnv), ['local','development','dev'], true)) { return; }
$mock = ['cn'=>getenv('MOCK_CN')?:'teststudent','mail'=>getenv('MOCK_MAIL')?:'teststudent@stonybrook.edu','nickname'=>getenv('MOCK_NICKNAME')?:'Test','sn'=>getenv('MOCK_SN')?:'Student'];
foreach ($mock as $k=>$v){ if (empty($_SERVER[$k])) { $_SERVER[$k]=$v; } }

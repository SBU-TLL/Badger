<?
require("credlyAPI.php");

$DataRaw = <<<EOT
{
	
    "email": "digitalbadging@connect.stonybrook.edu",
    "password":"d1g1t@lb2dg3s"
}
EOT;
//$DataRaw = <<<EOT
//{

//    "email": "paul.st.denis+celt@stonybrook.edu",
//    "password":"0stric4e$"
//}
//EOT;


$userInfo =json_decode($DataRaw);



getToken($userInfo);

?>



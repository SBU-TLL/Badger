<?
require("credlyAPI.php");




$DataRaw = <<<EOT
{
	
    "email": "${_SERVER['mail']}",
	"first_name":"${_SERVER['nickname']}",
    "last_name":"${_SERVER['sn']}",
    "badge_id":${_GET['badgeID']},
    "behalf_of":2326858
}
EOT;




$userInfo =json_decode($DataRaw);

$null= rewardBadge($userInfo);

//header("Location: https://stonybrookuniversity.credly.com/my-inbox");


?>
Temporary Text, Check your mail, you should have something from credly...


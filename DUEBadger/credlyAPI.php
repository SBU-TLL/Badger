<?php
$domain       = getenv('CREDLY_API_DOMAIN')   ?: 'https://api.credly.com/v1.1';
$APIKey       = getenv('CREDLY_API_KEY')       ?: '';
$APISecret    = getenv('CREDLY_API_SECRET')    ?: '';
$access_token = getenv('CREDLY_ACCESS_TOKEN')  ?: '';
$app_id       = getenv('CREDLY_APP_ID')        ?: '';

function createBadge($app_id,$image,$title){
global $domain,$APIKey,$APISecret,$access_token;    
$headerDataRaw ="X-Api-Key:$APIKey\r\nX-Api-Secret:$APISecret\r\nContent-type:application/x-www-form-urlencoded"; 


#"app_id":$app_id,	
$postDataRaw = <<<EOT
{
    "attachment":"$image",
    "title":"$title",
     "expires_in":"0",
"app_id":693
}
EOT;
 
  $postdata =json_decode($postDataRaw);
#$getdata="access_token=$access_token";  
$getdata="behalf_of=2326858&access_token=$access_token";  
    
print_r(credlyAPI("/badges", $headerDataRaw,$postdata,$getdata));    
}


function getToken($userInfo){

global $domain,$APIKey,$APISecret,$access_token;    
$email= $userInfo->email;
$password= $userInfo->password;
$postDataRaw = <<<EOT
{
}
EOT;
$user=base64_encode("$email:$password");
$headerDataRaw ="X-Api-Key:$APIKey\r\nX-Api-Secret:$APISecret\r\nAuthorization: Basic $user\r\nContent-type:application/x-www-form-urlencoded";
$postdata =json_decode($postDataRaw);
print_r($postdata);

$getdata="";
print_r(credlyAPI("/authenticate", $headerDataRaw,$postdata,$getdata));


}


function modifyBadge($userInfo){

global $domain,$APIKey,$APISecret,$access_token;    
$headerDataRaw ="X-Api-Key:$APIKey\r\nX-Api-Secret:$APISecret\r\nContent-type:application/x-www-form-urlencoded";

$email= $userInfo->email;
$first_name= $userInfo->first_name;
$last_name = $userInfo->last_name;
$badge_id =$userInfo->badge_id;

$postDataRaw = <<<EOT
{   
    "email": "$email",
        "first_name":"$first_name",
    "last_name":"$last_name",
    "badge_id":"$badge_id"
}
EOT;


$postdata =json_decode($postDataRaw);
$getdata="access_token=$access_token";

print_r(credlyAPI("/member_badges", $headerDataRaw,$postdata,$getdata));
}



function rewardBadge($userInfo){

global $domain,$APIKey,$APISecret,$access_token;    
$headerDataRaw ="X-Api-Key:$APIKey\r\nX-Api-Secret:$APISecret\r\nContent-type:application/x-www-form-urlencoded";
$email= $userInfo->email;
$first_name= $userInfo->first_name;
$last_name = $userInfo->last_name;
$badge_id =$userInfo->badge_id;
$behalf_of=$userInfo->behalf_of;
$postDataRaw = <<<EOT
{
    "email": "$email",
	"first_name":"$first_name",
    "last_name":"$last_name",
    "badge_id":"$badge_id"
}
EOT;


$postdata =json_decode($postDataRaw);
$getdata="access_token=$access_token&behalf_of=$behalf_of";
#$getdata="access_token=$access_token";
   
  return credlyAPI("/member_badges", $headerDataRaw,$postdata,$getdata);
}


function getManagers(){
global $domain,$APIKey,$APISecret,$access_token;
$postdata=array();
$headerDataRaw ="X-Api-Key:$APIKey\r\nX-Api-Secret:$APISecret\r\nContent-type:application/x-www-form-urlencoded";
$getdata="";
global $domain,$APIKey,$APISecret,$access_token;    
 return credlyAPI("/me/managers", $headerDataRaw,$postdata,$getdata);

}

function getBadges(){
global $domain,$APIKey,$APISecret,$access_token;
$postdata=array();
$headerDataRaw ="X-Api-Key:$APIKey\r\nX-Api-Secret:$APISecret\r\nContent-type:application/x-www-form-urlencoded";
$getdata="per_page=1000&access_token=$access_token&member_id=2326858";
return credlyAPI("/badges", $headerDataRaw,$postdata,$getdata);

}




function  credlyAPI($path, $header_data,$post_data,$get_data){

global $domain;    
$opts = array('http' =>
    array(
        'method'  => 'POST',
        'header'  => $header_data,
        'content' => http_build_query($post_data),
	'timeout' => 60
    )
);
$url ="$domain$path?".$get_data;

if(count($post_data)==0){
$opts = array('http' =>
    array(
        'method'  => 'GET',
        'header'  => $header_data,
        'timeout' => 60
    )
);

}   

$context = stream_context_create($opts);  
$results = @file_get_contents($url, false, $context,-1,40000); 
return $results;


}

function getHttpCode($http_response_header)
{
    if(is_array($http_response_header))
    {
        $parts=explode(' ',$http_response_header[0]);
        if(count($parts)>1) //HTTP/1.0 <code> <text>
            return intval($parts[1]); //Get code
    }
    return 0;
}



?>

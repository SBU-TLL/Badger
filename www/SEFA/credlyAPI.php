<?php 
$domain = "https://api.credly.com/v1.1";
#$access_token = "03b03d4432c7a060e86c21096d46bbdab1112bb8b0e1ca81e672e1b647d5af2c03732e4439d9ebefd73aa6d3ac1ccb0f7c85cfe1a908589f85458153b16f45c0";

$APIKey = "902b2dc4780df41c2a8a1a36d0cd89dc";
$APISecret = "q4kg/fJTBzkRydIh1LmFAvXUG1zKBXP6uqaFFlp89N2Fgqd/uWka1jt+Kv5oPQ30rhyytkmnhEZBj1ZbQL9I23cgbBlPqT3u4VvGIkqokajAtjUgEsKDB2Oponi5ogs9S0z9TyFxZqK6N0RGzfKvLq+4NYZXU2803skuHx/3G5Y=";
$access_token = "9f60ef27744872fa3b1da0b25b7a794b952f3104f393b31d5aa63b900d35d201708c7f34f0778d05ecf2a8e2fe0b1594a7ca8ec8cdde0c72dd8ec792528f45ec";

$app_id=693;

#$access_token = "171491a6d85f8597ab1c5a2757df41eff8ba4cba5405522ea915d2465c1b4f3dd4801384ebe0d581eec8c2a7f98f8bf83c5cc850aa13597cb74819f0346855c5";
#$APIKey = "020786f4a5441178c34bf969f9737fae";
#$APISecret = "A+52QWOILdRioG173iSkgsSxdljmuWjkUbJngofCypHjlMUSvnSbow57rQsb6+m5grIvzUwy0UnjZn+fUEL0mi3LbBgaeWQyNJYVoDE53cNsTsQLH2dOoMNvdqb7vCRw4UgW24GV1WThPgORRJOwjqfrVnNh8QUf5Yg9Bdi43RE=";

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

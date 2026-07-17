<?php 

function createBadge($domain,$APIKey,$APISecret,$access_token,$image,$title){
    
$headerDataRaw ="X-Api-Key:$APIKey\r\nX-Api-Secret:$APISecret\r\nContent-type:application/x-www-form-urlencoded"; 


$postDataRaw = <<<EOT
{
	
    "attachment":"$image",
    "title":"$title",
     "expires_in":"0",
	"is_givable":1
}
EOT;
 
  $postdata =json_decode($postDataRaw);
$getdata="access_token=$access_token";  
    
print_r(postCredly($domain,"/badges", $headerDataRaw,$postdata,$getdata));    
}






function rewardBadge($domain,$APIKey,$APISecret,$access_token,$badgeID,$userInfo){




$headerDataRaw ="X-Api-Key:$APIKey\r\nX-Api-Secret:$APISecret\r\nContent-type:application/x-www-form-urlencoded";


/*
$email= "zachary.zysberg@stonybrook.edu";
$first_name= "Electric";
$last_name = "Boogaloo";
$badge_id = "131204";
*/
$postDataRaw=<<<EOT
{
    "email": "${userInfo->email}",
	"first_name":"${userInfo->first_name}",
    "last_name":"${userInfo->last_name}",
    "badge_id":"${userInfo->badge_id}"
}
EOT;


$postdata =json_decode($postDataRaw);
$getdata="access_token=$access_token";

print_r(postCredly($domain,"/member_badges", $headerDataRaw,$postdata,$getdata));
}






function  postCredly($domain,$path, $header_data,$post_data,$get_data){

 
    
    
$opts = array('http' =>
    array(
        'method'  => 'POST',
        'header'  => $header_data,
        'content' => http_build_query($post_data),
	'timeout' => 60
    )
);
$url ="$domain$path?".$get_data;

$context = stream_context_create($opts);  
    
    
$results = file_get_contents($url, false, $context,-1,40000); 
return $results;
}





?>

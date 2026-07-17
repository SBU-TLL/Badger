<?php 
$domain = "https://api.credly.com/v1.1";
$access_token = "171491a6d85f8597ab1c5a2757df41eff8ba4cba5405522ea915d2465c1b4f3dd4801384ebe0d581eec8c2a7f98f8bf83c5cc850aa13597cb74819f0346855c5";
$APIKey = "020786f4a5441178c34bf969f9737fae";
$APISecret = "A+52QWOILdRioG173iSkgsSxdljmuWjkUbJngofCypHjlMUSvnSbow57rQsb6+m5grIvzUwy0UnjZn+fUEL0mi3LbBgaeWQyNJYVoDE53cNsTsQLH2dOoMNvdqb7vCRw4UgW24GV1WThPgORRJOwjqfrVnNh8QUf5Yg9Bdi43RE=";
#2326858



if($_POST["image"]){
    
    $encodedData = str_replace(' ','+',$_POST["image"]);
  file_put_contents("my.png", base64_decode($encodedData));
   print  createBadge($domain,$APIKey,$APISecret,$access_token, $encodedData,$_POST["firstline"]." ".$_POST["secondline"]);
    
    
    
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


function createBadge($domain,$APIKey,$APISecret,$access_token,$image,$title){
    
$headerDataRaw ="X-Api-Key:$APIKey\r\nX-Api-Secret:$APISecret\r\nContent-type:application/x-www-form-urlencoded"; 


$postDataRaw = <<<EOT
{
	
    "attachment":"$image",
    "title":"$title",
     "expires_in":"0"
}
EOT;
 
#	"authorized_issuer_ids":"2367566,2326858"
  $postdata =json_decode($postDataRaw);
#$getdata="access_token=$access_token&behalf_of=2326858";  
$getdata="access_token=$access_token&behalf_of=2231052";  
    
print_r(postCredly($domain,"/badges", $headerDataRaw,$postdata,$getdata));    
}






function testRewardBadge($domain,$APIKey,$APISecret,$access_token){




$headerDataRaw ="X-Api-Key:$APIKey\r\nX-Api-Secret:$APISecret\r\nContent-type:application/x-www-form-urlencoded";



$email= "zachary.zysberg@stonybrook.edu";
$first_name= "Electric";
$last_name = "Boogaloo";
$badge_id = "131204";

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

print_r(postCredly($domain,"/member_badges", $headerDataRaw,$postdata,$getdata));
}








?>

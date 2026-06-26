<?php 
require("credlyAPI.php");


if($_POST["image"]){
    
    $encodedData = str_replace(' ','+',$_POST["image"]);
   file_put_contents("my.png", base64_decode($encodedData));
   print_r(createBadge($app_id, $encodedData,$_POST["firstline"]." ".$_POST["secondline"]));
    
    
    
}

?>

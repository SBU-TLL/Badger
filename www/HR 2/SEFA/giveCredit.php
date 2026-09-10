<style>
body{
font-family: Helvetica, Arial, sans-serif;
font-size:1.5rem;
}
</style>
<?
require("credlyAPI.php");


if(array_key_exists("email", $_GET)){
	$userInfo=(object) $_GET;

rewardBadge($userInfo);
print"Badge Awarded!";
}

else{
	/*

	   $DataRaw = <<<EOT
	   {

	   "email": "paul.st.denis@stonybrook.edu",
	   "first_name":"Paul",
	   "last_name":"St.Denis",
	   "badge_id":144807,
	   "behalf_of":2326858
	   }
	   EOT;




	   $userInfo =json_decode($DataRaw);
	 */


	$badges=json_decode(getBadges());
	print"<form><select name='badge_id'>";

	foreach(($badges->data) as $badge)
	{
		$img= $badge->image_url;
		$title=$badge->title;
		$id=$badge->id;
		print <<<EOF

			<option value="$id">$title</option>


EOF;
	}
	print"</select>";

	print <<<EOF
		<br/>
	
               <input type="hidden" name="behalf_of" value="2326858"/>
		<label for="first_name">First Name</label>
		<input name="first_name"/>
		<br/>
           	<label for="last_name">Last Name</label>
                <input name="last_name"/>
		<br/>
		<label for="email">Email</label>
                <input name="email" size="30"/>
		</br>
		<input type="submit"/>
</form>
EOF;


}



?>



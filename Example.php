<?php
include 'Starred.class.php';
if(count($_GET) > 0)
{
foreach($_GET as $key => $value)
{
$getvars .= "$key=$value&";
}
}
$page = $getvars;
$getvars = urlencode($getvars);
$Starred =  new Starred;
$Rating = $Starred->RatingsInfo($page);
$voted = $Starred->UserRatingsInfo($page);
$key = $Starred->config("RATINGS_KEY");
if($voted)
{
$cvmsg = "You've already voted on this!";
}elseif(!$voted)
{
$cvmsg = "Rate this!";
}elseif(!isset($_SESSION[$key]))
{
$path = $Starred->config("RATINGS_LOGIN_PATH");
$cvmsg = "Please <a href='$path$page'>Log In</a> to rate.";
}
$user = $_SESSION['username'];
echo "<h1>Starred Example</h1>";
echo $Starred->DrawRateBar($getvars,$voted,$Rating,$cvmsg);
	
?>

<?php
include 'config.php';
/**Starred PHP class**/

class Starred {

function MySQLSetup() {
mysql_connect(RATINGS_HOSTNAME,RATINGS_USERNAME,RATINGS_PASSWORD) or die("Error! ".mysql_error());
mysql_select_db(RATINGS_DATABASE) or die("Error! ".mysql_error());
}

function config($request)
{
if ($request == "RATINGS_HOSTNAME") return(RATINGS_HOSTNAME);
if ($request == "RATINGS_USERNAME") return(RATINGS_USERNAME);
if ($request == "RATINGS_PASSWORD") return(RATINGS_PASSWORD);
if ($request == "RATINGS_DATABASE") return(RATINGS_DATABASE);
if ($request == "RATINGS_STARS_PATH") return(RATINGS_STARS_PATH);
if ($request == "RATINGS_LOGIN_PATH") return(RATINGS_LOGIN_PATH);
if ($request == "RATINGS_KEY") return(RATINGS_KEY);
}

function RatingsInfo($page, $show_voted_info = false) {
$this->MySQLSetup();
$select_all = mysql_query("SELECT * FROM ratings WHERE page = '$page'");
$total = mysql_num_rows($select_all);
$select_1 = mysql_query("SELECT * FROM ratings WHERE page = '$page' AND level=1");
$voted_1 = mysql_num_rows($select_1);
$select_2 = mysql_query("SELECT * FROM ratings WHERE page = '$page' AND level=2");
$voted_2 = mysql_num_rows($select_2);
$select_3 = mysql_query("SELECT * FROM ratings WHERE page = '$page' AND level=3");
$voted_3 = mysql_num_rows($select_3);
$select_4 = mysql_query("SELECT * FROM ratings WHERE page = '$page' AND level=4");
$voted_4 = mysql_num_rows($select_4);
$select_5 = mysql_query("SELECT * FROM ratings WHERE page = '$page' AND level=5");
$voted_5 = mysql_num_rows($select_5);
$votes_array = array($voted_1,$voted_2,$voted_3,$voted_4,$voted_5);
$Rating1 = array_keys($votes_array, max($votes_array));

if(count($Rating1) > 1) {
$Max_Rating = max($Rating1);
$Rating = $Max_Rating;
}else{
$Rating = $Rating1;
}
if($Rating[0] == 0){
$Rating = $Rating[0];
}else{
$Rating = $Rating[0]+1;
}
if($show_voted_info)
{
$select_majority = mysql_query("SELECT * FROM ratings WHERE page = '$page' AND level=$Rating");
$voted_majority = mysql_num_rows($select_majority);
$voted_info = "Rating shown is what the majority ($voted_majority) of $total people voted.";
}
return $Rating;
}


function UserRatingsInfo($page) {
$this->MySQLSetup();
$user = $_SESSION[RATINGS_KEY];
$user_rating_select = mysql_query("SELECT * FROM ratings WHERE user = '$user' AND page = '$page'");
$user_rating = mysql_fetch_assoc($user_rating_select);
if(mysql_num_rows($user_rating_select) <= 0) {
$currating = 0;
$voted = false;
}else
{
$currating = $user_rating['rating'];
$voted = true;
}
return $voted;
}

function RateIt($level,$page,$user) {
if(!isset($_SESSION[RATINGS_KEY])) {
$return = "You must log in to rate";
}elseif($this->UserRatingsInfo()) {
$return = "You've already rated this!";
}
$this->MySQLSetup();
mysql_query("INSERT INTO ratings (level, user, page) VALUES ('$level','$user','$page')") or $return = "MySQL query failed!";
if(isset($return)) {
return $return;
}else{
return "Successful!";
}
}
function DrawRateBar($getvars,$voted,$Rating,$cvmsg) {
$ratebar = '

<script type="text/javascript">

  Star1= new Image(30,30); 
  Star1.src="'.RATINGS_STARS_PATH.'/stargreen.png"; 

  Star2= new Image(30,30); 
  Star2.src="'.RATINGS_STARS_PATH.'/starred.png"; 

  Star3= new Image(30,30); 
  Star3.src="'.RATINGS_STARS_PATH.'/star.png";

function SwapPicture(pic,img_src) {

document[pic].src=img_src;

}


function ShowCurrentRating(level) {
var Star = 1;
while(Star <= level) {

var TheStar = \'Star\' + Star;

SwapPicture(TheStar,\''.RATINGS_STARS_PATH.'/stargreen.png\');
Star = Star + 1;
}
LockColour();

}

function RateIt(Level) {
var Star = 1;
while(Star <= Level) {

var TheStar = \'Star\' + Star;

SwapPicture(TheStar,\''.RATINGS_STARS_PATH.'/stargreen.png\');
Star = Star + 1;
}
LockColour();
AjaxRequest(Level);
}

function LockColour() {

document[\'Star1\'].onmouseover=\'\';
document[\'Star1\'].onmouseout=\'\';
document[\'Star1\'].onclick=\'\';
document[\'Star2\'].onmouseover=\'\';
document[\'Star2\'].onmouseout=\'\';
document[\'Star2\'].onclick=\'\';
document[\'Star3\'].onmouseover=\'\';
document[\'Star3\'].onmouseout=\'\';
document[\'Star3\'].onclick=\'\';
document[\'Star4\'].onmouseover=\'\';
document[\'Star4\'].onmouseout=\'\';
document[\'Star4\'].onclick=\'\';
document[\'Star5\'].onmouseover=\'\';
document[\'Star5\'].onmouseout=\'\';
document[\'Star5\'].onclick=\'\';
}

function AjaxRequest(level)
{
var url="rateit.php";
url=url+"?l="+level+"&page='.$getvars.'";

if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  xmlhttp.open("GET",url,false);
  xmlhttp.send(null);
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  xmlhttp.open("GET",url,true);
  // Do not send null for ActiveX
  xmlhttp.send();
  }
response.innerHTML = xmlhttp.responseText;
}
</script>

<div id="Bar">';
if($voted == true || !isset($_SESSION[KEY])){$ratebar .= "
<script type=\"text/javascript\">
$(document.getElementsByTagName('Bar')[0]).ready(function() {
ShowCurrentRating($Rating);
});
</script>";}
$ratebar .= '
<div id="response">
'.$cvmsg.'
</div>

<IMG NAME="Star1" SRC="'.RATINGS_STARS_PATH.'/starred.png" onclick="RateIt(1)" onmouseover="SwapPicture(\'Star1\',\''.RATINGS_STARS_PATH.'/star.png\')" onmouseout="SwapPicture(\'Star1\',\''.RATINGS_STARS_PATH.'/star.png\')" alt="Star 1">
<IMG NAME="Star2" SRC="'.RATINGS_STARS_PATH.'/starred.png" onclick="RateIt(2)" onmouseover="SwapPicture(\'Star1\',\''.RATINGS_STARS_PATH.'/star.png\'); SwapPicture(\'Star2\',\''.RATINGS_STARS_PATH.'/star.png\')" onmouseout="SwapPicture(\'Star2\',\''.RATINGS_STARS_PATH.'/star.png\'); SwapPicture(\'Star1\',\''.RATINGS_STARS_PATH.'/star.png\')" alt="Star 2">
<IMG NAME="Star3" SRC="'.RATINGS_STARS_PATH.'/starred.png" onclick="RateIt(3)" onmouseover="SwapPicture(\'Star1\',\''.RATINGS_STARS_PATH.'/star.png\'); SwapPicture(\'Star2\',\''.RATINGS_STARS_PATH.'/star.png\'); SwapPicture(\'Star3\',\''.RATINGS_STARS_PATH.'/star.png\')" onmouseout="SwapPicture(\'Star1\',\''.RATINGS_STARS_PATH.'/star.png\'); SwapPicture(\'Star2\',\''.RATINGS_STARS_PATH.'/star.png\'); SwapPicture(\'Star3\',\''.RATINGS_STARS_PATH.'/star.png\')" alt="Star 3">
<IMG NAME="Star4" SRC="'.RATINGS_STARS_PATH.'/starred.png" onclick="RateIt(4)" onmouseover="SwapPicture(\'Star1\',\''.RATINGS_STARS_PATH.'/star.png\'); SwapPicture(\'Star2\',\''.RATINGS_STARS_PATH.'.\'); SwapPicture(\'Star3\',\''.RATINGS_STARS_PATH.'/star.png\'); SwapPicture(\'Star4\',\''.RATINGS_STARS_PATH.'/star.png\')" onmouseout="SwapPicture(\'Star1\',\''.RATINGS_STARS_PATH.'/star.png\'); SwapPicture(\'Star2\',\''.RATINGS_STARS_PATH.'/star.png\'); SwapPicture(\'Star3\',\''.RATINGS_STARS_PATH.'/star.png\'); SwapPicture(\'Star4\',\''.RATINGS_STARS_PATH.'/star.png\')" alt="Star 4">
<IMG NAME="Star5" SRC="'.RATINGS_STARS_PATH.'/starred.png" onclick="RateIt(5)" onmouseover="SwapPicture(\'Star1\',\''.RATINGS_STARS_PATH.'/star.png\'); SwapPicture(\'Star2\',\''.RATINGS_STARS_PATH.'/star.png\'); SwapPicture(\'Star3\',\''.RATINGS_STARS_PATH.'/star.png\'); SwapPicture(\'Star4\',\''.RATINGS_STARS_PATH.'/star.png\'); SwapPicture(\'Star5\',\''.RATINGS_STARS_PATH.'/star.png\')" onmouseout="SwapPicture(\'Star1\',\''.RATINGS_STARS_PATH.'/star.png\'); SwapPicture(\'Star2\',\''.RATINGS_STARS_PATH.'/star.png\'); SwapPicture(\'Star3\',\''.RATINGS_STARS_PATH.'/star.png\'); SwapPicture(\'Star4\',\''.RATINGS_STARS_PATH.'/star.png\'); SwapPicture(\'Star5\',\''.RATINGS_STARS_PATH.'/star.png\')" alt="Star 5">
</div>';
return $ratebar;
}
}
?>

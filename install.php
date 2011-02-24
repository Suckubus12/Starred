<?php
/*Install script for Noisyscanner's Starred*/
if(isset($_POST['submitDB'])){
$host = $_POST['host'];
$db = $_POST['database'];
$user = $_POST['username'];
$pass = $_POST['password'];
mysql_connect("$host","$user","$pass") or die (mysql_error());
mysql_select_db("$db") or die (mysql_error());
echo"Database connection => OK<br>";
mysql_query("DROP TABLE IF EXISTS ratings") or die (mysql_error());
mysql_query("CREATE TABLE ratings(id int(11), user varchar(255), level int(11), page varchar(255))") or die(mysql_error());
echo"Database table exists => OK<br>";
mysql_query("INSERT INTO ratings (user, level, page)
VALUES ('Noisyscanner', 5, 'pid=123')") or die (mysql_error());
echo"Table setup => OK<br>";
echo"Test vote => OK<br>";
mysql_query("DELETE FROM ratings WHERE page='pid=123'") or die(mysql_error());
echo"Test vote deletion => OK<br>";
echo"Database testing complete.<br>";
$myFile = "config.php";
$file = fopen($myFile, 'w') or die("Could not open config file. Please check it's permissions and try again.");

$stringData = "<?php
define('RATINGS_HOSTNAME','$host');
define('RATINGS_DATABASE','$db');
define('RATINGS_USERNAME','$user');
define('RATINGS_PASSWORD','$pass');
";
fwrite($file, $stringData) or die("Could not write to config file. Please check it's permissions and try again.");
fclose($file);
echo"Writing config file => OK.<br>";
echo "Please proceed to the <a href='install.php?step=2'>Next Step</a>.<br>";
}
if(isset($_POST['submitLID'])){
$session_key = $_POST['key'];
$login_path = $_POST['login_path'];
$stars_path = $_POST['stars_path'];
$myFile = "config.php";
$file = fopen($myFile, 'a') or die("Could not open config file. Please check it's permissions and try again.");

$stringData = "
define('RATINGS_KEY','$session_key');
define('RATINGS_LOGIN_PATH','$login_path');
define('RATINGS_STARS_PATH', '$stars_path');
?>";
fwrite($file, $stringData) or die("Could not write to config file. Please check it's permissions and try again.");
fclose($file);
echo "Writing config file => OK.<br>";
echo "Installation complete! Please delete this install file.";
}
?>
<h1>Installation of Starred</h1>
<p>Welcome to the installation wizard of Noisyscanner's Starred script! Please follow the step-by-step instructions to get going.</p>
<?php if(intval($_GET['step']) == 1 || !isset($_GET['step'])){?> 
<h3>Database</h3>
<p>Starred uses a MySQL Database to store it's information, so please provide your database information.</p>
<form method="POST" action="install.php">
<label for="host">Host:</label><input type="text" name="host" id="host" value="localhost">
<label for="database">Database:</label><input type="text" name="database" id="database">
<label for="username">Username:</label><input type="text" name="username" id="username">
<label for="password">Password:</label><input type="password" name="password" id="password">
<input type="submit" id="submitDB" name="submitDB" value="submit">
</form>
<?php
}
if(intval($_GET['step']) == 2){
?>
<h3>Site</h3>
<p>Starred needs to know some paths and session data about your site here, as it requires your users to be logged in to vote. For this you will need a login system.</p>
<form method="POST" action="install.php">
<label for="key">Session key the user's username stored in:</label>$_SESSION['<input type="text" name="key" id="key" value="username">']<br>
<label for="login_path">Path to your Login Page (please use the complete URL):</label><input type="text" name="login_path" id="login_path"><br>
<label for="stars_path">Path to the Stars folder (please use the complete URL and leave no trailing slash):</label><input type="text" name="stars_path" id="stars_path"><br>
<input type="submit" id="submitLID" name="submitLID" value="submit">
</form>
<?php
}
?>


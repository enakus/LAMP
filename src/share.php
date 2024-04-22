<?php
session_start();
 
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

require_once "conf/shareconfig.php"; 
require_once "libs/dblib.php";
require_once "libs/dirlib.php";
require_once "libs/strlib.php";

$files = [];
$comments = [];

if(!isset($_REQUEST["sharename"])){
	header("location: index.php");
	exit;
}
else{
	$user_ret = get_share_author($link, trim($_REQUEST["sharename"]));
	if($user_ret == $_SESSION["username"]){
		$comments = get_all_comments($link, trim($_SESSION["username"]), trim($_REQUEST["sharename"]));
		$download_url = "<br><br><a href=download.php?sharename=" . trim($_REQUEST["sharename"]) . "> Download fileshare</a>";
		echo "<h1>" . trim($_REQUEST["sharename"]) . " files:</h1>";
		if ($handle = opendir("./shares/" . trim($_REQUEST["sharename"]))) {
	        	while (false !== ($entry = readdir($handle))) {
		    		if ($entry != "." && $entry != "..") {
		        		$files[] = $entry;
		    		}
	        	}
	        	closedir($handle);
	        }
	        foreach($files as $file){
  			echo "<p>" . $file . "</p>";
  		}
	}
	else{
		echo "[ERROR]: You can't request other people shares!<br>";
		echo "<a href=fileshares.php>Get back to your shares list</a><br>";
		exit;
	}
	
}

?>

<!DOCTYPE html>
<html>
<body>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<form action="./libs/filelib.php" method="post" enctype="multipart/form-data">
  		<br><h1>Select image to upload:</<h1><br>
  		<input type="file" class="btn btn-warning ml-3" name="fileToUpload" id="fileToUpload"><br>
  		<input type="submit" class="btn btn-danger ml-3" value="Upload Image" name="submit">
  		<input name="sharename" type="text" value="<?php echo trim($_REQUEST['sharename']); ?>"/>
	</form>
	<?php echo $download_url; ?>
	<br><h1>Comments:</h1><br>
	<?php
	if (empty($comments)) {
    		echo "No comments found.";
    	} else {
    		foreach ($comments as $comm) {
    		
    		echo $comm; 
    		}
    	}?>
	<form action="process_comment.php" method="post">
		<label for="comment">Enter your comment:</label><br>
	  	<textarea id="comment" name="comment" rows="5" cols="30"></textarea><br><br>
	  	<input name="sharename" type="text" value="<?php echo trim($_REQUEST['sharename']); ?>"/>
	  	<input type="submit" value="Submit Comment">
	</form>

</body>
</html>

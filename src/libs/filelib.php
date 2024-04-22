<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}

require_once "../conf/shareconfig.php"; 
require_once "./dblib.php";

$warn = "<a href=../fileshares.php>Get back to your shares list</a><br>";

$target_dir = "../shares/" . $_POST["sharename"] . "/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

function custom_sleep($delay) {
  $start = microtime(true);
  while (microtime(true) < $start + $delay);
}

if(isset($_POST["submit"]) && isset($_POST["sharename"])) {

	$user_ret = get_share_author($link, trim($_POST["sharename"]));
	if($user_ret == $_SESSION["username"]){
		$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
		if($check !== false) {
		  echo "[OK]: File is an image - " . $check["mime"] . ".<br>";
		  $uploadOk = 1;
		} else {
		  echo "[ERROR]: File is not an image.<br>";
		  $uploadOk = 0;
		  echo $warn; 
		  exit;
		}
	}
	else{
		echo "[ERROR]: You can't upload files to other people's shares! <br>";
		echo $warn; 
		exit;
	}
}

if (file_exists($target_dir)) {
    ;
} else {
    echo "[ERROR]:  Share '" . $_POST["sharename"] . "' does not exists!<br>";
    echo $warn;
    exit;
}

if (file_exists($target_file)) {
  echo "[ERROR]: Sorry, file already exists.<br>";
  $uploadOk = 0;
  echo $warn;
  exit;
}

if ($_FILES["fileToUpload"]["size"] > 500000) {
  echo "[ERROR]: Sorry, your file is too large.<br>";
  $uploadOk = 0;
  echo $warn;
  exit;
}

if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
  echo "[ERROR]: Sorry, only JPG, JPEG, PNG & GIF files are allowed.<br>";
  $uploadOk = 0;
  echo $warn;
  exit;
}

if ($uploadOk == 0) {
  echo "[ERROR]: Sorry, your file was not uploaded.<br>";
  echo $warn;
  exit;
  
} else {
  if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
    echo "[OK]: The file has been uploaded.<br>";
    echo $warn;
    exit;
  } else {
    echo "[ERROR]: Sorry, there was an error uploading your file.<br>";
    echo $warn;
    exit;
  }
}
?>

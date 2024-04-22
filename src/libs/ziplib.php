<?php
session_start();
 
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}

require_once "../conf/shareconfig.php"; 
require_once "dblib.php";
require_once "dirlib.php";
require_once "strlib.php";
require_once "ziplib.php";

$files = [];


function addFilesToZip($directory, $zip) {
	$files = scandir($directory);
        foreach ($files as $file) {
          if ($file != '.' && $file != '..') {
            $filePath = $directory . '/' . $file;
            if (is_dir($filePath)) {
              addFilesToZip($filePath, $zip);
            } else {
              $zip->addFile($filePath, basename($filePath));
            }
          }
        }
}

if(!isset($_REQUEST["sharename"])){
	header("location: ../index.php");
	exit;
}
else{
	$user_ret = get_share_author($link, trim($_REQUEST["sharename"]));
	if($user_ret == $_SESSION["username"]){
		  $folderPath = "../shares/" . trim($_REQUEST["sharename"]) . "/";
		  $zip = new ZipArchive();
		  $tmp_file = tempnam('/tmp','');

		  try {
		    $zip->open($tmp_file, ZipArchive::CREATE);

		    addFilesToZip($folderPath, $zip);

		    $zip->close();

		    header('Content-disposition: attachment; filename=' . trim($_REQUEST["sharename"]) .'.zip');
		    header('Content-type: application/zip');
		    readfile($tmp_file);
		  } finally {
		    if (file_exists($tmp_file)) {
		      if (unlink($tmp_file)) {
			echo "[OK]: Temporary file deleted successfully.";
		      } else {
			echo "[ERROR]: Could not delete temporary file.";
		      }
		    } else {
		      echo "[ERROR]: Temporary file not found.";
		    }
		  }
		
	}
	else{
		echo "BAD!";
	}
	
}
?>

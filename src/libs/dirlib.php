<?php

session_start();
 
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    exit;
}

function create_share_folder($folderName) {
    $folderPath = "/var/www/html/shares";
    $fullPath = $folderPath . DIRECTORY_SEPARATOR . $folderName;

    if (file_exists($fullPath)) {
      throw new Exception("Folder '{$folderName}' already exists.");
    }

    if (!mkdir($fullPath, 0700)) {
      throw new Exception("Error creating folder '{$folderName}'.");
    }

}

?>

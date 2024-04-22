<?php
session_start();
 
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

require_once "conf/shareconfig.php";
require_once "libs/dblib.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING);
    $user_ret = get_share_author($link, trim($_REQUEST["sharename"]));
    if($user_ret == $_SESSION["username"]){
    	$stmt = mysqli_prepare($link, "INSERT INTO comments (content, username, sharename) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sss", $param_com, $param_user, $param_share);
        
        $param_com = $comment; $param_user = trim($_SESSION["username"]); $param_share = trim($_REQUEST["sharename"]);
        
        if(mysqli_stmt_execute($stmt)){
            echo "[OK]: Comment submited!<br>";
            echo "<a href=fileshares.php>Get back to your shares list</a><br>";
            exit;
        }
        else{
            echo "[ERROR]: Execute err :(<br>";
            echo "<a href=fileshares.php>Get back to your shares list</a><br>";
            exit;
        }
    }
    else{
        echo "[ERROR]: You can't submit comments to othe users shares!<br>";
        echo "<a href=fileshares.php>Get back to your shares list</a><br>";
        exit;
    }
}
?>

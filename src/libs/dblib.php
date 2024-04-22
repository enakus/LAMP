<?php

session_start();
 
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    exit;
}

function check_if_exists($link, $sharename){
	$sql = "SELECT sharename FROM shares WHERE sharename = ?";
	if ($stmt = mysqli_prepare($link, $sql)) {
		mysqli_stmt_bind_param($stmt, "s", $param_sharename);

		$param_sharename = trim($sharename);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_store_result($stmt);

		if (mysqli_stmt_num_rows($stmt) > 0) {
			mysqli_stmt_close($stmt);
			return 1;
		}
	}
	else{
		mysqli_stmt_close($stmt);
		return 0;
	}
}

function create_fileshare($link, $sharename){
	$sql = "INSERT INTO shares (sharename, username) VALUES (?, ?);";
	if(check_if_exists($link, $sharename) == 0){	 
		if($stmt = mysqli_prepare($link, $sql)){
			mysqli_stmt_bind_param($stmt, "ss", $param_sharename, $param_username);
			    
			$param_sharename = trim($sharename);
			$param_username = trim($_SESSION["username"]);
			    
			if(mysqli_stmt_execute($stmt)){
			    return "[OK]: Fileshare '" . $sharename ."' was created!";
			    create_share_folder($sharename);
			} 
			else{
			    return "[ERROR]: " . mysqli_stmt_error($stmt);
			}
			mysqli_stmt_close($stmt);
		}
		mysqli_close($link);
	}else{
	    return "[ERROR]: Fileshare '" . $sharename . "' already exists!";
	    mysqli_close($link);
	}
}

function list_all_shares($link) {
	$sql = "SELECT sharename FROM shares WHERE username = ?";

	if ($stmt = mysqli_prepare($link, $sql)) {
	  mysqli_stmt_bind_param($stmt, "s", $param_username);

	  $param_username = $_SESSION["username"];
	  mysqli_stmt_execute($stmt);

	  $shares = [];

	  if (mysqli_stmt_errno($stmt)) {
	    echo "[ERROR]: " . mysqli_stmt_error($stmt);
	    return $shares;
	  }

	  mysqli_stmt_bind_result($stmt, $sharename);

	  while (mysqli_stmt_fetch($stmt)) {
	    $shares[] = $sharename;
	  }

	  mysqli_stmt_close($stmt);
	  return $shares;
	}

	mysqli_close($link);
}

function get_share_author($link, $sharename) {
	$sql = "SELECT username FROM shares WHERE sharename = ?";

	if ($stmt = mysqli_prepare($link, $sql)) {
	  mysqli_stmt_bind_param($stmt, "s", $param_sharename);

	  $param_sharename = $sharename;
	  mysqli_stmt_execute($stmt);

	  $username = "";

	  if (mysqli_stmt_errno($stmt)) {
	    return "[ERROR]: " . mysqli_stmt_error($stmt);
	  }

	  mysqli_stmt_bind_result($stmt, $username);

	  if(mysqli_stmt_fetch($stmt)){
	  	return $username;
	  }
	  else{
	  	return "[ERROR]: You dont own this share or it's does not exists!";
	  }
	  mysqli_stmt_close($stmt);
	}
	mysqli_close($link);
}

function get_all_comments($link, $username, $sharename) {
	$sql = "SELECT content, created_at, username FROM comments WHERE sharename = ?";
	$comments = [];
	if ($stmt = mysqli_prepare($link, $sql)) {
	  mysqli_stmt_bind_param($stmt, "s", $param_sharename);

	  $param_sharename = $sharename;
	  $param_username = $username;
	  mysqli_stmt_execute($stmt);

	  $comment = $author = $created_at = "";

	  if (mysqli_stmt_errno($stmt)) {
	    echo "errno";
	    return $comments;
	  }

	  mysqli_stmt_bind_result($stmt, $comment, $created_at, $author);

	  while(mysqli_stmt_fetch($stmt)){
	  	$comments[] = "<p>" . $created_at . " | " . $author . "<br>" .$comment . "</p><br>";
	  }
	  mysqli_stmt_close($stmt);
	}
	mysqli_close($link);
	return $comments;
}

?>

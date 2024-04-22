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

$shares = [];
$sharename = "";
$sharename_err = "";

$shares = list_all_shares($link);

if($_SERVER["REQUEST_METHOD"] == "POST"){

    if(empty(trim($_POST["sharename"]))){
        $sharename_err = "Please enter a sharename.";
    } 
    elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["sharename"]))){
        $sharename_err = "Sharename can only contain letters, numbers, and underscores.";
    } 
    else{
        $sharename_err = create_fileshare($link, trim($_POST["sharename"]));
        
        if(!str_contains($sharename_err, "[ERROR]")){
        	create_share_folder(trim($_POST["sharename"]));
        }else{
        	;
        }
        
        
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; text-align: center; }
    </style>
</head>
<body>
    <p>
        <a href="index.php" class="btn btn-warning">Main page</a>
    </p>
    <h1>File Shares List:</h1>
    <?php
    if (empty($shares)) {
    	echo "No shares found.";
    } else {
    	foreach ($shares as $share) {
    		
    		echo "<a href=share.php?sharename=" . $share . ">" .$share . "</a><br>"; 
    	}
    }
    ?>
    <div class="wrapper">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"> 
            <div class="form-group">
                <br><label>Fileshare name:</label>
                <input type="text" name="sharename" class="form-control <?php echo (!empty($sharename_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $sharename; ?>">
                <span class="invalid-feedback"><?php echo $sharename_err; ?></span>
            </div>    
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Create">
                <input type="reset" class="btn btn-secondary ml-2" value="Reset">
            </div>
        </form>
    </div>    
</body>
</html>

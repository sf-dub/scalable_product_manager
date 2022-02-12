<?php
// ** COPYRIGHT NOTICE: THIS CODE CANNOT BE USED FOR COMMERCIAL USE WITHOUT A LICENCE **
// ** Contact: admin@livenewsnow.org **
if(session_status() == PHP_SESSION_NONE){ session_start();}

if(!isset($_SESSION['loggedin'])){
  $_SESSION['loggedin'] = false;
}

$username = '';
$password = '';
$token = uniqid('', true);

if($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["token"])){

    if (!empty($_POST["username"]) && !empty($_POST["password"])) {

      require("db.php");

      $username = sanitize($_POST["username"]);
      $password = sanitize($_POST["password"]);

      $db = new connectDB();
      $db = $db->get_connection();
      $result = $db->query("SELECT crm_username,crm_password FROM crm_login limit 1");
      $finalpost = '';

    if($result){
      
    $rows = mysqli_fetch_array($result);
	
    $db_username = $rows[0];
    $db_password = $rows[1];

      if($username == $db_username && $password == $db_password){
          $_SESSION['loggedin'] = true;
          header("Location: main.php");
          exit();
      }else{
          header("Location: error.php");
          exit();
      }
    }else{
      header("Location: error.php");
      exit();
    }
}
}

function sanitize($input) {
  return htmlspecialchars(trim($input));
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title> Login </title>
</head>
<body style="text-align:center;background: rgb(233,233,233);
background: linear-gradient(90deg, rgba(233,233,233,1) 0%, rgba(70,70,199,1) 0%, rgba(47,159,181,1) 100%);">
<style>
#login{ margin-top:100px; }
input[type=submit] {
    cursor: pointer;
    margin-top:25px;
    margin-left:50px;
}
  label{
    color:#FFFFFF;
    font-weight: 100;
    width:80px;
    display:inline-block; }
</style>
<form id ="login" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST" enctype="application/x-www-form-urlencoded">
<div class="row">
<label for="username">Username:</label>
<input type="text" maxlength="99" id="username" name="username" required><br><br>
</div>
<div class="row">
<label for="password">Password:</label>
<input type="text" maxlength="99" id="password" name="password" ><br><br>
<input type="hidden" name="token" value="<?php echo($token); ?>" />
<input type="submit" value="Submit" name="submit">
</div>
</form>
</body>
</html>
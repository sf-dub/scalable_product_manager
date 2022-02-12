<?php
// ** COPYRIGHT NOTICE: THIS CODE CANNOT BE USED FOR COMMERCIAL USE WITHOUT A LICENCE **
// ** Contact: admin@livenewsnow.org **
if(session_status() == PHP_SESSION_NONE){ session_start();}
if(!isset($_SESSION['loggedin'])){
    include("lightheader.php");
    echo('<h1 style="color:#FFFFFF;text-align:center;margin-top:50px;"> You are not logged in </h1>');
    include("lightfooter.php");
  exit();
}else{
    if(isset($_SESSION['countPage'])){
    $_SESSION['countPage'] = 0;
    }
    if(isset($_SESSION['displayPage'])){
      $_SESSION['displayPage'] = 0;
    }
    session_unset();
    header("Location: index.php");
    exit();
}
?>

<?php
// ** COPYRIGHT NOTICE: THIS CODE CANNOT BE USED FOR COMMERCIAL USE WITHOUT A LICENCE **
// ** Contact: admin@livenewsnow.org **
if(session_status() == PHP_SESSION_NONE){ session_start();}

include_once("nocache.php"); // no browser caching to ensure fresh data 
include_once("config.php"); // sets pie chart data at bottom of page

// 2 checks for login session
if(!isset($_SESSION['loggedin'])){
    include("lightheader.php");
    echo('<div style="text-align:center;margin-top:50px;"><h1 style="color:#FFFFFF;"> You are not logged in </h1><br><a style="color:#FFFFFF;text-decoration:none;padding-bottom:2px;border-bottom: 1px solid #000" href="index.php" > Go Back </a></div>');
    include("lightfooter.php");
  exit();
}
if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == false){
    include("lightheader.php");
    echo('<div style="text-align:center;margin-top:50px;"><h1 style="color:#FFFFFF;"> You are not logged in </h1><br><a style="color:#FFFFFF;text-decoration:none;padding-bottom:2px;border-bottom: 1px solid #000" href="index.php" > Go Back </a></div>');
    include("lightfooter.php");
  exit();
}

// 4 sessions below are used throughout the application 
if(!isset($_SESSION['countPage'])){
  $_SESSION['countPage'] = 0;
}
if(!isset($_SESSION['displayPage'])){
  $_SESSION['displayPage'] = 0;
}
if(!isset($_SESSION['totalProductCount'])){
  getProductCount();
}
if(!isset($_SESSION['message'])){
  $_SESSION['message'] = '';
}

function getProductCount(){
  require_once("db.php");
  $db = new connectDB();
  $db = $db->get_connection();
  $countresult = $db->query("SELECT COUNT(*) FROM `crm_products`");
  $countresult = mysqli_fetch_array($countresult);
  $_SESSION['totalProductCount'] = $countresult[0];
  $db->close();
}

// insert product
// checks for not empty $_POST['name'] as the form can be submitted programmably with no data if public facing by a bad actor
if(isset($_POST['submit']) && $_POST['act'] == 'addProduct' && !empty($_POST['name'])) {

  require_once("db.php");

  $name = trim($_POST['name']);
  $desc = trim($_POST['description']);
  $price = trim($_POST['price']);
  $quantity = trim($_POST['quantity']);
  $category = trim($_POST['category']);
  $img = $_FILES['image'];

  $db = new connectDB();
  $db = $db->get_connection();
  
  // Check for no image and insert
  if($img['size'] == 0){
    $img = '';
    $query = "INSERT INTO crm_products (`name`,`description`,`price`,`quantity`,`category`,`image`) VALUES ('$name','$desc','$price','$quantity','$category','$img')";
    $result = $db->query($query);
    if($result){
      $_SESSION['message'] = 'Product inserted';
    }else{
      echo mysqli_errno($db);
      echo mysqli_error($db);
      $_SESSION['message'] = 'Could not insert Product.';
    }
   // Handle image and insert
  }else{
    $link = '';
    $filename = $img["name"];
    $tempname = $img["tmp_name"]; // where image is temporarily located on the server
    $link = 'assets/'.$filename;
    $tempname = $img["tmp_name"];

    move_uploaded_file($tempname, $link); // creates the image and saves to assets folder 

    $query = "INSERT INTO crm_products (`name`,`description`,`price`,`quantity`,`category`,`image`) VALUES ('$name','$desc','$price','$quantity','$category','$link')";
    $result = $db->query($query);
    if($result){
    $_SESSION['message'] = 'Product inserted';
    }else{
      echo mysqli_errno($db);
      echo mysqli_error($db);
      $_SESSION['message'] = 'Could not insert Customer.';
    }
  }
  $db->close();
}

// functionality for forward and back buttons which display when there's over 10 products 
if(isset($_POST['submit']) && $_POST['act'] == 'nextPage') {
  $_SESSION['countPage'] += 10;
  $_SESSION['displayPage'] += 1;
  pagination();
}
if(isset($_POST['submit']) && $_POST['act'] == 'backPage') {
  $_SESSION['countPage'] -= 10;
  $_SESSION['displayPage'] -= 1;
  pagination();
}

// pagination is the most used function it runs on page load and each time forward or back buttons are clicked
// returns data to be displayed
function pagination(){
  require_once("db.php");
  $db = new connectDB();
  $db = $db->get_connection();

  $buildData = '';
  $totalProductCount = $_SESSION['totalProductCount'];
  $counter = $_SESSION['countPage'];
  $page = $_SESSION['displayPage'];
  
  // $counter is used in the query to set page offset and display 10 results each time
  $result = $db->query("SELECT * FROM `crm_products` ORDER BY `ID` DESC LIMIT $counter, 10");

  if($result){
    $buildData .= '<div class="main">
    <table class="custtable" border="1">
    <tr>
    <th>ID</th>
    <th>Name</th>
    <th>Description</th>
    <th>Price</th>
    <th>Quantity</th>
    <th>Category</th>
    <th>Image</th>
    </tr>';

    while($row = mysqli_fetch_array($result)){
    $buildData .= '<tr>
    <td> '.$row['ID'].'</td>
    <td> '.$row['name'].'</td>
    <td> '.$row['description'].'</td>
    <td> '.$row['price'].'</td>
    <td> '.$row['quantity'].'</td>
    <td> '.$row['category'].'</td>
    <td> '.$row['image'].'</td>
    </tr>';
    }
    $db->close();
    $buildData .=
    '</table>
    <br>
    </div>';


    // No need to display buttons if there's less than 11 products
    if($totalProductCount <= 10){
      return $buildData;
    }

    // Check if it's the last page first because it's the only time we can
    if ($counter >= ($totalProductCount - 10)){
      $buildData .= '
      <form style="padding:5px" action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'" method="post">
      <input style="width:6em" type="submit" value="Previous" name="submit"/>
      <input type="hidden" name="act" value="backPage" /></form>';
      return $buildData;
    }
    // Check if over 10
    elseif($counter >= 10){
      $buildData .= '
      <div class="main" style="justify-content: space-between">
      <form style="padding:5px" action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'" method="post">
      <input style="width:6em;padding-right:5px;" type="submit" value="Previous" name="submit"/>
      <input type="hidden" name="act" value="backPage" /></form>

      <form style="padding:5px" action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'" method="post">
      <input style="width:6em" type="submit" value="Next" name="submit"/>
      <input type="hidden" name="act" value="nextPage" /></form>
      </div>';
    return $buildData;
    // Displays when products count is 11 - 21
    }else{
      $buildData .= '
      <form style="padding:5px" action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'" method="post">
      <input style="width:6em" type="submit" value="Next" name="submit"/>
      <input type="hidden" name="act" value="nextPage" /></form>';
      return $buildData;
    }
    }else{
      exit('Could not get data &#x25b2; from db '.$db->error);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="css/style.css" rel="stylesheet">
<link rel="icon" type="image/png" href="favicon.png"/>
<title>Products</title>
</head>
<body style="text-align:center;">
<div id="header" class="header">
  <a style="text-decoration:none;" href="main.php"><h3> Company Name / Logo</h3></a>
  <div class="dropdown">
  <button class="dropbtn">Menu</button>
  <div class="dropdown-content">
    <a href="#">Customers</a>
    <a href="#">Products</a>
    <a href="logout.php">Logout</a>
  </div>
</div> <!-- end dropdown -->
</div> <!-- end header -->
<?php
// display message if needed 
if(isset($_SESSION['message']) && !empty($_SESSION['message'])){
  echo('<div id ="message" class="main">');
  echo($_SESSION['message']);
  echo('</div>');
}
?>
<div id ="loop">
<?php

// need to update product count dynamically after inserting new data
getProductCount(); 
$totalProductCount = $_SESSION['totalProductCount'];

if($totalProductCount > 10){
  $page = $_SESSION['displayPage'];
  $var = ceil($totalProductCount / 10);
  if($page == 0){
    $_SESSION['displayPage'] = 1;
  }
  $page = $_SESSION['displayPage'];
  echo('<p><b>Products </b>: '.$totalProductCount.'');
  echo('<p><b>Page </b>: '.$page.' of '.$var.'</p> ');
}

$data = pagination();
echo($data);
?>
</div>
<div id="addProduct">
<h3>Add Product</h3>
<div class="main">
<form id ="makepost" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" enctype="multipart/form-data">
<div class="row">
<div class="col-25">
<label for="title">Name:</label>
</div>
<div class="col-75">
<input type="title" id="title" name="name" required/><br><br>
</div>
</div>
<div class="row">
<div class="col-25">
<label for="title">Description:</label>
</div>
<div class="col-75">
<input type="title" id="title" name="description"/><br><br>
</div>
</div>
<div class="row">
<div class="col-25">
<label for="title">Price:</label>
</div>
<div class="col-75">
<input type="title" id="title" name="price"/><br><br>
</div>
</div>
<div class="row">
<div class="col-25">
<label for="text">Quantity:</label><br>
</div>
<div class="col-75">
<input type="title" id="title" name="quantity"/><br><br>
</div>
</div>
<div class="row">
<div class="col-25">
<label for="text">Category:</label><br>
</div>
<div class="col-75">
<select name="category" id="category">
    <option value="carpet">Carpet</option>
    <option value="flooring">Flooring</option>
    <option value="lino">Lino</option>
    <option value="other">Other</option>
  </select><br><br>
</div>
</div>
<div class="row">
<div class="col-25">
<label for="text">Image:</label><br>
</div>
<div class="col-75">
Select an image to upload:
<input type="file" id="image" name="image" accept="image/*" capture="camera" /><br><br>
</div>
</div>
<input class="addCustInput" type="submit" value="submit" name="submit"/>
<input type="hidden" name="act" value="addProduct"/>
</div>
</form>
</div>
</div>
<div class="main">
<div id="spacebetween" class="spacebetween">
 <div>
<h3>Best Performing Stores</h3>
<canvas id="products" width="700" height="400"> </canvas>
</div>
<div>
<h3> Customer Retention </h3>
<canvas id="customers" width="700" height="400"> </canvas>
</div>
</div>
</div>
<script>
var drawPieChart = function(data, colors, type) {
  var canvas = document.getElementById(type);
  var ctx = canvas.getContext('2d');
  var x = canvas.width / 2;
      y = canvas.height / 2;
  var color,
      startAngle,
      endAngle,
      total = getTotal(data);
  
  for(var i=0; i<data.length; i++) {
    color = colors[i];
    startAngle = calculateStart(data, i, total);
    endAngle = calculateEnd(data, i, total);
    
    ctx.beginPath();
    ctx.fillStyle = color;
    ctx.moveTo(x, y);
    ctx.arc(x, y, y-100, startAngle, endAngle);
    ctx.fill();
    ctx.rect(canvas.width - 200, y - i * 30, 12, 12);
    ctx.fill();
    ctx.font = "bold 13px sans-serif";
    ctx.fillText(data[i].label + " - (" + calculatePercent(data[i].value, total) + "%)", canvas.width - 200 + 20, y - i * 30 + 10);
  }
};

var calculatePercent = function(value, total) {
  
  return (value / total * 100).toFixed(2);
};

var getTotal = function(data) {
  var sum = 0;
  for(var i=0; i<data.length; i++) {
    sum += data[i].value;
  }
      
  return sum;
};

var calculateStart = function(data, index, total) {
  if(index === 0) {
    return 0;
  }
  
  return calculateEnd(data, index-1, total);
};

var calculateEndAngle = function(data, index, total) {
  var angle = data[index].value / total * 360;
  var inc = ( index === 0 ) ? 0 : calculateEndAngle(data, index-1, total);
  
  return ( angle + inc );
};

var calculateEnd = function(data, index, total) {
  
  return degreeToRadians(calculateEndAngle(data, index, total));
};

var degreeToRadians = function(angle) {
  return angle * Math.PI / 180
}

// php variables from config.php are used to set values below
var data = [
  { label: '<?php echo($store1); ?>', value: <?php echo($store1data); ?> },
  { label: '<?php echo($store2); ?>', value: <?php echo($store2data); ?> },
  { label: '<?php echo($store3); ?>', value: <?php echo($store3data); ?> }
];
var colors = [ '#c1a41a', '#95041f', '#157FB5' ];
var type = "products";
drawPieChart(data, colors,type);

var data = [
  { label: 'Returning', value: <?php echo($returning); ?> },
  { label: 'New', value: <?php echo($new); ?> }
];

var colors = [ '#343794', '#34a747' ];
var type = "customers";
drawPieChart(data, colors,type);

/*
// Static variables if needed

var data = [
  { label: 'Example 1', value: 25 },
  { label: 'Example 2', value: 30 },
  { label: 'Example 3', value: 45 }
];

var data = [
  { label: 'Returning', value: 65 },
  { label: 'New', value: 35 }
];
*/

// This is used to not display form submission dialog when clicking buttons - bad ux
if ( window.history.replaceState ) {
  window.history.replaceState( null, null, window.location.href );
}
</script>
<?php
// reset message for next reload
$_SESSION['message'] = '';
?>
</body>
</html>
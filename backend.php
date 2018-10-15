<?php

$hostname = "sql1.njit.edu";
$username = "psn24";
$project  = "psn24";
$password = "tFbtFk7F";

$db = mysqli_connect($hostname,$username, $password ,$project);
if (mysqli_connect_errno())
  {	  
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }

mysqli_select_db( $db, $project ); 

$UCID = $_POST [ "UCID" ] ;
$password = $_POST [ "password" ] ;

$s="SELECT password from people where UCID='$UCID'";
$t=mysqli_query($db,$s);
$row=mysqli_fetch_row($t);
$thepassword = $row[0];

$hash = password_hash($password, PASSWORD_DEFAULT);
if(password_verify($thepassword, $hash)){
    $response = 'true';
  }
else $response = 'false';

if ($thepassword == ""){
  $response = 'false';
}

echo $response;

if ($response == 'true'){
	$q="SELECT role from people where UCID='$UCID'";
	$w=mysqli_query($db,$q);
	$rowe=mysqli_fetch_row($w);
	$therole = $rowe[0];
	echo $therole;
}

mysqli_free_result($t);
mysqli_free_result($w);
mysqli_close($db);
exit();

?>
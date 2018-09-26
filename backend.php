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

$u = $mysqli->query("SELECT UCID FROM people WHERE UCID = '$UCID'");
if($u->num_rows == 0) {
     $response = 'false';
}

echo $response;

/*

$url = 'https://web.njit.edu/~jh465/middle.php';
 
//Send URL
$ch = curl_init($url);
 
//DATA
$jsonData = array('response' => $response);

//Encode
$jsonDataEncoded = json_encode($jsonData);

//POST request.
curl_setopt($ch, CURLOPT_POST, 1);
 
//JSON string to the POST fields.
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
 
//Set the content type to application/json
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
 
//Execute the request
$result = curl_exec($ch);

//close
curl_close($ch);

*/

mysqli_free_result($t);
mysqli_close($db);
exit();

?>
<?php

if(isset($_POST['UCID'], $_POST['password'])){
    $ucid = ($_POST['UCID']);
    $password = $_POST['password'];
} 
$data = array('UCID'=>$ucid, 'password'=>$password);
//echo $data['UCID'];
$string = http_build_query($data);


$send = curl_init();
curl_setopt($send, CURLOPT_URL, "https://web.njit.edu/~psn24/backend.php");
curl_setopt($send, CURLOPT_POST, true);
curl_setopt($send, CURLOPT_POSTFIELDS, $string);
curl_setopt($send, CURLOPT_RETURNTRANSFER, true);
$resp = curl_exec($send); 

curl_close($send); 
//$jsonDecoded = json_decode($resp);
//echo $jsonDecoded; 

echo $resp; 

$fields = 'pass=' . "$password" . '&user=' . "$ucid" . '&uuid=' . '0xACA021';

$njitlogin = curl_init("https://cp4.njit.edu/cp/home/login");
curl_setopt($njitlogin, CURLOPT_POST, true);
curl_setopt($njitlogin, CURLOPT_POSTFIELDS, $fields);
curl_setopt($njitlogin, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($njitlogin);
curl_close($njitlogin);


if((strpos($response, "Failed") == false) AND (strpos($response, "Disabled") == false)){
 //{"NJIT": true, "Database": $resp}
  echo '{"LOGIN":"SUCCESSFUL"}';
}
else{
  echo '{"LOGIN":"FAILURE"}';
}


//$ch = curl_init("https://web.njit.edu/~lr66/download/curlpost.php");
//curl_setopt($ch, CURLOPT_POST, true);
//curl_setopt($ch, CURLOPT_POSTFIELDS, 

//curl_close($ch);

?>

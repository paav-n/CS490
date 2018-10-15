<?php
if(isset($_POST['UCID'], $_POST['password'])){
    $ucid = ($_POST['UCID']);
    $password = $_POST['password'];
} 
$data = array('UCID'=>$ucid, 'password'=>$password);
//echo $data['UCID'];
$string = http_build_query($data);
$send = curl_init();
curl_setopt($send, CURLOPT_URL, "https://web.njit.edu/~psn24/CS490/backend.php");
curl_setopt($send, CURLOPT_POST, true);
curl_setopt($send, CURLOPT_POSTFIELDS, $string);
curl_setopt($send, CURLOPT_RETURNTRANSFER, true);
$resp = curl_exec($send); 
curl_close($send); 
//$jsonDecoded = json_decode($resp);
//echo $jsonDecoded; 
echo $resp; 


?>

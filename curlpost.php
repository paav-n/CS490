<?php
$UCID = $_POST  ["UCID"];
$password = $_POST ["password"];
$data = array("UCID" => $UCID, "password" => $password);
#$data = array("UCID" => "jh465", "password" => "fdf");                                 
#$data_string = json_encode($data); #echo $data_string; 
$data_string = http_build_query($data);  
  
#echo $data_string;  

#echo $UCID;  
#echo $password;                                                               
                                                                                                                     
$ch = curl_init('https://web.njit.edu/~jh465/middle.php');                                                                      
curl_setopt($ch, CURLOPT_POST, true);  
#curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                    
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
#curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
    #'Content-Type: application/json',                                                                                
    #'Content-Length: ' . strlen($data_string))                                                                       
#);                                                                                                                   
                                          
$result = curl_exec($ch);
echo $result;
?>
<?php
//For login
$UCID = $_POST ["UCID"];
$password = $_POST ["password"];
//Command def
$struct = $_POST ["struct"];
$newQ = $_POST ["newQ"];
$difficulty = $_POST ["difficulty"];
$category = $_POST ["category"];
$funcName = $_POST ["funcName"];
$parameters = $_POST ["parameters"];
$testcase = $_POST ["testcase"]; 
$examName = $_POST ["examName"];
$examQ = $_POST ["examQ"];
$answer = $_POST ["answer"];
$parameterVal = $_POST ["parameterVal"];

$url = 'https://web.njit.edu/~jh465/beta/grading.php';
if ($struct == "addQuestion"){
  $data = array("struct" => $struct, "newQ" => $newQ, "difficulty" => $difficulty, "category" => $category, "funcName" => $funcName, 
  "parameters" => $parameters, "parameterVal" => $parameterVal, "testcase" => $testcase);
}
elseif($struct == "takeExam"){
  $data = array("struct" => $struct, "examName" => $examName);
}
elseif($struct == "showAll"){
  $data = array("struct" => $struct);
}
elseif($struct == "makeExam"){
 $data = array("struct" => $struct, "examName" => $examName, "examQ" => $examQ);
}
elseif($struct == "storeAnswers"){
 $data = array("struct" => $struct, "examName" => $examName, "examQ" => $examQ, "answer" => $answer, "UCID" => $UCID);
 echo $answer;
}
elseif($struct == "viewGrade"){
 $data = array("struct" => $struct, "UCID" => $UCID);
}
elseif($struct == "releaseGrade"){
 $data = array("struct" => $struct, "UCID" => $UCID);
}
elseif($struct == "viewStudents"){
 $data = array("struct" => $struct);
}
elseif($struct == "viewExams"){
 $data = array("struct" => $struct);
}
else{
  $url = 'https://web.njit.edu/~jh465/middle.php';
  $data = array("UCID" => $UCID, "password" => $password);
}
                                  
$data_string = http_build_query($data);  
                                                                                                                                                                             
$ch = curl_init($url);                                                                      
curl_setopt($ch, CURLOPT_POST, true);                                                                     
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                                                                                                                                                                           
$result = curl_exec($ch);
echo $result;
?>
<?php

//ini_set('display_errors',1);
//error_reporting(E_ALL);
$ucid = $_POST['UCID'];
$struct = $_POST['struct'];
$newQ = $_POST [ "newQ" ] ;
$testcase = $_POST [ "testcase" ] ;
$funcName = $_POST [ "funcName" ] ;
$parameters = $_POST [ "parameters" ] ;
$examQ = $_POST [ "examQ" ] ;
$examName = $_POST [ "examName" ] ;
$category = $_POST [ "category" ] ;
$difficulty = $_POST [ "difficulty" ] ;
$ans = $_POST [ "answer"];
$par = $_POST["parameterVal"];
$questID = $_POST["questionID"];

$data = array('UCID'=>$ucid, 
              'struct'=>$struct, 
              'newQ'=>$newQ,
              'testcase'=>$testcase,
              'funcName'=>$funcName,
              'parameters'=>$parameters,
              'examQ'=>$examQ,
              'examName'=>$examName,
              'category'=>$category,
              'difficulty'=>$difficulty,
              'answer'=>$ans,
              'parameterVal'=>$par,
              'questionID'=>$questID);
$string = http_build_query($data);


$send = curl_init();
curl_setopt($send, CURLOPT_URL, "https://web.njit.edu/~psn24/CS490/backend_logic.php");
curl_setopt($send, CURLOPT_POST, true);
curl_setopt($send, CURLOPT_POSTFIELDS, $string);
curl_setopt($send, CURLOPT_RETURNTRANSFER, true);
$resp = curl_exec($send); 

curl_close($send);
echo $resp; 
//if answer is set do all this
//assuming answer and examQ sent at same time
if(isset($_POST['answer'], $_POST['examQ'], $_POST['UCID'])){
$struct = 'gradeExam';
$split_questionID = explode(",", $examQ);//getting question ids
$count = count($split_questionID);//the number of questions
}

for($i = 0; $i < $count; $i++){

  $singleExamQ = $split_questionID[$i];
  $data1 = array('struct'=>$struct, 'questionID'=>$singleExamQ);
  $string1 = http_build_query($data1);
  
  $send1 = curl_init();
  curl_setopt($send1, CURLOPT_URL, "https://web.njit.edu/~psn24/CS490/backend_logic.php");
  curl_setopt($send1, CURLOPT_POST, true);
  curl_setopt($send1, CURLOPT_POSTFIELDS, $string1);
  curl_setopt($send1, CURLOPT_RETURNTRANSFER, true);
  $resp1 = curl_exec($send1); 
  curl_close($send1);
  //echo $resp1;
  $jsonResponse[$i] = $resp1; //putting the required tstcases/funcname..etc for each question in array
  //echo $resp1; 
} 

$count1 = count($jsonResponse);
$ans = explode("~", $ans); //assuming answers are sent tilda delimited
//for every question response, parse everyone of them
for($i = 0; $i < $count1; $i++){
  $parse = $jsonResponse[$i]; //the json of testcases/param/etc..
  $obj1 = json_decode($parse);
  $req_param_names = $obj1->{'parameterVal'};//parameter names
  $req_funcname = $obj1->{'functions'};//function name
  $questID = $obj1->{'questionID'};//questionID
 $case_output = $obj1->{'results'};//testcase outputs
 $case_input = $obj1->{'parameter'};//testcaseinput
  $answer = $ans[$i]; //student answers

  $points_worth += 10; //next time either professor decides point worth or by difficulty
   $grade1 += grade_exam($answer, $req_funcname, $req_param_names, $case_input, $case_output, $ucid);
} 

//get the average of the grades,
  $grade1 = ($grade1/$points_worth)*100; //have to make sure you make it be an int
  $grade1 = round($grade1);
  //echo "$grade1\n";
if($struct == 'gradeExam'){ 
$newdata = array('grade'=>$grade1, 'struct'=>'storeGrade', 'UCID'=>$ucid);
$string2 = http_build_query($newdata);


$send2 = curl_init();
curl_setopt($send2, CURLOPT_URL, "https://web.njit.edu/~psn24/CS490/backend_logic.php");
curl_setopt($send2, CURLOPT_POST, true);
curl_setopt($send2, CURLOPT_POSTFIELDS, $string2);
curl_setopt($send2, CURLOPT_RETURNTRANSFER, true);
$resp2 = curl_exec($send2);

curl_close($send2); 

}
//function to grade student exam
function grade_exam($student_answer, $funcname, $parameters, $testcase_input, $testcase_output, $studentID){
$grade = 0;

$student_answer = ltrim($student_answer); //trimming white space from beginning
$split_answer = preg_split("/\s+|\(/", $student_answer);
$def = $split_answer[0]; //first word should be 'def'
//echo "$def\n";

if ($def != "def"){
  echo "Function was not declared properly, 1 point off\n";
}
else{
  echo "Function declaration correct\n";
  $grade++;
}


$student_funcname = $split_answer[1]; //the second word which should be the function name
//echo "$student_funcname\n";

if($student_funcname != $funcname){
  echo "Function name incorrect, 1 point off\n";
}
else{
  echo "Function name correct!\n";
  $grade++;
}

$split_answer2 = explode(")", $student_answer); //splitting original student answer
$temp = $split_answer2[0]; //should give you everything up to ')'
$splitagain = explode("(", $temp); //splitting it again giving you everything up to '('
$studentparams = $splitagain[1]; //the student parameters
//echo "$studentparams\n";

$studentparams = preg_replace("/\s/","", $studentparams);
$parameters = preg_replace("/\s/","", $parameters);
//echo "$studentparams\n";
//echo "$parameters\n";

if(strcmp($studentparams, $parameters) == 0){
  echo "correct parameter names\n";
  $grade++;
}
else{
  echo "the parameter names are incorrect, 1 point off\n";
} 
//later if we're using more than one testcase_input per question have to split them and loop for each
$newparams = $testcase_input; //the testcase input parameters.

$file = "$studentID.py";

file_put_contents($file, $student_answer . "\n" . "print($student_funcname($newparams))");


$runpython = exec("python $studentID.py");
//echo "$runpython\n";
//echo "$testcase_output\n";
if ($runpython == $testcase_output){
  echo "Output was correct\n";
  $grade+=7;
}
else{
  echo "output incorrect, 7 points off\n";
}


return $grade;

} 

?>
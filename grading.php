<?php

//file_get_contents('php://input');
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
$points_sent = $_POST["points"];
$constraints = $_POST["constraints"];
$filter = $_POST["filter"];
$pointsReceived = $_POST["pointsReceived"];
$teacherComments = $_POST["teacherComments"];
$key = $_POST["key"];
$cat = $_POST["cat"];
$diff = $_POST["diff"];
$con = $_POST["con"];

$ans = rawurldecode($ans);
$testcase = rawurldecode($testcase);
//echo $testcase;

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
              'questionID'=>$questID,
              'points'=>$points_sent,
              'constraints'=>$constraints,
              'filter'=>$filter,
              'pointsReceived'=>$pointsReceived,
              'teacherComments'=>$teacherComments,
              'key'=>$key,
              'cat'=>$cat,
              'diff'=>$diff,
              'con'=>$con);
$string = http_build_query($data);

//echo $ans;


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
  $points_worth = $obj1->{'points'};
  $examID = $obj1->{'examID'};
  $constraint = $obj1->{'constraints'};
  
   //$grade1 += grade_exam($answer, $req_funcname, $req_param_names, $case_input, $case_output, $ucid, $points_worth);
   
   $gradeSolo = grade_exam($answer, $req_funcname, $req_param_names, $case_input, $case_output, $ucid, $points_worth, $constraint);
   
   $gradePlusComment = explode("^", $gradeSolo);
   $comments = $gradePlusComment[0];
   $gradeSolo2 = $gradePlusComment[1]; //grade by itself
   
   //echo $gradeSolo2;
   
   $grade1 += $gradePlusComment[1]; //grade completely 
   
   $questID = $split_questionID[$i];
   $points_total += $points_worth;
   
   if($struct == 'gradeExam'){ 
    $newdata = array('pointsReceived'=>$gradeSolo2, 'reasons'=>$comments, 'struct'=>'storeComment', 'UCID'=>$ucid, 'questionID'=>$questID, 'examID'=>$examID);
    $string2 = http_build_query($newdata);
    
    
    $send2 = curl_init();
    curl_setopt($send2, CURLOPT_URL, "https://web.njit.edu/~psn24/CS490/backend_logic.php");
    curl_setopt($send2, CURLOPT_POST, true);
    curl_setopt($send2, CURLOPT_POSTFIELDS, $string2);
    curl_setopt($send2, CURLOPT_RETURNTRANSFER, true);
    $resp2 = curl_exec($send2);
    
    curl_close($send2); 
  
  } 
   
} 

//get the average of the grades,
  $grade1 = ($grade1/$points_total)*100; //have to make sure you make it be an int
  $grade1 = round($grade1, 0);
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
function grade_exam($student_answer, $funcname, $parameters, $testcase_input, $testcase_output, $studentID, $pointValue, $constraints){
  $grade = $pointValue;
  $gradeTotal = 0;
  $comments = "";
  
  $student_answer = ltrim($student_answer); //trimming white space from beginning
  $split_answer = preg_split("/\s+|\(|:/", $student_answer);
  $def = $split_answer[0]; //first word should be 'def'
  //echo "$def\n";
  
  /*
  if ($def != "def"){
    $comments += "Function was not declared properly, 1 point off\n";
    $gradeTotal++;
  }
  else{
    $comments += "Function declaration correct\n";
    $grade++;
    $gradeTotal++;
  } */
  
  
  $student_funcname = $split_answer[1]; //the second word which should be the function name
  //echo "$student_funcname\n";
  
  //echo $student_funcname;
  //echo $funcname;
  
  if($student_funcname != $funcname){
    $comments .= "Function name incorrect. Function name required is $funcname, student provided $student_funcname, 5 point off~";
    $grade-=5; //come back to this!!!
    $gradeTotal+=5;
  }
  else{
    $comments .= "Function name correct!~";
    $gradeTotal+=5;
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
  
  //echo $studentparams;
  //echo $parameters;
  
  if(strcmp($studentparams, $parameters) == 0){
    $comments .= "correct parameter names~";
    $gradeTotal++;
  }
  else{
    $comments .= "Parameter names incorrect. The parameter names required are $parameters , student provided $studentparams, 1 point off~";
    $grade--;
    $gradeTotal++;
  } 
  
  switch ($constraints) {
    case "for":
      if(preg_match("/\bfor\b/", $student_answer)){
      //do nothing
      break;
    }
      else{
        $comments .= "Constraint incorrect. Did not use a for loop when the question required it, 1 point off~";
        $grade--;
        $gradeTotal++;
        break;
      }
    case "while":
      if(preg_match("/\bwhile\b/", $student_answer)){
        //do nothing
        break;
      }
      else{
        $comments .= "Constraint incorrect. Did not use a while loop when the question required it, 1 point off~";
        $grade--;
        $gradeTotal++;
        break;
        }
    case "recursion":
      if(preg_match("/(\b$student_funcname\b).*(\b$student_funcname\b)/", $student_answer)){
    //do nothing
        break;
      }
      else{
        $comments .= "Constraint incorrect. Did not use recursion when the question required it, 1 point off~";
        $grade--;
        $gradeTotal++;
        break;
      }
      //if you see the function name at least twice in the answer then recursion is used.
      //do this later
    default:
      echo "there are no constraints for this question";
      
  } 
  
  //check for colons in the student answer!!
  
  
  //fix this a bit 
  if(preg_match("/\bprint\b/", $student_answer)){
    $student_answer = preg_replace("/\bprint\b/", "return", $student_answer);
    $grade--;
    $gradeTotal++;
    $comments .= "incorrect return of function, 1 point off~";
    //change this to check for the last line having a print in it.
  }
  
  //CHECKING FOR COLONS STARTS HERE!!!!
  if(preg_match("/\bdef\b|\bfor\b|\bif\b|\belse\b|\bwhile\b/", $student_answer)){
  //for where there is a for, if , else etc.... check to see if there is a colon at the end of the line
  //foreach(
  $separator = "\r\n";
  $line = strtok($student_answer, $separator);
  $line1 = "";
  while ($line !== false) {
      if(preg_match("/\bdef\b|\bfor\b|\bif\b|\belse\b|\bwhile\b|\belif\b/", $line)){
        //check if it ends with a colon
        if (preg_match('/:$/', $line)) {
          //echo "it worked wohoo!\n";
          $line1 .= $line . $separator;
        }
        else{
          //add the colon at the end of the line
          //echo "nope try again\n";
          $grade--;
          $gradeTotal++; //come back to this
          $line1 .= $line . ":" . $separator;
          $comments .= "incorrect, no colon at the end of line, 1 point off~";
        }
      }
      else {
        $line1 .= $line . $separator;
      }
      $line = strtok( $separator );
  }
  $student_answer = $line1;
  echo $student_answer;
 }
 //CHECKING FOR COLONS ENDS HERE!!!

  $newparams = $testcase_input; //the testcase input parameters.
  $temp = explode("~", $newparams); //tilda delimited testcase inputs
  $testcaseCount = count($temp);
  
  
  
  //echo $testcase_input;
  
  $testcase_output = explode("~", $testcase_output);
  
  $file = "$studentID.py";
  
  $remainingGrade = $pointValue - $gradeTotal;
  $testcasePointValue = $remainingGrade/$testcaseCount;
  $testcasePointValue = round($testcasePointValue, 0);
  
  for($i = 0; $i < $testcaseCount; $i++){
    $newparams = $temp[$i]; //testcase inputs
    $testcase_output1 = $testcase_output[$i];
    

    
    file_put_contents($file, $student_answer . "\n" . "print($student_funcname($newparams))");
    
    
    $runpython = exec("python $studentID.py");
    //echo "$runpython\n";
    //echo "$testcase_output\n";
    if ($runpython == $testcase_output1){
      $comments .= "Testcase output was correct~";
    }
    else{
      //if there is a python error then make $runpython = "python error".
          if($runpython == ""){
              $grade-=$testcasePointValue;
              $comments .= "Testcase incorrect. For $funcname($newparams), the expected output is $testcase_output1, the student output was a python error. $testcasePointValue points off~";
            }
            else{
            //$testcasePointValue = round($testcasePointValue, 0);
            $grade-=$testcasePointValue;
            $comments .= "Testcase incorrect. For $funcname($newparams), the expected output is $testcase_output1, the student output was $runpython. $testcasePointValue points off~";
      }
    }
    //testcase input/output loop would end here $funcname($newparams)
  }
  
  $carrot = "^";
  //$grade = round($grade, 0);
  $grade = $comments .= $carrot .= $grade;
  
  return $grade;

} 
?> 
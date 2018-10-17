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

$struct = $_POST [ "struct" ] ;
$UCID = $_POST [ "UCID" ] ;
$newQ = $_POST [ "newQ" ] ;
$testcase = $_POST [ "testcase" ] ;
$funcName = $_POST [ "funcName" ] ;
$parameters = $_POST [ "parameters" ] ;
$examQ = $_POST [ "examQ" ] ; //comma delimited
$examName = $_POST [ "examName" ] ;
$category = $_POST [ "category" ] ;
$difficulty = $_POST [ "difficulty" ] ;
$grade = $_POST [ "grade" ] ;
$answer = $_POST [ "answer" ] ;
$questionID = $_POST [ "questionID" ] ;
$parameterVal = $_POST [ "parameterVal" ] ;

if ($struct == 'addQuestion'){
	$s = "INSERT INTO questions (question,category,difficulty)
	VALUES ('$newQ', '$category','$difficulty')";
	mysqli_query($db,$s);
	$s = "INSERT INTO testcases (testcase,functionName,parameters,parameterVal) values ('$testcase', '$funcName', '$parameters', '$parameterVal')";
	mysqli_query($db,$s);
	echo "inserted";
}
if ($struct == 'makeExam'){
	$s = "insert into exams (examName) VALUES ('$examName')"; 
	mysqli_query($db,$s);
	$s="SELECT examID from exams where examName='$examName'";
	$t=mysqli_query($db,$s);
	$row=mysqli_fetch_row($t);
	$theExam = $row[0];
	$qArray = explode(',', $examQ);
	foreach($qArray as $entry){
		$s = "UPDATE questions
		SET examID = '$theExam'
		where questionID = '$entry'";
		mysqli_query( $db,  $s );
	}
	echo "made exam you ****";
}
if ($struct == 'takeExam'){
	$s="SELECT examID from exams where examName='$examName'";
	$t=mysqli_query($db,$s);
	$row=mysqli_fetch_row($t);
	$theExam = $row[0];
	//echo "the exam is $examName";
	$s="SELECT * from questions where examID='$theExam'";
	$t=mysqli_query($db,$s);
	while($row = mysqli_fetch_array($t)){
		$questionID = $row[0];
		$questionText = $row[1];
		$data[] = array(
			"questionID" => $questionID,
			"questionText" => $questionText,
			"examName"=> $theExam
			);
	}	
	echo json_encode($data);
	//put all examQ in string comma delimited
	//return the exam in an array using JSON
	}
if ($struct == 'showAll'){
	$s="SELECT * from questions";
	$t=mysqli_query($db,$s);
	while($row = mysqli_fetch_array($t)){
		$questionID = $row[0];
		$questionText = $row[1];
		$data[] = array(
			"questionID" => $questionID,
			"questionText" => $questionText
			);
	}
	echo json_encode($data);
}
if ($struct == 'storeAnswers'){
	//give me examName, examQ, UCID, answer
	$s="SELECT examID from exams where examName='$examName'";
	$t=mysqli_query($db,$s);
	$row=mysqli_fetch_row($t);
	$theExam = $row[0];
	$qArray = explode(',', $examQ);
	$aArray = explode('~', $answer);
	$count = count($qArray);
	//echo $count;
	for($i=0; $i < $count; $i++){
		$s = "INSERT INTO answers (examID,questionID,UCID,answer)
		VALUES ('$theExam', '$qArray[$i]','$UCID','$aArray[$i]')";
		mysqli_query($db,$s);
	}
}
if ($struct == 'gradeExam'){
	//try to grade quesiton by question instead of this stupid way
	$s="SELECT * from testcases where questionID='$questionID'";
	$t=mysqli_query($db,$s);
	$row=mysqli_fetch_row($t);
	$params = $row[3];
	$function = $row[2];
	$result = $row[1];
	$parameterVal = $row[4];
	//$send = [$params, $function, $questionID, $result, $parameterVal];
	echo '{"parameter":"'.$params.'", "functions":"'.$function.'", "questionID":"'.$questionID.'", "results":"'.$result.'", "parameterVal":"'.$parameterVal.'"}';
}
if ($struct == 'storeGrade'){
	$s = "UPDATE people
	SET betaGrade = '$grade'
	where UCID = '$UCID'";
	mysqli_query( $db,  $s );
}
if ($struct == 'releaseGrade'){
	$s="SELECT betaGrade from people where UCID='$UCID'";
	$t=mysqli_query($db,$s);
	$row=mysqli_fetch_row($t);
	$theGrade = $row[0];
	$s = "UPDATE people
	SET releaseGrade = '$theGrade'
	where UCID = '$UCID'";
	mysqli_query( $db,  $s );
}
if ($struct == 'viewGrade'){
	$s="SELECT releaseGrade from people where UCID='$UCID'";
	$t=mysqli_query($db,$s);
	$row=mysqli_fetch_row($t);
	$theGrade = $row[0];
	echo "You got a $theGrade";
}
mysqli_free_result($s);
mysqli_close($db);
exit();

?>
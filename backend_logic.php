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

if ($struct == 'addQuestion'){
	$s = "INSERT INTO questions (question,category,difficulty)
	VALUES ('$newQ', '$category','$difficulty')";
	mysqli_query($db,$s);
	$s = "INSERT INTO testcases (testcase,functionName,parameters)
	VALUES ('$testcase', '$funcName','$parameters')";
	mysqli_query($db,$s);
	//echo "inserted";
}
if ($struct == 'makeExam'){
	$s = "insert into exams (examName)
	VALUES ('$examName')"; 
	mysqli_query($db,$s);
	$s="SELECT examID from exams where examName='$examName'";
	$t=mysqli_query($db,$s);
	$row=mysqli_fetch_row($t);
	$theExam = $row[0];
	$qArray = explode(',', $examQ);
	foreach($qArray as $entry){
		$s = "UPDATE questions
		SET examID = '$theExam'
		where questionID = '$entry'"
		mysqli_query( $db,  $s );
	}
}
if ($struct == 'takeExam'){
	$s="SELECT examID from exams where examName='$examName'";
	$t=mysqli_query($db,$s);
	$row=mysqli_fetch_row($t);
	$theExam = $row[0];
	$s="SELECT question from questions where examID='$theExam'";
	$t=mysqli_query($db,$s);
	//return the exam in an array using JSON
	
}
if ($struct == 'storeAnswers'){
	//store answers and send testcases
}
if ($struct == 'gradeExam'){
	//store answers and send testcases
}
if ($struct == 'storeGrade'){
	$s = "UPDATE users
	SET betaGrade = '$grade'
	where UCID = '$UCID'"
	($t = mysqli_query( $db,  $s ));
}
if ($struct == 'releaseGrade'){
	$s="SELECT betaGrade from users where UCID='$UCID'";
	$t=mysqli_query($db,$s);
	$row=mysqli_fetch_row($t);
	$theGrade = $row[0];
	$s = "UPDATE users
	SET releaseGrade = '$theGrade'
	where UCID = '$UCID'"
	($t = mysqli_query( $db,  $s ));
}
if ($struct == 'viewGrade'){
	$s="SELECT releaseGrade from users where UCID='$UCID'";
	$t=mysqli_query($db,$s);
	$row=mysqli_fetch_row($t);
	$theGrade = $row[0];
	//return theGrade using JSON
}
mysqli_free_result($s);
mysqli_close($db);
exit();

?>
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
$examQ = $_POST [ "examQ" ] ;
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
	//echo "inserted";
	mysqli_free_result($s);
	mysqli_close($db);
	exit();
}
if ($struct == 'makeExam'){
	//so much logic
}
if ($struct == 'takeExam'){
	$s="SELECT examID from exams where name='$examName'";
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
if ($struct == 'storeGrade'){
	$s = "insert into users (betaGrade)
	VALUES ('$grade')"; 
	($t = mysqli_query( $db,  $s ));
}
if ($struct == 'releaseGrade'){
	$s="SELECT betaGrade from users where UCID='$UCID'";
	$t=mysqli_query($db,$s);
	$row=mysqli_fetch_row($t);
	$theGrade = $row[0];
	$s = "insert into users (releaseGrade)
	VALUES ('$theGrade')"; 
	($t = mysqli_query( $db,  $s ));
}
if ($struct == 'viewGrade'){
	$s="SELECT releaseGrade from users where UCID='$UCID'";
	$t=mysqli_query($db,$s);
	$row=mysqli_fetch_row($t);
	$theGrade = $row[0];
	//return theGrade using JSON
}

mysqli_close($db);
exit();

?>
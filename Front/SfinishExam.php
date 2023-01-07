<?php session_start();?>
<!doctype html>
<html lang="en">
  <head>
  	<title>View Grades</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link href ="nav.css" rel="stylesheet"/>
    <style>
     table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
    </style>

	</head>
	<body>
	<div class="topnav">
        <a href="student_landing.php">Student Home Page</a>
  		<a href="SViewExams.php">View Exams</a>
		<a href="SfinishExam.php">View Grades</a>
  		<a href= "index.php">Logout</a>
	</div>

	<section class="ftco-section">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-md-6 text-center mb-5">
					<h2 class="heading-section">View Exam Grades:</h2>
                    <table id='ExamResults' style="width:100%"></table>
				</div>
			</div>
	</section>
	
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
	</body>
</html>

<script>
window.onload = function() 
{ 
    var exams="<th>Exams Completed</th>";
    var xhttp = new XMLHttpRequest();
    var tableData = '';
    xhttp.open("POST","sendRubric.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.onreadystatechange = function(){
        if(xhttp.readyState == 4 && xhttp.status == 200){
            let result = this.responseText;
            console.log(result);
            var data = JSON.parse(result);
            console.log(data);
            var arrayLength = data.length;
            console.log(arrayLength);
            for (var i=0; i<arrayLength; i++){
                var examName = data[i]["exam_name"];
                tableData +='<tr id=\"'+examName+'\"><td>'+examName+'</td><td><input type="button" id="'+examName+'" onclick="changeURL(\'' + examName + '\')" value="View Grade"></td></tr>';     
            }
            document.getElementById('ExamResults').innerHTML = tableData;
        }
    }
   xhttp.send(tableData);
}
function changeURL(examName){
    location.href = 'SViewGrades.php/'+examName+'';
}

</script>
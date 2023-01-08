<?php session_start();?>
<!doctype html>
<html lang="en">
  <head>
  	<title>Rubric</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link href ="../nav.css" rel="stylesheet"/>
    <style>
     table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
    </style>

	</head>
	<body>
	<div class="topnav">
        <a href= "../student_landing.php">Student Home Page</a>
  		<a href="../SViewExams.php">View Exams</a>
		<a href="../SfinishExam.php">View Grades</a>
  		<a href= "../index.php">Logout</a>
	</div>

	<section class="ftco-section">
		<div class="container">
			<div class="row justify-content-center">		
					<h2 class="heading-section">Exam Rubric:</h2>
                    <div id='examResults' style="width:100%"></div>
			</div>
	</section>
	
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
	</body>
</html>

<script>
    var url = window.location.pathname;
    var id = url.substring(url.lastIndexOf('/') + 1); 
    var sentData = "";

window.onload = function(){
    getRubric(id); 

}

function getRubric(examName){
	var arr = {};
	arr["exam_name"]=examName; 
    var req = {};
    req["request_exam"]=arr;
    var tableData = '<h1 id="examName"></h1>';
	var rubricReq = new XMLHttpRequest();
	rubricReq.open("POST","sendRubric.php", true);

  rubricReq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    rubricReq.onreadystatechange = function()  {
        if (rubricReq.readyState == 4 && rubricReq.status == 200) {
			      var test = rubricReq.responseText;
            sentData = test;
            result = JSON.parse(test);
            console.log(result);
            result_auto = result["autograde"];
            var name_check = result_auto["name_check"];
            var test_cases = result_auto["test_case_r"];
            var question_ids = result_auto["question_ids"];
            var questions = result_auto["questions"];
            var total = result_auto["total"];
            var total_perQ = result_auto["total_perQ"];
            var constraint = result_auto["constraint_check"];
            var actual_totalperQ = result["points_perQ"];

            result_edit = result["edit_results"];
            var name_edit = result_edit["nameEdit"];
            var comment_edit = result_edit["comments_edit"];
            var total_perQ_edit= result_edit["total_perQ_edit"];
            var total = result["examTotal"];
            var test1Edit = result_edit["test1_edit"];
            var test2Edit = result_edit["test2_edit"];
            var test3Edit = result_edit["test3_edit"];
            var test4Edit = result_edit["test4_edit"];
            var test5Edit = result_edit["test5_edit"];
            var tests = [test1Edit,test2Edit,test3Edit,test4Edit,test5Edit];
            var conEdit = result_edit["constraintEdit"];
            
            var count= 1;
            for (var i=0; i<questions.length; i++) {
                var nameResult= name_check[i]["points_received"];
                var nameComment = name_check[i]["comment"];
                var question_id = question_ids[i];
                var question = questions[i];
                var nameE = name_edit[i];
                var commentE = comment_edit[i];
                var tp_E = total_perQ_edit[i];
                
                tableData += '<div><caption><label for="Question"> Question ' + (count)+ ': ' + question + '</label></caption></div><table class="table table-bordered" id="table" style="text-align:center;"><tbody><tr><th></th><th style="height:20%;width:40%;"colspan="2">Comments</th><th>Max Points</th><th>Auto Grade</th><th>Teacher Edit</th></tr><tr><th>Name Check</th><td style="height:20%;width:40%;" colspan="2">'+nameComment+'</td><td style="height:20%;width:10%;" colspan="1">5</td><td style="height:20%;width:10%;" colspan="1">'+nameResult+'</td><td style="height:20%;width:10%;" colspan="1">'+nameE+'</td></tr>';
                var p = actual_totalperQ[i]-5;
                if(Object.keys(constraint[i]).length !=0){
                    p -=5;
                  var conResult = constraint[i]["points_received"];
                  var conComment = constraint[i]["comment"];
                  tableData += '<tr><th>Constraint Check</th><td style="height:20%;width:40%;"colspan="2">'+conComment+'</td><td style="height:20%;width:10%;" colspan="1">5</td><td style="height:20%;width:10%;" colspan="1">'+conResult+'</td><td style="height:20%;width:10%;" colspan="1">'+conEdit[i]+'</td></tr>';
                }
                var countTest = 0;
                for(var j=1; j<=test_cases[i].length; j++){
                    countTest++;
                }
                var pointsLeft = p/countTest;
                for(var j=1; j<=test_cases[i].length; j++){
                    var testE = 0;
                    if(tests[j-1].length==questions.length){
                        testE = tests[j-1][i];
                    }
                    else if(tests[j-1].length==1){
                        testE = tests[j-1][0];
                    }
                    var testCase1 = test_cases[i][j-1]["case_"+j];
                    var testComment = test_cases[i][j-1]["comments"];
                    tableData += '<tr><th>Test Case '+j+'</th><td style="height:20%;width:40%;" colspan="2">'+testComment+'</td><td style="height:20%;width:10%;" colspan="1">'+pointsLeft+'</td><td style="height:20%;width:10%;" colspan="1">' + testCase1 + '</td><td style="height:20%;width:10%;" colspan="1">'+testE+'</td></tr>';
                }

                var total_perQes = total_perQ_edit[i];
                tableData += '<tr><th>Teacher Comments</th><td style="height:20%;width:40%;" colspan="2" value="'+commentE+'">'+commentE+'</td><td style="height:20%;width:10%;" colspan="1"></td><td style="height:20%;width:10%;" colspan="1"></td><td style="height:20%;width:10%;" colspan="1"></td></tr><tr><th>Total</th><td style="height:20%;width:40%;" colspan="2"></td><td style="height:20%;width:10%;" colspan="1"></td><td style="height:20%;width:10%;" colspan="1"></td><td style="height:20%;width:10%;" colspan="1" id="total" >'+total_perQes+'</td></tr></table>';
                 count = count +1;
            }
                tableData += '<h3>Total: '+total+'/100</h3>';
            document.getElementById('examResults').innerHTML = tableData;
         //   var b = document.getElementById('examTotal');
         //   examTotal(b);
		}

       // rubricReq.send(tableData);
    }
    rubricReq.send("json=" + encodeURIComponent(JSON.stringify(req)));

}


</script>
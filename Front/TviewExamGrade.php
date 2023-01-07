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
        <a href="../teacher_landing.php">Teacher Home Page</a>
  		<a href="../createQ_bank.php">Add Questions</a>
  		<a href="../createExam_selectQ.php">Create Exam</a>
        <a href="../TtakenExam.php">View Exams</a>
		<a href= "../index.php">Logout</a>
	</div>

	<section class="ftco-section">
		<div class="container">
			<div class="row justify-content-center">
			
					<h2 class="heading-section">Exam Rubric:</h2>
                    <div id='examResults' style="width:100%"></div>
                    <input type="text" onload="examTotal()" id="examTotal" >
                    <input type ="submit" value="Submit" id="sendResults" onclick="sendResults();">
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
    var constraintCheck = [];
    var autoTest = [];
window.onload = function(){
    getRubric(id); 

}

function getRubric(examName){
	var arr = {};
	arr["exam_name"]=examName; 
  var tableData = '<h1 id="examName"></h1>';
	var rubricReq = new XMLHttpRequest();
	rubricReq.open("POST","../examRubric.php", true);
  rubricReq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    rubricReq.onreadystatechange = function()  {
        if (rubricReq.readyState == 4 && rubricReq.status == 200) {
			      var test = rubricReq.responseText;
            sentData = test;
            console.log(test);
            result = JSON.parse(test);
           // console.log(result);
            var name_check = result["name_check"];
        //    console.log(name_check);
            var test_cases = result["test_case_r"];
            autoTest = test_cases;
        //    console.log(test_cases);
            var question_ids = result["question_ids"];
          //  console.log(question_ids)
            var questions = result["questions"];
        //    console.log(questions);
            var total = result["total"];
        //    console.log(total);
            var total_perQ = result["total_perQ"];
        //    console.log(total_perQ);
            var constraint = result["constraint_check"];
            constraintCheck = constraint;
      //      console.log(constraint);
            var actual_totalperQ = result["question_point_value"];
            var count= 1;
            for (var i=0; i<questions.length; i++) {
                var nameResult= name_check[i]["points_received"];
                var nameComment = name_check[i]["comment"];
                var question_id = question_ids[i];
                var question = questions[i];
                tableData += '<div><caption><label for="Question"> Question ' + (count)+ ': ' + question + '</label></caption></div>';
                tableData += '<table class="table table-bordered" id="table" style="text-align:center;"><tbody><tr><th></th><th style="height:20%;width:40%;"colspan="2">Comments</th><th>Max Points</th><th>Auto Grade</th><th>Teacher Edit</th></tr><tr><th>Name Check</th><td style="height:20%;width:40%;" colspan="2">'+nameComment+'</td><td style="height:20%;width:10%;" colspan="1">5</td><td style="height:20%;width:10%;" colspan="1">'+nameResult+'</td><td style="height:20%;width:10%;" colspan="1"><input type="number" min="1" max="100" style="text-align:center;" id="nameEdit" value="'+nameResult+'" onchange="nameEdit(this);"></td></tr>';
             //   console.log(constraint[i]);
                var p = actual_totalperQ[i]-5;

                if(Object.keys(constraint[i]).length !=0){
                  p -=5;
                  var conResult = constraint[i]["points_received"];
                  var conComment = constraint[i]["comment"];
                  tableData += '<tr><th>Constraint Check</th><td style="height:20%;width:40%;"colspan="2">'+conComment+'</td><td style="height:20%;width:10%;" colspan="1">5</td><td style="height:20%;width:10%;" colspan="1">'+conResult+'</td><td style="height:20%;width:10%;" colspan="1"><input type="number" min="1" max="100" id="constraintEdit" style="text-align:center;" value="'+conResult+'" onchange="constraintEdit(this);"></td></tr>';
                }
                var countTest = 0;
                for(var j=1; j<=test_cases[i].length; j++){
                  countTest ++;
                }
                var pointsLeft = p/countTest;
                for(var j=1; j<=test_cases[i].length; j++){
                    var testCase1 = test_cases[i][j-1]["case_"+j];
                    var testComment = test_cases[i][j-1]["comments"];
                    tableData += '<tr><th>Test Case '+j+'</th><td style="height:20%;width:40%;" colspan="2">'+testComment+'</td><td style="height:20%;width:10%;" colspan="1">'+pointsLeft+'</td><td style="height:20%;width:10%;" colspan="1">' + testCase1 + '</td><td style="height:20%;width:10%;" colspan="1"><input type="number" min="1" max="100" style="text-align:center;" id="testCase'+j+'Edit" value="'+testCase1+'" onchange="testCaseEdit(this);"></td></tr>';
                }

                var total_perQes = total_perQ[i];
                tableData += '<tr><th>Comments</th><td style="height:20%;width:40%;" colspan="2"><input type="text" style="width:100%;" id="commentsEdit" onchange="commentsEdit(this);"></td><td style="height:20%;width:10%;" colspan="1"></td><td style="height:20%;width:10%;" colspan="1"></td><td style="height:20%;width:10%;" colspan="1"></td></tr><tr><th>Total</th><td style="height:20%;width:40%;" colspan="2"></td><td style="height:20%;width:10%;" colspan="1"></td><td style="height:20%;width:10%;" colspan="1"></td><td style="height:20%;width:10%;" colspan="1" id="total" >'+total_perQes+'</td></tr></table>';
                 count = count +1;
            }
            document.getElementById('examResults').innerHTML = tableData;
            var b = document.getElementById('examTotal');
            examTotal(b);
		}

       // rubricReq.send(tableData);
    }
    rubricReq.send("json=" + encodeURIComponent(JSON.stringify(arr)));

}

function nameEdit(a){
   // var p = document.getElementById('nameEdit').value;
     var p = a.value;
     a.innerHTML = p;
     a.value = p;
    calcTotal();
 //  console.log(p);
}

function testCase1Edit(a){
  var p = a.value;
  a.innerHTML = p;
  a.value = p;
    calcTotal();
 // console.log(p);
}

function testCase2Edit(a){
   // var p = document.getElementById('testCase2Edit').value;
   var p = a.value;
    a.innerHTML = p;
    a.value = p;
    calcTotal();
  // console.log(p);

}
function testCase3Edit(a){
   // var p = document.getElementById('testCase2Edit').value;
   var p = a.value;
    a.innerHTML = p;
    a.value = p;
    calcTotal();
  // console.log(p);

}
function testCase4Edit(a){
   // var p = document.getElementById('testCase2Edit').value;
   var p = a.value;
    a.innerHTML = p;
    a.value = p;
    calcTotal();
  // console.log(p);

}
function testCaseEdit(a){
   // var p = document.getElementById('testCase2Edit').value;
   var p = a.value;
    a.innerHTML = p;
    a.value = p;
    calcTotal();
  // console.log(p);

}
function commentsEdit(a){
    //var p = document.getElementById('commentsEdit').value;
    var p = a.value;
    a.value = p;
 //  console.log(p);
}
function constraintEdit(a){
    var p = a.value;
     a.innerHTML = p;
     a.value = p;
    calcTotal();
}

function calcTotal(a){
   //var elements =  document.querySelectorAll('[id=total]');
   var allTable = document.querySelectorAll('[id=table]');
   // console.log(allTable);

   document.addEventListener('change', (event) => { 
        for(var i=0; i<allTable.length; i++){
         // console.log(allTable.length+" alltable");
          var table = allTable[i];
          //console.log(table);
          var body = table.getElementsByTagName("tbody");

          // console.log(body);
            var rows = body.item(0).children;
         // console.log(rows);
            var col = rows[0].children;
            var total = 0;
            for(var j=1; j<rows.length -2; j++){
                total += (Number(rows[j].children[4].children[0].value));
            }
            total = parseFloat(total).toFixed(2);
         //   var total = (nameEdit+testCase1+testCase2);
            var totalElm = rows[rows.length-1].children[4];
          //  console.log(totalElm);
            totalElm.innerHTML= String(total);
            totalElm.value = String(total);
        }
        var b = document.getElementById("examTotal");
        examTotal(b);  
    });
}

function examTotal(a){
    var questTotals = document.querySelectorAll('[id=total]');
    var t = 0;
    for(var i=0; i<questTotals.length; i++){
      var val = Number(questTotals[i].innerHTML);
      t+= val;

    }
    t = parseFloat(t).toFixed(2);
    a.value= String(t);
}


function sendResults(){
    var examName = id;

    var answers = [];
    var outerArr = {};
    var result = JSON.parse(sentData);
 

    outerArr["examName"]=examName;
    outerArr["autograde"]=result;

    var nameEdit = document.querySelectorAll('[id=nameEdit]');
    var constraintEdit = document.querySelectorAll('[id=constraintEdit]');
    var totalPerQEdit = document.querySelectorAll('[id=total]');
    var comments = document.querySelectorAll('[id=commentsEdit]');
    var n =[];
    var ct = [];
    var t1 = [];
    var t2 = [];
    var tQ = [];
    var c = [];

    var editResults = {};
    var x = {};
    for(var i=1; i<6; i++){
      var testCaseEdit = document.querySelectorAll('[id=testCase'+i+'Edit]');
      t = [autoTest.length];
      
      if(testCaseEdit){
        var j = 0;
        for(var k=0; k<autoTest.length; k++){
         
            if(i <= autoTest[k].length){
              t[k]=testCaseEdit[j].value;
              j ++;
            }
           else {
              t[k]=null;
            }
   
        }
        x["test"+i+"_edit"]=t;
      }
     
    }
    var constraintCounter = 0;

    for(var i =0; i<constraintCheck.length;i++){
      if(Object.keys(constraintCheck[i]).length !=0){
        if(constraintCounter != constraintEdit.length){
          ct.push(constraintEdit[constraintCounter].value);
          constraintCounter ++;
        }
       
      }
      else{
        ct.push(null);
      }
    }

    for (var i=0; i<nameEdit.length; i++) {
      n.push(nameEdit[i].value);
    }
    for(var j =0; j<totalPerQEdit.length; j++){
      tQ.push(totalPerQEdit[j].innerHTML);
      c.push(comments[j].value);
    }

    x["nameEdit"]=n;
    x["constraintEdit"]=ct;
    x["total_perQ_edit"]=tQ;
    x["comments_edit"]=c;

    outerArr["edit_results"]=x;
    var examTot = document.getElementById("examTotal").value;
    outerArr["examTotal"]=examTot;
    editResults["sendResults"]= outerArr;
    //console.log(editResults);
    
    var answerSend = new XMLHttpRequest();
    answerSend.open("POST","../sendRubric.php", true);
    answerSend.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    answerSend.onreadystatechange = function() 
    {
        if (answerSend.readyState == 4 && answerSend.status == 200) {
            console.log(answerSend.responseText);
        }      
    }
   // console.log(editResults);
    answerSend.send("json=" + encodeURIComponent(JSON.stringify(editResults))); 

    window.location.href="../teacher_landing.php";
}
</script>









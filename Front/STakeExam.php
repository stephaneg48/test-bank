<?php session_start();?>
<!doctype html>
<html lang="en">
  <head>
  	<title>Take Exam</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link href ="../nav.css" rel="stylesheet"/>

	</head>
	<body>
	<div class="topnav">
        <a href="../student_landing.php">Student Home Page</a>
  		<a href="../SViewExams.php">View Exams</a>
		<a href="SfinishExam.php">View Grades</a>
  		<a href= "../index.php">Logout</a>
	</div>

	<section class="ftco-section">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-md-6 text-center mb-5">
					<h2 class="heading-section">Start Exam!</h2>
            <!--       <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                        go to exam
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                        <li><a class="dropdown-item" href="#">Action</a></li>
                        <li><a class="dropdown-item" href="../STakeExam.php">view exams</a></li>
                        <li><a class="dropdown-item" href="#">Something else here</a></li>
                    </ul>
                    </div> 
                  <div id="questions"><h3>Questions</h3></div> 
                    <table id='examContent'></table> 
                    <input type ="submit" value="Submit Exam" id="examResults" onclick="sendExamResults()">-->
                    
				</div>
                <div id="exam">
                        <p id="qel"></p>
                        <p id="ael"></p>
                        <nav aria-label="Page navigation example">
                            <ul class="pagination" id="navigation">
                            </ul>
                        </nav>
                </div>
			</div>
	</section>
	
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
	</body>
</html>

<script>
   var url = window.location.pathname.split('/');
  // var qNum = Number(url[url.length-1]);
   var id =  url[url.length-1];
   var questions = {};
   var points = {};
   var qIDs = {};

   var tempAns = [];
    var quest = document.getElementById("qel");
    var ans = document.getElementById("ael");
    var d = document.getElementById("exam");
    var nav= document.getElementById("navigation");
window.onload = function() 
{ 
   // var tableData="<th>Number</th><th>Points</th><th>Questions</th><th>Answer</th>";
    var qbank_req = new XMLHttpRequest();
    qbank_req.open("POST","exams.php", true);
    qbank_req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    qbank_req.onreadystatechange = function() 
    {
        if (qbank_req.readyState == 4 && qbank_req.status == 200) 
        {
            let result = this.responseText;
             var data = JSON.parse(result);
         //   console.log(data);
            var keys = Object.keys(data);
            var arrayLength = Object.keys(data).length;
            for (var i=0; i<arrayLength; i++){
               var examID = keys[i];
               var examKeys = Object.keys(data[examID]);
               var examName = data[examID]["examName"];
               console.log(id);
                if(id===examName){
                    questions = data[keys[i]]["questions"];
                    points = data[keys[i]]["points"];
                    qIDs = data[keys[i]]["question_ids"];
                    var count= 1;
                    for(var j=0; j<questions.length;j++){
                        tempAns.push("");
                    }
                    if(1<=questions.length){
                        var q = questions[0];
                        var p = points[0];
                        var qID = qIDs[0];
                        var qel = '1. '+q+' ('+p+'pts)';
                        quest.innerHTML = qel;
                        var ael = '<textarea id="quest1" onchange="saveVal(this,1);" onkeydown="addTab(this,event);" style="height:300px;width:100%;font-size:14pt;" ></textarea>';
                        ans.innerHTML = ael;
                       
                    }  
                    var nv = '';
                    var span = '';
                    for(var j=1; j<=questions.length;j++){
                        if(j==1){
                            span = '<span class="sr-only">(current)</span>';
                        }

                        nv += '<li id="num" onclick="numQuestions('+j+');" value="'+j+'" class="page-item"><a class="page-link" >'+j+span+'</a></li>';
                        span = '';

                    }    
                    if(1==questions.length){
                        nv += '<button type="button" class="btn btn-info" style="float:right;" onclick="sendExamResults();">Submit</button>';
                    } 
                    else{
                        nv += '<li id="next" onclick="next(this);" value="2" class="page-item"><a class="page-link" >Next</a></li>'
                    }     
                    nav.innerHTML = nv;
                }
            }      
           // document.getElementById('examContent').innerHTML = tableData;

        }
    };
   qbank_req.send(qel);
  // addTab();
}

function addTab(a,e){
    var start = a.selectionStart;
    var end = a.selectionEnd;
    var val = a.value;
    var target = a;
    //console.log(a.selectionStart);
    if(e.keyCode == 9) {
      //  console.log(val);
        target.value = val.substring(0,start)+'\t'+val.substring(end);
        a.selectionStart = a.selectionEnd = start+1;
        e.preventDefault();
    }
    return false;
}

function numQuestions(a){

    if(a<=questions.length){
            var q = questions[a-1];
            var p = points[a-1];
            var qID = qIDs[a-1];
            var qel = a+'. '+q+' ('+p+'pts)';
            quest.innerHTML = qel;
            var ael = '<textarea id="quest'+a+'" onchange="saveVal(this,'+a+');" onkeydown="addTab(this,event);" style="height:300px;width:100%;font-size:14pt;" ></textarea>';
            ans.innerHTML = ael;
            var answer = document.getElementById("quest"+a+"");
            answer.innerHTML= tempAns[a-1];  
        }  
        var nv = '';
        var span = '';
        //backbutton
        if(a!=1){
            nv += '<li id="prev" onclick="prev(this);" value="'+(a-1)+'" class="page-item"><a class="page-link" >Back</a></li>'
        }  
        for(var j=1; j<=questions.length;j++){
            if(j==1){
                span = '<span class="sr-only">(current)</span>';
            }
            nv += '<li id="num" onclick="numQuestions('+j+');" value="'+j+'" class="page-item"><a class="page-link" >'+j+span+'</a></li>';
            span = '';
        }    
        if(a==questions.length){
            nv += '<button type="button" class="btn btn-info" style="float:right;" onclick="sendExamResults();">Submit</button>';
        } 
        else{
            nv += '<li id="next" onclick="next(this);" value="'+(a+1)+'" class="page-item"><a class="page-link" >Next</a></li>'
        }   
        nav.innerHTML = nv;  
}

function next(a){
    var nextVal = a.value;
    numQuestions(nextVal);
}
function prev(a){
    var backVal = a.value;
    numQuestions(backVal);
}
function saveVal(el,a){
    tempAns[a-1]=el.value;
  //  console.log(tempAns);
}
function sendExamResults(){  
    
    var examName = id;
 //   var studentAnswer = document.querySelectorAll('[name=Sanswer]');
    var answers = [];
    var outerArr = {};
    for(var i=0; i<tempAns.length;i++){
        var tempArr = {};
        tempArr["exam_name"]=examName;
        var value = tempAns[i];
        tempArr["answer"]= value;
        var questionID = qIDs[i];
        tempArr["question_id"]=questionID;
        answers.push(tempArr);
    }
    outerArr["sendResults"]= answers;
    var answerSend = new XMLHttpRequest();
    answerSend.open("POST","sendExamResults.php", true);
    answerSend.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    answerSend.onreadystatechange = function() 
    {
        if (answerSend.readyState == 4 && answerSend.status == 200) {
            console.log(answerSend.responseText);
        }      
    }
    console.log(outerArr);
   answerSend.send("json=" + encodeURIComponent(JSON.stringify(outerArr))); 
   window.location.href="../student_landing.php";
}
</script>

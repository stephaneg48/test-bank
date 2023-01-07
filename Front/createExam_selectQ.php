<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Creating Question</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <link href ="split.css" rel="stylesheet"/>
        <link href ="nav.css" rel="stylesheet"/>
        <title>Exam Form</title>
        <style>
            table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
            }
        </style>
    </head> 
    <body>
        <div class="topnav">
            <a href="teacher_landing.php">Teacher Home Page</a>
  		    <a href="createQ_bank.php">Add Questions</a>
            <a class="active" href="#createExam">Create Exam</a>
            <a href="TtakenExam.php">View Exams</a>
            <a href= "index.php">Logout</a>
	    </div>
        <div class="row">
        <div class="col-lg-6 col-sm-12 left">
        <div class="col-md-6 text-center mb-5">
        <div id= "examFormBody" class="examformBody">
            <p>
            <h1>Create Exam</h1>
            <form id="createExam" class="createExam">
                <label for="Exam Name"></label>
                    <input type="text" id="examName" name="examName" placeholder="Exam Name" onchange="checkExamName()"><br><br>
                    <div id="questionArea"></div> 
                    <table id='examQ'></table>
                    <div>Total Points: </div>
                    <input type = "text" id="totalPoints">
            </form>
           <!-- <input disabled type ="submit" value="Submit" id="examSubmit" onclick="sendExam()">-->
                <button disabled type="button" class="btn btn-info"  id="examSubmit" onclick="sendExam()">Submit</button> 
            </p>
        </div>
        </div>
        </div>
            <div class="col-lg-6 col-sm-12 right">
            <div class="row justify-content-center">  
                <p>
                    <h1>Select Questions</h1>
                </p> 
            </div>

            <div class="row justify-content-center"> 
                    <select class="form-select" id="categorySearch" aria-label="Default select example">
                        <option value="">Category</option>
                        <option value="Conditional">Conditional</option>
                        <option value="Recursion">Recursion</option>
                        <option value="Lists">Lists</option>
                        <option value="Strings">Strings</option>
                        <option value="Loop">Loop</option>
                        <option value="Arithmetic">Arithmetic</option>
                    </select>

                    <select class="form-select form-select-sm" id="diffSearch" aria-label=".form-select-sm example">
                        <option value="">Difficulty</option>
                        <option value="easy">Easy</option>
                        <option value="medium">Medium</option>
                        <option value="hard">Hard</option>
                    </select>

                    <input type="search" class="form-control rounded w-25 p-3" id="key" placeholder="Search" aria-label="Search" aria-describedby="search-addon"/>
                    <button type="submit" class="btn btn-outline-info" id="searchQuery2">search</button>

                    <script src ="searchSelect.js" type="text/JavaScript"></script>

                    <table id='questions' style="width:70%;height:10%;"></table> 
                    <br><br>          
            </div>

            <div class="row justify-content-center">  
                <button type="button" class="btn btn-info"  id="questionSubmit" onclick="sendQuestion()">Submit</button>       
            </div>
        </div>
    </body>  
</html> 

<script>
window.onload = function() 
{ 

    var tableData="<th>ID</th><th>Questions</th><th>Category</th><th>Difficulty</th>";
    var qbank_req = new XMLHttpRequest();
    qbank_req.open("POST","questions.php", true);
    qbank_req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    qbank_req.onreadystatechange = function() 
    {
        if (qbank_req.readyState == 4 && qbank_req.status == 200) 
        {
            console.log(qbank_req.responseText);
            result = qbank_req.responseText;
            result2 = result.split("]");
            result = result2[0];
            result = result + "]";
            result = JSON.parse(result);
            console.log(result);
            var count= 1;
            for (var i=0; i<result.length; i++) 
            {
                var data= result[i]; // each entry
                
                //console.log(data);
                if(data.question !== ""){
                    tableData +='<tr id=\"'+data.id+'\"><td>'+(count)+'</td><td>'+data.question+'</td><td>'+data.category+'</td><td>'+data.difficulty
                    +'</td><td><input type=\"checkbox\" id=\"'+data.id+'\" name=\"check\" value=\"'+
                    data.question+'\"></td></tr>';
                    count = count +1;
                }
            }
            
            document.getElementById('questions').innerHTML = tableData;
        }

    };
   qbank_req.send(tableData);
}

function sendQuestion(){
    var array = []
    var checkboxes = document.querySelectorAll('input[type=checkbox]:checked');
    for (var i = 0; i < checkboxes.length; i++) {
    var data = [];
    data.push(checkboxes[i].id);
    data.push(checkboxes[i].value);
    array.push(data);
    }
    displayQuestion(array);
    //console.log(array);
}

function displayQuestion(array){
    var tableData= "<th>Question</th><th> Assign Points</th>";
    for (var i = 0; i < array.length; i++) {
        var id = array[i][0];
        var question = array[i][1];
        tableData += '<tr name="quest" id=\"'+id+'\"><td>'+question+'</td><td><input type="number" min="1" max="100" id=\"points\" onchange="addPoints();"></td></tr>';
    }
    document.getElementById('examQ').innerHTML = tableData;
}

function addPoints(){
    var p = document.querySelectorAll('[id=points]');
    var total = 0;
    const submitbutton = document.getElementById('examSubmit');
    for (var i = 0; i < p.length; i++) {
        var points = p[i];
        var val = parseInt(points.value);
        total += val;   
        if(!isNaN(total)){
            document.getElementById('totalPoints').value=total;
        }
        if(total == 100 && checkExamName()){
            submitbutton.disabled=false;
        }
        else{
            submitbutton.disabled=true;
        }
    }
}

function checkExamName(){
    const examName = document.getElementById('examName');
    const submitbutton = document.getElementById('examSubmit');
    if(examName.value !== ''){
        submitbutton.disabled=false;
        return true;
     }
    else{
        submitbutton.disabled=true;
        return false;
    }
}

function sendExam(){  
    var p = document.querySelectorAll('[id=points]');
    var examName = document.getElementById('examName');
    var questions = document.querySelectorAll('[name=quest]');
    var arr = [];
    var outerArray = {};
    for (var i = 0; i < p.length; i++){
        var tempArray = {};
        tempArray["examName"] = examName.value;
        tempArray["points"] = p[i].value;
        tempArray["questionId"] = questions[i].id;
        arr.push(tempArray);
    }
    outerArray["examSend"]=arr;
    var examSend = new XMLHttpRequest();
    examSend.open("POST","exams.php", true);
    examSend.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    examSend.onreadystatechange = function() 
    {
        if (examSend.readyState == 4 && examSend.status == 200) {
            console.log(examSend.responseText);
        }      
    }
    examSend.send("json=" + encodeURIComponent(JSON.stringify(outerArray)));
}
</script>
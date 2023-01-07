<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Creating Question</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <link href ="split.css" rel="stylesheet"/>
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
            <a href="teacher_landing.php">Teacher Home Page</a>
            <a class="active" href="#createQ">Add Questions</a>
  		    <a href="createExam_selectQ.php">Create Exam</a>
            <a href="TtakenExam.php">View Exams</a>
            <a href= "index.php">Logout</a>
	    </div>
        <div class="row">
        <div class="col-lg-6 col-sm-12 left">
        <div id = "Question_Main" class="Question">
        <div class="row justify-content-center">
            <p>
                <h1>Create Question</h1>
                <form id = "questionForm">
                <label for="Question Difficulty">Choose the Question Difficulty:</label>
                <select name="difficulty" id="difficulty">
                    <option value="easy">Easy</option>
                    <option value="medium">Medium</option>
                    <option value="hard">Hard</option>
                </select>
                <br><br>
                <textarea id="questionDescription" name="questionDescription" rows="8" cols="50" placeholder="Enter your question here"></textarea><br><br>
                <label for="question function">Enter function call here:</label>
                <input type="text" id="questionFunction" name="question function" size="30"><br><br>
                <label for="AskTestCase">How many test cases?:</label>
                <select name="AskTestCase" id="AskTestCase" onchange="testCases(this);">
                    <option value="2"> 2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
                    <br><br>  
                <div id="testCase" class="testCase">
                    <label for="test case">Enter Test Case 1:</label>
                    <input type="text" id="testcase1" name="testcase1" placeholder="Test Case" size="50"><br>
                    <label for="test case output">Enter Test Case 2:</label>
                    <input type="text" id="testcase2" name="testcase2"placeholder="Test Case" size="50">
                    <br><br>
                </div>
                <label for="QuestionCateogory">Choose Category:</label>
                <select name="QuestionCateogory" id="questionCateogory">
                    <option value="Loop">Loop</option>
                    <option value="Conditional">Conditional</option>
                    <option value="Recursion">Recursion</option>
                    <option value="Lists">Lists</option>
                    <option value="Strings">Strings</option>
                    <option value="Arithmetic">Arithmetic</option>
                </select>
                    <br><br> 
                <label for="QuestionConstraint">Choose the Constraint:</label>
                <select name="QuestionConstraint" id="QuestionConstraint">
                    <option value=""></option>
                    <option value="For"> For </option>
                    <option value="While">While </option>
                    <option value="Recursion">Recursion</option>
                    <option value="None">None</option>
                </select>
                    <br><br>  
                    <input type="submit" class="btn btn-info"  id="addQ" value="Submit">
                </form>
                <script src ="addQuestion.js" type="text/JavaScript"></script>
            </p>
        </div>

        </div>
        </div>
            <div class="col-lg-6 col-sm-12 right">
            <div class="row justify-content-center">
                <p>
                    <h1>Question Bank</h1>
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
                <button  class="btn btn-outline-info" id="searchQuery">search</button>
                <script src ="addQuestion.js" type="text/JavaScript"></script>

                <table id='questions' style="width:70%;height:10%;"></table>
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
       //     console.log(qbank_req.responseText);
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
                
               // console.log(data);
                if(data.question !== ""){
                    tableData += '<tr><td>'+(count)+'</td><td>'+data.question+'</td><td>'+data.category+'</td><td>'+data.difficulty+'</td></tr>';
                    count = count +1;
                }
            }
            
            document.getElementById('questions').innerHTML = tableData;
        }

    };
    qbank_req.send(tableData);
}

function testCases(a){
    var n= Number(a.value);
    var inputs= "";
    for(var i=1;i<=n;i++){
        inputs += '<label for="test case">Enter Test Case '+i+':</label><input type="text" id="testcase'+i+'" name="testcase'+i+'" placeholder="Test Case" size="50"><br></br>'
    }
    document.getElementById('testCase').innerHTML = inputs;
}
</script>
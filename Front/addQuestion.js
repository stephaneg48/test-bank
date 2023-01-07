
var search = document.getElementById("searchQuery");
if(search){
    search.addEventListener("click", Search);
    function Search(event){
        event.preventDefault();
       // console.log("here")
        let key = document.getElementById("key");
        let category = document.getElementById("categorySearch");
        let diff = document.getElementById("diffSearch");
        if(category && diff){
            var cat = category.value;
            var d = diff.value;
            var k = "";
            if(key){
                k = key.value;
            }
            var sendInfo = {"request":"true","category": cat, "difficulty": d, "key": k};
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function(){
                if(xhttp.readyState == 4 && xhttp.status == 200){
                    var tableData="<th>ID</th><th>Questions</th><th>Category</th><th>Difficulty</th>";
                    result = xhttp.responseText;
                    result2 = result.split("]");
                    result = result2[0];
                    result = result + "]";
                    result = JSON.parse(result);

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
            }
            xhttp.open("POST", "questions.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    
            xhttp.send("json=" + encodeURIComponent(JSON.stringify(sendInfo)));
    
        }

       // console.log(key);

    }
}




var addQ = document.getElementById("addQ");
if(addQ){
    addQ.addEventListener("click", Submit);
    function Submit(event){
        event.preventDefault();
        event.stopImmediatePropagation();
       // addQ.disabled=true;
      //  addQ.value = 'sending...please wait';
      //  this.form.submit;
    //    console.log("here");
        let questionDescription = document.getElementById("questionDescription").value;
        let difficulty = document.getElementById("difficulty").value;
        let questionFunction = document.getElementById("questionFunction").value;
        let testcase1 = document.getElementById("testcase1").value;
        let testcase2 = document.getElementById("testcase2").value;
        console.log(testcase1);
        console.log(testcase2);
        let testcase3 = "";
        let testcase4 = "";
        let testcase5 = "";
        let questionCateogory = document.getElementById("questionCateogory").value;
        let QuestionConstraint = document.getElementById("QuestionConstraint").value;
       // console.log(testcase1);
        if(document.getElementById("testcase3")){
            testcase3 = document.getElementById("testcase3").value;
        }
        if(document.getElementById("testcase4")){
            testcase4 = document.getElementById("testcase4").value;
        }
        if(document.getElementById("testcase5")){
            testcase5 = document.getElementById("testcase5").value;
        }

        if(questionDescription == ""){
            alert("Please enter question description!");
        }
        else if(questionFunction == ""){
            alert("Please enter function call");
        }
        else if(testcase1 == ""){
            alert("Please enter test case 1");
        }
        else if(testcase2 == ""){
            alert("Please enter test case 2");
        }
        else{
        // let request = {"request": false};
            var sendInfo = {"request":"false","questionDescription": questionDescription,
            "difficulty": difficulty,
            "questionFunction": questionFunction,
            "testcase1": testcase1,
            "testcase2": testcase2,
            "testcase3": testcase3,
            "testcase4": testcase4,
            "testcase5": testcase5,
            "questionCateogory": questionCateogory,
            "QuestionConstraint": QuestionConstraint  
            };
        //    console.log(sendInfo);
            var out = [];
        // out.push(request);
        // out.push(sendInfo);
        //  console.log(out);
        // console.log(out);
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function(){
                if(xhttp.readyState == 4 && xhttp.status == 200){
                    if(xhttp.responseText == "Question Added!"){
                        console.log(xhttp.responseText);
                    }
               //     console.log(xhttp.responseText);
                }
            }
            xhttp.open("POST", "questions.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            xhttp.send("json=" + encodeURIComponent(JSON.stringify(sendInfo)));

        } 
    }

}
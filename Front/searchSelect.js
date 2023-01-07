
var search = document.getElementById("searchQuery2");
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
                            tableData +='<tr id=\"'+data.id+'\"><td>'+(count)+'</td><td>'+data.question+'</td><td>'+data.category+'</td><td>'+data.difficulty
                                +'</td><td><input type=\"checkbox\" id=\"'+data.id+'\" name=\"check\" value=\"'+
                                data.question+'\"></td></tr>';
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
<?php session_start();?>
<!doctype html>
<html lang="en">
  <head>
  
  	<title>Auto Grader</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
	<link rel="stylesheet" href="bootstrap-4.3.1-dist/css/style.css">
	<link rel="stylesheet" href="bootstrap-4.3.1-dist/css/bootstrap.css">
	<link rel="stylesheet" href="bootstrap-4.3.1-dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="bootstrap-4.3.1-dist/css/styles.css">

	</head>
	<body>
	<section class="ftco-section">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-md-6 text-center mb-5">
					<?php
						if (isset($_SESSION['errorMessage'])){
							echo "<span style='color:red;'>Credentials are invalid</span>";
							unset($_SESSION['errorMessage']);
						}
					?>
				</div>
			</div>
			<div class="row justify-content-center">
				<div class="col-md-7 col-lg-5">
					<div class="login-wrap p-4 p-md-5">
					<i class=""></i>
		      	<h3 class="text-center mb-4">Login Page</h3>
						<form method ="post" class="login-form" action= "./logindata.php" id="myform">
		      		<div class="form-group">
						<h7 class="mb-4">UCID: </h7>
		      			<input type="text" class="textbox form-control rounded-left" id="ucid" name="ucid" placeholder="Ucid" required />
		      		</div>
	            	<div class="form-group">
						<h7 class="mb-4">Password: </h7>
	              		<input type="password" class="textbox form-control rounded-left" id="password" name="password" placeholder="Password" required />
	            	</div>
	            <div class="form-group">
	            	<button type="submit" class="form-control btn btn-dark rounded submit px-3" name="submit" id="submit" />Login</button>
	            </div>
				</form>
	        </div>
				</div>
			</div>
		</div>
	</section>
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
	</body>
</html>











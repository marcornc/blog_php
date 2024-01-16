<?php

	require "functions.php";

	if ($_SERVER['REQUEST_METHOD'] == "POST") {

		$email = addslashes($_POST['email']);
		$password = addslashes($_POST['password']);
;

		$query = "select * 
		  from users
		  where email = '$email' 
		  && password = '$password' 
		  limit 1";

		$result = mysqli_query($con,$query);

		
		//print_r($result->num_rows);

		if ($result->num_rows > 0) {

			$row = mysqli_fetch_assoc($result);

			$_SESSION['info'] = $row;

			header("Location: profile.php");
			die;

		}else{
			$error = "wrong email or password";
		}


	}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>login - my website</title>
</head>
<body>


	<?php include "header.php"; ?>

	<div style="margin: auto; max-width: 600px;">

		<?php 

			if (!empty($error)) {
				echo "<div>".$error."</div>";
			}

		?>

			<h2 style="text-align: center; padding: 10px;">Login</h2>

		<form method="post" style="margin: auto; padding: 10px;">
		
			<input type="email" name="email" placeholder="Email" required><br>
			<input type="password" name="password" placeholder="Password" required><br>

			<button>Signup</button>
		</form>
	</div>

	<?php include "footer.php"; ?>



</body>
</html>


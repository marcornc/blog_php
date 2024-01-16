	<style type="text/css">

		*{
			padding: 0;
			margin: 0;
			box-sizing: border-box;
		}

		body{
			background-color: #e5e5d3d4;
			font-family: tahoma;
		}

		header{
			background-color: #7e7ecf;

			display: flex;
			justify-content: center;
			align-items: center;
		}

		header div{
			padding: 20px;
		}

		header a{
			color: white;
		}

		a{
			text-decoration: none;
		}

		input, textarea{
			margin: 4px;
			padding: 8px;

			width: 100%;
		}

		button{
			padding:10px;
			cursor: pointer;
		}
	</style>

	<header>
		<div><a href="index.php">Home</a></div>
		<div><a href="profile.php">Profile</a></div>

		<?php if (empty($_SESSION['info'])): ?>
			<div><a href="login.php">Login</a></div>
			<div><a href="signup.php">Signup</a></div>

		<?php else:?>
				<div><a href="logout.php">Logout</a></div>
				
		<?php endif;?>
	</header>
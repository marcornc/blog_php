<?php

require "functions.php";

check_login();

//////////////////////////////////////////////////////
//////////////////////////////////////////////////////
/////////		DELETE
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////

if ($_SERVER['REQUEST_METHOD'] == "POST" && !empty($_POST['action']) && $_POST['action'] == 'delete') {

	$id = $_SESSION['info']['id'];
	$query = "DELETE FROM users 
                WHERE id = '$id' 
                LIMIT 1";
	$result = mysqli_query($con, $query);

	if (file_exists($_SESSION['info']['image'])) {
		unlink($_SESSION['info']['image']);
	}

	/////////////////		TO-DO		////////////////////		
	//Delete also all the pictures that are been up-load with the posts

	$query = "DELETE FROM posts
				where user_id = '$id'";
	$result = mysqli_query($con, $query);

	header("Location: logout.php");
	die;
}

//////////////////////////////////////////////////////
//////////////////////////////////////////////////////
/////////		EDIT
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////

elseif (
	$_SERVER['REQUEST_METHOD'] == "POST"
	&& !empty($_POST['username'])
) {
	$image_added = false;

	if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] == 0 && $_FILES['image']['type'] == "image/jpeg") {
		$folder = "uploads/";

		if (!file_exists($folder)) {
			mkdir($folder, 0777, true);
		}

		$image = $folder . $_FILES['image']['name'];

		move_uploaded_file($_FILES['image']['tmp_name'], $image);

		if (file_exists($_SESSION['info']['image'])) {
			unlink($_SESSION['info']['image']);
		}

		$image_added = true;
	}

	$username = addslashes($_POST['username']);
	$email = addslashes($_POST['email']);
	$password = addslashes($_POST['password']);
	$id = $_SESSION['info']['id'];

	if ($image_added == true) {
		$query = "UPDATE users 
                    SET username = '$username',  
                    email = '$email',  
                    password = '$password',  
                    image = '$image' 
                    WHERE id = '$id' 
                    LIMIT 1";
	} else {
		$query = "UPDATE users 
                    SET username = '$username',  
                    email = '$email',  
                    password = '$password' 
                    WHERE id = '$id' 
                    LIMIT 1";
	}

	$result = mysqli_query($con, $query);

	$query = "SELECT *
            FROM users
            WHERE id = '$id'
            LIMIT 1";
	$result = mysqli_query($con, $query);

	if ($result->num_rows > 0) {
		$_SESSION['info'] = mysqli_fetch_assoc($result);
	}

	header("Location: profile.php");
	die;
}

//////////////////////////////////////////////////////
//////////////////////////////////////////////////////
/////////		DELETE POST
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////

elseif ($_SERVER['REQUEST_METHOD'] == "POST" && !empty($_POST['action']) && $_POST['action'] == 'post_delete') {

	$id = $_GET['id'] ?? 0;
	$user_id = $_SESSION['info']['id'];

	$query = "SELECT * 
				FROM posts 
                WHERE id = '$id'
				&& user_id = '$user_id' 
                LIMIT 1";
	$result = mysqli_query($con, $query);

	if (mysqli_num_rows($result) > 0) {

		$row = mysqli_fetch_assoc($result);

		if (file_exists($row['image'])) {
			unlink($row['image']);
		}
	}

	$query = "DELETE FROM posts 
				WHERE id = '$id'
				&& user_id = '$user_id' 
				LIMIT 1";
	$result = mysqli_query($con, $query);



	/////////////////		TO-DO		////////////////////		
	//Delete also all the pictures that are been up-load with the posts

	header("Location: profile.php");
	die;
}

//////////////////////////////////////////////////////
//////////////////////////////////////////////////////
/////////		EDIT POST
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////

elseif ($_SERVER['REQUEST_METHOD'] == "POST" && !empty($_POST['action']) && $_POST['action'] == 'post_edit') {

	$image_added = false;

	$id = $_GET['id'] ?? 0;
	$user_id = $_SESSION['info']['id'];
}

if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] == 0 && $_FILES['image']['type'] == "image/jpeg") {


	$folder = "uploads/";

	if (!file_exists($folder)) {
		mkdir($folder, 0777, true);
	}

	$image = $folder . $_FILES['image']['name'];

	move_uploaded_file($_FILES['image']['tmp_name'], $image);

	//delete the old image
	$query = "SELECT * 
		FROM posts 
		WHERE id = '$id'
		&& user_id = '$user_id' 
		LIMIT 1";
	$result = mysqli_query($con, $query);

	if (mysqli_num_rows($result) > 0) {

		$row = mysqli_fetch_assoc($result);

		if (file_exists($row['image'])) {
			unlink($row['image']);
		}


		$image_added = true;
	}

	$post = addslashes($_POST['post']);

	if ($image_added == true) {
		$query = "UPDATE posts 
                    SET post = '$post' 
                    image = '$image' 
                    WHERE id = '$id'
					&& user_id = '$user_id' 
                    LIMIT 1";
	} else {
		$query = "UPDATE users 
                    SET post = '$post' 
                    WHERE id = '$id'
					&& user_id = '$user_id' 
                    LIMIT 1";
	}

	$result = mysqli_query($con, $query);

	header("Location: profile.php");
	die;
}

//////////////////////////////////////////////////////
//////////////////////////////////////////////////////
/////////		POST
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////

elseif (
	$_SERVER['REQUEST_METHOD'] == "POST"
	&& !empty($_POST['post'])
) {

	$image = "";

	if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] == 0 && $_FILES['image']['type'] == "image/jpeg") {
		$folder = "uploads/";

		if (!file_exists($folder)) {
			mkdir($folder, 0777, true);
		}

		$image = $folder . $_FILES['image']['name'];

		move_uploaded_file($_FILES['image']['tmp_name'], $image);
	}

	$user_id = $_SESSION['info']['id'];
	$post = addslashes($_POST['post']);
	$date = date('Y-m-d H:i:s');

	$query = "INSERT INTO posts (user_id, post, image, date) 
            VALUES ('$user_id', '$post', '$image', '$date')";

	$result = mysqli_query($con, $query);

	header("Location: profile.php");
	die;
}

?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Profile - my website</title>
</head>

<body>
	<?php include "header.php"; ?>

	<div style="margin: auto; max-width: 600px;">

		<!-- ////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////
        ///////////////		EDIT
        ////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////// -->

		<?php if (!empty($_GET['action']) && $_GET['action'] == 'edit') : ?>

			<h2 style="text-align: center; padding: 10px;">Edit Profile </h2>

			<form method="post" enctype="multipart/form-data" style="margin: auto; padding: 10px;">

				<img src="<?php echo $_SESSION['info']['image']; ?>" style="width: 100px; height: 100px; object-fit: cover; margin: auto; display: block;">

				image: <input type="file" name="image"><br>
				<input value="<?php echo $_SESSION['info']['username']; ?>" type="text" name="username" placeholder="Username" required><br>
				<input value="<?php echo $_SESSION['info']['email']; ?>" type="email" name="email" placeholder="Email" required><br>
				<input value="<?php echo $_SESSION['info']['password']; ?>" type="text" name="password" placeholder="Password" required><br>

				<button type="post">Save</button>
				<a href="profile.php"><button type="button">Cancel</button></a>
			</form>

			<!-- ////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////
        ///////////////		POST EDIT 
        ////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////// -->

		<?php elseif (!empty($_GET['action']) && $_GET['action'] == 'post_edit' && !empty($_GET['id'])) : ?>

			<?php

			$id = (int)$_GET['id'];
			$query = "SELECT * 
						FROM posts 
						WHERE id = '$id' 
						LIMIT 1";
			$result = mysqli_query($con, $query);

			?>

			<?php if (mysqli_num_rows($result) > 0) : ?>
				<?php $row = mysqli_fetch_assoc($result); ?>
				<h2 style="text-align: center; padding: 10px;">Edit Your Post </h2>

				<form method="post" enctype="multipart/form-data" style="margin: auto; padding: 10px;">
					<img src="<?= $row['image'] ?>" style="width: 100%; height: 200px; object-fit: cover;"><br>
					image: <input type="file" name="image" value="post_edit"><br>

					<textarea name="post" rows="8"><?= $row['post'] ?></textarea><br>

					<button>Save</button>
					<a href="profile.php"><button type="button">Cancel</button></a>
				</form>
			<?php endif; ?>

			<!-- ////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////
        ///////////////		POST DELETE 
        ////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////// -->

		<?php elseif (!empty($_GET['action']) && $_GET['action'] == 'post_delete' && !empty($_GET['id'])) : ?>

			<?php

			$id = (int)$_GET['id'];
			$query = "SELECT *  
					FROM posts 
					WHERE id = '$id'
					LIMIT 1";
			$result = mysqli_query($con, $query);

			?>

			<?php if (mysqli_num_rows($result) > 0) : ?>
				<?php $row = mysqli_fetch_assoc($result); ?>
				<h2 style="text-align: center; padding: 10px;">Are you sure you want to delete this post? </h2>

				<form method="post" enctype="multipart/form-data" style="margin: auto; padding: 10px;">
					<img src="<?= $row['image'] ?>" style="width: 100%; height: 200px; object-fit: cover;"><br>

					<div><?= $row['post'] ?></div><br>
					<input type="hidden" name="action" value="post_delete">
					<button>Delete</button>
					<a href="profile.php"><button type="button">Cancel</button></a>
				</form>
			<?php endif; ?>


			<!-- ////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////
        ///////////////		DELETE
        ////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////// -->

		<?php elseif (!empty($_GET['action']) && $_GET['action'] == 'delete') : ?>

			<h2 style="text-align: center; padding: 10px;">Are you sure you want to delete your profile? </h2>

			<div style="margin: auto; max-width: 600px; text-align: center;">
				<form method="post" enctype="multipart/form-data" style="margin: auto; padding: 10px;">

					<img src="<?php echo $_SESSION['info']['image']; ?>" style="width: 100px; height: 100px; object-fit: cover; margin: auto; display: block;"><br>
					<div><?php echo $_SESSION['info']['email']; ?></div>
					<div><?php echo $_SESSION['info']['password']; ?></div>

					<input type="hidden" name="action" value="delete">

					<button type="post">Delete</button>
					<a href="profile.php"><button type="button">Cancel</button></a>
				</form>
			</div>

			<!-- ////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////
    ///////////////		 REGULAR PROFILE
    ////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////// -->

		<?php else : ?>

			<h2 style="text-align: center; padding: 10px;">User Profile</h2>
			<br>
			<div style="margin: auto; max-width: 600px; text-align: center;">

				<div>
					<img src="<?php echo $_SESSION['info']['image']; ?>" style="width: 150px; height: 150px; object-fit: cover;">
				</div>

				<div>
					<?php echo $_SESSION['info']['username'] ?>
				</div>

				<div>
					<?php echo $_SESSION['info']['email'] ?>
				</div>

				<a href="profile.php?action=edit">
					<button>Edit Profile</button>
				</a>

				<a href="profile.php?action=delete">
					<button>Delete Profile</button>
				</a>

			</div>
			<br>
			<hr>
			<h5>Create a post:</h5>

			<form method="post" enctype="multipart/form-data" style="margin: auto; padding: 10px;">

				image: <input type="file" name="image"><br>

				<textarea name="post" rows="8"></textarea><br>

				<button>Post</button>
			</form>
			<hr>


			<!-- /////////////////////////////////////
    ///////////////////////////////////////
    ///////////////		POST SECTION
    ///////////////////////////////////////
    /////////////////////////////////////// -->

			<div>
				<?php
				$id = $_SESSION['info']['id'];
				$query = "SELECT * FROM posts WHERE user_id = '$id' ORDER BY id DESC limit 10";
				$result = mysqli_query($con, $query);
				?>

				<?php if (mysqli_num_rows($result) > 0) : ?>
					<?php while ($row = mysqli_fetch_assoc($result)) : ?>
						<?php


						$user_id = $row['user_id'];
						$query = "SELECT username, image FROM users WHERE id = '$user_id' limit 1";
						$user_result = mysqli_query($con, $query);

						$user_row = mysqli_fetch_assoc($user_result);


						?>
						<div style="display: flex; border: solid thin #aaa; border-radius: 10px; margin: 10px 0; background-color: white;">
							<div style="flex: 1; text-align: center;">
								<img src="<?= $user_row['image'] ?>" style="margin: 10px; width: 100px; height: 100px; object-fit: cover; border-radius: 50%;">
								<div><?= $user_row['username'] ?></div>
								<div style="color: #888;"><?= date("jS M, Y", strtotime($row['date'])) ?></div>
							</div>
							<div style="flex: 8;">
								<?php if (file_exists($row['image'])) : ?>
									<div>
										<img src="<?= $row['image'] ?>" style="width: 100%; height: 200px; object-fit: cover;">
									</div>
								<?php endif; ?>

								<div>
									<?= nl2br(htmlspecialchars($row['post'])) ?>
									<br><br>
									<a href="profile.php?action=post_edit&id=<?= $row['id'] ?>">
										<button>Edit</button>
									</a>

									<a href="profile.php?action=post_delete&id=<?= $row['id'] ?>">
										<button>Delete</button>
									</a>
								</div>
							</div>
						</div>
					<?php endwhile; ?>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>

	<?php include "footer.php"; ?>
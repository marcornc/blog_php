<?php

require "functions.php";

check_login();

?>


<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>my website</title>
</head>

<body>

	<?php include "header.php"; ?>

	<div style="margin: auto; max-width: 600px;">
		<h3 style="text-align: center;">Timeline</h3>
		<?php
		$id = $_SESSION['info']['id'];
		$query = "SELECT * FROM posts ORDER BY date DESC limit 10";
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
					<div style="flex: 1;">
						<img src="<?= $user_row['image'] ?>" style="margin: 10px; width: 100px; height: 100px; object-fit: cover; border-radius: 50%;">
						<div style="text-align: center;"><?= $user_row['username'] ?></div>
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

						</div>
					</div>
				</div>

			<?php endwhile; ?>
		<?php endif; ?>

		<?php include "footer.php"; ?>

	</div>

</body>

</html>
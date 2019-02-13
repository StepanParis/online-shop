<!doctype html>
<html lang="en">
	<head>
		<!-- Required meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css"
			  integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css"
			  integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
		<link rel="stylesheet" href="css/style.css">
		<title>Admin | Login</title>
	</head>
	<body>
		<div class="login-form">
			<?php if(!empty($error_text)) { ?>
				<div class="alert alert-danger" role="alert">
					<?php echo $error_text; ?>
				</div>
			<?php } ?>
			<form method="POST" class="container text-left">
				<input type="hidden" name="act" value="login" />
				<div class="form-group">
					<label for="username" class="h6">Username</label>
					<input type="text" class="form-control" name="username" placeholder="Enter username" required>
				</div>
				<div class="form-group">
					<label for="password" class="h6">Password</label>
					<input type="password" class="form-control" name="password" placeholder="*****" required>
				</div>
				<button type="submit" class="btn btn-primary">Login</button>
			</form>
		</div>
		<script src="https://code.jquery.com/jquery-3.3.1.min.js" i
				ntegrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"
				integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"
				integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
	</body>
</html>
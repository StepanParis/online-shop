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
		<title>Admin | Orders</title>
	</head>
	<body>
		<ul class="nav">
			<li class="nav-item">
				<span class="navbar-text p-2" title="Logged in as"><?php echo $user_name; ?></span>
			</li>
			<li class="nav-item">
				<a class="nav-link active p-2" href="admin.php?act=logout" title="Logout">&#8592; Logout</a>
			</li>
		</ul>
		<?php if($orders !== false) { ?>
			<?php if($orders_count > 0) { ?>
				<table class="table table-bordered">
					<caption>List of orders (<?php echo $orders_count; ?> pcs)</caption>
					<thead class="bg-light">
						<tr>
							<th scope="col" rowspan="2" class="text-center align-middle">#</th>
							<th scope="col" rowspan="2" class="text-center align-middle">Date / Time</th>
							<th scope="col" rowspan="2" class="text-center align-middle">Shopper ID</th>
							<th scope="col" colspan="3" class="text-center align-middle">Products</th>
						</tr>
						<tr>
							<th scope="col" class="text-center align-middle">Bali</th>
							<th scope="col" class="text-center align-middle">France</th>
							<th scope="col" class="text-center align-middle">Japan</th>
						</tr>
					</thead>
				  <tbody>
						<?php foreach($orders as $index => $order) { ?>
							<tr>
								<th scope="row" class="text-center align-middle bg-light"><?php echo $index+1; ?></th>
								<td class="text-center align-middle"><?php echo $order['datetime']; ?></td>
								<td class="text-center align-middle"><?php echo $order['shopper']; ?></td>
								<td class="text-center align-middle"><?php echo $order['product_1'] == 1 ?
                                        '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-minus text-danger"></i>';?>
                                </td>
								<td class="text-center align-middle"><?php echo $order['product_2'] == 1 ?
                                        '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-minus text-danger"></i>';?>
                                </td>
								<td class="text-center align-middle"><?php echo $order['product_3'] == 1 ?
                                        '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-minus text-danger"></i>';?>
                                </td>
							</tr>	
						<?php } ?>
					</tbody>
				</table>
			<?php } else { ?>
				<div class="alert alert-info" role="alert">
                    No data to display
				</div>
			<?php } ?>
		<?php } else { ?>
			<div class="alert alert-danger" role="alert">
                An error has occurred
			</div>
		<?php } ?>
		<script src="https://code.jquery.com/jquery-3.3.1.min.js" i
				ntegrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"
				integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"
				integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
	</body>
</html>
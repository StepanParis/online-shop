<?php
	
	//Сonnect the configuration
	require_once('../config/common.php');
	
	//User screen width
	$screen_width = isset($_POST['screen_width']) ? $_POST['screen_width'] : null;
	//User screen height
	$screen_height = isset($_POST['screen_height']) ? $_POST['screen_height'] : null;
	//Product Information
	$products = isset($_POST['products']) ? json_decode($_POST['products'],true) : null;
	//User ID
	$identifier = get_user_identifier($screen_width,$screen_height);
	
	if(count($products)) { //If the list of products is received
		if(insert_to_db($identifier,$products)) { //If it was successful to add an entry to the database
			if(insert_to_file()) { //If it was successfully write a JSON file
				response(false,'Data successfully saved');
			} else {
				response(true,'Data is stored in the database, but could not be written to the file');
			}
		} else {
			response(true,'Could not save data to DB');
		}
	} else {
		response(true,'No product data');
	}
	
	//create user id
	function get_user_identifier($screen_width,$screen_height) {
		$identifier = null;
		//User IP
		$ip = get_ip();
		//User Agent
		$user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
		//Hash user ID
		$identifier = md5($ip.$user_agent.$screen_width.$screen_height);
		return $identifier;
	}
	
	//Get the user's IP (taken from the Internet)
	function get_ip() {
		$ip = null;
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
	
	//Add data to the database
	function insert_to_db($identifier,$products) {
		$result = false;
		if(file_exists('../'.DB_FILE_PATH)) {
			$db = new SQLite3('../'.DB_FILE_PATH);
			if(!$db){
				return false;
			} else {
				$query = 'INSERT INTO orders_list (ol_timestamp, ol_shopper, ol_product_1, ol_product_2, ol_product_3)
						  VALUES (:timestamp, :shopper, :product_1, :product_2, :product_3)';
				$script = $db->prepare($query);
				$script->bindValue(':timestamp', time());
				$script->bindValue(':shopper', $identifier);
				$script->bindValue(':product_1', isset($products['product_1']) ? $products['product_1'] ? 1 : 0 : 0);
				$script->bindValue(':product_2', isset($products['product_2']) ? $products['product_2'] ? 1 : 0 : 0);
				$script->bindValue(':product_3', isset($products['product_3']) ? $products['product_3'] ? 1 : 0 : 0);
				$insert = $script->execute();
				if($insert === false) {
					$result = false;
				} else {
					$result = true;
				}
			}
			$insert->finalize();
			$db->close();
		} else {
			$result = false;
		}
		return $result;
	}
	
	//Writing data to a JSON file
	function insert_to_file() {
		$result = false;
		if(file_exists('../'.DB_FILE_PATH)) {
			$db = new SQLite3('../'.DB_FILE_PATH);
			if(!$db){
				$result = false;
			} else {
				$query = 'SELECT ol_shopper, ol_product_1, ol_product_2, ol_product_3 FROM orders_list';
				$script = $db->prepare($query);
				$orders = $script->execute();
				if($orders === false) {
					$result = false;
				} else {
					$json = array();
					while($order = $orders->fetchArray(SQLITE3_ASSOC)) {
						$products = array();
						if($order['ol_product_1'])
							$products[] = 'Product 1';
						if($order['ol_product_2'])
							$products[] = 'Product 2';
						if($order['ol_product_3'])
							$products[] = 'Product 3';
						$json[] = array(
							'shopper' => $order['ol_shopper'],
							'products' => $products
						);
					}
					if(file_put_contents('../'.JSON_FILE_PATH, json_encode($json)) === false) {
						$result = false;
					} else {
						$result = true;
					}
				}
			}
			$orders->finalize();
			$db->close();
		} else {
			$result = false;
		}
		return $result;
	}
	
	//Script response
	function response($has_errors,$text) {
		//Give the answer in JSON format in JS-script
		echo json_encode(array(
			'has_errors' => $has_errors,
			'text' => $text
		));
		exit;
	}
	
?>
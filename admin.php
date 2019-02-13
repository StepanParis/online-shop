<?php
	
	//Сonnect the configuration
	require_once('config/common.php');
	
	//Error text
	$error_text = null;
	
	//Check the authorization
	list($user_name,$is_auth) = check_auth();
	if($is_auth) { //If authorized
		if(isset($_GET['act']) AND $_GET['act'] == 'logout') { //If the exit button is pressed
			//Delete the authorization data
			remove_auth();
			//Display the page template with authorization form
			header('Location: admin.php');
		} else { //If the exit button is not pressed
			//Get the list of orders from the database
			$orders = get_orders();
			//Count of orders
			$orders_count = count($orders);
			//Display the page template with orders
			include('templates/orders.php');
		}
	} else { //If not authorized
		if(isset($_POST['act']) AND $_POST['act'] == 'login') { //If there is an authorization attempt
			//User login
			$username = isset($_POST['username']) ? $_POST['username'] : null;
			//User password
			$password = isset($_POST['password']) ? $_POST['password'] : null;
			if(!empty($username) AND !empty($password)) { //If both login and password are transmitted
				//Trying to login on
				list($user_name,$try_auth) = set_auth($username,$password);
				if($try_auth) { //If successfully logged in
					//Get the list of orders from the database
					$orders = get_orders();
					//Count of orders
					$orders_count = count($orders);
					//Display the page template with orders
					include('templates/orders.php');
				} else {
					$error_text = 'Could not log in';
					//Display the page template with authorization form
					include('templates/login.php');
				}
			} else {
				$error_text = 'You must specify a username and password';
				//Display the page template with authorization form
				include('templates/login.php');
			}
		} else {
			//Display the page template with authorization form
			include('templates/login.php');
		}
	}
	
	//Check the authorization
	function check_auth() {
		if(isset($_COOKIE[AUTH_COOKIE_NAME])) { //If there is an authorization cookie
			if(file_exists(DB_FILE_PATH)) { //If there is a file from the database
				$db = new SQLite3(DB_FILE_PATH); //Connect to the database
				if(!$db){
					return array(null,false);
				} else { //If succeeded
					$user_ip = get_ip(); //Get user IP
					//SQL-request
					$query = 'SELECT ap_id, ap_username FROM admin_profile WHERE ap_ip = "'.$user_ip.'" 
					          AND ap_auth_cookie = "'.$_COOKIE[AUTH_COOKIE_NAME].'" LIMIT 1';
					$script = $db->prepare($query);
					$select = $script->execute();
					if($select === false) { //If failed
						//Remove cookie by setting time in the past
						setcookie(AUTH_COOKIE_NAME, '', time()-3600);
						return array(null,false);
					} else {
						$user_data = $select -> fetchArray(SQLITE3_ASSOC);
						if(!empty($user_data)) {
							return array($user_data['ap_username'],true);
						} else {
							return array(null,false);
						}
					}
				}
				$select->finalize();
				$db->close();
			} else {
				return array(null,false);
			}
		} else {
			return array(null,false);
		}
	}
	
	//Authorization in the system
	function set_auth($username,$password) {
		if(!isset($_COOKIE[AUTH_COOKIE_NAME])) {
			if(file_exists(DB_FILE_PATH)) {
				$db = new SQLite3(DB_FILE_PATH);
				if(!$db){
					return array(null,false);
				} else {
					$user_ip = get_ip();
					$password_hash = md5($password.AUTH_SALT);
					$query = 'SELECT ap_id, ap_username FROM admin_profile WHERE ap_username = "'.$username.'" 
					          AND ap_password_hash = "'.$password_hash.'" LIMIT 1';
					$script = $db->prepare($query);
					$select = $script->execute();
					if($select === false) {
						return array(null,false);
					} else {
						$user_data = $select -> fetchArray(SQLITE3_ASSOC);
						if(!empty($user_data)) {
							//Hash cookie authorization
							$auth_cookie = md5($user_ip.date('d.m.Y_H:i:s').get_random_string());
							$query = 'UPDATE admin_profile SET ap_ip = "'.$user_ip.'", ap_auth_cookie = "'.$auth_cookie.'"
									  WHERE ap_id = '.$user_data['ap_id'].'';
							$script = $db->prepare($query);
							$update = $script->execute();
							if($update === false) {
								return array(null,false);
							} else {
								setcookie(AUTH_COOKIE_NAME, $auth_cookie, time()+3600);
								return array($user_data['ap_username'],true);
							}
						} else {
							return array(null,false);
						}
					}
				}
				$select->finalize();
				$db->close();
			} else {
				return array(null,false);
			}
		} else {
			return array(null,false);
		}
	}
	
	//Sign Out
	function remove_auth() {
		if(isset($_COOKIE[AUTH_COOKIE_NAME])) {
			//Remove cookie by setting time in the past
			setcookie(AUTH_COOKIE_NAME, '', time()-3600);
		}
	}
	
	//Get the list of orders from the database
	function get_orders() {
		if(file_exists(DB_FILE_PATH)) {
			$db = new SQLite3(DB_FILE_PATH);
			if(!$db){
				return false;
			} else {
				$query = 'SELECT * FROM orders_list ';
				$script = $db->prepare($query);
				$orders = $script->execute();
				if($orders === false) {
					return false;
				} else {
					$data = array();
					while($order = $orders->fetchArray(SQLITE3_ASSOC)) {
						$data[] = array(
							'datetime' => date('d.m.Y H:i:s',$order['ol_timestamp']),
							'shopper' => $order['ol_shopper'],
							'product_1' => $order['ol_product_1'],
							'product_2' => $order['ol_product_2'],
							'product_3' => $order['ol_product_3']
						);
					}
					return $data;
				}
			}
			$orders->finalize();
			$db->close();
		} else {
			return false;
		}
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
	
	//Get random string (taken from the Internet)
	function get_random_string($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
?>
<?php
	ob_start();
	session_start();
	//https://github.com/IQAndreas/php-spam-filter
	require_once 'spamfilter.php';

	if(isset($_SESSION['username'])) {
		$username = $_SESSION['username'];
		$fromuser = $_GET['user'];
		$id = $_GET['id'];
		$comment = (string)$_POST['comment'];	
		if (strlen($_POST['comment']) < 2) {
			header('Location:report.php');
		}	
		else {
			$filter = new SpamFilter();
			$result = $filter->check_text($comment);
			if ($result) {
				$m = new MongoClient();
				$db = $m -> map;
				$collection = $db -> spammers;
				$isSpammer = $collection -> count(array('username' => $username));
				if (!$isSpammer) {
					$collection -> insert(array('username' => $username, 'count' => 1));
				}
				else {
					$isSpammer = $collection -> find(array('username' => $username));
					foreach ($isSpammer as $c) {
						if($c["count"] < 3) {
							$collection -> update(array('username' => $username), array('$inc' => array("count" => 1)));
						}
						else {
							$collection = $db -> users;
							$collection -> remove(array('username' => $username));
						}
					}
				}

				$collection = $db -> attempts;
				$incorrectAttempts = $collection -> count(array("ip" => $_SERVER['REMOTE_ADDR']));
				if ($incorrectAttempts) {
					$attempts = $collection -> find(array("ip" => $_SERVER['REMOTE_ADDR']));
					foreach ($attempts as $attempt) {
						$wrongAttempts = $attempt["count"];
						if ($wrongAttempts < 3) {
							$collection -> update(array("ip" => $_SERVER['REMOTE_ADDR']), array('$inc' => array("count" => 1)));
						}
						else {
							$collection -> update(array("ip" => $_SERVER['REMOTE_ADDR']), array('$set' => array("blockedAt" => new MongoDate(), "blocked" => 1)));
							$collection->ensureIndex(array('blockedAt' => 1), array('expireAfterSeconds' => 900));
							$_SESSION["locked"] = 1;
						}
					}
				}
				else {
					$collection -> insert(array("ip" => $_SERVER['REMOTE_ADDR'], "count" => 1));
				}

				$_SESSION['spam'] = 1;
				header('Location:login.php');
			}
			else {
				if(strcmp($username, $fromuser) == 0) {
					$m = new MongoClient();
					$db = $m -> map;
					$collection = $db -> reports;
					$idArray = array('_id' => new MongoId($id));
					$cursor = $collection -> find($idArray);
					foreach ($cursor as $doc) {
						$collection -> update($idArray, 
							array('$push' => array("comments" => $comment, 
								"commenters" => $username)
							)
						);
					}
				}
				$collection = $db -> attempts;
				$incorrectAttempts = $collection -> count(array("ip" => $_SERVER['REMOTE_ADDR']));
				if ($incorrectAttempts) {
					$collection -> update(array('ip' => $_SERVER['REMOTE_ADDR']), array('$set' => array('count' => 0)));
				}
				header('Location:report.php');
			}
		}
		
	}
	else {
		header('Location:login.php');
	}
?>

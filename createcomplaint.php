<?php
	session_start();
	if(isset($_SESSION['username'])) {
		$username = $_SESSION['username'];
		$m = new MongoClient();
		$db = $m -> map;
		$collection = $db -> reports;
		$title = htmlspecialchars(($_POST["title"]));
		$description = str_replace("\n", "<br/>", nl2br($_POST["description"]));
		$location = $_POST["location"];
		$coords = $_POST["coords"];
		$dt = new DateTime(date('Y-m-d'), new DateTimeZone('UTC'));
		$ts = $dt->getTimestamp();
		$today = new MongoDate($ts);
		$report = array('votes' => 0, 'voters' => array(), 'title' => $title, 'description' => $description, 'location' => $location, 'coords' => $coords, 'username' => $username, 'time' => new MongoDate());
		$collection -> insert($report);
		header('Location:report.php');
	}
	else {
		header('Location:login.php');
	}
?>
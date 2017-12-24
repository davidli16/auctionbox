<?php

include ('./mysqlvars.php');
$db = mysqli_connect($host,$username,$password,$dbname);
if (mysqli_connect_errno()) {
	printf("Connect failed: %s\n", mysqli_connect_error());
	exit();
}

function query($q) {
	global $db;

	print "<!--$q-->";
	$result = $db->query($q);
	if(!$result)
		print("<!--Database Error: -- $q -- ".$db->error."-->");

	return $result;
}

function clean($data) {
	global $db;
	
	$data = strip_tags($data);
	$data = htmlentities($data);
	$data = $db->real_escape_string($data);
	$data = trim($data);
	return $data;
}

function set_time() {
	$month = $_POST['month'];
	$day = $_POST['day'];
	$year = $_POST['year'];
	$hour = $_POST['hour'];
	$minute = $_POST['minute'];
	$second = $_POST['second'];
	$selectedtime = $month."-".$day."-".$year." ".$hour.":".$minute.":".$second;

	query("UPDATE State SET currenttime = STR_TO_DATE('$selectedtime', '%b-%d-%Y %H:%i:%s')");
}

function prev_day() {
	query("UPDATE State SET currenttime = currenttime - INTERVAL 1 DAY");	
}

function next_day() {
	query("UPDATE State SET currenttime = currenttime + INTERVAL 1 DAY");	
}

function set_user() {
	global $db;
	
	$user = $_REQUEST['user'];

	// step 1: turn off auto-commit
	$result = $db->autocommit(FALSE);
	if (!$result) {
		set_error("Could not disable autocommit");
		$db->rollback();  // if error, roll back transaction
		return null;
	}

	// step 2: Create the user
	$result = query("INSERT INTO Users(UserID, Rating) VALUES('$user', 0) ON DUPLICATE KEY UPDATE Rating=Rating");
	if (!$result) {
		set_error("Could not register your account");
		$db->rollback();  // if error, roll back transaction
		return null;
	}

	// step 3: Update the current user of the system
	$result = query("UPDATE State SET user = '$user'");
	if (!$result) {
		set_error("Login unsuccessful");
		$db->rollback();  // if error, roll back transaction
		return null;
	}

	// step 4: assuming no errors, commit transaction
	$result = $db->commit();
	if (!$result) {
		set_error("Could not commit changes");
		$db->rollback();  // if error, roll back transaction
		return null;
	}
}

function logout() {
	query("UPDATE State SET user = NULL");
}

function create_auction() {
	global $user;
	global $sql_time;
	global $db;
	
	if(!isset($_REQUEST['name']) || !isset($_REQUEST['description']) || !isset($_REQUEST['min'])) 	{
		set_error("You did not fill in all the fields");
		return null;
	}

	$month = $_POST['month'];
	$day = $_POST['day'];
	$year = $_POST['year'];
	$hour = $_POST['hour'];
	$minute = $_POST['minute'];
	$second = $_POST['second'];
	$selectedtime = $month."-".$day."-".$year." ".$hour.":".$minute.":".$second;

	$name = clean($_REQUEST['name']);
	$description = clean($_REQUEST['description']);
	$min = floatval($_REQUEST['min']);
	$buy = (isset($_REQUEST['buy']) ? floatval($_REQUEST['buy']) : 'NULL');
	$categories = array();
	for($i = 1; $i <= 4; $i++) { 
		if(isset($_REQUEST['category'.$i])) {
			$categories[] = $_REQUEST['category'.$i];
		}
	}

	// step 1: turn off auto-commit
	$result = $db->autocommit(FALSE);
	if (!$result) {
		set_error("Could not disable autocommit");
		$db->rollback();  // if error, roll back transaction
		return null;
	}

	// step 2: Generate an itemID
	$result = query("SELECT MAX(ItemID) FROM Items");
	if (!$result) {
		set_error("Could not generate an auction");
		$db->rollback();  // if error, roll back transaction
		return null;
	}
	
	$row = $result->fetch_assoc();
	$next_id = $row['MAX(ItemID)'] + 1;

	// step 3: Add the item
	$result = query("INSERT INTO Items VALUES($next_id, '$name', $buy, $min, $min, $sql_time, STR_TO_DATE('$selectedtime', '%b-%d-%Y %H:%i:%s'), '$user', '$description')");
	if (!$result) {
		set_error("Could not create auction");
		$db->rollback();  // if error, roll back transaction
		return null;
	}
	
	// step 4: check relevant constraint(s)
	$result = query("CALL verify_time()");
	if (!$result) {
		set_error("Your auction can't start after it finishes");
		$db->rollback();  // if error, roll back transaction
		return null;
	}
	
	// step 5: Add the categories
	$sql = "INSERT INTO Categories VALUES";
	$first = true;
	foreach($categories AS $category) {
		if(!$first) $sql .= ",";
		$sql .= "($next_id, '$category')";
		$first = false;
	}
	$result = query($sql);
	if (!$result) {
		set_error("Could not add category");
		$db->rollback();  // if error, roll back transaction
		return null;
	}
	
	// step 6: assuming no errors, commit transaction
	$result = $db->commit();
	if (!$result) {
		set_error("Could not commit changes");
		$db->rollback();  // if error, roll back transaction
		return null;
	}
	
	return $next_id;
}

function place_bid() {
	global $user;
	global $db;
	global $sql_time;

	$item = intval($_POST['item']);
	$amount = floatval($_POST['bid']);

	// step 1: turn off auto-commit
	$result = $db->autocommit(FALSE);
	if (!$result) {
		set_error("Could not disable autocommit");
		$db->rollback();  // if error, roll back transaction
		return null;
	}

	// step 2: Enter the bid
	$result = query("INSERT INTO Bids VALUES($item, '$user', $sql_time, $amount)");
	if (!$result) {
		set_error("Could not insert bid");
		$db->rollback();  // if error, roll back transaction
		return null;
	}

	// step 3: Update the items table (second constraint)
	$result = query("UPDATE Items SET Currently = (SELECT MAX(Amount) FROM Bids WHERE ItemID = $item)");
	if (!$result) {
		set_error("Could not update the auction");
		$db->rollback();  // if error, roll back transaction
		return null;
	}
	
	// step 4: check relevant constraint(s)
	$result = query("CALL verify_bids()");
	if (!$result) {
		set_error("Your bid is invalid");
		$db->rollback();  // if error, roll back transaction
		return null;
	}
	
	// step 5: assuming no errors, commit transaction
	$result = $db->commit();
	if (!$result) {
		set_error("Could not commit changes");
		$db->rollback();  // if error, roll back transaction
		return null;
	}
}
?>
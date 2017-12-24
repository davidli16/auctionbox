<?php
include ('lib.php');
include ('lib_db.php');

/* Handles important changes */
switch($_do) {
	case "set_time": set_time(); break;
	case "set_user": set_user(); break;
	case "prev_day": prev_day(); break;
	case "next_day": next_day(); break;
	case "logout": logout(); break;
}

/* Get the current time and user */
$result = query("select user, currenttime from State");
$row = $result->fetch_assoc();
$cur_time = new DateTime($row['currenttime']);
$sql_time = "STR_TO_DATE('".$cur_time->format("M-d-y H:i:s")."', '%b-%d-%Y %H:%i:%s')";
$user = $row['user'];
$result->free();

/* Start printing the page! */
print '<?xml version="1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
	"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<title>AuctionBox</title>
	<link rel="stylesheet" type="text/css" href="reset-min.css" />
	<link rel="stylesheet" type="text/css" href="master.css" />
	<script type="text/javascript" src="js/curvycorners.js"></script>
	<script type="text/javascript" src="js/jquery-1.2.6.min.js"></script>
	<script type="text/javascript" src="js/jquery.timer.js"></script>
	<script type="text/javascript" src="js/jquery.jqURL.js"></script>
</head>

<body>

<div id="header">
	<div id="menu">
		<ul>
			<li><a href="?">Search</a></li>
			<li><a href="?action=browse">Browse</a></li>
			<li><a href="?action=create">Create</a></li>
			<li><a href="?action=kill">Reconnect</a></li>
			<?php if($user != null): ?>
			<li><a href="?<?=$_query?>do=logout">Logout</a></li>
			<?php endif;?>
		</ul>
	</div>
	<div id="login">
		<?php if($user != null): ?>
			Welcome, <a href="?action=user&user=<?=$user?>"><?=$user?></a>. It is currently <?=$cur_time->format("F j, Y, g:i:s a")?>.
		<?php else: ?>
			<form method="POST" action="index.php?<?=$_query?>">
				<input type="hidden" name="do" value="set_user" />
				<label>Please enter your name</label>
				<input type="text" name="user" value="Dr. Clock"  onfocus="if(this.value == 'Dr. Clock') this.value=''" onblur="if(this.value.length == 0) this.value = 'Dr. Clock'" /><input type="submit" value="Login" />
			</form>
		<?php endif; ?>
	</div>
</div>

<div id="footer">
<?php include 'config_time.php'; ?>
</div>	

<div id="content">
<?php
	/* Handles other actions */
	switch($_do) {
		case "place_bid": place_bid(); break;
	}
	if($error != null)
		print '<script type="text/javascript" src="js/master.js"></script>';

	if($error != null) {
		print '<div id="error" class="message">'.$error.'</div>';
		$error = null;
	}

	switch($_action) {
		case "kill": include 'kill.php'; break;
		case "browse": include 'browse.php'; break;
		case "user": include 'user.php'; break;
		case "create": include 'create.php'; break;
		case "view": include 'view.php'; break;
		default: include 'main.php'; break;
	}

?>
</div>
<div class="clear"></div>
</body>
</html>

<?php
$db->close();
?>
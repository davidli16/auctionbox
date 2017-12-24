<?php
$view_user = $_REQUEST['user'];
$user_result = query("SELECT * FROM Users WHERE UserID = '$view_user'");
$bid_result = query("SELECT Bids.ItemID, Name, Amount, DATE_FORMAT(Bid_Time, '%M %d, %Y, %r') AS Bid_Time FROM Bids, Items WHERE Items.ItemID = Bids.ItemID AND Bidder = '$view_user' AND Bid_Time <= $sql_time ORDER BY Bid_Time DESC LIMIT 10");
$items_result = query("SELECT DISTINCT Items.ItemID AS ItemID, Name, Seller, Ends, Started, COUNT(Bid_Time) AS BidCount FROM Items LEFT JOIN Bids ON Bids.ItemID = Items.ItemID WHERE Started <= $sql_time AND Seller = '$view_user' GROUP BY Items.ItemID ORDER BY Started LIMIT 10");

$view_user = $user_result->fetch_assoc();
?>

<div id="narrow-content">
<h1>User Profile</h1>
<table id="item-info">
	<tr>
		<td class="heading">User</td>
		<td><?=$view_user['UserID']?></td>
	</tr>
	<tr>
		<td class="heading">Rating</td>
		<td><?=$view_user['Rating']?></td>
	</tr>
	<tr>
		<td class="heading">Location</td>
		<td><?=$view_user['Location'] == null ? 'Unknown' : $view_user['Location']?></td>
	</tr>
	<tr>
		<td class="heading">Country</td>
		<td><?=$view_user['Location'] == null ? 'Unknown' : $view_user['Country']?></td>
	</tr>
</table>


<h1>Recent Auctions</h1>

<table id="listing">
	<thead>
		<th class="item">Item</th>
		<th class="bids">Bids</th>
	</thead>
<?php if($items_result->num_rows == 0): ?>
	<tr><td class="spanning" colspan="3"><?=$view_user['UserID']?> has not yet started any auctions.</td></tr>
<?php else: ?>
<?php
	while($row = $items_result->fetch_assoc()):
		$ends = new DateTime($row['Ends']);
		if($ends->format("U") > $cur_time->format("U")) {
			$differences = get_time_difference($cur_time->format('M-d-y H:i:s'),$ends->format('M-d-y H:i:s'));
			$status = $differences['days'].' days, '.$differences['hours'].' hour, '.$differences['minutes'].' minutes, '.$differences['seconds'].' seconds remaining';
		} else {
			$status = 'closed';
		}
		$status = '['.$status.']';
		
		$category_result = query("SELECT Category FROM Categories WHERE ItemID = {$row['ItemID']}");
		$category = "";
		while($category_row = $category_result->fetch_assoc()) {
			$category .= '<a href="?'.$_query.'category='.$category_row['Category'].'" class="category-link">'.$category_row['Category'].'</a>';
		}
		$category_result->free();
?>
	
	<tr onclick="window.location='?action=view&item=<?=$row['ItemID']?>';">
		<td class="item"><h2><?=$row['Name']?><span><?=$status?></span></h2><?=$category?></td>
		<td class="bids"><?=$row['BidCount']?></td>
	</tr>
	<? endwhile; ?>
<?php endif; ?>
</table>

<h1>Recent Bids</h1>
<table id="bids">
	<thead>
		<th>Item</th>
		<th>Bid</th>
		<th>Time</th>
	</thead>
<?php if($bid_result->num_rows == 0): ?>
	<tr><td class="spanning" colspan="3"><?=$view_user['UserID']?> has not yet placed any bids.</td></tr>
<?php else: ?>
	<?php while($bids = $bid_result->fetch_assoc()): ?>
		<tr onclick="window.location='?action=view&item=<?=$bids['ItemID']?>';">
			<td><?=$bids['Name']?></td>
			<td>$<?=$bids['Amount']?></td>
			<td><?=$bids['Bid_Time']?></td>
		</tr>
	<?php endwhile; ?>
<?php endif; ?>
</table>
</div>
<?php
$user_result->free();
$items_result->free();
$bid_result->free();
?>
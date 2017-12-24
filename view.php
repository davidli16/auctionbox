<?php
$item = intval($_REQUEST['item']);

if($_do == "create_auction") {
	$item = create_auction();
}

if($_do == "create_auction" && $item == null) {
} else {
	$item_result = query("SELECT *, DATE_FORMAT(Started, '%M %d, %Y, %r') AS Started FROM Items WHERE ItemID = $item AND Started <= $sql_time LIMIT 1");
	
	if($item_result->num_rows != 0) {
		$bid_result = query("SELECT *, DATE_FORMAT(Bid_Time, '%M %d, %Y, %r') AS Bid_Time FROM Bids WHERE ItemID = $item AND Bid_Time <= $sql_time ORDER BY Amount");
	
		/* Calculate max */
		$max = null;
		$max_result = query("SELECT Bidder, Amount AS Currently FROM Bids WHERE ItemID = $item AND Bid_Time <= $sql_time AND Amount >= ALL (SELECT temp.Amount FROM Bids temp WHERE temp.ItemID = $item AND temp.Bid_Time <= $sql_time) ORDER BY Amount LIMIT 1");
		if($max_result->num_rows != 0)
			$max = $max_result->fetch_assoc();
		$max_result->free();
	
		$item = $item_result->fetch_assoc();
	?>
	
	<h1>Auction Information</h1>
	
	<table id="item-info">
		<tr>
			<td class="heading">Item</td>
			<td><?=$item['Name']?></td>
		</tr>
		<tr>
			<td class="heading">Seller</td>
			<td><a href="?action=user&user=<?=$item['Seller']?>"><?=$item['Seller']?></a></td>
		</tr>
		<tr>
			<td class="heading">Minimum Bid</td>
			<td>$<?=$item['First_Bid']?></td>
		</tr>
		<tr>
			<td class="heading">Buy It Now! Price</td>
			<td><?php
				if($item['Buy_Price'] == 0) {
					print "You cannot buy this product.";
				} else {
					print '$'.$item['Buy_Price'];
				}
			?></td>
		</tr>
		<tr>
			<td class="heading">Started</td>
			<td><?=$item['Started']?></td>
		</tr>
		<tr>
			<td class="heading">Time Remaining</td>
			<td><?php
			$ends = new DateTime($item['Ends']);
			if($ends->format("U") > $cur_time->format("U")) {
				$differences = get_time_difference($cur_time->format('M-d-y H:i:s'),$ends->format('M-d-y H:i:s'));
				print $differences['days'].' days, '.$differences['hours'].' hour, '.$differences['minutes'].' minutes, '.$differences['seconds'].' seconds remaining';
			 } else {
				print 'closed';
			}
			?></td>
		</tr>
		<tr>
			<td class="heading">Description</td>
			<td><?=nl2br($item['Description'])?></td>
		</tr>
	</table>
	
	<div id="place-bid">
	<?php 
		$ends = new DateTime($item['Ends']);
		if($ends->format("U") <= $cur_time->format("U")):
			print "Sorry. This auction is over.";
			if($max == null):
				print " No one bid on this item.";
			else:
				print " <a href=\"?action=user&user={$max['Bidder']}\">{$max['Bidder']}</a> won the auction with a bid of \${$max['Currently']}.";
			endif;
		elseif($user == null):
			print 'You must be logged in to bid.';
		else: ?>
	<form method="POST" action="index.php?<?=$_query?>do=place_bid">
		<input type="hidden" name="item" value="<?=$item['ItemID']?>" />
		$<input type="text" name="bid" value="<?=$max['Currently']+1?>"   onfocus="if(this.value == '<?=$max['Currently']+1?>') this.value=''" onblur="if(this.value.length == 0) this.value = '<?=$max['Currently']+1?>'" />
		<input type="submit" value="Place Bid!" />
	</form>
	<?php
		endif;
	?>
	</div>
	
	<h1>Current Bids</h1>
	<table id="bids">
		<thead>
			<th>Bidder</th>
			<th>Bid</th>
			<th>Time</th>
		</thead>
	<?php if($bid_result->num_rows == 0): ?>
		<tr><td class="spanning" colspan="3">There are currently no bids on this item</td></tr>
	<?php else: ?>
		<?php while($bids = $bid_result->fetch_assoc()): ?>
			<tr>
				<td><a href="?action=user&user=<?=$bids['Bidder']?>"><?=$bids['Bidder']?></a></td>
				<td>$<?=$bids['Amount']?></td>
				<td><?=$bids['Bid_Time']?></td>
			</tr>
		<?php endwhile; ?>
	<?php endif; ?>
	</table>
	
	<?php
		$bid_result->free();
	} else {
		print '<div class="message">This auction does not exist</div>';
	}
	$item_result->free();
}
?>
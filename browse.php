<?php

$_page = intval($_REQUEST['page']);
if($_page == 0) $_page = 1;

$filters = "";
if(isset($_REQUEST['search']) && $_REQUEST['search'] != '') {
	$filters .= " AND Name LIKE '%{$_REQUEST['search']}%'";
}

if(isset($_REQUEST['category']) && $_REQUEST['category'] != '') {
	$category_ = str_replace('+',' ',$_REQUEST['category']);
	$category_ = str_replace('%20',' ',$category_);
	$filters .= " AND Items.ItemID IN (SELECT temp.ItemID FROM Categories temp WHERE Category = '$category_')";
}

switch($_REQUEST['status']) {
	case 'all': break;
	case 'closed': $filters .= " AND Ends <= $sql_time"; break;
	default:  $filters .= " AND Ends > $sql_time"; $_REQUEST['status'] = "open";
}

if((isset($_REQUEST['min']) && $_REQUEST['min'] != '') || (isset($_REQUEST['max']) && $_REQUEST['max'] != '')) {
	$range = "HAVING ";
}
if(isset($_REQUEST['min']) && $_REQUEST['min'] != '') {
	$range .= "MAX(Amount) >= {$_REQUEST['min']}";
}
if(isset($_REQUEST['min']) && $_REQUEST['min'] != '' && isset($_REQUEST['max']) && $_REQUEST['max'] != '') {
	$range .= " AND ";
}
if(isset($_REQUEST['max']) && $_REQUEST['max'] != '') {
	$range .= "MAX(Amount) <= {$_REQUEST['max']}";
}


define("ITEMS_PER_PAGE", 25);

/* Get Auctions */
switch($_REQUEST['order']) {
	case "seller": $order = "Seller ASC"; break;
	case "item": $order = "Name ASC"; break;
	case "bids": $order = "BidCount"; break;
	case "price": $order = "Currently ASC"; break;
	default: $order = "Ends ASC"; break;
}

/* Get Categories */
$cat_result = query("SELECT DISTINCT Category FROM Categories");
$categories = array();
while($cat_row = $cat_result->fetch_assoc()) {
	$categories[] = $cat_row['Category'];
}
$cat_result->free();

/* Calculate pages */
$p_result = query("SELECT Items.ItemID AS ItemID, Name, Seller, MAX(Amount) AS Currently, COUNT(Bid_Time) AS BidCount, Ends, Started FROM Items LEFT JOIN Bids ON Bids.ItemID = Items.ItemID WHERE Started <= $sql_time AND Bid_Time <= $sql_time $filters GROUP BY Items.ItemID $range ORDER BY $order");
$num_pages = $p_result->num_rows;
$count = $num_pages;
$num_pages = ceil($num_pages / ITEMS_PER_PAGE);
?>
	<div id="filter-bar">
	<form id="filters" action="index.php" method="GET">
		<input type="hidden" name="action" value="browse" />
		<table>
			<tr>
				<td><?=$count?> results</td>
				<?php if($num_pages > 1): ?>
				<td><label>page</label>
						<select name="page">
							<?php
									for($i = 1; $i <= $num_pages; $i++) {
										if($i == $_page) print "<option selected>$i</option>";
										else print "<option>$i</option>";
									}
							?>	
						</select>
				</td>
				<?php endif; ?>
				<td><label>min price</label>
						$<input type="text" name="min" value="<?=$_REQUEST['min']?>" />
				</td>
				<td><label>max price</label>
						$<input type="text" name="max" value="<?=$_REQUEST['max']?>" />
				</td>
				<td><label>category</label>
					<select name="category">	
						<?php
								print "<option select></option>";
								foreach($categories as $category) {
									if($category == $_REQUEST['category']) print "<option selected>$category</option>";
									else print "<option>$category</option>";
								}
						?>	
					</select>
				</td>
				<td><label>status</label>
					<select name="status">
						<?php
								$statuses = array("all", "open","closed");
								foreach($statuses as $status) {
									if($status == $_REQUEST['status']) print "<option selected>$status</option>";
									else print "<option>$status</option>";
								}
						?>
					</select>
				</td>
				<td><input type="submit" value="filter" /></td>
				<td><a href="?action=browse">clear</a></td>
			</tr>
		</table>
		</form>
	</div>

<?php
if($count != 0) {
	/* Get a list of the items */
	$offset = ($_page - 1) * ITEMS_PER_PAGE;
	$sql = "SELECT Items.ItemID AS ItemID, Name, Seller, MAX(Amount) AS Currently, COUNT(Bid_Time) AS BidCount, Ends, Started FROM Items LEFT JOIN Bids ON Bids.ItemID = Items.ItemID WHERE Started <= $sql_time AND Bid_Time <= $sql_time $filters GROUP BY Items.ItemID $range ORDER BY $order LIMIT $offset, ".ITEMS_PER_PAGE;
	$result = query($sql);
?>

	<table id="listing">
		<thead>
			<th class="item"><a href="?action=browse&page=1&order=item">Item</a></th>
			<th class="bids"><a href="?action=browse&page=1&order=bids">Bids</a></th>
			<th class="currently"><a href="?action=browse&page=1&order=price">Current Price</a></th>
			<th><a href="?action=browse&page=1&order=seller">Sold by</a></th>
		</thead>

<?php
		while($row = $result->fetch_assoc()):
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
			<td class="currently">$<?=$row['Currently']?></td>
			<td><a href="?action=user&user=<?=$row['Seller']?>"><?=$row['Seller']?></a></td>
		</tr>
		<? endwhile; ?>
	</table>
	
<?
	$result->free();
} else {
	print '<div class="message">There are currently no auctions that match your search criteria.</div>';
}

?>
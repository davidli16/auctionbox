<div id="narrow-content">
<form method="POST" action="?action=view&do=create_auction">
<h1>Create New Auction</h1>
<table id="create-form">
	<tr>
		<td class="heading">Item</td>
		<td><input type="text" name="name" /></td>
	</tr>
	<tr>
		<td class="heading">Description</td>
		<td><textarea name="description"></textarea></td>
	</tr>
	<tr>
		<td class="heading">Minimum Bid</td>
		<td>$<input type="text" name="min" /></td>
	</tr>
	<tr>
		<td class="heading">Ends</td>
		<td>
			<table>
				<tr>
					<td><label>month</label>
						<select size="1" name="month">
						<?php
							foreach($months as $month) {
								if($month == $cur_time->format("M")) print "<option selected>$month</option>";
								else print "<option>$month</option>";
							}
						?>
						</select>
					</td>
					<td><label>day</label>
						<select size="1" name="day">
							<?php
							for($i = 1; $i < 31; $i++) {
								$value = sprintf("%02d", $i);
								if(($i - 1) % 31 == $cur_time->format("d")) print "<option selected>$value</option>";
								else print "<option>$value</option>";
							}
							?>
						</select>
					</td>
					<td><label>year</label>
						<select size="1" name="year">
							<?php
							for($i = 1999; $i < 2008; $i++) {
								if($i == $cur_time->format("Y")) print "<option selected>$i</option>";
								else print "<option>$i</option>";
							}
							?>
						</select>
					</td>
					<td><label>hour</label>
						<select size="1" name="hour">
							<?php
							for($i = 0; $i < 24; $i++) {
								$value = sprintf("%02d", $i);
								if($i == $cur_time->format("H")) print "<option selected>$value</option>";
								else print "<option>$value</option>";
							}
							?>
						</select>
					</td>
					<td><label>minute</label>
						<select size="1" name="minute">
							<?php
							for($i = 0; $i < 60; $i++) {
								$value = sprintf("%02d", $i);
								if($i == $cur_time->format("i")) print "<option selected>$value</option>";
								else print "<option>$value</option>";
							}
							?>
						</select>
					</td>
					<td><label>second</label>
						<select size="1" name="second">
							<?php
							for($i = 0; $i < 60; $i++) {
								$value = sprintf("%02d", $i);
								if($i == $cur_time->format("s")) print "<option selected>$value</option>";
								else print "<option>$value</option>";
							}
							?>
						</select>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td class="heading">Buy It Now! Price <span class="note">[optional]</span></td>
		<td>$<input type="text" name="buy" /></td>
	</tr>
	<tr>
		<td class="heading">Categories <span class="note">[optional]</span></td>
		<td>
			<input type="text" name="category1" />
			<input type="text" name="category2" />
			<input type="text" name="category3" />
			<input type="text" name="category4" />
		</td>
	</tr>
	<tr>
		<td colspan="2" class="spanning"><input type="submit" value="Create" /><input type="reset" value="Start Over" />
</table>
</form>
</div>
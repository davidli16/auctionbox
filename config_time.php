<form method="POST" action="index.php?<?=$_query?>">
	<input type="hidden" name="do" value="set_time" />
	<table>
		<tr>
			<td><a href="?<?=$_query?>&do=prev_day">&laquo; Prev day</a></td>
			<td><label>month</label>
				<select size="1" name="month" onchange="form.submit();">
				<?php
					foreach($months as $month) {
						if($month == $cur_time->format("M")) print "<option selected>$month</option>";
						else print "<option>$month</option>";
					}
				?>
				</select>
			</td>
			<td><label>day</label>
				<select size="1" name="day" onchange="form.submit();">
					<?php
					for($i = 1; $i < 31; $i++) {
						$value = sprintf("%02d", $i);
						if($i == $cur_time->format("d")) print "<option selected>$value</option>";
						else print "<option>$value</option>";
					}
					?>
				</select>
			</td>
			<td><label>year</label>
				<select size="1" name="year" onchange="form.submit();">
					<?php
					for($i = 1999; $i < 2008; $i++) {
						if($i == $cur_time->format("Y")) print "<option selected>$i</option>";
						else print "<option>$i</option>";
					}
					?>
				</select>
			</td>
			<td><label>hour</label>
				<select size="1" name="hour" onchange="form.submit();">
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
				<select size="1" name="minute" onchange="form.submit();">
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
				<select size="1" name="second" onchange="form.submit();">
					<?php
					for($i = 0; $i < 60; $i++) {
						$value = sprintf("%02d", $i);
						if($i == $cur_time->format("s")) print "<option selected>$value</option>";
						else print "<option>$value</option>";
					}
					?>
				</select>
			</td>
			<td><a href="?<?=$_query?>&do=next_day">Next day &raquo;</a></td>
		</tr>
	</table>
</form>
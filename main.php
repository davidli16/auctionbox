<div id="main-search">
	<form method="GET" action="index.php">
		<input type="hidden" name="action" value="browse" />
		<input type="text" class="search-box" name="search" value="look inside the box"  onfocus="if(this.value == 'look inside the box') this.value=''" onblur="if(this.value.length == 0) this.value = 'look inside the box'" /><br />
		<input type="submit" value="Find my item" />
	</form>
</div>
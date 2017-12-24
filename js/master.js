$(document).ready(function(){
	$.timer(8000, function (timer) {
		close_error();
		timer.stop();
	});
	
	$('#error').click(function () { close_error(); });

	function close_error () {
		var doit = $.jqURL.get("do"); 
		if(doit == "create_auctions");
			window.history.back();
		$('#error').hide();
	}
});
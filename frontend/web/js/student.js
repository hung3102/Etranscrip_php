$(function() {
	advancedSearch()
});

function advancedSearch() {
	$('#std_advanced_search').hide();
	$('#open_button').click(function() {
		$('#std_advanced_search').show();
		$(this).hide();
	})
	$('#close_button').click(function() {
		$('#std_advanced_search').hide();
		$('#open_button').show();
	})
}

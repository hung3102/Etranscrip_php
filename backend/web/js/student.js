$(function() {
	$("#checkAll").change(function() {
		if(this.checked == true) {
			$('#std_grid :checkbox').prop("checked", true);
		} else {
			$('#std_grid :checkbox').removeAttr('checked');
		}
	});

	var baseUrl = window.location.origin;
	$("#modalButton").click(function(e){
	    e.preventDefault();
	    $.ajax({
	        type: "POST",
	        url: baseUrl + '/Etranscript/backend/web/x12/render-modal',
	        data: {
	        	studentIDs : $("#std_grid").yiiGridView("getSelectedRows"),
	        	allStd : $('#checkAll').is(':checked') ? true : false
	        },
	        success: function(content){
	            $('#sendModal').find('#modalContent').html(content);
	            $('#sendModal').modal('show');
	        },
	    });
	});

	$("#autoModalButton").click(function(e){
	    e.preventDefault();
	    $.ajax({
	        type: "POST",
	        url: autoUrl,
	        data: {
	        	studentIDs : $("#std_grid").yiiGridView("getSelectedRows"),
	        	allStd : $('#checkAll').is(':checked') == true ? true : false
	        },
	        success: function(content){
	            $('#autoSendModal').find('#autoModalContent').html(content);
	            $('#autoSendModal').modal('show');
	        },
	    });
	});

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

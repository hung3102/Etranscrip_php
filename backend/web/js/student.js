$(function() {
	$("#modalButton").click(function(e){
	    e.preventDefault();
	    $.ajax({
	        type: "POST",
	        url: '../x12/check-sr',
	        data: {
	        	studentIDs : $("#std_grid").yiiGridView("getSelectedRows")
	        },
	        success: function(content){
	            $('#sendModal').find('#modalContent').html(content);
	            $('#sendModal').modal('show');
	        },
	    });
	});
});

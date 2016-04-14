$(function() {
	$("#modalButton").click(function(e){
	    e.preventDefault();
	    $.ajax({
	        type: "POST",
	        url: '../x12/render-modal',
	        data: {
	        	studentIDs : $("#std_grid").yiiGridView("getSelectedRows")
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
	        url: '../x12/render-modal-auto',
	        data: {
	        	studentIDs : $("#std_grid").yiiGridView("getSelectedRows")
	        },
	        success: function(content){
	            $('#autoSendModal').find('#autoModalContent').html(content);
	            $('#autoSendModal').modal('show');
	        },
	    });
	});
});

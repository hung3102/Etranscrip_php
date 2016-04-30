$(function() {
	var getUrlParameter = function getUrlParameter(sParam) {
	    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
	        sURLVariables = sPageURL.split('&'),
	        sParameterName,
	        i;

	    for (i = 0; i < sURLVariables.length; i++) {
	        sParameterName = sURLVariables[i].split('=');

	        if (sParameterName[0] === sParam) {
	            return sParameterName[1] === undefined ? true : sParameterName[1];
	        }
	    }
	};
	$(".year_evaluation:last").append('<div class="addYear_button"><button type="button" class="btn btn-info">Add year</button></div>');
	addYear(getUrlParameter('id'));
})

function addYear(id) {
	$(".addYear_button").click(function() {
		// var last = $(".year_evaluation:last").clone(false);
		var baseUrl = window.location.origin;
		$.ajax({
			type: "POST",
			url: baseUrl + '/Etranscript/backend/web/school-report/create-year-form',
			data: {
				'index' : 3,
				'id' : id,
			},
			cache: false,
			success: function(result) {
				$('form:last').after(result);
			}
		})
		
		// $('.year_evaluation:last').find('input:text').val('');
		// $('.year_evaluation:last').find('textarea').val('');
		// $('.year_evaluation:last').find('select').val('');
		$('.addYear_button').remove();
		$('.year_evaluation:last').after('<hr class="horizon_line" />');
		$(".year_evaluation:last").append('<div class="addYear_button"><button type="button" class="btn btn-info">Add year</button></div>');
	})
}

// $(function() {
// 	nativeAddress();
// 	currentAddress();
// })

// function nativeAddress() {
// 	checkProvinceChange('.native_address');
// }

// function currentAddress() {
// 	checkProvinceChange('.current_address');
// 	checkDistrictChange();
// }

// function checkProvinceChange(type) {
// 	$(type + ' #province').change(function() {
// 		provinceID = $(type + ' #province').val();
// 		if(provinceID != -1) {
// 			$(type + ' #district').removeAttr('disabled');
// 			$(type + ' #district').html("<option value='-1'>Select district</option>");
// 			loadDistricts(provinceID, type);
// 		} else {
// 			$(type + ' #district').html(null);
// 			$(type + ' #district').attr('disabled', true);
// 			if(type == '.current_address') {
// 				$(type + ' #commune').html(null);
// 				$(type + ' #commune').attr('disabled', true);
// 				$(type + ' #detailAddress').html(null);
// 				$(type + ' #detailAddress').attr('disabled', true);
// 				$(type + ' #detailAddress').attr('value', null);
// 			}
// 		}
// 	})
// }

// function checkDistrictChange() {
// 	$('.current_address #district').change(function() {
// 		districtID = $('.current_address #district').val();
// 		if(districtID != -1) {
// 			$('.current_address #commune').removeAttr('disabled');
// 			$('.current_address #commune').html("<option value='-1'>Select commune</option>");
// 			loadCommunes(districtID);
// 			$('.current_address #detailAddress').attr('disabled', false);
// 		} else {
// 			$('.current_address #commune').html(null);
// 			$('.current_address #commune').attr('disabled', true);
// 			$('.current_address #detailAddress').html(null);
// 			$('.current_address #detailAddress').attr('disabled', true);
// 			$('.current_address #detailAddress').attr('value', null);
// 		}
// 	})
// }

// function loadDistricts(provinceID, type) {
// 	var baseUrl = window.location.origin;
// 	$.ajax({
// 		type: "POST",
// 		url: baseUrl + '/Etranscript/backend/web/province/load-districts',
// 		data: {provinceID : provinceID},
// 		cache: false,
// 		success: function(result) {
// 			$(type + ' #district').append(result);
// 		}
// 	})
// }

// function loadCommunes(districtID) {
// 	var baseUrl = window.location.origin;
// 	$.ajax({
// 		type: "POST",
// 		url: baseUrl + '/Etranscript/backend/web/district/load-communes',
// 		data: {districtID : districtID},
// 		cache: false,
// 		success: function(result) {
// 			$('#commune').append(result);
// 		}
// 	})
// }
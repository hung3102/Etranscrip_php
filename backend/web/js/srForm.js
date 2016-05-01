// $(document).ready(function() {
// 	// var getUrlParameter = function getUrlParameter(sParam) {
// 	//     var sPageURL = decodeURIComponent(window.location.search.substring(1)),
// 	//         sURLVariables = sPageURL.split('&'),
// 	//         sParameterName,
// 	//         i;

// 	//     for (i = 0; i < sURLVariables.length; i++) {
// 	//         sParameterName = sURLVariables[i].split('=');

// 	//         if (sParameterName[0] === sParam) {
// 	//             return sParameterName[1] === undefined ? true : sParameterName[1];
// 	//         }
// 	//     }
// 	// };
// 	$(".year_evaluation:last").after('<div class="add_button"><button type="button" id="addYear_button" class="btn btn-info">Thêm năm học</button></div>');
// 	addYear();
// 	removeYear();
// 	$(this).yiiActiveForm([], []);
// })

// // function addYear(id) {
// // 	$(document).on("click", "#addYear_button", function() {
// // 		if($('.year_evaluation').length == 3 ){
// // 			alert("The max of year is 3");
// // 			return true;
// // 		}
// // 		alert(initForm);
// // 		var baseUrl = window.location.origin;
// // 		$.ajax({
// // 			type: "POST",
// // 			url: baseUrl + '/Etranscript/backend/web/school-report/create-year-form',
// // 			data: {
// // 				'index' : i++,
// // 				'id' : id,
// // 				'initForm' : initForm,
// // 			},
// // 			cache: false,
// // 			success: function(result) {
// // 				$('.year_evaluation:last').after(result);
// // 			}		
// // 		})
// // 	})
// // }

// function addYear() {
// 	$(document).on("click", "#addYear_button", function() {
// 		if($('.year_evaluation').length == 3 ){
// 			alert("The max of year is 3");
// 			return true;
// 		}
// 		$('.year_evaluation:last').after($('.year_evaluation:last').clone());
// 		$('.year_evaluation:last').find(':input').val('');
// 		renumberIndex();
// 	})
// }

// function removeYear() {
// 	$(document).on("click", ".removeYear_button", function() {
// 		$(this).closest('.year_evaluation').remove();
// 		renumberIndex();
// 	})
// }

// function renumberIndex() {
// 	var index = 0;
//     $(".year_evaluation").each(function() {
//     	var prefix_for = "-mark-" + index + "-";
//     	var prefix_class = "-mark-" + index + "-";
//     	var prefix_id = "-mark-" + index + "-";
//         var prefix_name = "[mark][" + index + "]";
//         $(this).find(".control-label.sr-only").each(function() {
//         	var forName = $(this).attr('for');
//         	forName = forName.replace(/-mark-\d+-/, prefix_for);
//         	$(this).attr('for', forName);
//         	// alert($(this).attr('class'));
//         })
//         $(this).find(".required").each(function() {
//         	var className = $(this).attr('class');
//         	className = className.replace(/-mark-\d+-/, prefix_class);
//         	$(this).attr('class', className);
//         	// alert($(this).attr('class'));
//         })
//         $(this).find(":input").each(function() {
//         	this.id = this.id.replace(/-mark-\d+-/, prefix_id);
//         	this.name = this.name.replace(/\[mark\]\[\d+\]/, prefix_name)
//         });
//         index++;
        
//     });
// }
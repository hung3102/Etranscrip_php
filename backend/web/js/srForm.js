$(document).ready(function() {
	addAchievement();
	removeAchievement();
})

function addAchievement() {
	$('.achievement').each(function() {
		$(this).find('.ach_form:last').after('<button class="add_Ach_button" type="button">Add</button>');
		$(this).on("click", ".add_Ach_button", function() {
			last = $(this).closest('.achievement').find('.ach_form:last');
			last.after(last.clone());
			$(this).closest('.achievement').find('.input:last').val('');
			$(this).closest('.achievement').find('.del_Ach_button').remove();
			$(this).closest('.achievement').find('.input').after('<button class="del_Ach_button" type="button">Remove</button>');
			renumberIndex($(this).closest('.achievement'));
		})
	})
}

function removeAchievement() {
	$('.achievement').each(function() {
		if($(this).closest('.achievement').find('.ach_form').length > 1){
			$(this).find('.input').after('<button class="del_Ach_button" type="button">Remove</button>');
		}
		$(this).on("click", '.del_Ach_button', function() {
			if($(this).closest('.achievement').find('.ach_form').length == 1){
				return true;
			}
			$(this).prev().remove();
			renumberIndex($(this).closest('.achievement'));
			$(this).closest('.ach_form').remove();
		})
	})
}

function renumberIndex(elem) {
    elem.each(function() {
    	var index = 0;
        elem.find(".input").each(function() {
        	var prefix_name = "][" + index + "]";
        	this.name = this.name.replace(/\]\[\d+\]/, prefix_name)
        	index++;
        });
    });
}

// function calScore() {
// 	$('.subject_row .score').on("click", '.input', function() {
// 		score_elem = $(this).closest('.score');
// 		alert(score_elem.find('.wholeYear').val());
// 		if(score_elem.find('.wholeYear').val() != '') {
// 			term1_score = score_elem.find('.term1').val();
// 			term2_score = score_elem.find('.term2').val();
// 			if( term1_score != '' && term2_score != '') {
// 				whole_year = (term2_score * 2 + term1_score)/3;
// 				score_elem.find('.wholeYear').val(whole_year);
// 			}
// 		}
// 	})
// }
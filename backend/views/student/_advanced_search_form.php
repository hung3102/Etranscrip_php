<?php
use kartik\form\ActiveForm;
use common\models\Province;
use common\models\District;
use common\models\School;
use common\models\Commune;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\date\DatePicker;
?>
<?php 
	$form = ActiveForm::begin([
		'type' => ActiveForm::TYPE_INLINE,
        // 'formConfig' => ['labelSpan' => 1, 'deviceSize' => ActiveForm::SIZE_SMALL, 'showErrors' => true]
	]);
?>
<div class="horizontal_head">----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------</div>
<?= Html::a('Click here to open advanced search', null, ['id' => 'open_button']) ?>
<div id="std_advanced_search">
	<div id="school_search">
		<span class="title">School:</span>
		<span id="sprovince_search">
		<?php 
			$provinces = ArrayHelper::map(Province::find('')->orderBy('name')->all(), 'id', 'name');
			echo Html::dropDownList('sprovinceID', null, $provinces, 
						[
			                'prompt'=>'Select province',
			                'id' => 'province',
			                'onchange'=>'
			                    if($(this).val() >= 1) {
			                        $( "select#district" ).removeAttr("disabled");
			                        $( "select#school" ).removeAttr("disabled");
			                    } else {
			                        $( "select#district" ).attr("disabled", "true");
			                    }
			                    $.post( "'.Yii::$app->urlManager->createUrl(['address/load-districts']).'", 
			                    { 
			                    	provinceID: $(this).val(), 
			                    } )
			                    .done(function( data ) {
			                        $( "select#district" ).html( data );
			                    });'
	            		]); 
	    ?>
	    </span>
	    <span id="sdistrict_search">
	   	<?php
	   		// $address = new Address();
			$districts = District::find('')->asArray()->all();
	   		echo Html::dropDownList('sdistrictID', null, $districts, 
					[
		                'prompt'=>'Select district',
		                'id' => 'district',
		                'disabled' => true,
		                'onchange'=>'
		                    if($(this).val() >= 1) {
		                        $( "select#school" ).removeAttr("disabled");
		                    } else {
		                        $( "select#school" ).attr("disabled", "true");
		                    }
		                    $.post( "'.Yii::$app->urlManager->createUrl(['school/load-schools']).'", 
		                    { 
		                    	provinceID: null,
		                    	districtID: $(this).val() 
		                    } )
		                    .done(function( data ) {
		                        $( "select#school" ).html( data );
		                    });'
	        		]);
	    ?>
	    </span>
		<span id="school_form">
		<?php 
			$schools = ArrayHelper::map(School::find('')->orderBy('name')->all(), 'id', 'name');
			echo Html::dropDownList('StudentSearch[schoolID]', null, $schools, 
					[
		                'prompt'=>'Select school',
		                'disabled' => true,
		                'id' => 'school',
	        		]);
	    ?>
		</span>
	</div>
	<div id="name_search">
	<span class="title">Name:</span>
	<?php 
		echo Html::textInput('StudentSearch[name]', null, ['placeholder' => 'Input name']);
	?>
	</div>
	<div id="birthday_search">
	<span class="title">Birthday:</span>
	<?= DatePicker::widget([
				'name' => 'StudentSearch[birthday]',
                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                'options' => ['placeholder' => 'Enter birthday'],
                'pluginOptions' => [
                    'autoclose'=>true,
                    'format' => 'dd/mm/yyyy'
                ]
            ]);
	?>	
	</div>
	<div id="address_search">
		<span class="title">Current Address:</span>
		<span id="aprovince_search">
		<?= Html::dropDownList('StudentSearch[provinceID]', null, $provinces, 
					[
		                'prompt'=>'Select province',
		                'id' => 'aprovince',
		                'onchange'=>'
		                    if($(this).val() >= 1) {
		                    	$( "select#commune" ).removeAttr("disabled");
		                        $( "select#adistrict" ).removeAttr("disabled");
		                    } else {
		                        $( "select#adistrict" ).attr("disabled", "true");
		                        $( "select#commune" ).val("");
		                        $( "select#commune" ).attr("disabled", "true");
		                    }
		                    $.post( "'.Yii::$app->urlManager->createUrl(['address/load-districts']).'", 
		                    { 
		                    	provinceID: $(this).val(), 
		                    } )
		                    .done(function( data ) {
		                        $( "select#adistrict" ).html( data );
		                    });'
            		]); 
        ?>
		</span>
		<span id="adistrict_search">
	   	<?php
	   		echo Html::dropDownList('StudentSearch[districtID]', null, $districts, 
					[
		                'prompt'=>'Select district',
		                'id' => 'adistrict',
		                'disabled' => true,
		                'onchange'=>'
		                    if($(this).val() >= 1) {
		                        $( "select#commune" ).removeAttr("disabled");
		                    } else {
		                        $( "select#commune" ).attr("disabled", "true");
		                    }
		                    $.post( "'.Yii::$app->urlManager->createUrl(['address/load-communes']).'", 
		                    { 
		                    	districtID: $(this).val() 
		                    } )
		                    .done(function( data ) {
		                        $( "select#commune" ).html( data );
		                    });'
	        		]);
	    ?>
	    </span>
	    <span id="commune_search">
	   	<?php
	   		$communes = ArrayHelper::map(Commune::find('')->orderBy('name')->all(), 'id', 'name');
	   		echo Html::dropDownList('StudentSearch[communeID]', null, $communes, 
					[
		                'prompt'=>'Select commune',
		                'id' => 'commune',
		                'disabled' => true,
	        		]);
	    ?>
	    </span>
	</div>
	<div class="submit_button">
		<?= Html::submitButton('Search', ['class' => 'btn btn-info']) ?>
	</div>
	<?= Html::a('Close advanced search', null, ['id' => 'close_button']) ?>
</div>
<div class="horizontal_foot">----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------</div>
<?php $form->end(); ?>
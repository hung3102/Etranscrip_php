<?php
?>
<div id="SR">
	<div id="cover">
		<div class="caption1"> 
			<div class="text">BỘ GIÁO DỤC VÀ ĐÀO TẠO</div>
			<div class="horizon"></div>
		</div>
		<div class="caption2"> 
			<h1>HỌC BẠ</h1>
			<h3>TRUNG HỌC PHỔ THÔNG</h3>
		</div>
		<div class="std_name">
			<h5>Họ và tên học sinh</h5>
			<div class="name"> <?= $model->student->name ?> </div>
		</div>
		<div class="sr_number"><span>Số</span> <?= '<span class="text">'.$model->number.'</span>' ?> <span>/THPT</span></div>
	</div>

	<div id="page1">
		<div id="caption">
			<div class="caption1">
				<div class="caption1-1">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</div>
				<div class="caption1-2"> Độc lập - Tự do - Hạnh phúc </div>
				<div class="star_line">*****</div>
			</div>
			<div class="caption2">
				<div class="caption2-1">HỌC BẠ</div>
				<div class="caption2-2">TRUNG HỌC PHỔ THÔNG</div>
			</div>
		</div>
		<div id="std_info">
			<div class="image"></div>
			<table> 
			<tr>
				<td class="name"> Họ và tên: <?= '<span class="text-bold">'.$model->student->name.'</span>' ?></td>
				<td class="gender">Giới tính: <?= '<span class="text-bold">'.$model->student->getGenderText().'</span>'?></td>
			</tr>
			</table>
			<table>
			<?php $timestamp = strtotime($model->student->birthday); ?>
			<tr>
				<td lass="birthday">Ngày sinh: <?= '<span class="text-bold">'.date("d", $timestamp).'</span>' ?> tháng <?= '<span class="text-bold">'.date("m", $timestamp).'</span>' ?> 
					năm <?= '<span class="text-bold">'.date("Y", $timestamp).'</span>' ?>
				</td>
			</tr>
			<tr>
				<td class="native_address">Nơi sinh: <?= '<span class="text-bold">'.$model->student->nativeAddress->district->name . ', ' . $model->student->nativeAddress->district->province->name.'</span>' ?>
				</td>
			</tr>
			<tr>
				<td class="ethnic">Dân tộc: <?= '<span class="text-bold">'.$model->student->ethnic->name.'</span>' ?>
				</td>
			</tr>
			<tr>
				<td class="object">Thuộc đối tượng chính sách: <?= '<span class="text-bold">'.$model->student->getObjectsText().'</span>' ?>
				</td>
			</tr>
			<tr>
				<td class="current_address">Chỗ ở hiện tại: <?= '<span class="text-bold">'.$model->student->currentAddress->getFullAddress().'</span>'?>
				</td>
			</tr>
			</table>
			<table>
			<tr class="father">
				<td class="name">Họ và tên cha: <?= '<span class="text-bold">'.$model->student->fatherName.'</span>' ?></td>
				<td class="job">Nghề nghiệp: <?= '<span class="text-bold">'.$model->student->fatherJob.'</span>' ?>
				</td>
			</tr>
			<tr class="mother">
				<td class="name">Họ và tên mẹ: <?= '<span class="text-bold">'.$model->student->motherName.'</span>' ?></td>
				<td class="job">Nghề nghiệp: <?= '<span class="text-bold">'.$model->student->motherJob.'</span>' ?></td>
			</tr>
			<tr class="tutor">
				<td class="name">Họ và tên người giám hộ: <?= '<span class="text-bold">'.$model->student->tutorName.'</span>' ?></td>
				<td class="job">Nghề nghiệp: <?= '<span class="text-bold">'.$model->student->tutorJob.'</span>' ?></td>
			</tr>
			</table>

		</div>
		<?php $timestamp = strtotime($model->date); ?>
		<div id="date">
			ngày <?= date("d", $timestamp)?> tháng <?= date("m", $timestamp)?> năm <?= date("Y", $timestamp)?>
		</div>
		<div id="principal">
			<div class="constant">HIỆU TRƯỞNG</div>
			<div class="name"><?= $model->yearEvaluations[0]->principalName ?></div>
		</div>

		<div id="study_process">
			<div class="table_name">QUÁ TRÌNH HỌC TẬP</div>
			<table id="process_table">
				<tr class="title">
					<td class="year">Năm học</td>
					<td class="class">Lớp</td>
					<td class="school_address">Tên trường, huyện(quận, thị xã, TP thuộc tỉnh) tỉnh(TP)</td>
					<td class="principal_confirm">Xác nhận của hiệu trưởng</td>
				</tr>
				<?php 
				if($model->studyProcesses != null) {
					foreach ($model->studyProcesses as $studyProcess) { ?>
				<tr class="each">
					<td class="year_content"><?= $studyProcess->fromYear.'/'.$studyProcess->toYear ?></td>
					<td class="class_content"><?= $studyProcess->class ?></td>
					<td class="school_content"><?= $studyProcess->school->name.' - '.$studyProcess->school->address->district->name.', '.$studyProcess->school->address->district->province->name ?></td>
					<td class="confirm_content"><?= $studyProcess->principalName ?></td>
				</tr>
				<?php	}
				}else { ?>
				<tr class="empty_row">
					<td class="empty_cell">&nbsp;</td>
					<td class="empty_cell"></td>
					<td class="empty_cell"></td>
					<td class="empty_cell"></td>
				</tr>
				<tr class="empty_row">
					<td class="empty_cell">&nbsp;</td>
					<td class="empty_cell"></td>
					<td class="empty_cell"></td>
					<td class="empty_cell"></td>
				</tr>
				<?php } ?>
			</table>
		</div>
	</div>
</div>
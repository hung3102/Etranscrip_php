<?php
use common\models\Subject;
use yii\helpers\Html;
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
		<table id="head">
			<tr>
				<td class="image" rowspan="5">
				<?php if($model->student->image != null) {
					echo Html::img(Yii::$app->params['imageUrl'].$model->student->image, ['class' => 'std_img', 'style' => 'height:150px']);
				}?>
				</td>
				<td class="caption1-1">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</td>
			</tr>
			<tr>
				<td class="caption1-2"> Độc lập - Tự do - Hạnh phúc </td>
			</tr>
			<tr>
				<td class="star_line">*****</td>
			</tr>
			<tr>
				<td class="caption2-1">HỌC BẠ</td>
			</tr>
			<tr>
				<td class="caption2-2">TRUNG HỌC PHỔ THÔNG</td>
			</tr>
		</table>
		<div id="std_info">
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
			<div class="name"><?= $model->principalName ?></div>
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
				if($model->yearEvaluations != null) {
					foreach ($model->yearEvaluations as $yearEvaluation) { ?>
				<tr class="each">
					<td class="year_content"><?= $yearEvaluation->fromYear.'/'.$yearEvaluation->toYear ?></td>
					<td class="class_content"><?= $yearEvaluation->class ?></td>
					<td class="school_content"><?= $yearEvaluation->school->name.' - '.$yearEvaluation->school->address->district->name.', '.$yearEvaluation->school->address->district->province->name ?></td>
					<td class="confirm_content"><?= $yearEvaluation->principalName ?></td>
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
<!-- year evaluation here -->
	<?php if($model->yearEvaluations != null) {
		for($i = 0; $i < sizeof($model->yearEvaluations); $i++) { ?>
	<pagebreak />
	<div id="year_evaluation">
		<div id="part1">
			<div id="info">
				<table>
					<tr>
						<td colspan="5"><?= '<span style="font-weight:bold">Họ và tên: </span>'.$model->student->name ?></td>
						<td class="class" colspan="3"><?= '<span style="font-weight:bold">Lớp </span>'.$model->yearEvaluations[$i]->class ?></td>
						<td class="year" colspan="5"><?= '<span style="font-weight:bold">năm học </span>'.$model->yearEvaluations[$i]->fromYear.' - '.$model->yearEvaluations[$i]->toYear ?></td>
					</tr>
					<tr>
						<td colspan="3"><?= '<span style="font-weight:bold">Ban:  </span>'.$model->yearEvaluations[$i]->studyDepartment ?></td>
						<td class="advantage_subjects" colspan="10">
							<?= '<span style="font-weight:bold">Các môn học nâng cao: </span>'.$model->yearEvaluations[$i]->getAdvantageSubjects() ?>
						</td>
					</tr>
				</table>
			</div>
			<table id="year_table">
				<thead>
					<tr>
						<th class="subject_name" rowspan="2">Môn học/Hoạt động GD</th>
						<th class="score" colspan="3">Điểm trung bình hoặc xếp loại các môn</th>
						<th class="score_after" rowspan="2">Điểm hoặc xếp loại sau KT lại (nếu có)</th>
						<th class="teacher" rowspan="2">Giáo viên bộ môn</th>
					</tr>
					<tr>
						<th class="term1">H.kỳ I</th>
						<th class="term2">H.kỳ II</th>
						<th class="term3">CN</th>
					</tr>
				</thead>
				<tbody>
					<?php $subjects = Subject::find('')->all();
						foreach ($subjects as $subject) { ?>
					<tr class="subject_row">
						<td><?= $subject->name ?></td>
						<td>
						<?php
							if($model->yearEvaluations[$i]->checkTermExist(1)){
								$termEvaluation = $model->yearEvaluations[$i]->getTerm(1);
								$subjectScore = $termEvaluation->getSubjectScore($subject->name);
								$teacherName = $subjectScore != null ? $subjectScore->teacherName : null;
							} else {
								$subjectScore = null;
							}
							echo $subjectScore != null ? $subjectScore->score : null;
						?>
						</td>
						<td>
						<?php
							if($model->yearEvaluations[$i]->checkTermExist(2)){
								$termEvaluation = $model->yearEvaluations[$i]->getTerm(2);
								$subjectScore = $termEvaluation->getSubjectScore($subject->name);
								$teacherName = $subjectScore != null ? $subjectScore->teacherName : null;
							} else {
								$subjectScore = null;
							}
							echo $subjectScore != null ? $subjectScore->score : null;
						?>	
						</td>
						<td>
						<?php
							if($model->yearEvaluations[$i]->checkTermExist(0)){
								$termEvaluation = $model->yearEvaluations[$i]->getTerm(0);
								$subjectScore = $termEvaluation->getSubjectScore($subject->name);
								$teacherName = $subjectScore != null ? $subjectScore->teacherName : null;
							} else {
								$subjectScore = null;
							}
							echo $subjectScore != null ? $subjectScore->score : null;
						?>	
						</td>
						<td></td>
						<td><?= isset($teacherName) ? $teacherName : null ?></td>
					</tr>
					<?php } ?>
					<tr class"average">
						<td>ĐTB các môn</td>
						<td>
						<?php
							if($model->yearEvaluations[$i]->checkTermExist(1)){
								$termEvaluation = $model->yearEvaluations[$i]->getTerm(1);
							} else {
								$termEvaluation = null;
							}
							echo $termEvaluation != null ? $termEvaluation->getAverageScore() : null;
						?>	
						</td>
						<td>
						<?php
							if($model->yearEvaluations[$i]->checkTermExist(2)){
								$termEvaluation = $model->yearEvaluations[$i]->getTerm(2);
							} else {
								$termEvaluation = null;
							}
							echo $termEvaluation != null ? $termEvaluation->getAverageScore() : null;
						?>
						</td>
						<td>
						<?php
							if($model->yearEvaluations[$i]->checkTermExist(0)){
								$termEvaluation = $model->yearEvaluations[$i]->getTerm(0);
							} else {
								$termEvaluation = null;
							}
							echo $termEvaluation != null ? $termEvaluation->getAverageScore() : null;
						?>
						</td>
						<td></td>
						<td><?= $model->yearEvaluations[$i]->teacherName ?></td>
					</tr>
					<tr class="comment">
						<td colspan="6"><?= nl2br($model->yearEvaluations[$i]->note) ?>
						</td>
					</tr>
					<tr class="confirm">
						<td class="teacher_confirm" colspan="3">
							<div class="text1" style="font-weight: bold;">Xác nhận của giáo viên chủ nhiệm</div>
							<div class="text2">(Ký và ghi rõ họ tên)</div>
							<div class="name" style="font-weight: bold;"><?= $model->yearEvaluations[$i]->teacherName ?></div>
						</td>
						<td class="principal_confirm" colspan="3">
							<div class="text1" style="font-weight: bold;">Xác nhận của Hiệu trưởng</div>
							<div class="text2" style="font-weight: normal;">(Ký, ghi rõ họ tên và đóng dấu)</div>
							<div class="name" style="font-weight: bold;"><?= $model->yearEvaluations[$i]->principalName ?></div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div id="part2"></div>
	</div>

	<pagebreak />

	<div id="conduct_capacity">
		<div class="school"></div>
		<table>
			<tr class="bold">
				<td rowspan="2">HỌC KỲ</td>
				<td colspan="2">Kết quả xếp loại</td>
				<td rowspan="2">TS buổi nghỉ học cả năm</td>
				<td colspan="2" class="order_after">Xếp loại sau KT lại các môn học hoặc rèn luyện thêm về HK</td>
				<td class="upgrade" rowspan="5"><?= $model->yearEvaluations[$i]->upGradeType ?></td>
			</tr>
			<tr class="bold">
				<td>HK</td>
				<td>HL</td>
				<td>HK</td>
				<td>HL</td>
			</tr>
			<tr>
				<td class="term1">Học kỳ I</td>
				<td><?= $model->yearEvaluations[$i]->getConduct(1); ?></td>
				<td><?= $model->yearEvaluations[$i]->getLearnCapacity(1); ?></td>
				<td rowspan="3" class="miss_content"><?= $model->yearEvaluations[$i]->missedLesson ?></td>
				<td rowspan="3"></td>
				<td rowspan="3"></td>
			</tr>
			<tr>
				<td>Học kỳ II</td>
				<td><?= $model->yearEvaluations[$i]->getConduct(2); ?></td>
				<td><?= $model->yearEvaluations[$i]->getLearnCapacity(2); ?></td>
			</tr>
			<tr>
				<td>Cả năm</td>
				<td><?= $model->yearEvaluations[$i]->getConduct(0); ?></td>
				<td><?= $model->yearEvaluations[$i]->getLearnCapacity(0); ?></td>
			</tr>
			<tr id="certificate">
				<td class="vocational_certificate" colspan="6">- Có chứng chỉ nghề phổ thông: <?= $model->yearEvaluations[$i]->vocationalCertificate ?>
				</td>
				<td colspan="1">Loại: <?= $model->yearEvaluations[$i]->vocationalCertificateLevel ?></td>
			</tr>
			<tr id="achievement">
				<td colspan="7">
				- Các giải thưởng và khen thưởng: <?= $model->yearEvaluations[$i]->getAchievementString() ?>
				</td>
			</tr>
			<tr id="teacher_comment">
				<td colspan="7" class="title1">NHẬN XÉT CỦA GIÁO VIÊN CHỦ NHIỆM</td>
			</tr>
			<tr id="teacher_comment">
				<td colspan="7" class="title2">(Ký và ghi rõ họ tên)</td>
			</tr>
			<tr id="teacher_comment">
				<td colspan="7" class="content">
					<?= nl2br($model->yearEvaluations[$i]->teacherComment) ?>
				</td>
			</tr>		
			<tr id="teacher_comment">
				<td colspan="7" class="teacher_name"><?= $model->yearEvaluations[$i]->teacherName ?></td>
			</tr>	

			<tr id="principal_approval">
				<td colspan="7" class="title1">PHÊ DUYỆT CỦA HIỆU TRƯỞNG</td>
			</tr>
			<tr id="principal_approval">
				<td colspan="7" class="content">
					<?= nl2br($model->yearEvaluations[$i]->principalApproval) ?>
				</td>
			</tr>
			<tr id="principal_approval">
				<td colspan="4" class="empty"></td>
				<td colspan="3" class="date" style="height: 10px;">
				<?php $timestamp = strtotime($model->yearEvaluations[$i]->date);
						echo $model->yearEvaluations[$i]->school->address->district->name.', ngày '.date('d', $timestamp).' tháng '.date('m', $timestamp).' năm '.date('Y', $timestamp); ?>
				</td>
			</tr>
			<tr id="principal_approval">
				<td colspan="4" class="empty"></td>
				<td colspan="3" class="title2">Hiệu trưởng </td>
			</tr>
			<tr id="principal_approval">
				<td colspan="4" class="empty"></td>
				<td colspan="3" class="title3">(Phê duyệt, ký, ghi rõ họ tên và đóng dấu)</td>
			</tr>
			<tr id="principal_approval">
				<td colspan="4" class="last_empty"></td>
				<td colspan="3" class="principal_name">
					<?= $model->yearEvaluations[$i]->principalName ?>
				</td>
			</tr>
			
		</table>
	</div>	
	<?php }
	} ?>
</div>
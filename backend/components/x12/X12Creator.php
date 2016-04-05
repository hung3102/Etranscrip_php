<?php
namespace backend\components\x12;

use Yii;
use backend\components\x12;
use common\models\SchoolReport;
use common\models\Student;
use common\models\Address;
use common\models\Province;
use common\models\StudyProcess;
use common\models\YearEvaluation;
use common\models\TermEvaluation;
use common\models\SubjectScore;
use common\models\School;
use common\models\Achievement;
use common\models\RelationStudentObject;

class X12Creator {

	private $transactionCount = 0;

	public function create($schoolReportIDs) {
		$contents = "";
		$context = new Context('~', '*', ':');
		$contents .= $this->createISA($context) . "\n";
		$contents .= $this->createGS($context) . "\n";
		$contents .= $this->createST($context) . "\n";

		foreach ($schoolReportIDs as $schoolReportID) {
			$contents .= $this->createSchoolReport($context, $schoolReportID);	
		}

		$contents .= $this->createSE($context) . "\n";
		$contents .= $this->createGE($context) . "\n";
		$contents .= $this->createIEA($context) . "\n";

		return $contents;
	}

	private function createISA($context) {
		date_default_timezone_set('Asia/Saigon');
		$eSeparator = $context->getElementSeparator();
		$this->transactionCount++;
		return "ISA" . $eSeparator . "00" . $eSeparator . "          " . $eSeparator . "00" 
			. $eSeparator . "          " . $eSeparator . "ZZ" . $eSeparator . "SENDERID      " 
			. $eSeparator . "12" . $eSeparator . "RECEIVERID    " . $eSeparator . date('Ymd') 
			. $eSeparator . date('Hi') . $eSeparator . "U" . $eSeparator . "00401" . $eSeparator 
			. "000000001" . $eSeparator . "0" . $eSeparator . "T" . $eSeparator 
			. $context->getCompositeElementSeparator() . $context->getSegmentSeparator();
	}

	private function createGS($context) {
		date_default_timezone_set('Asia/Saigon');
		$eSeparator = $context->getElementSeparator();
		$this->transactionCount++;
		return "GS" . $eSeparator . "1212" . $eSeparator . "SENDERID" . $eSeparator . "RECEIVERID" . $eSeparator . date('Ymd') . $eSeparator . date('Hi') . $eSeparator . "000000001" . $eSeparator . "X" . $eSeparator . "00401" . $context->getSegmentSeparator();
	}

	private function createST($context) {
		$this->transactionCount++;
		return "ST" . $context->getElementSeparator() . "835" . $context->getElementSeparator() ."000000001" . $context->getSegmentSeparator();
	}

	private function createSchoolReport($context, $schoolReportID) {
		$schoolReport = SchoolReport::findOne($schoolReportID);
		if($schoolReport == null) {
			throw new Exception("SchoolReport not found with id ".$schoolReportID, 1);
		}
		$eSeparator = $context->getElementSeparator();
		$this->transactionCount++;
		return "SR" . $eSeparator . $schoolReport->number . $eSeparator . $schoolReport->date 
			. $context->getSegmentSeparator() . "\n" 
			. $this->createStudent($context, $schoolReport->studentID) . "\n"
			. $this->createStudyProcess($context, $schoolReportID)
			. $this->createYearEvaluation($context, $schoolReportID);
	}

	private function createStudent($context, $studentID) {
		$student = Student::findOne($studentID);
		if($student == null) {
			throw new Exception("Student not found with id ".$studentID, 1);
		}
		$gender = $student->getGenderText();
		$eSeparator = $context->getElementSeparator();
		$this->transactionCount++;
		return "STD" . $eSeparator . $student->name . $eSeparator . $gender . $eSeparator 
			. $student->birthday . $eSeparator . $student->ethnic->name . $eSeparator 
			. $student->religion->name . $eSeparator . $student->fatherName . $eSeparator 
			. $student->fatherJob . $eSeparator . $student->motherName . $eSeparator 
			. $student->motherJob . $eSeparator . $student->tutorName . $eSeparator 
			. $student->tutorJob . $context->getSegmentSeparator() . "\n" 
			. $this->createObjects($context, $student)
			. $this->createCurrentAddress($context, $student->currentAddressID) . "\n"
			. $this->createNativeAddress($context, $student->nativeAddressID);
	}

	private function createObjects($context, $student) {
		$objects = $student->objects;
		$return = "";
		$eSeparator = $context->getElementSeparator();
		if($objects != null) {
			foreach ($objects as $object) {
				$this->transactionCount++;
				$return .= "OJ" . $eSeparator . $object->content 
					. $context->getSegmentSeparator() . "\n";
			}
		}
		return $return;
	}

	private function createCurrentAddress($context, $currentAddressID) {
		$currentAddress = Address::findOne($currentAddressID);
		if($currentAddress == null) {
			throw new Exception("Current Address not found with id ".$currentAddressID, 1);
		}
		$eSeparator = $context->getElementSeparator();
		$this->transactionCount++;
		return "CA" . $eSeparator . $currentAddress->detailAddress . $eSeparator 
			. $currentAddress->getCommuneName() . $eSeparator . $currentAddress->getDistrictName() 
			. $eSeparator . $currentAddress->district->getProvinceName() 
			. $context->getSegmentSeparator();
	}

	private function createNativeAddress($context, $nativeAddressID) {
		$nativeAddress = Address::findOne($nativeAddressID);
		if($nativeAddress == null) {
			throw new Exception("Native Address not found with id ".$nativeAddressID, 1);
		}
		$eSeparator = $context->getElementSeparator();
		$this->transactionCount++;
		return "NA" . $eSeparator . $nativeAddress->detailAddress . $eSeparator 
			. $nativeAddress->getCommuneName() . $eSeparator . $nativeAddress->getDistrictName() 
			. $eSeparator . $nativeAddress->district->getProvinceName() 
			. $context->getSegmentSeparator();
	}

	private function createStudyProcess($context, $schoolReportID) {
		$studyProcesses = StudyProcess::findAll(['schoolReportID' => $schoolReportID]);
		if($studyProcesses == null) {
			throw new Exception("StudyProcess is not found with schoolReportID ".$schoolReportID, 1);
		}
		$eSeparator = $context->getElementSeparator();
		$return = "";
		for ($i=0; $i < count($studyProcesses); $i++) {
			$this->transactionCount++;
			$return .= "SP" . $eSeparator . ($i+1) . $eSeparator  . $studyProcesses[$i]->fromYear 
				. $eSeparator . $studyProcesses[$i]->toYear . $eSeparator
				. $studyProcesses[$i]->class . $eSeparator . $studyProcesses[$i]->principalName 
				. $context->getSegmentSeparator() . "\n"
				. $this->createSchool($context, $studyProcesses[$i]->schoolID);
		}
		return $return;
	}

	private function createSchool($context, $schoolID) {
		$school = School::findOne($schoolID);
		if($school == null) {
			throw new Exception("Error : School is not found with id ".$schoolID, 1);
		}
		$eSeparator = $context->getElementSeparator();
		$this->transactionCount++;
		return "SCH" . $eSeparator . $school->name . $eSeparator . $school->address->detailAddress 
			. $eSeparator . $school->address->getCommuneName() . $eSeparator
			. $school->address->getDistrictName() . $eSeparator
			. $school->address->district->getProvinceName() . $context->getSegmentSeparator() . "\n";
	}

	private function createYearEvaluation($context, $schoolReportID) {
		$yearEvaluations = YearEvaluation::findAll(['schoolReportID' => $schoolReportID]);
		if($yearEvaluations == null) {
			throw new Exception("YearEvaluation is not found with schoolReportID ".$schoolReportID, 1);
		}
		$eSeparator = $context->getElementSeparator();
		$return = "";
		for ($i=0; $i < count($yearEvaluations); $i++) {
			$this->transactionCount++;
			$return .= "YE" . $eSeparator . ($i+1) . $eSeparator . $yearEvaluations[$i]->class 
				. $eSeparator . $yearEvaluations[$i]->fromYear . $eSeparator
				. $yearEvaluations[$i]->toYear . $eSeparator . $yearEvaluations[$i]->studyDepartment 
				. $eSeparator . $yearEvaluations[$i]->note . $eSeparator 
				. $yearEvaluations[$i]->teacherName . $eSeparator . $yearEvaluations[$i]->missedLesson 
				. $eSeparator . $yearEvaluations[$i]->upGradeType . $eSeparator
				. $yearEvaluations[$i]->vocationalCertificate . $eSeparator
				. $yearEvaluations[$i]->vocationalCertificateLevel . $eSeparator 
				. $yearEvaluations[$i]->teacherComment . $eSeparator 
				. $yearEvaluations[$i]->principalApproval . $eSeparator 
				. $yearEvaluations[$i]->principalName . $eSeparator . $yearEvaluations[$i]->date 
				. $context->getSegmentSeparator() . "\n" 
				. $this->createAchievement($context, $yearEvaluations[$i]->id)
				. $this->createTermEvaluation($context, $yearEvaluations[$i]->id);
		}
		return $return;
	}

	private function createAchievement($context, $yearEvaluationID) {
		$achievements = Achievement::findAll(['yearEvaluationID' => $yearEvaluationID]);
		$return = "";
		if($achievements != null) {
			$eSeparator = $context->getElementSeparator();
			foreach ($achievements as $achievement) {
				$this->transactionCount++;
				$return .= "ACV" . $eSeparator . $achievement->name 
					. $context->getSegmentSeparator() . "\n";
			}
		}
		return $return;
	}

	private function createTermEvaluation($context, $yearEvaluationID) {
		$termEvaluations = TermEvaluation::findAll(['yearEvaluationID' => $yearEvaluationID]);
		if($termEvaluations == null) {
			throw new Exception("TermEvaluation is not found with yearEvaluationID "
				.$yearEvaluationID, 1);
		}
		$eSeparator = $context->getElementSeparator();
		$return = "";
		$i = 1;
		foreach ($termEvaluations as $termEvaluation) {
			$this->transactionCount++;
			$return .= "TE" . $eSeparator . $i++ . $eSeparator . $termEvaluation->getTermText() 
			. $eSeparator . $termEvaluation->learnCapacity . $eSeparator
			. $termEvaluation->conduct . $context->getSegmentSeparator() . "\n"
			. $this->createSubjectScore($context, $termEvaluation->id);
		}
		return $return;
	}

	private function createSubjectScore($context, $termEvaluationID) {
		$subjectScores = SubjectScore::findAll(['termEvaluationID' => $termEvaluationID]);
		if($subjectScores == null) {
			throw new Exception("SubjectScore is not found with termEvaluationID ". $termEvaluationID, 1);
		}
		$eSeparator = $context->getElementSeparator();
		$return = "";
		$i = 1;
		foreach ($subjectScores as $subjectScore) {
			$this->transactionCount++;
			$return .= "SS" . $eSeparator . $i++ . $eSeparator . $subjectScore->subject->name 
				. $eSeparator . $subjectScore->score . $eSeparator 
				. $subjectScore->teacherName . $context->getSegmentSeparator() . "\n";
		}
		return $return;
	}

	private function createSE($context) {
		return "SE" . $context->getElementSeparator() . $this->transactionCount 
			. $context->getElementSeparator() . "000000001" . $context->getSegmentSeparator();
	}

	private function createGE($context) {
		return "GE" . $context->getElementSeparator() . "1" . $context->getElementSeparator() . "000000001" . $context->getSegmentSeparator();
	}

	private function createIEA($context) {
		return "IEA" . $context->getElementSeparator() . "1" . $context->getElementSeparator() . "000000001" . $context->getSegmentSeparator();
	}

}
?>
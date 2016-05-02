<?php
namespace common\models;

use Yii;
use yii\base\Exception;
use common\models\SchoolReport;
use common\models\Student;
use common\models\Address;
use common\models\Province;
use common\models\District;
use common\models\Commune;
use common\models\Ethnic;
use common\models\Religion;
use common\models\School;
use common\models\YearEvaluation;
use common\models\TermEvaluation;
use common\models\Subject;
use common\models\SubjectScore;

class X12Integration {

	public function integrate($x12) {
		$this->createSchoolReportModel($x12);
		return true;
	}

	private function createSchoolReportModel($x12) {
		$transaction = Yii::$app->db->beginTransaction();
		try {
			$srLoops = $x12->findLoop("SR");
	        if($srLoops == null) {
	            throw new Exception("Error: School Report must exist in x12 file", 1);
	        }
	        foreach ($srLoops as $srLoop) {
	        	$schoolReport = SchoolReport::findOne(['number' => $srLoop->getSegment(0)->getElement(1)]);
	    		if($schoolReport == null) {
	        		$schoolReport = new SchoolReport();
	        		$schoolReport->number = $srLoop->getSegment(0)->getElement(1);
	        		$schoolReport->date = $srLoop->getSegment(0)->getElement(2);
	        		$schoolReport->principalName = $srLoop->getSegment(0)->getElement(3);
	        		$schoolReport->studentID = $this->createStudentModel($srLoop)->id;
	        		$schoolReport->save();
	        		// $this->createStudyProcessModel($srLoop, $schoolReport);
	        		$this->createYearEvaluationModel($srLoop, $schoolReport);
	        	} else {
	        		$schoolReport->date = $srLoop->getSegment(0)->getElement(2);
	        		$schoolReport->principalName = $srLoop->getSegment(0)->getElement(3);
	        		$schoolReport->studentID = $this->createStudentModel($srLoop, $schoolReport, true)->id;
	        		$schoolReport->save();
	        		// $this->createStudyProcessModel($srLoop, $schoolReport, true);
	        		$this->createYearEvaluationModel($srLoop, $schoolReport, true);
	        	}
	        }
	        $transaction->commit();
		} catch (Exception $e) {
			$transaction->rollBack();
			throw $e;
		}
        return true;
	}

	private function createStudentModel($srLoop, $schoolReport = null, $SR_EXIST = false) {
		$stdLoops = $srLoop->findLoop("STD");
        if($stdLoops == null) {
        	throw new Exception("Error: Student is must exist in School Report", 1);	
        } else if(count($stdLoops) != 1) {
        	throw new Exception("Error: Student is must exist only one in School Report", 1);
        }
        $stdLoop = $stdLoops[0];
		$attributes = [
			'name' => $stdLoop->getSegment(0)->getElement(1),
			'gender' => array_search($stdLoop->getSegment(0)->getElement(2), Student::$gender),
			'birthday' => $stdLoop->getSegment(0)->getElement(3),
			'fatherName' => $stdLoop->getSegment(0)->getElement(5),
			'fatherJob' => $stdLoop->getSegment(0)->getElement(6),
			'motherName' => $stdLoop->getSegment(0)->getElement(7),
			'motherJob' => $stdLoop->getSegment(0)->getElement(8),
			'tutorName' => $stdLoop->getSegment(0)->getElement(9),
			'tutorJob' => $stdLoop->getSegment(0)->getElement(10),
			'currentAddressID' => $this->createCurrentAddressModel($stdLoop)->id,
			'nativeAddressID' => $this->createNativeAddressModel($stdLoop)->id,
			'ethnicID' => $this->getEthnic($stdLoop->getSegment(0)->getElement(4))->id,
		];
		if($SR_EXIST == false) {
			$student = Student::findOne($attributes);
			if($student == null) {
				$student = new Student($attributes);
				$student->save();
			}
		} else {
			$student = $schoolReport->student;
			$student->attributes = $attributes;
			$student->save();
		}
		$this->createObjectModel($stdLoop, $student, $SR_EXIST);
		return $student;
	}

	private function createObjectModel($stdLoop, $student, $SR_EXIST = false) {
		if($SR_EXIST == true && $student->objects != null) {
			foreach ($student->objects as $object) {
				$student->unlink('objects', $object, true);	
			}
		}
		$ojLoops = $stdLoop->findLoop("OJ");
		if($ojLoops != null) {
			foreach ($ojLoops as $ojLoop) {
				$object = Object::findOne(['content' => $ojLoop->getSegment(0)->getElement(1)]);
				if($object == null) {
					throw new Exception(
						"Error : Not found object ". $ojLoop->getSegment(0)->getElement(1), 1
					);
				}
				date_default_timezone_set('Asia/Saigon');
				$student->link('objects', $object, [
					'created_time' => date('Y:m:d h:i:s'),
					'updated_time' => date('Y:m:d h:i:s'),
				]);
			}
		}
		return true;
	}

	private function createCurrentAddressModel($stdLoop) {
		$caLoops = $stdLoop->findLoop("CA");
		if($caLoops == null) {
        	throw new Exception("Error: Current Address is must exist in Student", 1);	
        } else if(count($caLoops) != 1) {
        	throw new Exception("Error: Current Address is must exist only one in Student", 1);
        }
        $caLoop = $caLoops[0];
        $province = $this->getProvince($caLoop->getSegment(0)->getElement(4));
        $district = $this->getDistrict($caLoop->getSegment(0)->getElement(3), $province);
        $commune = $this->getCommune($caLoop->getSegment(0)->getElement(2), $district);
        $attributes = [
			'detailAddress' => $caLoop->getSegment(0)->getElement(1),
			'communeID' => $commune->id,
			'districtID' => $district->id,
		];
		$address = Address::findOne($attributes);
		if($address == null) {
			$address = new Address($attributes);
			$address->save();
		}
		return $address;
	}

	private function createNativeAddressModel($stdLoop) {
		$naLoops = $stdLoop->findLoop("NA");
		if($naLoops == null) {
        	throw new Exception("Error: Native Address is must exist in Student", 1);	
        } else if(count($naLoops) != 1) {
        	throw new Exception("Error: Native Address is must exist only one in Student", 1);
        }
        $naLoop = $naLoops[0];
        $province = $this->getProvince($naLoop->getSegment(0)->getElement(4));
        $district = $this->getDistrict($naLoop->getSegment(0)->getElement(3), $province);
        $commune = $this->getCommune($naLoop->getSegment(0)->getElement(2), $district);
        $attributes = [
			'detailAddress' => $naLoop->getSegment(0)->getElement(1),
			'communeID' => $commune->id,
			'districtID' => $district->id,
		];
		$address = Address::findOne($attributes);
		if($address == null) {
			$address = new Address($attributes);
			$address->save();
		}
		return $address;
	}

	private function getProvince($name) {
		$province = Province::findOne(['name' => $name]);
		if($province == null) {
			throw new Exception("Error : Not found province ".$name, 1);
		}
		return $province;
	}

	private function getDistrict($name, $province) {
		$district = null;
		if($province->districts != null) {
			foreach ($province->districts as $check) {
				if($check->name == $name) {
					$district = $check;
					break;
				}
			}
		}
		if($district == null) {
			throw new Exception("Error : Not found district ".$name." of province ".$province->name, 1);
		}
		return $district;
	}

	private function getCommune($name, $district) {
		$commune = null;
		if($district->communes != null) {
			foreach ($district->communes as $check) {
				if($check->name == $name) {
					$commune = $check;
					break;
				}
			}
		}
		if($commune == null) {
			throw new Exception("Error : Not found commune ".$name." of district ".$district->name, 1);
		}
		return $commune;
	}

	private function getEthnic($name) {
		$ethnic = Ethnic::findOne(['name' => $name]);
		if($ethnic == null) {
			throw new Exception("Error : Not found ethnic ".$name, 1);
		}
		return $ethnic;
	}

	// private function getReligion($name) {
	// 	$religion = Religion::findOne(['name' => $name]);
	// 	if($religion == null) {
	// 		throw new Exception("Error : Not found religion ".$name, 1);
	// 	}
	// 	return $religion;
	// }

	// private function createStudyProcessModel($srLoop, $schoolReport, $SR_EXIST = false) {
	// 	if($SR_EXIST == true && $schoolReport->studyProcesses != null) {
	// 		foreach ($schoolReport->studyProcesses as $studyProcess) {
	// 			$schoolReport->unlink('studyProcesses', $studyProcess, true);
	// 		}
	// 	}
	// 	$spLoops = $srLoop->findLoop("SP");
	// 	if($spLoops == null) {
	// 		throw new Exception("Error: Study Process must exist in School Report", 1);
	// 	}
	// 	foreach ($spLoops as $spLoop) {
	// 		$attributes = [
	// 			'fromYear' => $spLoop->getSegment(0)->getElement(2),
	// 			'toYear' => $spLoop->getSegment(0)->getElement(3),
	// 			'class' => $spLoop->getSegment(0)->getElement(4),
	// 			'schoolID' => $this->getSchool($spLoop)->id,
	// 			'principalName' => $spLoop->getSegment(0)->getElement(5),
	// 			'schoolReportID' => $schoolReport->id,
	// 		];
	// 		$studyProcess = studyProcess::findOne($attributes);
	// 		if($studyProcess == null) {
	// 			$studyProcess = new studyProcess($attributes);
	// 			$studyProcess->save();
	// 		} else {
	// 			throw new Exception("Error : Detect Study Process duplicated in DB!", 1);
	// 		}
	// 	}
	// 	return true;
	// }

	private function createYearEvaluationModel($srLoop, $schoolReport, $SR_EXIST = false) {
		if($SR_EXIST == true) {
			$this->removeRelatedYearEvaluation($schoolReport);
		}
		$yeLoops = $srLoop->findLoop("YE");
		if($yeLoops == null) {
			throw new Exception("Error : Year Evaluation must be exist in School Report", 1);
		}
		foreach ($yeLoops as $yeLoop) {
			$attributes = [
				'schoolReportID' => $schoolReport->id,
				'class' => $yeLoop->getSegment(0)->getElement(2),
				'fromYear' => $yeLoop->getSegment(0)->getElement(3),
				'toYear' => $yeLoop->getSegment(0)->getElement(4),
				'schoolID' => $this->getSchool($yeLoop)->id,
				'studyDepartment' => $yeLoop->getSegment(0)->getElement(5),
				'note' => $yeLoop->getSegment(0)->getElement(6),
				'teacherName' => $yeLoop->getSegment(0)->getElement(7),
				'missedLesson' => $yeLoop->getSegment(0)->getElement(8),
				'upGradeType' => $yeLoop->getSegment(0)->getElement(9),
				'vocationalCertificate' => $yeLoop->getSegment(0)->getElement(10),
				'vocationalCertificateLevel' => $yeLoop->getSegment(0)->getElement(11),
				'teacherComment' => $yeLoop->getSegment(0)->getElement(12),
				'principalApproval' => $yeLoop->getSegment(0)->getElement(13),
				'principalName' => $yeLoop->getSegment(0)->getElement(14),
				'date' => $yeLoop->getSegment(0)->getElement(15),
			];
			$yearEvaluation = YearEvaluation::findOne($attributes);
			if($yearEvaluation == null) {
				$yearEvaluation = new YearEvaluation($attributes);
				$yearEvaluation->save();
			} else {
				throw new Exception("Error : Detect Year Evaluation duplicated in DB!", 1);
			}
			$this->createAchievementModel($yeLoop, $yearEvaluation);
			$this->createTermEvaluation($yeLoop, $yearEvaluation);
		}
		return true;
	}

	private function getSchool($yeLoop) {
		$schLoops = $yeLoop->findLoop("SCH");
		if($schLoops == null) {
			throw new Exception("Error : School must be exist in Year Evaluation", 1);
		} else if(count($schLoops) != 1) {
			throw new Exception("Error : School must be exist only one in Year Evaluation", 1);
		}
		$schLoop = $schLoops[0];
		$address = $this->createSchoolAddressModel($schLoop);
		$attributes = [
			'name' => $schLoop->getSegment(0)->getElement(1),
			'addressID' => $address->id,
		];
		$school = School::findOne($attributes);
		if($school == null) {
			throw new Exception("Error : Not found school ".$attributes['name']." at "
				.$address->getFullAddress(), 1);
		}
		return $school;
	}

	private function createSchoolAddressModel($schLoop) {
        $province = $this->getProvince($schLoop->getSegment(0)->getElement(5));
        $district = $this->getDistrict($schLoop->getSegment(0)->getElement(4), $province);
        $commune = $this->getCommune($schLoop->getSegment(0)->getElement(3), $district);
        $attributes = [
			'detailAddress' => $schLoop->getSegment(0)->getElement(2),
			'communeID' => $commune->id,
			'districtID' => $district->id,
		];
		$address = Address::findOne($attributes);
		if($address == null) {
			$address = new Address($attributes);
			$address->save();
		}
		return $address;				
	}

	private function createAchievementModel($yeLoop, $yearEvaluation) {
		$acvLoops = $yeLoop->findLoop("ACV");
		if($acvLoops != null) {
			foreach ($acvLoops as $acvLoop) {
				$attributes = [
					'name' => $acvLoop->getSegment(0)->getElement(1),
					'yearEvaluationID' => $yearEvaluation->id,
				];
				$achievement = Achievement::findOne($attributes);
				if($achievement == null) {
					$achievement = new Achievement($attributes);
					$achievement->save();
				} else {
					throw new Exception("Error : Detect achievement duplicated in DB!", 1);
				}
			}
		}
		return true;
	}

	private function createTermEvaluation($yeLoop, $yearEvaluation) {
		$teLoops = $yeLoop->findLoop("TE");
		if($teLoops == null) {
			throw new Exception("Error : Term Evaluation must be exist in Year Evaluation", 1);
		}
		foreach ($teLoops as $teLoop) {
			$attributes = [
				'yearEvaluationID' => $yearEvaluation->id,
				'term' => TermEvaluation::getTermIndex($teLoop->getSegment(0)->getElement(2)),
				'learnCapacity' => $teLoop->getSegment(0)->getElement(3),
				'conduct' => $teLoop->getSegment(0)->getElement(4),
			];
			$termEvaluation = TermEvaluation::findOne($attributes);
			if($termEvaluation == null) {
				$termEvaluation = new TermEvaluation($attributes);
				$termEvaluation->save();
			} else {
				throw new Exception("Error : Detect Term Evaluation duplicated in DB!", 1);
			}
			$this->createSubjectScoreModel($teLoop, $termEvaluation);
		}
		return true;
	}

	private function createSubjectScoreModel($teLoop, $termEvaluation) {
		$ssLoops = $teLoop->findLoop("SS");
		if($ssLoops == null) {
			throw new Exception("Error : Subject Score must be exist in Term Evaluation", 1);
		}
		foreach ($ssLoops as $ssLoop) {
			$attributes = [
				'termEvaluationID' => $termEvaluation->id,
				'subjectID' => $this->getSubject($ssLoop)->id,
				'score' => $ssLoop->getSegment(0)->getElement(3),
				'teacherName' => $ssLoop->getSegment(0)->getElement(4),
			];
			$subjectScore = SubjectScore::findOne($attributes);
			if($subjectScore == null) {
				$subjectScore = new SubjectScore($attributes);
				$subjectScore->save();
			} else {
				throw new Exception("Error : Detect Subject Score duplicated in DB!", 1);
			}
		}
		return true;
	}

	private function getSubject($ssLoop) {
		$subject = Subject::findOne(['name' => $ssLoop->getSegment(0)->getElement(2)]);
		if($subject == null) {
			throw new Exception("Error : Not found subject ".$ssLoop->getSegment(0)->getElement(2), 1);
		}
		return $subject;
	}

	//========= if schoolreport exist =============//
	private function removeRelatedYearEvaluation($schoolReport) {
		if($schoolReport->yearEvaluations != null) {
			foreach ($schoolReport->yearEvaluations as $yearEvaluation) {
				$this->removeRelatedAchievement($yearEvaluation);
				$this->removeRelatedTermEvaluation($yearEvaluation);
				$schoolReport->unlink('yearEvaluations', $yearEvaluation, true);
			}
		}
		return true;
	}

	private function removeRelatedAchievement($yearEvaluation) {
		if($yearEvaluation->achievements != null) {
			foreach ($yearEvaluation->achievements as $achievement) {
				$yearEvaluation->unlink('achievements', $achievement, true);
			}
		}
		return true;
	}

	private function removeRelatedTermEvaluation($yearEvaluation) {
		if($yearEvaluation->termEvaluations != null) {
			foreach ($yearEvaluation->termEvaluations as $termEvaluation) {
				$this->removeRelatedSubjectScore($termEvaluation);
				$yearEvaluation->unlink('termEvaluations', $termEvaluation, true);
			}
		}
		return true;
	}

	private function removeRelatedSubjectScore($termEvaluation) {
		if($termEvaluation->subjectScores != null) {
			foreach ($termEvaluation->subjectScores as $subjectScore) {
				$termEvaluation->unlink('subjectScores', $subjectScore, true);
			}
		}
		return true;
	}
}

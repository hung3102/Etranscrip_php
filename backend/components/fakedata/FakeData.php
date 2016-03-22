<?php
namespace backend\components\fakedata;

use \tebazil\yii2seeder\Seeder;
use Yii;
use common\models\District;
use common\models\Commune;

class FakeData {
	
	public function fakeProvince() {
		$provinces = ['Nghệ an', 'Hà nội'];
		$seeder = new Seeder();
		$generator = $seeder->getGeneratorConfigurator();
		$faker = $generator->getFakerConfigurator();

		$i = 0;
		date_default_timezone_set('Asia/Saigon');
		$seeder->table('tbl_province')->columns([
		    'id', //automatic pk
		    'name' => function() use ($provinces, &$i){
		    	return $provinces[$i++];
		    },
		    'created_time' => date('Y-m-d H:i:s'),
		    'updated_time' => date('Y-m-d H:i:s'),
	    ])->rowQuantity(2);
		 
		if($seeder->refill())
			return true;
		return false;
	}

	public function fakeDistrict() {
		$districts = ['Thành phố Vinh', 'Thị xã Cửa Lò', 'Thị xã Hoàng Mai', 'Thị xã Thái Hòa', 'Huyện Quỳ Châu', 'Huyện Quỳ Hợp', 'Huyện Nghĩa Đàn', 'Huyện Quỳnh Lưu', 'Huyện Kỳ Sơn', 'Huyện Tương Dương', 'Huyện Con Cuông', 'Huyện Tân Kỳ', 'Huyện Yên Thành', 'Huyện Diễn Châu', 'Huyện Anh Sơn', 'Huyện Đô Lương', 'Huyện Thanh Chương', 'Huyện Nghi Lộc', 'Huyện Nam Đàn', 'Huyện Hưng Nguyên', 'Huyện Quế Phong'];
		$seeder = new Seeder();
		$generator = $seeder->getGeneratorConfigurator();
		$faker = $generator->getFakerConfigurator();

		$i = 0;
		date_default_timezone_set('Asia/Saigon');
		$seeder->table('tbl_district')->columns([
		    'id', //automatic pk
		    'name' => function() use ($districts, &$i){
		    	return $districts[$i++];
		    },
		    'provinceID' => 1,
		    'created_time' => date('Y-m-d H:i:s'),
		    'updated_time' => date('Y-m-d H:i:s'),
	    ])->rowQuantity(21);
		 
		if($seeder->refill())
			return true;
		return false;
	}

	public function fakeCommune() {
		$seeder = new Seeder();
		$generator = $seeder->getGeneratorConfigurator();
		$faker = $generator->getFakerConfigurator();

		$file = Yii::$app->basePath.'/components/fakedata/NA_Districts.txt';
		$communes = $this->readCommunes($file);
		$ccount = 0;
		$dcount = 0;
		$rowQuantity = 0;
		foreach ($communes as $district) {
			$rowQuantity += count($district['communes']);
		}
		$districtID = $districtID = District::find()->where('name = :name', [
		    			'name'=>$communes[$dcount]['district']
		    		])->one()->id;
		date_default_timezone_set('Asia/Saigon');
		$seeder->table('tbl_commune')->columns([
		    'id', //automatic pk
		    'districtID' => function() use (&$districtID) {
		    	return $districtID;
		    },
		    'name' => function() use ($communes, &$districtID, &$ccount, &$dcount){
		    	$data = $communes[$dcount]['communes'][$ccount++];
		    	if($ccount == count($communes[$dcount]['communes'])) {
		    		$dcount++;
		    		if($dcount < count($communes))
		    			$districtID = District::find()->where('name = :name', [
		    				'name'=>$communes[$dcount]['district'],
		    			])->one()->id;
		    		$ccount = 0;
		    	}
		    	return $data;
		    },
		    'created_time' => date('Y-m-d H:i:s'),
		    'updated_time' => date('Y-m-d H:i:s'),
	    ])->rowQuantity($rowQuantity);
		 
		if($seeder->refill())
			return true;
		return false;
	}

	public function fakeAddress() {
		$seeder = new Seeder();
		$generator = $seeder->getGeneratorConfigurator();
		$faker = $generator->getFakerConfigurator();

		$hamlets = ['Xóm 1', 'Xóm 2', 'Xóm 3', 'Xóm 4', 'Xóm 5', 'Xóm 6', 'Xóm 7', 'Xóm 8', 'Xóm 9', 'Xóm 10', 'Xóm 11', 'Xóm 12', 'Xóm 13'];
		$ham_count = count($hamlets);
		$cSize = count(Commune::find('')->all());
		$ccount = 1;
		$i = 0;

		$dcount = Commune::find()->where('id = :id', ['id' => $ccount])->one()->districtID;
		$communeSize = count(Commune::find('')->all());
		date_default_timezone_set('Asia/Saigon');
		$seeder->table('tbl_address')->columns([
		    'id', //automatic pk
		    'detailAddress' => function() use ($hamlets, &$i){
		    	return $hamlets[$i++];
		    },
		    'districtID' => function() use (&$dcount) {
		    	return $dcount;
		    },
		    'communeID' => function() use ($cSize, $ham_count, &$ccount, &$i, &$dcount) {
		    	$return = $ccount;
		    	if($i == $ham_count && $ccount < $cSize) {
		    		$ccount++;
		    		$dcount = Commune::find()->where('id = :id', ['id' => $ccount])->one()->districtID;
		    		$i = 0;
		    	}
		    	return $return;
		    },
		    'created_time' => date('Y-m-d H:i:s'),
		    'updated_time' => date('Y-m-d H:i:s'),
	    ])->rowQuantity($communeSize * $ham_count);
		 
		if($seeder->refill())
			return true;
		return false;
	}

	private function readCommunes($file) {
		$f = fopen($file, 'r');
		$communes = [];
		while (!feof($f)) {
			$each_district = [];
			$communes_array = [];
			$line = fgets($f);
			$array = preg_split('/( : |, |.\n|.$)/', $line, -1, PREG_SPLIT_NO_EMPTY);
			$each_district['district'] = trim($array[0]);
			for ($i=1; $i < count($array); $i++) { 
				$commune = trim($array[$i]);
				$communes_array[] = $commune;
			}
			$each_district['communes'] = $communes_array;
			$communes[] = $each_district;
		}
		fclose($f);

		return $communes;
	}

	public function fakeReligion() {
		$religions = [];
		$seeder = new Seeder();
		$generator = $seeder->getGeneratorConfigurator();
		$faker = $generator->getFakerConfigurator();

		$i = 0;
		date_default_timezone_set('Asia/Saigon');
		$seeder->table('tbl_religion')->columns([
		    'id', //automatic pk
		    'name' => function() use ($religions, &$i){
		    	return $religions[$i++];
		    },
		    'created_time' => date('Y-m-d H:i:s'),
		    'updated_time' => date('Y-m-d H:i:s'),
	    ])->rowQuantity(count($religions));
		 
		if($seeder->refill())
			return true;
		return false;
	}

	public function fakeEthnic() {
		$ethnics = [];
		$seeder = new Seeder();
		$generator = $seeder->getGeneratorConfigurator();
		$faker = $generator->getFakerConfigurator();

		$i = 0;
		date_default_timezone_set('Asia/Saigon');
		$seeder->table('tbl_ethnic')->columns([
		    'id', //automatic pk
		    'name' => function() use ($ethnics, &$i){
		    	return $ethnics[$i++];
		    },
		    'created_time' => date('Y-m-d H:i:s'),
		    'updated_time' => date('Y-m-d H:i:s'),
	    ])->rowQuantity(count($ethnics));
		 
		if($seeder->refill())
			return true;
		return false;	
	}

	public function fakeSchool() {
		$schools = [];
		$seeder = new Seeder();
		$generator = $seeder->getGeneratorConfigurator();
		$faker = $generator->getFakerConfigurator();

		$i = 0;
		date_default_timezone_set('Asia/Saigon');
		$seeder->table('tbl_school')->columns([
		    'id', //automatic pk
		    'name' => function() use ($schools, &$i){
		    	return $schools[$i]['school'];
		    },
		    'addressID' => function() use ($schools, &$i) {
		    	$hamlet = 'Xóm ' . rand(1,13);
				return Address::find()->where(
					'detailAddress = :dAddress, districtID = :dID, provinceID = 1', 
					[
						'dAddress' => $hamlet,
						'iID' => $schools[$i++]['districtID'];
					])->one()->id;
		    }
		    'created_time' => date('Y-m-d H:i:s'),
		    'updated_time' => date('Y-m-d H:i:s'),
	    ])->rowQuantity(count($schools));
		 
		if($seeder->refill())
			return true;
		return false;		
	}

	private function readSchools($file) {
		$f = fopen($file, 'r');
		$schools = [];
		while (!feof($f)) {
			$each = [];
			$line = fgets($f);
			$array = preg_split('/( : |.\n|.$)/', $line, -1, PREG_SPLIT_NO_EMPTY);
			$each['school'] = trim($array[0]);
			$districtID = District::find()->where('name = :name', ['name' => trim($array[2])])
					->one()->id;
			$each['districtID'] = $districtID;
			$each['communeID'] = Commune::find()->where('name = :name, districtID = :dID', [
				'name' => trim($array[1]),
				'dID' => $districtID,
			])->one()->id;
			$schools[] = $each;
		}
		fclose($f);

		return $schools;
	}

	public function fakeStudent() {
		$firstName = [''];
		$middleName1 = []; $middleName2 = [];
		$lastName1 = []; $lastName2 = [];
		$this->createNames(&$firstName, &$middleName1, &$middleName2, &$lastName1, &$lastName2);

		$ethnicIDs = $this->createEthnicIDs();
		var_dump($ethnicIDs);exit();
		$relIDs = $this->createReligionIDs();
		var_dump($relIDs);exit();

		$job = [];

		$seeder = new Seeder();
		$generator = $seeder->getGeneratorConfigurator();
		$faker = $generator->getFakerConfigurator();

		$i = 0; 
		$first = "";
		$addressID = 0;
		$count['first'] = count($firstName);
		$count['mid1'] = count($middleName1);
		$count['mid2'] = count($middleName2);
		$count['last1'] = count($lastName1);
		$count['last2'] = count($lastName2);
		$count['eth'] = count($ethnicIDs);
		$count['rel'] = count($relIDs);
		$count['job'] = count($jobs);
		$count['address'] = count(Address::find('')->all());
		var_dump($count['address']);exit();

		date_default_timezone_set('Asia/Saigon');
		$seeder->table('tbl_student')->columns([
		    'id', //automatic pk
		    'name' => function() use ($firstName, $middleName1, $middleName2, $lastName1, $lastName2, $count, &$first, &$mid, &$i){
		    	$first = $firstName[rand(0, $count['first']-1)];
		    	if($i = rand(1,2) == 1) {
		    		$mid = $middleName1[rand(0, $count['mid1']-1)];
		    		return $first.' '.$mid.' '.$lastName1[rand(0, $count['last1']-1)];
		    	} else {
		    		return $first.' '.$middleName2[rand(0, $count['mid2']-1)].' '.$lastName2[rand(0, $count['last2']-1)];
		    	}
		    },
		    'gender' => function() use (&$i) {
		    	if($i == 1) return 'Nam';
		    	else return 'Nữ';
		    },
		    'birthday' => function() {

		    },
		    'currentAddressID' => function() use ($count, &$addressID) {
		    	$addressID = Address::find()->where('id = :id', ['id' => rand(1, $count['address'])])->one()->id;
		    },
		    'nativeAddressID' => $addressID,
		    'ethnicID' => function() use ($count) {
		    	return $ethnicIDs[rand(0, $count['eth']-1)];
		    }
		    'religionID' => function() {
		    	return $relIDs[rand(0, $count['rel']-1)];
		    }
		    'fatherName' => function() use ($lastName1, $count, &$first, &$mid) {
		    	return $first.' '.$mid.' '.$lastName1[rand(0, $count['last1']-1)];
		    },
		    'fatherJob' => function() use ($count) {
		    	return $jobs[rand(0, $count['job']-1)];
		    },
		    'motherName' => function() use ($lastName2, $count) {
		    	return $firstName[rand(0, $count['first']-1)].' '.$middleName2[rand(0, $count['mid2']-1)].' '.$lastName2[rand(0, $count['last2']-1)];
		    },
		    'motherJob' => function() use ($count) {
		    	return $jobs[rand(0, $count['job']-1)];
		    },
		    'created_time' => date('Y-m-d H:i:s'),
		    'updated_time' => date('Y-m-d H:i:s'),
	    ])->rowQuantity(200); //200000
	}

	private function createNames(&$firstName, &$middleName1, &$middleName2, &$lastName1, &$lastName2) {
		for ($i=0; $i < 200; $i++) { 
			if($i % 20 == 1) {
				$middleName1[$i] = 'Xuân';
				$middleName2[$i] = 'Xuân';
			} else if($i % 20 == 2) {
				$middleName1[$i] = 'Hải';
				$middleName2[$i] = 'Lan';
			} else if($i % 20 == 3) {
				$middleName1[$i] = 'Trường';
				$middleName2[$i] = 'Thu';
			} else if($i % 20 == 4) $middleName1[$i] = 'Phúc';
			else if($i % 20 == 5) $middleName1[$i] = 'Bá';
			else {
				$middleName1[$i] = 'Văn';
				$middleName2[$i] = 'Thị';
			}
		}
	}

	private function createEthnicIDs() {
		$ethnic_count = count(Ethnic::find('')->all());
		$mainEthnicID = Ethnic::find()->where('name = :name', ['name' => 'Kinh'])->one()->id;
		for ($i=0; $i < $ethnic_count*100; $i++) { 
			$ethnicIDs[$i] = $mainEthnicID;
		}
		for ($i=1; $i <= $ethnic_count; $i++) { 
			$ethnicIDs[$i] = $i;
		}
		return $ethnicIDs;
	}

	private function createReligionIDs() {
		$rel_count = count(Religion::find('')->all());
		$mainRelID = Religion::find()->where('name = :name', ['name' => 'Không'])->one()->id;
		for ($i=0; $i < $rel_count*500; $i++) { 
			$relIDs[$i] = $mainrelID;
		}
		for ($i=1; $i <= $rel_count; $i++) { 
			$relIDs[$i] = $i;
		}
		return $relIDs;
	}

	public function fakeSchoolReport() {
		$seeder = new Seeder();
		$generator = $seeder->getGeneratorConfigurator();
		$faker = $generator->getFakerConfigurator();

		$i = 0;
		$stdCount = count(Student::find('')->all());
		date_default_timezone_set('Asia/Saigon');
		$seeder->table('tbl_school_report')->columns([
			'id',
			'number' => function() use (&$i) {
				return ++$i.'/2016';
			},
			'studentID' => $i,
			'date' => date('Y-m-d',strtotime('20160321')),
			'created_time' => date('Y-m-d H:i:s'),
		    'updated_time' => date('Y-m-d H:i:s'),
		])->rowQuantity(100); //$stdCount
	}

	public function fakeStudyProcess() {
		$seeder = new Seeder();
		$generator = $seeder->getGeneratorConfigurator();
		$faker = $generator->getFakerConfigurator();

		$class = 10;
		$schoolCount = count(School::find('')->all());
		$schoolID = School::find()->where('id = :id', ['id' => rand(1, $schoolCount)])->one()->id;
		$className = ["A1", "A2", "A3", "A", "B", "C", "D", "E"];
		$name = $className[rand(0, count($className)-1)];
		date_default_timezone_set('Asia/Saigon');
		$seeder->table('tbl_study_process')->columns([
			'id',
			'schoolReportID',
			'fromTime' => // random year
			'toTime' => //random year
			'schoolID' => $schoolID,
			'class' => function() use (&$class, &$name, &$schoolID) {
				$return = $class++ . $name;
				if($class == 13) {
					$name = $className[rand(0, count($className)-1)];
					$schoolID = School::find()->where('id = :id', ['id' => rand(1, $schoolCount)])->one()->id;
					$class = 10;
				}
				return $return;
			},
			'created_time' => date('Y-m-d H:i:s'),
		    'updated_time' => date('Y-m-d H:i:s'),
		])->rowQuantity(99); //3*count(Student::find('')->all())
	}

}
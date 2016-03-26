<?php
namespace backend\components\fakedata;

use \tebazil\yii2seeder\Seeder;
use Yii;
use yii\helpers\ArrayHelper;
use common\models\District;
use common\models\Commune;
use common\models\Address;
use common\models\Ethnic;
use common\models\Religion;
use common\models\Student;
use common\models\School;
use common\models\RelationSchoolStudent;
use common\models\StudyProcess;
use common\models\SchoolReport;
use common\models\YearEvaluation;
use common\models\Subject;
use common\models\TermEvaluation;

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
		$religions = ["Không", "Phật giáo", "Công giáo", "Tin Lành", "Cao Đài", "Phật Giáo Hòa Hảo", "Hồi giáo", "Bahá'í", "Tịnh độ cư sĩ Phật hội Việt Nam", "Đạo Tứ Ân Hiếu Nghĩa", "Đạo Bửu Sơn Kỳ Hương", "Minh Sư Đạo", "Minh Lý Đạo", "Bà-la-môn"];
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
		$ethnics = ["Kinh", "Tày", "Thái", "Mường", "Khơ Me", "H'Mông", "Nùng", "Hoa", "Dao", "Gia Rai", "Ê Đê", "Ba Na", "Xơ Đăng", "Sán Chay", "Cơ Ho", "Chăm", "Sán Dìu", "Hrê", "Ra Glai", "M'Nông", "X’Tiêng", "Bru-Vân Kiều", "Thổ", "Khơ Mú", "Cơ Tu", "Giáy", "Giẻ Triêng", "Tà Ôi", "Mạ", "Co", "Chơ Ro", "Xinh Mun", "Hà Nhì", "Chu Ru", "Lào", "Kháng", "La Chí", "Phú Lá", "La Hủ", "La Ha", "Pà Thẻn", "Chứt", "Lự", "Lô Lô", "Mảng", "Cờ Lao", "Bố Y", "Cống", "Ngái", "Si La", "Pu Péo", "Rơ măm", "Brâu", "Ơ Đu"];
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
		$file = Yii::$app->basePath.'/components/fakedata/NA_THPT.txt';
		$schools = $this->readSchools($file);
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
					'detailAddress = :dAddress AND districtID = :dID AND communeID = :cID', 
					[
						'dAddress' => $hamlet,
						'dID' => $schools[$i]['districtID'],
						'cID' => $schools[$i++]['communeID'],
					])->one()->id;
		    },
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
			$array = preg_split('/( : |, |\r\n|\n|.$)/', $line, -1, PREG_SPLIT_NO_EMPTY);
			$each['school'] = trim($array[0]);
			$districtID = District::find()->where('name = :name', ['name' => trim($array[2])])
					->one()->id;
			$each['districtID'] = $districtID;
			$id = $each['communeID'] = Commune::find()->where('name = :name AND districtID = :dID', [
				'name' => trim($array[1]),
				'dID' => $districtID,
			])->one()->id;
			$schools[] = $each;
		}
		fclose($f);

		return $schools;
	}

	public function fakeStudent() {
		$firstName = $this->createFirstNames();
		$middleName1 = []; $middleName2 = [];
		$lastName1 = []; $lastName2 = [];
		$this->createMiddleNames($middleName1, $middleName2);
		$this->createLastNames($lastName1, $lastName2);
		$ethnicIDs = $this->createEthnicIDs();
		$relIDs = $this->createReligionIDs();
		$jobs = $this->createJobs();

		$seeder = new Seeder();
		$generator = $seeder->getGeneratorConfigurator();
		$faker = $generator->getFakerConfigurator();

		$i = 0; 
		$first = "";
		$mid = "";
		$addressID = 0;
		$dateStart = strtotime('1990-01-01');
		$dateEnd = strtotime('2000-12-31');
		$count['first'] = count($firstName);
		$count['mid1'] = count($middleName1);
		$count['mid2'] = count($middleName2);
		$count['last1'] = count($lastName1);
		$count['last2'] = count($lastName2);
		$count['eth'] = count($ethnicIDs);
		$count['rel'] = count($relIDs);
		$count['job'] = count($jobs);
		$count['address'] = count(Address::find('')->all());
		// echo "<pre>";
		// var_dump($ethnicIDs);
		// echo "</pre>";
		// exit();

		date_default_timezone_set('Asia/Saigon');
		$seeder->table('tbl_student')->columns([
		    'id', //automatic pk
		    'name' => function() use ($firstName, $middleName1, $middleName2, $lastName1, $lastName2, $count, &$first, &$mid, &$i){
		    	$first = $firstName[rand(0, $count['first']-1)];
		    	$mid = $middleName1[rand(0, $count['mid1']-1)];
		    	$i = rand(1,2);
		    	if( $i == 1) {
		    		return $first.' '.$mid.' '.$lastName1[rand(0, $count['last1']-1)];
		    	} else {
		    		return $first.' '.$middleName2[rand(0, $count['mid2']-1)].' '.$lastName2[rand(0, $count['last2']-1)];
		    	}
		    },
		    'gender' => function() use (&$i) {
		    	return $i;
		    },
		    'birthday' => function() use ($dateStart, $dateEnd) {
		    	return $this->randomDate($dateStart, $dateEnd);
		    },
		    'currentAddressID' => function() use ($count, &$addressID) {
		    	return $addressID = Address::find()->where('id = :id', ['id' => rand(1, $count['address'])])->one()->id;
		    },
		    'nativeAddressID' => function() use (&$addressID) {
		    	return $addressID;
		    },
		    'ethnicID' => function() use ($count, $ethnicIDs) {
		    	return $ethnicIDs[rand(0, $count['eth']-1)];
		    },
		    'religionID' => function() use ($relIDs, $count) {
		    	return $relIDs[rand(0, $count['rel']-1)];
		    },
		    'fatherName' => function() use ($lastName1, $count, &$first, &$mid) {
		    	return $first.' '.$mid.' '.$lastName1[rand(0, $count['last1']-1)];
		    },
		    'fatherJob' => function() use ($jobs, $count) {
		    	return $jobs[rand(0, $count['job']-1)];
		    },
		    'motherName' => function() use ($firstName, $middleName2, $lastName2, $count) {
		    	return $firstName[rand(0, $count['first']-1)].' '.$middleName2[rand(0, $count['mid2']-1)].' '.$lastName2[rand(0, $count['last2']-1)];
		    },
		    'motherJob' => function() use ($jobs, $count) {
		    	return $jobs[rand(0, $count['job']-1)];
		    },
		    'created_time' => date('Y-m-d H:i:s'),
		    'updated_time' => date('Y-m-d H:i:s'),
	    ])->rowQuantity(1000); //200000

	    if($seeder->refill())
			return true;
		return false;		
	}

	private function createFirstNames() {
		$firstName = [];
		for($i = 0; $i < 39; $i++){
			$firstName[$i] = "Nguyễn";
		}
		for($i = 39; $i < 51; $i++){
			$firstName[$i] = "Trần";
		}
		for($i = 51; $i < 61; $i++){
			$firstName[$i] = "Lê";
		}
		for($i = 61; $i < 68; $i++){
			$firstName[$i] = "Phạm";
		}
		for($i = 68; $i < 73; $i++){
			$firstName[$i] = "Hoàng";
		}
		for($i = 73; $i < 78; $i++){
			$firstName[$i] = "Phan";
		}
		for($i = 78; $i < 82; $i++){
			$firstName[$i] = "Vũ";
		}
		for($i = 82; $i < 84; $i++){
			$firstName[$i] = "Đặng";
		}
		for($i = 84; $i < 86; $i++){
			$firstName[$i] = "Bùi";
		}
		$firstName[86] = "Đỗ";
		$firstName[87] = "Hồ";
		$firstName[88] = "Ngô";
		$firstName[89] = "Dương";
		$firstName[90] = "Lý";
		$firstName[91] = "Phí";
		$firstName[92] = "Đào";
		$firstName[93] = "Đoàn";
		$firstName[94] = "Vương";
		$firstName[95] = "Trịnh";
		$firstName[96] = "Trương";
		$firstName[97] = "Đinh";
		$firstName[98] = "Lâm";
		$firstName[99] = "Phùng";
		$firstName[100] = "Mai";
		$firstName[101] = "Tô";
		$firstName[102] = "Hà";
		$firstName[103] = "Tạ";
		$firstName[104] = "Khuất";

		return $firstName;
	}

	private function createMiddleNames(&$middleName1, &$middleName2) {
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
			} else if($i % 20 == 4) {
				$middleName1[$i] = 'Phúc';
				$middleName2[$i] = 'Thu';
			}
			else if($i % 20 == 5) {
				$middleName1[$i] = 'Bá';
				$middleName2[$i] = 'Lan';
			}
			else {
				$middleName1[$i] = 'Văn';
				$middleName2[$i] = 'Thị';
			}
		}
	}

	private function createLastNames(&$lastName1, &$lastName2) {
		$manName = ["An", "Anh", "Bách", "Bảo", "Công", "Cường", "Đức", "Dũng", "Dương", "Đạt", "Duy", "Gia", "Hải", "Hiếu", "Hoàng", "Huy", "Hùng", "Khải", "Khang", "Khánh", "Khoa", "Khôi", "Kiên", "Lâm", "Long", "Lộc", "Minh", "Nam", "Nghĩa", "Ngọc", "Nguyên", "Nhân", "Phi", "Phong", "Phúc", "Quân", "Quang", "Quốc", "Tâm", "Thái", "Thành", "Thiên", "Thịnh", "Trung", "Tuấn", "Tùng", "Sơn", "Việt", "Vinh", "Uy"];
		$lastName1 = $manName;
		$girlName = ["An", "Anh", "Bích", "Châu", "Chi", "Diệp", "Điệp", "Đoan", "Dung", "Giang", "Hà", "Hạ", "Hân", "Hạnh", "Hoa", "Hương", "Khánh", "Khuê", "Lan", "Linh", "Loan", "Mai", "Mi", "Minh", "Nga", "Ngân", "Nghi", "Ngọc", "Nhi", "Nhiên", "Như", "Nhung", "Oanh", "Quyên", "Quỳnh", "Tâm", "Thảo", "Thi", "Thu", "Thư", "Thủy", "Trang", "Trà", "Sương", "Uyên", "Vân", "Vy", "Xuân", "Yên", "Yến"];
		$lastName2 = $girlName;
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
			$relIDs[$i] = $mainRelID;
		}
		for ($i=1; $i <= $rel_count; $i++) { 
			$relIDs[$i] = $i;
		}
		return $relIDs;
	}

	private function createJobs() {
	return $jobs = ["Bà vú", "Bác sĩ", "Bảo mẫu", "Bồi bàn", "Cán bộ", "Cảnh sát", "Cầu thủ bóng đá", "Công chứng viên", "Công nhân", "Công tác xã hội", "Công tố viên", "Diễn viên", "Diễn viên võ thuật", "Dược sĩ", "Đầu bếp", "Điều dưỡng viên", "Điêu khắc đá", "Gia sư", "Giảng viên", "Giáo viên", "Huấn luyện viên", "Kiến trúc sư", "Kỹ sư tư vấn giám sát", "Lao công", "Lập trình viên", "Luật sư", "Mại dâm", "Tính toán bảo hiểm", "Bán hàng tạp phẩm", "Dẫn chương trình", "Giúp việc", "Người mẫu", "Người mẫu ảnh", "Người mẫu quảng cáo", "Nhà báo", "Nhà kinh tế học", "Đạo diễn phim", "Nhân viên pha cà phê", "Nhiếp ảnh gia", "Nông dân", "Nữ tu", "Phóng viên truyền hình", "Quản lý xây dựng", "Thám tử tư", "Thiết kế xây dựng", "Thợ hồ", "Thợ may", "Thợ máy", "Thợ mỏ", "Thợ mộc", "Thợ xây", "Thủ thư", "Thư ký", "Tiếp viên hàng không", "Tổng thầu xây dựng", "Trợ giúp viên pháp lý", "Tư vấn xây dựng", "Vận động viên"];
	}

	public function randomDate($dateStart, $dateEnd) {
		$dayStep = 86400;
		$dateBetween = abs(($dateEnd - $dateStart) / $dayStep);
		$randomDay = rand(0, $dateBetween);
		return date("Y-m-d", $dateStart + ($randomDay * $dayStep));
	}

	public function fakeSchoolReport() {
		$seeder = new Seeder();
		$generator = $seeder->getGeneratorConfigurator();
		$faker = $generator->getFakerConfigurator();

		$i = 0;
		date_default_timezone_set('Asia/Saigon');
		$seeder->table('tbl_school_report')->columns([
			'id',
			'number' => function() use (&$i) {
				return ++$i.'/2016';
			},
			'studentID' => function() use (&$i) {
				return $i;
			},
			'date' => date('Y-m-d',strtotime('20160321')),
			'created_time' => date('Y-m-d H:i:s'),
		    'updated_time' => date('Y-m-d H:i:s'),
		])->rowQuantity(count(Student::find('')->all()));

		if($seeder->refill())
			return true;
		return false;
	}

	public function fakeSchoolStudent() {
		$seeder = new Seeder();
		$generator = $seeder->getGeneratorConfigurator();
		$faker = $generator->getFakerConfigurator();

		$i = 0;
		$sch_count = count(School::find('')->all());
		$seeder->table('tbl_relation_school_student')->columns([
			'id',
			'studentID' => function() use (&$i) {
				return ++$i;
			},
			'schoolID' => function() use ($sch_count) {
				return School::find()->where('id = :id', [
					'id' => rand(1, $sch_count)
				])->one()->id;
			},
			'created_time' => date('Y-m-d H:i:s'),
		    'updated_time' => date('Y-m-d H:i:s'),
		])->rowQuantity(count(Student::find('')->all()));

		if($seeder->refill())
			return true;
		return false;
	}

	public function fakeStudyProcess() {
		$seeder = new Seeder();
		$generator = $seeder->getGeneratorConfigurator();
		$faker = $generator->getFakerConfigurator();

		$class = 10;
		$i = 1;
		$year = rand(2006,2013);
		$className = ["A1", "A2", "A3", "A", "B", "C", "D", "E"];
		$name = $className[rand(0, count($className)-1)];
		date_default_timezone_set('Asia/Saigon');
		$seeder->table('tbl_study_process')->columns([
			'id',
			'schoolReportID' => function() use (&$i) {
				return $i;
			},
			'fromTime' => function() use (&$year) {
				return $year;
			},
			'toTime' => function() use (&$year) {
				return ++$year;
			},
			'schoolID' => function() use (&$i) {
				return RelationSchoolStudent::find()->where('studentID = :sID', ['sID' => $i])->one()->id;
			},
			'class' => function() use ($className, &$class, &$name, &$year, &$i) {
				$return = $class++ . $name;
				if($class == 13) {
					$name = $className[rand(0, count($className)-1)];
					$year = rand(2006,2013);
					$i++;
					$class = 10;
				}
				return $return;
			},
			'created_time' => date('Y-m-d H:i:s'),
		    'updated_time' => date('Y-m-d H:i:s'),
		])->rowQuantity(3*count(Student::find('')->all()));

		if($seeder->refill())
			return true;
		return false;
	}

	public function fakeSubject() {
		$seeder = new Seeder();
		$generator = $seeder->getGeneratorConfigurator();
		$faker = $generator->getFakerConfigurator();

		$subjects = ["Ngữ văn", "Toán", "Vật lý", "Hóa học", "Sinh học", "Lịch sử", "Địa lý", "Công nghệ", "Thể dục", "Ngoại ngữ", "Tin học", "Giáo dục công dân", "Giáo dục quốc phòng và an ninh"];
		$i = 0;

		$seeder->table('tbl_subject')->columns([
			'id',
			'name' => function() use ($subjects, &$i) {
				return $subjects[$i++];
			},
			'created_time' => date('Y-m-d H:i:s'),
		    'updated_time' => date('Y-m-d H:i:s'),
		])->rowQuantity(13);

		if($seeder->refill())
			return true;
		return false;
	}


	public function fakeYearEvaluation() {
		$seeder = new Seeder();
		$generator = $seeder->getGeneratorConfigurator();
		$faker = $generator->getFakerConfigurator();

		$names = [];
		$firstName = $this->createFirstNames();
		$middleName1 = [];$middleName2 = [];
		$this->createMiddleNames($middleName1, $middleName2);
		$lastName1 = [];$lastName2 = [];
		$this->createLastNames($lastName1, $lastName2);

		$i = 1;
		$j = 0;
		$departments = ['KHTN', 'KHXH & NV', 'Cơ bản'];
		$stdProcess = StudyProcess::find()->where('schoolReportID = :srID', [
					'srID' => $i,
				])->all();
		$seeder->table('tbl_year_evaluation')->columns([
			'id',
			'schoolReportID' => function() use (&$i) {
				return $i;
			},
			'class' => function() use (&$stdProcess, &$j) {
				return $stdProcess[$j]->class;
			},
			'fromYear' => function() use (&$stdProcess, &$j) {
				return $stdProcess[$j]->fromYear;
			},
			'toYear' => function() use (&$stdProcess, &$j) {
				return $stdProcess[$j]->toYear;
			},
			'studyDepartment' => function() use (&$j, &$d, $departments) {
				if($j == 0) {
					$d = $departments[rand(0,2)];
				}
				return $d;
			},
			'note' => 'Trong bảng này không sửa chữa ở chỗ nào',
			'teacherName' => function() use ($firstName, $middleName1, $middleName2, $lastName1, $lastName2) {
				if(rand(1,2) == 1) {
					return $firstName[rand(0, count($firstName)-1)].' '.$middleName1[rand(0, count($middleName1)-1)].' '.$lastName1[rand(0, count($lastName1)-1)];
				} else {
					return $firstName[rand(0, count($firstName)-1)].' '.$middleName2[rand(0, count($middleName2)-1)].' '.$lastName2[rand(0, count($lastName2)-1)];
				}
			},
			'missedLesson' => function() {
				return rand(0,4);
			},
			'upGradeType' => 'Đưọc lên lớp thẳng',
			'vocationalCertificate' => function() use (&$j) {
				if($j == 1) {
					return 'Tin văn phòng';
				}
			},
			'vocationalCertificateLevel' => function() use (&$j) {
				if($j == 1) {
					$level = ['Giỏi', 'Khá'];
					return $level[array_rand($level)];
				}	
			},
			'teacherComment' => "Học tốt nhưng hơi trầm.\n Nhiệt tình trong công việc của lớp",
			'principalApproval' => 'Đồng ý với đánh giá của giáo viên chủ nhiệm',
			'principalName' => 'Lại Thế Quang',
			'date' => function() use (&$i, &$j, &$stdProcess) {
				$year = $stdProcess[$j++]->toYear;
				if($j == 3) {
					$stdProcess = StudyProcess::find()->where('schoolReportID = :srID', [
						'srID' => ++$i
					])->all();
					$j = 0;
				}
				return date('Y-m-d', strtotime($year.'-05-30'));
			},
			'created_time' => date('Y-m-d H:i:s'),
		    'updated_time' => date('Y-m-d H:i:s'),
		])->rowQuantity(3 * count(SchoolReport::find('')->all()));

		if($seeder->refill())
			return true;
		return false;
	}

	// public function fakeAdvantageSubject() {
	// 	$seeder = new Seeder();
	// 	$generator = $seeder->getGeneratorConfigurator();
	// 	$faker = $generator->getFakerConfigurator();

	// 	$advantageSubjects = [
	// 		'KHTN' => ["Toán", "Vật lý", "Hóa học", "Sinh học"],
	// 		'KHXH & NV' => ["Ngữ văn", "Lịch sử", "Địa lý", "Ngoại ngữ"],
	// 	];
	// 	$i = 1;
	// 	$j = 0;
	// 	$studyDepartment = YearEvaluation::find()->where('id = :id', ['id' => $i])->one()
	// 			->studyDepartment;

	// 	$seeder->table('tbl_advantage_subject')->columns([
	// 		'id',
	// 		'yearEvaluationID' => function() use (&$i) {
	// 			return $i++;
	// 		},
	// 		'subjectID' => function() use (&$i, &$j, &$studyDepartment, $advantageSubjects) {
	// 			if($j == 4) {
	// 				$studyDepartment = YearEvaluation::find()->where('id = :id', ['id' => $i])->one()->studyDepartment;
	// 				$j = 0;
	// 			}
	// 			$j++;
	// 			if(isset($advantageSubjects[$studyDepartment])) {
	// 				return Subject::find()->where('name = :name', [
	// 					'name' => $advantageSubjects[$studyDepartment][$j-1]
	// 				])->one()->id;
	// 			}
	// 		},
	// 		'created_time' => date('Y-m-d H:i:s'),
	// 	    'updated_time' => date('Y-m-d H:i:s'),
	// 	])->rowQuantity(100);

	// 	if($seeder->refill())
	// 		return true;
	// 	return false;
	// }

	public function fakeTermEvaluation() {
		$seeder = new Seeder();
		$generator = $seeder->getGeneratorConfigurator();
		$faker = $generator->getFakerConfigurator();
		$i = 1;
		$j = 1;

		$seeder->table('tbl_term_evaluation')->columns([
			'id',
			'yearEvaluationID' => function() use (&$i) {
				return $i;
			},
			'term' => function() use (&$j, &$i) {
				if($j == 3){
					$i++;
					$j = 0;
				}
				return $j++;
			},
			'learnCapacity' => $faker->randomElement(['Giỏi', 'Khá', 'Trung bình']),
			'conduct' => $faker->randomElement(['Tốt', 'Khá']),
			'created_time' => date('Y-m-d H:i:s'),
		    'updated_time' => date('Y-m-d H:i:s'),
		])->rowQuantity(3 * count(YearEvaluation::find('')->all()));

		if($seeder->refill())
			return true;
		return false;
	}

	public function fakeSubjectScore() {
		$seeder = new Seeder();
		$generator = $seeder->getGeneratorConfigurator();
		$faker = $generator->getFakerConfigurator();

		$names = [];
		$firstName = $this->createFirstNames();
		$middleName1 = [];$middleName2 = [];
		$this->createMiddleNames($middleName1, $middleName2);
		$lastName1 = [];$lastName2 = [];
		$this->createLastNames($lastName1, $lastName2);

		$i = 1;
		$j = 0;
		$subjectIDs = ArrayHelper::getColumn(Subject::find('')->asArray()->all(), 'id');

		$seeder->table('tbl_subject_score')->columns([
			'id',
			'subjectID' => function() use ($subjectIDs, &$j, &$i) {
				if($j == count($subjectIDs)) {
					$i++;
					$j = 0;
				}
				return $subjectIDs[$j++];
			},
			'termEvaluationID' => function() use (&$i) {
				return $i;
			},
			'score' => function() {
				return rand(60,95)/10;
			},
			'teacherName' => function() use ($firstName, $middleName1, $middleName2, $lastName1, 
				$lastName2) {
				if(rand(1,2) == 1) {
					return $firstName[rand(0, count($firstName)-1)].' '.$middleName1[rand(0, count($middleName1)-1)].' '.$lastName1[rand(0, count($lastName1)-1)];
				} else {
					return $firstName[rand(0, count($firstName)-1)].' '.$middleName2[rand(0, count($middleName2)-1)].' '.$lastName2[rand(0, count($lastName2)-1)];
				}
			},
			'created_time' => date('Y-m-d H:i:s'),
		    'updated_time' => date('Y-m-d H:i:s'),
		])->rowQuantity(count(TermEvaluation::find('')->all()));

		if($seeder->refill())
			return true;
		return false;
	}	

	public function fakeRelationStudentObject() {
		$seeder = new Seeder();
		$generator = $seeder->getGeneratorConfigurator();
		$faker = $generator->getFakerConfigurator();
		$i = 0;

		$seeder->table('tbl_relation_student_object')->columns([
			'id',
			'studentID' => function() use (&$i) {
				$i = $i + 50;
				return $i;
			},
			'objectID' => 1,
			'created_time' => date('Y-m-d H:i:s'),
		    'updated_time' => date('Y-m-d H:i:s'),
		])->rowQuantity(count(Student::find('')->all()) / 50 - 1);

		if($seeder->refill())
			return true;
		return false;
	}
}
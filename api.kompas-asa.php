<?php

//
//$SETTINGS = array(
//"end_point"=>"",
//"pass" => "",
//"login"=> "",
//);
//
require_once 'config.php';

class kompasArray implements Iterator {

    //private $position = 0;
    private $container;

    public function __construct() {
        //$this->position = 0;
        $this->container = array();
    }

    function rewind() {
        return reset($this->container);
    }

    function current() {
        return current($this->container);
    }

    function key() {
        return key($this->container);
    }

    function next() {
        return next($this->container);
    }

    function valid() {
        return (key($this->container) !== null);
    }

    protected function add(&$sw) {
        $this->container[] = $sw;
    }

    public function get_count() {
        return count($this->container);
    }

    protected function &get_value($inx) {
        return $this->container[$inx];
    }

    protected function remove_all() {
        array_splice($this->container, 0);
    }

    protected function remove($inx) {
        unset($this->container[$inx]);
    }

}

class typeTesting {

    const Test = 0;
    const Exam = 1;
    const CombinedTest = 2;
    const ControlWork = 3;
    const CourseWork = 4;
    const CourseProject = 5;

    private $ID;

    public function __construct($aID) {
        $this->ID = $aID;
    }

    public function get_id() {
        return $this->ID;
    }

    public function __toString() {
        switch ($this->get_id()) {
            case typeTesting::Test:
                return "Зачёт";
            case typeTesting::Exam:
                return "Экзамен";
            case typeTesting::CombinedTest:
                return "Диф. зачёт";
            case typeTesting::ControlWork:
                return "Контрольная работа";
            case typeTesting::CourseProject:
                return "Курсовой проект";
            case typeTesting::CourseWork:
                return "Курсовая работа";
            default:
                return "UNKNOWN (" . $this->get_id() . ")";
        };
    }

}

class kompasTypesTesting extends kompasArray {

    public function add_type_testing(typeTesting $tt) {
        $this->add($tt);
    }

}

class kompasSemesterWork {

    private $fnumber;
    private $ftypes_testing;
    private $fHours;

    public function get_number() {
        return $this->fnumber;
    }
    
    public function get_hours() {
        return $this->fHours;
    }
    
    /**
     * Возвращает перечень видов аттестаций
     *
     * @author Timur
     * @return kompasTypesTesting
     */
    public function get_types_testing() {
        return $this->ftypes_testing;
    }

    public function __construct($anumber, $ahours) {
        $this->fnumber = $anumber;
        $this->ftypes_testing = new kompasTypesTesting();
        $this->fHours = $ahours;
    }

    public function attestation_count() {
        return $this->get_types_testing()->get_count();
    }

}

class kompasSubject extends kompasArray {

    private $fid;
    private $fname;
    private $fcode;

    public function get_name() {
        return $this->fname;
    }

    public function get_id() {
        return $this->fid;
    }

    public function get_code() {
        return $this->fcode;
    }

    public function __construct($aid, $aname, $acode) {
        parent::__construct();
        $this->fid = $aid;
        $this->fname = $aname;
        $this->fcode = $acode;
    }

    public function add_semester_work(kompasSemesterWork &$sw) {
        $this->add($sw);
    }

    public function work_count_in_semester($sem) {
        $pres = 0;
        foreach ($this as $sw) {
            if ($sw->get_number() == $sem) {
                $pres += $sw->attestation_count();
            }
        }
        return $pres;
    }

    public function attestation_count() {
        $num = 0;
        foreach ($this as $sw) {
            $num += $sw->attestation_count();
        }
        return $num;
    }

    public function semester_present($sem) {
        $pres = false;
        foreach ($this as $sw) {
            if ($sw->get_number() == $sem) {
                $pres = true;
            }
        }
        return $pres;
    }

    public function get_subject_hours() {
        $res = 0;
        if ($this->get_count() > 0) {
            foreach ($this as $sw) {
                $res+=$sw->get_hours();
            }
        }
        return $res;
    }

}

class kompasSubjectGroup extends kompasArray {

    private $fnumber;

    public function get_number() {
        return $this->fnumber;
    }

    public function __construct($anumber) {
        parent::__construct();
        $this->fnumber = $anumber;
    }

    public function add_subject(kompasSubject $s) {
        $this->add($s);
    }

    public function remove_subject($index) {
        $this->remove($index);
    }

    /**
     * Возвращает дисципину группы
     *
     * @author Timur
     * @return kompasSubject
     */
    public function &get_subject($inx) {
        return $this->get_value($inx);
    }

}

class kompasCycle extends kompasArray {

    private $fid;
    private $fname;
    private $fshortname;

    // private $fsubs;

    public function get_id() {
        return $this->fid;
    }

    public function get_name() {
        return $this->fname;
    }

    public function get_short_name() {
        return $this->fshortname;
    }

    public function __construct($aid, $aname, $shortname) {
        parent::__construct();
        $this->fid = $aid;
        $this->fname = $aname;
        $this->fshortname = $shortname;
        //$this->fsubs = //$subs;
    }

    /**
     * Возвращает перечень групп дисциплин
     *
     * @author Timur
     * @return kompasSubjectGroup
     */
    public function &get_subjects_groups() {
        return $this->fsubs;
    }

    public function add_subjects_group(kompasSubjectGroup &$subs) {
        $this->add($subs);
    }

}

class kompasCycles extends kompasArray {

    public function add_subject(kompasCycle &$s) {
        $this->add($s);
    }

    public function add_cycles(kompasCycles &$s) {
        foreach ($s as $cycle) {
            $this->add($cycle);
        }
    }

    /**
     * Возвращает циклов дисциплин
     *
     * @author Timur
     * @return kompasCycle
     */
    public function &get_cycle($inx) {
        return $this->get_value($inx);
    }

}

class kompasCurriculum {

    private $fcycles;
    private $fmetainfo;

    public function __construct($aMetaInfo) {
        $this->fcycles = new kompasCycles();
        $this->fmetainfo = $aMetaInfo;
    }

    /**
     * Возвращает перечень циклов дисциплин РУП
     *
     * @author Timur
     * @return kompasCycles
     */
    public function &get_cycles() {
        return $this->fcycles;
    }

    public function get_meta_info() {
        return $this->fmetainfo;
    }

    /**
     * Возвращает перечень циклов дисциплин РУП
     *
     * @author Timur
     * @return kompasSubject
     */
    public function find_subject($sub_name) {
        $cycles = $this->get_cycles();
        foreach ($cycles as $cycle) {
            foreach ($cycle as $subject_group) {
                foreach ($subject_group as $key => $value) {
                    $sub = $subject_group->get_subject($key);
                    if ($sub->get_name() == sub_name) {
                        return $sub;
                    }
                }
            }
        }
        return null;
    }

    public function find_subject_code($sub_name) {
        $cycles = $this->get_cycles();
        foreach ($cycles as $cycle) {
            foreach ($cycle as $subject_group) {
                foreach ($subject_group as $key => $value) {
                    $sub = $subject_group->get_subject($key);
                    if ($sub->get_name() == sub_name) {
                        return $cycle->get_name() . "." . $subject_group->get_number();
                    }
                }
            }
        }
        return null;
    }

}

class kompasProgramOfStudy {

    private $ContrOrganization; //Организация
    private $EduDepartment; //Подразделение
    private $EduLevel;  //Уровень подготовки
    private $EduForm; //Форма обучения
    private $EduSpecialty; //Специальность / направление подготовки
    private $EduSpecialtyCode;  //Код специальности
    private $EduSpecialization;  //Специализация / профиль
    private $EduQualification; //Квалификация
    private $EduBasicEdu; //Базовое образование
    private $EduProgram; //Программа подготовки
    private $EduDuration; //Срок обучения
    private $Curriculum; //учебный план

    public function __construct($fContrOrganization, $fEduDepartment, $fEduLevel, $fEduForm, $fEduSpecialty, $fEduSpecialtyCode, $fEduSpecialization, $fEduQualification, $fEduBasicEdu, $fEduProgram, $fEduDuration, &$fCurriculum) {
        $this->ContrOrganization = $fContrOrganization;
        $this->EduDepartment = $fEduDepartment;
        $this->EduLevel = $fEduLevel;
        $this->EduForm = $fEduForm;
        $this->EduSpecialty = $fEduSpecialty;
        $this->EduSpecialtyCode = $fEduSpecialtyCode;
        $this->EduSpecialization = $fEduSpecialization;
        $this->EduQualification = $fEduQualification;
        $this->EduBasicEdu = $fEduBasicEdu;
        $this->EduProgram = $fEduProgram;
        $this->EduDuration = $fEduDuration;
        $this->Curriculum = $fCurriculum;
    }

    public function get_organization_name() {
        return $this->ContrOrganization;
    }

    public function get_subdivision_name() {
        return $this->EduDepartment;
    }

    public function get_education_level() {
        return $this->EduLevel;
    }

    public function get_form_education() {
        return $this->EduForm;
    }

    public function get_direction() {
        return $this->EduSpecialty;
    }

    public function get_direction_code() {
        return $this->EduSpecialtyCode;
    }

    public function get_specialization() {
        return $this->EduSpecialization;
    }

    public function get_qualification() {
        return $this->EduQualification;
    }

    public function get_basic_education() {
        return $this->EduBasicEdu;
    }

    public function get_program_name() {
        return $this->EduProgram;
    }

    public function get_duration_education() {
        return $this->EduProgram;
    }

    /**
     * Возвращает учебный план направления подготовки
     *
     * @author Timur
     * @return kompasCurriculum
     */
    public function &get_curriculum() {
        return $this->Curriculum;
    }

}

class kompasStudent {

    private $EduBasicLang; //Основной изучаемый язык
    private $EduGroup; //Группа
    private $EduSemester; //Семестр
    private $EduStatus; //Статус студента
    private $EduCurSemStartDate; //Дата начала обучения по текущему семестру
    private $ContrNumber; //Номер договора / номер зачётной книжки
    private $ContrDate; //Дата заключения договора
    private $Program; //kompasProgramOfStudy
    private $IndividualSubjects; //kompasIndividualSubjects

    public function __construct($fEduBasicLang, $fEduGroup, $fEduSemester, $fEduStatus, $fEduCurSemStartDate, $fContrNumber, $fContrDate, &$fProgram, kompasIndividualSubjects &$fIndividualSubjects) {
        $this->EduBasicLang = $fEduBasicLang;
        $this->EduGroup = $fEduGroup;
        $this->EduSemester = $fEduSemester;
        $this->EduStatus = $fEduStatus;
        $this->EduCurSemStartDate = $fEduCurSemStartDate;
        $this->ContrNumber = $fContrNumber;
        $this->ContrDate = $fContrDate;
        $this->Program = $fProgram;
        $this->IndividualSubjects = $fIndividualSubjects;
        $fIndividualSubjects->set_student($this);
        if ($fIndividualSubjects->is_appro()) {
            $this->apply_individual_subjects();
        }
    }

    public function get_basic_lang() {
        return $this->EduBasicLang;
    }

    public function get_education_group() {
        return $this->EduGroup;
    }

    public function get_current_semester() {
        return $this->EduSemester;
    }

    public function get_status() {
        return $this->EduStatus;
    }

    public function get_current_semester_start_date() {
        return $this->EduCurSemStartDate;
    }

    public function get_agreement_number() {
        return $this->ContrNumber;
    }

    public function get_agreement_date() {
        $dt = explode(" ",$this->ContrDate);
        return $dt[0];
    }

    /**
     * Возвращает информацию о направлении подготовки студента
     *
     * @author Timur
     * @return kompasProgramOfStudy
     */
    public function &get_curent_program() {
        return $this->Program;
    }

    /**
     * Возвращает информацию об индивидуальном плане студента
     *
     * @author Timur
     * @return kompasIndividualSubjects
     */
    public function &get_individual_subjects() {
        return $this->IndividualSubjects;
    }

    public function apply_individual_subjects() {
        $curricula = $this->get_curent_program()->get_curriculum();
        $cycles = $curricula->get_cycles();
        foreach ($cycles as $cycle) {
            foreach ($cycle as $subject_group) {
                if ($subject_group->get_number() <> "0") {
                    foreach ($subject_group as $key => $value) {
                        if (!$this->get_individual_subjects()->is_subject_present($subject_group->get_subject($key)->get_name())) {
                            $subject_group->remove_subject($key);
                        }
                    }
                }
            }
        }
    }

    public function has_individual_plan() {
        return $this->get_individual_subjects()->is_appro();
    }

    public function has_sended_request_individual_plan() {
        return $this->get_individual_subjects()->is_sended();
    }

    //private $PersonalData; //ссылка на kompasPersonalData
}

class kompasStudents extends kompasArray {

    public function add_student(kompasStudent &$s) {
        $this->add($s);
    }

    /**
     * Возвращает запись о студенте
     *
     * @author Timur
     * @return kompasStudent
     */
    public function &get_student($index) {
        return $this->get_value($index);
    }

}

class kompasPersonalData {

    private $PersonFirstName; //Имя
    private $PersonLastName;   //Фамилия
    private $PersonPatronymic;  //Отчество
    private $PersonCode;  //КодФизЛица
    private $PersonEmail;  //Email
    private $PersonGender;  //Пол
    private $PersonBirthDay; //Дата рождения
    private $Students; //тип kompasStudents. На случай если будут передаваться все договора физ лица

    public function __construct($fPersonFirstName, $fPersonLastName, $fPersonPatronymic, $fPersonCode, $fPersonEmail, $fPersonGender, $fPersonBirthDay) {
        $this->PersonFirstName = $fPersonFirstName;
        $this->PersonLastName = $fPersonLastName;
        $this->PersonPatronymic = $fPersonPatronymic;
        $this->PersonCode = $fPersonCode;
        $this->PersonEmail = $fPersonEmail;
        $this->PersonGender = $fPersonGender;
        $this->PersonBirthDay = $fPersonBirthDay;
        $this->Students = new kompasStudents();
    }

    public function get_first_name() {
        return $this->PersonFirstName;
    }

    public function get_last_name() {
        return $this->PersonLastName;
    }

    public function get_patronymic() {
        return $this->PersonPatronymic;
    }

    public function get_id() {
        return $this->PersonCode;
    }

    public function get_email() {
        return $this->PersonEmail;
    }

    public function get_gender() {
        return $this->PersonGender;
    }

    public function get_birthday() {
		$dt = explode(" ",$this->PersonBirthDay);
        return $dt[0];
    }

    public function get_full_name() {
        return $this->get_last_name() . " " . $this->get_first_name() . " " . $this->get_patronymic();
    }

    /**
     * Возвращает запись о студенте
     *
     * @author Timur
     * @return kompasStudent
     */
    public function &student() {
        return $this->Students->get_student(0);
    }

    public function add_student(kompasStudent &$s) {
        $this->Students->add_student($s);
    }

}

class kompasIndividualSubjects extends kompasArray {

    private $Sended; //была ли отправлена заявка
    private $WhenAppro; //дата утверждения либо пустая строка
    private $Student; //студент

    public function __construct($fSended, $fWhenAppro) {
        parent::__construct();
        $this->Sended = $fSended;
        if ($fWhenAppro <> "") {
            
        }
        $this->WhenAppro = $fWhenAppro;
        $this->Student = null;
    }

    public function is_sended() {
        return $this->Sended;
    }

    public function when() {
        return $this->WhenAppro;
    }

    public function is_appro() {
        return $this->WhenAppro <> "";
    }

    public function add_subject($sub_name) {
        $this->add($sub_name);
    }

    public function reset_subject_list() {
        $this->remove_all();
        $this->Sended = false;
        $this->WhenAppro = "";
    }

    public function set_student(kompasStudent &$fStudent) {
        $this->Student = $fStudent;
    }

    public function apply() {
        kompasFactory::send_subject_on_choice_list($this->Student, $this);
    }

    public function is_subject_present($sn) {
        foreach ($this as $sub_name) {
            if ($sub_name == $sn) {
                echo $sub_name . " = " . $sn . "<br/>";
                return true;
            }
        }
        return false;
    }

}

class kompasFactory {

    private static $client;

    public static function singleton() {
        if (!isset(self::$client)) {
            //ini_set('soap.wsdl_cache_enabled', '0');
            ini_set('soap.wsdl_cache_ttl', '10');
            global $SETTINGS;
            self::$client = new SoapClient($SETTINGS["end_point"], array('login' => $SETTINGS["login"], 'password' => $SETTINGS["pass"]));
        }
        return self::$client;
    }

    private static function &parse_semester_work($response) {
        $att = "";
        $res = new kompasSemesterWork($response->Semester, $response->Hours);
        if (isset($response->Attestation)) {
            $att = $response->Attestation;
            switch ($att) {
                case "Зачёт":
                    $res->get_types_testing()->add_type_testing(new typeTesting(typeTesting::Test));
                    break;
                case "Диф. зачёт":
                    $res->get_types_testing()->add_type_testing(new typeTesting(typeTesting::CombinedTest));
                    break;
                case "Экзамен":
                    $res->get_types_testing()->add_type_testing(new typeTesting(typeTesting::Exam));
                    break;
            }
        }
        if (isset($response->Controlwork)) {
            $res->get_types_testing()->add_type_testing(new typeTesting(typeTesting::ControlWork));
        }
        if (isset($response->Coursework)) {
            $res->get_types_testing()->add_type_testing(new typeTesting(typeTesting::CourseWork));
        }
        if (isset($response->Courseproject)) {
            $res->get_types_testing()->add_type_testing(new typeTesting(typeTesting::CourseProject));
        }
        return $res;
    }

    private static function &parse_subject($response) {
        $res = new kompasSubject("", $response->Name, $response->Code);
        $sw = $response->SemesterWork;
        if (is_array($sw)) {
            foreach ($sw as $value) {
                $sw_val = self::parse_semester_work($value);
                $res->add_semester_work($sw_val);
            }
        } else {
            $sw_val = self::parse_semester_work($sw);
            $res->add_semester_work($sw_val);
        }
        //print $res->get_name();
        return $res;
    }

    private static function &parse_subject_group($response) {
        $res = new kompasSubjectGroup($response->Code);
        if (is_array($response->Subject)) {
            foreach ($response->Subject as $value) {
                $res->add_subject(self::parse_subject($value));
            }
        } else {
            $res->add_subject(self::parse_subject($response->Subject));
        }
        return $res;
    }

    private static function &parse_cycle($response) {
        $c_id = $response->IsOptional;
        $c_name = $response->Name;
        $c_shortname = $response->Abbreviation;
        $res = new kompasCycle($c_id, $c_name, $c_shortname);
        if (is_array($response->SubjectGroup)) {
            foreach ($response->SubjectGroup as $value) {
                $c_sg = self::parse_subject_group($value);
                $res->add_subjects_group($c_sg);
            }
        } else {
            $c_sg = self::parse_subject_group($response->SubjectGroup);
            $res->add_subjects_group($c_sg);
        }
        return $res;
    }

    private static function &parse_cycles($response) {
        $res = new kompasCycles();
        if (is_array($response->Cycle)) {
            foreach ($response->Cycle as $value) {
                $res->add_subject(self::parse_cycle($value));
            }
        } else {
            $res->add_subject(self::parse_cycle($response->Cycle));
        }
        return $res;
    }

    protected static function &parse_meta_info($buff) {

        $res = new asaMetaInfo($buff->OrganizationName, $buff->SubdivisionName, $buff->DirectionName, $buff->SpecializationName, $buff->DurationEducation, $buff->QualificationEducation, $buff->FormEducation, $buff->BaseEducationRate, $buff->Member);
        return $res;
    }

    public static function &get_user_curriculum($un) {
        $res = self::singleton()->GetFullStudentInfo(array('KontrNumber' => $un));
        //var_dump($res);
        $result = new kompasCurriculum("");
        $result->get_cycles()->add_cycles(self::parse_cycles(
                        $res->return->Curriculum));
        return $result;
    }

    public static function &get_student($student_id) {
        $res = self::singleton()->GetFullStudentInfo(array('KontrNumber' => $student_id));
        //var_dump($res);
        $result = new kompasPersonalData(
                $res->return->Student->PersonFirstName, $res->return->Student->PersonLastName, $res->return->Student->PersonPatronymic, $res->return->Student->PersonCode, $res->return->Student->PersonEmail, $res->return->Student->PersonGender, $res->return->Student->PersonBirthDay
        );

        $curr = new kompasCurriculum("");
        $curr->get_cycles()->add_cycles(self::parse_cycles(
                        $res->return->Curriculum));
        $program = new kompasProgramOfStudy(
                $res->return->Student->ContrOrganization, $res->return->Student->EduDepartment, $res->return->Student->EduLevel, $res->return->Student->EduForm, $res->return->Student->EduSpecialty, $res->return->Student->EduSpecialtyCode, $res->return->Student->EduSpecialization, $res->return->Student->EduQualification, $res->return->Student->EduBasicEdu, $res->return->Student->EduProgram, $res->return->Student->EduDuration, $curr
        );
        $ind = self::parse_subject_on_choice($res->return->SubjecsOnChoice);

        $stud = new kompasStudent(
                $res->return->Student->EduBasicLang, $res->return->Student->EduGroup, $res->return->Student->EduSemester, $res->return->Student->EduStatus, $res->return->Student->EduCurSemStartDate, $res->return->Student->ContrNumber, $res->return->Student->ContrDate, $program, $ind
        );
        $result->add_student($stud);
        return $result;
    }

    protected static function get_ArrayOfStrings(&$list) {
        $res = Array();
        foreach ($list as $value) {
            $res [] = $value;
        }
        return array('String' => $res);
    }

    private static function &parse_subject_on_choice($response) {
        $result = new kompasIndividualSubjects($response->Sended, $response->WhenAppro);
        if (is_array($response->Subject)) {
            foreach ($response->Subject as $value) {
                $result->add_subject($value);
            }
        } else {
            $result->add_subject($value);
        }
        return $result;
    }

    public static function &send_subject_on_choice_list(kompasStudent &$student, &$list) {
        $arrayOfStrings = self::get_ArrayOfStrings($list);
        $param = array('ContractNumber' => $student->get_agreement_number(),
            'Subjects' => $arrayOfStrings);
        try {
            $res = self::singleton()->SendSubjectOnChoiceList(
                    $param);
        } catch (Exception $e) {
            // echo "REQUEST HEADERS:\n<pre>" . self::singleton()->__getLastRequest() . "</pre>\n";
        }

        $result = 0; //self::parse_subject_on_choice($res->return, $student);
        return $result;
    }

}

?>

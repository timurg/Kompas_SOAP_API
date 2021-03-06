<?php

//$SETTINGS = array(
//   "KOMPAS_WS_URL" => "*******",
//    "KOMPAS_WS_LOGIN" => "******",
//    "KOMPAS_WS_PASS" => "******",
//);
require_once 'settings.php';
require_once 'common.php';

/**
 * Коды ошибок Компас-В: 210 - 290
 */
class kompasException extends veguException {
    
}

class kompasTypesTesting extends kompasArray {

    public function add_type_testing(typeTesting $tt) {
        $this->add($tt);
    }

}

class kompasPaymentInfo {

    private $fBankName;
    private $fBankCode;
    private $fValue;
    private $fHalfYearName;
    private $fPaymentTypeName;
    private $fPaymentDate;
    private $fOperationType;

    public function __construct($aBankName, $aBankCode, $aValue, $aHalfYearName, $aPaymentTypeName, $aPaymentDate, $aOperationType) {
        $this->fBankName = $aBankName;
        $this->fBankCode = $aBankCode;
        $this->fValue = $aValue;
        $this->fHalfYearName = $aHalfYearName;
        $this->fPaymentTypeName = $aPaymentTypeName;
        $this->fPaymentDate = $aPaymentDate;
        $this->fOperationType = $aOperationType;
    }

    public function get_bank_name() {
        return $this->fBankName;
    }

    public function get_bank_code() {
        return $this->fBankCode;
    }

    public function get_value() {
        return $this->fValue;
    }

    public function get_half_year_name() {
        return $this->fHalfYearName;
    }

    public function get_type_name() {
        return $this->fPaymentTypeName;
    }

    public function get_date() {
        return $this->fPaymentDate;
    }

    public function get_operation_type() {
        return $this->fOperationType;
    }

}

class kompasPayments extends kompasArray {

    public function add_payment(kompasPaymentInfo $tt) {
        $this->add($tt);
    }

    /**
     * Возвращает информацию по оплате по индексу.
     *
     * @author Timur
     * @return kompasPaymentInfo
     */
    public function &get_payment($inx) {
        return $this->get_value($inx);
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
                    if ($sub->get_name() == $sub_name) {
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
                    if ($sub->get_name() == $sub_name) {
                        return $cycle->get_short_name() . "." . $subject_group->get_number();
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
    private $SupervisingDepartmentCode; //Код профильной кафедры
    private $SupervisingDepartmentName; //Название профильной кафедры
    private $EduDirectionAbbreviation; //Аббревиатура специальности (направления)
    private $EduSpecialtyAbbreviation; //Аббревиатура специализации (профиля)

    public function __construct($fContrOrganization, $fEduDepartment, $fEduLevel, $fEduForm, $fEduSpecialty, $fEduSpecialtyCode, $fEduSpecialization, $fEduQualification, $fEduBasicEdu, $fEduProgram, $fEduDuration, $aSupervisingDepartmentCode, $aSupervisingDepartmentName, $fEduDirectionAbbreviation, $fEduSpecialtyAbbreviation, &$fCurriculum) {
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
        $this->SupervisingDepartmentCode = $aSupervisingDepartmentCode;
        $this->SupervisingDepartmentName = $aSupervisingDepartmentName;
        $this->EduDirectionAbbreviation = $fEduDirectionAbbreviation;
        $this->EduSpecialtyAbbreviation = $fEduSpecialtyAbbreviation;
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

    public function get_supervising_department_code() {
        return $this->SupervisingDepartmentCode;
    }

    public function get_supervising_department_name() {
        return $this->SupervisingDepartmentName;
    }

    public function get_direction_abbreviation() {
        return $this->EduDirectionAbbreviation;
    }

    public function get_specialty_abbreviation() {
        return $this->EduSpecialtyAbbreviation;
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

class kompasCreateEntrantProfileResult{
    private $success; //Булево поле, информация об успшности операции
    private $personID; //Идентификатор физ. лица.
    private $message; //Сообщение от КИС Компас-В
    private $isNewPerson; //Создано ли новое физ лицо?
    private $profileID; //Идентификатор анкеты (будущий номер договра)
    private $user_name; //логин абитуриента
    private $pass_word; //пароль абитуриента
    
    public function __construct($fsuccess, $fpersonID, $fmessage, $fisNewPerson, 
            $fprofileID, $user_name, $pass_word){
        $this->success = $fsuccess;
        $this->personID = $fpersonID;
        $this->message = $fmessage;
        $this->isNewPerson = $fisNewPerson;
        $this->profileID = $fprofileID;
        $this->user_name = $user_name;
        $this->pass_word = $pass_word;
    }
    /**
     * Возвращает информацию об успешности операции по созданию анкеты абитуриента.
     *
     * @author Timur
     * @return bool
     */
    public function is_success(){
        return $this->success;
    }
    
    /**
     * Возвращает идентификатор созданной анкеты. При заключении договора, идентификатор анкеты становится номером договора.
     *
     * @author Timur
     * @return string
     */
    public function get_profile_id(){
        return $this->profileID;
    }
    /**
     * Возвращает сообщение по последней операции по созданию анкеты абитурианта в КИС Компас-В.
     *
     * @author Timur
     * @return string
     */
    public function get_message(){
        return $this->message;
    }
    /**
     * Возвращает флаг - было ли создано физ. лицо при выполнении операции.
     *
     * @author Timur
     * @return bool
     */
    public function is_new_person(){
        return $this->isNewPerson;
    }
    /**
     * Возвращает идентификатор физ. лица.
     *
     * @author Timur
     * @return string
     */
    public function get_person_id(){
        return $this->personID;
    }
    
    /**
     * Возвращает логин абитуриента
     *
     * @author Timur
     * @return string
     */
    public function get_user_name(){
        return $this->user_name;
    }
    
    /**
     * Возвращает пароль абитуринта
     *
     * @author Timur
     * @return string
     */
    public function get_pass_word(){
        return $this->pass_word;
    }
}

class kompasStudent {

    private $EduBasicLang; //Основной изучаемый язык
    private $EduGroup; //Группа
    private $EduSemester; //Семестр
    private $EduStatus; //Статус студента
    private $EduCurSemStartDate; //Дата начала обучения по текущему семестру
	private $EduDateOfCommencement; //Дата начала первого семестра
    private $ContrNumber; //Номер договора / номер зачётной книжки
    private $ContrDate; //Дата заключения договора
    private $Program; //kompasProgramOfStudy
    private $IndividualSubjects; //kompasIndividualSubjects
    private $Payments; //kompasPayments

    public function __construct($fEduBasicLang, $fEduGroup, $fEduSemester, 
		$fEduStatus, $fEduCurSemStartDate, $fContrNumber, $fContrDate,
		&$fProgram, $fEduDateOfCommencement, kompasIndividualSubjects &$fIndividualSubjects) {
        $this->EduBasicLang = $fEduBasicLang;
        $this->EduGroup = $fEduGroup;
        $this->EduSemester = $fEduSemester;
        $this->EduStatus = $fEduStatus;
        $this->EduCurSemStartDate = $fEduCurSemStartDate;
        $this->ContrNumber = $fContrNumber;
        $this->ContrDate = $fContrDate;
        $this->Program = $fProgram;
		$this->EduDateOfCommencement = $fEduDateOfCommencement;
        $this->IndividualSubjects = $fIndividualSubjects;
		
        $fIndividualSubjects->set_student($this);
        if ($fIndividualSubjects->is_appro()) {
            $this->apply_individual_subjects();
        }
        $this->Payments = NULL;
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
        $dt = explode(" ", $this->ContrDate);
        return $dt[0];
    }
	
	/**
     * Возвращает дату начала первого семестра обучения студента.
     *
     * @author Timur
     * @return DateTime
     */
	public function get_date_of_commencement() {
        return $this->EduDateOfCommencement;
    }

    /**
     * Возвращает информацию о движении средств связанных с договором студента. Ленивая загрузка.
     *
     * @author Timur
     * @return kompasPayments
     */
    public function get_payments() {
        if (is_null($this->Payments)) {
            $this->Payments = kompasFactory::get_student_payments($this->get_agreement_number());
        }
        return $this->Payments;
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

    /**
     * Возвращает список дисциплин для выбора
     *
     * @author Dmitriy Ilyuschenkov
     * @return array of kompasSubjectGroup
     */
    public function get_individual_subjects_list_for_choice() {
        $arRes = array();
        $curricula = $this->get_curent_program()->get_curriculum();
        $cycles = $curricula->get_cycles();
        foreach ($cycles as $cycle) {
            foreach ($cycle as $subject_group) {
                if ($subject_group->get_number() <> "0") {
                    $gname = $subject_group->get_number();
                    $arrSubj = array();
                    foreach ($subject_group as $subj) {
                        $arrSubj[] = $subj->get_name();
                    }
                    $arRes[] = array(
                        "gname" => $gname,
                        "subjects" => $arrSubj
                    );
                }
            }
        }
        return $arRes;
    }

    public function get_selected_individual_subject_list() {
        $arRes = array();
        $arrSubjectStr = $this->get_individual_subjects();
        foreach ($arrSubjectStr as $s) {
            $code = $this->Program->get_curriculum()->find_subject_code($s);
            $arRes[] = array("code" => $code, "name" => $s);
        }
        return $arRes;
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
        $dt = explode(" ", $this->PersonBirthDay);
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
	private $OrderNumber; //Номер приказа об утверждении
	private $OrderDate; //Дата приказа об утверждении
	private $ResearchWorkTheme; //Тема ВКР
	private $ScientificAdviser; //Руководитель
    private $Curator; //Куратор студента
    private $AssistantOfScientificAdviser; //Консультант студента
    private $IsApproved; //Флаг - проверен ли ИУП.

    public function __construct($fSended, $fWhenAppro, $fOrderNumber, $fOrderDate, $fResearchWorkTheme, $fScientificAdviser, $fCurator,
                $fAssistantOfScientificAdviser, $fIsApproved) {
        parent::__construct();
        $this->Sended = $fSended;
        if ($fWhenAppro <> "") {
            
        }
        $this->WhenAppro = $fWhenAppro;
        $this->Student = null;
		
		$this->OrderNumber = $fOrderNumber;
		$this->OrderDate = $fOrderDate;
		$this->ResearchWorkTheme = $fResearchWorkTheme;
		$this->ScientificAdviser = $fScientificAdviser;
        $this->Curator = $fCurator;
        $this->AssistantOfScientificAdviser = $fAssistantOfScientificAdviser;
        $this->IsApproved = $fIsApproved;        
    }

    public function is_sended() {
        return $this->Sended;
    }

    public function when() {
        return $this->WhenAppro;
    }
	
	public function get_order_number() {
        return $this->OrderNumber;
    }
	
	public function get_order_date() {
        return $this->OrderDate;
    }
	
	public function get_research_work_theme() {
        return $this->ResearchWorkTheme;
    }
	
	public function get_scientific_adviser() {
        return $this->ScientificAdviser;
    }

    public function is_appro() {
        return $this->WhenAppro <> "";
    }
    
    public function get_curator() {
        return $this->Curator;
    }
    
    public function get_assistant_scientific_adviser() {
        return $this->AssistantOfScientificAdviser;
    }
    
    public function is_approved() {
        return $this->IsApproved;
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
            global $SETTINGS;
            self::$client = new SoapClient($SETTINGS["KOMPAS_WS_URL"], array('login' => $SETTINGS["KOMPAS_WS_LOGIN"], 'password' => $SETTINGS["KOMPAS_WS_PASS"], 'exceptions' => 0));
        }
        return self::$client;
    }

    private static function check_result($result, $err_code = 0, $internal_message = "Ошибка при отправке запроса КИС.", $agreement_number = NULL) {
        if (is_soap_fault($result)) {
            throw new kompasException($internal_message, $err_code, $result, $agreement_number);
        }
        return true;
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
        if (isset($response->ControlWork)) {
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
        self::check_result($res, 211, "Ошибка при получении учебного плана студента из КИС.", $un);
        $result = new kompasCurriculum("");
        $result->get_cycles()->add_cycles(self::parse_cycles(
                        $res->return->Curriculum));
        return $result;
    }

    public static function &get_student($student_id) {
        $res = self::singleton()->GetFullStudentInfo(array('KontrNumber' => $student_id));
        self::check_result($res, 210, "Ошибка при получении сведений о студенте из КИС.", $student_id);
        $result = new kompasPersonalData(
                $res->return->Student->PersonFirstName, $res->return->Student->PersonLastName, $res->return->Student->PersonPatronymic, $res->return->Student->PersonCode, $res->return->Student->PersonEmail, $res->return->Student->PersonGender, $res->return->Student->PersonBirthDay
        );

        $SupervisingDepartmentCode = "";
        $SupervisingDepartmentName = "";

        $curr = new kompasCurriculum("");
        if (isset($res->return->Curriculum)) {
            $curr->get_cycles()->add_cycles(self::parse_cycles(
                            $res->return->Curriculum));
            if (isset($res->return->Curriculum->SupervisingDepartmentCode)) {
                $SupervisingDepartmentCode = $res->return->Curriculum->SupervisingDepartmentCode;
            }
            if (isset($res->return->Curriculum->SupervisingDepartmentName)) {
                $SupervisingDepartmentName = $res->return->Curriculum->SupervisingDepartmentName;
            }
        }
        $program = new kompasProgramOfStudy(
                $res->return->Student->ContrOrganization, $res->return->Student->EduDepartment, $res->return->Student->EduLevel, $res->return->Student->EduForm, $res->return->Student->EduSpecialty, $res->return->Student->EduSpecialtyCode, $res->return->Student->EduSpecialization, $res->return->Student->EduQualification, $res->return->Student->EduBasicEdu, $res->return->Student->EduProgram, $res->return->Student->EduDuration, $SupervisingDepartmentCode, $SupervisingDepartmentName, $res->return->Student->EduDirectionAbbreviation, $res->return->Student->EduSpecialtyAbbreviation, $curr
        );
        $ind = self::parse_subject_on_choice($res->return->SubjecsOnChoice);
		
		$dateOfCommencement = isset($res->return->Student->EduDateOfCommencement) ? new DateTime($res->return->Student->EduDateOfCommencement) :  new DateTime();
        $stud = new kompasStudent(
                $res->return->Student->EduBasicLang, $res->return->Student->EduGroup, $res->return->Student->EduSemester, $res->return->Student->EduStatus, $res->return->Student->EduCurSemStartDate, $res->return->Student->ContrNumber, $res->return->Student->ContrDate, $program, $dateOfCommencement, $ind
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
        $result = new kompasIndividualSubjects($response->Sended, $response->WhenAppro, $response->OrderNumber, new DateTime($response->OrderDate) ,
                $response->ResearchWorkTheme, $response->ScientificAdviser, $response->Curator, $response->AssistantOfScientificAdviser,
                $response->IsApproved);
        if (is_array($response->Subject)) {
            foreach ($response->Subject as $value) {
                $result->add_subject($value);
            }
        } else {
            $result->add_subject($response->Subject);
        }
        return $result;
    }

    private static function &parse_payment_info($response) {
        $result = new kompasPaymentInfo($response->BankName, $response->BankCode, $response->Value, $response->HalfYearName, $response->PaymentTypeName, $response->PaymentDate, $response->OperationType);
        return $result;
    }

    private static function &parse_payments($response) {
        $result = new kompasPayments();
        if (is_array($response->Payments)) {
            foreach ($response->Payments as $value) {
                $result->add_payment(self::parse_payment_info($value));
            }
        } else {
            $result->add_payment(self::parse_payment_info($response->Payments));
        }
        return $result;
    }

    public static function &send_subject_on_choice_list(kompasStudent &$student, &$list) {
        $arrayOfStrings = self::get_ArrayOfStrings($list);
        $param = array('ContractNumber' => $student->get_agreement_number(),
            'Subjects' => $arrayOfStrings);
        $res = self::singleton()->SendSubjectOnChoiceList(
                $param);
        return self::check_result($res, 220, "Ошибка при отправке перечня выбранных дисциплин в КИС.", $student->get_agreement_number());
    }

    public static function get_student_payments($student_id) {
        $res = self::singleton()->getPayments(array('AgreementNumber' => $student_id));
        self::check_result($res, 240, "Ошибка при чтении информации по выплатам студента из КИС.", $student->get_agreement_number());
        return self::parse_payments($res->return);
    }

    public static function read_dictionary($dictionary_name, $owner = null) {
        $res = self::singleton()->readDictionary(array('DictionaryName' => $dictionary_name,
            'language' => "ru-RU"));
        self::check_result($res, 230, "Ошибка при чтении справочника из КИС.");

        $result = array();
        if (is_array($res->return->DictionaryValues)) {
            foreach ($res->return->DictionaryValues as $value) {
                if (($owner == null) || ($value->owner == $owner)) {
                    $result[$value->key] = $value->value;
                }
            }
        } else {
            $result[$res->return->DictionaryValues->key] = $res->return->DictionaryValues->value;
        }
        return $result;
    }
    /**
     * Парсинг результата вызова функции по создание анкеты абитуриента.
     *
     * @author Timur
     * @return kompasCreateEntrantProfileResult
     */
    private static function &parse_create_entrant_profile_result($response) {
        return new kompasCreateEntrantProfileResult(
                        $response->success,
                        $response->personID,
                        $response->message,
                        $response->isNewPerson,
                        $response->profileID,
                        $response->userName,
                        $response->password
                );
    }

    /**
     * Создание анкеты абитуриента.
     *
     * @author Timur
     * @param string $Name Имя
     * @param string $LastName Фамилия
     * @param string $Patronymic Отчество
     * @param DateTime $Birthday Дата рождения
     * @param string $Email e-mail
     * @param string $PhoneNumber Телефонный номер
     * @param string $City Город проживания
     * @param string $Country Страна проживания
     * @param string $idCode Код паспорта
     * @param string $idNumber Номер паспорта
     * @param string $idSupervisor Название организации выдавшей паспорт
     * @param DateTime $idDate Дата получения паспорта
     * @param string $BaseEducationRate Уровень образования
     * @param string $EducationOrganizationName Название учебного заведения
	 * @param string $EducationDocOrganizationPlace Место размещения образовательного учереждения
	 * @param string $EducationDocType Тип документа об образовании, возможные значения: диплом, аттестат.
	 * @param string $EducationDocNumber Номер документа об образование
	 * @param DateTime $EducationDocDate Дата выдачи документа
	 * @param string $EducationDocSeria Серия документа об образовании
	 * @param string $EducationDocRegNumber Регистрационный номер документа об образовании
     * @param integer $EducationDocYearOfEnrolling Год начала обучения
     * @param integer $EducationDocYearOfGraduating Год окончания обучения
     * @param string $SelectedEducationLevel Выбранный уровень образованния
     * @param string $SelectedDirection Направление (специальность)
     * @return kompasCreateEntrantProfileResult
     */
    public static function create_entrant_profile($Name, $LastName, $Patronymic,
            $Birthday, $Email, $Sex, $PhoneNumber, $City, $Country, 
            $idCode, $idNumber, $idSupervisor, $idDate,
            $BaseEducationRate, $EducationOrganizationName, $EducationDocOrganizationPlace,
			$EducationDocType, $EducationDocNumber, $EducationDocDate,
			$EducationDocSeria, $EducationDocRegNumber,
            $YearOfEnrolling, $YearOfGraduating, $SelectedEducationLevel,
            $SelectedDirection) {
        $res = self::singleton()->createEntrantProfile(array(
            'Name' => $Name,
            'LastName' => $LastName,
            'Patronymic' => $Patronymic,
            'Birthday' => $Birthday->format('Y-m-d'),
            'Email' => $Email,
            'PhoneNumber' => $PhoneNumber,
            'City' => $City,
            'Country' => $Country,
            'idCode' => $idCode,
            'idNumber' => $idNumber,
            'idSupervisor' => $idSupervisor,
            'idDate' => $idDate->format('Y-m-d'),
            'BaseEducationRate' => $BaseEducationRate,
            'EducationDocOrganizationName' => $EducationOrganizationName,
            'EducationDocYearOfEnrolling' => $YearOfEnrolling,
            'EducationDocYearOfGraduating' => $YearOfGraduating,
            'SelectedEducationLevel' => $SelectedEducationLevel,
            'EducationDocOrganizationPlace' => $EducationDocOrganizationPlace,
			'EducationDocType' => $EducationDocType,
			'EducationDocNumber' => $EducationDocNumber,
			'EducationDocDate' => $EducationDocDate->format('Y-m-d'),
			'EducationDocSeria' => $EducationDocSeria,
			'EducationDocRegNumber' => $EducationDocRegNumber,
			'SelectedDirection' => $SelectedDirection
            ));
        self::check_result($res, 200, "Ошибка при создании анкеты.");
        return self::parse_create_entrant_profile_result($res->return);
    }
    
    public static function change_student_password($profile_id, $new_pass) {
        $res = self::singleton()->changePasswordForStudent(array(
            'ProfileNumer' => $profile_id,
            'NewPass' => $new_pass
                ));
        self::check_result($res, 240, "Ошибка при смене пароля.", $profile_id);
        return $res->return;
    }
}

?>
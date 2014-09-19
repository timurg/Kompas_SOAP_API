<?php

require_once 'common.php';

class asaMatriculaRecord {

    private $fform_attestation;
    private $ftype_testing;
    private $fsubject;
    private $fsemester;
    private $fdate;
    private $fis_passed;
    private $fvalue;
    private $ftext_value;
    private $fball;
    private $fmax_ball;
    private $frate;

    public function get_form_attestation() {
        return $this->fform_attestation;
    }

    /**
     * Возвращает форму аттестации.
     *
     * @author Timur
     * @return typeTesting
     */
    public function get_type_testing() {
        return $this->ftype_testing;
    }

    /**
     * Возвращает наименование дисциплины.
     *
     * @author Timur
     * @return string
     */
    public function get_subject() {
        return $this->fsubject;
    }

    /**
     * Возвращает номер сесместра.
     *
     * @author Timur
     * @return int
     */
    public function get_semester() {
        return $this->fsemester;
    }

    /**
     * Возвращает дату аттестации.
     *
     * @author Timur
     * @return DateTime
     */
    public function get_date() {
        return $this->fdate;
    }

    /**
     * Проверяет была ли аттестация пройдена (получена положительная оценка). Истина - если оценка положительная.
     *
     * @author Timur
     * @return bool
     */
    public function is_passed() {
        return $this->fis_passed;
    }

    /**
     * Проверяет была ли аттестация пройдена (получена положительная оценка). Истина - если оценка положительная.
     *
     * @author Timur
     * @return int
     */
    public function get_value() {
        return $this->fvalue;
    }

    public function get_text_value() {
        return $this->ftext_value;
    }

    public function get_ball() {
        return $this->fball;
    }

    
    public function get_max_ball() {
        return $this->fmax_ball;
    }

    public function get_rate() {
        return $this->frate;
    }
    
    public function __construct($form_attestation, typeTesting &$type_testing, $subject, $semester, $date, $is_passed, $value, $text_value, $ball, $max_ball) {
        $this->fform_attestation = $form_attestation;
        $this->ftype_testing = $type_testing;
        $this->fsubject = $subject;
        $this->fsemester = $semester;
        $this->fdate = $date;
        $this->fis_passed = $is_passed;
        $this->fvalue = $value;
        $this->ftext_value = $text_value;
        $this->fball = $ball;
        $this->fmax_ball = $max_ball;

        if ($this->fmax_ball > 0) {
            $this->frate = round(($this->fball * 100) / $this->fmax_ball, 2);
        }
    }

    public function is_accepted() {
        return $this->is_passed() !== NULL;
    }

    public function is_grs() {
        return $this->get_form_attestation() == "grs";
    }

    public function is_test() {
        return $this->get_form_attestation() == "test";
    }

    public function is_paper() {
        return $this->get_form_attestation() == "paper";
    }

}

class asaMatricula extends kompasArray {

    public function add_matricula_record(asaMatriculaRecord &$mr) {
        $this->add($mr);
    }

    /**
     * Возвращает запись из зачётной книжки
     *
     * @author Timur
     * @param string $subject_name Название дисциплины
     * @param typeTesting $type_testing Форма аттестации
     * @param int $semester Номер семестра
     * @return asaMatriculaRecord
     */
    public function find_record($subject_name, typeTesting $type_testing, $semester) {
        foreach ($this as $sub) {
            if (($sub->get_subject() == $subject_name) && ($sub->get_type_testing()->get_id() == $type_testing->get_id()) && ($sub->get_semester() == $semester)) {
                return $sub;
            }
        }
        return NULL;
    }

}

class asaFactory {

    private static $client;
    //ww - Задание на ПАР, open - открытй вопрос, 
    //closed_single - закрытый вопрос с одним вариантом,
    //closed_multi - закрытый вопрос с множественным выбором,
    //seq - последовательность; corr - соответствие.


    public static $test_unit_type = array("open", "closed_single", "closed_multi", "seq", "corr");

    public static function singleton() {
        if (!isset(self::$client)) {

            //ini_set('soap.wsdl_cache_enabled', '0');
            ini_set('soap.wsdl_cache_ttl', '10');
            self::$client = new SoapClient('http://asa.insto.ru:3989/Service1.svc?wsdl');
        }
        return self::$client;
    }

    private static function &parse_matricula_record($response) {
        $text_val = $response->TextValue;
        $rec_date = new DateTime($response->Date);
        $is_pass = $text_val == NULL ? NULL : $response->IsPassed;
        $tt_str = $response->TypeTesting;
        $tt = NULL;
        switch ($tt_str) {
            case "Зачет":
                $tt = new typeTesting(typeTesting::Test);
                break;
            case "Дифференцированный зачет":
                $tt = new typeTesting(typeTesting::CombinedTest);
                break;
            case "Экзамен":
                $tt = new typeTesting(typeTesting::Exam);
                break;
            case "Курсовой проект":
                $tt = new typeTesting(typeTesting::CourseProject);
                break;
            case "Курсовая работа":
                $tt = new typeTesting(typeTesting::CourseWork);
                break;
            case "Контрольная работа":
                $tt = new typeTesting(typeTesting::ControlWork);
                break;
            default:
                $tt = new typeTesting(-1);
        }
        return new asaMatriculaRecord($response->FormAttestation, $tt, 
                $response->Subject, $response->Semester, 
                $rec_date, $is_pass, $response->Value, $text_val, 
                $response->Ball, $response->MaxBall);
    }

    private static function &parse_matricula($response) {
        $matr = new asaMatricula();
        $recs = $response->records->asaMatriculaRecord;
        if (is_array($recs)) {
            foreach ($recs as $value) {
                $mr_val = self::parse_matricula_record($value);
                $matr->add_matricula_record($mr_val);
            }
        } else {
            $mr_val = self::parse_matricula_record($recs);
            $matr->add_matricula_record($mr_val);
        }
        return $matr;
    }
    
    /**
     * Возвращает коллекцию записей зачётной книжки
     *
     * @author Timur
     * @param string $agreement_number Номер договора, зачётной книжки студента.
     * @return asaMatricula
     */
    public static function get_matricula($agreement_number) {
        $res = self::singleton()->GetMatricula(
                array('token' => 'A3E9268D-1360-4ABC-8A1C-DD2D3F7806A4',
                    'AgreementNumber' => $agreement_number));
        return self::parse_matricula($res->GetMatriculaResult);
    }
    
    /**
     * Возвращает рейтинг студента
     *
     * @author Timur
     * @param string $agreement_number Номер договора, зачётной книжки студента.
     * @param string $subject_name Название дисциплины
     * @return float
     */
    public static function get_student_rating($agreement_number, $subject_name) {
        $res = self::singleton()->GetStudentSubjectRating(
                array('token' => '',
                    'AgreementNumber' => $agreement_number,
                    'SubjectName' => $subject_name));
        return (float)$res->GetStudentSubjectRatingResult;
    }
    
    private function parse_students_rating_result($res, $agreement_numbers)
    {
        reset($agreement_numbers);
        $ret = array();
        if (is_array($res->double)) {
            foreach ($res->double as $value) {
                $ret[current($agreement_numbers)] = (float)$value;
                next($agreement_numbers);
            }
        } else {
            $ret[current($agreement_numbers)] = (float)$res->double;
        }
        return $ret;
    }


    /**
     * Возвращает рейтинг студентов
     *
     * @author Timur
     * @param array of string $agreement_numbers Номера договоров, зачётных книжек студентов.
     * @param string $subject_name Название дисциплины
     * @return array of float
     */
    public static function get_students_rating($agreement_numbers, $subject_name) {
        $res = self::singleton()->GetStudentRating(
                array('token' => '',
                    'AgreementNumbers' => array('string'=>$agreement_numbers),
                    'SubjectName' => $subject_name));
        return self::parse_students_rating_result($res->GetStudentRatingResult, 
                $agreement_numbers);
    }

}

?>

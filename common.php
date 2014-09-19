<?php
class kompasArray implements Iterator {
    private $container;

    public function __construct() {
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

?>
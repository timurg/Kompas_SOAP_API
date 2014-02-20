<?php
//В сценари требуется определение следующих констант:
//define("kompas_wdsl", "");
//define("kompas_login", "");
//define("kompas_pass", "");



class kompasArray implements Iterator{
    private $position = 0;
    private $container;
    
    public function __construct()
    {
        $this->position = 0;
    }
    
    function rewind() {
        $this->position = 0;
    }

    function current() {
        return $this->container[$this->position];
    }

    function key() {
        return $this->position;
    }

    function next() {
        ++$this->position;
    }

    function valid() {
        return isset($this->container[$this->position]);
    }
    
    protected function add(&$sw)
    {
        $this->container[] = $sw;
    }
    public function get_count()
    {
        return count($this->container);
    }
	
	protected function get_value($inx)
	{
		return $this->container[$inx];
	}
}


class kompasSemesterWork
{
    private $fnumber;
    private $ftype_testing;
    private $fHours;
	
	private $fcontrolwork = false;
    private $fcoursework = false;
    private $fcourseproject = false;
	
    public function get_number()
    {
        return $this->fnumber;
    }
    
    public function get_type_testing()
    {
        return $this->ftype_testing;
    }
    
	public function get_hours()
    {
        return $this->fHours;
    }
	
	public function control_work()
	{
		return $this->fcontrolwork;
	}
	
	public function course_work()
	{
		return $this->fcoursework;
	}
	
	public function course_project()
	{
		return $this->fcourseproject;
	}
	
    public function __construct($anumber, $atype_testing, $ahours, $acontrolwork, $acoursework, $acourseproject)
    {
                $this->fnumber = $anumber;
                $this->ftype_testing = $atype_testing;
		$this->fHours = $ahours;
		
		$this->fcontrolwork = $acontrolwork;
		$this->fcoursework = $acoursework;
		$this->fcourseproject = $acourseproject;
    }
	
}


class kompasSubject extends kompasArray
{
    private $fid;
    private $fname;
    private $fcode;
	
    public function get_name()
    {
        return $this->fname;
    }
    
    public function get_id()
    {
        return $this->fid;
    }
    
	public function get_code()
    {
        return $this->fcode;
    }
	
    public function __construct($aid, $aname, $acode)
    {
        $this->fid = $aid;
        $this->fname = $aname;
		$this->fcode = $acode;
    }
    
    public function add_semester_work(kompasSemesterWork &$sw)
    {
        $this->add($sw);
    }
	
	public function work_count_in_semester($sem)
	{
		$pres = 0;
		foreach($this as $sw)
		{
			if ($sw->get_number()==$sem)
			{
				$pres = $pres + 1;
			}
		}
		return $pres;
	}
	
	
	public function semester_present($sem)
	{
		$pres = false;
		foreach($this as $sw)
		{
			if ($sw->get_number()==$sem)
			{
				$pres = true;
			}
		}
		return $pres;
	}
	
	public function get_subject_hours()
	{
		if ($this->get_count()>0)
		{
			return $this->get_value(0)->get_hours();
		}
		return 0;
	}
}

class kompasSubjectGroup extends kompasArray{
    private $fnumber;
    
    public function get_number()
    {
        return $this->fnumber;
    }
    
    public function __construct($anumber)
    {
        $this->fnumber = $anumber;
    }
    
    public function add_subject(kompasSubject $s)
    {
        $this->add($s);
    }
}

class kompasCycle  extends kompasArray{
    private $fid;
    private $fname;
    private $fshortname;
   // private $fsubs;
    
    public function get_id()
    {
        return $this->fid;
    }
    
    public function get_name()
    {
        return $this->fname;
    }
    
    public function get_short_name()
    {
        return $this->fshortname;
    }
    
    public function __construct($aid, $aname, $shortname)
    {
        $this->fid = $aid;
        $this->fname = $aname;
        $this->fshortname = $shortname;
        //$this->fsubs = //$subs;
    }
    
    public function get_subjects_groups()
    {
        return $this->fsubs;
    }
    
    public function add_subjects_group(kompasSubjectGroup &$subs)
    {
        $this->add($subs);
    }
}

class kompasCycles extends kompasArray{
    
    public function add_subject(kompasCycle $s)
    {
        $this->add($s);
    }
	
	public function add_cycles(kompasCycles $s)
    {
		foreach ($s as $cycle){
			$this->add($cycle);
		}
    }
	
	public function get_cycle($inx)
	{
		return $this->get_value($inx);
	}
}

class kompasCurriculum {
    private $fcycles;
    private $fmetainfo;
    public function __construct($aMetaInfo)
    {
        $this->fcycles = new kompasCycles();
		$this->fmetainfo = $aMetaInfo;
    }
    
    public function get_cycles()
    {
        return $this->fcycles;
    }
	
	public function get_meta_info()
    {
        return $this->fmetainfo;
    }
}

class kompasFactory
{
    private static $client;
    
   
    public static function singleton()
    {
        if (!isset(self::$client)) {
			//ini_set('soap.wsdl_cache_enabled', '0');
            ini_set('soap.wsdl_cache_ttl', '10');
            self::$client = new SoapClient(kompas_wdsl,
                    array('login' => kompas_login, 'password'=> kompas_pass));
        }
        return self::$client;
    }

    
    private static function parse_semester_work($response)
    {
        $att = "";
        $contr = false;
        $cw = false;
        $cp = false;
        if (isset($response->Attestation))
        {
            $att = $response->Attestation;
        }
        if (isset($response->Controlwork))
        {
            $contr = $response->Controlwork;
        }
        if (isset($response->Coursework))
        {
            $cw = $response->Coursework;
        }
        if (isset($response->Courseproject))
        {
            $cp = $response->Courseproject;
        }
        $res = new kompasSemesterWork($response->Semester, $att,
			$response->Hours, $contr, $cw, $cp);
        return $res;
    }
    
    private static function parse_subject($response)
    {
        $res = new kompasSubject("", $response->Name, $response->Code);
        $sw = $response->SemesterWork;
	    if (is_array($sw)) 
		{
			foreach($sw as $value) {
				$sw_val = self::parse_semester_work($value);
				$res->add_semester_work($sw_val);
			}
		}
		else
		{
			$sw_val = self::parse_semester_work($sw);
			$res->add_semester_work($sw_val);
		}
		//print $res->get_name();
        return $res;
    }
    
    private static function parse_subject_group($response)
    {
        $res = new kompasSubjectGroup($response->Code);
		if (is_array($response->Subject)){
			foreach($response->Subject as $value)
			{
				$res->add_subject(self::parse_subject($value));
			};
		}
		else
		{
			$res->add_subject(self::parse_subject($response->Subject));
		}
        return $res;
    }
	
    private static function parse_cycle($response)
    {
	$c_id = $response->IsOptional;
	$c_name = $response->Name;
	$c_shortname = $response->Abbreviation;
        $res = new kompasCycle($c_id, $c_name, $c_shortname);
        
		//var_dump($response->SubjectGroups);
		//echo '====================================';
		//echo '<pre>';	 var_dump($response); echo '</pre>';
        if (is_array($response->SubjectGroup)){
            foreach($response->SubjectGroup as $value)
            {
                $c_sg = self::parse_subject_group($value);
                $res->add_subjects_group($c_sg);
            }
        }
        else{
            $c_sg = self::parse_subject_group($response->SubjectGroup);
            $res->add_subjects_group($c_sg);
        }
        return $res;
    }
	
	private static function parse_cycles($response)
    {
        $res = new kompasCycles();
		if (is_array($response->Cycle)){
			foreach($response->Cycle as $value)
			{
				$res->add_subject(self::parse_cycle($value));
			};
		}
		else
		{
			$res->add_subject(self::parse_cycle($response->Cycle));
		}
        return $res;
    }
    
	protected static function parse_meta_info($buff)
    {
		
		$res = new asaMetaInfo($buff->OrganizationName,
                $buff->SubdivisionName,
                $buff->DirectionName,
                $buff->SpecializationName,
                $buff->DurationEducation,
                $buff->QualificationEducation,
                $buff->FormEducation,
                $buff->BaseEducationRate,
				$buff->Member);
		return $res;
    }
	
    
   
    public static function get_user_curriculum($un)
    {
		$res = self::singleton()->GetFullStudentInfo(array('KontrNumber'=>$un));
                //var_dump($res);
		$result = new kompasCurriculum("");
                $result->get_cycles()->add_cycles(self::parse_cycles($res->return->Curriculum));
                return $result;
    }
}

?>
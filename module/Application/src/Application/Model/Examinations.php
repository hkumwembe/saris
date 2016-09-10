<?php
namespace Application\Model;

/**
 * Description of Admission
 *
 * @author hkumwembe
 */
use \Doctrine\Common\Collections\Criteria;
use DoctrineModule\Paginator\Adapter\Collection as Adapter;
use Zend\Paginator\Paginator;
use Application\Model\Assessment;

class Examinations extends Commonmodel {

    protected $em;
    protected $assessment;
    
    public function __construct(\Doctrine\ORM\EntityManager $em) {
        parent::__construct($em);
        $this->em = $em;
        $this->assessment  = new Assessment($em);
    }  
    
    /*
     * Get department classes
     */
    public function getDepartmentClasses($deptid){
        $query = $this->em->createQuery(" SELECT C,P,D FROM \Application\Entity\Classes C"
                                      . " JOIN C.fkProgramid P "
                                      . " JOIN P.fkDeptid D "
                                      . " WHERE D.pkDeptid = :deptid")
                          ->setParameter("deptid", $deptid);
        return $query->getResult();
    }
    
    /*
     * Get serviced modules
     */
    public function getServicedModules($deptid,$period = null){
        
        $currentperiod  = $this->getCurrentPeriod();
        $selectedmodules = array();
        $selectedperiod = ($period != null)?$period:$currentperiod[0]->getPkAcademicperiodid();
        
        $query = $this->em->createQuery(" SELECT S,C FROM \Application\Entity\Servicedmodule S"
                                      . " JOIN S.fkClassmoduleid C "
                                      . " WHERE S.servicingdept = :deptid "
                                      . " AND C.fkAcademicperiod = :period")
                          ->setParameter("deptid", $deptid)
                          ->setParameter("period", $selectedperiod);
        foreach($query->getResult() as $results){
            $selectedmodules[] = $results;
        }
        
        return $selectedmodules;
    }
    
    public function getRemark($swcount,$lwcount){
        
        $remark = "PS";
        
        if(($swcount + $lwcount) > 3){
            $remark = "RT";
        }elseif($swcount == 1 || $lwcount > 1){
            $remark = "SW";
        }elseif($lwcount == 1){
            $remark = "LW";
        }
        
        return $remark;
    }

    /*
     * Get student subject performance
     */
    public function getStudentSubjectPerformance($student,$items){
        $fg = $exammark = $cwk = $average = $cwkcount = 0;
        $rm = '-';
        if(!empty($items)){
            foreach($items as $item){
                $studentmark  = $this->em->getRepository("\Application\Entity\Studentmark")->findOneBy(array("fkStudentclassid"=>$student,"fkAiid"=>$item->getPkAiid()));
                //Check if the assessment item is assignment or midsemester
                if($item->getFkAtid()->getTypeCode()== "ASS" || $item->getFkAtid()->getTypeCode()== "MID"){
                    $average += ($item->getWeighting()/100) * $studentmark->getMark();
                    $cwkcount++;
                }
                //Check if item is end of semester exam
                if($item->getFkAtid()->getTypeCode()== "EXAM"){
                    $exammark = $studentmark->getMark();
                }
                //Check if item is average score
                if($item->getFkAtid()->getTypeCode()== "SSG"){
                    $fg = $studentmark->getMark();
                }
            }
        //Get subject remark
        $rm = ($fg>=50)?"PS":"FW";
        $cwk = round($average/$cwkcount);
        }
        
        return array("CWK"=>$cwk,"EXAM"=>$exammark,"FG"=>$fg,"RM"=>$rm);
    }

    
    /*
     * Generate remarks for students
     */
    
    public function generateResultcode($classid,$period,$level){
        
        $currentLevel = $level + 1;
        $students  = $this->getClasslist(array("fkClassid"=>$classid,"fkAcademicperiodid"=>$period->getPkAcademicperiodid()));
        $modules   = $this->em->getRepository("\Application\Entity\Classmodule")->findBy(array("fkClassid"=>$classid,"fkAcademicperiod"=>$period->getPkAcademicperiodid()));
        //Get assessment scale ranges
        $ranges    = $this->em->getRepository("\Application\Entity\Assessment101Scale")->findAll();
        foreach($students as $student){
            $grades = array();
            $studentgrade  = $this->em->getRepository("\Application\Entity\Studenteos")->findOneBy(array("fkStudentclassid"=>$student->getPkStudentclassid()));
            
            $modulecount = $swcount = $lwcount = $failurecount = 0;
            $modulesum   = $average = 0;
            
            foreach($modules as $module){
                
                //Get grade 
                $studentmark = $this->getAssessmentitemMarks(array("T.systemGenerated"=>'1',"A.fkClassmoduleid"=>$module->getPkClassmoduleid(),"S.fkStudentclassid"=>$student->getPkStudentclassid()),$level);
                
                if(count($studentmark)>0){
                    
                    $grades[] = $studentmark[0];
                    $modulesum += $studentmark[0]->getMark();
                    $modulecount++;
                }
            }
           
            $content = $this->assessment->createXMLContent($grades,$ranges,$period->getIsfinal());
            $resultCode = $this->assessment->GetResultCode($content); 
           
            if($modulecount != 0){
                $average = round($modulesum/$modulecount);
                $remark  = $this->em->getRepository("\Application\Entity\Resultcode")->findOneBy(array("code"=>$resultCode));
                //Save end of semester result
                $eosEntity  = count($studentgrade)>0?$studentgrade:new \Application\Entity\Studenteos();
                
                $eosEntity->setFkStudentclassid($student);
                if($currentLevel == 2){
                    $eosEntity->setEosaverageSystem($average);
                    $eosEntity->setResultcodeSystem($remark);
                }elseif($currentLevel == 3){
                    $eosEntity->setEosaverageDept($average);
                    $eosEntity->setResultcodeDept($remark);
                }elseif($currentLevel == 4){
                    $eosEntity->setEosaverageFaculty($average);
                    $eosEntity->setResultcodeFaculty($remark);
                }elseif($currentLevel == 5){
                   
                    $eosEntity->setEosaverageCollege($average);
                    $eosEntity->setResultcodeCollege($remark);
                }
                
                //Save entity
                $this->saveEosResult($eosEntity);
            }
        } 
    }

    
    public function getLecturerModuleAllocation($deptid,$period = null){
        //Current period
        $currentperiod  = $this->getCurrentPeriod();
        $selectedmodules = array();
        $selectedperiod = ($period != null)?$period:$currentperiod[0]->getPkAcademicperiodid();
        //Get department classes
        $classes = $this->getDepartmentClasses($deptid);

        foreach($classes as $class){
                $selectedmodules = array();
            //Get all modules assigned
                $modules = $this->em->getRepository("\Application\Entity\Classmodule")->findBy(array("fkAcademicperiod"=>$selectedperiod,"fkClassid"=>$class->getPkClassid()));
                //Get assigned modules
                foreach($modules as $module){
                    $canmodify = $isinhand = 1;
                    $status = "<span class='text text-danger'>Not allocated</span>";
                    //Get requested details
                    $staff = $this->em->getRepository("\Application\Entity\Servicedmodule")->findOneBy(array("fkClassmoduleid"=>$module->getPkClassmoduleid()));
                    //Get assigned staff
                    if(count($staff)<=0){
                        $staff     = $this->em->getRepository("\Application\Entity\Lecturermodule")->findOneBy(array("fkClassmoduleid"=>$module->getPkClassmoduleid()));
                        $status    = empty($staff)?sprintf("%s","<span class='text text-danger'>Not allocated</span>"):sprintf("<span class='text text-info'>%s, %s</span>",$staff->getFkStaffid()->getFkUserid()->getSurname(),  substr($staff->getFkStaffid()->getFkUserid()->getFirstname(),0,1)); 
                        $canmodify = empty($staff)?0:1;
                    }else{
                        $isinhand = 0;
                        if(!empty($staff->getFklmid())){
                            $status = sprintf("<span class='text text-info'>%s, %s (%s)</span>",$staff->getFklmid()->getFkStaffid()->getFkUserid()->getSurname(),substr($staff->getFklmid()->getFkStaffid()->getFkUserid()->getFirstname(),0,1),$staff->getServicingdept()->getDeptCode());
                        }elseif($staff->getFlag() == "UNABLETOALLOCATE"){
                            $status   = sprintf("<span class='text text-info'>%s</span>","UNABLE TO ALLOCATE"); 
                            $isinhand = 1;
                            $canmodify = 0;
                        }else{
                          $status   = sprintf("%s (%s)","HOD",$staff->getServicingdept()->getDeptCode());  
                        }
                        
                        
                    }
                    
                    $selectedmodules[$module->getPkClassmoduleid()] = array("module"=>$module,"allocations"=>$status,"canmodify"=>$canmodify,"isinhand"=>$isinhand,"staff"=>$staff);
                }
                
                $allocation[$class->getPkClassid()] = array("CLASSNAME"=> $class->getClassName()."(".$class->getClassCode().")","MODULE"=>$selectedmodules);
        }
        
       return $allocation;
    }
    
    
    
    public function getSubjectSummary($period,$group){
        $allocations = $this->em->getRepository('\Application\Entity\Classmodule')->findBy(array("fkPeriodid"=>$period->getPkPeriodid(),"fkGroupid"=>$group));
        
        //Get total number of students in the class
        $currentstudents = $this->em->getRepository('\Application\Entity\Studentclass')->findBy(array("fkPeriodid"=>$period->getPkPeriodid(),"fkGroupid"=>$group));
        $received = 0;
        foreach($allocations as $allocation){
            
            //Get aggregate students
            $query = $this->em->createQuery("SELECT COUNT(M.mark) AS markcount FROM \Application\Entity\Studentmark M"
                                 . " JOIN M.fkAiid A "
                                 . " JOIN A.fkAtid T"
                                 . " WHERE T.systemGenerated = 1"
                                 . " AND A.fkCcid = :course"
                                 . " GROUP BY M.fkAiid")
                    ->setParameter('course', $allocation->getPkCcid());
            if(count($query->getArrayResult())>0){
                
                $result = $query->getOneOrNullResult();
                $received = (!empty($result['markcount'])?$result['markcount']:0);
            }
            $modules[] = array("RECEIVED"=>$received,"TOTAL"=>count($currentstudents),"SUBJECTNAME"=>$allocation->getFkModuleid()->getModuleName(),"SUBJECTCODE"=>$allocation->getFkModuleid()->getModuleCode());
            $received = 0;
            
        }
        return $modules;
    }


    public function getLecturerModules($userid,$period=null){
       $query = $this->em->createQuery("SELECT M,C,S FROM \Application\Entity\Lecturermodule M "
                                       . " JOIN M.fkStaffid S "
                                       . " JOIN M.fkClassmoduleid C"
                                       . " WHERE S.fkUserid = :user "
                                       . " AND   C.fkAcademicperiod = :period")
                         ->setParameter('user', $userid)
                         ->setParameter('period', $period);
       return $query->getResult();
    }
    
    public function getLecturerQuestions($criteria = null,$groupBy= null){
        $query = $this->em->createQueryBuilder()
                           ->select('G,Q,S,A,C,M')
                           ->from('\Application\Entity\Question','Q')
                           ->join('Q.fkStaffid','S')
                           ->join('Q.fkAiid','A')
                           ->join('A.fkClassmoduleid','C')
                           ->join('C.fkClassid','G')
                           ->join('C.fkModuleid','M');
        if(!empty($criteria)){
            foreach($criteria as $field=>$value){
              $query->andWhere("{$field}='{$value}'");
            }
        }
        if($groupBy != null){
           $query->groupBy($groupBy);
        }
         return $query->getQuery()->execute();
    }
    
    public function getStudentMarks($criteria=null){
        $query = $this->em->createQueryBuilder()
                           ->select('S,A,T,CM,M')
                           ->from('\Application\Entity\Studentmark','S')
                           ->join('S.fkAiid','A')
                           ->join('A.fkAtid','T')
                           ->join('A.fkClassmoduleid','CM')
                           ->join('CM.fkModuleid','M');
        if(!empty($criteria)){
            foreach($criteria as $field=>$value){
              $query->andWhere("{$field}='{$value}'");
            }
        }
        
        return $query->getQuery()->execute();
    }
    
    public function getModulePerformance($criteria=null){
        $query = $this->em->createQueryBuilder()
                           ->select(" M.moduleCode as code,M.moduleName as name,
                                     AVG(S.mark) as average,
                                     MAX(S.mark) as maxmark,
                                     MIN(S.mark) as minmark,
                                     SUM((CASE 
                                     WHEN (S.mark>=75) THEN 1 
                                        ELSE 0
                                     END) AS DN,
                                     SUM((CASE 
                                     WHEN (S.mark>=60 AND S.mark < 75) THEN 1 
                                        ELSE 0
                                     END) AS CR,
                                     SUM((CASE 
                                     WHEN (S.mark < 50) THEN 1 
                                        ELSE 0
                                     END) AS F,
                                     SUM((CASE 
                                     WHEN (S.mark>=50 AND S.mark < 60) THEN 1 
                                        ELSE 0
                                     END) AS PS,
                                     SUM((CASE 
                                     WHEN (S.mark>=70) THEN 1 
                                        ELSE 0
                                     END) AS SPLUS,
                                     SUM((CASE 
                                     WHEN (S.mark>=60 AND S.mark < 70) THEN 1 
                                        ELSE 0
                                     END) AS SXPLUS,
                                     SUM((CASE 
                                     WHEN (S.mark>=50 AND S.mark < 60) THEN 1 
                                        ELSE 0
                                     END) AS FFPLUS,
                                     SUM((CASE 
                                     WHEN (S.mark>=40 AND S.mark < 50) THEN 1 
                                        ELSE 0
                                     END) AS FTPLUS,
                                     SUM((CASE 
                                     WHEN (S.mark>=35 AND S.mark < 40) THEN 1 
                                        ELSE 0
                                     END) AS TPLUS,
                                     SUM((CASE 
                                     WHEN (S.mark < 35) THEN 1 
                                        ELSE 0
                                     END) AS ZPLUS
                                     ")
                      ->from('\Application\Entity\Studentmark','S')
                      ->join('S.fkAiid', 'A')
                      ->join('A.fkAtid', 'T')
                      ->join('A.fkClassmoduleid', 'C')
                      ->join('C.fkModuleid', 'M');
        if(!empty($criteria)){
            foreach($criteria as $field=>$value){
              $query->andWhere("{$field}='{$value}'");
            }
        }
        
        return $query->getQuery()->execute();
    }
    
    
    public function getAverageMark($parameters = array()){
        $dql   = $this->em->createQueryBuilder();
        $query = $dql->select("E,C,S")
                     ->from("\Application\Entity\Studenteos", "E")
                     ->join("E.fkStudentclassid", "C")
                     ->join("C.fkStudentid", "S");
        if(!empty($parameters)){
            foreach($parameters as $field=>$value){
                 $dql->andWhere("{$field}='{$value}'");
            }
        }
                     
        $rs = $query->getQuery();
        return $rs->execute();
    }
    
    /*
     * @var parameters array
     * @return array
     */
    
    public function getDistinctGradeTrail($parameters = array(),$limit = 0){
        
        $dql   = $this->em->createQueryBuilder();
        $query = $dql->select(["F,S,G"])
                    // ->addSelect("MAX(F.timestamp) AS HIDDEN MAXTIMESTAMP")  
                     ->from("\Application\Entity\FgTracker", "F")
                     ->join("F.fkSmid", "S")
                     ->join("F.fkGradeflowid", "G");
        if(!empty($parameters)){
            foreach($parameters as $field=>$value){
                 $dql->andWhere("{$field}='{$value}'");
            }
        }
        
        $dql->groupBy("F.fkGradeflowid");
        $dql->orderBy("F.timestamp", "DESC");
               
        $rs = $query->getQuery();
        //print_r($rs->getSQL())."<BR>";
        return $rs->execute();
    }
    
    /*
     * @var parameters array
     * @return array
     */
    
    public function getModuleGradeTrailHeaders($parameters = array()){
        $header = array();
        foreach($this->getDistinctGradeTrail($parameters) as $grade){
           
            $header[$grade->getFkGradeflowid()->getLevel()] = $grade->getFkGradeflowid()->getFkRoleid()->getRoleName();
        }
        
        return $header;
    }
    
    
    /*
     * @var parameters array
     * @return array
     */
    
    public function getStudentModuleGradeTrail($parameters = array()){
        $tracked_grades = array();
        foreach($this->getDistinctGradeTrail($parameters,1) as $grade){
            //Get level grade
            $selectedGrade  = $this->em->getRepository("\Application\Entity\FgTracker")->findOneBy(array("fkGradeflowid"=>$grade->getFkGradeflowid()->getPkGradeflowid(),"fkSmid"=>$grade->getFkSmid()->getPkSmid()),array("timestamp"=>"DESC"));
            $tracked_grades[$selectedGrade->getFkSmid()->getFkStudentclassid()->getPkStudentclassid()][$selectedGrade->getFkGradeflowid()->getLevel()] = array("DATE"=>$selectedGrade->getTimestamp(),"GRADE"=>$selectedGrade->getGrade());
        }
        
        return $tracked_grades;
    }
    
    
    public function getAssignedModules($period,$group){
        $query = $this->em->createQuery("SELECT L FROM \Application\Entity\Lecturermodule L JOIN L.fkCcid P WHERE P.fkPeriodid = :period AND P.fkGroupid = :group")
                         ->setParameter('period', $period)
                         ->setParameter('group', $group);
        return $query->getResult();
    }
    
    /*
     * Format imported file content
     */
    
    public function formatFileImport($row,$asssementitem){
        $errors = array();
        $mark      = $row[1];
        $studentid = $row[0];
        
        //Validate if mark is valid
        if(!empty($mark)){
            //Check if mark is numeric and between 0 and 100
            if(!is_numeric($mark) || ($mark<0 || $mark>100)){
                $errors[]  = "Mark is not numeric or is not between 0-100";
            }
            
        }
        
        //Validate if student unique identifier exists
        if(!empty($studentid)){
            //Retrieve student class
            if($asssementitem->getFkAtid()->getTypeCode()=='EXAM'){
                $studentclass   = $this->em->getRepository('\Application\Entity\Studentclass')->findOneBy(array('examnumber'=>$studentid,"fkAcademicperiodid"=>$asssementitem->getFkClassmoduleid()->getFkAcademicperiod()->getPkAcademicperiodid()));
            }else{
                $studentprogram = $this->em->getRepository('\Application\Entity\Studentprogram')->findOneBy(array('studentnumber'=>$studentid));
                $studentclass   = $this->em->getRepository('\Application\Entity\Studentclass')->findOneBy(array('fkStudentprogramid'=>$studentprogram->getPkStudentprogramid()));
            }
            
           if(count($studentclass)<=0){
                $errors[]  = "Student number does not exist or not assigned module or class";
            }
            
        }
        return array("STUDENTNUMBER"=>$studentid,"MARK"=>$mark,"STUDENTCLASSID"=>$studentclass->getPkStudentclassid(),"ERRORS"=>$errors);
    }

    /*
     * Get assessment items
     * @params: courseid
     */
    public function moveGrade($assessmentitem,$destination,$start,$user,$students=array()){
        
        $selectedstudents = !empty($students)?$students:$this->em->getRepository("\Application\Entity\Studentmark")->findBy(array("fkAiid"=>$assessmentitem,"markLevel"=>$start));
        
        $gradedestination = $this->em->getRepository("\Application\Entity\Gradeflow")->findOneBy(array("level"=>$destination));
        
        $staffid          = $this->em->getRepository("\Application\Entity\Staff")->findOneBy(array("fkUserid"=>$user));
        
        foreach($selectedstudents as $student){
            $markEntity = $this->em->getRepository("\Application\Entity\Studentmark")->find($student->getPkSmid());
            //Copy record to grade tracker
            $fgEntity   = new \Application\Entity\FgTracker();
            $fgEntity->setCapturedby($staffid);
            $fgEntity->setFkGradeflowid($markEntity->getMarkLevel());
            $fgEntity->setFkSmid($markEntity);
            $fgEntity->setGrade($markEntity->getMark());
            $fgEntity->setInputMethod("Automatic");
            //$fgEntity->setNotes($notes);
            $fgEntity->setTimestamp($markEntity->getExamdate());
            
            if($this->saveGradeTracker($fgEntity)){
                //Update record
                $markEntity->setMarkLevel($gradedestination);
                $markEntity->setExamdate(new \DateTime());
                $this->saveMarks($markEntity);
            }
            
            
            
        }
    }
    
    /*
     * Get assessment items
     * @params: courseid
     */
    public function getStudentAssessmentMarks($id){
        $classstudents = array();
        //Get class module details
        $module  = $this->em->getRepository('\Application\Entity\Classmodule')->find($id);
        //Get list of students
        //$students       = $this->em->getRepository('\Application\Entity\Studentclass')->findBy(array("fkGroupid"=>$module->getFkGroupid()->getPkGroupid(),"fkPeriodid"=>$module->getFkPeriodid()->getPkPeriodid()));
        
        $searchcriteria = Criteria::create()
                              ->where(Criteria::expr()->eq('fkPeriodid', $module->getFkPeriodid()))
                              ->andWhere(Criteria::expr()->eq('fkGroupid', $module->getFkGroupid()));
        
        $students       = $this->getClasslist($searchcriteria);
        
        $assessments   = $this->getAssessmentitems($id);
        
        foreach($students as $student){
            
            //Get list of marks
            foreach($assessments as $assessment){
                //Get actual mark for student
                $mark                         = $this->em->getRepository('\Application\Entity\Studentmark')->findOneBy(array("fkAiid"=>$assessment->getPkAiid(),"fkStudentid"=>$student['CLASS']->getFkStudentid()->getPkStudentid()));
                if(count($mark)){
                   $marks[$assessment->getPkAiid()] = array("MARK"=>$mark->getMark(),"MARKID"=>$mark->getPkSmid());
                }else{
                   $marks[$assessment->getPkAiid()] = array("MARK"=>'');  
                }
            }
            
            $classstudents[] = array("REGISTRATIONNO"  =>$student['PROGRAM']->getRegistrationNumber(),
                                     "SURNAME"  =>$student['CLASS']->getFkStudentid()->getFkUserid()->getSurname(),
                                     "FIRSTNAME"=>$student['CLASS']->getFkStudentid()->getFkUserid()->getFirstname(),
                                     "STUDENTID"=>$student['CLASS']->getFkStudentid()->getPkStudentid(),
                                     "GENDER"   =>$student['CLASS']->getFkStudentid()->getFkUserid()->getGender(),
                                     "MARKS"    =>$marks);
        }        
        return $classstudents;
    }
    
    
     /*
     * Get assessment items
     * @params: courseid
     */
    public function getAssessmentitems($criteria = null){
        
        $query = $this->em->createQueryBuilder();
        $query->select("A")
              ->from("\Application\Entity\Assessmentitem", "A")
              ->join("A.fkAtid", "I");
        if(!empty($criteria)){
            foreach($criteria as $field=>$value){
                $query->andWhere("{$field}='{$value}'");
            }
        }
        $qb  = $query->getQuery();
//        print_r($qb->getSQL());
//        die();
        return $qb->getResult();
    }
    
    /*
     * Get assessment items
     * @params: criteria
     */
    public function getAssessmentitemMarks($criteria = null,$level = 0){
        
        $query = $this->em->createQueryBuilder();
        $query->select("S,A")
              ->from("\Application\Entity\Studentmark", "S")
              ->join("S.fkAiid", "A")
              ->join("A.fkAtid", "T")
              ->where("S.markLevel > $level");
        if(!empty($criteria)){
            foreach($criteria as $field=>$value){
                $query->andWhere("{$field}='{$value}'");
            }
        }
        $qb  = $query->getQuery();
        
        return $qb->getResult();
    }
    
    
    /*
     * Get module students
     * @params: module
     */
    public function getModuleStudents($module){
        $students = array();
        //If module is core then get all candidates for the class + some those auditing
        if($module->getIsCore()==1){
            
            //Get students from class
            $classstudents = $this->em->getRepository("\Application\Entity\Studentclass")->findBy(array("fkClassid"=>$module->getFkClassid()->getPkClassid(),"fkAcademicperiodid"=>$module->getFkAcademicperiod()->getPkAcademicperiodid(),"status"=>'1'),array("examnumber"=>"asc"));
            foreach($classstudents as $classstudent){
                $students[] = array("STUDENTCLASSID"=>$classstudent->getPkStudentclassid(),"REGNUMBER"=>$classstudent->getFkStudentprogramid()->getStudentnumber(),"SURNAME"=>$classstudent->getFkStudentid()->getFkUserid()->getSurname(),"FIRSTNAME"=>$classstudent->getFkStudentid()->getFkUserid()->getFirstname(),"GENDER"=>$classstudent->getFkStudentid()->getFkUserid()->getGender(),"EXAMNUMBER"=>$classstudent->getExamnumber());
            }
           
            //Get elective module
            $electivestudents = $this->em->getRepository("\Application\Entity\Studentmodule")->findBy(array("fkClassmoduleid"=>$module->getPkClassmoduleid()));
            foreach($electivestudents as $electivestudent){
               
                $students[] = array("STUDENTCLASSID"=>$electivestudent->getFkStudentclassid()->getPkStudentclassid(),"REGNUMBER"=>$electivestudent->getFkStudentclassid()->getFkStudentprogramid()->getStudentnumber(),"SURNAME"=>$electivestudent->getFkStudentclassid()->getFkStudentid()->getFkUserid()->getSurname(),"FIRSTNAME"=>$electivestudent->getFkStudentclassid()->getFkStudentid()->getFkUserid()->getFirstname(),"GENDER"=>$electivestudent->getFkStudentclassid()->getFkStudentid()->getFkUserid()->getGender(),"EXAMNUMBER"=>$electivestudent->getFkStudentclassid()->getExamnumber());
            }
        }elseif($module->getIsCore()==0){
            //If module is elective, get from student elective
            $electivestudents = $this->em->getRepository("\Application\Entity\Studentmodule")->findBy(array("fkClassmoduleid"=>$module->getPkClassmoduleid()));
            foreach($electivestudents as $electivestudent){
                $students[] = array("STUDENTCLASSID"=>$electivestudent->getFkStudentclassid()->getPkStudentclassid(),"REGNUMBER"=>$electivestudent->getFkStudentclassid()->getFkStudentprogramid()->getStudentnumber(),"SURNAME"=>$electivestudent->getFkStudentclassid()->getFkStudentid()->getFkUserid()->getSurname(),"FIRSTNAME"=>$electivestudent->getFkStudentclassid()->getFkStudentid()->getFkUserid()->getFirstname(),"GENDER"=>$classstudent->getFkStudentclassid()->getFkStudentid()->getFkUserid()->getGender(),"EXAMNUMBER"=>$electivestudent->getFkStudentclassid()->getExamnumber());
            }
        }
        
        return $students;
    }
    
    
    /*
     * 
     */
    public function saveAssessmentItem($object){
        
        if(!$object->getPkAiid()){
            $eo = new \Application\Entity\Assessmentitem();
        }else{
            $eo = $this->em->getRepository("\Application\Entity\Assessmentitem")->find($object->getPkAiid());
        }
        
        $eo->setAssessmentTitle($object->getAssessmentTitle());
        $eo->setCreatedon($object->getCreatedon());
        $eo->setShortName($object->getShortName());
        $eo->setFkAtid($object->getFkAtid());
        $eo->setFkClassmoduleid($object->getFkClassmoduleid());
        $eo->setFkStaffid($object->getFkStaffid());
        $eo->setWeighting($object->getWeighting());
      
        try{
            //Commit values set to the object 
            if(!$object->getPkAiid()){
                $this->em->persist($eo);
            }

            //Save values if just updating record
            $this->em->flush($eo);
            
            return $eo;
            
        }catch(Exception $e){
            
            throw($e->getMessages());
        }
    }
    
    /*
     * Save question
     */
    public function saveQuestion($object){
        
        if(!$object->getPkQid()){
            $eo = new \Application\Entity\Question();
        }else{
            $eo = $this->em->getRepository("\Application\Entity\Question")->find($object->getPkQid());
        }
        $eo->setQuestion($object->getQuestion());
        $eo->setMarkOutOf($object->getMarkOutOf());
        $eo->setFkAiid($object->getFkAiid());
        $eo->setFkStaffid($object->getFkStaffid());
        $eo->setQuestionNumber($object->getQuestionNumber());
        $eo->setInHand($object->getInHand());
        $eo->setFkPaperid($object->getFkPaperid());
        try{
            //Commit values set to the object 
            if(!$object->getPkQid()){
                $this->em->persist($eo);
            }
            //Save values if just updating record
            $this->em->flush($eo);
            return $eo;
        }catch(Exception $e){
            throw($e->getMessages());
        }
    }
    
    /*
     * Save lecturer module
     */
    public function saveLecturerModule($object){
        
        if(!$object->getPkLmid()){
            $eo = new \Application\Entity\Lecturermodule();
        }else{
            $eo = $this->em->getRepository("\Application\Entity\Lecturermodule")->find($object->getPkLmid());
        }
       
        $eo->setFkClassmoduleid($object->getFkClassmoduleid());
        $eo->setFkStaffid($object->getFkStaffid());
        
        try{
            //Commit values set to the object 
            if(!$object->getPkLmid()){
                $this->em->persist($eo);
            }

            //Save values if just updating record
            $this->em->flush($eo);
            
            return $eo;
            
        }catch(Exception $e){
            
            throw($e->getMessages());
        }
    }
    
    
     /*
     * Save End of semester result
     */
    public function saveEosResult($object){
        
        if(!$object->getPkEosid()){
            $eo = new \Application\Entity\Studenteos();
        }else{
            $eo = $this->em->getRepository("\Application\Entity\Studenteos")->find($object->getPkEosid());
        }
       
        $eo->setFkStudentclassid($object->getFkStudentclassid());
        $eo->setEosaverageCollege($object->getEosaverageCollege());
        $eo->setResultcodeCollege($object->getResultcodeCollege());
        $eo->setEosaverageSystem($object->getEosaverageSystem());
        $eo->setResultcodeSystem($object->getResultcodeSystem());
        $eo->setEosaverageDept($object->getEosaverageDept());
        $eo->setResultcodeDept($object->getResultcodeDept());
        $eo->setEosaverageFaculty($object->getEosaverageFaculty());
        $eo->setResultcodeFaculty($object->getResultcodeFaculty());
        
        try{
            //Commit values set to the object 
            if(!$object->getPkEosid()){
                $this->em->persist($eo);
            }

            //Save values if just updating record
            $this->em->flush($eo);
            
            return $eo;
            
        }catch(Exception $e){
            
            throw($e->getMessages());
        }
    }
    
    
    /*
     * Save Grade flow stage
     */
    public function saveFlowStage($object){
        
        if(!$object->getPkGradeflowid()){
            $eo = new \Application\Entity\Gradeflow();
        }else{
            $eo = $this->em->getRepository("\Application\Entity\Gradeflow")->find($object->getPkGradeflowid());
        }
       
        $eo->setFkRoleid($object->getFkRoleid());
        $eo->setDescription($object->getDescription());
        $eo->setLevel($object->getLevel());
        
        try{
            //Commit values set to the object 
            if(!$object->getPkGradeflowid()){
                $this->em->persist($eo);
            }
            //Save values if just updating record
            $this->em->flush($eo);
            return $eo;
        }catch(Exception $e){
            throw($e->getMessages());
        }
    }
    
    
    /*
     * Save serviced module
     */
    public function saveServicedModule($object){
        
        if(!$object->getPkSmid()){
            $eo = new \Application\Entity\Servicedmodule();
        }else{
            $eo = $this->em->getRepository("\Application\Entity\Servicedmodule")->find($object->getPkSmid());
        }
       
        $eo->setFkClassmoduleid($object->getFkClassmoduleid());
        $eo->setReqdept($object->getReqdept());
        $eo->setServicingdept($object->getServicingdept());
        $eo->setFklmid($object->getFklmid());
        $eo->setFlag($object->getFlag());
        
        try{
            //Commit values set to the object 
            if(!$object->getPkSmid()){
                $this->em->persist($eo);
            }

            //Save values if just updating record
            $this->em->flush($eo);
            
            return $eo;
            
        }catch(Exception $e){
            
            throw($e->getMessages());
        }
    }
    
    /*
     * 
     */
    public function saveMarks($object){
        
        if(!$object->getPkSmid()){
            $eo = new \Application\Entity\Studentmark();
        }else{
            $eo = $this->em->getRepository("\Application\Entity\Studentmark")->find($object->getPkSmid());
        }
        
       
        $eo->setExamdate($object->getExamdate());
        $eo->setFkAiid($object->getFkAiid());
        $eo->setFkStudentclassid($object->getFkStudentclassid());
        $eo->setMark($object->getMark());
        $eo->setMarkLevel($object->getMarkLevel());
        $eo->setPublishStatus($object->getPublishStatus());
        $eo->setUploadby($object->getUploadby());
         
        try{
            
            //Commit values set to the object 
            if(!$object->getPkSmid()){
                $this->em->persist($eo);
            }

            //Save values if just updating record
            $this->em->flush($eo);
            
            return $eo;
            
        }catch(Exception $e){
            
            throw($e->getMessages());
        }
    }
    
    
    /*
     * Save question mark
     */
    public function saveQuestionMark($object){
        
        if(!$object->getPkQgid()){
            $eo = new \Application\Entity\Questionmark();
        }else{
            $eo = $this->em->getRepository("\Application\Entity\Questionmark")->find($object->getPkQgid());
        }

        $eo->setFkQid($object->getFkQid());
        $eo->setFkStudentclassid($object->getFkStudentclassid());
        $eo->setMark($object->getMark());     
        try{
            //Commit values set to the object 
            if(!$object->getPkQgid()){
                $this->em->persist($eo);
            }

            //Save values if just updating record
            $this->em->flush($eo);
            
            return $eo;
            
        }catch(Exception $e){
            
            throw($e->getMessages());
        }
    }
    
    /*
     * 
     */
    public function saveGradeTracker($object){
        
        if(!$object->getPkFgtrackingid()){
            $eo = new \Application\Entity\FgTracker();
        }else{
            $eo = $this->em->getRepository("\Application\Entity\FgTracker")->find($object->getPkFgtrackingid());
        }
       
        $eo->setCapturedby($object->getCapturedby());
        $eo->setFkGradeflowid($object->getFkGradeflowid());
        $eo->setFkSmid($object->getFkSmid());
        $eo->setGrade($object->getGrade());
        $eo->setInputMethod($object->getInputMethod());
        $eo->setTimestamp($object->getTimestamp());
        $eo->setNotes($object->getNotes());
         
        try{
            
            //Commit values set to the object 
            if(!$object->getPkFgtrackingid()){
                $this->em->persist($eo);
            }

            //Save values if just updating record
            $this->em->flush($eo);
            
            return $eo;
            
        }catch(Exception $e){
            
            throw($e->getMessages());
        }
    }
    
    
    /*
     * 
     */
    public function saveMeanGrade($object){
        
        if(!$object->getPkSmg()){
            $eo = new \Application\Entity\Studentmeangrade();
        }else{
            $eo = $this->em->getRepository("\Application\Entity\Studentmeangrade")->find($object->getPkSmg());
        }
        
        $eo->setFg($object->getFg());
        $eo->setFkGroupid($object->getFkGroupid());
        $eo->setFkStudentid($object->getFkStudentid());
        $eo->setFkPeriodid($object->getFkPeriodid());
        $eo->setGradeComment($object->getGradeComment());
        $eo->setPreviousGradeComment($object->getPreviousGradeComment());
        try{
            
            //Commit values set to the object 
            if(!$object->getPkSmg()){
                $this->em->persist($eo);
            }

            //Save values if just updating record
            $this->em->flush($eo);
            
            return $eo;
            
        }catch(Exception $e){
            throw($e->getMessages());
        }
    }
    
    
    
    
}

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
class Admission extends Commonmodel {

    protected $em;


    public function __construct(\Doctrine\ORM\EntityManager $em) {
        parent::__construct($em);
        $this->em = $em;
    }
    
    public function getSelectionList($params = array()){
        $dq = $this->em->createQueryBuilder()
                       ->select("S,U")
                      ->from('\Application\Entity\Selectionlist','S')
                      ->join('S.fkUploadid', 'U');
                     if(!empty($params)){
                         foreach($params as $field=>$val){
                             $dq->andWhere("{$field}='{$val}'");
                         }
                     }
                     $dq->addOrderBy('U.fkAcademicperiodid','ASC')
                        ->addOrderBy('S.fkClassid','ASC')
                        ->addOrderBy('S.generatedRegnumber','DESC')
                        ->addOrderBy('S.surname','ASC')
                        ->addOrderBy('S.firstname','ASC')
                        ;
                    return $dq->getQuery()->execute();
    }
    
    public function getUnRegisteredSelectionList($params = array()){
        
        $dql = $this->em->createQueryBuilder();
        $dql->select("SP.studentnumber")
            ->from("\Application\Entity\Studentprogram", "SP");
        
        $query   = $this->em->createQueryBuilder();
        $query->select("S,EM")
                   ->from("\Application\Entity\Selectionlist", "S")
                   ->join("S.fkEntrymannerid", "EM")
                   ->where($query->expr()->notIn("S.generatedRegnumber", $dql->getDQL()));
                   
        if(!empty($params)){
            foreach($params as $field=>$value){
              $query->andWhere("{$field}='{$value}'");
            }
        }
        
        $query->addOrderBy('S.fkAcademicperiodid','ASC')
           ->addOrderBy('S.fkClassid','ASC')
           ->addOrderBy('S.generatedRegnumber','DESC')
           ->addOrderBy('S.surname','ASC')
           ->addOrderBy('S.firstname','ASC');
        
        return $query->getQuery()->execute();             
    }
    
    /*
     * Process generates registration numbers, fin account numbers, passwords and usernames for student
     */
    public function generateRegistrationNumbers($post,$cs){
      //Get all records sort by class, by surname, firstname
      $students = $this->getSelectionList(array("U.regnumberAssigned"=>'0',"S.generatedRegnumber"=>""));
      $regSerialNum = $finSerialNum = 0;
      $currentClass = "";
       foreach($students as $student){ 
           
            $year = !empty($student->getFkAcademicperiodid()->getParentid())?$student->getFkAcademicperiodid()->getParentid()->getAcyr():$student->getFkAcademicperiodid()->getAcyr();        
            $yearJoining = explode("/", $year);
            $programmeCode = $student->getFkClassid()->getFkProgramid()->getProgramCode();
                    
            if($currentClass != $student->getFkClassid()->getPkClassid()){
                $regSerialNum = 0;
                $resetSerial  = 1;
            }
            
            foreach($post['Registration']['process_steps'] as $step){
                if($step == 1){
                    if($regSerialNum == 0 && $resetSerial==1){
                        //Check if registration has been generated for the class
                        $currentProcessedRow = $this->getSelectionList(array("S.fkAcademicperiodid"=>$student->getFkAcademicperiodid()->getPkAcademicperiodid(),"S.fkClassid"=>$student->getFkClassid()->getPkClassid(),"S.generatedRegnumber !"=>NULL)); 
                        if(count($currentProcessedRow)>0){
                            $regNumberPart = explode("/",$currentProcessedRow[0]->getGeneratedRegnumber());
                            $regSerialNum = $regNumberPart[3]+1;
                            $finSerialNum = ($finSerialNum==0)?$currentProcessedRow[0]->getGeneratedFinAccountno()+1:$finSerialNum++;
                        }else{
                            $regSerialNum++;
                            $finSerialNum++;
                        }
                    }else{
                        $regSerialNum++;
                        $finSerialNum++;
                    }
                    
                    //Generation of reg #
                    $registrationNumber = sprintf("%s/%s/%s/%s",
                                                   $programmeCode,  substr($yearJoining[0], -2),$student->getFkEntrymannerid()->getEntryCode(),str_pad($regSerialNum, 3, "0", STR_PAD_LEFT));
                     
                     //Generate accountNo
                     $accountNo = sprintf("06%s%s",
                                           $yearJoining[0],str_pad($finSerialNum, 4, "0", STR_PAD_LEFT));
                }elseif($step == 2){
                    //Generate  temporaly username...
                    $tmpUsername = strtolower($programmeCode.substr($yearJoining[0],-2)."-".sprintf("%s%s%s", substr($student->getFirstname(),0,1),substr($student->getMiddlename(), 0,1),$student->getSurname()));
                   
                    $count = 1;
                    $originaltmpUsername = $tmpUsername;
                    while($cs->isUserAccountInUse($tmpUsername,  $this->em)){                                  
                        $tmpUsername = $originaltmpUsername . $count;
                        $count ++;
                    }
                    //Genarate temporary password
                    $tmpPassword = $cs->tempPassword(); 
                }
            }
            $resetSerial = 0; 
            //Update numbers
            $student->setGeneratedUsername($tmpUsername);
            $student->setGeneratedPassword($tmpPassword);
            $student->setGeneratedRegnumber($registrationNumber);
            $student->setGeneratedFinAccountno($accountNo);
            $this->saveSelectionList($student);
          
            $currentClass = $student->getFkClassid()->getPkClassid();
            $upload = $student->getFkUploadid();
            $upload->setRegnumberAssigned('1');
            $this->saveUploadList($upload);
       }
       
       
       
    }

    public function getRegistrationStatus($params = array(),$groupby="F.facultyCode"){
        
        $dq = $this->em->createQueryBuilder()
                       ->select(" F.facultyCode as label,sum((CASE 
                                     WHEN (S.status='1') THEN 1 
                                     ELSE 0
                                 END) AS registered,
                                 sum((CASE 
                                     WHEN (S.status='0') THEN 1 
                                     ELSE 0
                                 END) AS pending ")
                      ->from('\Application\Entity\Studentclass','S')
                      ->join('S.fkStudentprogramid', 'SP')
                      ->join('SP.fkProgramid', 'P')
                      ->join('P.fkDeptid', 'D')
                      ->join('D.fkFacultyid', 'F');
                     if(!empty($params)){
                         foreach($params as $field=>$val){
                             $dq->andWhere("{$field}='{$val}'");
                         }
                     }
                     //$dq->setParameter("code", $groupby);
                     $dq->groupBy($groupby);
                    return $dq->getQuery()->execute();
    }
    
    /*
     * 
     */
    public function saveUploadList($object){
        
        if(!$object->getPkUploadid()){
            $eo = new \Application\Entity\Selectionlistupload();
        }else{
            $eo = $this->em->getRepository("\Application\Entity\Selectionlistupload")->find($object->getPkUploadid());
        }
        
        $eo->setDateUploaded($object->getDateUploaded());
        $eo->setFileContents($object->getFileContents());
        $eo->setUploadedBy($object->getUploadedBy());
        $eo->setFileName($object->getFileName());
        $eo->setFkAcademicperiodid($object->getFkAcademicperiodid());
        $eo->setRegnumberAssigned($object->getRegnumberAssigned());
        $eo->setSaved($object->getSaved());
      
        try{
            //Commit values set to the object 
            if(!$object->getPkUploadid()){
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
    public function saveSelectionList($object){
        
        if(!$object->getPkSlid()){
            $eo = new \Application\Entity\Selectionlist();
        }else{
            $eo = $this->em->getRepository("\Application\Entity\Selectionlist")->find($object->getPkSlid());
        }
        
        $eo->setCenterNumber($object->getCenterNumber());
        $eo->setFirstname($object->getFirstname());
        $eo->setFkAcademicperiodid($object->getFkAcademicperiodid());
        $eo->setFkCampusid($object->getFkCampusid());
        $eo->setFkClassid($object->getFkClassid());
        $eo->setFkCountryid($object->getFkCountryid());
        $eo->setFkEntrymannerid($object->getFkEntrymannerid());
        $eo->setFkUploadid($object->getFkUploadid());
        $eo->setGender($object->getGender());
        $eo->setGeneratedFinAccountno($object->getGeneratedFinAccountno());
        $eo->setGeneratedPassword($object->getGeneratedPassword());
        $eo->setGeneratedRegnumber($object->getGeneratedRegnumber());
        $eo->setGeneratedUsername($object->getGeneratedUsername());
        $eo->setStudentNumber($object->getStudentNumber());
        $eo->setMiddlename($object->getMiddlename());
        $eo->setSurname($object->getSurname());
      
        try{
            //Commit values set to the object 
            if(!$object->getPkSlid()){
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
     * Get enrolled student list
     */
    public function getEnrolledList($post){

        $itemsperpage = !empty($post['itemcount'])?$post['itemcount']:50;
        $currentdate = new \DateTime();

        //Get current academic periods
        $criteriap = Criteria::create()
                    ->where(Criteria::expr()->lte('startDate',$currentdate))
                    ->andWhere(Criteria::expr()->gte('endDate',$currentdate))
                    ->orderBy(array("startDate"=> Criteria::ASC));
        $academicperiods = $this->getEntity("\Application\Entity\Academicperiod",$criteriap);
        
        //Get classes or groups
        $condition = Criteria::create()
                    ->orderBy(array("groupName"=> Criteria::ASC));
        $classes = $this->getEntity("\Application\Entity\Programgroup",$condition);
        
        //Set criteria for student enrollment
        $criteria = Criteria::create()
                    ->orderBy(array("surname"=> Criteria::ASC,"firstname"=>  Criteria::ASC));            
        if($post['search']){
            //Get search parameter values
            
            if(!empty($post['period'])){
                
                $period = $this->em->getRepository('\Application\Entity\Academicperiod')->find($post['period']);
                $criteria->where(Criteria::expr()->eq('fkPeriodid', $period));
            }
            if(!empty($post['class'])){
                $class = $this->em->getRepository('\Application\Entity\Programgroup')->find($post['class']);
                $criteria->andWhere(Criteria::expr()->eq('fkGroupid', $class));
            }
        }
        
        $students = $this->getEntity("\Application\Entity\Enrollment",$criteria); 
        
        $paginator = new Paginator(
                     new Adapter($students)
                );
        
        $paginator->setCurrentPageNumber(1)
                  ->setItemCountPerPage($itemsperpage);
        
        
        return  array("enrolledlist"=>$paginator,"academicperiod"=>$academicperiods,"classes"=>$classes);
        
    }
    
    public function enrollmentUploadValidator($rowarray){
        
       //Set validation chain
       $validationchain = new \Zend\Validator\ValidatorChain();
         //Attach not empty validation  
        $emptyValidator = new \Zend\Validator\NotEmpty();
        $emptyValidator->setMessage("Required");
        $validationchain->attach($emptyValidator);
        
        if($rowarray['status'] == 'email'){ 
            
            $emailValidator          = new \Zend\Validator\EmailAddress();
            
            $emailmsg = array(\Zend\Validator\EmailAddress::INVALID  => "Invalid email",
                                               \Zend\Validator\EmailAddress::INVALID_FORMAT  => "Invalid email");
            $emailValidator->setMessages($emailmsg);
            
           //Attach email validation  
            $validationchain->attach($emailValidator);
        }    
            return $validationchain;
    }
    
    /*
     * Populate enrolled student array
     */
    public function getEnrolledStudentsArray($rowdata){
        $error = $class = array();

        $rowarray = array("surname"=>array("val"=>$rowdata[0],"status"=>"required"),
                          "firstname"=>array("val"=>$rowdata[1],"status"=>"required"),
                          "gender"=>array("val"=>$rowdata[3],"status"=>"required"),
                          "class"=>array("val"=>$rowdata[4],"status"=>"required"),
                          "period"=>array("val"=>$rowdata[5],"status"=>"required"),
                          "emailaddress"=>array("val"=>$rowdata[6],"status"=>"email"),
                          "entrymanner"=>array("val"=>$rowdata[7],"status"=>"required"),
                          "studymode"=>array("val"=>$rowdata[8],"status"=>"required"));
        foreach($rowarray as $name=>$row){
            $vchain = $this->enrollmentUploadValidator($row);
            if(!$vchain->isValid($row['val'])){
                $error[$name] = implode(" ", $vchain->getMessages());
            }
        }

        //Get group or class entity object
        $class                   = $this->em->getRepository("\Application\Entity\Programgroup")->findOneBy(array('groupCode'=>$rowdata[4])); //    $this->getEntity("\Application\Entity\Programgroup",$criteria); 
        
        
        $group                   = $class->getPkGroupid();
        $groupname               = $class->getGroupcode();

        
        //Get entry manner object
        $entrymannercriteria     = Criteria::create()
                                    ->where(Criteria::expr()->eq('entrycode', $rowdata[8]));
        $entry                   = $this->getEntity("\Application\Entity\Entrymanner",$entrymannercriteria);
        
        //Get study mode
        $mode                   = $this->em->getRepository("\Application\Entity\Studymode")->find($rowdata[7]);
        
        
        //Check if email address already exist in enrolled table            
        $emailcriteria           = Criteria::create()
                                   ->where(Criteria::expr()->eq('emailaddress', $rowdata[6]));
        $emailrows               = $this->getEntity("\Application\Entity\Enrollment",$emailcriteria); 
        if(count($emailrows)>0){
              $error['emailaddress'] = "Email address already exists";
        }
        
        //Check if student number already exists in the system
        $enrollment             = $this->em->getRepository("\Application\Entity\Enrollment")->findBy(array("tempstudentno"=>$rowdata[9]));
        if(count($enrollment)>0){
              $error['studentno'] = "Student number already exists";
        }
       
        $studentdata =  array("surname"=>$rowdata[0],"firstname"=>$rowdata[1],"initial"=>$rowdata[2],"gender"=>$rowdata[3],"fkGroupid"=>$group,"groupname"=>$groupname,"fkPeriodid"=>$rowdata[5],"emailaddress"=>$rowdata[6],"fkEntrymannerid"=>$entry[0]->getPkEntrymannerid(),"entrymanner"=>$entry[0]->getEntryName(),"fkStudymodeid"=>$mode->getPkStudymodeid(),"studymodetitle"=>$mode->getTitle(),"studentno"=>$rowdata[9]);

        return array("studentdata"=>$studentdata,"error"=>$error);
    }

    /*
    * Save user information
    */
    public function enrollstudent($studentobject){
        if(!$studentobject->getPkEnrollmentid()){
            $student = new \Application\Entity\Enrollment();
        }else{
            $criteria = Criteria::create()
                        ->where(Criteria::expr()->eq("pkEnrollmentid", $studentobject->getPkEnrollmentid()));
            //$student = $this->getEntity('\Application\Entity\Enrollment',$criteria)->find(array("pkEnrollmentid"=>$studentobject->getPkEnrollmentid()));
            $student = $this->getEntity('\Application\Entity\Enrollment',$criteria);
        }
        
        //Set user object values to be saved
        $student->setEmailaddress($studentobject->getEmailaddress());
        $student->setFirstname($studentobject->getFirstname());
        $student->setSurname($studentobject->getSurname());
        $student->setFkEntrymannerid($studentobject->getFkEntrymannerid());
        $student->setFkGroupid($studentobject->getFkGroupid());
        $student->setFkPeriodid($studentobject->getFkPeriodid());
        $student->setFkStudymode($studentobject->getFkStudymode());
        $student->setInitial($studentobject->getInitial());
        $student->setTempstudentno($studentobject->getTempstudentno());
        $student->setYearjoined($studentobject->getYearjoined());
        $student->setTemppwd($studentobject->getTemppwd());
        $student->setGender($studentobject->getGender());
  
        //Commit values set to the object 
        if(!$studentobject->getPkEnrollmentid()){
            $this->em->persist($student);
        }
        
        //Save values if just updating record
        $this->em->flush($student);
    }
    
    /*
     * Sets enrollment object values
     */
    public function setEnrollmentObject($arrayval){
        
        //Get class/group detail
        $class    = $this->em->getRepository('\Application\Entity\Programgroup')->find($arrayval['fkGroupid']);
        //Get entry manner
        $entry    = $this->em->getRepository('\Application\Entity\Entrymanner')->find($arrayval['fkEntrymannerid']);
        //Get study mode
        $mode     = $this->em->getRepository('\Application\Entity\Studymode')->find($arrayval['fkStudymodeid']);
        //Get academic period
        $period     = $this->em->getRepository('\Application\Entity\Academicperiod')->find($arrayval['fkPeriodid']);
        
        //Set parameters
        $studentobject = new \Application\Entity\Enrollment();
        $studentobject->setFkGroupid($class);
        $studentobject->setFkEntrymannerid($entry);
        $studentobject->setFkStudymode($mode);
        $studentobject->setFkPeriodid($period);
        $studentobject->setSurname($arrayval['surname']);
        $studentobject->setFirstname($arrayval['firstname']);
        $studentobject->setGender($arrayval['gender']);
        $studentobject->setTempstudentno($arrayval['studentno']);
        $studentobject->setInitial($arrayval['initial']);
        $studentobject->setYearjoined(new \DateTime());
        $studentobject->setEmailaddress($arrayval['emailaddress']);
        $studentobject->setTemppwd('testing');
        
        return $studentobject;
    }
    
    /*
     * Save student
     */
    public function autoregisterstudent($enrollmentid){
        
        //Search for enrollmentid
        $criteria = Criteria::create()
                        ->where(Criteria::expr()->eq('pkEnrollmentid', $enrollmentid));
        $enrollmentobject = $this->getEntity("\Application\Entity\Enrollment", $criteria);

        //Search for student role
        $rolecriteria = Criteria::create()
                        ->where(Criteria::expr()->eq('roleName', 'STUD'));
        $roleobject = $this->getEntity("\Application\Entity\Role", $rolecriteria);
        
        //Search for a country
        $countrycriteria = Criteria::create()
                       ->where(Criteria::expr()->eq('countryCode', 'MWI'));
        $countryobject = $this->getEntity("\Application\Entity\Country", $countrycriteria);
        
        //Search entry manner
        $campuscriteria = Criteria::create()
                      ->where(Criteria::expr()->eq('campusName', 'Blantyre'));
        $campusobject = $this->getEntity("\Application\Entity\Campus", $campuscriteria);
        
        //District entity
        $district     = $this->em->getRepository('\Application\Entity\District')->find(1);
          
        $date = new \DateTime();
        
        //Set all array values for the object
        $student                =  new \Application\Model\Student($this->em);
        
        //Assign user table values
        $userdata               =  array("username"=>$enrollmentobject[0]->getEmailaddress(),"title"=>"","role"=>$roleobject[0],"surname"=>$enrollmentobject[0]->getSurname(),"firstname"=>$enrollmentobject[0]->getFirstname(),"initial"=>$enrollmentobject[0]->getInitial(),"gender"=>$enrollmentobject[0]->getGender(),"password"=>"test2","url"=>"","emailaddress"=>$enrollmentobject[0]->getEmailaddress(),"ipaddress"=>"","logindate"=>$date,"datecreated"=>$date);
        $userobject             =  $student->setUserObject($userdata);
        
        //Set  student object
        $studentdata            =  array("dob"=>$date,"country"=>$countryobject[0],"maritalstatus"=>"Single","district"=>$district);
        //Set student program object
        $studentprogramdata     =  array("entrymanner"=>$enrollmentobject[0]->getFkEntrymannerid(),"entryyear"=>$date,"program"=>$enrollmentobject[0]->getFkGroupid()->getFkProgramid(),"repeatinglevel"=>'1',"registrationnumber"=>$enrollmentobject[0]->getTempstudentno());
        
        //Set student class object
        $studentclassdata       =  array("examnumber"=>null,"campus"=>$campusobject[0],"class"=>$enrollmentobject[0]->getFkGroupid(),"period"=>$enrollmentobject[0]->getFkPeriodid(),"isregistered"=>"1","studymode"=>$enrollmentobject[0]->getFkStudymode(),"registrationdate"=>$date);
        
        $allocateobjects        = array("user"=>$userobject,"student"=>$studentdata,"program"=>$studentprogramdata,"class"=>$studentclassdata);
        
        $student->allocate($allocateobjects); 
        
        //Delete in enrollment record for the student
        $student->deletefromdb('\Application\Entity\Enrollment',$enrollmentid);
    }
    
    
     /*
     * Scholarship statistics report
     */
    public function getScholarshipReport($criteria=null){
        
        $query = $this->em->createQueryBuilder()
                           ->select(" SR.pkSponsorid as SID,SR.sponsorName as SNAME, SR.currentStatus as SSTATUS,
                                     SUM(CASE WHEN (U.gender ='F') THEN 1 
                                          ELSE 0
                                          END) AS F,
                                     SUM(CASE 
                                     WHEN (U.gender ='M') THEN 1 
                                        ELSE 0
                                     END) AS M
                                     
                                     ")
                      ->from('\Application\Entity\Sponsoredstudent','SS')
                      ->join('SS.fkSponsorid', 'SR')
                      ->join('SS.fkStudentprogramid', 'SP')
                      ->join('SP.fkStudentid', 'S')
                      ->join('S.fkUserid', 'U');
        if(!empty($criteria)){
            foreach($criteria as $field=>$value){
              $query->andWhere("{$field}='{$value}'");
            }
        }
        $query->orderBy("SR.sponsorName","ASC");
        $query->groupBy("SR.pkSponsorid");
        
        return $query->getQuery()->execute();
    }
    
    
    /*
     * Enrollment report
     */
    public function getEnrollmentReport($criteria=null,$groupBy=null){
        
        $query = $this->em->createQueryBuilder()
                           ->select(" F.facultyCode as FCODE,D.deptCode as DCODE,P.programCode as PCODE,C.classCode as CCODE,C.classYear as CYEAR,
                                     SUM((CASE 
                                     WHEN (U.gender ='F' AND SC.status='1' AND SC.fkDropoutstatusid IS NULL) THEN 1 
                                        ELSE 0
                                     END) AS RF,
                                     SUM((CASE 
                                     WHEN (U.gender ='M' AND SC.status='1' AND SC.fkDropoutstatusid IS NULL) THEN 1 
                                        ELSE 0
                                     END) AS RM,
                                     SUM((CASE 
                                     WHEN (U.gender ='F' AND SC.status='0' AND SC.fkDropoutstatusid IS NULL) THEN 1 
                                        ELSE 0
                                     END) AS NF,
                                     SUM((CASE 
                                     WHEN (U.gender ='M' AND SC.status='0' AND SC.fkDropoutstatusid IS NULL) THEN 1 
                                        ELSE 0
                                     END) AS NM,
                                     SUM((CASE 
                                     WHEN (U.gender ='F' AND SC.fkDropoutstatusid IS NOT NULL) THEN 1 
                                        ELSE 0
                                     END) AS DF,
                                     SUM((CASE 
                                     WHEN (U.gender ='M' AND SC.fkDropoutstatusid IS NOT NULL) THEN 1 
                                        ELSE 0
                                     END) AS DM,
                                     SUM((CASE 
                                     WHEN (U.gender ='F') THEN 1 
                                        ELSE 0
                                     END) AS EF,
                                     SUM((CASE 
                                     WHEN (U.gender ='M') THEN 1 
                                        ELSE 0
                                     END) AS EM
                                     ")
                      ->from('\Application\Entity\Studentclass','SC')
                      ->join('SC.fkClassid', 'C')
                      ->join('C.fkProgramid', 'P')
                      ->join('P.fkDeptid', 'D')
                      ->join('D.fkFacultyid', 'F')
                      ->join('SC.fkStudentid', 'ST')
                      ->join('SC.fkStudentprogramid', 'SP')
                      ->join('ST.fkUserid', 'U');
        if(!empty($criteria)){
            foreach($criteria as $field=>$value){
              $query->andWhere("{$field}='{$value}'");
            }
        }
        $query->orderBy($groupBy,"ASC");
        $query->groupBy($groupBy);
        
        return $query->getQuery()->execute();
    }
    
    /*
     * Enrollment status for new students
     */
    public function getSelectionListStatus($criteria=null,$groupBy=null){
       
        $query = $this->em->createQueryBuilder();
        $dql   = $this->em->createQueryBuilder();
        
        $dql->select("SP.studentnumber")
                   ->from("\Application\Entity\Studentprogram", "SP");
       
        $query->select("F.facultyCode as FCODE,D.deptCode as DCODE,P.programCode as PCODE,C.classCode as CCODE,C.classYear as CYEAR,
                  SUM((CASE 
                  WHEN (SC.gender ='F' AND SC.fkDropoutstatus IS NULL) THEN 1 
                     ELSE 0
                  END) AS NF,
                  SUM((CASE 
                  WHEN (SC.gender ='M' AND SC.fkDropoutstatus IS NULL) THEN 1 
                     ELSE 0
                  END) AS NM,
                  SUM((CASE 
                  WHEN (SC.gender ='F' AND SC.fkDropoutstatus IS NOT NULL) THEN 1 
                     ELSE 0
                  END) AS DF,
                  SUM((CASE 
                  WHEN (SC.gender ='M' AND SC.fkDropoutstatus IS NOT NULL) THEN 1 
                     ELSE 0
                  END) AS DM,
                  SUM((CASE 
                  WHEN (SC.gender ='F') THEN 1 
                     ELSE 0
                  END) AS EF,
                  SUM((CASE 
                  WHEN (SC.gender ='M') THEN 1 
                     ELSE 0
                  END) AS EM
                  ")
               ->from('\Application\Entity\Selectionlist','SC')
               ->join('SC.fkClassid', 'C')
               ->join('C.fkProgramid', 'P')
               ->join('P.fkDeptid', 'D')
               ->join('D.fkFacultyid', 'F')
               ->where($query->expr()->notIn("SC.generatedRegnumber", $dql->getDQL()));
        if(!empty($criteria)){
            foreach($criteria as $field=>$value){
              $query->andWhere("{$field}='{$value}'");
            }
        }
        
        $query->orderBy($groupBy,"ASC");
        $query->groupBy($groupBy);
        
        return $query->getQuery()->execute();
    }
    
    public function getGeneralEnrollmentReport($classlist,$selectionlist,$reporttype){
        
        
        foreach($classlist as $key=>$class){
            foreach($selectionlist as $selection){

                if($reporttype == 1){
                    $code = $class['CYEAR'];
                }elseif($reporttype == 2){
                    $code = $class['CCODE'];
                }elseif($reporttype == 3){
                    $code = $class['PCODE'];
                }elseif($reporttype == 4){
                   $code = $class['DCODE'];
                }elseif($reporttype == 5){
                    $code = $class['FCODE'];
                }

                if($reporttype != 1){
                    if(in_array($code,$selection)){

                        $classlist[$key]['NF'] = $class['NF'] + $selection['NF'];
                        $classlist[$key]['NM'] = $class['NM'] + $selection['NM'];
                        $classlist[$key]['DF'] = $class['DF'] + $selection['DF'];
                        $classlist[$key]['DM'] = $class['DM'] + $selection['DM'];
                        $classlist[$key]['EF'] = $class['EF'] + $selection['EF'];
                        $classlist[$key]['EM'] = $class['EM'] + $selection['EM'];
                        break;
                    }
                }else{
                        if($code == $selection['CYEAR']){

                        $classlist[$key]['NF'] = $class['NF'] + $selection['NF'];
                        $classlist[$key]['NM'] = $class['NM'] + $selection['NM'];
                        $classlist[$key]['DF'] = $class['DF'] + $selection['DF'];
                        $classlist[$key]['DM'] = $class['DM'] + $selection['DM'];
                        $classlist[$key]['EF'] = $class['EF'] + $selection['EF'];
                        $classlist[$key]['EM'] = $class['EM'] + $selection['EM'];
                        break;
                    }
                }
            
            }
        }
        return $classlist;
    } 
    
    
    
    
    
    
    
}

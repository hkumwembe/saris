<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Doctrine\Common\Collections\Criteria;
use Zend\Session\Container;
use Zend\Authentication\AuthenticationService;
use Zend\View\Model\JsonModel;
use Zend\Soap\Client;

class AdmissionController extends AbstractActionController
{
     protected $em;
     protected $am;
     protected $cs;
     protected $request;
     protected $admission;
     protected $userid;
     protected $session;

     public function __construct(\Doctrine\ORM\EntityManager $em,\Application\Model\Admission $am,  \Application\Service\Security $cs) {
        $this->session = new Container('ADMISSION');
        $this->em               = $em;
        $this->am               = $am;
        $this->cs               = $cs;
        $this->request          = $this->getRequest();
        $this->admission        = new \Application\Model\Admission($this->em); 
    }
    
    public function onDispatch(\Zend\Mvc\MvcEvent $e) {
        
        $this->authservice = new AuthenticationService();
        if(!$this->authservice->hasIdentity()){
            $this->redirect()->toRoute("login",array('action'=>'index'));
        }
        
        $identity           = $this->authservice->getIdentity();
        
        $this->userid       = $identity['pkUserid'];
        
        $this->layout()->setVariables(array("activemodule"=>$this->getEvent()->getRouteMatch()->getMatchedRouteName()));
        parent::onDispatch($e);
    }
    
    public function indexAction()
    {
        $chart  = new \Application\Model\Charts($this->em);
        return new ViewModel(array("pie"=>$chart->getEnrolledStatus(),"bar"=>$chart->getRegistrationStatus()));
    }
    
     public function selectionlistAction()
    {
        $successMsg = "";
        $flashMessenger = $this->flashMessenger();
        if($flashMessenger->hasSuccessMessages()){
            $successMsg = implode("<br>", $flashMessenger->getSuccessMessages());
        }
        
        return new ViewModel(array("msg"=>$successMsg));
    }
    
    public function uploadselectionlistAction()
    {
        $formfile = new \Application\Form\Selectionupload();
        
        if($this->request->getPost('btnupload')){
            $post = array_merge_recursive(
                            $this->request->getPost()->toArray(),
                            $this->request->getFiles()->toArray()
                    );
            $formfile->setData($post);
            if($formfile->isValid()){
                
                $validator = new \Zend\Validator\File\Extension('csv');
                if($validator->isValid($post['File']['filename'])){
                    
                    $uploadfile      = $formfile->getData();
                    $filecontent     = file_get_contents($uploadfile['File']['filename']['tmp_name']);
                    
                    //Get logged in user's object
                    $user = $this->em->getRepository("\Application\Entity\User")->find($this->userid);
                    //Get current period
                    $period  = $this->admission->getCurrentPeriod();
                    
                    $uploadObject    = new \Application\Entity\Selectionlistupload();
                    $uploadObject->setFileContents($filecontent);
                    $uploadObject->setDateUploaded(new \DateTime());
                    $uploadObject->setFileName($uploadfile['File']['filename']['name']);
                    $uploadObject->setFkAcademicperiodid($period[0]);
                    $uploadObject->setUploadedBy($user);
                    
                    $upload = $this->admission->saveUploadList($uploadObject);
                    //Redirect to upload list preview
                    return $this->redirect()->toRoute("admission",array("action"=>"ulp","id"=>$upload->getPkUploadid()));
                }
            } 
        }
        
        return new ViewModel(array("uploadform"=>$formfile));
    }
    
    public function srfAction(){
        $form = new \Application\Form\Registration($this->em);
        $form->bind($this->request->getPost());
        
        return new ViewModel(array("form"=>$form));
    }

    public function grnAction()
    {
        $formfile = new \Application\Form\Regno($this->em);
        if($this->request->getPost('btngenerate')){
            $this->admission->generateRegistrationNumbers($this->request->getPost(),$this->cs);
            $this->flashMessenger()->addSuccessMessage("Students' numbers successfully generated");
            $this->redirect()->toRoute("admission",array("action"=>"selectionlist"));
        }
        
//        return new JsonModel([
//            'code' => "success"
//        ]);
        return new ViewModel(array("form"=>$formfile));
    }
    
    public function slAction(){
        $students = $this->em->getRepository("\Application\Entity\Studentprogram")->findAll();
        return new ViewModel(array("students"=>$students));
    }
    
    /*
     * Uploaded list priview
     */
    public function ulpAction(){
        $uploadid = $this->getEvent()->getRouteMatch()->getParam('id');
        $uploadedlist = $this->em->getRepository("\Application\Entity\Selectionlistupload")->find($uploadid);
        return new ViewModel(array("list"=>$uploadedlist));
    }
    
    
    /*
     * Save selection upload
     */
    public function ssuAction(){
        $uploadid = $this->getEvent()->getRouteMatch()->getParam('id');
        $list = $this->em->getRepository("\Application\Entity\Selectionlistupload")->find($uploadid);
        
        foreach(explode("\n",$list->getFileContents()) as $record){
            $column = explode(",",$record);
            $object = new \Application\Entity\Selectionlist();
            //Validate campus
            $campus = $this->em->getRepository("\Application\Entity\Campus")->findOneBy(array("campusName"=>$column[9]));
            //Validate country
            $country = $this->em->getRepository("\Application\Entity\Country")->find($column[8]);
            //Validate class
            $class = $this->em->getRepository("\Application\Entity\Classes")->findOneBy(array("classCode"=>$column[5]));
            //Validate entry manner
            $entrymanner = $this->em->getRepository("\Application\Entity\Entrymanner")->findOneBy(array("entryCode"=>$column[4]));
           
             
            $object->setCenterNumber($column[6]);
            $object->setFirstname($column[0]);
            $object->setFkAcademicperiodid($list->getFkAcademicperiodid());
            $object->setFkCampusid($campus);
            $object->setFkClassid($class);
            $object->setFkCountryid($country);
            $object->setFkEntrymannerid($entrymanner);
            $object->setFkUploadid($list);
            $object->setGender($column[3]);
            $object->setStudentNumber($column[7]);
            $object->setMiddlename($column[2]);
            $object->setSurname($column[1]);
            
            $this->admission->saveSelectionList($object);
        }
        
        //Set upload record as saved
        $list->setSaved('1');
        $this->admission->saveUploadList($list);
        $this->flashMessenger()->addSuccessMessage("Selection list successfully uploaded");
        return $this->redirect()->toRoute("admission",array("action"=>"selectionlist"));
    }
    
    
    /*
     * Discard upload
     */
    public function duAction(){
        $uploadid = $this->request->getPost('uploadid');
        $this->admission->deletefromdb("\Application\Entity\Selectionlistupload",$uploadid);
        echo "Successful";
        die();
    }
    
    /*
     * View selection list
     */
    public function vslAction(){
        
        $list = $this->admission->getSelectionList();
        return new ViewModel(array("students"=>$list));
    }
    
    /*
     * 
     */
    public function spAction(){
        $id             = $this->getEvent()->getRouteMatch()->getParam("id");
        $transactions   = array();
        $studentprogram = $this->em->getRepository("\Application\Entity\Studentprogram")->findOneBy(array("fkStudentid"=>$id));
        $regNo = $studentprogram->getStudentnumber();
//        $options = array(
//            'location' => 'http://saris.medcol.mw/pages/exams/mainServer.php',
//            'uri'      => 'http://saris.medcol.mw/pages/exams/'
//        );
//        
//        $client = new Client(null,$options);
//        $transactions = $client->getAccountStatement($regNo,array(array('[COMDAT].[dbo].[AROBL].IDCUST'=>$regNo)));
        //Get academic status
        $status         = $this->em->getRepository("\Application\Entity\Studentclass")->findOneBy(array("fkStudentprogramid"=>$studentprogram->getPkStudentprogramid()));
        return new ViewModel(array("student"=>$studentprogram,"status"=>$status,"fintrans"=>$transactions));
    }
    
    /*
     * Auto register students
     */
    function autoregisterAction(){ 
        $students = $this->request->getPost('chkStudentid');
        
        foreach($students as $enrollmentid){
            //Get student enrolled details
            $this->admission->autoregisterstudent($enrollmentid);
        }
        
        $this->redirect()->toRoute("admission",array("action"=>"enrollment"));
    }


    //Emrollment 
    function enrollmentAction(){  
        return new ViewModel($this->admission->getEnrolledList($this->request->getPost()));
    }
    
    function groupenrollmentAction(){
        $criteria = Criteria::create();
        
        $formfile = new \Application\Form\Groupenrollment();
        
        //Get entry manner
        $entrymanners = $this->admission->getEntity("\Application\Entity\Entrymanner",$criteria);
        //Get Study mode
        $studymodes   = $this->admission->getEntity("\Application\Entity\Studymode",$criteria);
        //Academic period
        $acperiod     = $this->admission->getEntity("\Application\Entity\Academicperiod",$criteria);
        //Class
        $classes     = $this->admission->getEntity("\Application\Entity\Programgroup",$criteria);
        
        if($this->request->getPost('btnupload')){
            $post = array_merge_recursive(
                            $this->request->getPost()->toArray(),
                            $this->request->getFiles()->toArray()
                    );
            $formfile->setData($post);
            if($formfile->isValid()){
                
                $validator = new \Zend\Validator\File\Extension('csv');
                if($validator->isValid($post['File']['filename'])){
                    
                    $uploadfile      = $formfile->getData();
                    $filecontent     = $uploadfile['File']['filename']['tmp_name'];
                    $handle          = fopen($filecontent, "r");

                    while (($rowdata = fgetcsv($handle, 1000, ",")) !== FALSE){
                        //Process validation class/group, academic period, entry manner, study mode
                        $student[]   = $this->admission->getEnrolledStudentsArray($rowdata);
                    }
                    
                    $this->session->enrolmentlist = $student;
                    //$this->session->getManager()->getStorage()->clear('ADMISSION');
                    return $this->redirect()->toRoute("admission",array("action"=>"confirmgroupenrollment"));
                }
            } 
        }
        
        
        return new ViewModel(array("classes"=>$classes,"academicperiod"=>$acperiod,"entrymanners"=>$entrymanners,"studymodes"=>$studymodes,"uploadform"=>$formfile));
    }
    
    
    function confirmgroupenrollmentAction(){
        $list = $this->session->enrolmentlist;
        return new ViewModel(array("list"=>$list));
    }
    
    function saveenrolledlistAction(){
        $list = $this->session->enrolmentlist;
        foreach ($list as $student){
           //Check errors
           if(empty($student['error'])){
               //Set enrollment object
                $studentobject = $this->admission->setEnrollmentObject($student['studentdata']);
                //Save there are no errors against record
                $this->admission->enrollstudent($studentobject);
           }
        }
         $this->session->getManager()->getStorage()->clear('ADMISSION');
         //Redirect to enrollment page
         $this->flashMessenger()->addSuccessMessage("Students successfully enrolled");
         return $this->redirect()->toRoute("admission",array("action"=>"enrollment"));
    }
    
    function individualenrollmentAction(){
        $formenrollment = new \Application\Form\Enrollment($this->em);
        
        //Bind 
        $formenrollment->bind($this->request->getPost());
        //Check if form has been submitted
        if($this->request->getPost('submit')){
            
            $formenrollment->setData($this->request->getPost());
            //Check if form validation is okay
            if($formenrollment->isValid()){
             
                $formdata = $formenrollment->getData();
                $basic = $formdata['Enrollment']['basicdetails'];
                $enrollment = $formdata['Enrollment'];
                
                $enrollment['surname'] = $basic['surname'];
                $enrollment['firstname'] = $basic['firstname'];
                $enrollment['gender'] = $basic['gender'];
                $enrollment['emailaddress'] = $basic['emailaddress'];
                $enrollment['initial'] = $basic['initial'];

                //Set enrollment object
                $studentobject = $this->admission->setEnrollmentObject($enrollment);
                
                //Save student
                $this->admission->enrollstudent($studentobject);
                
                //Redirect to enrollment page
                $this->flashMessenger()->addSuccessMessage("Student successfully enrolled");
                return $this->redirect()->toRoute("admission",array("action"=>"enrollment"));
            }

        }
        
        return new ViewModel(array("formenrollment"=>$formenrollment));
    }
    //End of enrollment 
    
    function classlistAction(){
          
        $searchform = new \Application\Form\ClasslistSearch($this->em);
        $students   = array();
        $class      = $period = "";
        $unregisteredlist = $unregisteredselectionlist = array();
        if($this->request->getPost('class') && $this->request->getPost('academicyear')){
//            
            $semester           = $this->request->getPost('semester');
            $year               = $this->request->getPost('academicyear');
            $classid            = $this->request->getPost('class');
            
            //Get class object
            $class              = $this->em->getRepository("\Application\Entity\Classes")->find($classid);
            //Determine which level to check (Post graduate or undergraduate)
            if($semester){
                $period             = $this->em->getRepository("\Application\Entity\Academicyear")->findOneBy(array("parentid"=>$year,"acyr"=>$semester));
            }else{
                $period             = $this->em->getRepository("\Application\Entity\Academicyear")->find(array("pkAcademicperiodid"=>$year));
            }
            
            $students           = $this->admission->getClasslist(array("A.pkAcademicperiodid"=>$period->getPkAcademicperiodid(),"C.pkClassid"=>$classid,"SC.status"=>'1')); //$this->em->getRepository("\Application\Entity\Studentclass")->findBy(array("fkAcademicperiodid"=>$periodid,"fkClassid"=>$classid,"status"=>'1'));

            $count = 0;
            $data = array();
            foreach($students as $student){
                $count++;
                $regDate = !empty($student->getRegistrationdate())?date_format($student->getRegistrationdate(),"d M Y"):"N/A";
                $data[] = array($count,$student->getFkStudentprogramid()->getStudentnumber(),$student->getFkStudentid()->getFkUserid()->getSurname(),$student->getFkStudentid()->getFkUserid()->getFirstname(),$student->getFkStudentid()->getFkUserid()->getGender(),$student->getFkStudentprogramid()->getFkEntrymannerid()->getEntryName(),$regDate); 
            }
           
            $this->session->students      = $data;
            $unregisteredlist             = $this->admission->getClasslist(array("A.pkAcademicperiodid"=>$period->getPkAcademicperiodid(),"C.pkClassid"=>$classid,"SC.status"=>'0')); 
            //Get selected students that are not registered
            $unregisteredselectionlist    = $this->admission->getUnRegisteredSelectionList(array("S.fkAcademicperiodid"=>$period->getPkAcademicperiodid(),"S.fkClassid"=>$classid));
            $pcount = 0;
            $pendingdata = array();
            foreach($unregisteredlist as $ustudent){
                $pcount++;
                //$pendingdata[] = array($pcount,$ustudent->getGeneratedRegnumber(),$ustudent->getSurname(),$ustudent->getFirstname(),$ustudent->getGender(),$ustudent->getFkEntrymannerid()->getEntryName(),'N/A'); 
            }
            
            $this->session->ustudents      = $pendingdata;
        }
        return new ViewModel(array("searchform"=>$searchform,"class"=>$class,"students"=>$students,"unregisteredlist"=>$unregisteredlist,"period"=>$period,"unregisteredselectionlist"=>$unregisteredselectionlist));
    } 
    
    public function studentlistingAction(){
        $students = $this->em->getRepository('\Application\Entity\Studentprogram')->findAll();
        return new ViewModel(array("students"=>$students));
    }
    
    function regulationsAction(){
        return new ViewModel();
    } 
    
    function registerAction(){
        $form = new \Application\Form\Registration($this->em);
        
        return new ViewModel(array("frmregister"=>$form));
    }
    
    /*
     * Reports interface
     */
    public function rptAction(){
        
    }
    
    
    function uploadregulationsAction(){
        $formupload = new \Application\Form\Enrollment($this->em);
        return new ViewModel(array("formupload"=>$formupload));
    }     
    //End Class Management    
    function studentprofileAction(){
        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        
        $profile = $this->em->getRepository('\Application\Entity\Studentprogram')->findOneBy(array("fkStudentid"=>$id));
        
        return new ViewModel(array("profile"=>$profile));
    }
    
    function administrationpocketAction(){
        return new ViewModel();
    }  
    
    function classattendanceAction(){
        $exams            = new \Application\Model\Examinations($this->em);
        $modules = $exams->getLecturerModules($this->userid);
        return new ViewModel(array("modules"=>$modules));
    } 
    
    function classregisterAction(){
        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        $module = $this->em->getRepository("\Application\Entity\Lecturermodule")->find($id);
        return new ViewModel(array("module"=>$module));
    } 
    
    function progressionAction(){
        return new ViewModel();
    }  
    
    /*
     * Band for admission reports
     */
     function reportsAction(){
        return new ViewModel();
    }
    
    
    //Enrollment report
    public function esAction(){
        $searchform = new \Application\Form\EnrollmentSearch($this->em);
        $groupby = "C.classCode";
        $reporttype = 2;
        $title = "Enrollment by class";
        $criteria = $selectionCriteria = NULL;
        $reportcontent = array();
        
        
        //Generate query parameters
        if($this->request->isPost()){
           
            $reporttype    = $this->request->getPost('report');
            $semester      = $this->request->getPost('semester');
            $year          = $this->request->getPost('academicyear');
            $studenttype   = $this->request->getPost('studenttype');
            
            //Get selected semester
            //$objectsemester = $this->em->getRepository("\Application\Entity\Academicyear")->find($semester);
            $acyear         = $this->em->getRepository("\Application\Entity\Academicyear")->findOneBy(array("parentid"=>$year,"acyr"=>$semester));
            $title          = sprintf("%s semester %s",$acyear->getParentid()->getAcyr(),$semester);
           
            $criteria["SC.fkAcademicperiodid"]          = $acyear->getPkAcademicperiodid();
            $selectionCriteria["SC.fkAcademicperiodid"] = $acyear->getPkAcademicperiodid();
            
            if(!empty($studenttype)){
                $criteria["SP.fkEntrymannerid"]          = $studenttype;
                $selectionCriteria['SC.fkEntrymannerid'] = $studenttype;
            }
            
            if($reporttype == 1){
                $groupby = "C.classYear";
                $title .= " enrollment by year";
            }elseif($reporttype == 2){
                $groupby = "C.classCode";
                $title .= " enrollment by class";
            }elseif($reporttype == 3){
                $groupby = "P.programCode";
                $title .= " enrollment by program";
            }elseif($reporttype == 4){
                $groupby = "D.deptCode";
                $title .= " enrollment by department";
            }elseif($reporttype == 5){
                $groupby = "F.facultyCode";
                $title .= " enrollment by faculty";
            }
            
            $selectionlist = $this->admission->getSelectionListStatus($selectionCriteria,$groupby);
            $classlist     = $this->admission->getEnrollmentReport($criteria,$groupby);
            $reportcontent = $this->admission->getGeneralEnrollmentReport($classlist,$selectionlist,$reporttype);
            
        }
        return array("searchform"=>$searchform,"content"=>$reportcontent,"title"=>$title,"reporttype"=>$reporttype);
    }
    
    //Enrollment report
    public function adAction(){
        $searchform = new \Application\Form\AgeDistributionSearch($this->em);
        $groupby = "C.classCode";
        $title = "Enrollment by class";
        $headers = array();
        $agerange = $minval = NULL;
        $content = array();
        
        
        //Generate query parameters
        if($this->request->isPost()){
            
            $year        = $this->request->getPost('period');
            $agerange    = $this->request->getPost('agerange');
            $minval      = $this->request->getPost('minimum');
            
            for($classyear = 0; $classyear<=5; $classyear++){
                //Generate view content
                for($age = $minval; $age<=$agerange; $age++){
                    $sql = "";
                   if($age==$minval){
                       $ageparam = "<= {$age}";
                       $sign = "<=";
                   }elseif($age==$agerange){
                       $ageparam = ">= {$age}";
                       $sign = ">=";
                   }else{
                       $ageparam = "= {$age}";
                       $sign     = "=";
                   }

                   $sql = "SELECT 
                                SUM(CASE WHEN( GENDER = 'M') THEN 1 ELSE 0 END) AS M,SUM(CASE WHEN( GENDER = 'F') THEN 1 ELSE 0 END) AS F
                             FROM `studentclass` 
                            JOIN `student`
                            ON(`student`.`PK_STUDENTID` = studentclass.`FK_STUDENTID`)
                            JOIN `user`
                            ON(`user`.`PK_USERID` = `student`.`FK_USERID`)
                            JOIN classes
                            on(classes.PK_CLASSID = studentclass.FK_CLASSID)
                            WHERE DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(),`DOB`)),'%Y') {$sign} $age"
                          . " AND FK_ACADEMICPERIODID = {$year}"
                          . " AND CLASS_YEAR = {$classyear}";
                   
                   $statement = $this->em->getConnection()->prepare($sql);
                   $statement->execute();
                   $results = $statement->fetchAll();
                   $headers[$ageparam] = $ageparam;
                   
                   foreach($results as $result){
                        $content[$classyear][$ageparam] = $result;
                   }  
                }  
            }
        }
        return array("searchform"=>$searchform,"content"=>$content,"title"=>$title,"headers"=>$headers,"minimum"=>$minval,"range"=>$agerange);
    } 
    
    //Enrollment report
    public function scpAction(){
       $content = $this->admission->getScholarshipReport();
       return array("content"=>$content);
    }     
    
}
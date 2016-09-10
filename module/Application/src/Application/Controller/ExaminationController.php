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
use Zend\Authentication\AuthenticationService;
use Zend\Mail\Message;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;

class ExaminationController extends AbstractActionController
{
    protected $em;
    //protected $am;
    protected $request;
    protected $response;
    protected $exams;
    protected $userid;
    protected $examsession;
    protected $acl;


    public function __construct(\Doctrine\ORM\EntityManager $em) {
        $this->examsession = new Container('EXAM');
        $this->em               = $em;
        $this->response         = $this->getResponse();
        $this->request          = $this->getRequest();
        $this->exams            = new \Application\Model\Examinations($em);
        //$this->assessment       = new \Application\Model\Assessment($em);
        
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
    
    
    public function assignmentcountAction(){
       
       //Get item details 
       $type = $this->em->getRepository("\Application\Entity\Assessmenttype")->find($this->request->getPost('itemid')); 
       
       //Get number assessment administered for the type
       $assessments = $this->em->getRepository("\Application\Entity\Assessmentitem")->findBy(array("fkAtid"=>$this->request->getPost('itemid'),"fkClassmoduleid"=>$this->request->getPost('module'))) ;
       $title = (count($assessments)>0)?sprintf("%s %s",$type->getTypeName(),count($assessments)+1):$type->getTypeName();
       return new JsonModel(array("title"=>  $title));
    }


    /*
     * Lecturer module listing
     */
    public function lmaAction(){
        
        //Get current period
        $period = $this->exams->getCurrentPeriod();
        
        $allocations  = array();
        $year  = "";
        //Get user's department
        $deptentity      = $this->em->getRepository("\Application\Entity\Staff")->findOneBy(array("fkUserid"=>$this->userid));
        if(empty($period)){
           $this->exams->generateFlashMessages($this->flashMessenger(),array($this->getEvent()->getRouteMatch()->getMatchedRouteName(),"No academic year or semester is active. Make sure the academic session is activated to work with this function.","Lecturer module allocation"));
          return $this->redirect()->toRoute("exceptions",array("action","exception"));  
       }

        $year            = $period[0];
        $allocations     = $this->exams->getLecturerModuleAllocation($deptentity->getFkDeptid()->getPkDeptid());
        
        return new ViewModel(array("allocations"=>$allocations,"period"=>$year));
    }
    
    /*
     * Lecturer serviced module listing
     */
    public function lsmAction(){
        
        //Get current period
        $period = $this->exams->getCurrentPeriod();
        $servicedmodules = array();
        $year  = "";
        //Get user's department
        $deptentity      = $this->em->getRepository("\Application\Entity\Staff")->findOneBy(array("fkUserid"=>$this->userid));;
        if(empty($period)){
           $this->exams->generateFlashMessages($this->flashMessenger(),array($this->getEvent()->getRouteMatch()->getMatchedRouteName(),"No academic year or semester is active. Make sure the academic session is activated to work with this function.","Lecturer module allocation"));
          return $this->redirect()->toRoute("exceptions",array("action","exception"));  
       }

        $year            = $period[0];
        
        $servicedmodules = $this->exams->getServicedModules($deptentity->getFkDeptid()->getPkDeptid());
        return new ViewModel(array("period"=>$year,"servicedmodules"=>$servicedmodules));
    }
    
    /*
     * Module alloations
     */
    public function maAction(){
        
    }
    
    /*
     * Reports interface
     */
    public function rptAction(){
        
    }
    
    /*
     * Lecturer module allocation form
     */
    public function lcformAction(){
        
        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        $subid = $this->getEvent()->getRouteMatch()->getParam('subparam');
        $lecturermodule = "";
        if($subid){
            $lecturermodule = $this->em->getRepository("\Application\Entity\Lecturermodule")->find($subid);
        }
        
        
        $deptentity      = $this->em->getRepository("\Application\Entity\Staff")->findOneBy(array("fkUserid"=>$this->userid));;
        //Get selected module information
        $module = $this->em->getRepository("\Application\Entity\Classmodule")->find($id);
        
        //Get form
        $form = new \Application\Form\Lecturermodule($this->em,$deptentity->getFkDeptid()->getPkDeptid());
        
        $form->bind($this->request->getPost());
        if($this->request->getPost('save')){
            $form->setData($this->request->getPost());
            if($form->isValid()){
                $formData = $form->getData();
                
                //If request other departments option is not selected
                if($formData['Lecturermodule']['fkStaffid'] != "-1"){
                    
                    if($subid){
                        //Get existing record information
                        $entity = $lecturermodule;
                    }else{
                        //Set new entity
                        $entity = new \Application\Entity\Lecturermodule();
                    }
                    
                    //Delete serviced module first
                    $this->exams->deletefromdb("\Application\Entity\Servicedmodule", array("fkClassmoduleid"=>$module->getPkClassmoduleid()));
                    
                    //Get selected staff entity
                    $staffentity = $this->em->getRepository('\Application\Entity\Staff')->find($formData['Lecturermodule']['fkStaffid']);
                    
                    $entity->setFkClassmoduleid($module);
                    $entity->setFkStaffid($staffentity);
                    $this->exams->saveLecturerModule($entity);
                    $this->redirect()->toRoute("examination",array("action"=>"lma"));
                }else{
                    //Define custom validation
                    $validator = new \Zend\Validator\Callback(function($formvalue){
                        //Checking if department has been selected on not
                        return empty($formvalue['fkReqdeptid'])?false:true;
                    });
                    
                    $validator->setMessage("Select department");

                    if($validator->isValid($formData)){
                        
                        //Delete any lecturer assigned to the module
                        $this->exams->deletefromdb("\Application\Entity\Lecturermodule", array("fkClassmoduleid"=>$module->getPkClassmoduleid()));
                        
                        //Get servicing department entity
                        $servicingdeptentity = $this->em->getRepository('\Application\Entity\Department')->find($formData['fkReqdeptid']);
                        
                        //If the module is already assigned update
                        $currentmodule = $this->em->getRepository('\Application\Entity\Servicedmodule')->findOneBy(array("fkClassmoduleid"=>$module->getPkClassmoduleid()));
                        if(count($currentmodule)>0){
                            $entity  = $currentmodule;
                        }else{
                            //Save serviced entity
                            $entity = new \Application\Entity\Servicedmodule();
                        }
                        
                        $entity->setFkClassmoduleid($module);
                        $entity->setReqdept($deptentity->getFkDeptid());
                        $entity->setServicingdept($servicingdeptentity);
                        $entity->setFlag("REQUESTED");
                        if($this->exams->saveServicedModule($entity)){
                            //If allocatre, send an email to hod
                             //send email
                                $message = new Message();
                                $message->addTo($emailaddress)
                                        ->addFrom('rplus@medcol.mw')
                                        ->setSubject('Natural products password reset')
                                        ->setBody("Dear {$userfullname},<br> Your password has been reset to {$password} <br> Thanks ");

                                $transport = $this->cs->configureMailTransport();
                                $transport->send($message);
                            
                        }
                        $this->redirect()->toRoute("examination",array("action"=>"lma"));
                    }else{
                        $messages = $validator->getMessages();
                        $form->get('fkReqdeptid')->setMessages(array($messages['callbackValue']));
                    }
                    
                }
                
                
                
            }
        }
        
        return new ViewModel(array("module"=>$module,"form"=>$form,"details"=>$lecturermodule));
    }
    
    
   public function servicemoduleformAction(){
       
       
        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        $subid = $this->getEvent()->getRouteMatch()->getParam('subparam');
        $module = $this->em->getRepository("\Application\Entity\Classmodule")->find($id);
        $lecturermodule = "";

        if($subid){
            $lecturermodule = $this->em->getRepository("\Application\Entity\Servicedmodule")->find($subid);
        }
    
       $deptentity      = $this->em->getRepository("\Application\Entity\Staff")->findOneBy(array("fkUserid"=>$this->userid));;
       $form = new \Application\Form\Servicedmodule($this->em,$deptentity->getFkDeptid()->getPkDeptid());
       
       $form->bind($this->request->getPost());
       if($this->request->getPost('save')){
            $form->setData($this->request->getPost());
            if($form->isValid()){
                $formdata = $form->getData();
               
                $assignedstaffid = !empty($lecturermodule->getFklmid())?$lecturermodule->getFklmid()->getPkLmid():""; 
                
                $entity = $lecturermodule;
               
                // If staff id is numeric, save in lecturer module table, and save in serviced module
                if(is_numeric($formdata['Servicedmodule']['fkStaffid'])){
                    
                    $staffentity = $this->em->getRepository("\Application\Entity\Staff")->find($formdata['Servicedmodule']['fkStaffid']);
                    
                    $lecturerentity = new \Application\Entity\Lecturermodule();
                    $lecturerentity->setFkClassmoduleid($module);
                    $lecturerentity->setFkStaffid($staffentity);
                    $lecturerid = $this->exams->saveLecturerModule($lecturerentity);
                    
                    $entity->setFklmid($lecturerid);
                    $entity->setFlag("SERVICED");
                }else{
                    // Otherwise, save in serviced module
                    $entity->setFklmid(NULL);
                    $entity->setFlag($formdata['Servicedmodule']['fkStaffid']);
                }
                $this->exams->saveServicedModule($entity);
                
                if($assignedstaffid != ""){
                 //Delete if lecturer exist
                    $this->exams->deletefromdb("\Application\Entity\Lecturermodule",$assignedstaffid);
                }
                
                $this->redirect()->toRoute("examination",array("action"=>"lsm"));
            }
       }
       
       
       return new ViewModel(array("form"=>$form,"module"=>$module,"details"=>$lecturermodule));
   } 
   
    /*
     * Redirect to add question form
     */
    public function qformAction(){
        
        $qdetails = "";
        $form = new \Application\Form\Question($this->em);
        $form->bind($this->request->getPost());
         
        $id             = $this->getEvent()->getRouteMatch()->getParam('id');
        $assessmentitem = $this->em->getRepository("\Application\Entity\Assessmentitem")->find($id);
        //If edit question has been selected 
        $qid = $this->getEvent()->getRouteMatch()->getParam('subparam');
        if($qid){
            $qdetails = $this->em->getRepository("\Application\Entity\Question")->find($qid);
        }

        if($this->request->getPost('save')){
            $form->setData($this->request->getPost());
            if($form->isValid()){
                $formdata = $form->getData();
                $entity = !empty($qdetails)?$qdetails:new \Application\Entity\Question();
                
                //Check if staff has been selected
                $staffid = ($formdata['Question']['fkStaffid'])?$this->em->getRepository('\Application\Entity\Staff')->find($formdata['Question']['fkStaffid']):NULL;
                $paperid = $this->em->getRepository('\Application\Entity\Exampaper')->find($formdata['Question']['fkPaperid']);
                //Initialize fields
                $entity->setQuestion($formdata['Question']['question']);
                $entity->setQuestionNumber($formdata['Question']['questionNumber']);
                $entity->setFkAiid($assessmentitem);
                $entity->setMarkOutOf($formdata['Question']['markOutOf']);
                $entity->setFkStaffid($staffid);
                $entity->setFkPaperid($paperid);
                $this->exams->saveQuestion($entity);
                
                //Set success message and then redirect to view
                $this->flashMessenger()->addSuccessMessage("Question successfully added");
                $this->redirect()->toRoute('examination', array('action'=>'asq',"id"=>$id));
            }
            
        }
        return new ViewModel(array("form"=>$form,"details"=>$qdetails,"item"=>$assessmentitem));
    }
   
   public function rsAction(){
       
       //Get list of stages in the flow
       $stages = $this->em->getRepository("\Application\Entity\Gradeflow")->findAll();
       
       return new ViewModel(array("stages"=>$stages));
   } 
    
   public function signoffAction(){
       
       $head = $this->exams->isHead($this->userid);
       if(!$head){
            $this->exams->generateFlashMessages($this->flashMessenger(),array($this->getEvent()->getRouteMatch()->getMatchedRouteName(),"You do no have rights to sign off grades as Head","Sign off"));
               return $this->redirect()->toRoute("exceptions",array("action","exception"));  
       }else{
            $year = $class = $studentaverages = ""; 
            $modules =  $students = $signedoff = $received = $moduleperformance = array();
            $period  = $this->exams->getCurrentPeriod();
            if(empty($period)){
               $this->exams->generateFlashMessages($this->flashMessenger(),array($this->getEvent()->getRouteMatch()->getMatchedRouteName(),"No academic year or semester is active. Make sure the academic session is activated to work with this function.","Assessments"));
               return $this->redirect()->toRoute("exceptions",array("action","exception"));  
            }
            
            $id   = $this->getEvent()->getRouteMatch()->getParam("id");
            
            //Get form for department program
            $searchform = new \Application\Form\Programlisting($this->em,$head[0]->getPkDeptid());
            $year    = $period[0];
            if($this->request->getPost('class') || $id){
                //Get selected class
                $classid = !empty($id)?$id:$this->request->getPost('class');
                
                //Get list of all students for the class
                $students           = $this->em->getRepository("\Application\Entity\Studentclass")->findBy(array("fkAcademicperiodid"=>$period[0]->getPkAcademicperiodid(),"fkClassid"=>$classid));
                $modules = $this->em->getRepository("\Application\Entity\Classmodule")->findBy(array("fkAcademicperiod"=>$period[0]->getPkAcademicperiodid(),"fkClassid"=>$classid));
                
                $count = 0;
                $data = array();
                foreach($students as $student){
                    $grades = array();
                    $count++;
                    $studentArray = array($count,$student->getFkStudentprogramid()->getStudentnumber(),$student->getFkStudentid()->getFkUserid()->getSurname(),$student->getFkStudentid()->getFkUserid()->getFirstname(),$student->getFkStudentid()->getFkUserid()->getGender(),""); 
                    foreach($modules as $module){
                        $mark = $this->exams->getStudentMarks(array("S.fkStudentclassid"=>$student->getPkStudentclassid(),"T.systemGenerated"=>'1',"A.fkClassmoduleid"=>$module->getPkClassmoduleid()));
                        $grade = !empty($mark)?$mark[0]->getMark():"-";
                        array_push($studentArray, $grade);
                    }
                    
                    //Get student remark
                    $remark = $this->em->getRepository("\Application\Entity\Studenteos")->findOneBy(array("fkStudentclassid"=>$student->getPkStudentclassid()));
                    $average = !empty($remark)?$remark->getEosaverageSystem():"";
                    $resultcode = !empty($remark)?$remark->getResultcodeSystem()->getCode():"";
                    
                    array_push($studentArray, "");
                    array_push($studentArray, "");
                    array_push($studentArray, $average);
                    array_push($studentArray, $resultcode);
                    $data[] = $studentArray;
                }
                
                $studentclass[$classid] = $data;
                
                $this->examsession->students      = $studentclass;
                
                
                //Get class information 
                $class   = $this->em->getRepository("\Application\Entity\Classes")->find($classid);
                
                $this->examsession->class      = array(array("classid"=>$classid,"classname"=>sprintf("%s (%s)",$class->getClassName(),$class->getClassCode())));
                
                //Get average grades
                $studentaverages = $this->exams->getAverageMark(array("C.fkAcademicperiodid"=>$period[0]->getPkAcademicperiodid(),"C.fkClassid"=>$classid));
                
                
                $modulecount = 0;
                foreach($modules as $module){
                    $modulecount++;
                    $totalstudents = array();
                    
                    //Get module performance summary
                    $modulesummary = $this->exams->getModulePerformance(array("T.systemGenerated"=>1,"C.pkClassmoduleid"=>$module->getPkClassmoduleid()));
                  
                    //Populate list of modules for reporting
                    $modulelist[] = array($modulecount,$module->getFkModuleid()->getModuleCode(),$module->getFkModuleid()->getModuleName(),round($modulesummary[0]['average']),$modulesummary[0]['maxmark'],$modulesummary[0]['minmark'],$modulesummary[0]['DN'],$modulesummary[0]['CR'],$modulesummary[0]['PS'],$modulesummary[0]['F'],0);
                    
                    
                    //$activemodule                                    = $module->getFkClassmoduleid();
                    $students[$module->getPkClassmoduleid()]   = count($this->exams->getModuleStudents($module));
                    $received[$module->getPkClassmoduleid()]   = count($this->exams->getAssessmentitemMarks(array("A.fkClassmoduleid"=>$module->getPkClassmoduleid(),"T.systemGenerated"=>"1"),1));
                    $signedoff[$module->getPkClassmoduleid()]  = count($this->exams->getAssessmentitemMarks(array("A.fkClassmoduleid"=>$module->getPkClassmoduleid(),"T.systemGenerated"=>"1"),2));
                    
                    //Generate array to be included in the subject performance report
                    $count = 0;
                    foreach($this->exams->getModuleStudents($module) as $student){
                        $count++;
                        //Get student performance marks
                        $items      = $this->em->getRepository("\Application\Entity\Assessmentitem")->findBy(array("fkClassmoduleid"=>$module->getPkClassmoduleid()));
                        $studentmarks = $this->exams->getStudentSubjectPerformance($student['STUDENTCLASSID'],$items);
                        
                        $totalstudents[] = array($count,$student['REGNUMBER'],$student['EXAMNUMBER'],$student['SURNAME'],$student['FIRSTNAME'],$student['GENDER'],$studentmarks['CWK'],$studentmarks['EXAM'],$studentmarks['FG'],$studentmarks['RM']);
                        
                    }
                    
                    $lecturer            = $this->em->getRepository("\Application\Entity\Lecturermodule")->findOneBy(array("fkClassmoduleid"=>$module->getPkClassmoduleid()));

                    $staff = !empty($lecturer)?sprintf("%s %s",$lecturer->getFkStaffid()->getFkUserid()->getFirstname(),$lecturer->getFkStaffid()->getFkUserid()->getSurname()):"N/A";
                    $weighting = sprintf("CWK(%s), EXAM(%s)",$module->getCwkweight().'%',$module->getExweight().'%');
                    $moduleperformance[] = array("weighting"=>$weighting,"lecturer"=>$staff,"class"=>sprintf("%s (%s)",$class->getClassName(),$class->getClassCode()),"module"=>sprintf("%s (%s)",$module->getFkModuleid()->getModuleName(),$module->getFkModuleid()->getModuleCode()),"students"=>$totalstudents);
                }
                
                $this->examsession->performance = $moduleperformance;
                $this->examsession->modulelist  = $modulelist;
            }

            return new ViewModel(array("averages"=>$studentaverages,"department"=>$head[0],"modules"=>$modules,"searchform"=>$searchform,"period"=>$year,"total"=>$students,"signedoff"=>$signedoff,"class"=>$class,"received"=>$received)); 
       }
   }
   
   public function compilegradesAction(){
       $classid      = $this->request->getPost("classid"); //11;
       $period       = $this->request->getPost("period"); //5; //
       $level        = $this->request->getPost("level"); //2; //

       $periodentity = $this->em->getRepository("\Application\Entity\Academicyear")->find($period);
       
       $this->exams->generateResultcode($classid, $periodentity, $level-1);
       
       $result = new JsonModel(array("title"=>"Results successfully compiled"));
       return $result;
   }
   
   public function mamAction(){
       
       $year = ""; 
       $modules =  $students = $signedoff = array();
       $period  = $this->exams->getCurrentPeriod();
       if(empty($period)){
          $this->exams->generateFlashMessages($this->flashMessenger(),array($this->getEvent()->getRouteMatch()->getMatchedRouteName(),"No academic year or semester is active. Make sure the academic session is activated to work with this function.","Assessments"));
          return $this->redirect()->toRoute("exceptions",array("action","exception"));  
       }
       
       $year    = $period[0];
       $modules = $this->exams->getLecturerModules($this->userid, $period[0]->getPkAcademicperiodid());
       
       foreach($modules as $module){
           $activemodule                                    = $module->getFkClassmoduleid();
           $students[$activemodule->getPkClassmoduleid()]   = count($this->exams->getModuleStudents($activemodule));
           $signedoff[$activemodule->getPkClassmoduleid()]  = count($this->exams->getAssessmentitemMarks(array("A.fkClassmoduleid"=>$activemodule->getPkClassmoduleid(),"T.systemGenerated"=>"1"),1));
       }
       
       return new ViewModel(array("modules"=>$modules,"period"=>$year,"total"=>$students,"signedoff"=>$signedoff));
   }
   
    public function cqmAction(){
       $year = ""; 
       $assessmentitems =   array();
       $period  = $this->exams->getCurrentPeriod();
       if(empty($period)){
          $this->exams->generateFlashMessages($this->flashMessenger(),array($this->getEvent()->getRouteMatch()->getMatchedRouteName(),"No academic year or semester is active. Make sure the academic session is activated to work with this function.","Assessments"));
          return $this->redirect()->toRoute("exceptions",array("action","exception"));  
       }
       
       $year            = $period[0];
       $criteria        =  array("S.fkUserid"=>$this->userid,"C.fkAcademicperiod"=>$year->getPkAcademicperiodid());
       $assessmentitems = $this->exams->getLecturerQuestions($criteria,"A.pkAiid");
      
//       foreach($modules as $module){
//           $activemodule                                    = $module->getFkClassmoduleid();
//           $students[$activemodule->getPkClassmoduleid()]   = count($this->exams->getModuleStudents($activemodule));
//           $signedoff[$activemodule->getPkClassmoduleid()]  = count($this->exams->getAssessmentitemMarks(array("A.fkClassmoduleid"=>$activemodule->getPkClassmoduleid(),"T.systemGenerated"=>"1"),1));
//       }
       
       return new ViewModel(array("items"=>$assessmentitems,"period"=>$year)); 
    }
    
   
   
   public function asmntsAction(){
       $period  = $this->exams->getCurrentPeriod();
       $marks   = array();
       //Get selected module id
       $id = $this->getEvent()->getRouteMatch()->getParam("id");
       
       //Get module details
       $module     = $this->em->getRepository("\Application\Entity\Classmodule")->find($id);
       $staffid    = $this->em->getRepository("\Application\Entity\Staff")->findOneBy(array("fkUserid"=>  $this->userid));
       $items      = $this->exams->getAssessmentitems(array("A.fkClassmoduleid"=>$module->getPkClassmoduleid(),"I.systemGenerated"=>'0',"A.fkStaffid"=>$staffid->getPkStaffid()));
//       print_r($items);
//       die();
       //Get total number of students
       $students   = $this->exams->getModuleStudents($module); //$this->em->getRepository("\Application\Entity\Studentclass")->findBy(array("fkAcademicperiodid"=>$module->getFkAcademicperiod()->getPkAcademicperiodid(),"fkClassid"=>$module->getFkClassid()->getPkClassid()));

       foreach($items as $item){
           $marks[$item->getPkAiid()]= count($this->exams->getAssessmentitemMarks(array("S.fkAiid"=>$item->getPkAiid(),"S.markLevel"=>1)));  
       }
       
       return new ViewModel(array("items"=>$items,"period"=>$period[0],"module"=>$module,"marks"=>$marks,"students"=>$students));
   }
   
   public function asqAction(){
       $period  = $this->exams->getCurrentPeriod();
//       $marks   = array();
       //Get selected module id
       $id = $this->getEvent()->getRouteMatch()->getParam("id");
       
//       //Get module details
//       $module     = $this->em->getRepository("\Application\Entity\Classmodule")->find($id);
//       $staffid    = $this->em->getRepository("\Application\Entity\Staff")->findOneBy(array("fkUserid"=>  $this->userid));
       $item      = $this->exams->getAssessmentitems(array("A.pkAiid"=>$id));
      
//       //Get questions
       $questions   = $this->em->getRepository("\Application\Entity\Question")->findBy(array("fkAiid"=>$id));
//
//       foreach($items as $item){
//           $marks[$item->getPkAiid()]= count($this->exams->getAssessmentitemMarks(array("S.fkAiid"=>$item->getPkAiid(),"S.markLevel"=>1)));  
//       }
       
       return new ViewModel(array("items"=>$item[0],"period"=>$period[0],"questions"=>$questions));
   }
   
   public function gradesignoffAction(){
       $itemid             = $this->request->getPost('itemid');
       $destination        = $this->request->getPost('destination');
       $start              = $this->request->getPost('start');
       $studentitems       = $this->request->getPost('students');
       $studentobjectarray = array();
       
      
       if(!empty($studentitems)){
           foreach(preg_split("/,/", $studentitems) as $student){
               $studentobjectarray[] = $this->em->getRepository("\Application\Entity\Studentmark")->findOneBy(array("fkAiid"=>$itemid,"markLevel"=>$start,"fkStudentclassid"=>$student));
           }
       }
       
       //$students    = $test;
       $this->exams->moveGrade($itemid, $destination, $start, $this->userid,$studentobjectarray);
       
       //Send email
       
       $result = new JsonModel(array("title"=>$itemid));
       return $result;
   }
   
   public function gfAction(){
       $period  = $this->exams->getCurrentPeriod();
       
       //Get flow stages
       $stages   = $this->em->getRepository("\Application\Entity\Gradeflow")->findAll();
       return new ViewModel(array("period"=>$period[0],"stages"=>$stages));
   }
   
   public function emAction(){
   }
   
   public function flowformAction(){
       $form      = new \Application\Form\Flow($this->em);
       $stage   = $this->em->getRepository("\Application\Entity\Gradeflow")->findOneBy(array(),array("level"=>"desc"));
       
       
        $details = "";
        $form->bind($this->request->getPost());
         //If edit faculty has been selected then select from database
        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        if($id){
            $details = $this->em->getRepository("\Application\Entity\Gradeflow")->find($id);
        }

        if($this->request->getPost('save')){
            $form->setData($this->request->getPost());
            if($form->isValid()){
                $formdata = $form->getData();
                //Check if action is to update record
                $entity = !empty($details)?$details:new \Application\Entity\Gradeflow();
                
                //Check if staff has been selected
                $roleid = $this->em->getRepository('\Application\Entity\Role')->find($formdata['Flow']['fkRoleid']);

                //Initialize fields
                $entity->setDescription($formdata['Flow']['description']);
                $entity->setLevel($formdata['Flow']['level']);
                $entity->setFkRoleid($roleid);
                
                if($this->exams->saveFlowStage($entity)){
                    //Set success message and then redirect to view
                    $this->flashMessenger()->addSuccessMessage("Stage successfully saved");
                    $this->redirect()->toRoute('examination', array('action'=>'em'));
                }
            }
        }
       
       return new ViewModel(array("stage"=>$stage,"form"=>$form,"details"=>$details));
   }
   
   public function lmsAction(){
       $period  = $this->exams->getCurrentPeriod();
       $marks   = array();
       //Get selected module id
       $id = $this->getEvent()->getRouteMatch()->getParam("id");
       //Get module details
       $module     = $this->em->getRepository("\Application\Entity\Classmodule")->find($id);
       $staffid    = $this->em->getRepository("\Application\Entity\Staff")->findOneBy(array("fkUserid"=>  $this->userid));
       $item      = $this->exams->getAssessmentitems(array("A.fkClassmoduleid"=>$module->getPkClassmoduleid(),"I.systemGenerated"=>1,"A.fkStaffid"=>$staffid->getPkStaffid()));
       
       //Get total number of students
       $students   = $this->exams->getModuleStudents($module); //$this->em->getRepository("\Application\Entity\Studentclass")->findBy(array("fkAcademicperiodid"=>$module->getFkAcademicperiod()->getPkAcademicperiodid(),"fkClassid"=>$module->getFkClassid()->getPkClassid()));
       foreach($students as $student){
           $itemid = $item[0]->getPkAiid();
           $marks[$student['STUDENTCLASSID']]= $this->em->getRepository("\Application\Entity\Studentmark")->findOneBy(array("fkAiid"=>$itemid,"fkStudentclassid"=>$student['STUDENTCLASSID']));  
       }
       return new ViewModel(array("period"=>$period[0],"module"=>$module,"marks"=>$marks,"students"=>$students,"item"=>$item[0]));
   }
   
   public function gradetrailAction(){
       $period  = $this->exams->getCurrentPeriod();
       //Get selected module id
       $id = $this->getEvent()->getRouteMatch()->getParam("id");
       //Get module details
       $grades     = $this->em->getRepository("\Application\Entity\FgTracker")->findBy(array("fkSmid"=>$id));
       
       $item     = $this->em->getRepository("\Application\Entity\Studentmark")->find($id);
       return new ViewModel(array("period"=>$period[0],"grades"=>$grades,"item"=>$item));
   }
   
   
   public function mmmAction(){
      $id        = $this->getEvent()->getRouteMatch()->getParam("id");
      $userlevel = $this->getEvent()->getRouteMatch()->getParam("subparam");
      //Get assignment type code where system generated value is 1
      $assessmenttype = $this->em->getRepository('\Application\Entity\Assessmenttype')->findOneBy(array("systemGenerated"=>1));
      $allGrades = $currentHeaders = array();
      //Check first if assessment items already exists
      $item     = $this->em->getRepository('\Application\Entity\Assessmentitem')->findOneBy(array("fkAtid"=>$assessmenttype->getPkAtid(),"fkClassmoduleid"=>$id));
      
      //Get students' marks for the item
      $studentmarks = $this->em->getRepository('\Application\Entity\Studentmark')->findBy(array("fkAiid"=>$item->getPkAiid()));
      
      //Get headers
      $headers      = $this->exams->getModuleGradeTrailHeaders(array("S.fkAiid"=>$item->getPkAiid()));
      
      foreach ($studentmarks as $studentmark){
          $allGrades = array();
          //Get tracked marks
          $trackedGrades  = $this->exams->getStudentModuleGradeTrail(array("F.fkSmid"=>$studentmark->getPkSmid()));
          $currentGrade[$studentmark->getFkStudentclassid()->getPkStudentclassid()][$studentmark->getMarkLevel()->getLevel()] = array("DATE"=>$studentmark->getExamdate(),"GRADE"=>$studentmark->getMark());
          $currentHeaders[$studentmark->getMarkLevel()->getLevel()] = $studentmark->getMarkLevel()->getFkRoleid()->getRoleName();
          
          $allGrades[$studentmark->getFkStudentclassid()->getPkStudentclassid()]      = array_replace($trackedGrades[$studentmark->getFkStudentclassid()->getPkStudentclassid()], $currentGrade[$studentmark->getFkStudentclassid()->getPkStudentclassid()]);
          
          $studentgrades[] = array("STUDENT"=>$studentmark,"GRADES"=>$allGrades);
          
//          reset($allGrades);
//          print_r(key($allGrades));
          
      }
      $headerTitles = array_replace($headers, $currentHeaders);
      return new ViewModel(array("students"=>$studentgrades,"item"=>$item,"headers"=>$headerTitles,"userlevel"=>$userlevel));
   }
    
   public function camAction(){
       $id = $this->getEvent()->getRouteMatch()->getParam("id");
       $studentmarks = array();
       
       $item = $this->em->getRepository("\Application\Entity\Assessmentitem")->find($id);
       $assessmentmarks = $this->em->getRepository('\Application\Entity\Studentmark')->findBy(array('fkAiid'=>$item->getPkAiid()));
       $students   = $this->exams->getModuleStudents($item->getFkClassmoduleid());
       //Generate marks
       foreach($assessmentmarks as $assessmentmark){
            $studentmarks[$assessmentmark->getFkStudentclassid()->getPkStudentclassid()]   =  array("mark"=>$assessmentmark->getMark(),"id"=>$assessmentmark->getPkSmid()); 
        }
      $frmgrades      = new \Application\Form\Grades(count($students),$this->em);
      
       if($this->request->isPost()){
            
            $mark           = $this->request->getPost('mark');
            //$assessmentid   = $this->request->getPost('fkAiid');
            $pkSmid        = $this->request->getPost('pkSmid');
//            print_r($this->request->getPost());
//            die();
            foreach($this->request->getPost('student') as $key=>$student){
                if(!empty($mark[$key]['mark'])){
                    $marks = new \Application\Entity\Studentmark();
                    if($pkSmid[$key]){
                        $marks = $this->em->getRepository('\Application\Entity\Studentmark')->find($pkSmid[$key]);
                    }

                    $fkStudentid  = $this->em->getRepository('\Application\Entity\Studentclass')->find($student);
                    //$fkAiid       = $this->em->getRepository('\Application\Entity\Assessmentitem')->find($assessmentid);
                    $fkUserid     = $this->em->getRepository('\Application\Entity\User')->find($this->userid);
                    $level     = $this->em->getRepository('\Application\Entity\Gradeflow')->findOneBy(array("level"=>1));

                    $marks->setExamdate(new \DateTime());
                    $marks->setFkAiid($item);
                    $marks->setFkStudentclassid($fkStudentid);
                    $marks->setMark($mark[$key]['mark']);
                    $marks->setMarkLevel($level);
                    $marks->setPublishStatus('0');
                    $marks->setUploadby($fkUserid);

                    $this->exams->saveMarks($marks);
                    $this->redirect()->toRoute("examination",array("action"=>"asmnts","id"=>$item->getFkClassmoduleid()->getPkClassmoduleid()));
                }
            }
            
       }
      return new ViewModel(array("item"=>$item,"students"=>$students,"marks"=>$studentmarks,"form"=>$frmgrades));
   }
   
   public function eqmAction(){
       $id = $this->getEvent()->getRouteMatch()->getParam("id");
       $questionmarks = $paper = array();
       
       $item = $this->em->getRepository("\Application\Entity\Assessmentitem")->find($id);
       
       //Get questions for the lecturer
       $criteria            =  array("S.fkUserid"=>$this->userid,"Q.fkAiid"=>$item->getPkAiid());
       $questions = $this->exams->getLecturerQuestions($criteria);
       
       foreach($questions as $question){
       //Group questions based on paper
        $paper[$question->getFkPaperid()->getPaperName()][$question->getPkQid()] = $question; 
       //Get marks for the question for each individual student
        $assessmentmarks = $this->em->getRepository('\Application\Entity\Questionmark')->findBy(array('fkQid'=>$question->getPkQid()));
         //Generate marks
        foreach($assessmentmarks as $assessmentmark){
             $questionmarks[$assessmentmark->getFkQid()->getPkQid()][$assessmentmark->getFkStudentclassid()->getPkStudentclassid()]   =  array("mark"=>$assessmentmark->getMark(),"id"=>$assessmentmark->getPkQgid()); 
         }
       }
       
       
       $students   = $this->exams->getModuleStudents($item->getFkClassmoduleid());
       $frmgrades      = new \Application\Form\Questionmark(count($students),$questions);
      
       if($this->request->isPost()){
            
            $mark           = $this->request->getPost('mark');
            $qgid           = $this->request->getPost('pkQgid');
            foreach($this->request->getPost('student') as $key=>$student){
               
                foreach($student as $qid=>$studentid){
                    
                    if(!empty($mark[$key][$qid])){
                        $marks = !empty($qgid[$key][$qid])?$this->em->getRepository('\Application\Entity\Questionmark')->find($qgid[$key][$qid]):new \Application\Entity\Questionmark();
                        $fkStudentid  = $this->em->getRepository('\Application\Entity\Studentclass')->find($studentid);
                        $fkQid     = $this->em->getRepository('\Application\Entity\Question')->find($qid);

                        $marks->setFkQid($fkQid);
                        $marks->setFkStudentclassid($fkStudentid);
                        $marks->setMark($mark[$key][$qid]);

                        $this->exams->saveQuestionMark($marks);
                        
                    }
                }
            }
            
            //Send ticked questions to owner
            foreach($this->request->getPost('signoff') as $questionid){
                $fkQid     = $this->em->getRepository('\Application\Entity\Question')->find($questionid);
                $fkQid->setInHand('0');
                $this->exams->saveQuestion($fkQid);
            }
            
            return $this->redirect()->toRoute("examination",array("action"=>"eqm","id"=>$item->getPkAiid()));
       }
      return new ViewModel(array("papers"=>$paper,"item"=>$item,"students"=>$students,"marks"=>$questionmarks,"form"=>$frmgrades,"questions"=>$questions));
   }

   public function imAction(){
       $id = $this->getEvent()->getRouteMatch()->getParam("id");
       $item = $this->em->getRepository("\Application\Entity\Assessmentitem")->find($id);
       
       $formfile = new \Application\Form\Marksupload();
        
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
                        $contents[]   = $this->exams->formatFileImport($rowdata,$item);
                    }
                    $this->examsession->uploadlist = $contents;
                    return $this->redirect()->toRoute("examination",array("action"=>"cu","id"=>$id));
                }
            } 
        }
        
        return new ViewModel(array("uploadform"=>$formfile,"item"=>$item));
   }
   
   public function iqmAction(){
       $id  = $this->getEvent()->getRouteMatch()->getParam("id");
       
       $q   = $this->em->getRepository("\Application\Entity\Question")->find($id);
       
       $item = $this->em->getRepository("\Application\Entity\Assessmentitem")->find($q->getFkAiid()->getPkAiid());
       
       $formfile = new \Application\Form\Marksupload();
        
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
                        $contents[]   = $this->exams->formatFileImport($rowdata,$item);
                    }
                    $this->examsession->uploadlist = $contents;
                    return $this->redirect()->toRoute("examination",array("action"=>"cqmu","id"=>$id));
                }
            } 
        }
        
        return new ViewModel(array("uploadform"=>$formfile,"item"=>$item,"question"=>$q));
   }
   
   
   
   
    public function cuAction(){
      $id = $this->getEvent()->getRouteMatch()->getParam("id");
      $item = $this->em->getRepository("\Application\Entity\Question")->find($id);
      $contents = $this->examsession->uploadlist;
      
      if($this->request->getPost('btnupload')){
         
          foreach($contents as $student){
                if(empty($student['ERRORS'])){
                    //Check if marks have already been captured
                    $pkSmid = $this->em->getRepository('\Application\Entity\Studentmark')->findOneBy(array("fkAiid"=>$id,"fkStudentclassid"=>$student['STUDENTCLASSID']));
                    
                    $marks = !empty($pkSmid)?$pkSmid:new \Application\Entity\Studentmark();

                    $fkStudentid  = $this->em->getRepository('\Application\Entity\Studentclass')->find($student['STUDENTCLASSID']);
                    $fkUserid     = $this->em->getRepository('\Application\Entity\User')->find($this->userid);
                    $level     = $this->em->getRepository('\Application\Entity\Gradeflow')->findOneBy(array("level"=>1));

                    $marks->setExamdate(new \DateTime());
                    $marks->setFkAiid($item);
                    $marks->setFkStudentclassid($fkStudentid);
                    $marks->setMark($student['MARK']);
                    $marks->setMarkLevel($level);
                    $marks->setPublishStatus('0');
                    $marks->setUploadby($fkUserid);
                    $this->exams->saveMarks($marks);
                    
                }
          }
          return $this->redirect()->toRoute("examination",array("action"=>"cam","id"=>$id));
      }
      
      return new ViewModel(array("item"=>$item,"list"=>$contents));
    }
    
    public function cqmuAction(){
      $id = $this->getEvent()->getRouteMatch()->getParam("id");
      $question = $this->em->getRepository("\Application\Entity\Question")->find($id);
      $contents = $this->examsession->uploadlist;
      
      if($this->request->getPost('btnupload')){
         
          foreach($contents as $student){
                if(empty($student['ERRORS'])){
                    //Check if marks have already been captured
                    $pkSmid = $this->em->getRepository('\Application\Entity\Questionmark')->findOneBy(array("fkQid"=>$id,"fkStudentclassid"=>$student['STUDENTCLASSID']));
                    
                    $marks = !empty($pkSmid)?$pkSmid:new \Application\Entity\Questionmark();

                    $fkStudentid  = $this->em->getRepository('\Application\Entity\Studentclass')->find($student['STUDENTCLASSID']);
                   
                    $marks->setFkQid($question);
                    $marks->setFkStudentclassid($fkStudentid);
                    $marks->setMark($student['MARK']);
                    
                    $this->exams->saveQuestionMark($marks);
                    
                }
          }
          return $this->redirect()->toRoute("examination",array("action"=>"eqm","id"=>$question->getFkAiid()->getPkAiid()));
      }
      
      return new ViewModel(array("question"=>$question,"list"=>$contents));
    }
    
    
    
    
    public function amrkAction(){
        
       $id = $this->getEvent()->getRouteMatch()->getParam("id");
       $moduleweights = array();
       $module     = $this->em->getRepository("\Application\Entity\Classmodule")->find($id);
       //Get list of assessment items and marks
       $items      = $this->em->getRepository("\Application\Entity\Assessmentitem")->findBy(array("fkClassmoduleid"=>$module->getPkClassmoduleid()));
       
       //Get list of students
       $students   = $this->exams->getModuleStudents($module);
       //Get student marks
       foreach($students as $student){
           foreach($items as $item){
               $studentmark  = $this->em->getRepository("\Application\Entity\Studentmark")->findOneBy(array("fkStudentclassid"=>$student["STUDENTCLASSID"],"fkAiid"=>$item->getPkAiid()));
               $mark[$student["STUDENTCLASSID"]][$item->getPkAiid()] = (count($studentmark)>0)?$studentmark->getMark():"-";
           }
       }
       
       //Save grades
       if($this->request->getPost()){
           $moduleweights = $this->request->getPost('item');
           if($this->request->getPost('save')){
                //Save new waights
                foreach($moduleweights as $itemdid=>$moduleweight){
                    $itemweight = $this->em->getRepository("\Application\Entity\Assessmentitem")->find($itemdid);
                    $itemweight->setWeighting($moduleweight);
                    $this->exams->saveAssessmentItem($itemweight);
                }
                
                //Get assignment type code where system generated value is 1
                $assessmenttype = $this->em->getRepository('\Application\Entity\Assessmenttype')->findOneBy(array("systemGenerated"=>1));
                
                //Check first if assessment items already exists
                $averageitem     = $this->em->getRepository('\Application\Entity\Assessmentitem')->findOneBy(array("fkAtid"=>$assessmenttype->getPkAtid(),"fkClassmoduleid"=>$module->getPkClassmoduleid()));
                
                //Save new item for the final grade
                $itementity      = !empty($averageitem)?$averageitem:new \Application\Entity\Assessmentitem();

                //Get satffid
                $staff          = $this->em->getRepository('\Application\Entity\Staff')->findOneBy(array("fkUserid"=>  $this->userid));
                //Save assessment entity object
                $itementity->setAssessmentTitle('Average mark');
                $itementity->setCreatedon(new \DateTime());
                $itementity->setFkAtid($assessmenttype);
                $itementity->setFkClassmoduleid($module);
                $itementity->setFkStaffid($staff);
                $itementity->setWeighting(100);
                $objitem  =  $this->exams->saveAssessmentItem($itementity);
                
                //Save student final marks if item is saved
                if($objitem){
                    $marks       = $this->request->getPost('studentmark');
                    foreach($marks as $studentclassid=>$mark){
                            
                           $fkStudentid   =  $this->em->getRepository('\Application\Entity\Studentclass')->find($studentclassid);
                            //Get user object 
                           $userid   =  $this->em->getRepository('\Application\Entity\User')->find($this->userid);
                           //Check if mark already exist
                           $marksitem = $this->em->getRepository('\Application\Entity\Studentmark')->findOneBy(array("fkAiid"=>$objitem->getPkAiid(),"fkStudentclassid"=>$fkStudentid));
                           $level     = $this->em->getRepository('\Application\Entity\Gradeflow')->findOneBy(array("level"=>1));
                           
                           
                           $marks = !empty($marksitem)?$marksitem:new \Application\Entity\Studentmark();
                           //Save as marks
                           $marks->setExamdate(new \DateTime());
                           $marks->setFkAiid($objitem);
                           $marks->setFkStudentclassid($fkStudentid);
                           $marks->setMark($mark);
                           $marks->setMarkLevel($level);
                           $marks->setPublishStatus('0');
                           $marks->setUploadby($userid);
                           $this->exams->saveMarks($marks);
        
                    }
                }

                return $this->redirect()->toRoute("examination",array("action"=>"asmnts","id"=>$module->getPkClassmoduleid()));
           }
       }
       
       return new ViewModel(array("module"=>$module,"students"=>$students,"items"=>$items,"marks"=>$mark,"weights"=>$moduleweights));
    }
    
    public function amgmtAction(){
        
    }

    public function csqmAction(){
        
       $id = $this->getEvent()->getRouteMatch()->getParam("id");
       $moduleweights = $mark = $paper = array();
        //Get list of assessment items and marks
       $item            = $this->em->getRepository("\Application\Entity\Assessmentitem")->find($id);
       $module          = $this->em->getRepository("\Application\Entity\Classmodule")->find($item->getFkClassmoduleid()->getPkClassmoduleid());
       //Get list of questions
       $questions       = $this->em->getRepository("\Application\Entity\Question")->findBy(array("fkAiid"=>$id,"inHand"=>'0'));
       
       //Get list of students
       $students   = $this->exams->getModuleStudents($module);
       //Generate question paper headers
       foreach($questions as $question){
        $paper[$question->getFkPaperid()->getPaperName()][$question->getPkQid()] = $question; 
       }
       
       //Get student marks
       foreach($students as $student){
           foreach($questions as $question){
               
               $studentmark  = $this->em->getRepository("\Application\Entity\Questionmark")->findOneBy(array("fkStudentclassid"=>$student["STUDENTCLASSID"],"fkQid"=>$question->getPkQid()));
               $mark[$student["STUDENTCLASSID"]][$question->getPkQid()] = (count($studentmark)>0)?$studentmark->getMark():"-";
           }
       }
       
      // Save grades
       if($this->request->isPost()){
           
           foreach($this->request->getPost('studentmark') as $student=>$mark){
                    
                    //Check if marks have already been captured
                    $pkSmid = $this->em->getRepository('\Application\Entity\Studentmark')->findOneBy(array("fkAiid"=>$id,"fkStudentclassid"=>$student));                   
                    $marks = !empty($pkSmid)?$pkSmid:new \Application\Entity\Studentmark();

                    $fkStudentid  = $this->em->getRepository('\Application\Entity\Studentclass')->find($student);
                    $fkUserid     = $this->em->getRepository('\Application\Entity\User')->find($this->userid);
                    $level     = $this->em->getRepository('\Application\Entity\Gradeflow')->findOneBy(array("level"=>1));

                    $marks->setExamdate(new \DateTime());
                    $marks->setFkAiid($item);
                    $marks->setFkStudentclassid($fkStudentid);
                    $marks->setMark($mark);
                    $marks->setMarkLevel($level);
                    $marks->setPublishStatus('0');
                    $marks->setUploadby($fkUserid);
                    $this->exams->saveMarks($marks);
          }
          return $this->redirect()->toRoute("examination",array("action"=>"asmnts","id"=>$item->getFkClassmoduleid()->getPkClassmoduleid()));
       }
       
       return new ViewModel(array("papers"=>$paper,"item"=>$item,"students"=>$students,"questions"=>$questions,"marks"=>$mark,"weights"=>$moduleweights));
    }

    public function assessmentitemformAction(){
      
      $id             = $this->getEvent()->getRouteMatch()->getParam("id");
      $assessmentid   = $this->getEvent()->getRouteMatch()->getParam("subparam");
      
      $itemsdetails = "";
      if(!empty($assessmentid)){
          $itemsdetails = $this->em->getRepository("\Application\Entity\Assessmentitem")->find($assessmentid);
      }
      //Get class module
      $module = $this->em->getRepository("\Application\Entity\Classmodule")->find($id);
      //Assessment item form
      $form  = new \Application\Form\Assessment($this->em,$module);
       //Bind submitted values to form
      $form->bind($this->request->getPost());
      
      
      if($this->request->getPost()){
          $form->setData($this->request->getPost());
          if($form->isValid()){
              $post = $form->getData();
              
              //Get assessment type entity
              $type       =  $this->em->getRepository("\Application\Entity\Assessmenttype")->find($post['Assessment']['fkAtid']);
              $staffid    =  $this->em->getRepository("\Application\Entity\Staff")->findOneBy(array("fkUserid"=>  $this->userid));
              
              $itemEntity = !empty($itemsdetails)?$itemsdetails: new \Application\Entity\Assessmentitem();
              $itemEntity->setFkClassmoduleid($module);
              $itemEntity->setWeighting($post['Assessment']['weighting']);
              $itemEntity->setCreatedon(new \DateTime());
              $itemEntity->setFkAtid($type);
              $itemEntity->setAssessmentTitle($post['Assessment']['assessmentTitle']);
              //$itemEntity->setShortName($post['Assessment']['shortName']);
              $itemEntity->setFkStaffid($staffid);
              
              $this->exams->saveAssessmentItem($itemEntity);
              return $this->redirect()->toRoute("examination",array("action"=>"asmnts","id"=>$id));
          }
      }
      
      
      return new ViewModel(array("module"=>$module,"form"=>$form,"details"=>$itemsdetails));
    }
   
}
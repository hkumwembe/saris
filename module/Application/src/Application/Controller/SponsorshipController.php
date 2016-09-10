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

class SponsorshipController extends AbstractActionController
{
    protected $em;
    protected $userid;
    protected $request;
    protected $response;
    protected $sp;
    
    public function __construct(\Doctrine\ORM\EntityManager $em) {
        $this->em = $em;
        //$this->cs = $cs;
        $this->sp      = new \Application\Model\Sponsorship($this->em);
    }
    
    public function onDispatch(\Zend\Mvc\MvcEvent $e) {
        $this->layout()->setVariables(array("activemodule"=>$this->getEvent()->getRouteMatch()->getMatchedRouteName()));
        parent::onDispatch($e);
    }
    
    public function indexAction()
    {
        $successMsg = "";
        $flashMessenger = $this->flashMessenger();
        if($flashMessenger->hasSuccessMessages()){
            $successMsg = implode("<br>", $flashMessenger->getSuccessMessages());
        }
        
        $sponsors = $this->em->getRepository("\Application\Entity\Sponsor")->findAll();
        return new ViewModel(array("sponsors"=>$sponsors,"msg"=>$successMsg));
    }
    
    
    /*
     * Redirect to notices form view and save notice information
     */
    public function sfAction(){
        
        $sponsordetails = "";
        $form = new \Application\Form\Sponsor($this->em);
        $form->bind($this->request->getPost());
         //If edit sponsor has been selected then select from database
        $id = $this->getEvent()->getRouteMatch()->getParam('id');
       
        if($id){
            $sponsordetails = $this->em->getRepository("\Application\Entity\Sponsor")->find($id);
        }

        if($this->request->getPost('save')){
            $form->setData($this->request->getPost());
            if($form->isValid()){
                $formdata = $form->getData();
                //Check if action is to update record
                $sponsorEntity  = !empty($sponsordetails)?$sponsordetails:new \Application\Entity\Sponsor();
                
                //Initialize fields
                $sponsorEntity->setSponsorName($formdata['Sponsor']['sponsorName']);
                $sponsorEntity->setContactPerson($formdata['Sponsor']['contactPerson']);
                $sponsorEntity->setDescription($formdata['Sponsor']['description']);
                $sponsorEntity->setPhoneNumber($formdata['Sponsor']['phoneNumber']);
                $sponsorEntity->setPostalAddress($formdata['Sponsor']['postalAddress']);
                $sponsorEntity->setCurrentStatus($formdata['Sponsor']['currentStatus']);
                
                $this->sp->saveSponsor($sponsorEntity);
                //Set success message and then redirect to view
                $this->flashMessenger()->addSuccessMessage("Sponsor details saved");
                return $this->redirect()->toRoute('ss', array('action'=>'index'));
            }
            
        }
        return new ViewModel(array("form"=>$form,"details"=>$sponsordetails));
    }
    
    public function ssAction()
    {
        $successMsg = "";
        $pendingallocation = array();
        $flashMessenger = $this->flashMessenger();
        if($flashMessenger->hasSuccessMessages()){
            $successMsg = implode("<br>", $flashMessenger->getSuccessMessages());
        }
        
        $id                = $this->getEvent()->getRouteMatch()->getParam('id');
        $sponsor           = $this->em->getRepository("\Application\Entity\Sponsor")->find($id);
        //$studentsObject    = $this->sp->SponsoredList(array("SS.fkSponsorid"=>$id));
        $students          = $this->sp->SponsoredList(array("SS.fkSponsorid"=>$id));
        
        $availableStudents = $this->sp->UnSponsoredList($students);
        
        //Generate autocomplete array
        foreach($availableStudents as $availablestudent){
            $pendingallocation[] = array("label"=>sprintf("%s %s",$availablestudent->getFkStudentid()->getFkUserid()->getFirstname(),$availablestudent->getFkStudentid()->getFkUserid()->getSurname()),"value"=>$availablestudent->getPkStudentprogramid());
        }
        
        if($this->request->isPost()){
            $studentid = $this->request->getPost('studentid');
            $sponsorid = $this->request->getPost('sponsorid');

            $student = $this->em->getRepository("\Application\Entity\Studentprogram")->find($studentid);
            $sponsor = $this->em->getRepository("\Application\Entity\Sponsor")->find($sponsorid);
            
            //Populate sponsorship object
            $sponsorshipObject = new \Application\Entity\Sponsoredstudent();
            $sponsorshipObject->setFkSponsorid($sponsor);
            $sponsorshipObject->setFkStudentprogramid($student);
            
            //Save student sponsorship
            $this->sp->saveSponsoredStudent($sponsorshipObject);
            //Refresh sponsorship beneficiary page
            $this->flashMessenger()->addSuccessMessage("Student successfully assigned sponsorhip");
            return $this->redirect()->toRoute('ss', array('action'=>'ss',"id"=>$id));
        }
        
        return new ViewModel(array("sponsor"=>$sponsor,"students"=>$students,"availablestudents"=> json_encode($pendingallocation),"msg"=>$successMsg));
    }
    
    public function dlnAction(){
        
        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        $this->cm->deletefromdb("\Application\Entity\Notice",$id);
        
        $this->flashMessenger()->addSuccessMessage("Notice successfully removed");
        $this->redirect()->toRoute('communication', array('action'=>'index'));
    }
    
}
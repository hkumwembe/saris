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

class AccommodationController extends AbstractActionController
{
     protected $em;
    // protected $cs;
    
    public function __construct(\Doctrine\ORM\EntityManager $em) {
        $this->em = $em;
        $this->preferences      = new \Application\Model\Preferences($this->em);
        //$this->cs = $cs;
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
        
        $hostels  = $this->em->getRepository("\Application\Entity\Hostel")->findAll();
        return new ViewModel(array("hostels"=>$hostels,"msg"=>$successMsg));
    }
    
    /*
     * Redirect to hostel form view and save hostel information
     */
    public function hostelformAction(){
        
        $hosteldetails = "";
        $form = new \Application\Form\Hostel($this->em);
        $form->bind($this->request->getPost());
         //If edit faculty has been selected then select from database
        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        if($id){
            $hosteldetails = $this->em->getRepository("\Application\Entity\Hostel")->find($id);
        }

        if($this->request->getPost('save')){
            $form->setData($this->request->getPost());
            if($form->isValid()){
                $formdata = $form->getData();
                
                $entity         = !empty($hosteldetails)?$hosteldetails:new \Application\Entity\Hostel();
               
                $campusentity   = $this->em->getRepository("\Application\Entity\Campus")->find($formdata['Hostel']['fkCampusid']);
                $categoryentity = $this->em->getRepository("\Application\Entity\Hostelcategory")->find($formdata['Hostel']['fkHtid']);
                
                //Initialize fields
                $entity->setHostelName($formdata['Hostel']['hostelName']);
                $entity->setFkCampusid($campusentity);
                $entity->setFkHtid($categoryentity);
                
                if($this->preferences->saveHostel($entity)){
                    //Set success message and then redirect to view
                    $this->flashMessenger()->addSuccessMessage("Hostel information saved");
                    $this->redirect()->toRoute('accommodation', array('action'=>'index'));
                }
                
                
            }
            
        }
        return new ViewModel(array("form"=>$form,"details"=>$hosteldetails));
    }
    
    
    public function roomsAction()
    {
        $successMsg = "";
        $flashMessenger = $this->flashMessenger();
        if($flashMessenger->hasSuccessMessages()){
            $successMsg = implode("<br>", $flashMessenger->getSuccessMessages());
        }
        
        $id = $this->getEvent()->getRouteMatch()->getParam("id");
        
        //Get hostel details
        $hostel  = $this->em->getRepository("\Application\Entity\Hostel")->find($id);
        
        $rooms  = $this->em->getRepository("\Application\Entity\Hostelroom")->findBy(array("fkHostelid"=>$id));
        return new ViewModel(array("rooms"=>$rooms,"msg"=>$successMsg,"hostel"=>$hostel));
    }
    
    
    
}
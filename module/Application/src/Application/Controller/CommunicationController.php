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

class CommunicationController extends AbstractActionController
{
    protected $em;
    protected $userid;
    protected $request;
    protected $response;
    protected $cm;
    
    public function __construct(\Doctrine\ORM\EntityManager $em) {
        $this->em = $em;
        //$this->cs = $cs;
        $this->cm      = new \Application\Model\Communication($this->em);
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
        
        $notices = $this->em->getRepository("\Application\Entity\Notice")->findAll();
        return new ViewModel(array("notices"=>$notices,"msg"=>$successMsg));
    }
    
    public function sessionProgressAction()
    {
        $adapter     = new JsPush(array('updateMethodName' => 'Zend_ProgressBar_Update',
                                       'finishMethodName' => 'Zend_ProgressBar_Finish'));
        $progressBar = new ProgressBar($adapter, 0, 100);

        for ($i = 1; $i <= 100; $i++) {
            if ($i < 20) {
                $text = 'Just beginning';
            } else if ($i < 50) {
                $text = 'A bit done';
            } else if ($i < 80) {
                $text = 'Getting closer';
            } else {
                $text = 'Nearly done';
            }

            $progressBar->update($i, $text);
            //usleep(100000);
        }

        $progressBar->finish();

        die;
        
    }
    
    /*
     * Redirect to notices form view and save notice information
     */
    public function nfAction(){
        
        $noticedetails = "";
        $form = new \Application\Form\Notice($this->em);
        $form->bind($this->request->getPost());
         //If edit notice has been selected then select from database
        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        if($id){
            $noticedetails = $this->em->getRepository("\Application\Entity\Notice")->find($id);
        }

        if($this->request->getPost('save')){
            $form->setData($this->request->getPost());
            if($form->isValid()){
                $formdata = $form->getData();
                //Check if action is to update record
                $noticeEntity  = !empty($noticedetails)?$noticedetails:new \Application\Entity\Notice();
                
                //Check if staff has been selected
                $staffid = $this->em->getRepository('\Application\Entity\User')->find($this->userid);
                
                //Initialize fields
                $noticeEntity->setHeader($formdata['Notice']['header']);
                $noticeEntity->setBody($formdata['Notice']['body']);
                $noticeEntity->setDatePublished(new \DateTime());
                $noticeEntity->setAccounttype($formdata['Notice']['accountType']);
                $noticeEntity->setIsActive($formdata['Notice']['isActive']);
                $noticeEntity->setCapturedBy($staffid);
                
                $this->cm->saveNotice($noticeEntity);
                //Set success message and then redirect to view
                $this->flashMessenger()->addSuccessMessage("Notice information saved");
                $this->redirect()->toRoute('communication', array('action'=>'index'));
            }
            
        }
        return new ViewModel(array("form"=>$form,"details"=>$noticedetails));
    }
    
    public function caAction()
    {
        $parameter = $this->getEvent()->getRouteMatch()->getParam('id');
        //list($action,$id) = explode("#", $parameter);
        return new ViewModel(array("msg"=>"Are you sure you want to delete the item?","parameters"=>$parameter));
        //$this->redirect()->toRoute('communication', array('action'=>$action,"id"=>$id));
    }
    
    public function dlnAction(){
        
        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        $this->cm->deletefromdb("\Application\Entity\Notice",$id);
        
        $this->flashMessenger()->addSuccessMessage("Notice successfully removed");
        $this->redirect()->toRoute('communication', array('action'=>'index'));
    }
    
    public function rlAction()
    {
        $successMsg = "";
        $flashMessenger = $this->flashMessenger();
        if($flashMessenger->hasSuccessMessages()){
            $successMsg = implode("<br>", $flashMessenger->getSuccessMessages());
        }
        
        $regulations = $this->em->getRepository("\Application\Entity\Regulation")->findAll();
        return new ViewModel(array("regulations"=>$regulations,"msg"=>$successMsg));
    }
    
}
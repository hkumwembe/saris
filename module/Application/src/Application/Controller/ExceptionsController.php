<?php
namespace Application\Controller;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TestController
 *
 * @author hkumwembe
 */
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ExceptionsController extends AbstractActionController {
    private $message;
    private $pageTitle;
    
    public function __construct() {
        
    }
    
    public function onDispatch(\Zend\Mvc\MvcEvent $e) {
        $this->message  = "";
        $this->pageTile = "";
        $flashMessenger = $this->flashMessenger();
        if($flashMessenger->hasMessages()){
            $messages = $flashMessenger->getMessages(); 
            $this->layout()->setVariables(array("activemodule"=>$messages[0]));
            $this->message    = $messages[1];
            $this->pageTile   = $messages[2];
        }
        //$this->layout()->setVariables(array("activemodule"=>$this->getEvent()->getRouteMatch()->getMatchedRouteName()));
        parent::onDispatch($e);
    }
    
    public function indexAction() {
        return new ViewModel();
    }
    
    public function errorAction() {
        $array = "Test";
        return new ViewModel(array("title"=>$array));
    }
    
    public function accessdeniedAction() {
        return new ViewModel();
    }
    
    public function exceptionAction() {
        return new ViewModel(array("message"=>$this->message,"title"=>$this->pageTile));
    }
    
    
}

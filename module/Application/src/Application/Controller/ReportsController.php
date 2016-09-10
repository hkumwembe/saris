<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Zend\Authentication\AuthenticationService;
use Application\Model\pdf\Registeredlist;


class ReportsController extends AbstractActionController{
    
    protected $em;
    protected $request;
    protected $session;
    protected $examsession;

    public function __construct(\Doctrine\ORM\EntityManager $em) {
        $this->session = new Container('ADMISSION');
        $this->examsession = new Container('EXAM');
        $this->em      = $em;
        $this->request = $this->getRequest();
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
    /*
     * PDF report for registered student
     */
    public function rlAction(){
        $pdfreport  = new Registeredlist();
        $students   = $this->session->students;
        $title      = "Bachelor of Science in Information Systems year 1 - Registered";
        $headers   = array(array("W"=>12,"V"=>"Count"),array("W"=>45,"V"=>"Registration #"),array("W"=>40,"V"=>"Surname"),array("W"=>40,"V"=>"First name"),array("W"=>13,"V"=>"Gender"),array("W"=>50,"V"=>"Entry manner"),array("W"=>50,"V"=>"Registration date"));
        $pdfreport->generateReport($headers,$students,$title);
    }
    
    /*
     * PDF report for unregistered student
     */
    public function plAction(){
        $pdfreport  = new Registeredlist();
        $students   = $this->session->ustudents;
        $title      = "Bachelor of Science in Information Systems year 1 - Pending registration";
        $headers   = array(array("W"=>12,"V"=>"Count"),array("W"=>45,"V"=>"Registration #"),array("W"=>40,"V"=>"Surname"),array("W"=>40,"V"=>"First name"),array("W"=>13,"V"=>"Gender"),array("W"=>50,"V"=>"Entry manner"),array("W"=>50,"V"=>"Registration date"));
        $pdfreport->generateReport($headers,$students,$title);
    }
    
    /*
     * PDF class subject performance
     */
    public function spAction(){
        
        $pdfreport     = new Registeredlist();
        $performance   = $this->examsession->performance;
        $title         = "Bachelor of Science in Information Systems year 1 - Subject performance";
        $headers   = array(array("W"=>12,"V"=>"Count"),array("W"=>45,"V"=>"Registration #"),array("W"=>35,"V"=>"Exam #"),array("W"=>40,"V"=>"Surname"),array("W"=>40,"V"=>"First name"),array("W"=>13,"V"=>"Gender"),array("W"=>15,"V"=>"CWK"),array("W"=>15,"V"=>"EXAM"),array("W"=>20,"V"=>"Final grade"),array("W"=>20,"V"=>"Remark"));
        
    $pdfreport->subjectPerformance($headers,$performance,$title);
    }
    
    /*
     * Generate class performance summary
     */
    public function cpsAction(){
        $pdfreport     = new \Application\Model\pdf\Gradebook();
        $modulelist    = $this->examsession->modulelist;
        $class         = $this->examsession->class;
        $students      = $this->examsession->students;
        
        $pdfreport->generateGradeBook($students,$modulelist,$class);
    }
    
    
    
}

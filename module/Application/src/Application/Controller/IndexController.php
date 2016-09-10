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
use HighRoller\ColumnChart;
use HighRoller\SeriesData;
use Zend\View\Model\JsonModel;
use Zend\Soap\Client;


class IndexController extends AbstractActionController
{
    protected $em;
    protected $cs;
    
    public function __construct(\Doctrine\ORM\EntityManager $em,  \Application\Service\Security $cs) {
        $this->em = $em;
        $this->cs = $cs;
    }

    public function onDispatch(\Zend\Mvc\MvcEvent $e) {
        
        $this->authservice = new AuthenticationService();
        if(!$this->authservice->hasIdentity()){
            $this->redirect()->toRoute("login",array('action'=>'index'));
        }
        
        $this->layout()->setVariables(array("activemodule"=>$this->getEvent()->getRouteMatch()->getMatchedRouteName()));
        parent::onDispatch($e);
    }
    
    public function indexAction()
    {
//        $options = array(
//            'location' => 'http://saris.medcol.mw/pages/exams/mainServer.php',
//            'uri'      => 'http://saris.medcol.mw/pages/exams/'
//        );
//        $client = new Client(null,$options);
//        $regNo = '201250031969';
//        print_r($client->getAccountStatement($regNo,array(array('[COMDAT].[dbo].[AROBL].IDCUST'=>$regNo))));
//        die();
        
        $notices = $notices = $this->em->getRepository("\Application\Entity\Notice")->findBy(array("accounttype"=>NULL));
        $chart  = new \Application\Model\Charts($this->em);
        return new ViewModel(array("pie"=>$chart->getEnrolledStatus(),"bar"=>$chart->getRegistrationStatus(),"notices"=>$notices));
        //return new ViewModel();
    }
    
    public function formAction(){
        return new ViewModel();
    }
    
    public function eventsAction(){
        $events[] = array("title"=>"Senate meeting","start"=>"2016-02-05","end"=>"2016-02-06");
        $events[] = array("title"=>"PSU Election","start"=>"2016-02-02","end"=>"2016-02-02");
        $events[] = array("title"=>"Semester 1 assessments","start"=>"2016-02-22","end"=>"2016-02-28");
        $events[] = array("title"=>"Semester 2 registration","start"=>"2016-03-10","end"=>"2016-03-18");
        $events[] = array("title"=>"Semester 2 opening","start"=>"2016-03-10");
        echo json_encode($events);
        die();
    }
    
    
    public function logoutAction()
    {
        $this->authservice->clearIdentity();
        return $this->redirect()->toRoute('login', array(
                         'action' => 'index'
                 ));
    }
    
    
}
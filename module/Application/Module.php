<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Doctrine\Common\Annotations;

class Module
{
    public function init(){
        $namespace = 'Gedmo\Mapping\Annotation';
        $lib       = 'vendor/gedmo/doctrine-extensions/lib';
        Annotations\AnnotationRegistry::registerAutoloadNamespace($namespace,$lib);
    }
    
    public function onBootstrap(MvcEvent $e)
    {
        
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    public function getControllerConfig()
    {
        return array('factories' => array(
            'Application\Controller\Index' => function ($sm){
                //$config = $sm->getServiceLocator()->get('config');
                
                $em = $sm->getServiceLocator()->get('doctrine.entitymanager.orm_default');
                $cs = $sm->getServiceLocator()->get('Application\Service\Security');
                $controller = new Controller\IndexController($em,$cs);
                return $controller;
            },
            'Application\Controller\Admission' => function ($sm){
                $em = $sm->getServiceLocator()->get('doctrine.entitymanager.orm_default');
                $sc = $sm->getServiceLocator()->get('Application\Service\Security');
                
                //Instantiate admission model
                $am = $sm->getServiceLocator()->get('Application\Model\Admission');
                $controller = new Controller\AdmissionController($em,$am,$sc);
                return $controller;
            },
             'Application\Controller\Accommodation' => function ($sm){
                $em = $sm->getServiceLocator()->get('doctrine.entitymanager.orm_default');
                //$cs = $sm->getServiceLocator()->get('Application\Service\Common');
                
                //Instantiate admission model
                //$am = $sm->getServiceLocator()->get('Application\Model\Accommodation');
                $controller = new Controller\AccommodationController($em);
                return $controller;
            },
            'Application\Controller\Login' => function ($sm){
                $em = $sm->getServiceLocator()->get('doctrine.entitymanager.orm_default');
                $cs = $sm->getServiceLocator()->get('Application\Service\Security');
                $controller = new Controller\LoginController($em,$cs);
                return $controller;
            },
            'Application\Controller\Examination' => function ($sm){
                $em = $sm->getServiceLocator()->get('doctrine.entitymanager.orm_default');
                $controller = new Controller\ExaminationController($em);
                return $controller;
            },
            'Application\Controller\Reports' => function ($sm){
                $em = $sm->getServiceLocator()->get('doctrine.entitymanager.orm_default');
                $controller = new Controller\ReportsController($em);
                return $controller;
            },
            'Application\Controller\Communication' => function ($sm){
                $em = $sm->getServiceLocator()->get('doctrine.entitymanager.orm_default');
                $controller = new Controller\CommunicationController($em);
                return $controller;
            },
            'Application\Controller\Sponsorship' => function ($sm){
                $em = $sm->getServiceLocator()->get('doctrine.entitymanager.orm_default');
                $controller = new Controller\SponsorshipController($em);
                return $controller;
            },
            'Application\Controller\Finance' => function ($sm){
                $em = $sm->getServiceLocator()->get('doctrine.entitymanager.orm_default');
                $cs = $sm->getServiceLocator()->get('Application\Service\Security');
                $controller = new Controller\FinanceController($em,$cs);
                return $controller;
            },
            
            'Application\Controller\Administration' => function ($sm){
                $em = $sm->getServiceLocator()->get('doctrine.entitymanager.orm_default');
                $cs = $sm->getServiceLocator()->get('Application\Service\Security');
                $controller = new Controller\AdministrationController($em,$cs);
                return $controller;
            }
        ));
    }
    
     public function getServiceConfig()
    {
         
         return array(
                'factories'=>array(
                   'Zend\Authentication\AuthenticationService'=> function($serviceManager) {
                       // If you are using DoctrineORMModule:
                       return $serviceManager->get('doctrine.authenticationservice.orm_default');
                   },
                   'Application\Model\Admission' => function($sm){
                       $em = $sm->get('doctrine.entitymanager.orm_default');
                       
                       $model = new Model\Admission($em);
                       return $model;
                   },
                   'Application\Model\Preferences' => function($sm){
                       $em = $sm->get('doctrine.entitymanager.orm_default');
                       
                       $model = new Model\Preferences($em);
                       return $model;
                   }
                ),
               'invokables' => array(
                    'Application\Service\Security' => 'Application\Service\Security',
                    'Application\Model\Commonmodel' => 'Application\Model\Commonmodel'
                ),
          );
         
        
    }
    
    
}

<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Factory;

/**
 * Description of ExaminationFactory
 *
 * @author hkumwembe
 */

use Application\Controller\ExaminationController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


class ExaminationFactory implements FactoryInterface {
    
    public function createService(ServiceLocatorInterface $serviceLocator) {
        $sm   = $serviceLocator->getServiceLocator();
        $qm   = $sm->get("SlmQueue\Queue\QueuePluginManager");
        $sq   = $qm->get("SlmQueueDoctrine\Factory\DoctrineQueueFactory");
        $em   = $sm->get('doctrine.entitymanager.orm_default');
        
        return new ExaminationController($em,$sq);
    }
    
}

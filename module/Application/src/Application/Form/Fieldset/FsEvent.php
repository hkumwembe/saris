<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Form\Fieldset;

/**
 * Description of Calendar event fieldset
 *
 * @author hkumwembe
 */

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;
use Application\Entity\Calendarevent;


class FsEvent extends Fieldset implements InputFilterProviderInterface
{
    protected $em;
    
    function getEventTypes(){
        $options = array();
        $events = $this->em->getRepository("\Application\Entity\Eventtype")->findAll();
        foreach ($events as $event){
            $options[$event->getPkEventtypeid()] = $event->getDescription();
        }
        
        return $options; 
    }


    
    public function __construct(\Doctrine\ORM\EntityManager $em = null)
    {
        $this->em = $em;
        
        parent::__construct('Event');
        
        $this->setHydrator(new ClassMethodsHydrator(false))
            ->setObject(new Calendarevent());
        
        $this->add(array(
            'name' => 'start',
            'type' => 'Zend\Form\Element\Date',
            'options' => array(
                'label' => 'Start date:* '
            ),
            'attributes' => array(
                'required' => 'required',
                'class'    => 'form-control',
                'id'    => 'start'
            )
        ));
        
        $this->add(array(
            'name' => 'end',
            'type' => 'Zend\Form\Element\Date',
            'options' => array(
                'label' => 'End date:* '
            ),
            'attributes' => array(
                'required' => 'required',
                'class'    => 'form-control',
                'id'    => 'end'
            )
        ));
        
        $this->add(array(
            'name' => 'pkEventid',
            'type' => 'hidden'
        ));  
            
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'targetgroup',
            'options' => array(
                'label' => 'Target group: ',
                'value_options' => array("STUDENT"=>"Student","STAFF"=>"Staff"),
                'empty_option'  => "--Select--"
            ),
            'attributes' => array(
                'class'    => 'form-control',
                
            )
        ));  
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'fkEventtypeid',
            'options' => array(
                'label' => 'Event:*',
                'value_options' => $this->getEventTypes(),
                'empty_option'  => "--Select--"
            ),
            'attributes' => array(
                'class'    => 'form-control',
                'required' => 'required',
            )
        ));  
        
    }

    /**
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return array(
            'fkEventtypeid' => array(
                'required' => true
            ),
            'start' => array(
                'required' => true
            ),
            'end' => array(
                'required' => true
            ),
            'targetgroup' => array(
                'required' => false
            )
        );
    }
}

<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Form\Fieldset\Accom;

/**
 * Description of Login fieldset
 *
 * @author hkumwembe
 */

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;
use Application\Entity\Hostel;


class FsHostel extends Fieldset implements InputFilterProviderInterface
{
    protected $em;
    
    /*
     * Fetch list of campus
     */
    public function getCampuses(){
        
        $options = array();
        
        $objects = $this->em->getRepository('\Application\Entity\Campus')->findAll();
        foreach($objects as $object ){
            $options[$object->getPkCampusid()] = $object->getCampusName();
        }
        
        return $options;
    }
    
    /*
     * Fetch list of campus
     */
    public function getCategories(){
        
        $options = array();
        
        $objects = $this->em->getRepository('\Application\Entity\Hostelcategory')->findAll();
        foreach($objects as $object ){
            $options[$object->getPkHtid()] = $object->getDescription();
        }
        
        return $options;
    }
    
    
    public function __construct(\Doctrine\ORM\EntityManager $em = null)
    {
        $this->em = $em;
        
        parent::__construct('Hostel');
        
        
        
        $this->setHydrator(new ClassMethodsHydrator(false))
            ->setObject(new Hostel());
        
        
        $this->add(array(
            'name' => 'hostelName',
            'options' => array(
                'label' => 'Name:* '
            ),
            'attributes' => array(
                'required' => 'required',
                'class'    => 'form-control',
                
            )
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'fkCampusid',
            'options' => array(
                'label' => 'Campus: ',
                'value_options' => $this->getCampuses(),
                'empty_option'  => '--Select--'
            ),
            'attributes' => array(
                //'required' => 'required',
                'class'    => 'form-control',
                
                
            )
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'fkHtid',
            'options' => array(
                'label' => 'Category: * ',
                'value_options' => $this->getCategories(),
                'empty_option'  => '--Select--'
            ),
            'attributes' => array(
                'required' => 'required',
                'class'    => 'form-control',
                
                
            )
        ));
        
        
        $this->add(array(
            'name' => 'pkHostelid',
            'type' => 'hidden',
            'options' => array(
                'label' => ' '
            ),
        ));  
        
    }

    /**
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return array(
            
            'fkCampusid' => array(
                'required' => true
            ),
            'fkHtid' => array(
                'required' => true
            ),
            'hostelName' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'DoctrineModule\Validator\UniqueObject',
                        'options' => array(
                            'use_context' => true,
                            'object_repository' => $this->em->getRepository('Application\Entity\Hostel'),
                            'fields' => 'hostelName',
                            'object_manager' => $this->em,
                            'message' => 'Hostel name already exists in the system'
                        )
                    )
                ) 
            )
            
            
            
        );
    }
}

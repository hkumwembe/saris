<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Form\Fieldset\Communication;

/**
 * Description of Notice fieldset
 *
 * @author hkumwembe
 */

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;
use Application\Entity\Notice;


class FsNotice extends Fieldset implements InputFilterProviderInterface
{
    protected $em;
    
    public function __construct(\Doctrine\ORM\EntityManager $em = null)
    {
        $this->em = $em;
        
        parent::__construct('Notice');
        
        
        
        $this->setHydrator(new ClassMethodsHydrator(false))
            ->setObject(new Notice());
        
        $this->add(array(
            'name' => 'header',
            'options' => array(
                'label' => 'Title:* '
            ),
            'attributes' => array(
                'required' => 'required',
                'class'    => 'form-control'
            )
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Textarea',
            'name' => 'body',
            'options' => array(
                'label' => 'Body:* '
            ),
            'attributes' => array(
                'required' => 'required',
                'class'    => 'ckeditor',
                'id'    => 'noticebody',
                
            )
        ));
        
        $this->add(array(
            'name' => 'pkNoticeid',
            'type' => 'hidden'
        ));  
            
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'accountType',
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
            'name' => 'isActive',
            'options' => array(
                'label' => 'Notice status: ',
                'value_options' => array("1"=>"Active","0"=>"Disabled"),
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
            'body' => array(
                'required' => true
            ),
            'header' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'DoctrineModule\Validator\UniqueObject',
                        'options' => array(
                            'use_context' => true,
                            'object_repository' => $this->em->getRepository('Application\Entity\Notice'),
                            'fields' => 'header',
                            'object_manager' => $this->em,
                            'message' => 'Duplicate title in the system'
                        )
                    )
                ) 
            ),
            'accountType' => array(
                'required' => false
            ),
            'isActive' => array(
                'required' => true
            ),
        );
    }
}

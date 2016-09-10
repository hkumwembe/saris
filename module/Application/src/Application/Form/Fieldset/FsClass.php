<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Form\Fieldset;

/**
 * Description of Login fieldset
 *
 * @author hkumwembe
 */

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;
use Application\Entity\Classes;


class FsClass extends Fieldset implements InputFilterProviderInterface
{
    protected $em;
    
     /*
     * Fetch list of users
     */
    public function getStudymode(){
        $options = array();
        $modes = $this->em->getRepository("\Application\Entity\Studymode")->findAll();

        foreach($modes as $mode ){
            $options[$mode->getPkStudymodeid()] = $mode->getTitle();
        }
        
        return $options;
    }
    
    
    public function __construct(\Doctrine\ORM\EntityManager $em = null)
    {
        $this->em = $em;
        
        parent::__construct('Class');
        
        
        
        $this->setHydrator(new ClassMethodsHydrator(false))
            ->setObject(new Classes());
        
        
        $this->add(array(
            'name' => 'pkClassid',
            'type' => 'hidden'
        ));
        
        $this->add(array(
            'name' => 'classCode',
            'options' => array(
                'label' => 'Class code:*'
            ),
            'attributes' => array(
                'required' => 'required',
                'class'    => 'form-control',
                //'placeholder' => 'Username'
            )
        ));
        
        $this->add(array(
            'name' => 'className',
            'options' => array(
                'label' => 'Class name:*'
            ),
            'attributes' => array(
                'required' => 'required',
                'class'    => 'form-control',
                //'placeholder' => 'Username'
            )
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Number',
            'name' => 'classYear',
            'options' => array(
                'label' => 'Year:*'
            ),
            'attributes' => array(
                'required' => 'required',
                'class'    => 'form-control',
                //'placeholder' => 'Username'
            )
        ));
        
        $this->add(array(
            'name' => 'fkProgramid',
            'type' => 'hidden',
            'options' => array(
                'label' => ' '
            ),
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'fkTmodeid',
            'options' => array(
                'label' => 'Teaching mode: ',
                'value_options' => $this->getStudymode(),
                'empty_option'  => "--Select--"
            ),
            'attributes' => array(
                'class'    => 'form-control',
                
            )
        ));  
        
        
        
    }

    /**
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return array(
            'fkTmodeid' => array(
                'required' => true
            ),
            'fkProgramid' => array(
                'required' => true
            ),
            'className' => array(
                'required' => true
            ),
            'classYear' => array(
                'required' => true
            ),
            'classCode' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'DoctrineModule\Validator\UniqueObject',
                        'options' => array(
                            'use_context' => true,
                            'object_repository' => $this->em->getRepository('Application\Entity\Classes'),
                            'fields' => 'classCode',
                            'object_manager' => $this->em,
                        )
                    )
                ) 
            )
            
        );
    }
}

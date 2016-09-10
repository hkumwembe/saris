<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Form\Fieldset\Exams;

/**
 * Description of Login fieldset
 *
 * @author hkumwembe
 */

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;
use Application\Entity\Gradeflow;


class FsFlow extends Fieldset implements InputFilterProviderInterface
{
    protected $em;
    
    /*
     * Fetch list of programs categories
     */
    public function getRoles(){
        
        $options = array();
        
        $objects = $this->em->getRepository('\Application\Entity\Role')->findBy(array("isFacultyRole"=>'1'));
        foreach($objects as $object ){
            $options[$object->getPkRoleid()] = $object->getDescription();
        }
        
        return $options;
    }
    
    
    public function __construct(\Doctrine\ORM\EntityManager $em = null)
    {
        $this->em = $em;
        
        parent::__construct('Flow');
        
        
        
        $this->setHydrator(new ClassMethodsHydrator(false))
            ->setObject(new Gradeflow());
        
        $this->add(array(
            'name' => 'level',
            'type' => 'Number',
            'options' => array(
                'label' => 'Level:* '
            ),
            'attributes' => array(
                'required' => 'required',
                'class'    => 'form-control',
                'readonly' => 'readonly'
                
                
            )
        ));
        
        $this->add(array(
            'name' => 'description',
            'options' => array(
                'label' => 'Stage description:* '
            ),
            'attributes' => array(
                'required' => 'required',
                'class'    => 'form-control',
                
                
            )
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'fkRoleid',
            'options' => array(
                'label' => 'Role:* ',
                'value_options' => $this->getRoles(),
                'empty_option'  => '--Select--'
            ),
            'attributes' => array(
                'required' => 'required',
                'class'    => 'form-control',
                
                
            )
        ));
        
        
        $this->add(array(
            'name' => 'pkGradeflowid',
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
            'fkRoleid' => array(
                'required' => true
            ),
            'description' => array(
                'required' => false
            ),
            'level' => array(
                'required' => true
            )
        );
    }
}

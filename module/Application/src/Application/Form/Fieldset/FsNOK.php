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
use Application\Entity\Studentcontact;


class FsNOK extends Fieldset implements InputFilterProviderInterface
{
    protected $em;
    
    public function getRelationship(){
        $options = $this->em->getRepository('\Application\Entity\Relation')->findAll();
        foreach($options as $option ){
            $list[$option->getPkRelationid()] = $option->getRelationship();//$option->getNationality();
        }
        
        return $list;
    }
    
    public function __construct(\Doctrine\ORM\EntityManager $em = null)
    {
        $this->em = $em;
        
        parent::__construct('NOK');
        
        $this->setHydrator(new ClassMethodsHydrator(false))
            ->setObject(new Studentcontact());
        
        
        $this->add(array(
            'name' => 'pkContactid',
            'type' => 'hidden',
            'options' => array(
                'label' => ' '
            ),
        ));
        
        $this->add(array(
            'name' => 'fkStudentid',
            'type' => 'hidden',
            'options' => array(
                'label' => ' '
            ),
        ));
        
       /*
         * Next of kin
         */
         $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'noktitle',
            'options' => array(
                'label' => 'Title:*',
                'value_options' => array(
                             'Dr.'   => 'Dr.',
                             'Prof.' => 'Prof.',
                             'Mrs'   => 'Mrs',
                             'Mr'    => 'Mr',
                             'Miss'  => 'Miss',       
                     ),
                'empty_option' => "--Title---",
            ),
            'attributes' => array(
                'required' => 'required',
                'class'    => 'form-control',
                
            )
        ));
        
        $this->add(array(
            'name' => 'noksurname',
            'options' => array(
                'label' => 'Surname:*',
                
            ),
            'attributes' => array(
                'required' => 'required',
                'class'    => 'form-control',
                'placeholder' => 'Surname'
            )
        ));
        
        $this->add(array(
            'name' => 'nokfirstname',
            'options' => array(
                'label' => 'Firstname:*'
            ),
            'attributes' => array(
                'required' => 'required',
                'class'    => 'form-control',
                'placeholder' => 'Firstname'
            )
        ));
        
        
        $this->add(array(
            'name' => 'nokpostaladdress',
            'type' => 'textarea',
            'options' => array(
                'label' => 'Postal address:*'
            ),
            'attributes' => array(
                'required' => 'required',
                'class'    => 'form-control',
            )
        ));
        
        $this->add(array(
            'name' => 'nokmobile',
            'type' => 'text',
            'options' => array(
                'label' => 'Mobile phone:*'
            ),
            'attributes' => array(
                'required' => 'required',
                'class'    => 'form-control',
            )
        ));
        
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Email',
            'name' => 'nokemail',
            'options' => array(
                'label' => 'Email address:*'
            ),
            'attributes' => array(
                'class'    => 'form-control',
                //'required' => 'required',
                
            )
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'nokrelationship',
            'options' => array(
                'label' => 'Relationship:*',
                'value_options' => $this->getRelationship(),
                'empty_option' => "--Relationship--",
            ),
            'attributes' => array(
                'required' => 'required',
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
//            'dob' => array(
//                'required' => true
//            ),
//           'maritalStatus' => array(
//                'required' => true
//            ),
//            'fkDistrictid' => array(
//                'required' => true
//            ),
//            'fkCountryid' => array(
//                'required' => true
//            ),
        );
    }
}

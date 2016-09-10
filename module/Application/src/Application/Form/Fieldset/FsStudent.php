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
use Application\Entity\Student;


class FsStudent extends Fieldset implements InputFilterProviderInterface
{
    protected $em;
    
    public function getDistricts(){
        $options = $this->em->getRepository('\Application\Entity\District')->findAll();
        foreach($options as $option ){
            $list[$option->getPkDistrictid()] = $option->getDistrictName();
        }
        
        return $list;
    }
    
    public function getCountries(){
        $options = $this->em->getRepository('\Application\Entity\Country')->findBy(array(),array("countryName"=>"ASC"));
        foreach($options as $option ){
            $list[$option->getPkCountryid()] = $option->getCountryName();//$option->getNationality();
        }
        
        return $list;
    }
    
     public function getReligion(){
        $options = $this->em->getRepository('\Application\Entity\Religion')->findAll();
        foreach($options as $option ){
            $list[$option->getPkReligionid()] = $option->getReligionName();//$option->getNationality();
        }
        
        return $list;
    }
    
    public function getMaritalstatus(){
        $options = $this->em->getRepository('\Application\Entity\Maritalstatus')->findAll();
        foreach($options as $option ){
            $list[$option->getPkMaritalstatusid()] = $option->getStatusTitle();//$option->getNationality();
        }
        
        return $list;
    }
    
    public function __construct(\Doctrine\ORM\EntityManager $em = null)
    {
        $this->em = $em;
        
        parent::__construct('Student');
        
        $this->setHydrator(new ClassMethodsHydrator(false))
            ->setObject(new Student());
        
        
        $this->add(array(
            'name' => 'pkStudentid',
            'type' => 'hidden',
            'options' => array(
                'label' => ' '
            ),
        ));
        
        $this->add(array(
            'name' => 'fkUserid',
            'type' => 'hidden',
            'options' => array(
                'label' => ' '
            ),
        ));
        
        $this->add(array(
            'name' => 'dob',
            'type' => 'date',
            'options' => array(
                'label' => 'Date of birth:*'
            ),
            'attributes' => array(
                'required' => 'required',
                'class'    => 'form-control',
                //'placeholder' => 'Username'
            )
        ));
        
        $this->add(array(
            'name' => 'ta',
            'options' => array(
                'label' => 'TA:'
            ),
            'attributes' => array(
                'class'    => 'form-control',
            )
        ));
        
        $this->add(array(
            'name' => 'village',
            'options' => array(
                'label' => 'Village:'
            ),
            'attributes' => array(
                'class'    => 'form-control',
            )
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'fkDistrictid',
            'options' => array(
                'label' => 'District:*',
                'value_options' => $this->getDistricts(),
                'empty_option'=>'--Select district--',
            ),
            'attributes' => array(
                'required' => 'required',
                'class'    => 'form-control', 
            )
        ));
        
         $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'fkReligionid',
            'options' => array(
                'label' => 'Religion:*',
                'value_options' => $this->getReligion(),
                'empty_option'=>'--Select--',
            ),
            'attributes' => array(
                'required' => 'required',
                'class'    => 'form-control', 
            )
        ));
         
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'maritalStatus',
            'options' => array(
                'label' => 'Marital status:*',
                'value_options' => $this->getMaritalstatus(),
                'empty_option'=>'--Select--',
            ),
            'attributes' => array(
                'required' => 'required',
                'class'    => 'form-control',
                //'placeholder' => 'Username'
            )
        ));
        
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'fkCountryid',
            'options' => array(
                'label' => 'Nationality:*',
                'value_options' => $this->getCountries(),
                'empty_option'=>'--Select--',
            ),
            'attributes' => array(
                'required' => 'required',
                'class'    => 'form-control',
                //'placeholder' => 'Username'
            )
        ));
        
        $this->add(array(
             'name' => 'basicdetails',
             'type' => 'Application\Form\Fieldset\FsBasicUserDetails'
         ));
        
              
    }

    /**
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return array(
            'dob' => array(
                'required' => true
            ),
           'maritalStatus' => array(
                'required' => true
            ),
            'fkDistrictid' => array(
                'required' => true
            ),
            'fkCountryid' => array(
                'required' => true
            ),
        );
    }
}

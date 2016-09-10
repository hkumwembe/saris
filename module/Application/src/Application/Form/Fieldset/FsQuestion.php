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


class FsQuestion extends Fieldset implements InputFilterProviderInterface
{
    protected $em;
    protected $module;

    /*
     * Fetch list of staff
     */
    public function getStaff(){
        
        $options = array();
        $results = $this->em->getRepository("\Application\Entity\Staff")->findAll();
        foreach($results as $result){
             $options[$result->getPkStaffid()] = sprintf("%s %s",$result->getFkUserid()->getSurname(),$result->getFkUserid()->getFirstname());
        }
        
         return $options;
        
    }
    
    /*
     * Fetch list of exam paper
     */
    public function getPaper(){
        
        $options = array();
        $results = $this->em->getRepository("\Application\Entity\Exampaper")->findAll();
        foreach($results as $result){
             $options[$result->getPkPaperid()] = $result->getPaperName();
        }
        
         return $options;
        
    }


    public function __construct(\Doctrine\ORM\EntityManager $em = null)
    {
        $this->em = $em;
        
        parent::__construct('Question');
        
        $this->setHydrator(new ClassMethodsHydrator(false))
            ->setObject(new \Application\Entity\Question());
        
        
        $this->add(array(
            'name' => 'question',
            'type' => 'Zend\Form\Element\Textarea',
            'options' => array(
                'label' => 'Question:* '
            ),
            'attributes' => array(
                'required' => 'required',
                'class'    => 'form-control',
                'id'       =>'title'
            )
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'fkStaffid',
            'options' => array(
                'label' => 'Examiner:*',
                'value_options' => $this->getStaff(),
                'empty_option'  => "--Select--"
            ),
            'attributes' => array(
                'required' => 'required',
                'class'    => 'form-control',
                'id'     => 'atid'
                
            )
        ));
         
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'fkPaperid',
            'options' => array(
                'label' => 'Paper:*',
                'value_options' => $this->getPaper(),
                'empty_option'  => "--Select--"
            ),
            'attributes' => array(
                'required' => 'required',
                'class'    => 'form-control',
                'id'     => 'atid'
                
            )
        ));
        
        $this->add(array(
             'name'         => 'markOutOf',
             'type'         => 'Zend\Form\Element\Number',
             'attributes'   => array('class'=> 'form-control','id'=>'markOutOf',
                 'required' => 'required',
                 ),
	     'options'      => array('label' => 'Mark out of*:',),
         ));
        
        $this->add(array(
             'name'         => 'questionNumber',
             'type'         => 'Zend\Form\Element\Number',
             'attributes'   => array('class'=> 'form-control','id'=>'questionNumber',
                 'required' => 'required',
                 ),
	     'options'      => array('label' => 'Question number*:',),
         ));
         
         $this->add(array(
            'name' => 'pkQid',
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
            'markOutOf' => array(
                'required' => true
            ),
            'questionNumber' => array(
                'required' => true
            ),
            'question' => array(
                'required' => true
            ),
             'fkStaffid' => array(
                'required' => true
            ),
            'fkPaperid' => array(
                'required' => true
            ),

        );
    }
}

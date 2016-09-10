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


class FsAssessment extends Fieldset implements InputFilterProviderInterface
{
    protected $em;
    protected $module;


    /*
     * Fetch list of program group
     */
    public function getTypes(){
        
        $types = array();
         $query = $this->em->createQuery("SELECT I FROM \Application\Entity\Assessmenttype I "
                              . " WHERE I.systemGenerated = '0' "
                              . " AND I.pkAtid NOT IN (SELECT IDENTITY(A.fkAtid) FROM \Application\Entity\Assessmentitem A"
                              . " JOIN A.fkAtid AT "
                             // . " JOIN A.fkClassmoduleid M"
                              . " WHERE AT.existOnce = '1' AND A.fkClassmoduleid = :moduleid )")
                  ->setParameter("moduleid", $this->module->getPkClassmoduleid());
         
         foreach($query->getResult() as $result){
             $types[$result->getPkAtid()] = $result->getTypeName();
         }
        
         return $types;
        
    }


    public function __construct(\Doctrine\ORM\EntityManager $em = null,$module = null)
    {
        $this->em = $em;
        $this->module = $module;
        
        parent::__construct('Assessment');
        
        $this->setHydrator(new ClassMethodsHydrator(false))
            ->setObject(new \Application\Entity\Assessmentitem());
        
        
        $this->add(array(
            'name' => 'assessmentTitle',
            'options' => array(
                'label' => 'Title:* '
            ),
            'attributes' => array(
                'required' => 'required',
                'class'    => 'form-control',
                'id'       =>'title'
            )
        ));
        
        $this->add(array(
            'name' => 'shortName',
            'options' => array(
                'label' => 'Short title: '
            ),
            'attributes' => array(
                'class'    => 'form-control',
            )
        ));
        
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'fkAtid',
            'options' => array(
                'label' => 'Assessment type:*',
                'value_options' => $this->getTypes(),
                'empty_option'  => "--Select--"
            ),
            'attributes' => array(
                'required' => 'required',
                'class'    => 'form-control',
                'id'     => 'atid'
                
            )
        ));
         
         
         $this->add(array(
            'name' => 'fkClassmoduleid',
            'type' => 'hidden',
            'options' => array(
                'label' => ' '
            ),
        ));
         
        $this->add(array(
             'name'         => 'weighting',
             'type'         => 'Zend\Form\Element\Number',
             'attributes'   => array('class'=> 'form-control','id'=>'weighting','required' => 'required',),
	     'options'      => array('label' => 'Weighting(%)*:',),
         ));
         
         $this->add(array(
            'name' => 'pkAiid',
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
            'fkAtid' => array(
                'required' => true
            ),
            'assessmentTitle' => array(
                'required' => true
            ),

        );
    }
}

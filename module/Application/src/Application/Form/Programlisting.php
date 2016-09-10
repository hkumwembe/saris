<?php
namespace Application\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class Programlisting extends Form
 {
     private $em;
     private $dept;


     //Get programs
     public function getPrograms(){
         $types = array();
         $query = $this->em->createQuery("SELECT P FROM \Application\Entity\Program P "
                              . " WHERE P.fkDeptid = :department ")
                  ->setParameter("department", $this->dept);
         
         foreach($query->getResult() as $result){
             $types[$result->getPkProgramid()] = $result->getProgramCode();
         }
        
         return $types;
     }
     
     //Get classes
     public function getClasses(){
         $types = array();
         $query = $this->em->createQuery("SELECT C FROM \Application\Entity\Classes C "
                                       . "JOIN C.fkProgramid P "
                                       . " WHERE P.fkDeptid = :department ")
                  ->setParameter("department", $this->dept);
         
         foreach($query->getResult() as $result){
             $types[$result->getPkClassid()] = $result->getClassCode();
         }
        
         return $types;
     }
     
     
     
     

     public function __construct(\Doctrine\ORM\EntityManager $em = null,$department)
     {
         // we want to ignore the name passed
         parent::__construct('type');
         $this->em = $em; 
         $this->dept = $department;
	
         $this->setAttribute('method', 'post')
             ->setHydrator(new ClassMethodsHydrator(false))
             ->setInputFilter(new InputFilter());

        $this->add(array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'program',
                'options' => array(
                    'label' => 'Select assessment item',
                    'value_options' => $this->getPrograms(),
                    'empty_option'  => '--Select--'
                ),
                'attributes' => array(
                'id' => 'program',
                'class' => 'form-control'
                )
            ));
        
        $this->add(array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'class',
                'options' => array(
                    'value_options' => $this->getClasses(),
                    'empty_option'  => '--Select--'
                ),
                'attributes' => array(
                'id' => 'class',
                'class' => 'form-control',
                'required' => true
                )
            ));
        
           
     }
 }

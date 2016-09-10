<?php
namespace Application\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class NationalitySearch extends Form
 {
     private $em;

     //Get years
     public function getAcademicYears(){
         $options = array();
         $query = $this->em->createQuery("SELECT A FROM \Application\Entity\Academicyear A "
                              . " WHERE A.parentid IS NULL");
        
         foreach($query->getResult() as $result){
             $options[$result->getPkAcademicperiodid()] = $result->getAcyr();
         }
        
         return $options;
     }
     
     //Get semesters
     public function getSemesters(){
         $options = array();
         $query = $this->em->createQuery("SELECT A FROM \Application\Entity\Academicyear A "
                              . " WHERE A.pkAcademicperiodid not in(SELECT CASE WHEN (AC.parentid IS NULL) THEN 0 ELSE IDENTITY(AC.parentid) END FROM \Application\Entity\Academicyear AC) ");
         
         foreach($query->getResult() as $result){
             $options[$result->getAcyr()] = $result->getAcyr();
         }
        
         return $options;
     }
     
     //Get student type
     public function getNationality(){
         $options = array();
         $results = $this->em->getRepository("\Application\Entity\Country")->findAll();
         
         foreach($results as $result){
             $options[$result->getPkCountryid()] = $result->getNationality();
         }
        
         return $options;
     }
     
     

     public function __construct(\Doctrine\ORM\EntityManager $em = null)
     {
         // we want to ignore the name passed
         parent::__construct('search');
         $this->em = $em; 
	
         $this->setAttribute('method', 'post')
             ->setHydrator(new ClassMethodsHydrator(false))
             ->setInputFilter(new InputFilter());
         
         
         
        $this->add(array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'nationality',
                'options' => array(
                    'label' => 'Student type',
                    'value_options' => $this->getNationality(),
                    'empty_option'  => '--Select--'
                ),
                'attributes' => array(
                'id' => 'nationality',
                'class' => 'form-control'
                )
            ));
        
        $this->add(array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'academicyear',
                'options' => array(
                    'value_options' => $this->getAcademicYears(),
                    'empty_option'  => '--Select--'
                ),
                'attributes' => array(
                'id' => 'academicyear',
                'class' => 'form-control',
                'required' => true
                )
            ));
        
        $this->add(array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'semester',
                'options' => array(
                    'value_options' => $this->getSemesters(),
                    'empty_option'  => '--Select--'
                ),
                'attributes' => array(
                'id' => 'semester',
                'class' => 'form-control',
                'required' => true
                )
            ));
           
     }
 }

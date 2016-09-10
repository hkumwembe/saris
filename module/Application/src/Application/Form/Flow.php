<?php
namespace Application\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class Flow extends Form
 {
     private $em;
     
     public function __construct(\Doctrine\ORM\EntityManager $em = null){
         
         // we want to ignore the name passed
         parent::__construct('frmflow');
         $this->em = $em; 
         //$this->deptid = $deptid;
	
         $this->setAttribute('method', 'post')
             ->setHydrator(new ClassMethodsHydrator(false))
             ->setInputFilter(new InputFilter());
         
         $fieldset = new Fieldset\Exams\FsFlow($em);
         $fieldset->setUseAsBaseFieldset(true);
         $this->add($fieldset);
     
    }
     
 }

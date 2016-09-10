<?php
namespace Application\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class Question extends Form 
 {
     private $em;
     
     public function __construct(\Doctrine\ORM\EntityManager $em = null)
     {
         // we want to ignore the name passed
         parent::__construct('frmquestion');
         $this->em = $em; 
	
         $this->setAttribute('method', 'post')
             ->setHydrator(new ClassMethodsHydrator(false))
             ->setInputFilter(new InputFilter());
         
         $fieldset = new Fieldset\FsQuestion($em);
         $fieldset->setUseAsBaseFieldset(true);
         $this->add($fieldset);
 
     }
     
 }

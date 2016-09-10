<?php
namespace Application\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class Assessment extends Form
 {
     private $em;
     
     public function __construct(\Doctrine\ORM\EntityManager $em = null,$module = null)
     {
         // we want to ignore the name passed
         parent::__construct('frmassessment');
         $this->em = $em; 
	
         $this->setAttribute('method', 'post')
             ->setHydrator(new ClassMethodsHydrator(false))
             ->setInputFilter(new InputFilter());

        $fieldset = new Fieldset\FsAssessment($em,$module);
        $fieldset->setUseAsBaseFieldset(true);
        $this->add($fieldset);

//        $this->add(array(
//            'name' => 'submit',
//            'attributes' => array(
//                'type' => 'submit',
//                'value' => 'Login',
//                'class' => 'btn btn-lg btn-primary btn-block'
//            )
//        ));
           
     }
 }

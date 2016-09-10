<?php
namespace Application\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class Registration extends Form
 {
     private $em;
     
     public function __construct(\Doctrine\ORM\EntityManager $em = null)
     {
         // we want to ignore the name passed
         parent::__construct('frmuser');
         $this->em = $em; 
	
         $this->setAttribute('method', 'post')
             ->setHydrator(new ClassMethodsHydrator(false))
             ->setInputFilter(new InputFilter());

        $fieldset = new Fieldset\FsStudent($em);
        $fieldset->setUseAsBaseFieldset(true);
        $this->add($fieldset);

        $classfieldset = new Fieldset\FsStudentContact($em);
        $this->add($classfieldset);
        
        $nokfieldset = new Fieldset\FsNOK($em);
        $this->add($nokfieldset);
        
        $emgfieldset = new Fieldset\FsEMG($em);
        $this->add($emgfieldset);
        
//        $empfieldset = new Fieldset\FsEmployment($em);
//        $this->add($empfieldset);
        
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Login',
                'class' => 'btn btn-lg btn-primary btn-block'
            )
        ));
           
     }
 }

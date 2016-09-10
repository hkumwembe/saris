<?php
namespace Application\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class Questionmark extends Form
 {
     
     public function __construct($studentcounter,$questions)
     {
         // we want to ignore the name passed
         parent::__construct('Marks');
	
         $this->setAttribute('method', 'post')
             ->setHydrator(new ClassMethodsHydrator(false))
             ->setInputFilter(new InputFilter());
         foreach($questions as $question){
            $this->add(array(
                'type' => 'Zend\Form\Element\Collection',
                'name' => $question->getPkQid(),
                'options' => array(
                    'count' => $studentcounter,
                    'should_create_template' => true,
                    'template_placeholder' => '__mark__',
                    'target_element' => new \Application\Form\Fieldset\FsAssessmentmark()
                )
            ));
         }
           
     }
 }

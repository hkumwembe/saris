<?php
namespace Application\Form\Fieldset;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class FsGenerateRegNo extends Fieldset
 {
    protected $em;
    
    public function getYears(){
        $years = $this->em->getRepository('\Application\Entity\Selectionlistupload')->findBy(array("regnumberAssigned"=>'0'));
        $options = array();
        foreach($years as $year){
            $options[$year->getFkAcademicperiodid()->getParentid()->getPkAcademicperiodid()] = $year->getFkAcademicperiodid()->getParentid()->getAcyr();
        }
        return $options;
    }

    public function __construct(\Doctrine\ORM\EntityManager $em = null)
    {
        $this->em         = $em;
         // we want to ignore the name passed
         parent::__construct('Registration');
         
//         $this->add(array(
//             'name'         => 'year',
//             'type' => 'Zend\Form\Element\Select',
//             'attributes'   => array('class'=> 'form-control','id'=>'servicingDept'),
//              'options' => array(
//                'label' => 'Year:*',
//                'value_options' => $this->getYears()
//              )
//         )); 
       
         
         $this->add(array(
            'type' => 'Zend\Form\Element\MultiCheckbox',
            'name' => 'process_steps',
            'options' => array(
                'label' => 'Study modes:',
                'value_options' => array(
                    '1'=> 'Generate registration numbers',
                    '2'=> 'Generate temporary usernames and passwords',
                    '3'=> 'Generate students financial accounts (Effective when student registers)',
                    '4'=> 'Email temporary usernames and passwords to students with emails',
                )
            )
        )); 
     }
     
     /**
     * @return array
     */
//    public function getInputFilterSpecification()
//    {
////        return array(
////            
////            'year' => array(
////                'required' => true
////            )
////        );
//    }
     
     
     
     
 }
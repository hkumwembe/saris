<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Form\Fieldset\Sponsor;

/**
 * Description of Login fieldset
 *
 * @author hkumwembe
 */

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;
use Application\Entity\Sponsor;


class FsSponsor extends Fieldset implements InputFilterProviderInterface
{
    protected $em;
    
    
    public function __construct(\Doctrine\ORM\EntityManager $em = null)
    {
        $this->em = $em;
        
        parent::__construct('Sponsor');
        
        
        
        $this->setHydrator(new ClassMethodsHydrator(false))
            ->setObject(new Sponsor());
        
        
        $this->add(array(
            'name' => 'sponsorName',
            'options' => array(
                'label' => 'Sponsor name:* '
            ),
            'attributes' => array(
                'required' => 'required',
                'class'    => 'form-control',
                
            )
        ));
        
        $this->add(array(
            'type' => 'TextArea',
            'name' => 'description',
            'options' => array(
                'label' => 'Description: '
            ),
            'attributes' => array(
                'class'    => 'form-control',
                
            )
        ));
        
        $this->add(array(
            'name' => 'contactPerson',
            'options' => array(
                'label' => 'Contact person: '
            ),
            'attributes' => array(
                'class'    => 'form-control',
                
            )
        ));
        
        $this->add(array(
            'type' => 'TextArea',
            'name' => 'postalAddress',
            'options' => array(
                'label' => 'Postal address: '
            ),
            'attributes' => array(
                'class'    => 'form-control',
                
            )
        ));
        
        $this->add(array(
            'name' => 'phoneNumber',
            'options' => array(
                'label' => 'Phone number: '
            ),
            'attributes' => array(
                'class'    => 'form-control',
                
            )
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'currentStatus',
            'options' => array(
                'label' => 'Sponsor status: ',
                'value_options' => array("1"=>"Active","0"=>"Retired"),
            ),
            'attributes' => array(
                //'required' => 'required',
                'class'    => 'form-control',
                
                
            )
        ));
        
        
        $this->add(array(
            'name' => 'pkSponsorid',
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
            
            'contactPerson' => array(
                'required' => false
            ),
            'description' => array(
                'required' => false
            ),
            'postalAddress' => array(
                'required' => false
            ),
            'phoneNumber' => array(
                'required' => false
            ),
            'currentStatus' => array(
                'required' => false
            ),
            'sponsorName' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'DoctrineModule\Validator\UniqueObject',
                        'options' => array(
                            'use_context' => true,
                            'object_repository' => $this->em->getRepository('Application\Entity\Sponsor'),
                            'fields' => 'sponsorName',
                            'object_manager' => $this->em,
                            'message' => 'Sponsor name already exists in the system'
                        )
                    )
                ) 
            )
            
            
            
        );
    }
}

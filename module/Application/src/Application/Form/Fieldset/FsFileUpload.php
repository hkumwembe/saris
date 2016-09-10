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


class FsFileUpload extends Fieldset implements InputFilterProviderInterface
{
    protected $em;
    public function __construct()
    {
        parent::__construct('FileUpload');
        
        $this->add(array(
            'type' => 'File',
            'name' => 'fileupload',
            'options' => array(
                'label' => 'Document to pload:*',
                
            ),
            'attributes' => array(
                'required' => 'required',
                'class'    => 'form-control',
                'id'    => 'fileupload'
                
            )
        ));

    }

    /**
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return array(
            'fileupload' => array(
                'required' => true
            )
        );
    }
}

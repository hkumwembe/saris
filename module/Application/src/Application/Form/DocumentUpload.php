<?php
namespace Application\Form;

use Zend\InputFilter;
use Zend\Form\Element;
use Zend\Form\Form;

class DocumentUpload extends Form
 {
     
     public function __construct()
     {
         // we want to ignore the name passed
         parent::__construct('frmdocument');
        $this->addElements();
        $this->addInputFilter();
           
     }
     
    public function addElements()
    {
        // File Input
        $file = new Element\File('document-file');
        $file->setLabel('Document to upload')
             ->setAttribute('id', 'document-file')
             ->setAttribute('class', 'form-control');
        $this->add($file);
        
        // File name
        $name = new Element\Text('document-name');
        $name->setLabel('Document title')
             ->setAttribute('id', 'document-name')
             ->setAttribute('class', 'form-control');
        $this->add($name);
    }
    
    public function addInputFilter()
    {
        $inputFilter = new InputFilter\InputFilter();

        // File Input filter
        $fileInput = new InputFilter\FileInput('document-file');
        $fileInput->setRequired(true);
                  
        $fileInput->getFilterChain()->attachByName(
            'filerenameupload',
            array(
                'target'    => './data/tmpuploads/doc.pdf',
                'randomize' => true,
                'use_upload_extension'=>true
            )
        );
        
        $fileInput->getValidatorChain()
            ->attachByName('filesize',      array('max' => 2004800))
            ->attachByName('filemimetype',  array('mimeType' => 'application/pdf,application/msword'));
            //->attachByName('fileimagesize', array('maxWidth' => 100, 'maxHeight' => 100));
        $inputFilter->add($fileInput);
        
        // File Input

        $inputFilter->add(array(
            "name"  => "document-name",
            "required" => true,
            'validators' => array(
                    array(
                        'name' => 'not_empty',
                        'message'=>"Testing"
                    ),
                )
        ));

        $this->setInputFilter($inputFilter);
    }
 }

<?php
namespace Application\Model;

/**
 * Description of Admission
 *
 * @author hkumwembe
 */

class Communication extends Commonmodel {

    protected $em;
    
    public function __construct(\Doctrine\ORM\EntityManager $em) {
        parent::__construct($em);
        $this->em = $em;
    }  
    
    
    public function saveNotice($object){
        
        if(!$object->getPkNoticeid()){
            $eo = new \Application\Entity\Notice();
        }else{
            $eo = $this->em->getRepository("\Application\Entity\Notice")->find($object->getPkNoticeid());
        }
        
        $eo->setCapturedBy($object->getCapturedBy());
        $eo->setHeader($object->getHeader());
        $eo->setBody($object->getBody());
        $eo->setDatePublished($object->getDatePublished());
        $eo->setAccounttype($object->getAccounttype());
        $eo->setIsActive($object->getIsActive());
        
        try{
            
            //Commit values set to the object 
            if(!$object->getPkNoticeid()){
                $this->em->persist($eo);
            }

            //Save values if just updating record
            $this->em->flush($eo);
            
            return $eo;
            
        }catch(Exception $e){
            throw($e->getMessages());
        }
    }   
    
}

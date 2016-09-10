<?php
namespace Application\Model;

/**
 * Description of Sponsorship
 *
 * @author hkumwembe
 */

class Sponsorship extends Commonmodel {

    protected $em;
    
    public function __construct(\Doctrine\ORM\EntityManager $em) {
        parent::__construct($em);
        $this->em = $em;
    }  
    
    //List of students on sponsorship
    public function SponsoredList($criteria=null){
        
        $dql   = $this->em->createQueryBuilder();
        $dql->select("SS,S,SP,P,ST,U")
                   ->from("\Application\Entity\Sponsoredstudent", "SS")
                   ->join("SS.fkSponsorid", "S")
                   ->join("SS.fkStudentprogramid", "SP")
                   ->join("SP.fkProgramid", "P")
                   ->join("SP.fkStudentid", "ST")
                   ->join("ST.fkUserid", "U");
        
        if(!empty($criteria)){
            foreach($criteria as $field=>$value){
              $dql->andWhere("{$field}='{$value}'");
            }
        }
        
        return $dql->getQuery()->execute();
    }

    //List of students on pending sponsorship allocation
    public function UnSponsoredList($students,$criteria=null){
        
        $allocation[] = "HOLDER";
        foreach($students as $student){
            $allocation[] = $student->getFkStudentprogramid()->getPkStudentprogramid();
        }
        
        $query   = $this->em->createQueryBuilder();
        $query->select("SPA,PA,STA,UA")
                   ->from("\Application\Entity\Studentprogram", "SPA")
                   ->join("SPA.fkProgramid", "PA")
                   ->join("SPA.fkStudentid", "STA")
                   ->join("STA.fkUserid", "UA")
                   ->where($query->expr()->notIn("SPA.pkStudentprogramid", $allocation));
                   
        if(!empty($criteria)){
            foreach($criteria as $field=>$value){
              $query->andWhere("{$field}='{$value}'");
            }
        }
        
        return $query->getQuery()->execute();
    }




    public function saveSponsor($object){
        
        if(!$object->getPkSponsorid()){
            $eo = new \Application\Entity\Sponsor();
        }else{
            $eo = $this->em->getRepository("\Application\Entity\Sponsor")->find($object->getPkSponsorid());
        }
        
        $eo->setSponsorName($object->getSponsorName());
        $eo->setContactPerson($object->getContactPerson());
        $eo->setCurrentStatus($object->getCurrentStatus());
        $eo->setDescription($object->getDescription());
        $eo->setDocumentUrl($object->getDocumentUrl());
        $eo->setPhoneNumber($object->getPhoneNumber());
        $eo->setPostalAddress($object->getPostalAddress());
        
        try{
            
            //Commit values set to the object 
            if(!$object->getPkSponsorid()){
                $this->em->persist($eo);
            }

            //Save values if just updating record
            $this->em->flush($eo);
            
            return $eo;
            
        }catch(Exception $e){
            throw($e->getMessages());
        }
    }   
    
    public function saveSponsoredStudent($object){
        
        if(!$object->getPkSsid()){
            $eo = new \Application\Entity\Sponsoredstudent();
        }else{
            $eo = $this->em->getRepository("\Application\Entity\Sponsoredstudent")->find($object->getPkSsid());
        }
        
        $eo->setFkSponsorid($object->getFkSponsorid());
        $eo->setFkStudentprogramid($object->getFkStudentprogramid());
        
        try{
            
            //Commit values set to the object 
            if(!$object->getPkSsid()){
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

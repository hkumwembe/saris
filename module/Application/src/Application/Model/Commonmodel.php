<?php
namespace Application\Model;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use Zend\InputFilter\Factory;
/**
 * Description of Usermodel
 *
 * @author hkumwembe
 */

abstract class Commonmodel{
    
    protected $em;
    
    public function __construct(\Doctrine\ORM\EntityManager $em) {
        $this->em = $em;
    } 
    
    
    /*
     * Save user
     */
    public function saveUser($object) {
        
         if(!$object->getPkUserid()){
                $oe = new \Application\Entity\User();
         }else{
                $oe = $this->em->getRepository("\Application\Entity\User")->find($object->getPkUserid());
         }

            //Set user object values to be saved
            $oe->setFirstname($object->getFirstname());
            $oe->setSurname($object->getSurname());
            $oe->setGender($object->getGender());
            $oe->setTitle($object->getTitle());
            $oe->setOthernames($object->getOthernames());
            $oe->setEmailaddress($object->getEmailaddress());
            $oe->setUsername($object->getUsername());
            $oe->setPassword($object->getPassword());
            $oe->setFkRoleid($object->getFkRoleid());
            $oe->setAccounttype($object->getAccounttype());
            $oe->setIpaddress($object->getIpaddress());
            $oe->setLastloginip($object->getLastloginip());
            $oe->setLastlogindate($object->getLastlogindate());
            $oe->setLogindate($object->getLastlogindate());
            $oe->setLogintimes($object->getLogintimes());
            
            try{
                //Commit values set to the object 
                if(!$object->getPkUserid()){
                    $this->em->persist($oe);
                }
                //Save values if just updating record
                $this->em->flush($oe);
                return $oe;

            }catch(Exception $e){
                throw($e->getMessages());
            }
    }
    

    
    /*
     * Format ajax form errors
     */
    public function formatErrorMessage($errorObject){
        /*
        * Form has errors. Put errors into an array
        */
        $messages = "";
        
        foreach($errorObject as $messageObject){
            foreach($messageObject as $messages){
                
                if(count($messages)>1){
                    foreach($messages as $ms){
                        return $ms;
                    }
                }else{
                    return $messages;
                }
            }
        }
    }
    
    public function generateFlashMessages($messageContainer,$options){
        foreach($options as $option){
            $messageContainer->addMessage($option);
        }
    }

    
    public function isHead($user){
        
        $query = $this->em->createQuery("SELECT D FROM \Application\Entity\Department D"
                                      . " JOIN D.fkStaffid S "
                                      . " WHERE S.fkUserid = :user ")
                          ->setParameter("user", $user);
        return count($query->getResult())>0?$query->getResult():false;
    }





    /*
     * Fetch available list
     */
    public function getAvailableModules($classid){
        $options = array();
        $academicperiodid = $this->getCurrentYr();
        $modulequery  = $this->em->createQuery("SELECT M FROM \Application\Entity\Module M"
                                             . " WHERE M.pkModuleid NOT IN( SELECT IDENTITY(C.fkModuleid) FROM "
                                             . " \Application\Entity\Classmodule C JOIN C.fkAcademicperiod A WHERE C.fkClassid = :classid "
                                             . " AND A.parentid = :parentid ) ")
                             ->setParameter('classid', $classid)
                             ->setParameter('parentid', $academicperiodid[0]->getPkAcademicperiodid());
        foreach($modulequery->getResult() as $module ){
            $options[$module->getPkModuleid()] = $module->getModuleName()." (".$module->getModuleCode().")";
        }
        
        return $options;
    }
    
   public function getClassModules($classid){
       
       $academicyear = $this->getCurrentYr();

       $query = $this->em->createQuery(" SELECT C,A FROM \Application\Entity\Classmodule C "
                                       . " JOIN C.fkAcademicperiod A "
                                       . " WHERE A.parentid = :period "
                                       . " AND C.fkClassid = :classid")
                
                          ->setParameter("period", $academicyear[0]->getPkAcademicperiodid())
                          ->setParameter("classid", $classid);
        
        return $query->getResult();
   } 
   
   public function getSystemsettings(){
       return $this->em->getRepository("\Application\Entity\Settings")->find(1);
   }
    
    
    /*
     * Generate current academic year
     */
    
    public function getCurrentYr($class = null){
        //Get current date
        $date = new \DateTime();
        
        $query = $this->em->createQuery(" SELECT A FROM \Application\Entity\Academicyear A"
                                       ." WHERE :currentdate BETWEEN A.startDate AND A.endDate "
                                       . " AND A.parentid is null "
                                       . " AND A.category = 'GENERIC' ")
                
                          ->setParameter("currentdate", $date);
        
        return $query->getResult();
    }
    
    
        /*
     * Generate current academic year
     */
    
    public function getCurrentPeriod($class = null){
        //Get current date
        $date = new \DateTime();

        $query = $this->em->createQuery(" SELECT A FROM \Application\Entity\Academicyear A"
                                       ." WHERE :currentdate BETWEEN A.startDate AND A.endDate "
                                       . " AND A.parentid is not null ")
                
                          ->setParameter("currentdate", $date);
        
        return $query->getResult();
    }
    
    
    
    
    /*
     * Get date difference in days
     */
    function dateDiff($start, $end) {

        $start_ts = strtotime($start);

        $end_ts = strtotime($end);

        $diff = $end_ts - $start_ts;

        return round($diff / 86400);
    }
    
    /*
     * Is academic year setting fine
     */
    public function IsWithin($context){
        
        if(empty($context['parentid'])){
        
            $period = $this->em->createQuery(" SELECT AP FROM \Application\Entity\Academicyear AP "
                                            . "WHERE ( :startdate BETWEEN AP.startDate AND AP.endDate "
                                            . " OR  :enddate BETWEEN AP.startDate AND AP.endDate) "
                                            . " AND AP.pkAcademicperiodid != :yearid "
                                            . " AND :category = 'GENERIC' ")
                               ->setParameter("startdate", $context['startDate'])
                               ->setParameter("enddate",$context['endDate'])
                               ->setParameter("yearid", $context['pkAcademicperiodid'])
                               ->setParameter("category", $context['category']);
        }else{
            
            //Get parent academic year information
            $academicyear = $this->em->getRepository("\Application\Entity\Academicyear")->find($context['parentid']);
            
            $period = $this->em->createQuery(" SELECT AP FROM \Application\Entity\Academicyear AP "
                                            . "WHERE ( :startdate BETWEEN AP.startDate AND AP.endDate "
                                            . " OR  :enddate BETWEEN AP.startDate AND AP.endDate) "
                                            . " AND (AP.pkAcademicperiodid != :yearid AND AP.parentid = :parentid)"
                                            )
                               ->setParameter("startdate", $context['startDate'])
                               ->setParameter("enddate",$context['endDate'])
                               ->setParameter("yearid", $context['pkAcademicperiodid'])
                               ->setParameter("parentid", $context['parentid'])
                               
                                ;
        }

        return $period->getResult();
        
    }




    /*
     * Get class list
     */
    public function getClasslist($criteria = array(),$groupBy= null){
        
      $query = $this->em->createQueryBuilder()
                           ->select('SC,S,A,C,U')
                           ->from('\Application\Entity\Studentclass','SC')
                           ->join('SC.fkStudentid','S')
                           ->join('S.fkUserid','U')
                           ->join('SC.fkClassid','C')
                           ->join('SC.fkAcademicperiodid','A');
        if(!empty($criteria)){
            foreach($criteria as $field=>$value){
              $query->andWhere("{$field}='{$value}'");
            }
        }
        $query->addOrderBy("U.surname", "ASC");
        if($groupBy != null){
           $query->groupBy($groupBy);
        }
         return $query->getQuery()->execute();
    }
    
    /*
     * Search for objects from entity
     */
    public function getEntity($entity,$criteria){
        return $this->em->getRepository($entity)->matching($criteria);
    }
    
     /*
     * Remove module object from database
     */
    public function deletefromdb($entity,$criteria=array()){
        $this->em->getConnection()->beginTransaction();
        try{
            /*
             * 
             */
            if(!is_array($criteria)){
                $object = $this->em->getRepository($entity)->find($criteria);
                $this->em->remove($object);
            }else{
                $object = $this->em->getRepository($entity)->findBy($criteria);
                foreach($object as $obj){
                    $this->em->remove($obj);
                }
            }
            
            $this->em->flush();
            $this->em->getConnection()->commit();
        }catch(Doctrine\DBAL\DBALException $e){
            $this->em->getConnection()->rollBack();
            $this->logger->addWriter($this->writer);
            $this->logger->info("Test");
        }
    }
}

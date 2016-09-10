<?php
namespace Application\Model;

/**
 * Description of Assessment
 *
 * @author hkumwembe
 */
use DOMDocument;
use Zend\Config\Reader\Xml;
use Zend\Json\Json;

class Assessment extends Commonmodel {
    
    protected $em;
    
    public function __construct(\Doctrine\ORM\EntityManager $em) {
        parent::__construct($em);
        $this->em = $em;
    }

    // _Internal: Remove recursion in result array
    public function del_p(&$ary) {    foreach ($ary as $k=>$v) {        if ($k==='_p') unset($ary[$k]);        elseif (is_array($ary[$k]))            $this->del_p($ary[$k]);    }}


    function ary2xml($cary, $d=0, $forcetag='') {    $res=array();    foreach ($cary as $tag=>$r) {        if (isset($r[0])) {            $res[]=ary2xml($r, $d, $tag);        } else {            if ($forcetag) $tag=$forcetag;            $sp=str_repeat("\t", $d);            $res[]="$sp<$tag";            if (isset($r['_a'])) {foreach ($r['_a'] as $at=>$av) $res[]=" $at=\"$av\"";}            $res[]=">".((isset($r['_c'])) ? "\n" : '');            if (isset($r['_c'])) $res[]=ary2xml($r['_c'], $d+1);            elseif (isset($r['_v'])) $res[]=$r['_v'];            $res[]=(isset($r['_c']) ? $sp : '')."</$tag>\n";        }            }    return implode('', $res);}

    // Insert element into array
    function ins2ary(&$ary, $element, $pos) {    $ar1=array_slice($ary, 0, $pos); $ar1[]=$element;    $ary=array_merge($ar1, array_slice($ary, $pos));}

    /*
     * Convert xml content to array
     */
    function xml2ary(&$string) 
    {       $parser = xml_parser_create(); 
            xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
            xml_parse_into_struct($parser, $string, $vals, $index);
            xml_parser_free($parser);
            $mnary=array();
            $ary=&$mnary; 
            foreach ($vals as $r) {
                $t=$r['tag']; 
                if ($r['type']=='open') {   
                    if (isset($ary[$t])) { 
                        if (isset($ary[$t][0])) 
                            $ary[$t][]=array();
                        else 
                            $ary[$t]=array($ary[$t], array());
                        $cv=&$ary[$t][count($ary[$t])-1];
                        } 
                    else $cv=&$ary[$t];
                    if (isset($r['attributes']))
                    {foreach ($r['attributes'] as $k=>$v)
                        $cv['_a'][$k]=$v;
                    }           
                    $cv['_c']=array();
                    $cv['_c']['_p']=&$ary;
                    $ary=&$cv['_c'];        
                    } elseif ($r['type']=='complete') { 
                    if (isset($ary[$t])) {  
                    if (isset($ary[$t][0])) $ary[$t][]=array(); 
                    else $ary[$t]=array($ary[$t], array()); 
                    $cv=&$ary[$t][count($ary[$t])-1];           
                    } else $cv=&$ary[$t];      
                    if (isset($r['attributes'])) 
                    {foreach ($r['attributes'] as $k=>$v) $cv['_a'][$k]=$v;}  
                    $cv['_v']=(isset($r['value']) ? $r['value'] : '');      
                    } elseif ($r['type']=='close') {         
                    $ary=&$ary['_p'];        }    }     
                    $this->del_p($mnary); 


                    return $mnary;
    }
    
    public function createXMLContent($grades,$ranges,$isFinal)
    {
       $xmlDoc= new DomDocument("1.0");
       $parent = $xmlDoc->createElement('input');
       $parent = $xmlDoc->appendChild($parent);
       $courses = $xmlDoc->createElement("courses");
       $courses = $parent->appendChild($courses);
       
       foreach($grades as $grade)
        {
             $course = $xmlDoc->createElement("course");
             $course->setAttribute("grade",(float)$grade->getMark());
             $course = $courses->appendChild($course);
             $courseCode = $xmlDoc->createTextNode($grade->getFkAiid()->getPkAiid());
             $courseCode = $course->appendChild($courseCode);
       }
//       
//       //Assessment scale range definition
       $range = $xmlDoc->createElement('assessment-scale-ranges');
       $range = $parent->appendChild($range);
       foreach($ranges as $scaleRange)
        {
            $rangeElement = $xmlDoc->createElement(str_replace(" ", '-', strtolower($scaleRange->getRemark())));
            $rangeElement->setAttribute("remark",$scaleRange->getRemark());
            $rangeElement->setAttribute("from",$scaleRange->getMarkFrom());
            $rangeElement->setAttribute("to",$scaleRange->getMarkTo());
            $rangeElement = $range->appendChild($rangeElement);
        }
       
              
       $content = $xmlDoc->saveXML();
       return $content;
}

function GetResultCode($XMLcontent){
    $reader  = new Xml();
    $json    = new Json();
    //$par = $this->xml2ary($XMLcontent);
    $par   = $reader->fromString($XMLcontent);
    $content = $json->fromXml($XMLcontent,false);
    
    return $this->passConditionChecker($par);	    
}

public function passConditionChecker($par)
    {
        $lwCount = 0;
        $swCount = 0;
        //foreach($par['input']['_c']['courses']['_c']['course'] as $course){
        foreach ($par['courses'] as $course){
           foreach($course as $module){
                   $mark = $module['grade'];
                   
                   if(($mark <=
                       $par['assessment-scale-ranges']['marginal-failure']['to'])
                       && ($mark >=
                       $par['assessment-scale-ranges']['marginal-failure']['from'])){
                       $lwCount++;
                   }elseif($mark <= $par['assessment-scale-ranges']['undoubted-failure']['to']){

                        $swCount++;
                   }
           }
       }
       
       if($swCount >= 1 || $lwCount > 1){
               return "SW";
       }elseif($swCount == 0 && $lwCount == 1){
           
               return "LW";
       }else{
                
               return "PS";
       }
    } 
    
}

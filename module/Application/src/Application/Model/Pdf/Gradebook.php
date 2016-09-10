<?php
namespace Application\Model\pdf;
require './vendor/Plugins/fpdf/CustomFPDF.php';

use CustomFPDF;

/**
 * Description of Charts
 *
 * @author hkumwembe
 */

class Gradebook extends CustomFPDF {
    public $fpdf;
    protected $y0; //Ordinate of column start


    public function __construct() {
        $this->fpdf = new \CustomFPDF();
    }
    /*
     * Generate class summary
     */
    public function classSummary($header,$data,$title){
       $this->fpdf->SetTitle($title);
       
       $this->fpdf->getPageHeader();
      
       $this->fpdf->table($header,$data);
       $this->fpdf->Ln(10);
       
       //Get overal summary
       $this->fpdf->Cell(110,7,"Class Overal Performance Summary",0,0,'C');
       $header2 = array(array("W"=>25,"V"=>""),array("W"=>13,"V"=>"DF"),array("W"=>13,"V"=>"FW"),array("W"=>13,"V"=>"RF"),array("W"=>13,"V"=>"RR"),array("W"=>13,"V"=>"RW"),array("W"=>13,"V"=>"SP"),array("W"=>13,"V"=>"WD"),array("W"=>14,"V"=>"TOTAL"));
       $data2[] = array("Female",2,3,1,5,6,7,1,8);
       $data2[] = array("Male",2,3,1,5,6,7,1,8);
       $data2[] = array("Total",2,3,1,5,6,7,1,8);
       $this->fpdf->Ln();
       $this->fpdf->table($header2,$data2);
       $this->fpdf->Ln(10);
       
    }
    
    public function generateGradeBook($students,$modules,$classes){
        foreach($classes as $class){
            $title         = "END OF YEAR 2015/2016 RESULTS SUMMARY: ".$class['classname'];
            $headers       = array(array("W"=>10,"V"=>"No."),array("W"=>30,"V"=>"Module code"),array("W"=>80,"V"=>"Module name"),array("W"=>11,"V"=>"Avg"),array("W"=>25,"V"=>"Highest mark"),array("W"=>25,"V"=>"Lowest mark"),array("W"=>9,"V"=>"DN"),array("W"=>9,"V"=>"CR"),array("W"=>9,"V"=>"PS"),array("W"=>8,"V"=>"F"),array("W"=>10,"V"=>"INC"));        
           
            $this->classSummary($headers, $modules, $title);
            $generalheaders        = array(array("W"=>12,"V"=>"Count"),array("W"=>45,"V"=>"Registration #"),array("W"=>40,"V"=>"Surname"),array("W"=>40,"V"=>"First name"),array("W"=>20,"V"=>"Gender","O"=>1),array("W"=>7,"V"=>""));   
            foreach($modules as $module){
                $moduleheaders[]   = array("W"=>20,"V"=>$module[1],"O"=>1);
            }
            $bookheaders = array_merge($generalheaders,$moduleheaders,array(array("W"=>8,"V"=>""),array("W"=>20,"V"=>"History","O"=>1),array("W"=>20,"V"=>"Average","O"=>1),array("W"=>20,"V"=>"Remark","O"=>1)));
            $title         = "2015/2016 END OF YEAR RESULTS: ".$class['classname'];
            //List of students
            $this->fpdf->getPageHeader();
            
            $this->fpdf->SetTitle($title);
            $this->fpdf->table($bookheaders,$students[$class['classid']]);
            $this->fpdf->Ln(10);
        }
        $this->fpdf->Output();    
    }


    /*
     * Generate detailed class summary
     */
    public function detailedClassSummary($header,$data,$title){
        
       $this->fpdf->SetTitle($title);
       $this->fpdf->getPageHeader();
       
       $this->fpdf->table($header,$data);
       $this->fpdf->Ln(10);
       $this->fpdf->Output();
    }
    
    public function classGradeBook($header,$data,$title){
       foreach($data as $modulesetting){
           
//            $topic = $modulesetting['module'];
//            $this->fpdf->SetTitle($modulesetting['class']);
            $this->fpdf->getPageHeader();
            $this->fpdf->table($header,$modulesetting['students']);
            $this->fpdf->Ln(10);
       }
       $this->fpdf->Output();
    }
    
}

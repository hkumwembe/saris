<?php
namespace Application\Model\pdf;
require './vendor/Plugins/fpdf/CustomFPDF.php';

use CustomFPDF;

/**
 * Description of Charts
 *
 * @author hkumwembe
 */

class Registeredlist extends CustomFPDF {
    public $fpdf;
    protected $y0; //Ordinate of column start


    public function __construct() {
        $this->fpdf = new \CustomFPDF();
    }
    
    public function generateReport($header,$data,$title){
        
       $this->fpdf->SetTitle($title);
       $this->fpdf->getPageHeader();
       
       $this->fpdf->table($header,$data);
       $this->fpdf->Ln(10);
       $this->fpdf->Output();
    }
    
    public function subjectPerformance($header,$data,$title){
       foreach($data as $modulesetting){
            $topic = $modulesetting['module'];
            $this->fpdf->SetTitle($modulesetting['class']);
            $this->fpdf->getPageHeader();
            
            $this->fpdf->Cell(20,10,'Module: ',0,0);
            $this->fpdf->Cell(160,10,$topic,0,0,'L');
            $this->fpdf->Cell(30,10,'Weighting: ',0,0);
            $this->fpdf->Cell(150,10,$modulesetting['weighting'],0,0,'L');
            $this->fpdf->Ln(10);
            $this->fpdf->Cell(20,10,'Lecturer: ',0,0);
            $this->fpdf->Cell(177,10,$modulesetting['lecturer'],0,0,'L');
            $this->fpdf->Ln(15);

            $this->fpdf->table($header,$modulesetting['students']);
            $this->fpdf->Ln(10);
       }
       $this->fpdf->Output();
    }
    
}

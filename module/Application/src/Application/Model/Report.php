<?php
namespace Application\Model\Pdf;
require './vendor/Plugins/fpdf/CustomFPDF.php';

use CustomFPDF;

/**
 * Description of Charts
 *
 * @author hkumwembe
 */

class Report {
    public $fpdf;
    protected $col = 0;
    protected $y0; //Ordinate of column start


    public function __construct() {
        $this->fpdf = new \CustomFPDF();
    }
    
    function Header(){
//        $this->Image('../../images/logo.jpg',20,15,17,25);
//			//$this->Image('../../images/logo.jpg',140,15,17,25);
			
        $this->fpdf->Ln(15);

        $this->fpdf->SetFont('Times','',12);
        //Title
        $this->fpdf->Cell(90,1,'University of Malawi',0,0,'C');
        //Line break
        $this->fpdf->Ln();
        $this->fpdf->SetFont('Times','B',12);
        $this->fpdf->Cell(90,10,'The College of Medicine',0,0,'C');
        //Line break
        $this->fpdf->Ln(15);
        $this->fpdf->SetFont('Times','B',11);
        $this->fpdf->Cell(240,10,'End of year',0,0,'C');
        
        $this->fpdf->Ln(1);
        $this->fpdf->SetFont('Times','B',11);
        $this->fpdf->SetTextColor(0);
        $this->fpdf->SetFillColor(255,255,255);
        $this->fpdf->Cell(250,6,'Bachelor of science in Information technology',0,1,'L',1);
        
    }
    
    function Footer() {
            if(!$this->fpdf->_numberingFooter)
                    return;
            //Go to 1.5 cm from bottom
            $this->fpdf->SetY(-15);
            //Select Arial italic 8
            $this->fpdf->SetFont('Arial','',8);
            $this->fpdf->Cell(130,7,'COM Registry - Confidential',0,0,'L');
            $this->fpdf->SetFont('Arial','B',8);
            $this->fpdf->Cell(120,7,$this->numPageNo(),0,0,'L'); 
            $this->fpdf->SetFont('Arial','',8);
            $this->fpdf->Cell(400,7,$this->date,0,0,'Y',0,0,'R'); 
            if(!$this->fpdf->_numbering)
                    $this->fpdf->_numberingFooter=false;
    }
    
    function table($header){
        global $showheader;
        //Column widths
        $w=array(12,70,40,25,10,40);
        //Header
        $this->fpdf->SetFont('Arial','B',10);
        for($i=0;$i<count($header);$i++)
            $this->fpdf->Cell($w[$i],7,$header[$i],1,0,'L');
        $this->fpdf->Ln();
        $y = 1;
        $this->fpdf->FontStyleSetFont('Arial','',10);
//    foreach($data as $stud)
//    {
//        
//    
//        $this->Cell(12,7,$y,1,0,'C');
//        $this->Cell(70,7,$stud["name"],1,0,'L');
//        $this->Cell(40,7,$stud["regNum"],1,0,'L');
//        $this->Cell(25,7,$stud["AccountNo"],1,0,'L');
//        $this->Cell(10,7,$stud["gender"],1,0,'L');
//        $this->Cell(40,7,$stud["status"],1,0,'L');
//        //$this->Cell(50,7,$stud["remark"],1,0,'L');
//		//$this->Cell(40,7,$stud["status"],1,0,'L');
//        
//        $this->Ln();
//        $y++;
//        $showheader = 0;
//    }
    
    $this->fpdf->Ln(3);
    $this->fpdf->SetFillColor(81,160,225);
    $this->fpdf->Cell(195,5,'',1,0,'C',1);

}
    
    
    
}

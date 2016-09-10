<?php
require('fpdf.php');

class CustomFPDF extends FPDF {
    var $_toc=array();
    var $_numbering=false;
    var $_numberingFooter=false;
    var $_numPageNum=1;
    var $B=0;
    var $CurPageFormat;
    var $I=0;
    var $U=0;
    var $HREF='';
    var $InHeader = '';
    var $ALIGN='';
    var $col=0;
    var $title = '';
    public $date;
	
	
    function WriteHTML($html)
    {
        //HTML parser
        $html=str_replace("\n", ' ', $html);
        $a=preg_split('/<(.*)>/U', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
		
        foreach($a as $i=>$e)
        {
			
            if($i%2==0)
            {
                //Text
                if($this->HREF)
                    $this->PutLink($this->HREF, $e);
                elseif($this->ALIGN == 'center')
                    $this->Cell(0, 5, $e, 0, 1, 'C');
                else
                    $this->Multicell(0,4,$e);		
            }
            else
            {               
				//Tag
                if($e{0}=='/')
                    $this->CloseTag(strtoupper(substr($e, 1)));
                else
                {
                    //Extract properties
                    $a2=split(' ', $e);
					
                    $tag=strtoupper(array_shift($a2));
                    $prop=array();
                    foreach($a2 as $v)
                        if(ereg('^([^=]*)=["\']?([^"\']*)["\']?$', $v, $a3))
                            $prop[strtoupper($a3[1])]=$a3[2];
                    $this->OpenTag($tag, $prop);
                }
            }
        }
		
    }


	
    function OpenTag($tag, $prop)
    {
        if($tag=='STRONG'){
			$tag = 'B';
		}
		//Opening tag
        if($tag=='B' or $tag=='I' or $tag=='U' )
            $this->SetStyle($tag, true);
        if($tag=='A')
            $this->HREF=$prop['HREF'];
		if($tag=='UL')
            $this->Ln(2);
		if($tag=='LI'){
			$this->cell(5,5,'');
			$this->cell(5,5,'-');
				
		}
        if($tag=='BR')
            $this->Ln(2);
        if($tag=='P')
			$this->Ln(2);
            $this->ALIGN=$prop['ALIGN'];
        if($tag=='HR')
        {
            if( $prop['WIDTH'] != '' )
                $Width = $prop['WIDTH'];
            else
                $Width = $this->w - $this->lMargin-$this->rMargin;
            $this->Ln(2);
            $x = $this->GetX();
            $y = $this->GetY();
            $this->SetLineWidth(0.4);
            $this->Line($x, $y, $x+$Width, $y);
            $this->SetLineWidth(0.2);
            $this->Ln(2);
        }
    }

function CloseTag($tag)
{
    if($tag=='STRONG'){
                    $tag = 'B';
            }

            //Closing tag
    if($tag=='B' or $tag=='I' or $tag=='U')
        $this->SetStyle($tag, false);
    if($tag=='A')
        $this->HREF='';
    if($tag=='P')
        $this->ALIGN='';
            if($tag == 'STRONG' || $tag == 'UL' || $tag == 'LI' || $tag == 'BR')
        $this->Ln(1);

}

function SetStyle($tag, $enable)
{
    //Modify style and select corresponding font
    $this->$tag+=($enable ? 1 : -1);
    $style='';
    foreach(array('B', 'I', 'U') as $s)
        if($this->$s>0)
            $style.=$s;
    $this->SetFont('', $style);
}

function PutLink($URL, $txt)
{
    //Put a hyperlink
    $this->SetTextColor(0, 0, 255);
    $this->SetStyle('U', true);
    $this->Write(5, $txt, $URL);
    $this->SetStyle('U', false);
    $this->SetTextColor(0);
}
	
	
function AddPage($orientation='', $format='') {

        parent::AddPage($orientation,$format);
        if($this->_numbering)
                $this->_numPageNum++;
}
	
function MultiCellBlt($w, $h, $blt, $txt, $border=0, $align='J', $fill=false)
	{
		//Get bullet width including margins
		$blt_width = $this->GetStringWidth($blt)+$this->cMargin*2;

		//Save x
		$bak_x = $this->x;

		//Output bullet
		$this->Cell($blt_width,$h,$blt,0,'',$fill);

		//Output text
		$this->MultiCell($w-$blt_width,$h,$txt,$border,$align,$fill);

		//Restore x
		$this->x = $bak_x;
}

function Rotate($angle,$x=-1,$y=-1) { 

        if($x==-1) 
            $x=$this->x; 
        if($y==-1) 
            $y=$this->y; 
        if($this->angle!=0) 
            $this->_out('Q'); 
        $this->angle=$angle; 
        if($angle!=0) 

        { 
            $angle*=M_PI/180; 
            $c=cos($angle); 
            $s=sin($angle); 
            $cx=$x*$this->k; 
            $cy=($this->h-$y)*$this->k; 
             
            $this->_out(sprintf('q %.5f %.5f %.5f %.5f %.2f %.2f cm 1 0 0 1 %.2f %.2f cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy)); 
        } 
}
	
function VCell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false){
	//Output a cell
	$k=$this->k;
	if($this->y+$h>$this->PageBreakTrigger && !$this->InHeader && !$this->InFooter && $this->AcceptPageBreak())
	{
		//Automatic page break
		$x=$this->x;
		$ws=$this->ws;
		if($ws>0)
		{
			$this->ws=0;
			$this->_out('0 Tw');
		}
		$this->AddPage($this->CurOrientation,$this->CurPageFormat);
		$this->x=$x;
		if($ws>0)
		{
			$this->ws=$ws;
			$this->_out(sprintf('%.3F Tw',$ws*$k));
		}
	}
	if($w==0)
		$w=$this->w-$this->rMargin-$this->x;
	$s='';
// begin change Cell function 
	if($fill || $border>0)
	{
		if($fill)
			$op=($border>0) ? 'B' : 'f';
		else
			$op='S';
		if ($border>1) {
			$s=sprintf('q %.2F w %.2F %.2F %.2F %.2F re %s Q ',$border,
						$this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
		}
		else
			$s=sprintf('%.2F %.2F %.2F %.2F re %s ',$this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
	}
	if(is_string($border))
	{
		$x=$this->x;
		$y=$this->y;
		if(is_int(strpos($border,'L')))
			$s.=sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);
		else if(is_int(strpos($border,'l')))
			$s.=sprintf('q 2 w %.2F %.2F m %.2F %.2F l S Q ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);
			
		if(is_int(strpos($border,'T')))
			$s.=sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);
		else if(is_int(strpos($border,'t')))
			$s.=sprintf('q 2 w %.2F %.2F m %.2F %.2F l S Q ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);
		
		if(is_int(strpos($border,'R')))
			$s.=sprintf('%.2F %.2F m %.2F %.2F l S ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
		else if(is_int(strpos($border,'r')))
			$s.=sprintf('q 2 w %.2F %.2F m %.2F %.2F l S Q ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
		
		if(is_int(strpos($border,'B')))
			$s.=sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
		else if(is_int(strpos($border,'b')))
			$s.=sprintf('q 2 w %.2F %.2F m %.2F %.2F l S Q ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
	}
	if(trim($txt)!='')
	{
		$cr=substr_count($txt,"\n");
		if ($cr>0) { // Multi line
			$txts = explode("\n", $txt);
			$lines = count($txts);
			for($l=0;$l<$lines;$l++) {
				$txt=$txts[$l];
				$w_txt=$this->GetStringWidth($txt);
				if ($align=='U')
					$dy=$this->cMargin+$w_txt;
				elseif($align=='D')
					$dy=$h-$this->cMargin;
				else
					$dy=($h+$w_txt)/2;
				$txt=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));
				if($this->ColorFlag)
					$s.='q '.$this->TextColor.' ';
				$s.=sprintf('BT 0 1 -1 0 %.2F %.2F Tm (%s) Tj ET ',
					($this->x+.5*$w+(.7+$l-$lines/2)*$this->FontSize)*$k,
					($this->h-($this->y+$dy))*$k,$txt);
				if($this->ColorFlag)
					$s.=' Q ';
			}
		}
		else { // Single line
			$w_txt=$this->GetStringWidth($txt);
			$Tz=100;
			if ($w_txt>$h-2*$this->cMargin) {
				$Tz=($h-2*$this->cMargin)/$w_txt*100;
				$w_txt=$h-2*$this->cMargin;
			}
			if ($align=='U')
				$dy=$this->cMargin+$w_txt;
			elseif($align=='D')
				$dy=$h-$this->cMargin;
			else
				$dy=($h+$w_txt)/2;
			$txt=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));
			if($this->ColorFlag)
				$s.='q '.$this->TextColor.' ';
			$s.=sprintf('q BT 0 1 -1 0 %.2F %.2F Tm %.2F Tz (%s) Tj ET Q ',
						($this->x+.5*$w+.3*$this->FontSize)*$k,
						($this->h-($this->y+$dy))*$k,$Tz,$txt);
			if($this->ColorFlag)
				$s.=' Q ';
		}
	}
// end change Cell function 
	if($s)
		$this->_out($s);
	$this->lasth=$h;
	if($ln>0)
	{
		//Go to next line
		$this->y+=$h;
		if($ln==1)
			$this->x=$this->lMargin;
	}
	else
		$this->x+=$w;
}

function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
{
	//Output a cell
	$k=$this->k;
	if($this->y+$h>$this->PageBreakTrigger && !$this->InHeader && !$this->InFooter && $this->AcceptPageBreak())
	{
		//Automatic page break
		$x=$this->x;
		$ws=$this->ws;
		if($ws>0)
		{
			$this->ws=0;
			$this->_out('0 Tw');
		}
		$this->AddPage($this->CurOrientation,$this->CurPageFormat);
		$this->x=$x;
		if($ws>0)
		{
			$this->ws=$ws;
			$this->_out(sprintf('%.3F Tw',$ws*$k));
		}
	}
	if($w==0)
		$w=$this->w-$this->rMargin-$this->x;
	$s='';
// begin change Cell function
	if($fill || $border>0)
	{
		if($fill)
			$op=($border>0) ? 'B' : 'f';
		else
			$op='S';
		if ($border>1) {
			$s=sprintf('q %.2F w %.2F %.2F %.2F %.2F re %s Q ',$border,
				$this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
		}
		else
			$s=sprintf('%.2F %.2F %.2F %.2F re %s ',$this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
	}
	if(is_string($border))
	{
		$x=$this->x;
		$y=$this->y;
		if(is_int(strpos($border,'L')))
			$s.=sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);
		else if(is_int(strpos($border,'l')))
			$s.=sprintf('q 2 w %.2F %.2F m %.2F %.2F l S Q ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);
			
		if(is_int(strpos($border,'T')))
			$s.=sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);
		else if(is_int(strpos($border,'t')))
			$s.=sprintf('q 2 w %.2F %.2F m %.2F %.2F l S Q ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);
		
		if(is_int(strpos($border,'R')))
			$s.=sprintf('%.2F %.2F m %.2F %.2F l S ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
		else if(is_int(strpos($border,'r')))
			$s.=sprintf('q 2 w %.2F %.2F m %.2F %.2F l S Q ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
		
		if(is_int(strpos($border,'B')))
			$s.=sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
		else if(is_int(strpos($border,'b')))
			$s.=sprintf('q 2 w %.2F %.2F m %.2F %.2F l S Q ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
	}
	if (trim($txt)!='') {
		$cr=substr_count($txt,"\n");
		if ($cr>0) { // Multi line
			$txts = explode("\n", $txt);
			$lines = count($txts);
			for($l=0;$l<$lines;$l++) {
				$txt=$txts[$l];
				$w_txt=$this->GetStringWidth($txt);
				if($align=='R')
					$dx=$w-$w_txt-$this->cMargin;
				elseif($align=='C')
					$dx=($w-$w_txt)/2;
				else
					$dx=$this->cMargin;

				$txt=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));
				if($this->ColorFlag)
					$s.='q '.$this->TextColor.' ';
				$s.=sprintf('BT %.2F %.2F Td (%s) Tj ET ',
					($this->x+$dx)*$k,
					($this->h-($this->y+.5*$h+(.7+$l-$lines/2)*$this->FontSize))*$k,
					$txt);
				if($this->underline)
					$s.=' '.$this->_dounderline($this->x+$dx,$this->y+.5*$h+.3*$this->FontSize,$txt);
				if($this->ColorFlag)
					$s.=' Q ';
				if($link)
					$this->Link($this->x+$dx,$this->y+.5*$h-.5*$this->FontSize,$w_txt,$this->FontSize,$link);
			}
		}
		else { // Single line
			$w_txt=$this->GetStringWidth($txt);
			$Tz=100;
			if ($w_txt>$w-2*$this->cMargin) { // Need compression
				$Tz=($w-2*$this->cMargin)/$w_txt*100;
				$w_txt=$w-2*$this->cMargin;
			}
			if($align=='R')
				$dx=$w-$w_txt-$this->cMargin;
			elseif($align=='C')
				$dx=($w-$w_txt)/2;
			else
				$dx=$this->cMargin;
			$txt=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));
			if($this->ColorFlag)
				$s.='q '.$this->TextColor.' ';
			$s.=sprintf('q BT %.2F %.2F Td %.2F Tz (%s) Tj ET Q ',
						($this->x+$dx)*$k,
						($this->h-($this->y+.5*$h+.3*$this->FontSize))*$k,
						$Tz,$txt);
			if($this->underline)
				$s.=' '.$this->_dounderline($this->x+$dx,$this->y+.5*$h+.3*$this->FontSize,$txt);
			if($this->ColorFlag)
				$s.=' Q ';
			if($link)
				$this->Link($this->x+$dx,$this->y+.5*$h-.5*$this->FontSize,$w_txt,$this->FontSize,$link);
		}
	}
// end change Cell function
	if($s)
		$this->_out($s);
	$this->lasth=$h;
	if($ln>0)
	{
		//Go to next line
		$this->y+=$h;
		if($ln==1)
			$this->x=$this->lMargin;
	}
	else
		$this->x+=$w;
}

function startPageNums() {
        $this->_numbering=true;
        $this->_numberingFooter=true;
}

function stopPageNums() {
        $this->_numbering=false;
}

function numPageNo() {
        return $this->_numPageNum;
}

function TOC_Entry($txt, $level=0) {
        $this->_toc[]=array('t'=>$txt,'l'=>$level,'p'=>$this->numPageNo());
}

function insertTOC( $location=1,$labelSize=20,$entrySize=10,$tocfont='Arial',$label='Table of Contents') {
        //make toc at end
        $this->stopPageNums();
        $this->AddPage('L');
        $tocstart=$this->page;

        $this->SetFont($tocfont,'B',$labelSize);
        $this->Cell(0,5,$label,0,1,'C');
        $this->Ln(10);

        foreach($this->_toc as $t) {

                //Offset
                $level=$t['l'];
                if($level>0)
                        $this->Cell($level*8);
                $weight='';
                if($level==0)
                        $weight='B';
                $str=$t['t'];
                $this->SetFont($tocfont,$weight,$entrySize);
                $strsize=$this->GetStringWidth($str);
                $this->Cell($strsize+2,$this->FontSize+2,$str);

                //Filling dots
                $this->SetFont($tocfont,'',$entrySize);
                $PageCellSize=$this->GetStringWidth($t['p'])+2;
                $w=$this->w-$this->lMargin-$this->rMargin-$PageCellSize-($level*8)-($strsize+2);
                $nb=$w/$this->GetStringWidth('.');
                $dots=str_repeat('.',$nb);
                $this->Cell($w,$this->FontSize+2,$dots,0,0,'R');

                //Page number
                $this->Cell($PageCellSize,$this->FontSize+2,$t['p'],0,1,'R');
        }

        //Grab it and move to selected location
        $n=$this->page;
        $n_toc = $n - $tocstart + 1;
        $last = array();

        //store toc pages
        for($i = $tocstart;$i <= $n;$i++)
                $last[]=$this->pages[$i];

        //move pages
        for($i=$tocstart-1;$i>=$location-1;$i--)
                $this->pages[$i+$n_toc]=$this->pages[$i];

        //Put toc pages at insert point
        for($i = 0;$i < $n_toc;$i++)
                $this->pages[$location + $i]=$last[$i];
}

public function SetTitle($title) {
    $this->title = $title;
}

public function getTitle() {
    return $this->title;
}

function searchForId($id, $array) {
   $status = 0;
   foreach ($array as $key => $val) {
       if (array_key_exists($id, $val)) {
           $status++;
       }
   }
   return $status;
}

public function Header(){
    $date = date("j F Y");
    //Logo
    $this->Ln(13);
    $this->image('public/img/logo.jpg',20,20,0,20);
    //Arial bold 15
    $this->Ln(5);
    $this->SetFont('Arial','',15);
    $this->Cell(110,1,'University of Malawi',0,0,'C');
    $this->Ln(7);
    $this->SetFont('Arial','B',13);
    //Title
    $this->Cell(110,1,'The Polytechnic',0,0,'C');
    //Line break
    $this->SetFont('Arial','B',7);
    $this->SetLeftMargin(20);
    $this->Ln(10);
    //$this->address();
    $this->SetFont('Arial','B',12);
    $this->SetTextColor(0);
    $this->SetFillColor(255);
    $this->Cell(258,7,  $this->getTitle(),1,1,'L',1);
    $this->Ln(3);
    
}

function getPageHeader(){
    $this->SetFont('Arial','B',12);
    //$this->SetTitle($this->getTitle());
    $this->AddPage('L');
    $this->startPageNums();
    $this->SetLeftMargin(20);
}

function address(){
    
    $this->Cell(47,10,'Principal',0,0,'L');
    //$this->Cell(108,10,'College of medicine',0,0,'R');
    $this->Cell(210,10,'Private Bag 360',0,0,'R');
    $this->Ln(3);
    
    $this->SetFont('Arial','B',7);
    $this->Cell(80,10,'M H C Mipando, MSc,PhD',0,0,'L');
    $this->Cell(177,10,'Chichiri',0,0,'R');
    $this->Ln(3);
    $this->SetFont('Arial','B',7);
    $this->Cell(80,10,'',0,0,'C');
    $this->Cell(177,10,'Blantyre 3',0,0,'R');
    $this->Ln(3);
    $this->SetFont('Arial','B',7);
    $this->Cell(80,10,'',0,0,'C');
    $this->Cell(177,10,'Malawi',0,0,'R');
    $this->Ln(15);
}

function table($header,$data){
        global $showheader;
        //Header
        $this->SetFont('Arial','B',11);
       
        //for($i=0;$i<count($header);$i++)
        $h = ($this->searchForId('O', $header))?20:6;
        foreach($header as $headerTitle){
           
           if(array_key_exists('O',$headerTitle)){ 
            $this->VCell(7,$headerTitle['W'],$headerTitle['V'],1,0,'D');
           }else{
              
               $this->Cell($headerTitle['W'],$h,$headerTitle['V'],1,0,'U');
           }
        }
        $this->Ln();
        $y = 1;
        
        
        $this->SetFont('Arial','',10);
        foreach($data as $row)
        {
            //$val = array_values($row);
            $key = 0;
            foreach($header as $headerTitle){
                if(array_key_exists($key,$row)){
                    $width = (strlen($row[$key])<=2 && array_key_exists('O',$headerTitle))?7:$headerTitle['W'];
                    $this->Cell($width,6,$row[$key],1,0); 
                }
                $key++;
            }
            
            $this->Ln();
            $y++;
            $showheader = 0;
        }
    
//    $this->Ln(3);
//    $this->SetFillColor(81,160,225);
//    $this->Cell(258,5,'',1,0,'C',1);

}


function Footer() {
        if(!$this->_numberingFooter)
                return;
        //Go to 1.5 cm from bottom
        $this->SetY(-15);
        //Select Arial italic 8
        $this->SetFont('Arial','',8);
        $this->Cell(130,7,'Confidential',0,0,'L');
        $this->SetFont('Arial','B',8);
        $this->Cell(120,7,$this->numPageNo(),0,0,'L'); 
        $this->SetFont('Arial','',8);
        $this->Cell(400,7,$this->date,0,0,'Y',0,0,'R'); 
        if(!$this->_numbering)
                $this->_numberingFooter=false;
}
}

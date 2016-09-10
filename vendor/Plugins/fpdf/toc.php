<?php
require('fpdf.php');

class PDF_TOC extends FPDF {
	var $_toc=array();
	var $_numbering=false;
	var $_numberingFooter=false;
	var $_numPageNum=1;
	var $B=0;
    var $I=0;
    var $U=0;
    var $HREF='';
    var $ALIGN='';
	var $col=0;
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
	
function gradeBook($courses,$students,$AcYr,$semester,$class,$prevcourses=""){ 

    global $showbanner;
    global $divisor;
    global $dbConn;
    global $classCategory;
    $showbanner=0;
	

    //Get program class details
    $searchArray[] = array("tbl_class.id"=>$class);
    $rsClass = getProgrammeClasses($searchArray);
    $oClass = $rsClass->fetchRow(DB_FETCHMODE_OBJECT);
    $classCategory = $oClass->classcategoryid;

    

    $this->SetFont('Times','B',10);
    
    $this->cell(7,32,'No',1,0,'L');
    $this->cell(34,32,'Registration. No.',1,0,'L');
    $this->cell(35,32,'Surname',1,0,'L');
    $this->cell(35,32,'Firstname',1,0);

    $this->VCell(6,32,' Sex',1,0,'D');

    $this->VCell(6,32,'  ',1,0,'D');

    if($prevcourses != ""){
     foreach($prevcourses as $prevcourse){
        $this->VCell(6,32,$prevcourse['COURSE_CODE'],1,0,'D');
     }
	$this->VCell(2,32,"",1,0,'D');
    }
    foreach($courses as $course){
        $this->VCell(6,32,$course['COURSE_CODE'],1,0,'D');
    }
	
	$this->VCell(6,32,'  ',1,0,'D');
    
	if($semester!=1){
    		$this->VCell(6,32,'WN',1,0,'D');
    	}
	
    
    $this->VCell(6,32,'History',1,0,'D');
    if($oClass->classcategoryid==2){
     $this->VCell(6,32,'Average',1,0,'D');
    }
    $this->VCell(9,32,'Remark',1,0,'D');
    
    //Display all the data
    $this->Ln(32);
    $y = 1;
    foreach($students as $student)
    {
    $this->SetFont('Times','',10);   
    $this->Cell(7,5,$y,1,'C'); 
    $this->Cell(34,5,$student['regNum'],1);  
	$this->SetFont('Times','',9); 
   $this->Cell(35,5,strtoupper($student['Surname']),1); 
  
    $this->Cell(35,5,strtoupper($student['Firstname']),1);
  	$this->SetFont('Times','',10);
    $this->Cell(6,5,$student['Sex'],1,0,'L');
    
	//$pavg = ($this->IsFinalClass($class))?$this->PAverage($student['regNum'],$AcYr):'';
	
    //$this->Cell(6,5,$pavg,1,0,'L');
    $this->Cell(6,5,'',1,0,'L');
    
   $totalAvg =0;
   $total =0;
   $i = 0;


 if($prevcourses != ""){
     foreach($prevcourses as $prevcourse){
			//$this->SetFillColor(0,0,0);
		 		
			$searchArray[] = array("tbl_class.id"=>$class);
    		       $rsClass = getProgrammeClasses($searchArray);
    		       $oClass = $rsClass->fetchRow(DB_FETCHMODE_OBJECT);

			$search = array();
			$search[] = array("tbl_class.programme_id"=>$oClass->programmeid);
			$search[] = array("tbl_class.year"=>$oClass->yearofstudy-1);

			$rsPClass = getProgrammeClasses($search);
    			$oPClass = $rsPClass->fetchRow(DB_FETCHMODE_OBJECT);
			
			$prevyear = PreviousAcademicYear();





			$avg = $this->subjResult($prevcourse['COURSEID'],$student,$prevyear->id,$oPClass->classid,$semester,$prevcourse['MODULARITY']);
			if(is_numeric($avg) && $prevcourse['DISPLAYMODE']=='GRADE'){
				$i++;
				$total += $avg;
			}
			
			if($course['DISPLAYMODE']=='LETTER' && is_numeric($avg)){
				if($avg<50){
					$avg = 'F';
				}else{
					$avg = 'P';
				}
			}
			
			
			if(empty($avg) && $avg !=0 ){
				$avg = '-';
			}
			
			
			
			if(($avg < 50 && $avg !='-' && is_numeric($avg)) or $avg=='F'){
				$this->SetFont('Times','B',10);
				$this->SetTextColor(255);
				$this->SetFillColor(0,0,0);
				$this->Cell(6,5,$avg,1,0,'L',true);
			}
			else
			{
				$this->SetFont('Times','',10);
				$this->SetTextColor(0);
				$this->SetFillColor(255,255,255);
				$this->Cell(6,5,$avg,1,0,'L');
			}
        
   	
    }
	$this->Cell(2,5,"",1,0,'L');	


 }


 foreach($courses as $course){
			//$this->SetFillColor(0,0,0);
		 		
			$avg = $this->subjResult($course['COURSEID'],$student,$AcYr,$class,$semester,$course['MODULARITY']);
			if(is_numeric($avg) && $course['DISPLAYMODE']=='GRADE'){
				$i++;
				$total += $avg;
			}
			
			if($course['DISPLAYMODE']=='LETTER' && is_numeric($avg)){
				if($avg<50){
					$avg = 'F';
				}else{
					$avg = 'P';
				}
			}
			
			
			if(empty($avg) && $avg !=0 ){
				$avg = '-';
			}
			
			
			
			if(($avg < 50 && $avg !='-' && is_numeric($avg)) or $avg=='F'){
				$this->SetFont('Times','B',10);
				$this->SetTextColor(255);
				$this->SetFillColor(0,0,0);
				$this->Cell(6,5,$avg,1,0,'L',true);
			}
			else
			{
				$this->SetFont('Times','',10);
				$this->SetTextColor(0);
				$this->SetFillColor(255,255,255);
				$this->Cell(6,5,$avg,1,0,'L');
			}
        
   
    }
	
	$totalAvg =($total==0)?'-':round($total/$i);
	
	$this->SetFont('Times','',8);
            $this->SetTextColor(0);
            $this->SetFillColor(255,255,255);
    $this->Cell(6,5,'',1,0,'L');
	$oRemark = $this->semRemark($student['id'],$AcYr);
    if($semester!=1)
    {
		$this->Cell(6,5,$oRemark->ResultComment,1,0,'L');
    } 
    $oHistory = $this->repeatHistory($student['id']);

    $this->SetFont('Times','',10);
            $this->SetTextColor(0);
            $this->SetFillColor(255,255,255);
      $this->Cell(6,5,$oHistory->history,1,0,'L');
    
		$sqlFG = sprintf("
								select 	FG, ResultComment
								from tbl_studentmeangrade 
								where student = '%s'
								and academicYear_id = '%s'
								and semester_id = '%s'
								
							 "
							 ,escapeString($student['id'])
							 ,escapeString($AcYr)
							 ,escapeString($semester)									 
					  );
					
		$rsFG = $dbConn->query($sqlFG);
		$oFG = $rsFG->fetchRow(DB_FETCHMODE_OBJECT);

	if($oClass->classcategoryid==2){
        	$this->Cell(6,5,$totalAvg,1,0,'L');
	}

    	//$this->Cell(6,5,$oFG->FG,1,0,'L');
    	if($oFG->ResultComment=='FC'){
    		$this->SetFont('Times','B',10);
		$this->SetTextColor(0);
		$this->SetFillColor(190,190,190);
	}else{
		$this->SetFont('Times','',10);
		$this->SetTextColor(0);
		$this->SetFillColor(255,255,255);

	}
	$this->Cell(9,5,$oFG->ResultComment,1,0,'L',true);
    
	$this->Ln();
    $y++;
    }
    $this->Ln(11);   
} 

function getCourseIncompleteStatus($acYr,$semester,$course,$class,$modularity){
	global $dbConn;
	$sql = sprintf("
						select
						  count(student_id) as numCount
						from tbl_studentclass
						  join tbl_student
							on (tbl_student.id = tbl_studentclass.student_id)
						where semester_id = '%s'
							and academicYear_id = '%s'
							and class_id = '%s'
							and (tbl_student.id not in(select
														student_id
													  from tbl_studentgrade
													  where class_id = '%s'
														  and if('%s'=0,semester = '%s','%s'=1)
														  and academicYear_id = '%s'
														  and course_code = '%s'
														  and ExamType = 1)
								or tbl_student.id not in(select
														student_id
													  from tbl_studentgrade
													  where class_id = '%s'
														  and if('%s'=0,semester = '%s','%s'=1)
														  and academicYear_id = '%s'
														  and course_code = '%s'
														  and finalgrade != 'N/E'
														  and ExamType = 1))
								and tbl_student.id not in (select
															  student_id
															from tbl_studentstatus
															where academicYear_id = '%s')
						"
						 ,escapeString($semester)
						 ,escapeString($acYr)
					     ,escapeString($class)
						 ,escapeString($class)
						 ,escapeString($modularity)
						 ,escapeString($semester)
						 ,escapeString($modularity)
						 ,escapeString($acYr)
						 ,escapeString($course)
						 ,escapeString($class)
						 ,escapeString($modularity)
						 ,escapeString($semester)
						 ,escapeString($modularity)
						 ,escapeString($acYr)
						 ,escapeString($course)
						 ,escapeString($acYr)
						);

	$rs = $dbConn->query($sql);
	$o = $rs->fetchRow(DB_FETCHMODE_OBJECT);
	return $o->numCount;
}

function getCourseAggregates($acYr,$semester,$course,$class,$modularity){
	global $dbConn;
	$sqlAggr = sprintf("
						select
						  round(AVG(if(finalGrade REGEXP '^[0-9]+$' and '%s'='GRADE',finalGrade,0 ))) as average,
						  round(min(if(finalGrade REGEXP '^[0-9]+$' and '%s'='GRADE',finalGrade,0 ))) as lowest,
						  max(if(finalGrade  REGEXP '^[0-9]+$' and '%s'='GRADE',finalGrade,0)) as highest,
						  sum(if('%s'='GRADE',finalGrade>=75,0)) as DN,
						  sum(if('%s'='GRADE',finalGrade BETWEEN 60 and 74,0)) as CR,
						  sum(if(locate('P',finalGrade)!=0,1, if('%s'='GRADE',IF((finalGrade BETWEEN 50 and 59),1,0),finalGrade>=50)      )) as PS ,
						  sum(if(locate('F',finalGrade)!=0,1, if('%s'='GRADE',if((finalGrade BETWEEN 0 and 49),1,0),finalGrade<50))) as FAIL,
						  sum(finalGrade >=50) as totalPass,
						  sum(finalGrade < 50) as totalFail
						from tbl_studentgrade
						where ExamType = 1
							and academicYear_id = '%s'
							and if('%s'=0,semester = '%s','%s'=1)
							and course_code = '%s'
							and class_id = '%s'
						       						"
						,escapeString($course['DISPLAYMODE']) 
						,escapeString($course['DISPLAYMODE'])
						,escapeString($course['DISPLAYMODE']) 
						,escapeString($course['DISPLAYMODE']) 
						,escapeString($course['DISPLAYMODE'])
						,escapeString($course['DISPLAYMODE'])
						,escapeString($course['DISPLAYMODE'])
						,escapeString($acYr)
						 ,$modularity
					        ,escapeString($semester)
						 ,$modularity
						 ,escapeString($course['COURSEID'])	
						 ,escapeString($class)
						);

	$rs = $dbConn->query($sqlAggr);
	return $rs;
	
}

function getSemester1Summary($acYr,$class){
	global $dbConn;
	$sql = sprintf("
				   		select
						  sum(ResultComment='SW' AND sex = 'M') as sw_male,
						  sum(ResultComment='SW' AND sex = 'F') as sw_female,
						  sum(ResultComment='LW' AND sex = 'M') as lw_male,
						  sum(ResultComment='LW' AND sex = 'F') as lw_female,
						  sum(ResultComment='PS' AND sex = 'M') as ps_male,
						  sum(ResultComment='PS' AND sex = 'F') as ps_female,
						  sum(ResultComment='SW') as sw_total,
						  sum(ResultComment='LW') as lw_total,
						  sum(ResultComment='PS') as ps_total
						from tbl_studentmeangrade
						  LEFT JOIN tbl_student
							ON (tbl_student.id = tbl_studentmeangrade.student)
						where academicYear_id = '%s'
							and semester_id = '1'
							and class_id = '%s'
				   "
				   ,escapeString($acYr)	
				   ,escapeString($class)
				 );
	$rs = $dbConn->query($sql);
	$o = $rs->fetchRow(DB_FETCHMODE_OBJECT);
	return $o;
	
}

function getSemester2Remarks($acYr,$class){
	global $dbConn;
	$otherCodes = array('RF','RT','FW','SP','DF','WD','RW','RR');
	$codes =  array();
	$sql = sprintf("
				   		select
						  ResultComment
						from tbl_studentmeangrade
						where academicYear_id = '%s'
						and semester_id = '2'
						and class_id = '%s'
						group by ResultComment
				   "
				   ,escapeString($acYr)	
				   ,escapeString($class)
				 );
	$rs = $dbConn->query($sql);
	while($o = $rs->fetchRow(DB_FETCHMODE_OBJECT)){
		$codes[] = $o->ResultComment;
	}
	$code = array_unique(array_merge($codes,$otherCodes));
	return $code;
}

function getSemester2Aggregate($remark,$acYr,$class){
	global $dbConn;
	$sql = sprintf("
				   		select
						  if(sum(sex = 'M') !=0,sum(sex = 'M'),0) as male,
						  if(sum(sex = 'M') + sum(sex = 'F') !=0,sum(sex = 'M') + sum(sex = 'F'),0) as total,
						  if(sum( sex = 'F') !=0,sum( sex = 'F'),0) as female
						from tbl_studentmeangrade
						  LEFT JOIN tbl_student
							ON (tbl_student.id = tbl_studentmeangrade.student)
						where academicYear_id = '%s'
							and semester_id = '2'
							and class_id = '%s'
						and ResultComment = '%s'
				   "
				   ,escapeString($acYr)	
				   ,escapeString($class)
				   ,escapeString($remark)
				 );
	$rs = $dbConn->query($sql);
	$o = $rs->fetchRow(DB_FETCHMODE_OBJECT);
	return $o;
}

function semRemark($student,$AcYr){
	global $dbConn;
	$sql = sprintf("
				   		select
						  ResultComment,
						  if(repeatStatus != 0,CONCAT('R',repeatStatus),'') AS rstatus
						from tbl_studentmeangrade
						  LEFT JOIN tbl_student
							ON (tbl_student.id = tbl_studentmeangrade.student)
						where academicYear_id = '%s'
							and semester_id = '1'
							and student = '%s'
				   "
				   ,escapeString($AcYr)	
				   ,escapeString($student)
				 );
	$rs = $dbConn->query($sql);
	$o = $rs->fetchRow(DB_FETCHMODE_OBJECT);
	return $o;
	
}

function repeatHistory($student){
	global $dbConn;
	$sql = sprintf("
				   		select
						  if(repeatStatus is not null ,CONCAT('R',if(repeatStatus=0,'FN',repeatStatus)),'') AS history
						from tbl_student
						where id = '%s'
				   "
				   ,escapeString($student)
				 );
	$rs = $dbConn->query($sql);

	$o = $rs->fetchRow(DB_FETCHMODE_OBJECT);
	return $o;
}


function summary($AcYr,$class,$className,$header,$courses,$semester ){
	
	global $showbanner,$dbConn,$acYr;
	
	//Get semester 1 Summary
	$oSem1Summary = $this->getSemester1Summary($AcYr,$class);
	$courseRemarks= $this->getSemester2Remarks($AcYr,$class);
	  
	$headerWidth = count($courseRemarks)*12;
	
	$this->Image('../../images/logo.jpg',20,15,17,25);
	//$this->Image('../../images/logo.jpg',140,15,17,25);
    
    $this->Ln(8);
    
    $this->SetFont('Times','',12);
    //Title
    $this->Cell(90,1,'University of Malawi',0,0,'C');
    //Line break
    $this->Ln();
    $this->SetFont('Times','B',12);
    $this->Cell(90,10,'The College of Medicine',0,0,'C');
    //Line break
    $this->Ln(15);
	
	$this->SetFont('Times','B',12); 
	$showbanner=3;
	$this->cell(260,5,$acYr.' END OF YEAR RESULTS SUMMARY ',0,0,'L');
	$this->Ln(7);
	$this->cell(260,7,' SUMMARY: '.$className,1,0,'L');
	$this->Ln(10);
	 $w=array(8,18,120,9,'20','20',8,8,8,8,8);
    //Generate summary headers
    $this->SetFont('Arial','B',9);
    for($i=0;$i<count($header);$i++){
            if($i ==3) {
                 $this->Cell($w[$i],5,$header[$i],1,0,'L');
            }
            else{
                 $this->Cell($w[$i],5,$header[$i],1,0,'C');
            }
        
        }
	$y=1;
     $this->Ln();
     //Loop through courses
     foreach($courses as $course){
		 $this->SetFont('Arial','',9);
         $this->Cell(8,5,$y,1,0,'C'); 
         $this->Cell(18,5,$course['COURSE_CODE'],1,0,'L');
         $this->Cell(120,5,$course['COURSE_NAME'],1,0,'L');

		 
		 $rAggregates = $this->getCourseAggregates($AcYr,$semester,$course,$class,$course['MODULARITY']);
		 //if($course['DISPLAYMODE']=='GRADE'){
	  	 $oAggregates = $rAggregates->fetchRow(DB_FETCHMODE_OBJECT);
		 //}
	
		 $this->Cell(9,5,$oAggregates->average,1,0,'C');
		 $this->Cell(20,5,$oAggregates->highest,1,0,'C');
		 $this->Cell(20,5,$oAggregates->lowest,1,0,'C');
		 $this->Cell(8,5,$oAggregates->DN,1,0,'C');
		 $this->Cell(8,5,$oAggregates->CR,1,0,'C');
		 $this->Cell(8,5,$oAggregates->PS,1,0,'C');
		 $this->Cell(8,5,$oAggregates->FAIL,1,0,'C');
		 
		 $this->Cell(8,5,$this->getCourseIncompleteStatus($AcYr,$semester,$course['COURSEID'],$class,$course['MODULARITY']),1,0,'C');
		 
		 $y++;
         $this->Ln();
	 }
	
	$this->Ln(10);
	$this->SetFont('Times','B',11);
	$this->cell(20,7,'',0,0,'L');
	//$this->cell(70,5,'Semester 1',0,0,'C');
	//if($semester == 2){
		$this->cell($headerWidth,5,'Class Overal Performance Summary',0,0,'C');
	//}
	
	$this->Ln();
	//$this->cell(20,7,'',0,0,'L');
	//$this->cell(40,5,'No. of Students',1,0,'C');
	$this->cell(20,5,'',1,0,'C');
	//$this->cell(3,5,'',0,0,'C');
	//$this->cell(25,5,'Light Warning',1,0,'C');
	//$this->cell(25,5,'Serious Warning',1,0,'C');
	//$this->cell(15,5,'Pass',1,0,'C');
	
	//$this->cell(2,5,'',0,0,'C');
	asort($courseRemarks);
	//if($semester==2){
		
		foreach($courseRemarks as $courseRemark){
			$oAggregate = $this->getSemester2Aggregate($courseRemark,$AcYr,$class);
			$codes[$courseRemark] = array("MALE"=>$oAggregate->male,"FEMALE"=>$oAggregate->female,"TOTAL"=>$oAggregate->total);
			$this->cell(12,5,$courseRemark,1,0,'C');
		}
	//}
	
	$aggregate = $this->studentsAggregate($class,$semester,$AcYr);
	$this->Ln();
	$this->SetFont('Times','',10);
	$this->cell(20,5,'Male',1,0,'R');
	//$this->SetFont('Times','B',11);
	//$this->cell(20,5,$aggregate['male'],1,0,'C');
	$this->SetFont('Times','',10);
	//$this->cell(3,5,'',0,0,'C');
	//$this->cell(25,5,$oSem1Summary->lw_male,1,0,'C');
	//$this->cell(25,5,$oSem1Summary->sw_male,1,0,'C');
	//$this->cell(15,5,$oSem1Summary->ps_male,1,0,'C');
	//$this->cell(2,5,'',0,0,'C');
	//if($semester==2){
		foreach($codes as $code=>$remarkAggregate){
			
			$this->cell(12,5,$remarkAggregate['MALE'],1,0,'C');
		}
	//}
	
	$this->Ln();
	$this->SetFont('Times','',10);
	$this->cell(20,5,'Female',1,0,'R');
	//$this->SetFont('Times','B',11);
	//$this->cell(20,5,$aggregate['female'],1,0,'C');
	//$this->SetFont('Times','',10);
	//$this->cell(3,5,'',0,0,'C');
	//$this->cell(25,5,$oSem1Summary->lw_female,1,0,'C');
	//$this->cell(25,5,$oSem1Summary->sw_female,1,0,'C');
	//$this->cell(15,5,$oSem1Summary->ps_female,1,0,'C');
	//$this->cell(2,5,'',0,0,'C');
	//if($semester==2){
		foreach($codes as $code=>$remarkAggregate){
			
			$this->cell(12,5,$remarkAggregate['FEMALE'],1,0,'C');
		}
	//}
	$this->Ln();
	$this->SetFont('Times','B',10);
	$this->cell(20,5,'Total',1,0,'R');
	//$this->SetFont('Times','B',11);
	$this->SetFont('Times','',10);
	//$this->cell(20,5,$aggregate['total'],1,0,'C');
	//$this->SetFont('Times','',10);
	//$this->cell(3,5,'',0,0,'C');
	//$this->cell(25,5,$oSem1Summary->lw_total,1,0,'C');
	//$this->cell(25,5,$oSem1Summary->sw_total,1,0,'C');
	//$this->cell(15,5,$oSem1Summary->ps_total,1,0,'C');
	//$this->cell(2,5,'',0,0,'C');
    //if($semester==2){
        foreach($codes as $code=>$remarkAggregate){
		
		$this->cell(12,5,$remarkAggregate['TOTAL'],1,0,'C');
	}
        
    //}
	$this->SetFont('Times','B',7);
	$this->Ln(10);
	$this->cell(260,5,'DN=Distinction, CR=Credit, PP=Pass and Proceed, PS=Pass, FC=First Class ,USC=Upper Second Class ,LSC=Lower Second Class ,TC=Third Class, DF=Deferred ,RT=Repeat ,FW=Fail and Withdrawn ,WD=Withdrew ,RR = Remedial rotation ,RW=Results Withheld ,RF=Referal, SP=Suspended, Inc=Incomplete, F=Fail, SD=Suspected Death, PHP= Post Humus Pass ',1,0,'C');
	$this->SetFont('Times','',10);
	
}

function getAllClasses(){
	global $dbConn;
 	$sqlClasses = sprintf("
								select 	tbl_class.id,if(tbl_class.year = 0,concat(tbl_program.name,' FOUNDATION'),tbl_class.code) as classCode,tbl_class.name  
								from tbl_class 
								join tbl_program
								on(tbl_program.id = tbl_class.programme_id)
								where tbl_program.programLevel_id = 5
								and tbl_class.year
								
								order by classCode,tbl_class.year
							");
	$rsClasses = $dbConn->query($sqlClasses);
	return $rsClasses;
}

function getClassSummary($AcYr,$semester,$class){
	global $dbConn;
 	$sqlClasses = sprintf("
								select
  name,
  count(student) as totalStudents,
  sum(ResultComment='DN' OR ResultComment='FC') as DN,
  sum(ResultComment='CR' OR ResultComment='LSC' OR ResultComment='USC') as CR,
  sum(ResultComment='TF') as TF,
  sum(ResultComment='WDT') as TWD,
  sum(ResultComment='PS' OR ResultComment='PP' OR ResultComment='TC' OR ResultComment='P') as PASS,
  sum(ResultComment='WDV') as WDV,
  sum(ResultComment='WDM') as WDM,
  sum(ResultComment='INC') as INC,
  sum(ResultComment='EX') as EX,
  sum(ResultComment='RT') as RT,
  sum(ResultComment='DF') as DF,
  sum(ResultComment='RF') as RF,
  sum(ResultComment='DEC') as 'DEC',
  sum(ResultComment='WD') as WD,
  sum(ResultComment='FW') as FW,
  sum(ResultComment='RR') as RR,
  sum(ResultComment='RW') as RW,
  sum(ResultComment='TF') as TF,
  sum(ResultComment='SP') as SP
from tbl_studentmeangrade
  left join tbl_class
    on (tbl_class.id = tbl_studentmeangrade.class_id)
  join tbl_studentclass
on(tbl_studentclass.student_id = tbl_studentmeangrade.student
   and tbl_studentclass.semester_id = tbl_studentmeangrade.semester_id
   and tbl_studentclass.academicYear_id = tbl_studentmeangrade.academicYear_id 
   and tbl_studentmeangrade.class_id = tbl_studentclass.class_id)
left join tbl_student
on(tbl_student.id = tbl_studentmeangrade.student)
where tbl_studentmeangrade.class_id = '%s'
    and tbl_studentmeangrade.academicYear_id = '%s'
    and tbl_studentmeangrade.semester_id = '%s'
group by tbl_studentmeangrade.class_id
							"
							 ,escapeString($class)
							 ,escapeString($AcYr)
							 ,escapeString($semester)
							);
	//print_r($sqlClasses);
//die();
	$rsClasses = $dbConn->query($sqlClasses);
	return $rsClasses;
}

function getFoundationClass(){
	global $dbConn;
 	$sqlClasses = sprintf("
								select
								  tbl_class.id,
								  tbl_class.code,
								  IF(tbl_program.code = '5004','MBBS',
								  if(tbl_program.code = '5001','MLS',
								  IF(tbl_program.code = '5002','PT','PHARM'))) as className
								from tbl_class
								  LEFT JOIN tbl_program
									ON (tbl_program.id = tbl_class.programme_id)
								where year = '0'
								
							"
							);
	$rsClasses = $dbConn->query($sqlClasses);
	return $rsClasses;
}

function getAllClass($AcYr=''){
	global $dbConn;
 	$sqlClasses = sprintf("
								select distinct
								 tbl_class.id,
								 tbl_class.code,
								 tbl_program.name as className,year
								   from tbl_class
								 LEFT JOIN tbl_program
								   ON (tbl_program.id = tbl_class.programme_id)
								 join tbl_studentclass
								   on (tbl_studentclass.class_id = tbl_class.id
									   and tbl_studentclass.academicYear_id = '%s')
								 where year != '0'
								 and showInGradeBook = 1
								 and programLevel_id = 5
							order by 3,4
							"
							,escapeString($AcYr)
							);
	$rsClasses = $dbConn->query($sqlClasses);
	return $rsClasses;
}

function summaryOfResults($AcYr,$classes,$semester){
	global $dbConn,$showbanner;
	
	$this->Ln();
	$this->SetFont('Times','B',12);
	$this->cell(250,5,'SUMMARY OF EXAMINATION RESULTS',0,0,'C');
	
	$this->Ln(6);
	$this->SetFont('Times','B',8);
	$this->cell(35,5,'PROGRAMME',1,0,'L');
	$this->cell(25,5,'COURSE/YEAR',1,0,'C');
	$this->cell(10,5,'DN',1,0,'C');
	$this->cell(10,5,'CR',1,0,'C');
	$this->cell(10,5,'PASS',1,0,'C');
	$this->cell(10,5,'RF',1,0,'C');
	$this->cell(10,5,'FW',1,0,'C');
	$this->cell(10,5,'RR',1,0,'C');
	$this->cell(10,5,'RT',1,0,'C');
	$this->cell(10,5,'DF',1,0,'C');
	$this->cell(10,5,'RW',1,0,'C');
	$this->cell(10,5,'TF',1,0,'C');
	$this->cell(10,5,'INC',1,0,'C');
	$this->cell(10,5,'EX',1,0,'C');
	$this->cell(10,5,'WDM',1,0,'C');
	$this->cell(10,5,'WDV',1,0,'C');
	$this->cell(10,5,'WDT',1,0,'C');
	$this->cell(10,5,'DEC',1,0,'C');
	$this->cell(10,5,'SP',1,0,'C');

	$this->cell(14,5,'TOTAL',1,0,'C');
	
	$this->Ln();
	//$rsClass = $this->getAllClasses();
	//Get all foundation classes
	$rsClass  = $this->getFoundationClass();
	$prog  = 'FOUNDATION';
	$totalTWD = $totalEX = $totalINC = $totalWDM = $totalWDV = $totalTWD = $grandTotalPass = $total = $totalDN = $totalCR = $totalPASS = $totalRF = $totalFW = $totalRR = $totalRT = $totalDF = $totalRW = $totalRT = $totalRT =$totalRW=$totalTF=$totalWD=$totalDEC = $totalSP = 0;
	$grandTotal = $ptotal =0;
	while($oClass =$rsClass->fetchRow(DB_FETCHMODE_OBJECT)){
				$progLabel = ($prog==$prProg)?'':$prog;
				$rsClassSummary = $this->getClassSummary($AcYr,$semester,$oClass->id);
				$oClassSummary = $rsClassSummary->fetchRow(DB_FETCHMODE_OBJECT);
				$this->SetFont('Times','',9);
				$this->cell(35,5,$progLabel,1,0,'L');
				
				$this->cell(25,5,$oClass->className,1,0,'L');
				$this->cell(10,5,$oClassSummary->DN,1,0,'C');
				$this->cell(10,5,$oClassSummary->CR,1,0,'C');
				$this->cell(10,5,$oClassSummary->PASS,1,0,'C');
				$this->cell(10,5,$oClassSummary->RF,1,0,'C');
				$this->cell(10,5,$oClassSummary->FW,1,0,'C');
				$this->cell(10,5,$oClassSummary->RR,1,0,'C');
				$this->cell(10,5,$oClassSummary->RT,1,0,'C');
				$this->cell(10,5,$oClassSummary->DF,1,0,'C');
				$this->cell(10,5,$oClassSummary->RW,1,0,'C');
				$this->cell(10,5,$oClassSummary->TF,1,0,'C');
				$this->cell(10,5,$oClassSummary->INC,1,0,'C');
				$this->cell(10,5,$oClassSummary->EX,1,0,'C');
				$this->cell(10,5,$oClassSummary->WDM,1,0,'C');
				$this->cell(10,5,$oClassSummary->WDV,1,0,'C');
				$this->cell(10,5,$oClassSummary->TWD,1,0,'C');
				$this->cell(10,5,$oClassSummary->DEC,1,0,'C');
				$this->cell(10,5,$oClassSummary->SP,1,0,'C');
				$this->cell(14,5,$oClassSummary->totalStudents,1,0,'C');
				$this->Ln();
				$total += $oClassSummary->totalStudents;
				$grandTotal += $oClassSummary->totalStudents;
				$grandTotalCR += $oClassSummary->CR;
				$grandTotalDN += $oClassSummary->DN;
				$grandTotalRF += $oClassSummary->RF;
				$grandTotalDF += $oClassSummary->DF;
				$grandTotalDEC += $oClassSummary->DEC;
				$grandTotalSP += $oClassSummary->SP;
				$grandTotalFW += $oClassSummary->FW;
				$grandTotalRR += $oClassSummary->RR;
				$grandTotalRT += $oClassSummary->RT;
				$grandTotalINC += $oClassSummary->INC;
				$grandTotalEX += $oClassSummary->EX;
				$grandTotalWDM += $oClassSummary->WDM;
				$grandTotalWDV += $oClassSummary->WDV;
				$grandTotalRW += $oClassSummary->RW;
				$grandTotalTF += $oClassSummary->TF;
				$grandTotalPASS += $oClassSummary->PASS;
				$grandTotalTWD += $oClassSummary->TWD;
				
				//Getting totals
				$totalDN += $oClassSummary->DN;
				$totalCR += $oClassSummary->CR;
				$totalPASS += $oClassSummary->PASS; 
				$totalFW += $oClassSummary->FW;
				$totalRR += $oClassSummary->RR;
				$totalDF += $oClassSummary->DF; 
				$totalRF += $oClassSummary->RF;
				$totalRT += $oClassSummary->RT;
				$totalRW += $oClassSummary->RW;
				$totalTF += $oClassSummary->TF;
				$totalEX += $oClassSummary->EX;
				$totalINC += $oClassSummary->INC;
				$totalWDM += $oClassSummary->WDM;
				$totalWDV += $oClassSummary->WDV;
				$totalTWD += $oClassSummary->TWD;
				$totalDEC += $oClassSummary->DEC;
				$totalSP += $oClassSummary->SP;
				$prProg=$prog ;
		}
				//$grandTotal += $total;
				$ptotal += $total;
				$this->SetFont('Times','B',9);
				/*$this->cell(35,5,'TOTAL',1,0,'L');
				$this->cell(25,5,'',1,0,'C');
				$this->cell(12,5,$totalDN,1,0,'C');
				$this->cell(12,5,$totalCR,1,0,'C');
				$this->cell(12,5,$totalPASS,1,0,'C');
				$this->cell(12,5,$totalRF,1,0,'C');
				$this->cell(12,5,$totalFW,1,0,'C');
				$this->cell(12,5,$totalRR,1,0,'C');
				$this->cell(12,5,$totalRT,1,0,'C');
				$this->cell(12,5,$totalDF,1,0,'C');
				$this->cell(12,5,$totalRW,1,0,'C');
				$this->cell(12,5,$totalTF,1,0,'C');
				$this->cell(12,5,$totalINC,1,0,'C');
				$this->cell(12,5,$totalEX,1,0,'C');
				$this->cell(12,5,$totalWDM,1,0,'C');
				$this->cell(12,5,$totalWDV,1,0,'C');
				$this->cell(12,5,$totalTWD,1,0,'C');
				$this->cell(12,5,$totalDEC,1,0,'C');
				$this->cell(12,5,$totalSP,1,0,'C');
				$this->cell(14,5,$ptotal,1,0,'C');*/

				$this->SetFont('Times','B',10);
				$this->SetTextColor(0);
				$this->SetFillColor(230,230,230);
				$this->cell(35,5,'TOTAL',1,0,'L',true);
				$this->cell(25,5,'',1,0,'C',true);
				$this->cell(10,5,$totalDN,1,0,'C',true);
				$this->cell(10,5,$totalCR,1,0,'C',true);
				$this->cell(10,5,$totalPASS,1,0,'C',true);
				$this->cell(10,5,$totalRF,1,0,'C',true);
				$this->cell(10,5,$totalFW,1,0,'C',true);
				$this->cell(10,5,$totalRR,1,0,'C',true);
				$this->cell(10,5,$totalRT,1,0,'C',true);
				$this->cell(10,5,$totalDF,1,0,'C',true);
				$this->cell(10,5,$totalRW,1,0,'C',true);
				$this->cell(10,5,$totalTF,1,0,'C',true);
				$this->cell(10,5,$totalINC,1,0,'C',true);
				$this->cell(10,5,$totalEX,1,0,'C',true);
				$this->cell(10,5,$totalWDM,1,0,'C',true);
				$this->cell(10,5,$totalWDV,1,0,'C',true);
				$this->cell(10,5,$totalTWD,1,0,'C',true);
				$this->cell(10,5,$totalDEC,1,0,'C',true);
				$this->cell(10,5,$totalSP,1,0,'C',true);
				$this->cell(14,5,$ptotal,1,0,'C',true);
				$this->Ln();
		$showbanner = 8;		
	$rsClasses  = $this->getAllClass($AcYr);
	$prProg = '';
	$progLabel = 1;
	$count = 0;
	$totalWDM = $totalEX = $totalINC = $totalWDM = $totalWDV = $totalTWD = $ptotal = $totalDN = $totalCR = $totalPASS = $totalRF = $totalFW = $totalRR = $totalRT = $totalDF = $totalRW = $totalRT = $totalRT =$totalRW=$totalTF=$totalWD=$totalDEC = $totalSP = 0;
	while($oClasses =$rsClasses->fetchRow(DB_FETCHMODE_OBJECT)){
				$progLabel = ($oClasses->className==$prProg)?'':$oClasses->className;
				
				$rsClassSummary = $this->getClassSummary($AcYr,$semester,$oClasses->id);
				$oClassSummary = $rsClassSummary->fetchRow(DB_FETCHMODE_OBJECT);
				
				if($prProg!=$oClasses->className && $progLabel !='' && $count!=0){
					
					/*$this->SetFont('Times','B',9);
					$this->cell(35,5,'TOTAL',1,0,'L');
					$this->cell(25,5,'',1,0,'C');
					$this->cell(12,5,$totalDN,1,0,'C');
					$this->cell(12,5,$totalCR,1,0,'C');
					$this->cell(12,5,$totalPASS,1,0,'C');
					$this->cell(12,5,$totalRF,1,0,'C');
					$this->cell(12,5,$totalFW,1,0,'C');
					$this->cell(12,5,$totalRR,1,0,'C');
					$this->cell(12,5,$totalRT,1,0,'C');
					$this->cell(12,5,$totalDF,1,0,'C');
					$this->cell(12,5,$totalRW,1,0,'C');
					$this->cell(12,5,$totalTF,1,0,'C');
					$this->cell(12,5,$totalINC,1,0,'C');
					$this->cell(12,5,$totalEX,1,0,'C');
					$this->cell(12,5,$totalWDM,1,0,'C');
					$this->cell(12,5,$totalWDV,1,0,'C');
					$this->cell(12,5,$totalTWD,1,0,'C');
					$this->cell(12,5,$totalDEC,1,0,'C');
					$this->cell(12,5,$totalSP,1,0,'C');
					$this->cell(14,5,$ptotal,1,0,'C');*/
					
					$this->SetFont('Times','B',10);
					$this->SetTextColor(0);
					$this->SetFillColor(230,230,230);
					$this->cell(35,5,'TOTAL',1,0,'L',true);
					$this->cell(25,5,'',1,0,'C',true);
					$this->cell(10,5,$totalDN,1,0,'C',true);
					$this->cell(10,5,$totalCR,1,0,'C',true);
					$this->cell(10,5,$totalPASS,1,0,'C',true);
					$this->cell(10,5,$totalRF,1,0,'C',true);
					$this->cell(10,5,$totalFW,1,0,'C',true);
					$this->cell(10,5,$totalRR,1,0,'C',true);
					$this->cell(10,5,$totalRT,1,0,'C',true);
					$this->cell(10,5,$totalDF,1,0,'C',true);
					$this->cell(10,5,$totalRW,1,0,'C',true);
					$this->cell(10,5,$totalTF,1,0,'C',true);
					$this->cell(10,5,$totalINC,1,0,'C',true);
					$this->cell(10,5,$totalEX,1,0,'C',true);
					$this->cell(10,5,$totalWDM,1,0,'C',true);
					$this->cell(10,5,$totalWDV,1,0,'C',true);
					$this->cell(10,5,$totalTWD,1,0,'C',true);
					$this->cell(10,5,$totalDEC,1,0,'C',true);
					$this->cell(10,5,$totalSP,1,0,'C',true);
					$this->cell(14,5,$ptotal,1,0,'C',true);

				
					$this->Ln();	
					$totalTWD = $totalEX = $totalINC = $totalWDM = $totalWDV = $totalTWD = $ptotal = $totalDN = $totalCR = $totalPASS = $totalRF = $totalRR = $totalFW = $totalRT = $totalDF = $totalRW = $totalRT = $totalRT =$totalRW=$totalTF=$totalWD=$totalDEC = $totalSP = 0;
				}
				
				//$grandTotal += $ptotal;
				$this->SetFont('Times','',9);
				$this->cell(35,5,$progLabel,1,0,'L');
				$this->cell(25,5,$oClasses->year,1,0,'C');
				$this->cell(10,5,$oClassSummary->DN,1,0,'C');
				$this->cell(10,5,$oClassSummary->CR,1,0,'C');
				$this->cell(10,5,$oClassSummary->PASS,1,0,'C');
				$this->cell(10,5,$oClassSummary->RF,1,0,'C');
				$this->cell(10,5,$oClassSummary->FW,1,0,'C');
				$this->cell(10,5,$oClassSummary->RR,1,0,'C');
				$this->cell(10,5,$oClassSummary->RT,1,0,'C');
				$this->cell(10,5,$oClassSummary->DF,1,0,'C');
				$this->cell(10,5,$oClassSummary->RW,1,0,'C');
				$this->cell(10,5,$oClassSummary->TF,1,0,'C');
				$this->cell(10,5,$oClassSummary->INC,1,0,'C');
				$this->cell(10,5,$oClassSummary->EX,1,0,'C');
				$this->cell(10,5,$oClassSummary->WDM,1,0,'C');
				$this->cell(10,5,$oClassSummary->WDV,1,0,'C');
				$this->cell(10,5,$oClassSummary->TWD,1,0,'C');
				$this->cell(10,5,$oClassSummary->DEC,1,0,'C');
				$this->cell(10,5,$oClassSummary->SP,1,0,'C');
				$this->cell(14,5,$oClassSummary->totalStudents,1,0,'C');
				$this->Ln();

				$ptotal += $oClassSummary->totalStudents;
				$total += $oClassSummary->totalStudents;
				$grandTotal += $oClassSummary->totalStudents;
				$grandTotalCR += $oClassSummary->CR;
				$grandTotalDN += $oClassSummary->DN;
				$grandTotalRF += $oClassSummary->RF;
				$grandTotalDF += $oClassSummary->DF;
				$grandTotalDEC += $oClassSummary->DEC;
				$grandTotalSP += $oClassSummary->SP;
				$grandTotalFW += $oClassSummary->FW;
				$grandTotalRR += $oClassSummary->RR;
				$grandTotalRT += $oClassSummary->RT;
				$grandTotalINC += $oClassSummary->INC;
				$grandTotalEX += $oClassSummary->EX;
				$grandTotalWDM += $oClassSummary->WDM;
				$grandTotalWDV += $oClassSummary->WDV;
				$grandTotalTWD += $oClassSummary->TWD;
				$grandTotalRW += $oClassSummary->RW;
				$grandTotalTF += $oClassSummary->TF;
				$grandTotalPASS += $oClassSummary->PASS;
				
				
				//Getting totals
				$totalDN += $oClassSummary->DN;
				$totalCR += $oClassSummary->CR;
				$totalPASS += $oClassSummary->PASS; 
				$totalFW += $oClassSummary->FW;
				$totalRR += $oClassSummary->RR;
				$totalDF += $oClassSummary->DF; 
				$totalRF += $oClassSummary->RF;
				$totalRT += $oClassSummary->RT;
				$totalRW += $oClassSummary->RW;
				$totalTF += $oClassSummary->TF;
				$totalEX += $oClassSummary->EX;
				$totalINC += $oClassSummary->INC;
				$totalWDM += $oClassSummary->WDM;
				$totalWDV += $oClassSummary->WDV;
				$totalTWD += $oClassSummary->TWD;
				$totalDEC += $oClassSummary->DEC;
				$totalSP += $oClassSummary->SP;
				
				
				$prProg=$oClasses->className ;
				$count++;
				
				
		}
				
				
				/*$this->SetFont('Times','B',9);
				$this->cell(35,5,'TOTAL',1,0,'L');
				$this->cell(25,5,'',1,0,'C');
				$this->cell(12,5,$totalDN,1,0,'C');
				$this->cell(12,5,$totalCR,1,0,'C');
				$this->cell(12,5,$totalPASS,1,0,'C');
				$this->cell(12,5,$totalRF,1,0,'C');
				$this->cell(12,5,$totalFW,1,0,'C');
				$this->cell(12,5,$totalRR,1,0,'C');
				$this->cell(12,5,$totalRT,1,0,'C');
				$this->cell(12,5,$totalDF,1,0,'C');
				$this->cell(12,5,$totalRW,1,0,'C');
				$this->cell(12,5,$totalTF,1,0,'C');
				$this->cell(12,5,$totalINC,1,0,'C');
				$this->cell(12,5,$totalEX,1,0,'C');
				$this->cell(12,5,$totalWDM,1,0,'C');
				$this->cell(12,5,$totalWDV,1,0,'C');
				$this->cell(12,5,$totalDEC,1,0,'C');
				$this->cell(12,5,$totalSP,1,0,'C');
				$this->cell(14,5,$ptotal,1,0,'C');*/

				$this->SetFont('Times','B',10);
				$this->SetTextColor(0);
				$this->SetFillColor(230,230,230);
				$this->cell(35,5,'TOTAL',1,0,'L',true);
				$this->cell(25,5,'',1,0,'C',true);
				$this->cell(10,5,$totalDN,1,0,'C',true);
				$this->cell(10,5,$totalCR,1,0,'C',true);
				$this->cell(10,5,$totalPASS,1,0,'C',true);
				$this->cell(10,5,$totalRF,1,0,'C',true);
				$this->cell(10,5,$totalFW,1,0,'C',true);
				$this->cell(10,5,$totalRR,1,0,'C',true);
				$this->cell(10,5,$totalRT,1,0,'C',true);
				$this->cell(10,5,$totalDF,1,0,'C',true);
				$this->cell(10,5,$totalRW,1,0,'C',true);
				$this->cell(10,5,$totalTF,1,0,'C',true);
				$this->cell(10,5,$totalINC,1,0,'C',true);
				$this->cell(10,5,$totalEX,1,0,'C',true);
				$this->cell(10,5,$totalWDM,1,0,'C',true);
				$this->cell(10,5,$totalWDV,1,0,'C',true);
				$this->cell(10,5,$totalTWD,1,0,'C',true);
				$this->cell(10,5,$totalDEC,1,0,'C',true);
				$this->cell(10,5,$totalSP,1,0,'C',true);
				$this->cell(14,5,$ptotal,1,0,'C',true);

				$this->Ln();
				$this->SetFont('Times','B',12);
				$this->cell(35,8,'GRAND TOTAL',1,0,'L');
				$this->cell(25,8,'',1,0,'C');
				$this->cell(10,8,$grandTotalDN,1,0,'C');
				$this->cell(10,8,$grandTotalCR,1,0,'C');
				$this->cell(10,8,$grandTotalPASS,1,0,'C');
				$this->cell(10,8,$grandTotalRF,1,0,'C');
				$this->cell(10,8,$grandTotalFW,1,0,'C');
				$this->cell(10,8,$grandTotalRR,1,0,'C');
				$this->cell(10,8,$grandTotalRT,1,0,'C');
				$this->cell(10,8,$grandTotalDF,1,0,'C');
				$this->cell(10,8,$grandTotalRW,1,0,'C');
				$this->cell(10,8,$grandTotalTF,1,0,'C');
				$this->cell(10,8,$grandTotalINC,1,0,'C');
				$this->cell(10,8,$grandTotalEX,1,0,'C');
				$this->cell(10,8,$grandTotalWDM,1,0,'C');
				$this->cell(10,8,$grandTotalWDV,1,0,'C');
				$this->cell(10,8,$grandTotalTWD,1,0,'C');
				$this->cell(10,8,$grandTotalDEC,1,0,'C');
				$this->cell(10,8,$grandTotalSP,1,0,'C');
				
				$this->cell(14,8,$grandTotal,1,0,'C');
				$this->Ln();
				
				
}


function studentsAggregate($class,$semester,$AcYr){
        global $dbConn;
        $sqlStudents = sprintf("
			  						 	select 	sum(sex='F') as female,sum(sex='M') as male,sum(sex='F')+ sum(sex='M') as total
										from tbl_studentclass 
										join tbl_student
										on(tbl_student.id = tbl_studentclass.student_id)
										where semester_id = '%s'
										and academicYear_id = '%s'
										and class_id = '%s'
									 "
									  ,escapeString($semester)
									  ,escapeString($AcYr)
								      ,escapeString($class)
									 );
			  $rsStudents = $dbConn->query($sqlStudents);
			  $oStudents = $rsStudents->fetchRow(DB_FETCHMODE_OBJECT);
			  return array("female"=>$oStudents->female,"male"=>$oStudents->male,"total"=>$oStudents->total);
    }

function coursesOffered($courses,$className,$prevcourses=""){
	global $dbConn;
	$this->Ln();
	$this->SetFont('Times','B',12);
	$this->cell(400,5,strtoupper($className),0,0,'L');
	$this->Ln(10);
	$this->SetFont('Times','B',11);
	$this->cell(250,5,'COURSES OFFERED',0,0,'L');
	$this->Ln(6);
	
	
	$this->Ln();
	/*if(!empty($prevcourses)){
	 foreach($prevcourses as $prevcourse){
		$this->SetFont('Times','B',12);
		$this->cell(25,5,strtoupper($prevcourse['COURSE_CODE']),0,0,'L');
		$this->cell(10,5,':',0,0,'L');
		$this->cell(200,5,$prevcourse['COURSE_NAME'],0,0,'L');
		$this->Ln(8);
	 }
	}*/
	foreach($courses as $course){
		$this->SetFont('Times','B',12);
		$this->cell(25,5,strtoupper($course['COURSE_CODE']),0,0,'L');
		$this->cell(10,5,':',0,0,'L');
		$this->cell(200,5,$course['COURSE_NAME'],0,0,'L');
		$this->Ln(8);
	}
}

function subjResult($course,$student,$AcYr,$class,$semester,$modularity){
        global $dbConn;
		//Check the scheme
		$sqlCourseMark = sprintf("
											 	select 	finalGrade
												from tbl_studentgrade 
												where student_id = '%s'
												and if('%s'=0,semester = '%s','%s'=1)
												and academicYear_id = '%s'
												and course_code = '%s'
												and ExamType = '1'
											 "
											 ,escapeString($student['id'])
									  		 ,escapeString($modularity)
											 ,escapeString($semester)
											 ,escapeString($modularity)
											 ,escapeString($AcYr)
								      		 ,escapeString($course)
									  );
					
					$rsCourseMark = $dbConn->query($sqlCourseMark);
					$oCourseMark = $rsCourseMark->fetchRow(DB_FETCHMODE_OBJECT);
       
        return ($oCourseMark->finalGrade=='')?'-':$oCourseMark->finalGrade;
}

//Page header
function Header()
{
    global $className;
    global $showbanner;
    global $showheaders;
    global $class;
    global $acYr;
    global $semester;
    if($semester==1)
    {
        $TypeOfReport = "END OF SEMESTER 1 GRADES";
    }
    elseif($semester==2)
    {
        $TypeOfReport = "END OF YEAR GRADES";
    }
    
    
    if($showbanner==1){
			$this->Image('../../images/logo.jpg',20,15,17,25);
			//$this->Image('../../images/logo.jpg',140,15,17,25);
			
			$this->Ln(15);
			
			$this->SetFont('Times','',12);
			//Title
			$this->Cell(90,1,'University of Malawi',0,0,'C');
			//Line break
			$this->Ln();
			$this->SetFont('Times','B',12);
			$this->Cell(90,10,'The College of Medicine',0,0,'C');
			//Line break
			$this->Ln(15);
			$this->SetFont('Times','B',11);
			$this->Cell(240,10,$TypeOfReport,0,0,'C');
			//$this->Cell(20,10,date('d F Y'),0,0,'C');
			//Line break
		   
			
			$this->Ln(1);
			$this->SetFont('Times','B',11);
			$this->SetTextColor(0);
			$this->SetFillColor(255,255,255);
			$this->Cell(250,6,$acYr.' '.$TypeOfReport.': '.strtoupper($className),0,1,'L',1);
		 
    }
    elseif($showbanner==0)
    {
        
		global $courses;
		global $previouscourses;
		global $students; 
		global $AcYr;
		global $semester; 
	       global $classCategory; 
    
		$this->Ln();
		$this->SetFont('Times','B',10);
		$this->cell(7,32,'No',1,0,'L');
		$this->cell(34,32,'Registration. No.',1,0,'L');
		
		$this->cell(35,32,'Surname',1,0,'L');
	
	
		$this->cell(35,32,'Firstname',1,0);
	
		$this->VCell(6,32,' Sex',1,0,'D');
	   
	   
		$this->VCell(6,32,'  ',1,0,'D');

     if(!empty($previouscourses)){
      foreach($previouscourses as $prevcourse){
          $this->VCell(6,32,$prevcourse['COURSE_CODE'],1,0,'D');

      }
       $this->VCell(2,32,"",1,0,'D');
     }
    foreach($courses as $course){
        $this->VCell(6,32,$course['COURSE_CODE'],1,0,'D');

    }

		$this->VCell(6,32,'  ',1,0,'D');
    
		if($semester!=1){
			$this->VCell(6,32,'WN',1,0,'D');
		}
		
		$this->VCell(6,32,'History',1,0,'D');
		if($classCategory ==2){
			$this->VCell(6,32,'Average',1,0,'D');
		}
		$this->VCell(9,32,'Remark',1,0,'D');
    }elseif($showbanner==8){
		$this->Ln(6);
		$this->SetFont('Times','B',8);
		$this->cell(35,5,'PROGRAMME',1,0,'L');
		$this->cell(25,5,'COURSE/YEAR',1,0,'C');
		$this->cell(10,5,'DN',1,0,'C');
		$this->cell(10,5,'CR',1,0,'C');
		$this->cell(10,5,'PASS',1,0,'C');
		$this->cell(10,5,'RF',1,0,'C');
		$this->cell(10,5,'FW',1,0,'C');
		$this->cell(10,5,'RR',1,0,'C');
		$this->cell(10,5,'RT',1,0,'C');
		$this->cell(10,5,'DF',1,0,'C');
		$this->cell(10,5,'RW',1,0,'C');
		$this->cell(10,5,'TF',1,0,'C');
		$this->cell(10,5,'INC',1,0,'C');
		$this->cell(10,5,'EX',1,0,'C');
		$this->cell(10,5,'WDM',1,0,'C');
		$this->cell(10,5,'WDV',1,0,'C');
		$this->cell(10,5,'WDT',1,0,'C');
		$this->cell(10,5,'DEC',1,0,'C');
		$this->cell(10,5,'SP',1,0,'C');
	
		$this->cell(14,5,'TOTAL',1,0,'C');
	}
	
    
    //Line break
    $this->Ln();

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
	
	
	
	
	
	function VCell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false)
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

	function insertTOC( $location=1,
						$labelSize=20,
						$entrySize=10,
						$tocfont='Times',
						$label='Table of Contents'
						) {
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

	function Footer() {
		if(!$this->_numberingFooter)
			return;
		//Go to 1.5 cm from bottom
		$this->SetY(-15);
		//Select Arial italic 8
		$this->SetFont('Arial','',8);
		$this->Cell(130,7,'COM Registry - Confidential',0,0,'L');
		$this->SetFont('Arial','B',8);
		$this->Cell(120,7,$this->numPageNo(),0,0,'L'); 
		$this->SetFont('Arial','',8);
		$this->Cell(400,7,$this->date,0,0,'Y',0,0,'R'); 
		if(!$this->_numbering)
			$this->_numberingFooter=false;
	}
}
?>

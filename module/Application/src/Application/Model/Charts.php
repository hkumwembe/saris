<?php
namespace Application\Model;

require './vendor/ghunti/highcharts-php/src/Highchart.php';
require './vendor/ghunti/highcharts-php/src/HighchartOption.php';
require './vendor/ghunti/highcharts-php/src/HighchartJsExpr.php';
require './vendor/ghunti/highcharts-php/src/HighchartOptionRenderer.php';

use Ghunti\HighchartsPHP\Highchart;
use Ghunti\HighchartsPHP\HighchartJsExpr;
use Application\Model\Admission;
/**
 * Description of Charts
 *
 * @author hkumwembe
 */

class Charts {
    
    protected $em;
    
    public function __construct(\Doctrine\ORM\EntityManager $em) {
        $this->em = $em;
    } 
    
    public function getEnrolledStatus(){
        
        $chart = new Highchart();

        $chart->chart->renderTo = "container";
        $chart->chart->plotBackgroundColor = null;
        $chart->chart->plotBorderWidth = null;
        $chart->chart->plotShadow = false;
        $chart->title->text = "2014/2015 ENROLMENT STATUS";

        $chart->tooltip->formatter = new HighchartJsExpr(
            "function() {
            return '<b>'+ this.point.name +'</b>: '+ Math.round(this.percentage) +' %';}");
        
        $chart->plotOptions->pie->allowPointSelect = 1;
        $chart->plotOptions->pie->cursor = "pointer";
        $chart->plotOptions->pie->dataLabels->enabled = 1;
        $chart->plotOptions->pie->dataLabels->color = "#000000";
        $chart->plotOptions->pie->dataLabels->connectorColor = "#000000";

        $chart->plotOptions->pie->dataLabels->formatter = new HighchartJsExpr(
            "function() {
            return '<b>'+ this.point.name +'</b>: '+ Math.round(this.percentage) +' %'; }");

        $chart->series[] = array(
            'type' => "pie",
            'name' => "Browser share",
            'data' => array(
                array(
                    "Registered",
                    45
                ),
                array(
                    'name' => 'Enrolled',
                    'y' => 12.8,
                    'sliced' => true,
                    'selected' => true
                )
            )
        );
        
        return $chart;
    }
    
    
     public function getRegistrationStatus(){
        $admission = new Admission($this->em);
        //Get list of faculties
        $facultyArray = $registered = array();
        $facultyobject = $admission->getRegistrationStatus(); 
        foreach($facultyobject as $faculty){
            $facultyArray[] = $faculty['label'];
            $registered[]   = $faculty['registered']/1;
            $pending[]      = $faculty['pending']/1;
        } 
        $bar = new Highchart();

        $bar->chart->renderTo = "barcontainer";
        $bar->chart->type = "column";
        $bar->title->text = "";
        $bar->xAxis->categories = $facultyArray;
        $bar->yAxis->min = 0;
        $bar->yAxis->title->text = "Total number of student";
        $bar->yAxis->stackLabels->enabled = 1;
        $bar->yAxis->stackLabels->style->fontWeight = "bold";
        $bar->yAxis->stackLabels->style->color = new HighchartJsExpr(
            "(Highcharts.theme && Highcharts.theme.textColor) || 'gray'");
        $bar->legend->align = "right";
        $bar->legend->x = - 100;
        $bar->legend->verticalAlign = "top";
        $bar->legend->y = 20;
        $bar->legend->floating = 1;
        $bar->legend->backgroundColor = new HighchartJsExpr(
            "(Highcharts.theme && Highcharts.theme.legendBackgroundColorSolid) || 'white'");
        $bar->legend->borderColor = "#CCC";
        $bar->legend->borderWidth = 1;
        $bar->legend->shadow = false;
        $bar->tooltip->formatter = new HighchartJsExpr(
            "function() {
            return '<b>'+ this.x +'</b><br/>'+
            this.series.name +': '+ this.y +'<br/>'+
            'Total: '+ this.point.stackTotal;}");
        $bar->plotOptions->column->stacking = "normal";
        $bar->plotOptions->column->dataLabels->enabled = 1;
        $bar->plotOptions->column->dataLabels->color = new HighchartJsExpr(
            "(Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'");
        $bar->series[] = array(
            'name' => "Registered",
            'data' => $registered
        );
        $bar->series[] = array(
            'name' => "Pending",
            'data' => $pending
        );
        
  return $bar; 
} 
}

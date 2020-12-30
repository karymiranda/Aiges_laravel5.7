<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Codedge\Fpdf\Fpdf\Fpdf;

class PdfController extends Fpdf
{
  var $height = 6;

  // Para la boleta de notas encabezado
  public function headerBoletaNotas($centro)
  {
   // $routeImage = __DIR__."..\..\..\..\public\\".$centro['logo'];
     $routeImage = __DIR__."..\..\..\..\public\logoce.jpg";
    $routeImageMINED = __DIR__."..\..\..\..\public\EscudoDeElSalvador.jpg";

    $this->Image($routeImage, 10, 4, 25);
    $this->Image($routeImageMINED, 170, 6, 30);
    
    $this->SetFont('Arial','', 11);
    $this->Cell(0, $this->height - 1, utf8_decode('Ministerio de Educación, Ciencia y Tecnologia'), 0, 1, 'C');
    $this->SetFont('Arial','B',12);
    $this->Cell(0, $this->height, utf8_decode($centro->v_nombrecentro), 0, 1, 'C');
    $this->SetFont('Arial','IB', 10);
    $this->Cell(0, $this->height - 1, utf8_decode('"VEN CON NOSOTROS A CAMINAR"'), 0, 1, 'C');
    $this->SetFont('Arial','I',10);
    $this->Cell(0, $this->height-2, utf8_decode($centro->v_direccion), 0, 1, 'C');		
    $this->Cell(0, $this->height-2, utf8_decode('Teléfono '.$centro->v_telefono.' E-mail: '.$centro->correo_electronico), 0, 1, 'C');
    $this->Cell(0, $this->height, '', 'B', 1, 'C');
  }

  public function boletaTitulo($object = array())
  {
    $this->Ln(5);
    $this->SetFont('Arial','B', 10);
    $this->Cell(0, $this->height - 1, 'REGISTRO DE EVALUACION', 0, 1, 'C');
    $this->Cell(0, $this->height - 1, 'POR ASIGNATURA Y TRIMESTRE', 0, 1, 'C');
    $this->Cell(0, $this->height - 1, 'EDUCACION BASICA', 0, 1, 'C');
    $this->SetFont('Helvetica','BU', 13);
    $this->Cell(0, $this->height - 1, utf8_decode($object['seccion']['descripcion']), 0, 1, 'C');

    $this->Ln(8);
    $this->SetFont('Arial','', 10);
    $this->Cell(38, $this->height + 1, 'Nombre del Alumno/a: ', 0, 0, 'R');
    $this->Cell(110, $this->height, utf8_decode($object['alumno']->v_apellidos.', '.$object['alumno']->v_nombres), 'B');
    $this->Cell(15, $this->height, 'NIE:', 0, 0, 'R');
    $this->Cell(30, $this->height, $object['alumno']->v_nie, 'B', 1, 'C');

    $this->Cell(38, $this->height + 2, 'Profesor/a Encargado: ', 0, 0, 'R');
    $this->Cell(110, $this->height + 2, utf8_decode($object['profesor']->v_apellidos).", ".utf8_decode($object['profesor']->v_nombres), 'B', 0);

    $this->Cell(22, $this->height + 2, utf8_decode('Año:'), 0, 0, 'C');
    $this->Cell(18, $this->height + 2, utf8_decode($object['seccion']['anio']), 'B', 1, 'C');
  }

  public function tableNotesBoleta($object = array())
	{
    $evaluaciones = $object['eva'];
    $namePeriodo = $object['periodo'];
    $this->Ln(10);
    $this->SetFillColor(210);
    $this->SetFont('Arial','B', 9);
    $this->Cell(70, 18, "ASIGNATURAS", 1, 0, 'C', 1);
    $this->Cell(120, $this->height, strtoupper("${namePeriodo}"), 1, 1, 'C', 1);
    $this->Cell(70);
    $this->Cell(95, $this->height, "ACTIVIDADES", 1, 1, 'C', 1);
    $this->Cell(70);
    $this->Cell(31.67, $this->height, $evaluaciones[0]->nombre." (".$evaluaciones[0]->d_porcentajeActividad."%)", 1, 0, 'C', 1);
    $this->Cell(31.67, $this->height, $evaluaciones[1]->nombre." (".$evaluaciones[1]->d_porcentajeActividad."%)", 1, 0, 'C', 1);
    $this->Cell(31.67, $this->height, $evaluaciones[2]->nombre." (".$evaluaciones[2]->d_porcentajeActividad."%)", 1, 0, 'C', 1);
    $this->SetXY( $this->getX() , $this->getY() - 6);
    $this->Cell(25, 12, "PROMEDIO", 1, 1, 'C', 1);
    $this->SetFont('Arial','', 10);

//dd($object['notas']);
    foreach ($object['notas'] as $key => $value) {
      $promedio = self::promedio($value);
      $this->Cell(70, $this->height + 1, utf8_decode($key) , 1, 0, 'L');
      $this->Cell(31.67, $this->height + 1, number_format(@$value['ACT1'], 1), 1, 0, 'C');
      $this->Cell(31.67, $this->height + 1, number_format(@$value['ACT2'], 1), 1, 0, 'C');
      $this->Cell(31.67, $this->height + 1, number_format(@$value['ACT3'], 1), 1, 0, 'C');
      
      if($promedio <= 5.9)
        $this->SetTextColor(255, 0, 0);
                
      $this->SetFont('Arial','B', 10);
      $this->Cell(25, $this->height + 1, number_format($promedio, 1) , 1, 1, 'C');
      self::getSetClearFont();
    }
    
    if(count($object['notas']) == 0){
      $this->Cell(190, $this->height + 10, 'NO HAY NOTAS EN ESTE PERIODO PARA ESTE ALUMNO' , 1, 1, 'C');
    }
  }

  public function footerNotesBoletas($object = array())
  {
    $this->Ln(10);
    $this->SetFont('Arial','', 10);
    $this->Cell(95, $this->height, "F._____________________________________", 0, 0, 'C');
    $this->Cell(95, $this->height, "F._____________________________________", 0, 1, 'C');
    
    $this->Cell(95, $this->height, "  ".utf8_decode($object['centro']->nombre_director_ar), 0, 0, 'C');
    $this->Cell(100, $this->height, (utf8_decode($object['profesor']->v_nombres)).", ". (utf8_decode($object['profesor']->v_apellidos)), 0, 1, 'C');
    $this->Cell(95, $this->height, "  Director del Centro Escolar", 0, 0, 'C');
    $this->Cell(100, $this->height, "Profesor/a encargado/a", 0, 0, 'C');
    $this->Ln(5);
  }


  // Funciones privadas
  private function promedio($notas)
  {
    return (
      (isset($notas['ACT1']) ? floatval($notas['ACT1']) : 0) +
      (isset($notas['ACT2']) ? floatval($notas['ACT2']) : 0) +
      (isset($notas['ACT3']) ? floatval($notas['ACT3']) : 0) 
    );
  } 

  private function getSetClearFont()
  {
    $this->SetFont('Arial','', 10);
    $this->SetTextColor(0, 0, 0);
  }

  public function FooterConstancia(){
    $this->SetFont('Arial','B',7);
    $this->Ln(5);
    $this->Cell(0,10,utf8_decode('Este documento tendrá validéz con el sello de la dirección y será entregado al padre de familia'), 0,0,'C');
  }
}

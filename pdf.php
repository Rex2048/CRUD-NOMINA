<?php

require('fpdf19/fpdf.php');
if (!isset($_GET['id'])) {
    die('Falta el parametro id');
    }
$idBuscado = $_GET['id']; 

if(!file_exists('usuarios.json')) {die('No se encuentra el json usuarios');}

$usuarios = json_decode(file_get_contents('usuarios.json'), true) ?? [];

$usr= null;
foreach($usuarios as $u){
    if((string)($u['id'] ?? '') === (string)$idBuscado){
    $usr = $u;
    break; 
   }
}


function money($valor){
return '$ ' . number_format($valor ?? 0, 0, ',', '.');
}

function campo($usr, $clave, $default = 0){
return  $usr[$clave] ?? $default; 
}

class NominaPDF extends FPDF{
    function headerCell($x, $y, $w, $h, $texto, $fill = true, $align = 'L') {
        $this->SetXY($x, $y);
        if ($fill) {
            $this->SetFillColor(155, 187, 210); // Azul-gris como la imagen
            $this->SetTextColor(0, 0, 0);
            $this->SetFont('Arial', 'B', 9);
        } else {
            $this->SetFont('Arial', '', 9);
        }
        $this->Cell($w, $h, $texto, 1, 0, $align, $fill);
    }
}

$extraturno               = campo($usr, 'extraturno', 0);          // horas extra en $
$anticipos                = campo($usr, 'anticipos', 0);
$anticipoVacacionesPagadas = campo($usr, 'anticipo_vacaciones_pagadas', 0);
$periodoNomina            = campo($usr, 'periodo_nomina', '');      // ej: "01 al 30 de Abril 2026"
 
$nombre          = campo($usr, 'nombre', '');
$identificacion  = campo($usr, 'identificacion', '');
$centroCosto     = campo($usr, 'departamento', '');
$diasLaborados   = campo($usr, 'dias_trabajados', 0);
$sueldoMensual   = campo($usr, 'sueldo', 0);
$auxNoPrestacional = campo($usr, 'auxilio_alimentacion', 0);
$auxTransporte   = campo($usr, 'auxilioTransporte', 0);
 
$salarioValor        = campo($usr, 'salarioDiario', 0) * $diasLaborados;
$vacacionesDisfrutadas = campo($usr, 'vacaciones', 0);
$auxIncapacidad      = campo($usr, 'pago_incapacidad', 0);
 
$totalDevengado = campo($usr, 'total_devengado', 0);
 
$salud    = campo($usr, 'salud', 0);
$pension  = campo($usr, 'pension', 0);
$fondoSolidaridad = campo($usr, 'fondo_solidaridad_pensional', 0);
$prestamos = campo($usr, 'valor_cuota', 0); // cuota del prestamo que se descuenta este periodo
 
$totalDeducciones = campo($usr, 'total_deducciones', 0);
$netoAPagar       = campo($usr, 'total_nomina_a_pagar', 0);
 
$prestamoValorInicial = campo($usr, 'monto_del_desembolso', 0);
$prestamoCuotaPagada  = campo($usr, 'numero_de_cuota_pagada', 0);
$prestamoValorCuota   = campo($usr, 'valor_cuota', 0);
$prestamoCuotasPorDescontar = campo($usr, 'cuotas_por_descontar', 0);
$prestamoSaldo = campo($usr, 'saldo_del_prestamo', 0);
 
// ---------------------------------------------------------
// 5. CONSTRUCCION DEL PDF
// ---------------------------------------------------------
$pdf = new NominaPDF('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(true, 10);
 
$anchoPagina = 190; // ancho util (210 - 2*10 margen)
$x0 = 10;
$y = 10;
 
// --- Fila encabezado empresa ---
// Si tienes el logo, ponlo aqui (ajusta ruta y medidas):
if (file_exists('logo.png')) {
    $pdf->Image('logo.png', $x0 + 140, $y + 2, 40);
}
 
$pdf->SetXY($x0, $y);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell($anchoPagina - 45, 8, 'HERMES INFINITY PROJECTS SAS', 1, 2, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell($anchoPagina - 45, 8, 'NIT.950.468.970 - 5', 1, 2, 'C');
$pdf->Cell(45, 16, '', 1, 1, 'C'); // celda vacia donde va el logo (borde para que cuadre la tabla)
// Nota: como Cell() con salto (2) no maneja bien celdas de distinto ancho en la misma fila,
// si el logo no calza visualmente, ajusta manualmente con SetXY como se hizo con Image().
 
$y += 16;
 
// --- Nombre del trabajador ---
$pdf->SetXY($x0, $y);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(45, 8, 'Nombre del trabajador:', 1);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell($anchoPagina - 45, 8, $nombre, 1);
$y += 8;
 
// --- Identificacion / Periodo ---
$pdf->SetXY($x0, $y);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(45, 8, 'Identificacion:', 1);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(60, 8, $identificacion, 1);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(40, 8, 'Nomina:', 1);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell($anchoPagina - 145, 8, $periodoNomina, 1);
$y += 8;
 
// --- Centro de costo / Dias laborados ---
$pdf->SetXY($x0, $y);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(45, 8, 'Centro de Costo:', 1);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(60, 8, $centroCosto, 1);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(40, 8, 'Dias laborados:', 1);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell($anchoPagina - 145, 8, $diasLaborados, 1);
$y += 8;
 
// --- Salario mensual / Auxilio no prestacional ---
$pdf->SetXY($x0, $y);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(45, 8, 'Salario mensual:', 1);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(50, 8, money($sueldoMensual), 1, 0, 'R');
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(45, 8, 'Aux. no prestacional:', 1);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell($anchoPagina - 140, 8, money($auxNoPrestacional), 1, 0, 'R');
$y += 8;
 
// --- Auxilio de transporte ---
$pdf->SetXY($x0, $y);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(45, 8, 'Auxilio de transporte:', 1);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell($anchoPagina - 45, 8, money($auxTransporte), 1, 0, 'R');
$y += 10;
 
// ---------------------------------------------------------
// 6. TABLAS DEVENGADOS / DEDUCIDOS (lado a lado)
// ---------------------------------------------------------
$colIzqX = $x0;          // DEVENGADOS empieza en x0
$colDerX = $x0 + 95;     // DEDUCIDOS empieza a la mitad (95mm)
$anchoCol = 95;
 
// Titulos
$pdf->SetXY($colIzqX, $y);
$pdf->SetFillColor(155, 187, 210);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell($anchoCol, 7, 'DEVENGADOS', 1, 0, 'C', true);
$pdf->SetXY($colDerX, $y);
$pdf->Cell($anchoCol, 7, 'DEDUCIDOS', 1, 0, 'C', true);
$y += 7;
 
// Encabezados de columnas Concepto/Valor
$pdf->SetXY($colIzqX, $y);
$pdf->Cell(50, 6, 'Concepto', 1, 0, 'C', true);
$pdf->Cell(45, 6, 'Valor', 1, 0, 'C', true);
$pdf->SetXY($colDerX, $y);
$pdf->Cell(50, 6, 'Concepto', 1, 0, 'C', true);
$pdf->Cell(45, 6, 'Valor', 1, 0, 'C', true);
$y += 6;
 
// Filas de DEVENGADOS
$devengados = [
    ['Salario', $salarioValor],
    ['Auxilio No Prestacional', $auxNoPrestacional],
    ['Auxilio de transporte', $auxTransporte],
    ['Auxilio monetario por Incapacidad', $auxIncapacidad],
    ['Vacaciones disfrutadas', $vacacionesDisfrutadas],
];
 
// Filas de DEDUCIDOS
$deducidos = [
    ['Salud', $salud],
    ['Pension', $pension],
    ['Fondo de solidaridad Pensional', $fondoSolidaridad],
    ['Anticipos', $anticipos],
    ['Prestamos', $prestamos],
    ['Anticipo Vacaciones Pagadas', $anticipoVacacionesPagadas],
];
 
$pdf->SetFont('Arial', '', 8);
$filas = max(count($devengados), count($deducidos));
for ($i = 0; $i < $filas; $i++) {
    $pdf->SetXY($colIzqX, $y);
    if (isset($devengados[$i])) {
        $pdf->Cell(50, 6, $devengados[$i][0], 1);
        $pdf->Cell(45, 6, money($devengados[$i][1]), 1, 0, 'R');
    } else {
        $pdf->Cell(95, 6, '', 1);
    }
    $pdf->SetXY($colDerX, $y);
    if (isset($deducidos[$i])) {
        $pdf->Cell(50, 6, $deducidos[$i][0], 1);
        $pdf->Cell(45, 6, money($deducidos[$i][1]), 1, 0, 'R');
    } else {
        $pdf->Cell(95, 6, '', 1);
    }
    $y += 6;
}
 
// Totales
$pdf->SetXY($colIzqX, $y);
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(155, 187, 210);
$pdf->Cell(50, 7, 'TOTAL DEVENGADOS', 1, 0, 'L', true);
$pdf->Cell(45, 7, money($totalDevengado), 1, 0, 'R', true);
$pdf->SetXY($colDerX, $y);
$pdf->Cell(50, 7, 'TOTAL DEDUCIDOS', 1, 0, 'L', true);
$pdf->Cell(45, 7, money($totalDeducciones), 1, 0, 'R', true);
$y += 7;
 
// Neto a pagar (fila completa del lado derecho, como en la imagen)
$pdf->SetXY($colDerX, $y);
$pdf->SetFillColor(180, 205, 220);
$pdf->Cell(50, 7, 'NETO A PAGAR', 1, 0, 'L', true);
$pdf->Cell(45, 7, money($netoAPagar), 1, 0, 'R', true);
$y += 14;
 

$pdf->SetXY($colIzqX, $y);
$pdf->SetFont('Arial', '', 9);
$pdf->MultiCell(90, 6, "Recibi de conformidad y acepto en todas partes este pago:", 0);
 
// Tabla info prestamo
$py = $y;
$pdf->SetXY($colDerX, $py);
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetFillColor(155, 187, 210);
$pdf->Cell(60, 6, 'Informacion prestamo', 1, 0, 'L', true);
$pdf->Cell(35, 6, 'Valor', 1, 0, 'C', true);
$py += 6;
 
$pdf->SetFont('Arial', '', 8);
$pdf->SetXY($colDerX, $py);
$pdf->Cell(60, 6, 'Valor inicial', 1);
$pdf->Cell(35, 6, money($prestamoValorInicial), 1, 0, 'R');
$py += 6;
 
$pdf->SetXY($colDerX, $py);
$pdf->Cell(60, 6, 'No. Cuota pagada / Vr. cuota', 1);
$pdf->Cell(35, 6, $prestamoCuotaPagada . ' / ' . money($prestamoValorCuota), 1, 0, 'R');
$py += 6;
 
$pdf->SetXY($colDerX, $py);
$pdf->Cell(60, 6, 'Cuotas por descontar', 1);
$pdf->Cell(35, 6, money($prestamoCuotasPorDescontar), 1, 0, 'R');
$py += 6;
 
$pdf->SetXY($colDerX, $py);
$pdf->Cell(60, 6, 'Saldo del prestamo', 1);
$pdf->Cell(35, 6, money($prestamoSaldo), 1, 0, 'R');
$py += 10;
 
// --- Firma ---
$firmaY = max($y + 30, $py + 5);
$pdf->SetXY($colIzqX, $firmaY);
$pdf->Cell(90, 0, '', 'T'); // linea de firma
$pdf->SetXY($colIzqX, $firmaY + 2);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(90, 5, $nombre, 0, 2);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(90, 5, 'C.C. No. ' . $identificacion, 0, 2);
 

$nombreArchivo = 'nomina_' . preg_replace('/[^A-Za-z0-9_]/', '_', $nombre) . '.pdf';
$pdf->Output('D', $nombreArchivo);


?>
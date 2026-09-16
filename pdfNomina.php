<?php

// direccion
require_once file_exists('vendor/autoload.php') ? 'vendor/autoload.php' : 'dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$archivo_json = 'usuarios.json';
$usuarios = [];

if (file_exists($archivo_json)) {
    $contenido = file_get_contents($archivo_json);
    $usuarios = json_decode($contenido, true) ?? [];
}

$options = new Options();
$options->set('isHtml5ParserEnabled', true);

$dompdf = new Dompdf($options);

// css
$html = '
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; font-size: 7px; margin: 5px; }
        h2 { text-align: center; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 3px 1px; text-align: center; }
        th { background-color: #e0e0e0; }
        .text-start { text-align: left; }
        .fw-bold { font-weight: bold; }
    </style>
</head>
<body>
    <h2>Reporte General de Nómina</h2>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Dept.</th>
                <th>Cargo</th>
                <th>ID</th>
                <th>Sueldo</th>
                <th>Días</th>
                <th>Sal. Día</th>
                <th>Vac.</th>
                <th>Aux. Transp.</th>
                <th>Incap.</th>
                <th>Recargo</th>
                <th>Dominical</th>
                <th>Aux. Aliment.</th>
                <th>Total Dev.</th>
                <th>Salud</th>
                <th>Pensión</th>
                <th>F. Solid.</th>
                <th>Monto Desemb.</th>
                <th>Cuotas Descont.</th>
                <th>Fecha Desemb.</th>
                <th>Cuota Pag.</th>
                <th>Cuotas Pend.</th>
                <th>Fin Préstamo</th>
                <th>Valor Cuota</th>
                <th>Saldo Préstamo</th>
                <th>Total Deduc.</th>
                <th>Total Pagar</th>
                <th>Prima</th>
                <th>Cesantías</th>
                <th>Int. Cesantías</th>
                <th>Vac. Prest.</th>
                <th>Total Prest.</th>
                <th>Coste Día</th>
                <th>Coste Mes</th>
                <th>Coste Año</th>
            </tr>
        </thead>
        <tbody>';

if (empty($usuarios)) {
    $html .= '<tr><td colspan="35">No hay registros almacenados en el sistema.</td></tr>';
} else {
    foreach ($usuarios as $usr) {
        $fecha = !empty($usr['fecha_del_desemboloso']) ? date('d/m/Y', strtotime($usr['fecha_del_desemboloso'])) : 'N/A';
        $html .= '<tr>
            <td class="text-start">' . htmlspecialchars($usr['nombre'] ?? '') . '</td>
            <td>' . htmlspecialchars($usr['departamento'] ?? '') . '</td>
            <td>' . htmlspecialchars($usr['cargo'] ?? '') . '</td>
            <td>' . htmlspecialchars($usr['identificacion'] ?? '') . '</td>
            <td>$' . number_format($usr['sueldo'] ?? 0, 0, ',', '.') . '</td>
            <td>' . number_format($usr['dias_trabajados'] ?? 0, 0, ',', '.') . '</td>
            <td>$' . number_format($usr['salarioDiario'] ?? 0, 0, ',', '.') . '</td>
            <td>$' . number_format($usr['vacaciones'] ?? 0, 0, ',', '.') . '</td>
            <td>$' . number_format($usr['auxilioTransporte'] ?? 0, 0, ',', '.') . '</td>
            <td>$' . number_format($usr['pago_incapacidad'] ?? 0, 0, ',', '.') . '</td>
            <td>$' . number_format($usr['recargo_total'] ?? 0, 0, ',', '.') . '</td>
            <td>$' . number_format($usr['total_dominical'] ?? 0, 0, ',', '.') . '</td>
            <td>$' . number_format($usr['auxilio_alimentacion'] ?? 0, 0, ',', '.') . '</td>
            <td class="fw-bold">$' . number_format($usr['total_devengado'] ?? 0, 0, ',', '.') . '</td>
            <td>$' . number_format($usr['salud'] ?? 0, 0, ',', '.') . '</td>
            <td>$' . number_format($usr['pension'] ?? 0, 0, ',', '.') . '</td>
            <td>$' . number_format($usr['fondo_solidaridad_pensional'] ?? 0, 0, ',', '.') . '</td>
            <td>$' . number_format($usr['monto_del_desembolso'] ?? 0, 0, ',', '.') . '</td>
            <td>' . number_format($usr['numero_cuotas_a_descontar'] ?? 0, 0, ',', '.') . '</td>
            <td>' . $fecha . '</td>
            <td>' . number_format($usr['numero_de_cuota_pagada'] ?? 0, 0, ',', '.') . '</td>
            <td>' . number_format($usr['cuotas_por_descontar'] ?? 0, 0, ',', '.') . '</td>
            <td>' . htmlspecialchars($usr['nomina_en_que_termina_prestamo'] ?? '') . '</td>
            <td>$' . number_format($usr['valor_cuota'] ?? 0, 0, ',', '.') . '</td>
            <td>$' . number_format($usr['saldo_del_prestamo'] ?? 0, 0, ',', '.') . '</td>
            <td>$' . number_format($usr['total_deducciones'] ?? 0, 0, ',', '.') . '</td>
            <td class="fw-bold">$' . number_format($usr['total_nomina_a_pagar'] ?? 0, 0, ',', '.') . '</td>
            <td>$' . number_format($usr['prestaciones_prima'] ?? 0, 0, ',', '.') . '</td>
            <td>$' . number_format($usr['prestaciones_cesantias'] ?? 0, 0, ',', '.') . '</td>
            <td>$' . number_format($usr['prestaciones_interes_cesantias'] ?? 0, 0, ',', '.') . '</td>
            <td>$' . number_format($usr['prestaciones_vacaciones'] ?? 0, 0, ',', '.') . '</td>
            <td class="fw-bold">$' . number_format($usr['total_prestaciones'] ?? 0, 0, ',', '.') . '</td>
            <td>$' . number_format($usr['coste_empresa_diario'] ?? 0, 0, ',', '.') . '</td>
            <td>$' . number_format($usr['coste_empresa_mensual'] ?? 0, 0, ',', '.') . '</td>
            <td>$' . number_format($usr['coste_empresa_anual'] ?? 0, 0, ',', '.') . '</td>
        </tr>';
    }
}

$html .= '</tbody></table></body></html>';

$dompdf->loadHtml($html);
$dompdf->setPaper('A2', 'landscape');
$dompdf->render();
$dompdf->stream("reporte_nomina_general.pdf", ["Attachment" => true]);
exit();
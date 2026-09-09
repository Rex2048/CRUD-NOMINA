<?php
// Leer el archivo JSON si existe
$usuarios = [];
if (file_exists('usuarios.json')) {
    $contenido = file_get_contents('usuarios.json');
    $usuarios = json_decode($contenido, true) ?? [];
}
?>
<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD de Nomina - Almacenamiento JSON</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body class="bg-dark text-light py-4">

<div class="container">
<!-- TABLA DE REGISTROS -->
    <h3 class="mb-3 text-info">Registros guardados</h3>
    <div class="table-responsive">
        <table class="table table-dark table-striped table-hover table-bordered align-middle text-center">
            <thead class="table-active">
                <tr>
                    <th>Nombre</th>
                    <th>Departamento</th>
                    <th>Cargo</th>
                    <th>No. identificacion</th>
                    <th>Sueldo</th>
                    <th>Dias laborados</th>
                    <th>Salario Dia trabajado</th>
                    <th>Vacaciones</th>
                    <th>Auxilio de transporte</th>
                    <th>Pago incapacidad</th>
                    <th>Total recargo</th>
                    <th>Total dominical</th>
                    <th>Auxilio alimentacion no prestacional</th>
                    <th>Total devengado</th>
                    <th>Salud</th>
                    <th>Pension</th>
                    <th>Fondo solidaridad</th>
                    <th>Monto desembolso</th>
                    <th>No. cuoras a descontar</th>
                    <th>Fecha desembolso</th>
                    <th>No. de cuota pagada</th>
                    <th>Cuotas por descontar</th>
                    <th>Nomina que termina prestamo</th>
                    <th>Valor cuota</th>
                    <th>Saldo de Prestamo</th>
                    <th>Total deducciones</th>
                    <th>Total nomina a pagar</th>
                    <th>Prima</th>
                    <th>Cesantias</th>
                    <th>Intereses cesantias</th>
                    <th>Vacaciones</th>
                    <th>Total</th>
                    <th>Coste empresa diario</th>
                    <th>Coste empresa mensual</th>
                    <th>Coste empresa anual</th>

                </tr>
            </thead>
            <tbody>
                <?php if (empty($usuarios)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">No hay registros almacenados en el sistema.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($usuarios as $usr): ?>
                        <tr>
                            <td class="text-start fw-semibold"><?php echo htmlspecialchars($usr['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($usr['departamento']); ?></td>
                            <td><?php echo htmlspecialchars($usr['cargo']); ?></td>
                            <td><?php echo htmlspecialchars($usr['identificacion']); ?></td>
                            <td class="text-success">$<?php echo number_format($usr['sueldo'] ?? 0, 0, ',', '.'); ?></td>
                            <td><?php echo number_format($usr['dias_trabajados'] ?? 0, 0, ',', '.'); ?></td>
                            <td>$<?php echo number_format($usr['vacaciones'] ?? 0, 0, ',', '.'); ?></td>
                            <td>$<?php echo number_format($usr['auxilioTransporte'] ?? 0, 0, ',', '.'); ?></td>
                            <td>$<?php echo number_format($usr['pago_incapacidad'] ?? 0, 0, ',', '.'); ?></td>
                            <td>$<?php echo number_format($usr['total_recargo'] ?? 0, 0, ',', '.'); ?></td>
                            <td>$<?php echo number_format($usr['total_dominical'] ?? 0, 0, ',', '.'); ?></td>
                            <td>$<?php echo number_format($usr['auxilio_alimentacion'] ?? 0, 0, ',', '.'); ?></td>
                            <td>$<?php echo number_format($usr['total_devengado'] ?? 0, 0, ',', '.'); ?></td>
                            <td>$<?php echo number_format($usr['total_dominical'] ?? 0, 0, ',', '.'); ?></td>
                            <td>$<?php echo number_format($usr['salud'] ?? 0, 0, ',', '.'); ?></td>
                            <td>$<?php echo number_format($usr['pension'] ?? 0, 0, ',', '.'); ?></td>
                            <td>$<?php echo number_format($usr['fondo_solidaridad_pensional'] ?? 0, 0, ',', '.'); ?></td>
                            <td>$<?php echo number_format($usr['total_dominical'] ?? 0, 0, ',', '.'); ?></td>
                            <td>$<?php echo number_format($usr['monto_del_desembolso'] ?? 0, 0, ',', '.'); ?></td>
                            <td><?php echo isset($usr['fecha_del_desemboloso']) ? date('d/m/Y', strtotime($usr['fecha_del_desemboloso'])) : 'Sin fecha'; ?></td>
                            <td><?php echo number_format($usr['numero_de_cuota_pagada'] ?? 0, 0, ',', '.'); ?></td>
                            <td><?php echo number_format($usr['Cuotas_por_descontar'] ?? 0, 0, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($usr['nomina_en_que_termina_prestamo']); ?></td>
                            <td>$<?php echo number_format($usr['valor_cuota'] ?? 0, 0, ',', '.'); ?></td>
                            <td>$<?php echo number_format($usr['saldo_del_prestamo'] ?? 0, 0, ',', '.'); ?></td>
                            <td>$<?php echo number_format($usr['total_deducciones'] ?? 0, 0, ',', '.'); ?></td>
                            <td>$<?php echo number_format($usr['total_nomina_a_pagar'] ?? 0, 0, ',', '.'); ?></td>
                            <td>$<?php echo number_format($usr['prestaciones_primas'] ?? 0, 0, ',', '.'); ?></td>
                            <td>$<?php echo number_format($usr['prestaciones_cesantias'] ?? 0, 0, ',', '.'); ?></td>
                            <td>$<?php echo number_format($usr['prestaciones_intereses_cesantias'] ?? 0, 0, ',', '.'); ?></td>
                            <td>$<?php echo number_format($usr['prestaciones_vacaciones'] ?? 0, 0, ',', '.'); ?></td>
                            <td>$<?php echo number_format($usr['prestaciones_total'] ?? 0, 0, ',', '.'); ?></td>
                            <td>$<?php echo number_format($usr['coste_empresa_diario'] ?? 0, 0, ',', '.'); ?></td>
                            <td>$<?php echo number_format($usr['coste_empresa_mensual'] ?? 0, 0, ',', '.'); ?></td>
                            <td>$<?php echo number_format($usr['coste_empresa_anual'] ?? 0, 0, ',', '.'); ?></td>
                        </tr>

                        <!-- 

                            <td class="text-success">$<?php echo number_format($usr['total_devengado'] ?? 0, 0, ',', '.'); ?></td>
                            <td class="text-danger">-$<?php echo number_format($usr['total_deducciones'] ?? 0, 0, ',', '.'); ?></td>
                            <td class="fw-bold text-warning">$<?php echo number_format($usr['total_nomina_a_pagar'] ?? 0, 0, ',', '.'); ?></td>
                            <td>
                                <a class="btn btn-warning btn-sm fw-semibold me-1" href="procesar.php?accion=generar_pdf&id=<?php echo $usr['id']; ?>">PDF</a>
                                <a class="btn btn-danger btn-sm fw-semibold" href="procesar.php?accion=eliminar&id=<?php echo $usr['id']; ?>" onclick="return confirm('¿Deseas eliminar este registro?');">Borrar</a>
                            </td>
                        </tr>
                        -->
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
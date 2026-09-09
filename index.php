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
    <h2 class="mb-4 text-center text-primary">Sistema de Gestion de Nomina</h2>

    <form action="procesar.php" method="POST">
        <!-- seccion 1 -->
        <div class="card border-secondary mb-4 bg-dark-subtle">
            <div class="card-header bg-secondary text-white fw-bold">
                1. Informacion general del empleado
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Nombre Completo:</label>
                        <input type="text" name="nombre" class="form-control" required placeholder="Ej. Nelson Andres Herrera">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Centro de costo / depto:</label>
                        <input type="text" name="departamento" class="form-control" required placeholder="Ej. Administracion">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Cargo:</label>
                        <input type="text" name="cargo" class="form-control" required placeholder="Ej. Gerente">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">No. identificacion:</label>
                        <input type="number" name="identificacion" class="form-control" required placeholder="Ej. 1020745165">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Sueldo base ($):</label>
                        <input type="number" name="sueldo" class="form-control" required placeholder="4240000">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Dias laborados:</label>
                        <input type="number" name="dias_trabajados" class="form-control" required min="0" max="30" value="30">
                    </div>
                </div>
            </div>
        </div>

        <!-- seccion 2 devengados y horas extra -->
        <div class="card border-secondary mb-4 bg-dark-subtle">
            <div class="card-header bg-secondary text-white fw-bold">
                2. Devengados y horas extra
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Dias incapacidad (EPS/ARL):</label>
                        <input type="number" name="dias_incapacidad" class="form-control" min="0" placeholder="0">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Horas recargo nocturno:</label>
                        <input type="number" name="horas_nocturnas_trabajadas" class="form-control" min="0" placeholder="0">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Horas dominicales / festivas:</label>
                        <input type="number" name="dias_dominicales" class="form-control" min="0"  placeholder="0">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Aux. alimentacion (No Prestacional):</label>
                        <input type="number" name="auxilio_alimentacion" class="form-control" min="0" placeholder="0">
                    </div>
                </div>
            </div>
        </div>

        <!-- seccion 3 reducciones por prestamo -->
        <div class="card border-secondary mb-4 bg-dark-subtle">
            <div class="card-header bg-secondary text-white fw-bold">
                3. Deducciones por prestamos
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Monto del desembolso ($):</label>
                        <input type="number" name="monto_del_desembolso" class="form-control" min="0" placeholder="0">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">No. cuotas totales:</label>
                        <input type="number" name="numero_cuotas_descontar" class="form-control" min="0" placeholder="0">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Fecha del desembolso:</label>
                        <input type="date" name="fecha_del_desemboloso" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">No. cuota pagada actual:</label>
                        <input type="number" name="numero_de_cuota_pagada" class="form-control" min="0" placeholder="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Nomina finaliza prestamo:</label>
                        <input type="text" name="nomina_en_que_termina_prestamo" class="form-control" placeholder="Ej. Abril 2026">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Valor cuota mensual ($):</label>
                        <input type="number" name="valor_cuota" class="form-control" min="0" placeholder="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Saldo del prestamo ($):</label>
                        <input type="number" name="saldo_del_prestamo" class="form-control" min="0" placeholder="0">
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success btn-lg w-100 fw-bold mb-5 shadow">
            guardar registro nomina
        </button>
        <a href="nomina.php" class="btn btn-success btn-lg w-100 fw-bold mb-5 shadow" role="button">
            ver nominas generales
        </a>

    </form>

    <!-- TABLA DE REGISTROS -->
    <h3 class="mb-3 text-info">Registros guardados</h3>
    <div class="table-responsive">
        <table class="table table-dark table-striped table-hover table-bordered align-middle text-center">
            <thead class="table-active">
                <tr>
                    <th>Nombre</th>
                    <th>Identificacion</th>
                    <th>Cargo</th>
                    <th>Total devengado</th>
                    <th>Total deducciones</th>
                    <th>Neto a pagar</th>
                    <th>Acciones</th>
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
                            <td><?php echo htmlspecialchars($usr['identificacion']); ?></td>
                            <td><?php echo htmlspecialchars($usr['cargo']); ?></td>
                            <td class="text-success">$<?php echo number_format($usr['total_devengado'] ?? 0, 0, ',', '.'); ?></td>
                            <td class="text-danger">-$<?php echo number_format($usr['total_deducciones'] ?? 0, 0, ',', '.'); ?></td>
                            <td class="fw-bold text-warning">$<?php echo number_format($usr['total_nomina_a_pagar'] ?? 0, 0, ',', '.'); ?></td>
                            <td>
                                <a class="btn btn-warning btn-sm fw-semibold me-1" href="procesar.php?accion=generar_pdf&id=<?php echo $usr['id']; ?>">PDF</a>
                                <a class="btn btn-danger btn-sm fw-semibold" href="procesar.php?accion=eliminar&id=<?php echo $usr['id']; ?>" onclick="return confirm('¿Deseas eliminar este registro?');">Borrar</a>
                            </td>
                            
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
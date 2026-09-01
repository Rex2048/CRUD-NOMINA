<?php
// 1. Leer el archivo JSON si existe, si no, inicializar un array vacío
$usuarios = [];
if (file_exists('usuarios.json')) {
    $contenido = file_get_contents('usuarios.json');
    $usuarios = json_decode($contenido, true) ?? [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CRUD de Usuarios sin Base de Datos</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        form { margin-bottom: 30px; padding: 15px; border: 1px solid #ccc; width: 300px; }
        input { display: block; margin-bottom: 10px; width: 95%; padding: 5px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .btn-eliminar { color: red; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

    <h2>Registrar Usuario</h2>
    <!-- Formulario para enviar datos al backend -->
    <form action="procesar.php" method="POST">
        <label>Nombre:</label>
        <input type="text" name="nombre" required placeholder="Ej. Juan Pérez">

        <label>Departamento:</label>
        <input type="text" name="departamento" required placeholder="Ej. Contabilidad">

        <label>Cargo:</label>
        <input type="text" name="cargo" required placeholder="Ej. Administrador">

        <label>No. Identificación:</label>
        <input type="text" name="identificacion" required placeholder="Ej. 1048854156">

        <label>Sueldo (sin puntos y sin espacios):</label>
        <input type="number" name="sueldo" required placeholder="1750000">
        
        <label>Días trabajados:</label>
        <input type="number" name="dias_trabajados" required min="0" placeholder="Ej. 20">

        <button type="submit">Guardar Usuario</button>
    </form>

    <h2>Usuarios Registrados</h2>
    <!-- Tabla de administración de usuarios -->
    <table>
        <thead>
            <tr>
                <th>Nombre de Usuario</th>
                <th>Identificador</th>
                <th>Generar PDF</th>
                <th>Borrar Usuario</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($usuarios)): ?>
                <tr>
                    <td colspan="5">No hay usuarios registrados aún.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($usuarios as $usr): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($usr['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($usr['identificador']); ?></td>
                        <td>
                            <a class="btn-eliminar" href="procesar.php?accion=generar_pdf&id=<?php echo $usr['id']; ?>" onclick="return confirm('¿Seguro que deseas generar un PDF de este usuario?');">Generar PDF</a>
                        </td>
                        <td>
                            <a class="btn-eliminar" href="procesar.php?accion=eliminar&id=<?php echo $usr['id']; ?>" onclick="return confirm('¿Seguro que deseas eliminar este usuario?');">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>

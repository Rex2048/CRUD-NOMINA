<?php
// Archivo para almacenar datos de usuarios
$archivo_json = 'usuarios.json';

// CASO 1: ELIMINAR REGISTRO O ACCIONES GET
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    
    if (file_exists($archivo_json)) {
        $contenido = file_get_contents($archivo_json);
        $usuarios = json_decode($contenido, true) ?? [];
        
        if ($_GET['accion'] === 'eliminar') {
            // Filtrar y remover el elemento por ID
            $usuarios = array_filter($usuarios, function($usr) use ($id) {
                return $usr['id'] !== $id;
            });
            // Reindexar el array
            $usuarios = array_values($usuarios);
            file_put_contents($archivo_json, json_encode($usuarios, JSON_PRETTY_PRINT));
        } elseif ($_GET['accion'] === 'generar_pdf') {
            
        }
    }
    
    header('Location: index.php');
    exit();
}

// CASO 2: REGISTRAR UN NUEVO USUARIO (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Capturar y limpiar los datos enviados por el formulario frontend
    $nombre = trim($_POST['nombre'] ?? '');
    $identificacion = (int)($_POST['identificacion'] ?? 0);
    $departamento = trim($_POST['departamento'] ?? '');
    $cargo = trim($_POST['cargo'] ?? '');

    $dias_trabajados = (int)($_POST['dias_trabajados'] ?? 0);
    $sueldo = (int)($_POST['sueldo'] ?? 0);

    // DEVENGADO
    $dias_incapacidad = (int)($_POST['dias_incapacidad'] ?? 0);
    $horas_nocturnas_trabajadas = (int)($_POST['horas_nocturnas_trabajadas'] ?? 0);
    $dias_dominicales = (int)($_POST['dias_dominicales'] ?? 0);
    $auxilio_alimentacion = (int)($_POST['auxilio_alimentacion'] ?? 0);

    // DEDUCCIONES
    $monto_del_desembolso = (int)($_POST['monto_del_desembolso'] ?? 0);
    $numero_cuotas_a_descontar = (int)($_POST['numero_cuotas_descontar'] ?? 0);
    $fecha_del_desemboloso = $_POST['fecha_del_desemboloso'] ?? '';
    $numero_de_cuota_pagada = (int)($_POST['numero_de_cuota_pagada'] ?? 0); 
    $nomina_en_que_termina_prestamo = $_POST['nomina_en_que_termina_prestamo'] ?? '';
    $valor_cuota = (int)($_POST['valor_cuota'] ?? 0);
    $saldo_del_prestamo = (int)($_POST['saldo_del_prestamo'] ?? 0);

    /*
        ==========================================
        ==========================================
                        DEVENGADOS
        ==========================================
        ==========================================
    */
    if (!empty($nombre) && !empty($departamento) && !empty($cargo) && $identificacion >= 0 && $dias_trabajados >= 0) {

        $salarioDiario = $sueldo / 30;
        $salario = $salarioDiario * $dias_trabajados;
        $vacaciones = ($sueldo * $dias_trabajados) / 720;
        $IBC = $sueldo;

        // Calcular Auxilio siguiendo normativa
        if ($sueldo <= 3501810) {
            $auxilioTransporte = (249095 / 30) * $dias_trabajados;
        } else {
            $auxilioTransporte = 0;
        }

        // Calcular Pago incapacidad eps
        $IBC_diario = $salarioDiario;
        $valorDiaIncapacidad = $IBC_diario * 0.666;
        $pago_incapacidad = $valorDiaIncapacidad * $dias_incapacidad;

        // Calcular recargo nocturno
        $valor_hora_diurna = $salarioDiario / 24;
        $valor_hora_nocturna = $valor_hora_diurna + ($valor_hora_diurna * 0.35);
        $recargo_total = $horas_nocturnas_trabajadas * $valor_hora_nocturna;

        // Calcular incapacidad ARL
        $incapacidad_ARL = $salarioDiario * $dias_incapacidad;

        // Calcular horas dominicales
        $valor_hora_ordinaria = $sueldo / 210;
        $valor_hora_dominical = $valor_hora_ordinaria * 1.90;
        $total_dominical = $valor_hora_dominical * $dias_dominicales;

        // Calcular Auxilio Alimenticio no prestacional
        $total_remuneracion = $sueldo + $auxilio_alimentacion;
        $limite_legal = $total_remuneracion * 0.40;
        $valor_excedente = max(0, $total_remuneracion - $limite_legal);
        $IBC = $sueldo + $valor_excedente;

        // Total devengado
        $total_devengado = $salario + $vacaciones + $auxilioTransporte + $pago_incapacidad
            + $incapacidad_ARL + $recargo_total + $total_dominical + $auxilio_alimentacion;

        /*
        ==========================================
        ==========================================
                    DEDUCCIONES NOMINALES
        ==========================================
        ==========================================
        */
        $SMMLV = 1750905;
        $salud = $IBC * 0.04;
        $pension = $IBC * 0.04;

        if ($IBC < $SMMLV * 4) {
            $fondo_solidaridad_pensional = 0;
        } else if ($IBC >= $SMMLV * 4 && $IBC <= $SMMLV * 16) {
            $fondo_solidaridad_pensional = $IBC * 0.010;
        } else if ($IBC > $SMMLV * 16 && $IBC <= $SMMLV * 17) {
            $fondo_solidaridad_pensional = $IBC * 0.012;
        } else if ($IBC > $SMMLV * 17 && $IBC <= $SMMLV * 18) {
            $fondo_solidaridad_pensional = $IBC * 0.014;
        } else if ($IBC > $SMMLV * 18 && $IBC <= $SMMLV * 19) {
            $fondo_solidaridad_pensional = $IBC * 0.016;
        } else if ($IBC > $SMMLV * 19 && $IBC <= $SMMLV * 20) {
            $fondo_solidaridad_pensional = $IBC * 0.018;
        } else if ($IBC > $SMMLV * 20) {
            $fondo_solidaridad_pensional = $IBC * 0.020;
        } else {
            $fondo_solidaridad_pensional = 0;
        }

        /*
        ==========================================
        ==========================================
                    DEDUCCIONES POR PRESTAMO
        ==========================================
        ==========================================
        */
        $cuotas_por_descontar = max(0, $numero_cuotas_a_descontar - $numero_de_cuota_pagada);
        $total_deducciones = $salud + $pension + $fondo_solidaridad_pensional + $valor_cuota;
        $total_nomina_a_pagar = $total_devengado - $total_deducciones;

        /*
        ==========================================
        ==========================================
                    PRESTACIONES SOCIALES
        ==========================================
        ==========================================
        */
        $prestaciones_prima = ($sueldo + $auxilioTransporte) * 0.0833;
        $prestaciones_cesantias = ($sueldo + $auxilioTransporte) * 0.0833;
        $prestaciones_interes_cesantias = ($prestaciones_cesantias * $dias_trabajados * 0.12) / 360;
        $prestaciones_vacaciones = $sueldo * 0.0417;
        $total_prestaciones = $prestaciones_prima + $prestaciones_cesantias + $prestaciones_interes_cesantias + $prestaciones_vacaciones;



        /*
        ==========================================
        ==========================================
                    COSTES DE LA EMPRESA
        ==========================================
        ==========================================
        */
        $pension_patronal = $IBC * 0.12;     
        $arl_patronal     = $IBC * 0.00522;   
        $caja_compensacion = $IBC * 0.04;     

        if ($IBC < ($SMMLV * 10)) {
            $salud_patronal = 0;
            $sena           = 0;
            $icbf           = 0;
        } else {
            $salud_patronal = $IBC * 0.085;   
            $sena           = $IBC * 0.02;    
            $icbf           = $IBC * 0.03;   
        }

        $total_aportes_patronales = $pension_patronal + $arl_patronal + $caja_compensacion + $salud_patronal + $sena + $icbf;

        $coste_empresa_mensual = $total_devengado + $total_aportes_patronales + $total_prestaciones;
        $coste_empresa_diario  = $coste_empresa_mensual / 30;
        $coste_empresa_anual   = $coste_empresa_mensual * 12;

        // Leer los usuarios existentes en el archivo JSON
        $usuarios = [];
        if (file_exists($archivo_json)) {
            $contenido = file_get_contents($archivo_json);
            $usuarios = json_decode($contenido, true) ?? [];
        }

        // Estructura del nuevo registro
        $nuevo_usuario = [
            'id' => uniqid(),
            'nombre' => $nombre,
            'identificacion' => $identificacion,
            'departamento' => $departamento,
            'cargo' => $cargo,
            'sueldo' => $sueldo,
            'dias_trabajados' => $dias_trabajados,
            'dias_incapacidad' => $dias_incapacidad,
            'horas_nocturnas_trabajadas' => $horas_nocturnas_trabajadas,
            'dias_dominicales' => $dias_dominicales,
            'auxilio_alimentacion' => $auxilio_alimentacion,

            // Préstamos
            'monto_del_desembolso' => $monto_del_desembolso,
            'numero_cuotas_a_descontar' => $numero_cuotas_a_descontar,
            'fecha_del_desemboloso' => $fecha_del_desemboloso,
            'numero_de_cuota_pagada' => $numero_de_cuota_pagada,
            'cuotas_por_descontar' => $cuotas_por_descontar,
            'nomina_en_que_termina_prestamo' => $nomina_en_que_termina_prestamo,
            'valor_cuota' => $valor_cuota,
            'saldo_del_prestamo' => $saldo_del_prestamo,

            // Cálculos devengados
            'salario' => $salario,
            'salarioDiario' => $salarioDiario,
            'valor_hora_ordinaria' => $valor_hora_ordinaria,
            'valor_hora_diurna' => $valor_hora_diurna,
            'valor_hora_nocturna' => $valor_hora_nocturna,
            'valor_hora_dominical' => $valor_hora_dominical,
            'vacaciones' => $vacaciones,
            'recargo_total' => $recargo_total,
            'total_dominical' => $total_dominical,
            'valorDiaIncapacidad' => $valorDiaIncapacidad,
            'pago_incapacidad' => $pago_incapacidad,
            'incapacidad_ARL' => $incapacidad_ARL,

            // Bases de cotización (IBC)
            'IBC' => $IBC,
            'IBC_diario' => $IBC_diario,
            'limite_legal' => $limite_legal,
            'valor_excedente' => $valor_excedente,

            // Totales devengados y auxilios
            'auxilioTransporte' => $auxilioTransporte,
            'total_remuneracion' => $total_remuneracion,
            'total_devengado' => $total_devengado,

            // Deducciones de ley y totales
            'salud' => $salud,
            'pension' => $pension,
            'fondo_solidaridad_pensional' => $fondo_solidaridad_pensional,
            'total_deducciones' => $total_deducciones,

            // Nómina final
            'total_nomina_a_pagar' => $total_nomina_a_pagar,

            // Provisiones de Prestaciones Sociales
            'prestaciones_prima' => $prestaciones_prima,
            'prestaciones_cesantias' => $prestaciones_cesantias,
            'prestaciones_interes_cesantias' => $prestaciones_interes_cesantias,
            'prestaciones_vacaciones' => $prestaciones_vacaciones,
            'total_prestaciones' => $total_prestaciones,

            // costes de la empresa
            'coste_empresa_diario'  => $coste_empresa_diario,
            'coste_empresa_mensual' => $coste_empresa_mensual,
            'coste_empresa_anual'   => $coste_empresa_anual
        ];

        // Añadir el nuevo registro al array de usuarios
        $usuarios[] = $nuevo_usuario;

        // Guardar en el archivo JSON
        file_put_contents($archivo_json, json_encode($usuarios, JSON_PRETTY_PRINT));
    }

    // Redireccionar al frontend
    header('Location: index.php');
    exit();
}
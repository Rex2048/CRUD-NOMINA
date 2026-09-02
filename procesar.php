<?php
//Archivo para almacenar datos de usuarios
$archivo_json = 'usuarios.json';

// CASO 1: REGISTRAR UN NUEVO USUARIO (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Capturar y limpiar los datos enviados por el formulario frontend
    $nombre = trim($_POST['nombre']);
    $identificacion = (int)$_POST['identificacion'];
    $departamento = trim($_POST['departamento']);
    $cargo = trim($_POST['cargo']);

    $dias_trabajados = (int)$_POST['dias_trabajados'];
    $sueldo = (int)$_POST['sueldo'];
    //Falta agregar entradas del FRONT-END
		//DEVENGADO
	$dias_incapacidad = (int)$_POST['dias_incapacidad'];
    $horas_nocturnas_trabajadas = (int)$_POST['horas_nocturnas_trabajadas'];
    $dias_dominicales = (int)$_POST['dias_dominicales'];
    $auxilio_alimentacion = (int)$_POST['auxilio_alimentacion'];
		//DEDUCCIONES
	$monto_del_desembolso = (int)$_POST['$monto_del_desembolso'];
	$numero_cuotas_a_descontar = (int)$_POST['$numero_cuotas_descontar'];
	$fecha_del_desemboloso = $_POST['fecha_del_desemboloso'] ?? '';
	$numero_de_cuota_pagada = $_POST['numero_de_cuota_pagada']; 
	$nomina_en_que_termina_prestamo = $_POST['nomina_en_que_termina_prestamo'];
	$valor_cuota = $_POST['valor_cuota'];
	$saldo_del_prestamo = $_POST['saldo_del_prestamo'];
    /*
        ==========================================
        ==========================================
                        DEVENGADOS
        ==========================================
        ==========================================
    
    */
    // Validar de forma básica que los datos no estén vacíos o sean incorrectos
    if (!empty($nombre) && !empty($departamento) && !empty($cargo) && $identificacion >= 0 && $dias_trabajados >= 0) {


        $salarioDiario = $sueldo / 30;
        $salario = $salarioDiario * $dias_trabajados;
        $vacaciones = ($sueldo * $dias_trabajados) / 720;
        $IBC = $sueldo;
        //Calcular Auxlio siguiendo normativa
        if ($sueldo <= 3501810) {
            $auxilioTransporte = (249095 / 30) * $dias_trabajados;
        } else {
            $auxilioTransporte = 0;
        }

        //Calcular Pago incapacidad eps
        $IBC_diario = $salarioDiario;
        $valorDiaIncapacidad = $IBC_diario * 0.666;
        $pago_incapacidad = $valorDiaIncapacidad * $dias_incapacidad;

        //Calcular recargo nocturno
        $valor_hora_diurna = $salarioDiario / 24;
        $valor_hora_nocturna = $valor_hora_diurna + $valor_hora_diurna * 0.35;
        $recargo_total = $horas_nocturnas_trabajadas * $valor_hora_nocturna;

        //Calcular incapacidad ARL
        $incapacidad_ARL = $salarioDiario  * $dias_incapacidad;

        //Calcular horas dias_dominicales
        $valor_hora_ordinaria = $sueldo / 210;
        $valor_hora_dominical = $valor_hora_ordinaria * 1.90;
        $total_dominical = $valor_hora_dominical * $horas_nocturnas_trabajadas;

        //Calcular Auxilio Alimenticio no prestacional
        $total_remuneracion = $sueldo + $auxilio_alimentacion;
        $limite_legal = $total_remuneracion * 0.40;
        $valor_excedente = $total_remuneracion - $limite_legal;
        $IBC = $sueldo + $valor_excedente;

        //Total devengado
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
        } else if ($IBC > $SMMLV * 4 && $IBC <= $SMMLV * 16) {
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
		$cuotas_por_descontar = $numero_cuotas_a_descontar - $numero_de_cuota_pagada;



        // Leer los usuarios existentes en el archivo JSON
        $usuarios = [];
        if (file_exists($archivo_json)) {
            $contenido = file_get_contents($archivo_json);
            $usuarios = json_decode($contenido, true) ?? [];
        }

        // Crear la estructura del nuevo usuario con su ID único simulado
        $nuevo_usuario = [
            // Datos base del usuario
            'id' => uniqid(),
            'nombre' => $nombre,
            'identificacion' => $identificacion,
            'departamento' => $departamento,
            'cargo' => $cargo,
            'dias_trabajados' => $dias_trabajados,
            'dias_incapacidad' => $dias_incapacidad,
            'horas_nocturnas_trabajadas' => $horas_nocturnas_trabajadas,
            'dias_dominicales' => $dias_dominicales,
            'auxilio_alimentacion' => $auxilio_alimentacion,

            // Variables de salarios y cálculos diarios
            'salario' => $salario,
            'salarioDiario' => $salarioDiario,
            'valor_hora_ordinaria' => $valor_hora_ordinaria,
            'valor_hora_diurna' => $valor_hora_diurna,
            'valor_hora_nocturna' => $valor_hora_nocturna,
            'valor_hora_dominical' => $valor_hora_dominical,

            // Novedades, recargos e incapacidades
            'vacaciones' => $vacaciones,
            'recargo_total' => $recargo_total,
            'total_dominical' => $total_dominical,
            'valorDiaIncapacidad' => $valorDiaIncapacidad,
            'pago_incapacidad' => $pago_incapacidad,
            'incapacidad_ARL' => $incapacidad_ARL,

            // Bases de cotización (IBC) y topes
            'IBC' => $IBC,
            'IBC_diario' => $IBC_diario,
            'limite_legal' => $limite_legal,
            'valor_excedente' => $valor_excedente,

            // Totales devengados y auxilios
            'auxilioTransporte' => $auxilioTransporte,
            'total_remuneracion' => $total_remuneracion,
            'total_devengado' => $total_devengado,

            // Deducciones de ley
            'salud' => $salud,
            'pension' => $pension,
            'fondo_solidaridad_pensional' => $fondo_solidaridad_pensional
        ];

        // Añadir el nuevo registro al array de usuarios
        $usuarios[] = $nuevo_usuario;

        // Convertir todo el array de vuelta a formato de texto JSON bien formateado
        $json_final = json_encode($usuarios, JSON_PRETTY_PRINT);

        // Guardar (escribir) el texto en el archivo usuarios.json
        file_put_contents($archivo_json, $json_final);
    }

    // Redireccionar al usuario de vuelta al frontend para limpiar la petición
    header('Location: index.php');
    exit();
}

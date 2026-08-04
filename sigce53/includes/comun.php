<?php

function getConflictoIntereses($conexion,$usuario_solicita){

    try
    {

    $conflicto_intereses = $conexion->prepare("SELECT getConflictoIntereses(?)");
    if (!$conflicto_intereses) {
        throw new Exception("ERROR AL CONSULTAR CONFLICTO (ERR:001)");
    }
    $conflicto_intereses->bind_param("i", $usuario_solicita);
    if (!$conflicto_intereses->execute()) {
        throw new Exception("ERROR AL CONSULTAR CONFLICTO (ERR:002)");
    }
    $conflicto_intereses->store_result();
    $conflicto_intereses->bind_result($clientes_conflicto);
    $conflicto_intereses->fetch();
    $conflicto_intereses->close();

    return $clientes_conflicto;

    } catch (mysqli_sql_exception $e) {
        return '';
    }

}

function getRazonSocial($conexion,$no_cliente,$fecha_solicitud){

    try
    {
    
    $fecha_solicitud = convertirAFechaEspecifica($fecha_solicitud);

    $razon = $conexion->prepare("SELECT getRazonSocial(?,?)");
    if (!$razon) {
        throw new Exception("ERROR AL CONSULTAR CONFLICTO (ERR:001)");
    }
    $razon->bind_param("ss", $no_cliente, $fecha_solicitud);
    if (!$razon->execute()) {
        throw new Exception("ERROR AL CONSULTAR CONFLICTO (ERR:002)");
    }
    $razon->store_result();
    $razon->bind_result($razon_social);
    $razon->fetch();
    $razon->close();

    return $razon_social;

    } catch (mysqli_sql_exception $e) {
        return '';
    }

}

function convertirAFechaEspecifica($fechaHora) {
    $formatos = [
        'Y-m-d H:i:s',
        'Y-m-d',
        'Y/m/d H:i:s',
        'Y/m/d',  
        'd-m-Y H:i:s',
        'd-m-Y', 
        'd/m/Y H:i:s',
        'd/m/Y', 
    ];

    foreach ($formatos as $formato) {
        $fechaHoraObj = DateTime::createFromFormat($formato, $fechaHora);

        if ($fechaHoraObj !== false) {
            $partesFechaHora = explode(' ', $fechaHora);

            if (count($partesFechaHora) === 1) {
                $fechaFormateada = $fechaHoraObj->format('Y-m-d') . ' 00:00:00';
            } else {
                $fechaFormateada = $fechaHoraObj->format('Y-m-d H:i:s');
            }

            return $fechaFormateada;
        }
    }

    return "";
}

function mdlGetMaguey($id_sinca, $tipo_consulta){

    try {
  
        include "../../common/conexion.php";
  
        $conexion->autocommit(false);
        $conexion->set_charset("utf8");
  
  
        $magueys = "";
        $especie   = "";
        $sep   = "";
  
            if($tipo_consulta == 0){
                $stmt = $conexion->prepare("CALL maguey_producciones(?);");
            }else if($tipo_consulta == 1 || $tipo_consulta == 2){
                $stmt = $conexion->prepare("CALL maguey_granel_entrada(?);");
            } else if($tipo_consulta == 3 || $tipo_consulta == 4){
                $stmt = $conexion->prepare("CALL maguey_granel_entrada_envasado(?);");
            } else if($tipo_consulta == 5 || $tipo_consulta == 6){
                $stmt = $conexion->prepare("CALL maguey_envasado_entrada(?);");
            } else if($tipo_consulta == 7 || $tipo_consulta == 8){
                $stmt = $conexion->prepare("CALL maguey_almacen_granel_entrada(?);");
            } else if($tipo_consulta == 9){
                $stmt = $conexion->prepare("CALL maguey_almacen_envasado_entrada(?);");
            }
            if (!$stmt) {
                throw new Exception("Error al consultar lotes REF-1");
            }
            $stmt->bind_param("s",$id_sinca);
  
            if (!$stmt->execute()) {
                throw new Exception("Error al consultar lotes REF-2");
            }
  
            $stmt->store_result();
            
            if($tipo_consulta == 0){
                $stmt->bind_result($_especie,$_pinas,$_agave,$_art,$_predio,$_origen);
            }else if($tipo_consulta == 5 || $tipo_consulta == 6 || $tipo_consulta == 9 ){
                $stmt->bind_result($_especie,$_litros,$_predio,$_origen);
            } else {
                $stmt->bind_result($_especie,$_agave,$_litros,$_predio,$_origen);
            }
  
            
            while ($stmt->fetch()) {
                $especie .= $sep.$_especie;
                $sep   = ",";
            }
            $stmt->close();
        
            $agaves = $especie;
        
            if ($agaves != '') {
        
                $agaves = explode(",", $agaves);
                $magueys = "";
                foreach ($agaves as $info) {
                    $agaves2 = explode("( ", $info);
        
                    $comun = $agaves2[0];
                    $agaveprimera = explode(" ", $agaves2[1]);
                    $especie = "<i>(" . ucwords(strtolower($agaveprimera[0])) . " " . strtolower($agaveprimera[1])  . strtolower($agaveprimera[2]) . "</i>";
        
                    $magueys .= $comun . " " . $especie . ", ";
                }
                $magueys = rtrim($magueys, ", "); // quito la ultima coma de la concatenación
        
            } 
    
  
  
            $conexion->commit();
            $conexion->close();
            return $magueys;
    
        } catch (Exception $e) {
            print_r($e);
            $conexion->rollback();
            $conexion->close();
            return "";
        }
    
            
  
}

function getStatusClienteSuspendido($conexion, $no_cliente) {
    $sql = "CALL ed_estatus_cliente(?)";

    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        throw new Exception("Error al preparar consulta de cliente");
    }

    $stmt->bind_param("s", $no_cliente);

    if (!$stmt->execute()) {
        throw new Exception("Error al ejecutar consulta de cliente");
    }

    $result = $stmt->get_result();

    $suspendido = false;
    $fechaSuspension = null;

    while ($row = $result->fetch_assoc()) {
        if ($row['estatus_actual'] === 'OFF') {
            $suspendido = true;
            $fechaSuspension = formatearFechaNuevo($row['fecha_ultimo_cambio']);
            break; // ya encontramos lo que buscamos
        }
    }

    $stmt->close();

    // IMPORTANTE cuando usas CALL
    while ($conexion->more_results() && $conexion->next_result()) {;}

    return [
        "suspendido" => $suspendido,
        "fecha" => $fechaSuspension
    ];
}

function formatearFechaNuevo($fecha) {
    $date = new DateTime($fecha);

    $meses = [
        "enero", "febrero", "marzo", "abril", "mayo", "junio",
        "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"
    ];

    $dia = $date->format('d');
    $mes = $meses[(int)$date->format('m') - 1];
    $anio = $date->format('Y');

    return "$dia de $mes de $anio";
}
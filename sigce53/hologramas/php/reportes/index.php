<?php
if (isset($_GET["action"]) && !empty($_GET["action"])) {
  $action = $_GET["action"];
  switch($action) {
    case "estadisticas1"         : estadisticas1();           break;
    case "estadisticas2"         : estadisticas2();           break;
    case "estadisticas3"         : estadisticas3();           break;
    case "acumulado":             acumulado();                break;
  }
}

function acumulado() {
  try {
    include(__DIR__ . '/../../../common/conexion.php');
	  $conexion->set_charset("utf8");
    $sqltxt = "
      SELECT 
          SUM(importe_total) AS total_sales
      FROM 
          edo_cuenta_servicios
      WHERE id_edo_cuenta IS NOT NULL  AND YEAR(fecha_concepto) > 2021 
      AND no_control NOT IN ('C9999','C9998') AND fecha_concepto != '0000-00-00';
                                
    ";
    $sql = $conexion->prepare($sqltxt);
    if (!$sql) throw new Exception("Ocurrio un error al obtener la información (ERROR:01) $conexion->error");
    if (!$sql->execute()) throw new Exception("Ocurrio un error al obtener la información (ERROR:02) $conexion->error");
    $sql->store_result();
    $sql->bind_result($_TOTAL);
    $sql->fetch();
    $promedio = $_TOTAL / 37;
    $sql->close();
    // 2022
    $sqltxt = "
      SELECT 
          SUM(importe_total) AS total_sales
      FROM 
          edo_cuenta_servicios
      WHERE id_edo_cuenta IS NOT NULL  AND YEAR(fecha_concepto) = 2022
      AND no_control NOT IN ('C9999','C9998') AND fecha_concepto != '0000-00-00';
                                
    ";
    $sql = $conexion->prepare($sqltxt);
    if (!$sql) throw new Exception("Ocurrio un error al obtener la información (ERROR:01) $conexion->error");
    if (!$sql->execute()) throw new Exception("Ocurrio un error al obtener la información (ERROR:02) $conexion->error");
    $sql->store_result();
    $sql->bind_result($_TOTAL22);
    $sql->fetch();
    $promedio22 = $_TOTAL22 / 12;
    $sql->close();
    // 2023
    $sqltxt = "
      SELECT 
          SUM(importe_total) AS total_sales
      FROM 
          edo_cuenta_servicios
      WHERE id_edo_cuenta IS NOT NULL  AND YEAR(fecha_concepto) = 2023
      AND no_control NOT IN ('C9999','C9998') AND fecha_concepto != '0000-00-00';
                                
    ";
    $sql = $conexion->prepare($sqltxt);
    if (!$sql) throw new Exception("Ocurrio un error al obtener la información (ERROR:01) $conexion->error");
    if (!$sql->execute()) throw new Exception("Ocurrio un error al obtener la información (ERROR:02) $conexion->error");
    $sql->store_result();
    $sql->bind_result($_TOTAL23);
    $sql->fetch();
    $promedio23 = $_TOTAL23 / 12;
    $sql->close();
    // 2024
    $sqltxt = "
      SELECT 
          SUM(importe_total) AS total_sales
      FROM 
          edo_cuenta_servicios
      WHERE id_edo_cuenta IS NOT NULL  AND YEAR(fecha_concepto) = 2024
      AND no_control NOT IN ('C9999','C9998') AND fecha_concepto != '0000-00-00';         
    ";
    $sql = $conexion->prepare($sqltxt);
    if (!$sql) throw new Exception("Ocurrio un error al obtener la información (ERROR:01) $conexion->error");
    if (!$sql->execute()) throw new Exception("Ocurrio un error al obtener la información (ERROR:02) $conexion->error");
    $sql->store_result();
    $sql->bind_result($_TOTAL24);
    $sql->fetch();
    $promedio24 = $_TOTAL24 / 12;
    $sql->close();
    // 2025
    $sqltxt = "
    SELECT 
        SUM(importe_total) AS total_sales
    FROM 
        edo_cuenta_servicios
    WHERE id_edo_cuenta IS NOT NULL  AND YEAR(fecha_concepto) = 2025
    AND no_control NOT IN ('C9999','C9998') AND fecha_concepto != '0000-00-00';
    ";
    $sql = $conexion->prepare($sqltxt);
    if (!$sql) throw new Exception("Ocurrio un error al obtener la información (ERROR:01) $conexion->error");
    if (!$sql->execute()) throw new Exception("Ocurrio un error al obtener la información (ERROR:02) $conexion->error");
    $sql->store_result();
    $sql->bind_result($_TOTAL25);
    $sql->fetch();
    $sql->close();
    // RLY
    $sqltxt = "
    SELECT 
        SUM(importe_total) AS total_sales
    FROM 
        edo_cuenta_servicios
    WHERE id_edo_cuenta IS NOT NULL  
    AND (fecha_concepto >= CURDATE() - INTERVAL 1 YEAR)
    -- AND ( fecha_concepto >= DATE_FORMAT(CURDATE(), NOW())
    AND fecha_concepto < CURDATE() 
    AND no_control NOT IN ('C9999','C9998') AND fecha_concepto != '0000-00-00';
    ";
    $sql = $conexion->prepare($sqltxt);
    if (!$sql) throw new Exception("Ocurrio un error al obtener la información (ERROR:01) $conexion->error");
    if (!$sql->execute()) throw new Exception("Ocurrio un error al obtener la información (ERROR:02) $conexion->error");
    $sql->store_result();
    $sql->bind_result($_TOTALRLY);
    $sql->fetch();
    $sql->close();
    // TOTAL DEUDOR
    $sqltxt = "
    SELECT SUM(importe_total) sumaSaldo 
    FROM edo_cuenta_servicios 
    WHERE no_control NOT IN ('0','C9998','C9999') AND id_edo_cuenta IS NOT NULL 
     AND YEAR(fecha_registro) > 2021 AND (importe_pagado > 0 || importe_pendiente > 0) ";
    $sql = $conexion->prepare($sqltxt);
    if (!$sql) throw new Exception("Ocurrio un error al obtener la información (ERROR:01) $conexion->error");
    if (!$sql->execute()) throw new Exception("Ocurrio un error al obtener la información (ERROR:02) $conexion->error");
    $sql->store_result();
    $sql->bind_result($_SALDO_DEUDOR);
    $sql->fetch();
    $sql->close();
    // TOTAL PAGADO
    $sqltxt = "
    SELECT SUM(ecc.saldo_actual_pagos) sumaPagado
          FROM edo_cuenta_caratula ecc
    -- LEFT JOIN edo_cuenta_pagos ecp ON ecc.id_edo_cuenta = ecp.id_edo_cuenta
    WHERE ecc.no_control NOT IN ('0','C9998','C9999') AND (ecc.estatus = '1' OR ecc.estatus IS NULL)
     -- AND YEAR(ecc.fecha_creacion) > 2021 AND (ecc.importe_pagado > 0 || ecc.importe_pendiente > 0) ";
    $sql = $conexion->prepare($sqltxt);
    if (!$sql) throw new Exception("Ocurrio un error al obtener la información (ERROR:01) $conexion->error");
    if (!$sql->execute()) throw new Exception("Ocurrio un error al obtener la información (ERROR:02) $conexion->error");
    $sql->store_result();
    $sql->bind_result($_SALDO_PAGADO);
    $sql->fetch();
    $sql->close();

    $_SALDO_DEUDOR -= $_SALDO_PAGADO;
    
    $json["acumulado"]  = "$ ". number_format($_TOTAL, 2);
    $json["total22"]  = "$ ". number_format($_TOTAL22, 2);
    $json["promedio22"]  = "$ ". number_format($promedio22, 2);
    $json["total23"]  = "$ ". number_format($_TOTAL23, 2);
    $json["promedio23"]  = "$ ". number_format($promedio23, 2);
    $json["total24"]  = "$ ". number_format($_TOTAL24, 2);
    $json["promedio24"]  = "$ ". number_format($promedio24, 2);
    $json["total25"]  = "$ ". number_format($_TOTAL25, 2);
    $json["totalRLY"]  = "$ ". number_format($_TOTALRLY, 2);
    $json["saldoDeudor"]  = "$ ". number_format($_SALDO_DEUDOR, 2);
    $json["totalRLY"]  = "$ ". number_format($_TOTALRLY, 2);
    $json["rotacion"]  = "$ ". $_TOTALRLY / $_SALDO_DEUDOR;
    $json["diasCxC"]  = ceil(365 / ($_TOTALRLY / $_SALDO_DEUDOR)) . " días";
    echo json_encode($json);
  } catch (\Exception $e) {
    $conexion->close();
    echo $e->getMessage();
    header('HTTP/1.1 500 Internal Server Booboo');
    header('Content-Type: application/json; charset=UTF-8');
    die();
  }
}

function estadisticas1() {  
  try {
    include(__DIR__ . '/../../../common/conexion.php');
	  $conexion->set_charset("utf8");
    $tipo        = $_GET['tipo'];
    $limit        = isset($_GET['limit']) ? " LIMIT " . $_GET['limit'] : "";
    $offset       = isset($_GET['offset']) ? " OFFSET " . $_GET['offset'] : "";

    $sqltxt = "
      SELECT 
          MONTH(fecha) AS month,
          YEAR(fecha) AS year,
          SUM(cantidad) AS total_sales
      FROM 
          h_pedidos
      WHERE no_cliente NOT IN ('C9999','C9998') 
      GROUP BY 
          year, month
      ORDER BY 
          year DESC, month DESC;
    ";

    $sql = $conexion->prepare($sqltxt);
    if (!$sql) throw new Exception("Ocurrio un error al obtener la información (ERROR:01) $conexion->error");
    if (!$sql->execute()) throw new Exception("Ocurrio un error al obtener la información (ERROR:02) $conexion->error");
    $sql->store_result();
    $sql->bind_result($_MES, $_ANIO, $_MONTO);
    $count = 0;
    $arrMes = [1=>"Ene", 2=>"Feb", 3=>"Mar", 4=>"Abr", 5=>"May", 6=>"Jun",
                  7=>"Jul", 8=>"Ago", 9=>"Sep", 10=>"Oct", 11=>"Nov", 12=>"Dic" ];
    while($sql->fetch()) {
      $arrayGrafica[$_MES]["mes"] = $_MES;
      $arrayGrafica[$_MES]["nMes"] = $arrMes[$_MES];
      $arrayGrafica[$_MES]["m".$_ANIO] = $_MONTO;
    }
    
    foreach ($arrayGrafica as $key => $value) {
      $veces = 0; $suma = 0;
      for($i = 2022; $i <= date("Y"); $i++) {
        if(isset($value["m".$i])) {
          $suma += $value["m".$i];
          $veces++;
        }
        $promedio = $suma / $veces;
        $arrayGrafica[$key]["promedio"] = $promedio;
      }
    }

    $sqltxt = "
      SELECT 
          MONTH(fecha) AS month,
          YEAR(fecha) AS year,
          SUM(cantidad) AS total_sales
      FROM 
          h_pedidos
      WHERE no_cliente NOT IN ('C9999','C9998') 
      GROUP BY 
          year, month
      ORDER BY 
          year DESC, month DESC
    ";

    $sql = $conexion->prepare("SELECT COUNT(*) FROM ($sqltxt) AS TABLA ");
    if (!$sql) throw new Exception("Ocurrio un error al obtener la información (ERROR:03) $conexion->error");
    if (!$sql->execute()) throw new Exception("Ocurrio un error al obtener la información (ERROR:04) $conexion->error");
    $sql->store_result();
    $sql->bind_result($numRegistros);
    $sql->fetch();
    $sql->close();

    $sql = $conexion->prepare("$sqltxt $limit $offset ");
    if (!$sql) throw new Exception("Ocurrio un error al obtener la información (ERROR:05) $conexion->error");
    if (!$sql->execute()) throw new Exception("Ocurrio un error al obtener la información (ERROR:06) $conexion->error");
    $sql->store_result();
    $sql->bind_result($_MES, $_ANIO, $_MONTO);
    $count = 0;
    while($sql->fetch()) {
        $array[$count]["mes"] = $_MES;
        $array[$count]["anio"] = $_ANIO;
        $array[$count]["monto"]   = $_MONTO;
        $array[$count]["nMes"] = $arrMes[$_MES];
        $count++;
    }
    $sql->close();
    
    $json["total"]  = $numRegistros;
    $json["rows"]   = $array;
    $json["grafica"] = $arrayGrafica;
    echo json_encode($json);
  } catch (\Exception $e) {
    $conexion->close();
    echo $e->getMessage();
    header('HTTP/1.1 500 Internal Server Booboo');
    header('Content-Type: application/json; charset=UTF-8');
    die();
  }
}

function estadisticas2() {  
  try {
    include(__DIR__ . '/../../../common/conexion.php');
    $conexion->set_charset("utf8");
    $tipo        = $_GET['tipo'] ?? '';
    $limit       = isset($_GET['limit']) ? " LIMIT " . $_GET['limit'] : "";
    $offset      = isset($_GET['offset']) ? " OFFSET " . $_GET['offset'] : "";

    $txtcond = '';
    $groupby = ' no_control ';
    $orderby = ' total_sales DESC ';
    $_MESANIO = 'TODOS';
    if((isset($_GET['anio']) && isset($_GET['mes'])) && ($_GET['anio'] >= 0 && $_GET['mes'] >= 0)) {
      if($_GET['anio'] > 0 && $_GET['mes'] >= 0) { 
        if ($_GET['mes'] == 0) {
          $txtcond =  ' AND ( YEAR(fecha_concepto) = '.$_GET['anio'].' ) ';
          $_MESANIO = $_GET['anio'];
          $groupby = ' year, no_control ';
          $orderby = ' year DESC, total_sales DESC ';
        } else {  
          $txtcond =  ' AND ( YEAR(fecha_concepto) = '.$_GET['anio'].' AND MONTH(fecha_concepto) = '.$_GET['mes'] . ' ) ';
          $_MESANIO = $_GET['mes'].'-'. $_GET['anio'] ;
          $groupby = ' year, month, no_control ';
          $orderby = ' year DESC, month DESC, total_sales DESC ';
        }
      }
    }

    $getAnio = $_GET['anio'] ?? -1;

    if($getAnio >= 0) {
      $sqltxt = "
        SELECT 
            YEAR(fecha_concepto) AS year,
            MONTH(fecha_concepto) AS month,
            edo_cuenta_servicios.no_control,
            SUM(importe_total) AS total_sales
        FROM 
            edo_cuenta_servicios 
        INNER JOIN (
          SELECT no_control, SUM(importe_total) AS total_sales
            FROM edo_cuenta_servicios 
            WHERE id_edo_cuenta IS NOT NULL  AND no_control NOT IN ('C9999','C9998') AND fecha_concepto != '0000-00-00' 
            $txtcond 
            GROUP BY $groupby 
            ORDER BY $orderby 
        ) AS global_maxc
        WHERE id_edo_cuenta IS NOT NULL $txtcond
        AND edo_cuenta_servicios.no_control NOT IN ('C9999','C9998') AND fecha_concepto != '0000-00-00' 
        GROUP BY 
            year, month, edo_cuenta_servicios.no_control
        ORDER BY 
            year DESC, total_sales DESC
        LIMIT 10                      
      ";
    } else {
      $sqltxt = "
      SELECT  YEAR(fecha_concepto) AS year,
            MONTH(fecha_concepto) AS month, 
            no_control, SUM(importe_total) AS total_sales
        FROM 
          edo_cuenta_servicios 
        WHERE 
          id_edo_cuenta IS NOT NULL  AND no_control NOT IN ('C9999','C9998') AND fecha_concepto != '0000-00-00' 
        GROUP BY $groupby 
        ORDER BY $orderby
            LIMIT 10 ";
    }
    $sql = $conexion->prepare($sqltxt);
    if (!$sql) throw new Exception("Ocurrio un error al obtener la información (ERROR:01) $conexion->error");
    if (!$sql->execute()) throw new Exception("Ocurrio un error al obtener la información (ERROR:02) $conexion->error");
    $sql->store_result();
    
    $_ANIO = null;
    $_MES = null;
    $_NO_CONTROL = null;
    $_MONTO = null;
    
    $sql->bind_result($_ANIO, $_MES, $_NO_CONTROL, $_MONTO);
    
    $arrayGrafica = [];
    $arrCliente = [];
    $arrMes = [1=>"Ene", 2=>"Feb", 3=>"Mar", 4=>"Abr", 5=>"May", 6=>"Jun",
                  7=>"Jul", 8=>"Ago", 9=>"Sep", 10=>"Oct", 11=>"Nov", 12=>"Dic" ];
    
    while($sql->fetch()) {
      $keyAnio = $_ANIO ?? '';
      $keyNoControl = $_NO_CONTROL ?? '';
      
      $arrayGrafica[$keyAnio]["anio"] = $keyAnio;
      $arrayGrafica[$keyAnio][$keyNoControl] = $_MONTO;
      $arrCliente[] = $keyNoControl;
    }
    
    $orderAnio = ($getAnio > 0) ? " total_sales DESC " : " year DESC, total_sales DESC ";

    if($getAnio >= 0) {
      $sqltxt = "
        SELECT 
            c.nombre,
            YEAR(ecs.fecha_concepto) AS year,
            MONTH(ecs.fecha_concepto) AS month,
            ecs.no_control,
            SUM(ecs.importe_total) AS total_sales,
            IF(c.asociado='0','CLIENTE',IF(c.asociado='1','ASOCIADO',IF(c.asociado='2','SOCIO','')))
        FROM 
            edo_cuenta_servicios ecs 
        INNER JOIN 
            clientes c ON c.no_cliente = ecs.no_control
        WHERE ecs.id_edo_cuenta IS NOT NULL  $txtcond
        AND ecs.no_control NOT IN ('C9999','C9998') AND ecs.fecha_concepto != '0000-00-00' 
        AND ecs.no_control IN (
          SELECT no_control FROM (
            SELECT ecs.no_control, SUM(ecs.importe_total) AS total_sales
            FROM edo_cuenta_servicios ecs
            WHERE ecs.id_edo_cuenta IS NOT NULL  AND ecs.no_control NOT IN ('C9999','C9998') AND ecs.fecha_concepto != '0000-00-00' 
            $txtcond
            $txtcond 
            GROUP BY $groupby 
            ORDER BY $orderby 
            LIMIT 10) AS global_maxc
        )
        GROUP BY 
            ecs.no_control, year
        ORDER BY 
            $orderAnio
      ";
    } else {
      $sqltxt = "
      SELECT 
            c.nombre,
            'TODOS' year,
            'TODOS' month,
            ecs.no_control,
            ecs.total_sales,
            IF(c.asociado='0','CLIENTE',IF(c.asociado='1','ASOCIADO',IF(c.asociado='2','SOCIO','')))
        FROM (
        SELECT  ecs.no_control, SUM(ecs.importe_total) AS total_sales
            FROM edo_cuenta_servicios ecs
            WHERE ecs.id_edo_cuenta IS NOT NULL  AND ecs.no_control NOT IN ('C9999','C9998') AND ecs.fecha_concepto != '0000-00-00' 
            GROUP BY ecs.no_control 
            ORDER BY total_sales DESC
            LIMIT 10 ) AS ecs
        INNER JOIN 
            clientes c ON c.no_cliente = ecs.no_control 
            GROUP BY $groupby 
        ORDER BY $orderby ";
    }
    
    $sql = $conexion->prepare("SELECT COUNT(*) FROM ($sqltxt) AS TABLA ");
    if (!$sql) throw new Exception("Ocurrio un error al obtener la información (ERROR:03) $conexion->error");
    if (!$sql->execute()) throw new Exception("Ocurrio un error al obtener la información (ERROR:04) $conexion->error");
    $sql->store_result();
    $sql->bind_result($numRegistros);
    $sql->fetch();
    $sql->close();

    $sql = $conexion->prepare("$sqltxt $limit $offset ");
    if (!$sql) throw new Exception("Ocurrio un error al obtener la información (ERROR:05) $conexion->error");
    if (!$sql->execute()) throw new Exception("Ocurrio un error al obtener la información (ERROR:06) $conexion->error");
    $sql->store_result();
    
    $_NOMBRE = null;
    $_ANIO = null;
    $_MES = null;
    $_NO_CONTROL = null;
    $_MONTO = null;
    $_TIPO = null;
    
    $sql->bind_result($_NOMBRE, $_ANIO, $_MES, $_NO_CONTROL, $_MONTO, $_TIPO);
    
    $array = [];
    $count = 0;
    while($sql->fetch()) {
        $array[$count]["nombre"]   = $_NOMBRE;
        $array[$count]["nocontrol"] = $_NO_CONTROL;
        $array[$count]["anio"] = $_ANIO;
        $array[$count]["monto"]   = $_MONTO;
        $array[$count]["tipo"]   = $_TIPO;
        $count++;
    }
    $sql->close();
    
    $json = [];
    $json["total"]   = $numRegistros;
    $json["rows"]    = $array;
    $json["grafica"] = $arrayGrafica;
    $json["clientes"] = $arrCliente;
    echo json_encode($json);
  } catch (\Exception $e) {
    if (isset($conexion) && $conexion instanceof \mysqli) {
        $conexion->close();
    }
    echo $e->getMessage();
    header('HTTP/1.1 500 Internal Server Booboo');
    header('Content-Type: application/json; charset=UTF-8');
    die();
  }
}

function estadisticas3() {  
  try {
    include(__DIR__ . '/../../../common/conexion.php');
	  $conexion->set_charset("utf8");
    //$conexion->autocommit(FALSE);
    $tipo        = $_GET['tipo'];
    $limit        = isset($_GET['limit']) ? " LIMIT " . $_GET['limit'] : "";
    $offset       = isset($_GET['offset']) ? " OFFSET " . $_GET['offset'] : "";
    if($tipo == "grafico") {
      //$limit = " LIMIT 10";
      $offset = "";
    }
    $txtcond = '';
    $groupby = ' ecco.descripcion ';
    $orderby = ' IMPORTE_TOTAL DESC ';
    $_MESANIO = 'TODOS';
    if((isset($_GET['year']) && isset($_GET['month'])) && ($_GET['year'] >= 0 && $_GET['month'] >= 0)) {
      if($_GET['year'] > 0 && $_GET['month'] >= 0) { 
        if ($_GET['month'] == 0) {
          $txtcond =  ' AND ( YEAR(ecs.fecha_concepto) = '.$_GET['year'].' ) ';
          $_MESANIO = $_GET['year'];
          $groupby = ' YEAR(ecs.fecha_concepto), ecco.descripcion ';
          $orderby = ' YEAR(ecs.fecha_concepto) DESC, IMPORTE_TOTAL DESC ';
        } else {  
          $txtcond =  ' AND ( YEAR(ecs.fecha_concepto) = '.$_GET['year'].' AND MONTH(ecs.fecha_concepto) = '.$_GET['month'] . ' ) ';
          $_MESANIO = $_GET['month'].'-'. $_GET['year'] ;
          $groupby = ' YEAR(ecs.fecha_concepto), MONTH(ecs.fecha_concepto), ecco.descripcion ';
          $orderby = ' YEAR(ecs.fecha_concepto) DESC, MONTH(ecs.fecha_concepto) DESC, IMPORTE_TOTAL DESC ';
        }
      }
    }
    $sqltxt = "
      FROM (
        SELECT 
          IF(SUBSTRING_INDEX(ecs.clave, '-', 1) = 'UMGDE', 'GUÍAS DOCUMENTALES EXCLUSIVAS',IF(ecco.descripcion != '',UPPER(ecco.descripcion), ecs.datos_concepto) )SERVICIO, 
          ecs.fecha_concepto, 
          YEAR(ecs.fecha_concepto) ANIO, MONTH(ecs.fecha_concepto) MES,
          SUBSTRING_INDEX(ecs.clave, '-', 1), 
          ecs.clave, 
          SUM(ecs.importe_total) IMPORTE_TOTAL 
        FROM edo_cuenta_servicios ecs 
        LEFT JOIN edo_cuenta_conceptos ecco ON ecs.clave = ecco.clave 
        LEFT JOIN clientes c ON ecs.no_control = c.no_cliente 
        LEFT JOIN a_usuarios au ON ecs.usuario_registro = au.id_us 
        WHERE ecs.no_control NOT IN ('C9999','C9998') 
        $txtcond
        GROUP BY $groupby
        ORDER BY $orderby
      ) AS g  
    ";
    //echo "SELECT COUNT(g.SERVICIO) $sqltxt ";
    $sql = $conexion->prepare("SELECT COUNT(g.SERVICIO) $sqltxt ");
    if (!$sql) throw new Exception("Ocurrio un error al obtener la información (ERROR:01) $conexion->error");
    if (!$sql->execute()) throw new Exception("Ocurrio un error al obtener la información (ERROR:02) $conexion->error");
    $sql->store_result();
    $sql->bind_result($numRegistros);
    $sql->fetch();
    $sql->close();
    //echo "SELECT g.SERVICIO, g.IMPORTE_TOTAL, g.ANIO, g.MES $sqltxt ORDER BY g.IMPORTE_TOTAL DESC $limit $offset ";
    $sql = $conexion->prepare("SELECT g.SERVICIO, g.IMPORTE_TOTAL, g.ANIO, g.MES $sqltxt ORDER BY g.IMPORTE_TOTAL DESC $limit $offset ");
    if (!$sql) throw new Exception("Ocurrio un error al obtener la información (ERROR:03) $conexion->error");
    if (!$sql->execute()) throw new Exception("Ocurrio un error al obtener la información (ERROR:04) $conexion->error");
    $sql->store_result();
    $sql->bind_result($_SERVICIO, $_MONTO, $_ANIO, $_MES);
    $count = 0;
    while($sql->fetch()) {
      $array[$count]["servicio"] = $_SERVICIO;
      $array[$count]["mesanio"] = $_MESANIO;
      $array[$count]["monto"]   = $_MONTO;
      $count++;
    }
    $sql->close();
    
    $json["total"]  = $numRegistros;
    $json["rows"]   = $array;
    echo json_encode($json);
  } catch (\Exception $e) {
    $conexion->close();
    echo $e->getMessage();
    header('HTTP/1.1 500 Internal Server Booboo');
    header('Content-Type: application/json; charset=UTF-8');
    die();
  }
}

?>
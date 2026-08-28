<?php


include(__DIR__ . "/../../../common/conexion.php");
//include('../../../common/ExceptionCRM.php');
$conexion->set_charset("utf8");

if(isset($_GET['search'])) {
    $search=$_GET['search'];
    $limit= $_GET['limit'];
    $offset= $_GET['offset'];

    $rows = [];
    $regtot = [];
    //$nivelUs = $_GET['nivelUs'];
    //$cargoUs = $_GET['cargoUs'];


    $iniSql = "SELECT h_pedidos.id_row, h_pedidos.no_pedido, DATE(h_pedidos.fecha) fecha, h_pedidos.no_cliente, h_pedidos.marca cve ,
        h_pedidos.serie, marcas.marca, h_pedidos.edo, h_pedidos.tipo, h_pedidos.fi, h_pedidos.ff, h_pedidos.cantidad, h_pedidos.status, h_pedidos.urgente,
        shp.tipo_pago, shp.comprobante, s.folio, h_pedidos.holograma
        FROM h_pedidos
        inner join marcas on marcas.no_cliente=h_pedidos.no_cliente and marcas.cve_marca=h_pedidos.marca
        INNER JOIN sh_detalle sh ON sh.id=h_pedidos.id_sh_d
        INNER JOIN sh_pedidos shp ON sh.id_solicitud = shp.id_solicitud
        INNER JOIN solicitudes s ON s.id = shp.id_solicitud
        ";

    /*$iniSql = "SELECT ie.*, u.*, md.id mdNA FROM inf_etiquetas ie
    LEFT JOIN a_usuarios u ON ie.id_us = u.id_us
    LEFT JOIN marcasdetalle md ON ((ie.num_informe = md.num_informe COLLATE utf8_unicode_ci ) AND (ie.id_compuesto_informe = md.id_compuesto_informe COLLATE utf8_unicode_ci ) )";*/
    $and = ""; $cadenaSql = "";
    $where = "";
    if($search != "") {
      	$and = (($and === "")? " WHERE ": $and);
      	$where =
      	' WHERE (h_pedidos.no_cliente like "%'.$search.'%" or h_pedidos.edo like "%'.$search.'%" or s.solicitud like "%'.$search.'%"
      	marcas.marca like "%'.$search.'%" ) ' ;
    } else {
        $fechaini = $_GET['fechaini'];
        $fechafin = $_GET['fechafin'];
        $nocliente = $_GET['nocliente'];

        if($fechaini == "" && $fechafin == "")
            $where = "";
        elseif ($fechaini != "" && $fechafin == "")
            $where = " WHERE DATE(h_pedidos.fecha) = '$fechaini' ";
        else
            $where = " WHERE DATE(h_pedidos.fecha) BETWEEN '$fechaini' AND '$fechafin' ";

        if($nocliente != "") {
            $where .= ($where !== "") ? " AND " : " WHERE ";
            $where .= " h_pedidos.no_cliente IN ('".$nocliente."') ";
        }


  	}

    try{
        $cadenaSql = 
        "SELECT SUM(h_pedidos.cantidad) suma
        FROM h_pedidos
        inner join marcas on marcas.no_cliente=h_pedidos.no_cliente and marcas.cve_marca=h_pedidos.marca
        INNER JOIN sh_detalle sh ON sh.id=h_pedidos.id_sh_d
        INNER JOIN sh_pedidos shp ON sh.id_solicitud = shp.id_solicitud
        INNER JOIN solicitudes s ON s.id = shp.id_solicitud" . $where ;
        $sqlCount = $conexion->prepare($cadenaSql);
        if (!$sqlCount)  throw new Exception(json_encode(["codigo" => 1, "ref" => "ERR:01", "msg" => "Error en consulta: " . $conexion->error]));
        //$sqlCount->bind_param("i", $orden_id);
        if (!$sqlCount->execute())  throw new Exception(json_encode(["codigo" => 1, "ref" => "ERR:01", "msg" => "Error en consulta: " . $conexion->error]));
        $sqlCount->store_result();
        $sqlCount->bind_result($totalH);
        $sqlCount->fetch();
        //echo $totalH . "holiiis";
        /*$sqlCount = $conexion->prepare($cadenaSql);
        if(!$sqlCount) {
            $errorsql=$conexion->error;
            throw new CrmSqlException("Error al preparar la query de conteo de los informe",$errorsql,'');
        }
        if (!$sqlCount->execute()) {
            $errorsql=$sqlCount->error; //guarda el mensaje de error en errorsql
            throw new CrmSqlException("Error al ejecutar la consulta para conteo",$errorsql,'');
        } 
        $resultSet = $sql->get_result();
        //pull all results as an associative array
        $result = $resultSet->fetch_object();
        echo $result->suma . "holiiis";
        //$totalH = $sqlCount->num_rows; // cuenta el total de registros devueltos
        $sql = $conexion->prepare($cadenaSql);
        if (!$sql) { 
            $errorsql=$conexion->error;
            throw new CrmSqlException("Error al preparar la query de consulta de informes",$errorsql,'');
        }*/
        $cadenaSql = $iniSql . $where ;
      	$sqlCount= $conexion->prepare($cadenaSql);
      	if($sqlCount){
            if (!$sqlCount->execute()) {
              $errorsql=$sqlCount->error; //guarda el mensaje de error en errorsql
              throw new CrmSqlException("Error al ejecutar la consulta para conteo",$errorsql,'');
            }
            $sqlCount->store_result();
            $totalRes = $sqlCount->num_rows;
            // cuenta el total de registros devueltos
            /*Termina conteo sin limite*/
            //echo $cadenaSql." ORDER BY hs.id_recibo desc LIMIT $limit OFFSET $offset ";
            $sql = $conexion->prepare($cadenaSql." ORDER BY h_pedidos.id_sh_d desc LIMIT $limit OFFSET $offset ");
            //echo $cadenaSql." ORDER BY hp.id_sh_d desc LIMIT $limit OFFSET $offset ";
            if ($sql) { /*si la conexion esta preparada*/
                if (!$sql->execute()) {
                    $errorsql=$sql->error; //guarda el mensaje de error en errorsql
                    throw new CrmSqlException("Error al ejecutar la consulta para resultados",$errorsql,'');

                }
                $sql->execute();
                //grab a result set
                $resultSet = $sql->get_result();
                //pull all results as an associative array
                $result = $resultSet->fetch_all(MYSQLI_ASSOC);
                foreach($result as $row) {
                    $row_array['id_row'] = $row['id_row'];
                    $row_array['no_pedido'] = $row['no_pedido'];
                    $row_array['fecha'] = $row['fecha'];
                    $row_array['no_cliente'] = $row['no_cliente'];
                    $row_array['marca'] = $row['marca'];
                    $row_array['cve_marca'] = $row['cve'];
                    $row_array['estado'] = $row['edo'];

                    $row_array['folio'] = $row['folio'];
                    $folioi = $row['no_cliente'].$row['cve'].str_pad($row['fi'], 7, "0", STR_PAD_LEFT).$row['serie'];
                    $foliof = $row['no_cliente'].$row['cve'].str_pad($row['ff'], 7, "0", STR_PAD_LEFT).$row['serie'];

                    $row_array['folioi'] = $folioi;
                    $row_array['foliof'] = $foliof;
                    $row_array['cantidad'] = $row['cantidad'];

                    $tipo = "";
                    if($row['tipo'] == 1)
                      $tipo = "MEZCAL";
                    if($row['tipo'] == 2)
                      $tipo = "MEZCAL ARTESANAL";
                    if($row['tipo'] == 3)
                      $tipo = "MEZCAL ANCESTRAL";
                    $row_array['categoria'] = $tipo;

                    $row_array['holograma'] = ($row['holograma']=='1'?'NUEVOS':'GENÉRICOS');
                    $row_array['tipo_pago'] = $row['tipo_pago'];
                    $row_array['urgente'] = ($row['urgente'] == '1'? 'URGENTE': 'NORMAL');
                    $status = "";
                    if($row['status'] == 0)
                        $status="SIN SOLICITAR";
                    elseif($row['status'] == 1)
                        $status="SOLICITADO";
                    elseif($row['status'] == 2)
                        $status="RECIBIDO";
                    elseif($row['status'] == 3)
                        $status="PROCESANDO";
                    elseif($row['status'] == 4)
                        $status="IMPRESO";
                    elseif($row['status'] == 5)
                        $status="ENTREGADO";
                    elseif($row['status'] == 6)
                        $status="EN INVENTARIO";
                    $row_array['estatus'] = $status;
                    //$row_array['firma'] = $row['firma'];
                    /*$resp = crearCarpeta($row['no_cliente']);
                    $row_array['carpetaUnica'] = $resp["CU"];*/
                    //$row_array['txte'] = $row['estatus'] . " -- " .(($row['estatus'] == 1)?"FINALIZADO":($row['estatus'] == 2)?"CANCELADO":"INICIAL");
                    $rows[]=$row_array;
                }
                //*/
                $sql->close();
                /* cerrar query */
                $json["total"]=$totalRes;
                $json["rows"]=$rows;
                $json["sql"] = $cadenaSql;
                $json["totalH"] = number_format($totalH, 0);
                //$json["otraCadena"] = $otraCadena;
                print_r(json_encode($json)); //end else sql execute
            } else
                $errorsql=$conexion->error;
            //throw new CrmSqlException("Error al preparar la query de consulta de informes",$errorsql,'');
             //end else if sqlcount

        } else { // al preparar la consulta
            $errorsql=$conexion->error;
            throw new CrmSqlException("Error al preparar la query de conteo de los informe",$errorsql,'');
        }
    } catch(CrmSqlException $e) {
      echo $e->getMessage();
      echo 0;
      $conexion->rollback();
    }finally{
      $conexion->close();
    }
}

?>

        <?php
//include('conexion.php');
include("../../common/conexion.php");
mysqli_set_charset($conexion,"utf8");


    $limit= $_GET['limit'];
    $offset= $_GET['offset'];
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $rows = array();
    $regtot = array();
    $clientesel= (!isset($_GET['clientesel'])) ? "": $_GET['clientesel'];   

    $sqlP = "select *from cextracciones";
    if ($resultado = $conexion->query($sqlP))
        $infoCampo = $resultado->fetch_fields();
    $sqlP = "select *from historial_extraccion_verificadores";
    if ($resultado = $conexion->query($sqlP))
        $infoCampo2 = $resultado->fetch_fields();

    $where = ($search != "")?" WHERE (p.id_paraje LIKE '%$search%' || p.paraje LIKE '%$search%' || p.id_cliente LIKE '%$search%') AND p.status_predio = '1' ": " WHERE p.status_predio = '1' ";
    // FECHAS
    $fechaini = $_GET['fechaini'];
    $fechafin = $_GET['fechafin'];
    if ($fechaini != "" && $fechafin == "") {
        $where .= ($where != "") ? " AND ": "";
        $where .= " (DATE(c.fecharegistro) = '$fechaini') ";
    } elseif ($fechaini != "" && $fechafin != "") {
        $where .= ($where != "") ? " AND ": "";
        $where .= " (DATE(c.fecharegistro) BETWEEN '$fechaini' AND '$fechafin') ";
    }
    // CLIENTE
    if($clientesel != "" && $clientesel != "0") {
        $where .= ($where != "") ? " AND ": ""; 
        $where .= " (p.id_cliente IN ('$clientesel')) ";
    }

    $cadenaSql = "SELECT c.*, hev.*, c.id_extraccion cid_extraccion,p.id_cliente, 
    p.paraje, pe.tapada, pe.lts_producidos, p.maguey_con_registro, p.servicio, 
    IF(pe.fecha_rendimiento = '0000-00-00', DATE(pe.periodo_destilacion_fin), DATE(pe.fecha_rendimiento) ) pe_fecha 
    FROM paraje p 
    INNER JOIN cextracciones c ON p.id_paraje = c.id_paraje
    LEFT JOIN historial_extraccion_verificadores hev ON c.id_extraccion = hev.no_guia 
    LEFT JOIN rv_produccion_entrada pe ON c.id_extraccion = pe.no_guia 
    $where AND p.status = '1' ORDER BY c.id ASC";
    //echo $cadenaSql ;
    $sqlCount= $conexion->prepare($cadenaSql);
    $sqlCount->execute(); /* ejecutar la consulta */
    $sqlCount->store_result();
    $totalRes = $sqlCount->num_rows; // cuenta el total de registros devueltos
    
    $sql = $conexion->prepare($cadenaSql." LIMIT $limit OFFSET $offset ");
    if ($sql) { /*si la conexion esta preparada*/
        $sql->execute(); /* ejecutar la consulta */
        $resultSet = $sql->get_result();
        $result = $resultSet->fetch_all(MYSQLI_ASSOC);
        foreach($result as $row) {
            foreach ($infoCampo as $valor) {
                $nameC = $valor->name;
                if($nameC != "poligono")
                    $registro[$nameC] = $row["$nameC"];
            }
            foreach ($infoCampo2 as $valor) {
                $nameC = $valor->name;
                if($nameC != "poligono")
                    $registro[$nameC] = $row["$nameC"];
            }
            $registro["tguia"] = "";
            if($row["maguey_con_registro"] == 2 && $row["servicio"] == "EXCLUSIVO") 
                $registro["tguia"] = "DOCUMENTAL EXCLUSIVA";
            elseif($row["maguey_con_registro"] == 2 && $row["servicio"] == "NORMAL") 
                $registro["tguia"] = "DOCUMENTAL NORMAL";
            else
                $registro["tguia"] = "EN SITIO";
            $registro["estado"] = ($row["no_cliente_envia"] != "") ? "USADA": "DISPONIBLE";
            $registro["paraje"] = $row["paraje"];
            $registro["id_extraccion"] = $row["cid_extraccion"];
            $registro["tapada"] = "";
            $registro["lts_producidos"] = "";
            $registro["pe_fecha"] = "";
            $registro["id_cliente"] = $row["id_cliente"];
            // VALIDAR DONDE FUE USADA LA GUÍA
            if($row["tapada"] != "") {
                $registro["tapada"] = $row["tapada"];
                $registro["lts_producidos"] = $row["lts_producidos"];
                $registro["pe_fecha"] = $row["pe_fecha"];
            } else {
                if($row["no_guia"] != "") {
                    $sqlt = $conexion->prepare("SELECT pe.tapada, pe.lts_producidos, 
                    IF(pe.fecha_rendimiento = '0000-00-00',pe.periodo_destilacion_fin, pe.fecha_rendimiento) pe_fecha
                    FROM rv_produccion_entrada pe 
                     INNER JOIN rv_produccion_ensamble pen ON pe.id_produccion_entrada = pen.id_produccion_entrada 
                     WHERE pen.no_guia = '".$row["no_guia"]."' LIMIT 1 ");
                    if ($sqlt) { /*si la conexion esta preparada*/
                        $sqlt->execute(); /* ejecutar la consulta */
                        $resultSetT = $sqlt->get_result();
                        $resultT = $resultSetT->fetch_all(MYSQLI_ASSOC);
                        foreach($resultT as $rowt) {
                            $registro["tapada"] = $rowt["tapada"];
                            $registro["lts_producidos"] = $rowt["lts_producidos"];
                            $registro["pe_fecha"] = $rowt["pe_fecha"];
                        }
                    }
                }
            }

            $registro["limit"] = $limit;
            $registro["offset"] = $offset;
            $rows[]=$registro;
        } //end while
        $sql->close();/* cerrar query */
        $json["total"]=$totalRes;
        $json["rows"]=$rows;
        //$json["registro"]=$cadenaSql;

        print_r(json_encode($json));
    } else
        print_r($conexion->error);
    $conexion->close();

?>

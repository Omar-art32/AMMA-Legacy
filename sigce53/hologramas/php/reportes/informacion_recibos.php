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
    $iniSql = "
    SELECT hs.id_salidas, hs.id_recibo, hs.no_cliente, hs.anio_rcbo, hs.no_cliente, hs.edo,
    m.marca, hs.marca cve_marca, hs.serie, hs.solicitud, hs.fecha_entr, hs.destino, hs.fi1, hs.ff1, hs.se1,
    hs.tipo, hs.anio_rcbo, hs.acuse, hs.usr 
    FROM h_salidas hs
    LEFT JOIN marcas m on m.no_cliente=hs.no_cliente and m.cve_marca=hs.marca
    ";

    $and = ""; $cadenaSql = "";

    if($search != "") {
      	$and = (($and === "")? " WHERE ": $and);
      	$where =
      	' WHERE (hs.no_cliente like "%'.$search.'%" or edo like "%'.$search.'%" or solicitud like "%'.$search.'%"
      	or destino like "%'.$search.'%" or m.marca like "%'.$search.'%" ) ' ;
    } else {
        $fechaini = $_GET['fechaini'];
        $fechafin = $_GET['fechafin'];

        if($fechaini == "" && $fechafin == "")
            $where = "";
        elseif ($fechaini != "" && $fechafin == "")
            $where = " WHERE fecha_entr = '$fechaini' ";
        else
            $where = " WHERE fecha_entr BETWEEN '$fechaini' AND '$fechafin'";

  	}
    try{
        $cadenaSql = $iniSql . $where ;
        //echo $cadenaSql;
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
        $sql = $conexion->prepare($cadenaSql." ORDER BY hs.id_salidas desc LIMIT $limit OFFSET $offset ");
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
                $row_array['id_salidas'] = $row['id_salidas'];
                $idrecibo  = 'AR'.str_pad($row["id_recibo"],4,'0',STR_PAD_LEFT).'/'.$row["anio_rcbo"];
                $row_array['id_recibo'] = $idrecibo;
                $row_array['anio'] = "20".$row['anio_rcbo'];
                $row_array['anio_rcbo'] = $row['anio_rcbo'];
                $row_array['nocliente'] = $row['no_cliente'];
                $row_array['marca'] = $row['marca'];
                $row_array['cve_marca'] = $row['cve_marca'];
                $row_array['serie'] = $row['serie'];
                $row_array['estado'] = $row['edo'];
                $row_array['solicitud'] = $row['solicitud'];
                $row_array['persona_entrega'] = $row['usr'];
                $row_array['fecha_entrega'] = $row['fecha_entr'];
                $row_array['destino'] = $row['destino'];
                $folioi = $row['no_cliente'].$row['cve_marca'].str_pad($row['fi1'], 7, "0", STR_PAD_LEFT).$row['serie'];
                $foliof = $row['no_cliente'].$row['cve_marca'].str_pad($row['ff1'], 7, "0", STR_PAD_LEFT).$row['serie'];

                $row_array['folioi'] = $folioi;
                $row_array['foliof'] = $foliof;
                $row_array['cantidad'] = $row['se1'];

                $tipo = "";
                if($row['tipo'] == 1)
                  $tipo = "MEZCAL";
                if($row['tipo'] == 2)
                  $tipo = "MEZCAL ARTESANAL";
                if($row['tipo'] == 3)
                  $tipo = "MEZCAL ANCESTRAL";
                $row_array['categoria'] = $tipo;
                $idR = dechex($row['id_salidas']^1337);
                $row_array['nombreRecibo'] = $idR;
                $row_array['acuse'] = $row['acuse'];
                $rows[]=$row_array;
            }
            //*/
            $sql->close();
            /* cerrar query */
            $json["total"]=$totalRes;
            $json["rows"]=$rows;
            $json["sql"] = $cadenaSql;
            //$json["otraCadena"] = $otraCadena;
            print_r(json_encode($json)); //end else sql execute
            } else{ //end if sql, comienza su else
                $errorsql=$conexion->error;
                //throw new CrmSqlException("Error al preparar la query de consulta de informes",$errorsql,'');
            } //end else if sqlcount
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

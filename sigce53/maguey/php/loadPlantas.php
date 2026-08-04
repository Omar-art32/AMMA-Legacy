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

    $sqlP = "select *from existenciaplanta";
    if ($resultado = $conexion->query($sqlP))
        $infoCampo = $resultado->fetch_fields();
    $where = "";
    // CUADRO DE BÚSQUEDA
    if($search != "") {
        $where = "  (p.id_paraje LIKE '%$search%' || p.paraje LIKE '%$search%' || p.lat LIKE '%$search%' || p.lng LIKE '%$search%' || p.id_cliente LIKE '%$search%' 
                || p.nombrep LIKE '%$search%' || p.rcampo LIKE '%$search%' || c.nombre LIKE '%$search%' || ep.regmaguey LIKE '%$search%' ) ";
    } 
    // FECHAS
    $fechaini = $_GET['fechaini'];
    $fechafin = $_GET['fechafin'];
    if ($fechaini != "" && $fechafin == "") {
        $where .= ($where != "") ? " AND ": "";
        $where .= " (DATE(ep.fecharegistro) = '$fechaini') ";
    } elseif ($fechaini != "" && $fechafin != "") {
        $where .= ($where != "") ? " AND ": "";
        $where .= " (DATE(ep.fecharegistro) BETWEEN '$fechaini' AND '$fechafin') ";
    }
    // CLIENTE
    if($clientesel != "" && $clientesel != "0") {
        $where .= ($where != "") ? " AND ": ""; 
        $where .= " (p.id_cliente IN ('$clientesel')) ";
    }
    $idus = (isset($_GET['idus'])) ? $_GET['idus'] : 0;
    // ---------------------------------------------------------------------------------------------------
    // ---------------------------------------------------------------------------------------------------
    $sql_conflicto = "";
    if($idus > 0 ){
        $usuario_solicita = $_GET['idus'];

        $conflicto_intereses = $conexion->prepare("SELECT getConflictoIntereses(?)");
        if (!$conflicto_intereses) 
            throw new Exception("ERROR AL CONSULTAR CONFLICTO (ERR:001)");
        $conflicto_intereses->bind_param("i", $usuario_solicita);
        if (!$conflicto_intereses->execute()) 
            throw new Exception("ERROR AL CONSULTAR CONFLICTO (ERR:002)");
        $conflicto_intereses->store_result();
        $conflicto_intereses->bind_result($clientes_conflicto);
        $conflicto_intereses->fetch();
        $conflicto_intereses->close();

        /**MODIFICAR CONSULTA DEACUERDO A LAS NECECIDADES 
         * LA VARIABLE $clientes_conflicto TRAE LOS CLIENTES EN EL SIGUIENTE FORMATO 'C9999','C9998'
        */
        /*if($usuario_solicita == 1)
            $clientes_conflicto = "'C9999','C9998','C0001','C0003','C0249'";*/

        $sql_conflicto = ($clientes_conflicto != "") ? " AND (no_cliente NOT IN ({$clientes_conflicto}) )" : "";
    }
    // ---------------------------------------------------------------------------------------------------
    // ---------------------------------------------------------------------------------------------------
    $where = ($where != "") ? " WHERE " . $where : ""; 
    $where2 = (($where == "" && $sql_conflicto == "") ? " WHERE ": " AND ") . " p.status = '1' ORDER BY ep.fecharegistro ASC";
    $cadenaSql = "SELECT ep.*, DATE(ep.fecharegistro) fecharegistro, c.nombre comun,  
    l.localidad local, m.nombre municipio, e.nombre mestado, p.paraje, p.id_cliente
    FROM existenciaplanta ep
    INNER JOIN paraje p ON ep.id_paraje = p.id_paraje 
    INNER JOIN comun c ON ep.id_comun = c.id_comun 
    INNER JOIN localidades l ON p.id_localidad = l.id
    INNER JOIN municipios m ON l.MunicipioID = m.id 
    INNER JOIN estados e ON m.estado = e.clave 
    $where  $sql_conflicto $where2 ";
    //echo $cadenaSql;
    $sqlCount= $conexion->prepare($cadenaSql);
    $sqlCount->execute(); 
    $sqlCount->store_result();
    $totalRes = $sqlCount->num_rows; 
    
    $sql = $conexion->prepare($cadenaSql." LIMIT $limit OFFSET $offset ");
    if ($sql) { 
        $sql->execute(); 
        $resultSet = $sql->get_result();
        $result = $resultSet->fetch_all(MYSQLI_ASSOC);
        foreach($result as $row) {
			
            foreach ($infoCampo as $valor) {
                $nameC = $valor->name;
                if($nameC != "poligono")
                    $registro[$nameC] = $row["$nameC"];
            }
			$registro['comun'] = $row["comun"];
            $registro['paraje'] = $row["paraje"];
            $registro['id_cliente'] = $row["id_cliente"];
            $registro['fecharegistro'] = $row["fecharegistro"];
            $registro["local"] = $row["local"];
            $registro["municipio"] = $row["municipio"];
            $registro["mestado"] = $row["mestado"];
            $registro["limit"] = $limit;
            $registro["offset"] = $offset;
            $rows[]=$registro;
        } 
        $sql->close(); 
        $json["total"]=$totalRes;
        $json["rows"]=$rows;
        //$json["registro"]=$cadenaSql;

        print_r(json_encode($json));
    } else
        print_r($conexion->error);
    $conexion->close();

?>

<?php
//include('conexion.php');
include("../../common/conexion.php");
mysqli_set_charset($conexion,"utf8");

if(isset($_POST['tipo'])) {
    $tipo = $_POST['tipo'];
    $idParaje = $_POST['idParaje'];
    $sqlP = "select *from crmreg.paraje";
    if ($resultado = $conexion->query($sqlP))
        $infoCampo = $resultado->fetch_fields();
    $cadenaSql = "SELECT p.*, l.id local, m.id municipio, m.estado mestado
	FROM paraje p
        INNER JOIN localidades l ON p.id_localidad = l.id
        INNER JOIN municipios m ON l.MunicipioID = m.id
            WHERE p.id_paraje = '" . $idParaje . "' ";
    $sql = $conexion->prepare($cadenaSql);
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
            $registro["fecha"] = date("d-m-Y", strtotime($registro["fecha"]));
            $registro["local"] = $row["local"];
            $registro["municipio"] = $row["municipio"];
            $registro["mestado"] = $row["mestado"];
        }
        $sql->close();
        $json["rows"]=$registro;
        $json["exito"]="si";
        //$json["registro"]=$cadenaSql;
        print_r(json_encode($json));
    } else
        print_r($conexion->error);
    $conexion->close();
} elseif(isset($_POST['funcion'])) {
	if($_POST['funcion'] == "agregaG") {
		$conexion->autocommit(FALSE);
		$now="NOW()";
		$idp = $_POST['idp'];
		$ga = $_POST['ga'];
		$idus = $_POST['idus'];
		$output = [];

		$consulta = "SELECT SUBSTR(id_extraccion,2,length(id_extraccion)) id FROM cextracciones 
        WHERE id = (SELECT MAX(id) FROM cextracciones WHERE SUBSTRING(id_extraccion, 1, 2) != 'GP' ) ORDER BY id DESC;";
		$consultaid = $conexion->query($consulta);
		if($consultaid==false) throw new Exception("Error al obtener id paraje");

		while ($row = $consultaid->fetch_array(MYSQLI_ASSOC)){
			if($row['id'] > 0)
				$idS = $row['id'];
			else
				$idS = 1;
		}

		for($i = 1; $i <= $ga; $i++) {
			$idG = "G".($i+$idS);
			$datoextracc="('$idG','$idp','1',$now,' ',$idus)";
			$datoextracc="insert into cextracciones(id_extraccion,id_paraje,status,fecha,constancia,id_us)values".$datoextracc;
			$resextrac=$conexion->query($datoextracc);
			if($resextrac==false) throw new Exception("Error al realizar el registro en extracciones ".$datoextracc);
		}
		$output['exito'] = "1";
		$conexion->commit();
		$conexion->close();
		echo json_encode($output);
	} elseif($_POST['funcion'] == "transferir") {
		$conexion->autocommit(FALSE);
		$now = date("Y-m-d H:i:s");
		$mnocliente = $_POST['mnocliente'];
		$mpredioa = $_POST['mpredioa'];
        $idus = $_POST['idus'];
		$sqlpredio="SELECT *
             FROM crmreg.paraje
             WHERE id_paraje IN ('".$mpredioa."') ";
		if ($resultadop = $conexion->query($sqlpredio))
			$infoCampo = $resultadop->fetch_fields();
		$sql = $conexion->prepare($sqlpredio);
		if ($sql) {
			 if (!$sql->execute()) {
				$errorsql=$sql->error; //guarda el mensaje de error en errorsql
				throw new CrmSqlException("Error al ejecutar la consulta para resultados",$errorsql,'');
			}else{
                $error = "";
                $seccion = "";
                $sqlTodos = "";
                $sqlTodoss = "";
				$sql->execute();
				$resultSet = $sql->get_result();
				$result = $resultSet->fetch_all(MYSQLI_ASSOC);
                $i = 0;
                // SACAR ÚLTIMO ID PARAJE
                $consulta="SELECT SUBSTR(id_paraje,2,length(id_paraje)) id from paraje WHERE id = (SELECT MAX(id) from paraje) ORDER BY id DESC";
                $consultaid = $conexion->query($consulta);
                if($consultaid==false) throw new Exception("Error al obtener id paraje");
                if ($consultaid->num_rows > 0){
                    $idP ="P";
                    while ($row = $consultaid->fetch_array(MYSQLI_ASSOC)){
                        if($row['id'] > 0)
                          $idP .= ($row['id']+1);
                        else
                          $idP .= 1;
                    }
                }
                // PARAJE
				foreach($result as $row) {

					$values = ""; $campos = ""; $poligono = "";
					foreach ($infoCampo as $valor) {
                        $nameC = $valor->name;
                        if($nameC != "poligono" && $nameC != "constancia_predio" && $nameC != "constancia_extracciones" && $nameC != "maguey_con_registro") {
      						$val = $row["$nameC"];
                            if($nameC == 'id_cliente') {
                                $id_cliente = $row["$nameC"];
                                $val = $mnocliente;
                            }
                            if($nameC == 'id_paraje'){
								$idParaje = $val;
                                $val = $idP;
							}
                            if($nameC == 'fecha' || $nameC == 'fecha_paraje')
                                $val = date('Y-m-d');
      						// CAMPOS
      						$campos .= ($campos != "") ? ",": "";
      						$campos .= $nameC;
      						// VALOR
      						$values .= ($values != "") ? ",": "";
      						$values .= "'$val'";
                        } elseif($nameC == "poligono") {
                            $val = $row["$nameC"];
                            $poligono = $val;
                        }
					}
					// COMPLETAR REGISTRO
                    $mcr = ($poligono != "") ? '1': '2';
					$campos .= ",id_us,numa,maguey_con_registro";
					$values .= ",'".$idus."','".$idParaje."','".$mcr."'";
                    $sqlTodos = 'INSERT INTO paraje ('.$campos.') VALUES ('.$values.');';
                    $sqlT = $conexion->prepare($sqlTodos);
                    if(!$sqlT->execute()) {
                        echo $sqlT->error;
                        $error = 1;
                        $seccion = "P";
                        break;
                    }
                    unset($infoCampo);

				}
                //$sqlSalidHEV = "";
                // EXISTENCIAPLANTA
                if($error == "") {
                    $sqlep="SELECT *
                         FROM crmreg.existenciaplanta
                         WHERE id_paraje IN ('".$mpredioa."') and existenciaplantas > 0 ";
                    if ($resultadoep = $conexion->query($sqlep))
                        $infoCampo = $resultadoep->fetch_fields();
                    $sqlepp = $conexion->prepare($sqlep);
                    $sqlepp->execute();
                    $resultep = $sqlepp->get_result();
                    $resultep = $resultep->fetch_all(MYSQLI_ASSOC);
					$np = 0;
                    foreach($resultep as $row) {
                        $values = ""; $campos = "";
                        foreach ($infoCampo as $valor) {
                            $nameC = $valor->name;
                            if($nameC != "id_plantas" && $nameC != "id_us") {
                                $val = $row["$nameC"];
                                if($nameC == 'existenciaplantas')
                                    $existencia = $row["$nameC"];
                                if($nameC == 'existenciaplantas')
                                    $existenciaplantas = $row["existenciaplantas"];
                                if($nameC == 'cantidadini')
                                    $val = $row["existenciaplantas"];
                                if($nameC == 'id_paraje')
                                    $val = $idP;
                                // CAMPOS
                                $campos .= ($campos != "") ? ",": "";
                                $campos .= $nameC;
                                // VALOR
                                $values .= ($values != "") ? ",": "";
                                $values .= "'$val'";
                            } elseif($nameC == 'id_plantas')
                                $id_plantas = $row["id_plantas"];
                        }
						$campos .= ",id_us,numa";
                        $values .= ",'".$idus."','".$id_plantas."'";
                        $sqlTodos = 'INSERT INTO existenciaplanta ('.$campos.') VALUES ('.$values.');';
						//echo $sqlTodos;
                        $sqlT = $conexion->prepare($sqlTodos);
                        if(!$sqlT->execute()) {
                            $error = 2;
                            $seccion = "P";
                            break;
                        } else {
                            // AGREGAR ARREGLO PARA GUARDAR SALIDA DE PLANTAS HEV
                            $arrPl[$np]["IDP"] = $id_plantas;
                            $arrPl[$np]["IDC"] = $id_cliente;
                            $arrPl[$np]["EXP"] = $existenciaplantas;
                            $np++;
                            // CANCELAR EXISTENCIA EN PLANTAS
                            if($error == "") {
                                $sqlTodos = 'UPDATE crmreg.existenciaplanta
                                        SET existenciaplantas = 0
                                        WHERE id_plantas = "'.$id_plantas.'" ';
                                $sqlT = $conexion->prepare($sqlTodos);
                                if(!$sqlT->execute()) {
                                    $error = 4;
                                    $seccion = "P";
                                    break;
                                }
                            }
                        }
                    }
					//GUARDANDO SALIDA DE PLANTAS EN HEV
                    foreach ($arrPl as $value) {
                        $sqlTodos = ' INSERT INTO crmreg.historial_extraccion_verificadores
                                    (no_guia,id_plantas,no_cliente_envia,no_cliente_recibe,extraccion,direccion_cliente_recibe,fecha_subio,fecha_realizo,id_verificador)
                                    VALUES (0,"'.$value["IDP"].'","'.$value["IDC"].'","'.$value["IDC"].'","'.$value["EXP"].'",
                                    "SALIDA POR TRANSFERENCIA (AMMA) PREDIO '.$idP.' POR IDUS '.$idus.'","'.$now.'","'.$now.'","1"); ';
                        $sqlT = $conexion->prepare($sqlTodos);
                        if(!$sqlT->execute()) {
                            $error = 3;
                            $seccion = "P";
                            break;
                        }
                    }
                    if(isset($arrPl))
                        unset($arrPl);
                }

                /*// GUÍAS
                if($error == "") {
                    $sqlg="SELECT *
                         FROM crmreg.cextracciones
                         WHERE id_paraje IN ('".$mpredioa."')
                        AND id_extraccion NOT IN (SELECT no_guia FROM crmreg.historial_extraccion_verificadores);  ";
                    if ($resultadog = $conexion->query($sqlg))
                        $infoCampo = $resultadog->fetch_fields();
                    $sqlgp = $conexion->prepare($sqlg);
                    $sqlgp->execute();
                    $resultg = $sqlgp->get_result();
                    $resultg = $resultg->fetch_all(MYSQLI_ASSOC);
                    foreach($resultg as $row) {
                        $values = ""; $campos = "";
                        foreach ($infoCampo as $valor) {
                            $nameC = $valor->name;
                            //if($nameC != "id_plantas") {
                            $val = $row["$nameC"];
                            if($nameC == 'fecha')
                                $val = date('Y-m-d');
                            $campos .= ($campos != "") ? ",": "";
                            $campos .= $nameC;
                            // VALOR
                            $values .= ($values != "") ? ",": "";
                            $values .= "'$val'";
                            //}
                        }
                        $sqlTodos = 'INSERT INTO cextracciones ('.$campos.') VALUES ('.$values.');';

                        $sqlT = $conexion->prepare($sqlTodos);
                        if(!$sqlT->execute()) {
                            $error = 5;
                            $seccion = "P";
                            break;
                        }
                    }
                }*/

                // CONSTANCIAS
                if($error == "") {
                    $sqlc="SELECT *
                         FROM crmreg.constancias
                         WHERE id_paraje IN ('".$mpredioa."') ";
                    if ($resultadoc = $conexion->query($sqlc))
                        $infoCampo = $resultadoc->fetch_fields();
                    $sqlcp = $conexion->prepare($sqlc);
                    $sqlcp->execute();
                    $resultc = $sqlcp->get_result();
                    $resultc = $resultc->fetch_all(MYSQLI_ASSOC);
                    foreach($resultc as $row) {
                        $values = ""; $campos = "";
                        foreach ($infoCampo as $valor) {
                            $nameC = $valor->name;
                            if($nameC != "id_constancia") {
                                $val = $row["$nameC"];
                                if($nameC == 'fecha')
                                    $val = date('Y-m-d');
                                if($nameC == 'id_paraje')
                                    $val = $idP;
                                // CAMPOS
                                $campos .= ($campos != "") ? ",": "";
                                $campos .= $nameC;
                                // VALOR
                                $values .= ($values != "") ? ",": "";
                                $values .= "'$val'";
                            }
                        }
						// COMPLETAR REGISTRO
						$campos .= ",id_us";
						$values .= ",'".$idus."'";
                        $sqlTodos = 'INSERT INTO constancias ('.$campos.') VALUES ('.$values.');';
                        $sqlT = $conexion->prepare($sqlTodos);
                        if(!$sqlT->execute()) {
                            $error = 6;
                            $seccion = "P";
                            break;
                        }
                    }
                }
                //echo $sqlTodos;
                if($error != "") {
                    /*$sqlT = $conexion->prepare($sqlTodos);
                    //$output["sql"] = $sqlTodos;
                    if (!$sqlT->execute()) {
                        $errorsql=$sqlT->error; //guarda el mensaje de error en errorsql
                        throw new CrmSqlException("Error al ejecutar la consulta para resultados",$errorsql,'');*/
                    $output['exito'] = "0";
                    $msj = "";
                    if($error == 1)
                        $msj = "Paraje";
                    elseif($error == 2)
                        $msj = "Existenciaplanta";
                    elseif($error == 3)
                        $msj = "HEV";
                    elseif($error == 4)
                        $msj = "Upd EP";
                    elseif($error == 5)
                        $msj = "Guías";
                    elseif($error == 6)
                        $msj = "Constancias";
                    $output['msj'] = $msj;
                } else {
                    $output['exito'] = "1";
                    $conexion->commit();
                    $conexion->close();
                }

			}
		}

		//$output = [];
        print_r(json_encode($output));

    } elseif($_POST['funcion'] == "transferirG") {
		$conexion->autocommit(FALSE);
		$now="NOW()";
		$idus = $_POST['idus'];
        /*$sqlclientes = "
        SELECT no_cliente, registro_crm
        from clientes
        WHERE no_cliente IN (
        'C0134','C0115',        'C0135',        'C0136',        'C0137',        'C0138',        'C0139',        'C0140',        'C1402',        'C0145',
        'C0147','C0148',        'C0149',        'C0165',        'C0080',        'C0081',        'C0082',        'C0083',        'C0084',
        'C0085','C0086',        'C0087',        'C0088',        'C0089',        'C0090',        'C0091',        'C0092',        'C0093',
        'C0094','C0095',        'C0096',        'C0097',        'C0098',        'C0099',        'C0108',        'C0109',        'C0110',
        'C0111','C0112',        'C0113',        'C0114',        'C0116',        'C0117',        'C0118',        'C0119',        'C0120',
        'C0121','C0122',        'C0123',        'C0124',        'C0125',        'C0126',        'C0127',        'C0100',        'C0101',
        'C0102','C0103',        'C0026',        'C0104',        'C0129',        'C0130',        'C0131',        'C0132',        'C0133',
        'C0107','C0106',        'C0105',        'C0173',        'C0172',        'C0171',        'C0170',        'C0174',        'C0175',
        'C0176','C0177',        'C0178',        'C0179',        'C0180',        'C0181',        'C0182' )";*/
		$sqlclientes = "
		SELECT no_cliente, registro_crm
        from clientes
        WHERE no_cliente IN
		(
			'C0155',
			'C0156',
			'C0157',
			'C0158',
			'C0159',
			'C0160',
			'C0161',
			'C0163',
			'C0164',
			'C0166',
			'C0167',
			'C0168',
			'C0169');";
		/*$sqlclientes = "SELECT no_cliente, registro_crm
        from clientes
        WHERE no_cliente IN (
        'C0134','C0115',        'C0135',        'C0136' )";*/
        $sqlcli = $conexion->prepare($sqlclientes);
        if ($sqlcli) {
            if (!$sqlcli->execute()) {
                $errorsql = $sqlcli->error; //guarda el mensaje de error en errorsql
                throw new CrmSqlException("Error al ejecutar la consulta para resultados",$errorsql,'');
            } else{
				$error = "";
                $resultSetC = $sqlcli->get_result();
                $resultcli = $resultSetC->fetch_all(MYSQLI_ASSOC);
                foreach($resultcli as $rowc) {
            		$sqlpredio="SELECT p.*
                        FROM crmreg.paraje p
                        INNER JOIN crmreg.existenciaplanta e ON p.id_paraje = e.id_paraje
                        WHERE e.existenciaplantas > 0 AND p.id_cliente IN ('".$rowc['registro_crm']."')
                        GROUP BY p.id_paraje
                         ";
                    $mnocliente = $rowc['no_cliente'];
            		if ($resultadop = $conexion->query($sqlpredio))
            			$infoCampo1 = $resultadop->fetch_fields();
            		$sql = $conexion->prepare($sqlpredio);
            		if ($sql) {
            			 if (!$sql->execute()) {
            				$errorsql=$sql->error; //guarda el mensaje de error en errorsql
            				throw new CrmSqlException("Error al ejecutar la consulta para resultados",$errorsql,'');
            			}else{
                            //echo "hola";
                            $seccion = "";
                            $sqlTodos = "";
                            $sqlTodoss = "";
            				$sql->execute();
            				$resultSet = $sql->get_result();
            				$result = $resultSet->fetch_all(MYSQLI_ASSOC);
                            $i = 0;

                            // PARAJE
            				foreach($result as $row) {
                                // SACAR ÚLTIMO ID PARAJE
                                $consulta="SELECT SUBSTR(id_paraje,2,length(id_paraje)) id from paraje WHERE id = (SELECT MAX(id) from paraje) ORDER BY id DESC";
                                $consultaid = $conexion->query($consulta);
                                if($consultaid==false) throw new Exception("Error al obtener id paraje");
                                if ($consultaid->num_rows > 0){
                                    $idP ="P";
                                    while ($rowcid = $consultaid->fetch_array(MYSQLI_ASSOC)){
                                        if($rowcid['id'] > 0)
                                          $idP .= ($rowcid['id']+1);
                                        else
                                          $idP .= 1;
                                    }
                                }
                                //
            					$values = ""; $campos = "";
            					foreach ($infoCampo1 as $valor) {
                                    $nameC = $valor->name;
                                    if($nameC != "poligono" && $nameC != "constancia_predio" && $nameC != "constancia_extracciones") {
                  						$val = $row["$nameC"];
                                        if($nameC == 'id_cliente') {
                                            $id_cliente = $row["$nameC"];
                                            $val = $mnocliente;
                                        }
                                        if($nameC == 'id_paraje'){
            								$idParaje = $val;
                                            $val = $idP;
            							}
                                        if($nameC == 'fecha' || $nameC == 'fecha_paraje')
                                            $val = date('Y-m-d');
                  						// CAMPOS
                  						$campos .= ($campos != "") ? ",": "";
                  						$campos .= $nameC;
                  						// VALOR
                  						$values .= ($values != "") ? ",": "";
                  						$values .= "'$val'";
                                    }
            					}
            					// COMPLETAR REGISTRO
            					$campos .= ",id_us,numa";
            					$values .= ",'".$idus."','".$idParaje."'";
                                $sqlTodos = 'INSERT INTO paraje ('.$campos.') VALUES ('.$values.');';
                                //echo $sqlTodos;
                                $sqlT = $conexion->prepare($sqlTodos);
                                if(!$sqlT->execute()) {
                                    //echo $sqlT->error;
                                    $error = 1;
                                    $seccion = "P";
                                    break;
                                } else {    // EXISTENCIAPLANTA
                                    unset($infoCampo);
                                    $sqlep="SELECT *
                                         FROM crmreg.existenciaplanta
                                         WHERE id_paraje IN ('".$idParaje."') and existenciaplantas > 0 ";
                                    if ($resultadoep = $conexion->query($sqlep))
                                        $infoCampo = $resultadoep->fetch_fields();
                                    $sqlepp = $conexion->prepare($sqlep);
                                    $sqlepp->execute();
                                    $resultep = $sqlepp->get_result();
                                    $resultep = $resultep->fetch_all(MYSQLI_ASSOC);
                                    foreach($resultep as $row) {
                                        $values = ""; $campos = "";
                                        foreach ($infoCampo as $valor) {
                                            $nameC = $valor->name;
                                            if($nameC != "id_plantas") {
                                                $val = $row["$nameC"];
                                                if($nameC == 'existenciaplantas')
                                                    $existencia = $row["$nameC"];
                                                if($nameC == 'existenciaplantas')
                                                    $existenciaplantas = $row["existenciaplantas"];
                                                if($nameC == 'fecha_registro')
                                                    $val = $now;
                                                if($nameC == 'id_paraje')
                                                    $val = $idP;
                                                if($nameC == 'id_us')
                                                    $val = $idus;
                                                // CAMPOS
                                                $campos .= ($campos != "") ? ",": "";
                                                $campos .= $nameC;
                                                // VALOR
                                                $values .= ($values != "") ? ",": "";
                                                $values .= "'$val'";
                                            } elseif($nameC == 'id_plantas')
                                                $id_plantas = $row["id_plantas"];
                                        }
                                        $sqlTodos = 'INSERT INTO existenciaplanta ('.$campos.') VALUES ('.$values.');';
                                        $sqlT = $conexion->prepare($sqlTodos);
                                        if(!$sqlT->execute()) {
                                            $error = 2;
                                            $seccion = "P";
                                            break;
                                        } else {
                                            $sqlTodos = ' INSERT INTO crmreg.historial_extraccion_verificadores
                                                        (no_guia,id_plantas,no_cliente_envia,no_cliente_recibe,extraccion,direccion_cliente_recibe,fecha_subio,fecha_realizo,id_verificador)
                                                        VALUES (0,"'.$id_plantas.'","'.$id_cliente.'","'.$id_cliente.'","'.$existenciaplantas.'",
                                                        "SALIDA POR TRANSFERENCIA (AMMA) PREDIO '.$idP.' POR IDUS '.$idus.'","'.$now.'","'.$now.'","1"); ';
                                            $sqlT = $conexion->prepare($sqlTodos);
                                            if(!$sqlT->execute()) {
                                                $error = 3;
                                                $seccion = "P";
                                                break;
                                            }
                                            // CANCELAR EXISTENCIA EN PLANTAS
                                            if($error == "") {
                                                $sqlTodos = 'UPDATE crmreg.existenciaplanta
                                                        SET existenciaplantas = 0
                                                        WHERE id_plantas = "'.$id_plantas.'" ';
                                                $sqlT = $conexion->prepare($sqlTodos);
                                                if(!$sqlT->execute()) {
                                                    $error = 4;
                                                    $seccion = "P";
                                                    break;
                                                }
                                            }
                                        }
                                    }  // FIN EXISTENCIAPLANTA

                                    // CONSTANCIAS
                                    unset($infoCampo);
                                    if($error == "") {
                                        $sqlc="SELECT *
                                             FROM crmreg.constancias
                                             WHERE id_paraje IN ('".$idParaje."') ";
                                        if ($resultadoc = $conexion->query($sqlc))
                                            $infoCampo = $resultadoc->fetch_fields();
                                        $sqlcp = $conexion->prepare($sqlc);
                                        $sqlcp->execute();
                                        $resultc = $sqlcp->get_result();
                                        $resultc = $resultc->fetch_all(MYSQLI_ASSOC);
                                        foreach($resultc as $row) {
                                            $values = ""; $campos = "";
                                            foreach ($infoCampo as $valor) {
                                                $nameC = $valor->name;
                                                if($nameC != "id_constancia") {
                                                    $val = $row["$nameC"];
                                                    if($nameC == 'fecha')
                                                        $val = date('Y-m-d');
                                                    if($nameC == 'id_paraje')
                                                        $val = $idP;
                                                    // CAMPOS
                                                    $campos .= ($campos != "") ? ",": "";
                                                    $campos .= $nameC;
                                                    // VALOR
                                                    $values .= ($values != "") ? ",": "";
                                                    $values .= "'$val'";
                                                }
                                            }
                                            // COMPLETAR REGISTRO
                                            $campos .= ",id_us";
                                            $values .= ",'".$idus."'";
                                            $sqlTodos = 'INSERT INTO constancias ('.$campos.') VALUES ('.$values.');';
                                            $sqlT = $conexion->prepare($sqlTodos);
                                            if(!$sqlT->execute()) {
                                                $error = 5;
                                                $seccion = "P";
                                                break;
                                            }
                                        }
                                    } // FIN CONSTANCIAS

                                    unset($infoCampo);
                                    // GUÍAS
                                    if($error == "") {
                                        $now="NOW()";
                                        $idp = $idP; // PREDIO A ASIGNAR GUÍAS
                                        $ga = 3; // GUÍAS A ASIGNAR

                                        $consultamax = "SELECT SUBSTR(id_extraccion,2,length(id_extraccion)) id FROM cextracciones WHERE id = (SELECT MAX(id) FROM cextracciones) ORDER BY id DESC;";
                                        $consultaid = $conexion->query($consultamax);
                                        if($consultaid==false) throw new Exception("Error al obtener id paraje");

                                        while ($rowgm = $consultaid->fetch_array(MYSQLI_ASSOC)){
                                            if($rowgm['id'] > 0)
                                                $idS = $rowgm['id'];
                                            else
                                                $idS = 1;
                                        }

                                        for($i = 1; $i <= $ga; $i++) {
                                            $idG = "G".($i+$idS);
                                            $datoextracc="('$idG','$idp','1',$now,' ',$idus)";
                                            $datoextracc="insert into cextracciones(id_extraccion,id_paraje,status,fecha,constancia,id_us)values".$datoextracc;
                                            $resextrac=$conexion->query($datoextracc);
                                            if($resextrac==false) {
                                                throw new Exception("Error al realizar el registro en extracciones ".$datoextracc);
                                                $error = 6;
                                                $seccion = "P";
                                                break;
                                            }
                                        }
                                    }

                                } // FIN ELSE

            				}




                        }
                    } // end foreach clientes
                }
                //echo $sqlTodos;
                if($error != "") {
                    /*$sqlT = $conexion->prepare($sqlTodos);
                    //$output["sql"] = $sqlTodos;
                    if (!$sqlT->execute()) {
                        $errorsql=$sqlT->error; //guarda el mensaje de error en errorsql
                        throw new CrmSqlException("Error al ejecutar la consulta para resultados",$errorsql,'');*/
                    $output['exito'] = "0";
                    $msj = "";
                    if($error == 1)
                        $msj = "Paraje";
                    elseif($error == 2)
                        $msj = "Existenciaplanta";
                    elseif($error == 3)
                        $msj = "HEV";
                    elseif($error == 4)
                        $msj = "Upd EP";
                    elseif($error == 6)
                        $msj = "Guías";
                    elseif($error == 5)
                        $msj = "Constancias";
                    $output['msj'] = $msj;
                } else {
                    $output['exito'] = "1";
                    //$conexion->commit();
                    $conexion->close();
                }

			}
		}

		//$output = [];
        print_r(json_encode($output));

    }
} else {
    $limit= $_GET['limit'];
    $offset= $_GET['offset'];
    $search = isset($_GET['search']) ? $_GET['search'] : '1';
    $rows = array();
    $regtot = array();
    $clientesel= (!isset($_GET['clientesel'])) ? "": $_GET['clientesel'];

    $sqlP = "select *from paraje";
    if ($resultado = $conexion->query($sqlP))
        $infoCampo = $resultado->fetch_fields();
    $where = "";
    // CUADRO DE BÚSQUEDA
    if($search != "") {
        $where = "  (id_paraje LIKE '%$search%' || paraje LIKE '%$search%' || lat LIKE '%$search%' || lng LIKE '%$search%' || id_cliente LIKE '%$search%' 
                || nombrep LIKE '%$search%' || rcampo LIKE '%$search%' || l.localidad LIKE '%$search%' || mun.nombre LIKE '%$search%' || mun.estado LIKE '%$search%') ";
    } 
    // FECHAS
    $fechaini = $_GET['fechaini'];
    $fechafin = $_GET['fechafin'];
    if ($fechaini != "" && $fechafin == "") {
        $where .= ($where != "") ? " AND ": "";
        $where .= " (DATE(p.fecharegistro) = '$fechaini') ";
    } elseif ($fechaini != "" && $fechafin != "") {
        $where .= ($where != "") ? " AND ": "";
        $where .= " (DATE(p.fecharegistro) BETWEEN '$fechaini' AND '$fechafin') ";
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
    $cadenaSql = "SELECT p.* , DATE(p.fecharegistro) p_fecharegistro,
        l.localidad nomlocalidad, mun.nombre nommunicipio, es.nombre nomestado, c.nombre nombrecli
        FROM paraje p 
        INNER JOIN clientes c ON p.id_cliente = c.no_cliente 
        LEFT JOIN localidades l ON p.id_localidad = l.id 
        LEFT JOIN municipios mun ON mun.id = l.MunicipioID
          LEFT JOIN estados es ON es.clave = mun.estado
      $where  $sql_conflicto ORDER BY p.fecharegistro ASC ";
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
            
			$cadenaSqlGO = "SELECT *
			from cextracciones c
			WHERE id_paraje = '".$row['id_paraje']."' AND id_extraccion IN (SELECT no_guia from historial_extraccion_verificadores) ";
			$sqlCountGO = $conexion->prepare($cadenaSqlGO);
			$sqlCountGO->execute(); /* ejecutar la consulta */
			$sqlCountGO->store_result();
			$totalResGO = $sqlCountGO->num_rows; // cuenta el total de registros devueltos

			$cadenaSqlG = "SELECT * from cextracciones WHERE id_paraje = '".$row['id_paraje']."' ";
			$sqlCountG= $conexion->prepare($cadenaSqlG);
			$sqlCountG->execute(); /* ejecutar la consulta */
			$sqlCountG->store_result();
			$totalResG = $sqlCountG->num_rows; // cuenta el total de registros devueltos
            foreach ($infoCampo as $valor) {
                $nameC = $valor->name;
                if($nameC != "poligono") 
                    $registro[$nameC] = $row["$nameC"];
            }
            $registro["origen"] = ($row["numa"] > 0) ? "EXTERNO": "LOCAL";
            $registro['fecharegistro'] = $row["p_fecharegistro"];
            $registro['registrom'] = $totalResG;
            $registro['nombrecli'] = $row["nombrecli"];
            $registro['localidad'] = $row["nomlocalidad"];
            $registro['municipio'] = $row["nommunicipio"];
            $registro['nomestado'] = $row["nomestado"];
            $registro['estado'] = $totalResG;
			$registro['guias'] = $totalResG;
			$registro['guiaso'] = $totalResGO;
            $registro["limit"] = $limit;
            $registro["offset"] = $offset;

            $maguey_con_registro = $row["maguey_con_registro"];
            if($maguey_con_registro == 1){
                $txtmcr = "EN SITIO";
            } elseif($maguey_con_registro == 2){
                $txtmcr = "DOCUMENTAL";
            } else {
                $txtmcr = "";
            }
            $txtmcr .= ($row["servicio"] != "") ? " ".$row["servicio"] : "";
            $registro["registro"] = $txtmcr;
          $rows[]=$registro;
        } //end while
        $sql->close();/* cerrar query */
        $json["total"]=$totalRes;
        $json["rows"]=$rows;
        $json["registro"]=$cadenaSql;

        print_r(json_encode($json));
    } else
        print_r($conexion->error);
    $conexion->close();
}
?>

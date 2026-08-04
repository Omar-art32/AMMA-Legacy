<?php
session_start();

require_once("../../aclientes/php/enviar_mail.php");


if (is_ajax()) {
	if (isset($_POST["action"]) && !empty($_POST["action"])) { 
		$action = $_POST["action"];
		switch($action) { 
			case "agregar": agregar(); break;
			case "obtenerSolicitudes": obtenerSolicitudes(); break;
			case "obtenerSolicitud": obtenerSolicitud(); break;
			case "actualizarEstado": actualizarEstado(); break;
			case "verificarRFC": verificarRFC(); break;
		}
	}
}

function is_ajax() {
	return isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest";
}

function agregar() {

	/*if (!isset($_SESSION["captcha_validado"]) or !$_SESSION["captcha_validado"]) {
		die("ERROR"); 
	}*/

	$carpetaDocumentos = "../documentos/";
	$carpetaUnica = uniqid();

	if (!mkdir($carpetaDocumentos . $carpetaUnica)) {
		die("ERROR al crear la carpeta.");
	}

	$nombre = utf8_decode(mb_strtoupper($_POST["txtNombre"], "utf-8"));
	$rfc = mb_strtoupper($_POST["txtRfc"], "utf-8");
	$repLegal = utf8_decode(mb_strtoupper($_POST["txtRepLegal"], "utf-8"));
	$calle = utf8_decode(mb_strtoupper($_POST["txtCalle"], "utf-8"));
	$noext = mb_strtoupper($_POST["txtNoExt"], "utf-8");
	$noint = mb_strtoupper($_POST["txtNoInt"], "utf-8");
	$colonia = utf8_decode(mb_strtoupper($_POST["txtColonia"], "utf-8"));
	$municipio = $_POST["txtIdMpio2"];
	$cp = $_POST["txtCp"];
	$telefono = $_POST["txtTelefono"];
	$fax = $_POST["txtFax"];
	$correo = $_POST["txtCorreo"];

	$tipoPersona = $_POST["cbxTipoPersona"];

	$fechaNac = $_POST["txtFechaNac"];
	$sexo = $_POST["cbxSexo"];
	$edoCivil = $_POST["cbxEdoCivil"];
	$escolaridad = $_POST["cbxEscolaridad"];
	$ocupacion = utf8_decode(mb_strtoupper($_POST["txtOcupacion"], "utf-8"));

	if (isset($_POST["chkDiscapacidad"]) &&  $_POST["chkDiscapacidad"]) $discapacidad = $_POST["cbxDiscapacidad"];
	else $discapacidad = 0;

	if (isset($_POST["chkLengua"]) && $_POST["chkLengua"]) $lenguaIndigena = utf8_decode(mb_strtoupper($_POST["txtLengua"]));
	else $lenguaIndigena = null;

	$hijos = intval($_POST["txtHijos"]);

	if (isset($_POST["chkOrganizacion"]) && $_POST["chkOrganizacion"]) $organizacion = utf8_decode(mb_strtoupper($_POST["txtOrganizacion"]));
	else $organizacion = null;

	$trabHombres = intval($_POST["txtHombres"]);
	$trabMujeres = intval($_POST["txtMujeres"]);
	$trabDiscapacitados = intval($_POST["txtDiscapacitados"]);


	$tipoSolicitud = 1;

	$aActividades = json_decode($_POST["aActividades"]);

	$esMagueyero = $aActividades[0] ? 1 : 0;
	$esMezcalero = $aActividades[1] ? 1 : 0;
	$esEnvasador = $aActividades[2] ? 1 : 0;
	$esComercializador = $aActividades[3] ? 1 : 0;
	$esViverista = $aActividades[4] ? 1 : 0;

	$aInstalaciones = json_decode($_POST["aInstalaciones"], true);
	$aMarcas = json_decode($_POST["aMarcas"], true);

	$activacion = hash("sha256", hash("sha256", uniqid("", true) . mt_rand()));

	try {
		include("../../aclientes/php/conexion.php");

		$conexion->autocommit(FALSE);

		$sql = "INSERT INTO prospectos(nombre, rfc, calle, noexterior, nointerior, colonia, municipio, cp, telefono, fax, correo, rep_legal, " .
				"tipo_persona, magueyero, mezcalero, envasador, comercializador, viverista, activacion, organizacion, trab_hombres, trab_mujeres, trab_discapacitados) " .
				"VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		$ps = $conexion->prepare($sql);			
		$ps->bind_param("ssssssisssssiiiiiissiii", $nombre, $rfc, $calle, $noext, $noint, $colonia, $municipio, $cp, $telefono, $fax, $correo, $repLegal, 
						$tipoPersona, $esMagueyero, $esMezcalero, $esEnvasador, $esComercializador, $esViverista, $activacion, $organizacion, $trabHombres, $trabMujeres, $trabDiscapacitados);

		if (!$ps->execute()) throw new Exception("Error al agregar el registro en la tabla de prospectos.");
		$ps->close();

		$idProspecto = $conexion->insert_id;

		if ($tipoPersona == 1) {
			$aFecha = explode("/", $fechaNac);
			$dia = $aFecha[0];
			$mes = $aFecha[1];
			$anio = $aFecha[2];
			$fechaNac = "$anio-$mes-$dia"; 

			$sql = "INSERT INTO datos_pf(fecha_nac, sexo, edo_civil, escolaridad, ocupacion, discapacidad, lengua_indigena, numero_hijos) VALUES(?, ?, ?, ?, ?, ?, ?, ?)";
			$ps = $conexion->prepare($sql);
			$ps->bind_param("siiisisi", $fechaNac, $sexo, $edoCivil, $escolaridad, $ocupacion, $discapacidad, $lenguaIndigena, $hijos);

			if (!$ps->execute()) throw new Exception("Error al agregar el registro en la tabla de datos_pf.");
			$ps->close();

			$idDatosPF = $conexion->insert_id;

			$sql = "INSERT INTO prospectos_datos_pf(prospecto, datos_pf) VALUES(?, ?)";
			$ps = $conexion->prepare($sql);
			$ps->bind_param("ii", $idProspecto, $idDatosPF);
			if (!$ps->execute()) throw new Exception("Error al agregar el registro en la tabla de prospectos_datos_pf.");

			$ps->close();
		}


		$fecha = new DateTime("now");
		$estado = 1; //99; Cuando tenga que verificar el mail
		$sql = "INSERT INTO solicitudes(estado, fecha, tipo) VALUES(?, ?, ?)";
		$ps = $conexion->prepare($sql);
		$ps->bind_param("isi", $estado, $fecha->format("Y-m-d"), $tipoSolicitud);

		if (!$ps->execute()) throw new Exception("Error al agregar el registro en la tabla de solicitudes");
		
		$ps->close();

		$idSolicitud = $conexion->insert_id;

		$sql = "INSERT INTO prospectos_solicitudes(prospecto, solicitud) VALUES(?, ?)";
		$ps = $conexion->prepare($sql);
		$ps->bind_param("ii", $idProspecto, $idSolicitud);

		if (!$ps->execute()) throw new Exception("Error al agregar el registro en la tabla prospectos_solicitudes");
		
		$ps->close();

		$sql = "INSERT INTO instalaciones(tipo, alias, calle, noexterior, nointerior, colonia, municipio, cp, referencia, telefono, fax, correo, responsable, numero, latitud, longitud, es_propiedad, es_notariado) " .
				"VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		$ps = $conexion->prepare($sql);

		$sql = "INSERT INTO prospectos_instalaciones(prospecto, instalacion) VALUES(?, ?)";
		$ps2 = $conexion->prepare($sql);

		foreach ($aInstalaciones as $instalacion) {
			$tipoInst = intval($instalacion["tipo"]);
			$alias = utf8_decode(mb_strtoupper($instalacion["alias"], "utf-8"));
			$calleInst = utf8_decode(mb_strtoupper($instalacion["calle"], "utf-8"));
			$noextInst = mb_strtoupper($instalacion["noExt"], "utf-8");
			$nointInst = mb_strtoupper($instalacion["noInt"], "utf-8");
			$coloniaInst = utf8_decode(mb_strtoupper($instalacion["colonia"], "utf-8"));
			$municipioInst = intval($instalacion["municipio"]);
			$cpInst = $instalacion["cp"];
			$referencia = utf8_decode(mb_strtoupper($instalacion["referencia"]));
			$telefonoInst = $instalacion["telefono"];
			$faxInst = $instalacion["fax"];
			$correoInst = $instalacion["correo"];
			$respInst = utf8_decode(mb_strtoupper($instalacion["responsable"], "utf-8"));

			$numero = intval($instalacion["numero"]);

			$latitud = floatval($instalacion["latitud"]);
			$longitud = floatval($instalacion["longitud"]);

			$esPropiedad = intval($instalacion["esPropiedad"]);
			$esNotariado = intval($instalacion["esNotariado"]);

			$ps->bind_param("isssssissssssiddii", $tipoInst, $alias, $calleInst, $noextInst, $nointInst, $coloniaInst, $municipioInst, $cpInst, $referencia, $telefonoInst, $faxInst, $correoInst, 
							$respInst, $numero, $latitud, $longitud, $esPropiedad, $esNotariado);
			if (!$ps->execute()) throw new Exception("Error al agregar el registro en la tabla instalaciones");

			$idInstalacion = $conexion->insert_id;
			$ps2->bind_param("ii", $idProspecto, $idInstalacion);
			if (!$ps2->execute()) throw new Exception("Error al agregar el registro en la tabla prospectos_instalaciones");

			if (!guardarDocumentos($instalacion["id"], $idInstalacion, null, $idProspecto, $conexion, $carpetaUnica)) throw new Exception("Error al guardar los documentos de las instalaciones");

		}
		$ps->close();

		$sql = "INSERT INTO marcas_por_agregar(prospecto, cve_marca, marca, tipo, registro) VALUES(?, ?, ?, ?, ?)";
		$ps = $conexion->prepare($sql);

		foreach ($aMarcas as $marca) {
			$claveMarca =$marca["clave"];
			$nombreMarca = utf8_decode(mb_strtoupper($marca["nombre"], "utf-8"));
			$tipoMarca = $marca["tipo"];
			$registroMarca = utf8_decode(mb_strtoupper($marca["registro"], "utf-8"));

			$ps->bind_param("issis", $idProspecto, $claveMarca, $nombreMarca, $tipoMarca, $registroMarca);

			if (!$ps->execute()) throw new Exception("Error al agregar el registro en la tabla marcas");

			if (!guardarDocumentos($marca["id"], null, $claveMarca, $idProspecto, $conexion, $carpetaUnica)) throw new Exception("Error al guardar los documentos de las marcas");			
		}

		$ps->close();
		$ps2->close();

		if (!guardarDocumentos(null, null, null, $idProspecto, $conexion, $carpetaUnica)) throw new Exception("Error al guardar los documentos generales");

		$conexion->commit();

		$_SESSION["idProspecto"] = $idProspecto;

		$conexion->close();

		echo json_encode(array("status" => "correcto", "msj" => "Solicitud agregada exitosamente."));

		//enviarMail($correo, "Confirme sus solicitud para ser asociado del CRM.", generarMensajeActivacion($idSolicitud, $activacion));
	}
	catch (Exception $e) {
		$conexion->rollback();
		echo json_encode(array("status" => "error", "msj" => "Error en la base de datos: " . $e->getMessage()));
		$conexion->close();
	}
}

function obtenerSolicitudes() {
	try {
		$solicitudes = array();
		$sql = "SELECT s.id, s.numero, s.anio, p.nombre AS cliente, s.estado, s.fecha, s.tipo, 'PROSPECTO' AS codigo_cliente FROM solicitudes s " .
				"INNER JOIN prospectos_solicitudes ps ON s.id = ps.solicitud INNER JOIN prospectos p on ps.prospecto = p.id WHERE s.estado <> 99 " .
				"UNION ALL SELECT s.id, s.numero, s.anio, c.nombre AS cliente, s.estado, s.fecha, s.tipo, c.no_cliente AS codigo_cliente FROM solicitudes s " .
				"INNER JOIN clientes_solicitudes cs ON s.id = cs.solicitud INNER JOIN clientes c on cs.cliente = c.no_cliente ORDER BY 6 DESC";

		include("../../aclientes/php/conexion.php");

		if ($ps = $conexion->prepare($sql)) {
			$ps->execute();

			$result = $ps->get_result();

			if ($result->num_rows > 0) {
				while($row=$result->fetch_assoc()) {
					$fecha = new DateTime($row["fecha"]);
					array_push($solicitudes, array("id" => $row["id"], "numero" => $row["numero"], "anio" => $row["anio"], "cliente" => utf8_encode($row["cliente"]), 
					"estado" => $row["estado"], "fecha" => $fecha->format("d/m/Y"), "tipo" => $row["tipo"], "no_cliente" => $row["codigo_cliente"]));
				}
			}

			$conexion->close();

			echo json_encode(array("status" => "correcto", "msj" => "Solicitudes recuperadas existosamente.", "solicitudes" => $solicitudes));
		}
		else {
			echo json_encode(array("status" => "error", "msj" => "Error al preparar la setencia SQL.")); 
		}
	}
	catch (mysqli_sql_exception $e) {
		echo json_encode(array("status" => "error", "msj" => "Error en la base de datos: " . $e->getMessage()));
		$conexion->close();
	}
}

function obtenerSolicitud() {
	$idSolicitud = $_POST["idSolicitud"];

	try {
		$sql = "SELECT COUNT(*) AS es_prospecto FROM prospectos_solicitudes WHERE solicitud = ?";

		include("../../aclientes/php/conexion.php");

		if ($ps = $conexion->prepare($sql)) {
			$ps->bind_param("i", $idSolicitud);
			$ps->execute();
			$result = $ps->get_result();

			$tipoSolicitante = 2;
			if ($row = $result->fetch_assoc()) {
				if ($row["es_prospecto"] > 0) $tipoSolicitante = 1;
			}

			$ps->close();

			if ($tipoSolicitante == 1) {
				$sql = "SELECT p.*, 'PROSPECTO' AS codigo_cliente, e.nombre AS nom_estado, m.nombre AS nom_municipio, s.observaciones, s.numero, s.anio " .
						"FROM prospectos p " .
						"INNER JOIN municipios m ON p.municipio = m.id INNER JOIN estados e ON m.estado = e.clave " .
						"INNER JOIN prospectos_solicitudes ps ON p.id = ps.prospecto INNER JOIN solicitudes s ON ps.solicitud = s.id " .
						"WHERE ps.solicitud = ?";
			}
			else {
				$sql = "SELECT c.*, c.no_cliente AS codigo_cliente, e.nombre AS nom_estado, m.nombre AS nom_municipio, s.observaciones, s.numero, s.anio " .
						"FROM clientes c " .
						"INNER JOIN municipios m ON c.municipio = m.id INNER JOIN estados e ON m.estado = e.clave " .
						"INNER JOIN clientes_solicitudes cs ON c.no_cliente = cs.cliente INNER JOIN solicitudes s ON cs.solicitud = s.id " . 
						"WHERE cs.solicitud = ?";
			}

			$ps = $conexion->prepare($sql);
			$ps->bind_param("i", $idSolicitud);
			$ps->execute();

			$result = $ps->get_result();

			if ($row = $result->fetch_assoc()) {
				$domicilio = $row["calle"] . " " . $row["noexterior"];

				if (strlen($row["nointerior"]) > 0) $domicilio .= "-" . $row["nointerior"];
				if (strlen($row["colonia"]) > 0) $domicilio .= ", " . $row["colonia"];

				$domicilio .= ", " . $row["nom_municipio"] . ", " . $row["nom_estado"] . ", C.P.: " . $row["cp"];

				if ($tipoSolicitante == 1) {
					$sql = "SELECT i.*, t.descripcion, m.nombre as municipio, e.nombre as estado FROM instalaciones i INNER JOIN prospectos_instalaciones pi ON i.id = pi.instalacion " .
							"INNER JOIN tipos_instalaciones t on i.tipo = t.id INNER JOIN prospectos_solicitudes ps ON pi.prospecto = ps.prospecto " .
							"INNER JOIN municipios m ON i.municipio = m.id INNER JOIN estados e ON m.estado = e.clave " .
							"WHERE ps.solicitud = ? ORDER BY i.id";
				}
				else {
					$sql = "SELECT i.*, t.descripcion, m.nombre as municipio, e.nombre as estado FROM instalaciones i INNER JOIN clientes_instalaciones ci ON i.id = ci.instalacion " .
							"INNER JOIN tipos_instalaciones t on i.tipo = t.id INNER JOIN clientes_solicitudes cs ON ci.cliente = cs.cliente " .
							"INNER JOIN municipios m ON i.municipio = m.id INNER JOIN estados e ON m.estado = e.clave " .
							"WHERE cs.solicitud = ? ORDER BY i.id";
				}
				$ps2 = $conexion->prepare($sql);
				$ps2->bind_param("i", $idSolicitud);
				$ps2->execute();

				$result2 = $ps2->get_result();

				$instalaciones = array();

				while ($row2 = $result2->fetch_assoc()) {
					$domicilioInst = $row2["calle"] . " " . $row2["noexterior"];
					if (strlen($row2["nointerior"]) > 0) $domicilioInst .= "-" . $row2["nointerior"];
					/*if (strlen($row2["colonia"]) > 0) $domicilioInst .= ", " . $row2["colonia"];
					$domicilioInst .= ", " . $row2["municipio"] . ", " . $row2["estado"] . ", C.P.: " . $row2["cp"];
					if (strlen($row2["referencia"]) > 0) $domicilioInst .= ", " . $row2["referencia"];*/

					array_push($instalaciones, array("id" => $row2["id"], "tipo" => $row2["descripcion"], "alias" => utf8_encode($row2["alias"]), "numero" => $row2["numero"], 
								"domicilio" => utf8_encode($domicilioInst), "colonia" => utf8_encode($row2["colonia"]), "municipio" => utf8_encode($row2["municipio"]),
								"estado" => utf8_encode($row2["estado"]), "cp" => $row2["cp"], "referencia" => utf8_encode($row2["referencia"]), "telefono" => $row2["telefono"], 
								"fax" => $row2["fax"], "correo" => $row2["correo"], "responsable" => utf8_encode($row2["responsable"])));
				}

				$ps2->close();


				if ($tipoSolicitante == 1) {
					$sql = "SELECT m.* FROM marcas_por_agregar m INNER JOIN prospectos_solicitudes ps ON m.prospecto = ps.prospecto " .
							"WHERE ps.solicitud = ? ORDER BY m.cve_marca";
				}
				else {
					$sql = "SELECT m.* FROM marcas m INNER JOIN clientes_solicitudes cs ON m.no_cliente = cs.cliente " .
							"WHERE cs.solicitud = ? ORDER BY m.cve_marca";
				}
				$ps2 = $conexion->prepare($sql);
				$ps2->bind_param("i", $idSolicitud);
				$ps2->execute();

				$result2 = $ps2->get_result();

				$marcas = array();

				while ($row2 = $result2->fetch_assoc()) {
					array_push($marcas, array("clave" => $row2["cve_marca"], "nombre" => utf8_encode($row2["marca"]), "tipo" => $row2["tipo"], "registro" => utf8_encode($row2["registro"])));
				}

				$ps2->close();


				if ($tipoSolicitante == 1) {
					$sql = "SELECT id, tipo, instalacion, marca FROM documentos d INNER JOIN prospectos_documentos pd ON d.id = pd.documento INNER JOIN prospectos_solicitudes ps ON pd.prospecto = ps.prospecto " . 
							"WHERE ps.solicitud = ? ORDER BY marca, instalacion, id";
				}
				else {
					$sql = "SELECT id, tipo, instalacion. marca FROM documentos d INNER JOIN clientes_documentos pd ON d.id = pd.documento INNER JOIN clientes_solicitudes cs ON pd.cliente = cs.cliente " . 
							"WHERE cs.solicitud = ? ORDER BY marca, instalacion, id";					
				}
				$ps2 = $conexion->prepare($sql);
				$ps2->bind_param("i", $idSolicitud);
				$ps2->execute();

				$result2 = $ps2->get_result();

				$documentos = array();

				while ($row2 = $result2->fetch_assoc()) {
					array_push($documentos, array("id" => dechex($row2["id"]^1337), "tipo" => $row2["tipo"], "instalacion" => $row2["instalacion"], "marca" => $row2["marca"]));
				}

				$ps2->close();

				$actividades = array();
				if ($row["magueyero"] == 1) array_push($actividades, "A");
				if ($row["mezcalero"] == 1) array_push($actividades, "M");
				if ($row["envasador"] == 1) array_push($actividades, "E");
				if ($row["comercializador"] == 1) array_push($actividades, "C");
				if ($row["viverista"] == 1) array_push($actividades, "V");

				echo json_encode(array("status" => "correcto", "msj" => "Solicitud recuperada exitosamente.", "numero" => $row["numero"], "anio" => $row["anio"], 
					"nombre" => utf8_encode($row["nombre"]), "no_cliente" => $row["codigo_cliente"], "persona" => $row["tipo_persona"], "rfc" => $row["rfc"], 
					"representante" => $row["rep_legal"], "domicilio" => utf8_encode($domicilio), "telefono" => $row["telefono"], "fax" => $row["fax"], 
					"correo" => $row["correo"], "actividades" => $actividades, "observaciones" => utf8_encode($row["observaciones"]), "documentos" => $documentos, 
					"instalaciones" => $instalaciones, "marcas" => $marcas));
				$ps->close();
			}
			else {
				echo json_encode(array("status" => "error", "msj" => "No se encontro el detalle de la solicitud.")); 
			}

			$conexion->close();
		}
		else {
			echo json_encode(array("status" => "error", "msj" => "Error al preparar la setencia SQL.")); 
		}
	}
	catch (mysqli_sql_exception $e) {
		echo json_encode(array("status" => "error", "msj" => "Error en la base de datos: " . $e->getMessage()));
		$conexion->close();
	}
}

function actualizarEstado() {
	$idSolicitud = $_POST["idSolicitud"];
	$nuevoEstado = $_POST["nuevoEstado"];
	//$numSolicitud = $_POST["txtNumSolicitud"];
	$numSocio = $_POST["txtNumSocio"];
	$observaciones = $_POST["txtObs"];

	$anioActual = intval(date("Y"));

	try {

		include("../../aclientes/php/conexion.php");
		$conexion->autocommit(FALSE);

		switch ($nuevoEstado) {
			case 0:
			case 2:
			case 5:
				$sql = "UPDATE solicitudes SET estado = ?, observaciones = ? WHERE id = ?";
				$ps = $conexion->prepare($sql);
				$ps->bind_param("isi", $nuevoEstado, $observaciones, $idSolicitud);
				break;
			case 1;
				$sql = "UPDATE solicitudes SET estado = 1, numero = null, observaciones = ? WHERE id = ?";
				$ps = $conexion->prepare($sql);
				$ps->bind_param("si", $observaciones, $idSolicitud);
				break;
			case 3;
				$numSolicitud = recuperarNumSolicitud($conexion, $anioActual);
				$sql = "UPDATE solicitudes SET estado = 3, numero = ?, anio = ?, observaciones = ? WHERE id = ?";
				$ps = $conexion->prepare($sql);
				$ps->bind_param("iisi", $numSolicitud, $anioActual, $observaciones, $idSolicitud);
				break;
			case 4:
				convertirProspectoEnAsociado($idSolicitud, $numSocio, $conexion);
				$sql = "UPDATE solicitudes SET estado = 4, observaciones = ? WHERE id = ?";
				$ps = $conexion->prepare($sql);
				$ps->bind_param("si", $observaciones, $idSolicitud);
				break;				

		}
		if (!$ps->execute()) throw new Exception("Error al actualizar el estado de la solicitud.");
		$ps->close();

		$conexion->commit();

		$conexion->close();

		//sleep(1);

		echo json_encode(array("status" => "correcto", "msj" => "Estado de la solicitud actualizado exitosamente."));

		//enviarMail("ponchoware@hotmail.com", "El estado de su solicitud ha sido modificado.", generarMensajeCambioEstado($nuevoEstado));
	}
	catch (Exception $ex) {
		$conexion->rollback();
		echo json_encode(array("status" => "error", "msj" => "Error en la base de datos: " . $ex->getMessage()));
		$conexion->close();
	}
}

function convertirProspectoEnAsociado($idSolicitud, $numSocio, $conexion) {

	$sql = "SELECT prospecto FROM prospectos_solicitudes WHERE solicitud = ?";
	$ps = $conexion->prepare($sql);
	$ps->bind_param("i", $idSolicitud);
	if (!$ps->execute()) throw new Exception("Erroral recuperar el registro de la tabla de prospectos");
	
	$result = $ps->get_result();

	if ($row = $result->fetch_assoc()) {
		$idProspecto = $row["prospecto"];

		$ps->close();

		$sql = "INSERT INTO clientes(no_cliente, asociado, registro, nombre, rfc, calle, noexterior, nointerior, colonia, municipio, magueyero, mezcalero, envasador, comercializador, viverista, " .
				"cp, telefono, fax, correo, rep_legal, tipo_persona, organizacion, trab_hombres, trab_mujeres, trab_discapacitados) " .
				"SELECT ?, 1, CURRENT_DATE, nombre, rfc, calle, noexterior, nointerior, colonia, municipio, magueyero, mezcalero, envasador, comercializador, viverista, " .
				"cp, telefono, fax, correo, rep_legal, tipo_persona, organizacion, trab_hombres, trab_mujeres, trab_discapacitados FROM prospectos WHERE id = ?";
		$ps = $conexion->prepare($sql);
		$ps->bind_param("si", $numSocio, $idProspecto);
		
		if (!$ps->execute()) throw new Exception("Error al agregar el registro en la tabla de clientes.");
		

		$ps->close();

		$sql = "INSERT INTO clientes_solicitudes(cliente, solicitud) VALUES(?, ?)";
		$ps = $conexion->prepare($sql);
		$ps->bind_param("si", $numSocio, $idSolicitud);
		if (!$ps->execute()) throw new Exception("Error al agregar la solicitud");
		

		$ps->close();		

		$sql = "INSERT INTO clientes_documentos(cliente, documento) SELECT ?, documento FROM prospectos_documentos WHERE prospecto = ?";
		$ps = $conexion->prepare($sql);
		$ps->bind_param("si", $numSocio, $idProspecto);
		if (!$ps->execute()) throw new Exception("Error al agregar los documentos");
		

		$ps->close();

		$sql = "INSERT INTO clientes_instalaciones(cliente, instalacion) SELECT ?, instalacion FROM prospectos_instalaciones WHERE prospecto = ?";
		$ps = $conexion->prepare($sql);
		$ps->bind_param("si", $numSocio, $idProspecto);
		if (!$ps->execute()) throw new Exception("Error al agregar la instalacion");
		

		$ps->close();

		$sql = "INSERT INTO clientes_datos_pf(cliente, datos_pf) SELECT ?, datos_pf FROM prospectos_datos_pf WHERE prospecto = ?";
		$ps = $conexion->prepare($sql);
		$ps->bind_param("si", $numSocio, $idProspecto);
		if (!$ps->execute()) throw new Exception("Error al agregar la instalacion");

		$ps->close();

		$sql = "DELETE FROM prospectos WHERE id = ?";
		$ps = $conexion->prepare($sql);
		$ps->bind_param("i", $idProspecto);
		if (!$ps->execute()) throw new Exception("Error al eliminar el registro de la tabla de prospectos");
		

		$ps->close();

		return true;
	}
	else {
		throw new Exception("No se ha encontrado el registro en la tabla de prospectos.");
	}
}

function guardarDocumentos($idFila, $idInstalacion, $idMarca, $idProspecto, $conexion, $carpetaUnica) {
	$error = 0;
	foreach ($_FILES as $nombre => $archivo) {
		if ($idFila == null || substr($nombre, -strlen($idFila)) === $idFila) {

			if ($idFila != null && strpos($nombre, "-") !== false) {
				$aPartes = explode("-", $nombre);
				$tipoArch = $aPartes[0];
			}
			else {
				$tipoArch = $nombre;
			}

			switch ($tipoArch) {
				case "fileRFC":
					$error = agregarArchivo($idProspecto, $_FILES[$nombre], 1, null, null, $carpetaUnica, $conexion);
					break;
				case "fileActaConst":
					$error = agregarArchivo($idProspecto, $_FILES[$nombre], 2, null, null, $carpetaUnica, $conexion);
					break;
				case "fileIFE":
					$error = agregarArchivo($idProspecto, $_FILES[$nombre], 3, null, null, $carpetaUnica, $conexion);
					break;
				case "fileIFERep":
					$error = agregarArchivo($idProspecto, $_FILES[$nombre], 4, null, null, $carpetaUnica, $conexion);
					break;
				case "fileCompDomFiscal":
					$error = agregarArchivo($idProspecto, $_FILES[$nombre], 5, null, null, $carpetaUnica, $conexion);
					break;
				case "fileDesignaResp":
					$error = agregarArchivo($idProspecto, $_FILES[$nombre], 6, null, null, $carpetaUnica, $conexion);
					break;
				case "filePlano":
					$error = agregarArchivo($idProspecto, $_FILES[$nombre], 7, $idInstalacion, null, $carpetaUnica, $conexion);
					break;
				case "fileIfeResponsable":
					$error = agregarArchivo($idProspecto, $_FILES[$nombre], 8, $idInstalacion, null, $carpetaUnica, $conexion);
					break;
				case "filePosesion":
					$error = agregarArchivo($idProspecto, $_FILES[$nombre], 9, $idInstalacion, null, $carpetaUnica, $conexion);
					break;
				case "fileContrato":
					$error = agregarArchivo($idProspecto, $_FILES[$nombre], 10, $idInstalacion, null, $carpetaUnica, $conexion);
					break;
				case "fileIfeArrendador":
					$error = agregarArchivo($idProspecto, $_FILES[$nombre], 11, $idInstalacion, null, $carpetaUnica, $conexion);
					break;
				case "fileIfeArrendatario":
					$error = agregarArchivo($idProspecto, $_FILES[$nombre], 12, $idInstalacion, null, $carpetaUnica, $conexion);
					break;
				case "fileEtiqueta":
					$error = agregarArchivo($idProspecto, $_FILES[$nombre], 13, null, $idMarca, $carpetaUnica, $conexion);
					break;
				case "fileTitulo":
					$error = agregarArchivo($idProspecto, $_FILES[$nombre], 14, null, $idMarca, $carpetaUnica, $conexion);
					break;
				case "fileLicencia":
					$error = agregarArchivo($idProspecto, $_FILES[$nombre], 15, null, $idMarca, $carpetaUnica, $conexion);
					break;
				case "fileEnTramite":
					$error = agregarArchivo($idProspecto, $_FILES[$nombre], 16, null, $idMarca, $carpetaUnica, $conexion);
					break;
				case "fileCartaResponsiva":
					$error = agregarArchivo($idProspecto, $_FILES[$nombre], 17, null, $idMarca, $carpetaUnica, $conexion);
					break;
				case "fileCorresp":
					$error = agregarArchivo($idProspecto, $_FILES[$nombre], 18, null, $idMarca, $carpetaUnica, $conexion);
					break;
				case "fileMaquila":
					$error = agregarArchivo($idProspecto, $_FILES[$nombre], 19, null, $idMarca, $carpetaUnica, $conexion);
					break;
			}
		}

		if ($error > 0) return false;
	}

	return true;
}

function agregarArchivo($idProspecto, $archivo, $tipoDoc, $idInstalacion, $idMarca, $carpetaUnica, $conexion) {
	$carpetaDocumentos = "../documentos/";
	$nombreArchivo = $carpetaUnica . "/" . uniqid() . ".pdf";

	if ($archivo["size"] > 104448000) {
		return 1; //echo "ERROR el tamaño del archivo es mayor al maximo permitido.";
	}

	else if (move_uploaded_file($archivo["tmp_name"], $carpetaDocumentos . $nombreArchivo)) {

		try {
			$sql = "INSERT INTO documentos(nombre_original, tipo, nombre_archivo, instalacion, marca) VALUES(?, ?, ?, ?, ?)";

			if ($ps = $conexion->prepare($sql)) {
				$ps->bind_param("sisis", $archivo["name"], $tipoDoc, $nombreArchivo, $idInstalacion, $idMarca);
				if (!$ps->execute()) throw new Exception("Error al agregar el registro en la tabla documentos");
				
				$ps->close();

				$idDocumento = $conexion->insert_id;

				$sql = "INSERT INTO prospectos_documentos(prospecto, documento) VALUES(?, ?)";
				$ps = $conexion->prepare($sql);
				$ps->bind_param("ii", $idProspecto, $idDocumento);

				if (!$ps->execute()) throw new Exception("Error al agregar el registro en la tabla prospectos_documentos");				

				$ps->close();

				return 0;
			}
			else {
				return 2; //echo "ERROR al prepara la sentencia SQL"; 
			}
		}
		catch (Exception $e) {
			return 3; //echo "ERROR $e->getMessage()";
		}
	} 
	else {
		return 4;//echo "ERROR al mover el archivo";
	}	
}

function recuperarNumSolicitud($conexion, $anioActual) {
	$numSolicitud = 1;
	$sql = "SELECT MAX(numero) AS max_solicitud FROM solicitudes WHERE anio = ?";
	$ps = $conexion->prepare($sql);
	$ps->bind_param("i", $anioActual);
	if (!$ps->execute()) throw new Exception("Error al recuperar numero de Solicitud");
	
	$result = $ps->get_result();

	if ($row = $result->fetch_assoc()) {
		if ($row["max_solicitud"] != null) $numSolicitud = $row["max_solicitud"] + 1;
	}

	$ps->close();

	return $numSolicitud;
}

function generarMensajeActivacion($idSolicitud, $activacion) {
	include("../../aclientes/php/url_raiz.php");
	$urlFinal = $urlRaiz . "verificar_correo.html?id=" . $idSolicitud . "&activacion=" . $activacion;
	$mensaje = "<html>Este mensaje ha sido enviado a esta cuenta derivado de una solicitud para ser asociado del Consejo Regulador del Mezcal con este correo electrónico.<br><br>" .
			"Si usted ha recibido este correo sin haberlo solicitado, por favor bórrelo e ignórelo.<br><br>" .
			"Si usted desea continuar con la solicitud, de clic a la dirección que aparece a continuación:<br><br>" . 
			"<a href=\"" . $urlFinal . "\">" . $urlFinal . "</a></html>";

	return $mensaje;
}

function generarMensajeCambioEstado($nuevoEstado) {
	$mensaje = "<html>Le informamos que el estado de su solicitud ha cambiado a: ";

	switch ($nuevoEstado) {
		case 0: $mensaje .= "RECHAZADA"; break;
		case 1: $mensaje .= "EN REVISION"; break;
		case 2: $mensaje .= "PENDIENTE DE PAGO"; break;
		case 3: $mensaje .= "EN AUTORIZACION"; break;
		case 4: $mensaje .= "EN EJECUCION"; break;
		case 5: $mensaje .= "TERMINADA"; break;
	}
	return $mensaje;
}

function verificarRFC() {
	$rfc = $_POST["rfc"];

	$existeProspecto = false;
	$existeAsociado = false;

	try {
		$sql = "SELECT COUNT(*) AS existe FROM prospectos WHERE rfc = ?";

		include("../../aclientes/php/conexion.php");

		$ps = $conexion->prepare($sql);
		$ps->bind_param("s", $rfc);
		$ps->execute();

		$result = $ps->get_result();

		if ($row=$result->fetch_assoc()) {
			$existeProspecto = $row["existe"] > 0;
		}

		$ps->close();

		if (!$existeProspecto) {
			$sql = "SELECT COUNT(*) AS existe FROM clientes WHERE rfc = ?";

			$ps = $conexion->prepare($sql);
			$ps->bind_param("s", $rfc);
			$ps->execute();

			$result = $ps->get_result();

			if ($row=$result->fetch_assoc()) {
				$existeAsociado = $row["existe"] > 0;
			}

			$ps->close();
		}

		$conexion->close();

		if ($existeProspecto) {
			echo json_encode(array("status" => "error", "msj" => "Ya existe una solicitud de asociado con el mismo RFC."));
		}
		else if ($existeAsociado) {
			echo json_encode(array("status" => "error", "msj" => "Ya existe un asociado con el mismo RFC.", "existeRFC"));	
		}
		else {
			echo json_encode(array("status" => "correcto", "msj" => "El RFC no ha sido registrado anteriormente."));		
		}
	}
	catch (mysqli_sql_exception $e) {
		echo json_encode(array("status" => "error", "msj" => "Error en la base de datos: " . $e->getMessage()));
		$conexion->close();
	}

}

?>
<?php
try {
	$arr_exs = array();
	include("../../../common/conexion.php");
	$conexion->set_charset("utf8");
	$no_cliente=$_POST["cliente"];
	$marca=$_POST["marca"];
	$num_edos=$_POST["num_edos"];
	$sql = "SELECT id_existencias,marca,edo,tipo,serie,fol_ini,fol_fin,existencias, cliente_crm, marca_crm 
	FROM h_existencias WHERE no_cliente ='{$no_cliente}' and marca='{$marca}' ";
	$sqlm = "SELECT IF(status='1', 'ACTIVA', 'INACTIVA') estatus FROM marcas WHERE no_cliente ='{$no_cliente}' and cve_marca='{$marca}'";
	if($num_edos>0)
	{
		$edo=$_POST["edo"];
		$tipo=$_POST["tipo"];
		$sql.=" and edo='{$edo}' and tipo=$tipo";
	}
	else
	{
		$sql.=" and edo=''";
	}
	$sql.=" order by cliente_crm desc, fol_fin asc";
	$result=$conexion->query($sql);
	$num_res=0;
	if($result)
	{
		$num_res=$result->num_rows;
	}
	if($num_res>0)
	{
		while($row=$result->fetch_assoc())
		{
		  array_push($arr_exs, array("id"=>$row["id_existencias"],"marca" => utf8_encode($row["marca"]),"edo" => utf8_encode($row["edo"]),"tipo" => $row["tipo"],"serie" => utf8_encode($row["serie"]),"fol_ini" => $row["fol_ini"],"fol_fin" => $row["fol_fin"],"existencias" => $row["existencias"],
		  		"cliente_crm" => $row["cliente_crm"], "marca_crm" => $row["marca_crm"] ) );
		}
	}
	$resultm = $conexion->query($sqlm);
	$rowm = $resultm->fetch_object();



	
	// BUSCAR HISTÓRICO DE HOLOGRAMAS DE LA MARCA
	$sql_edo = (isset($edo) && $edo != "") ? " AND hs.edo = '$edo' ": "" ;
	//$sql_edo = ($edo != "") ? " and hs.edo = '$edo' ": "" ;
	$arrHistorico = array();
	$sql = $conexion->prepare("
		SELECT hs.id_salidas HSID, hs.no_cliente AS CLIENTE, c.nombre NOMBRE, hs.marca AS MARCA, m.marca AS NMARCA, hs.edo AS EDO, hs.solicitud AS SOLICITUD, hs.id_recibo AS RECIBO,
					hs.fecha_entr AS FECHAENTREGA, CAST(fi1 AS SIGNED INTEGER) AS FOLIOI, CAST(ff1 AS SIGNED INTEGER) AS FOLIOF, se1 AS CANTIDAD
		FROM h_salidas hs
		INNER JOIN clientes c ON hs.no_cliente = c.no_cliente
		LEFT JOIN marcas m ON CONCAT(hs.no_cliente, '-', hs.marca) = CONCAT(m.no_cliente, '-', m.cve_marca)
		WHERE YEAR(fecha_entr) > 2020
		and hs.no_cliente = '$no_cliente' AND hs.marca = '$marca' $sql_edo
		ORDER BY hs.no_cliente, marca, fi1 DESC;");
	if (!$sql) throw new Exception("ERROR AL CONSULTAR INFORMACIÓN SOBRE EL SERVICIO (ERR:001) $conexion->error");
	if (!$sql->execute()) throw new Exception("ERROR AL CONSULTAR INFORMACIÓN SOBRE EL SERVICIO (ERR:002) $conexion->error");
	$sql->store_result();
	$sql->bind_result($HSID, $CLIENTE, $NOMBRE, $MARCA, $NMARCA, $EDO, $SOLICITUD, $RECIBO, $FECHAENTREGA, $FOLIOI, $FOLIOF, $CANTIDAD);
	$cont = 0;
	while ($sql->fetch()) {
				$sql2 = $conexion->prepare("
				SELECT eh.id_envasado_holograma, ee.id_envasado_entrada IDEE, ee.no_lote LOTE, ee.no_cliente NOCLIENTE, ee.fecha_envasado_fin FECHA,
				CAST(eh.holograma_inicio AS SIGNED INTEGER) AS INI,  CAST(eh.holograma_fin AS SIGNED INTEGER) AS FIN
					FROM rv_envasado_entrada ee
					LEFT JOIN rv_envasado_holograma eh ON ee.id_envasado_entrada = eh.id_envasado_entrada
					WHERE ee.id_marca = '$CLIENTE-$MARCA' AND eh.holograma_inicio NOT LIKE '%ostenta%' AND eh.holograma_fin NOT LIKE '%ostenta%'
					AND ( '$FOLIOI' BETWEEN CAST(eh.holograma_inicio AS SIGNED INTEGER) AND CAST(eh.holograma_fin AS SIGNED INTEGER)
					|| '$FOLIOF' BETWEEN CAST(eh.holograma_inicio AS SIGNED INTEGER) AND CAST(eh.holograma_fin AS SIGNED INTEGER)
					|| CAST(eh.holograma_inicio AS SIGNED INTEGER) BETWEEN '$FOLIOI' AND '$FOLIOF'
					|| CAST(eh.holograma_fin AS SIGNED INTEGER) BETWEEN '$FOLIOI' AND '$FOLIOF' )
					ORDER BY CAST(eh.holograma_inicio AS SIGNED INTEGER) ASC ");
				if (!$sql2) throw new Exception("ERROR AL CONSULTAR INFORMACIÓN SOBRE EL SERVICIO (ERR:001) $conexion->error");
				if (!$sql2->execute()) throw new Exception("ERROR AL CONSULTAR INFORMACIÓN SOBRE EL SERVICIO (ERR:002) $conexion->error");
				$sql2->store_result();
				$sql2->bind_result($IDEH, $IDEE, $LOTE, $NOCLIENTE, $FECHA, $INI, $FIN);
				$n = 0;
				array_push($arrHistorico,
					array(
						"estatus" => "P",
						"cont" => $cont,
						"recibo" => $RECIBO,
						"estado" => $EDO,
						"nocliente" => $CLIENTE,
						"fechaentrega" => $FECHAENTREGA,
						"solicitud" => $SOLICITUD,
						"cantidad" => $CANTIDAD,
						"folioi" => $FOLIOI,
						"foliof" => $FOLIOF
					)
				);
				$txtr = "";
				$estatusHistorico[$cont]["estatus"] = (!isset($estatusHistorico[$cont]["estatus"]))?"": $estatusHistorico[$cont]["estatus"];
				if($sql2->num_rows > 0) {
						$gini = 0; $resta = 0; $ban1 = 0;
						//$i++;
						while ($sql2->fetch()) {
							$numresta = 0;
							//
							array_push($arrHistorico,
								array(
									"estatus" => "E",
									"recibo" => $RECIBO,
									"solicitud" => $SOLICITUD,

									"efecha" => $FECHA,
									"enocliente" => $NOCLIENTE,
									"elote" => $LOTE,
									"efolioi" => $INI,
									"efoliof" => $FIN,
									"resta" => ($FIN-$INI)+1,
									"id_envasado_entrada" => $IDEE
								)
							);
							//
							if($gini != 0) {
									$resta = $INI - ($gini+1);
									$txtr .= $resta . " = " . $INI . "-" . ($gini+1) . " <br> ";
									$numresta = $resta;
									if ($resta > 5) { // SÓLO SI HAY MÁS DE 5 FOLIOS AUSENTES, SINO SE CONSIDERA MERMA
											$ini = $CLIENTE . $MARCA . str_pad(($gini+1), 7, "0", STR_PAD_LEFT) . "A";
											$fin = $CLIENTE . $MARCA . str_pad(($INI-1), 7, "0", STR_PAD_LEFT) . "A";
											//$resta = number_format(($gini+1),0) . "-" . number_format(($INI - 1),0);
											$resta = $ini . "-" . $fin;
											if(($gini+1) < ($INI-1)) {
													$fini = ($gini+1);
													$ffin = ($INI-1);
											} else {
													$ffin = ($gini);
													$fini = ($INI+1);
											}

											array_push($arrHistorico,
												array(
													"estatus" => "F",
													"recibo" => $RECIBO,
													"solicitud" => $SOLICITUD,
													"efolioi" => $fini,
													"efoliof" => $ffin,
													"resta" => $numresta,
													"paso" => 1
												)
											);
											$estatusHistorico[$cont]["estatus"] = ($estatusHistorico[$cont]["estatus"] != "FALTA" ) ? "FALTA": $estatusHistorico[$cont]["estatus"];
									} 
							}
							$gini = $FIN;
							$n++;
						}
						// VALIDAR FOLIO FINAL DEL ENVASADO CON EL FOLIO FINAL DE ENTREGA
						$resta = $FOLIOF - $FIN;
						$txtr .= $resta . " = " . $FOLIOF . "-" . $FIN . " <br> ";
						$numresta = $resta;
						if ($resta > 5) { // SÓLO SI HAY MÁS DE 5 FOLIOS AUSENTES, SINO SE CONSIDERA MERMA
							array_push($arrHistorico,
								array(
									"estatus" => "F",
									"recibo" => $RECIBO,
									"solicitud" => $SOLICITUD,
									"efolioi" => ($FIN+1),
									"efoliof" => $FOLIOF,
									"resta" => $numresta,
									"paso" => 2
								)
							);
							$ban1 = 1;
							$estatusHistorico[$cont]["estatus"] = ($estatusHistorico[$cont]["estatus"] != "FALTA" ) ? "FALTA": $estatusHistorico[$cont]["estatus"];
						}
				} else {
						//$resta = ($FOLIOF - $FOLIOI) + 1;
						$resta = 0;
						array_push($arrHistorico,
							array(
								"estatus" => "F",
								"recibo" => $RECIBO,
								"solicitud" => $SOLICITUD,
								"efolioi" => $FOLIOI,
								"efoliof" => $FOLIOF,
								"resta" => ($FOLIOF - $FOLIOI) + 1,
								"paso" => 3
							)
						);
						$estatusHistorico[$cont]["estatus"] = ($estatusHistorico[$cont]["estatus"] != "FALTA" ) ? "FALTA": $estatusHistorico[$cont]["estatus"];
				}

				//array_push($arrHistorico,array( "sumas" => $txtr));
				// SI SOLO ES 1 ENVASADO REPORTADO
				if($n == 1) {
						$resta = "";
						$numresta = 0;
						
						if($FOLIOI != $INI || $FOLIOF != $FIN){
							if($FOLIOI < $INI) {
									$numresta += (($INI - $FOLIOI)+1);
									$ini = $CLIENTE . $MARCA . str_pad($FOLIOI+1, 7, "0", STR_PAD_LEFT) . "A";
									$fin = $CLIENTE . $MARCA . str_pad(($INI), 7, "0", STR_PAD_LEFT) . "A";
									//$resta = number_format($FOLIOI,0) . "-" . number_format(($INI-1),0);
									//$resta = $ini . "-" . $fin;
									$resta = ($INI) - $FOLIOI;

									if((($INI - $FOLIOI)+1) > 5) {
											if($INI < $FOLIOI) {
													$fini = $INI;
													$ffin = $FOLIOI-1;
											} else {
													$fini = $FOLIOI;
													$ffin = $INI-1;
											}

											array_push($arrHistorico,
												array(
													"estatus" => "F",
													"recibo" => $RECIBO,
													"solicitud" => $SOLICITUD,
													"efolioi" => $fini,
													"efoliof" => $ffin,
													"resta" => $resta,
													"paso" => 4
												)
											);
											$estatusHistorico[$cont]["estatus"] = ($estatusHistorico[$cont]["estatus"] != "FALTA" ) ? "FALTA": $estatusHistorico[$cont]["estatus"];
									} 
							}
							if($FOLIOF > $FIN && $ban = 0) {
									$numresta += (($FOLIOF - $FIN)+1);
									$resta .= ($resta != "") ? "; ": "";
									$ini = $CLIENTE . $MARCA . str_pad(($FIN + 1), 7, "0", STR_PAD_LEFT) . "A";
									$fin = $CLIENTE . $MARCA . str_pad(($FOLIOF), 7, "0", STR_PAD_LEFT) . "A";
									//$resta .= number_format(($FIN + 1),0) . "-" . number_format(($FOLIOF-1),0);
									$resta .= $ini . "-" . $fin;
									$resta = ($FOLIOF) - ($FIN + 1);

									if((($FOLIOF - $FIN)+1) > 5) {
										if(($FIN+1) < $FOLIOF) {
												$fini = ($FIN+1);
												$ffin = $FOLIOF;
										} else {
												$fini = $FOLIOF+1;
												$ffin = $FIN;
										}

										array_push($arrHistorico,
											array(
												"estatus" => "F",
												"recibo" => $RECIBO,
												"solicitud" => $SOLICITUD,
												"efolioi" => $fini,
												"efoliof" => $ffin,
												"resta" => $resta,
												"paso" => 5
											)
										);
										$estatusHistorico[$cont]["estatus"] = ($estatusHistorico[$cont]["estatus"] != "FALTA" ) ? "FALTA": $estatusHistorico[$cont]["estatus"];
									} 
							}
						}

				}
				$cont++;
		}


	$txth = "
	<style>
			tr.hide-table-padding td {
			  padding: 0;
			}

			.expand-button {
				position: relative;
			}

			.accordion-toggle .expand-button:after
			{
			  position: absolute;
			  left:.75rem;
			  top: 50%;
			  transform: translate(0, -50%);
			  content: '-';
			}
			.accordion-toggle.collapsed .expand-button:after
			{
			  content: '+';
			}
		</style>
	";

	

	$txtff = '
	</div>
	</td>
	<td></td>
	</tr>
	';


	if(count($arrHistorico) > 0) {
		$txth .= '
		<div class="form-control">
		<div class="col-md-1" style="background-color: #EC9784">&nbsp</div>
		<div class="col-md-11">Hologramas no encontrados </div>
		</div>
		<div class="table-responsive">
			<table class="table">
				<thead>
					<tr style="font-size: 14px;" bgcolor="#52BE80">
								<th class="col" align=""><strong>RECIBO</strong></th>
								<th class="col" align=""><strong>SOLICITUD</strong></th>
								<th class="col" align=""><strong>ESTADO</strong></th>
								<th class="col" align=""><strong>FECHA DE ENTREGA</strong></th>
								<th class="col" align=""><strong>CANTIDAD</strong></th>
								<th class="col" align=""><strong>RANGO</strong></th>
								<th class="col" align="" ><strong>DETALLE</strong></th>
					</tr>
				</thead>
				<tbody>
				';
		$ctipo = ""; $cont = 1; $colorP = ""; $cuenta = 0;
		//foreach ($arrHistorico as $filah) {
		foreach ($arrHistorico as $key => $filah) {
			if(isset($filah["estatus"]) && $filah["estatus"] != "FALTA" && $filah["estatus"] != "") {
					if($filah["estatus"] == "P") { // PRINCIPAL
							$estatusP = @$estatusHistorico[$cont-1]["estatus"];
							if($estatusP == 'FALTA') {
									$colorP = ($estatusP == "FALTA") ? "color: red": "";
									//
									$txth .= ($ctipo == "E") ? $txtff : '';
									$ini = $CLIENTE . $MARCA . str_pad($filah["folioi"], 7, "0", STR_PAD_LEFT) . "A";
									$fin = $CLIENTE . $MARCA . str_pad($filah["foliof"], 7, "0", STR_PAD_LEFT) . "A";
									$vn = $cont;
									$txth .= '
									<tr class="accordion-toggle collapsed" id="accordion'.$vn.'" data-toggle="collapse" data-parent="#accordion'.$vn.'" href="#collapse'.$vn.'" aria-expanded="false" style="font-size: 12px; '.$colorP.' ">
											<td >'.$filah['recibo'].'</td>
											<td >'.$filah['solicitud'].'</td>
											<td >'.$filah['estado'].'</td>
											<td >'.$filah['fechaentrega'].'</td>
											<td >'.$filah['cantidad'].'</td>
											<td >'.$ini.'-'.$fin.'</td>
											<td class="expand-button" ></td>
									</tr>
									';
									//$cont++;
								}
								$cont++;
					} elseif($filah["estatus"] == "F" ) { // HOLOGRAMAS FALTANTES
							$cuenta++;
							//$vn = $cont;
							$txtif = '
							<tr class="hide-table-padding">
							<td colspan="6">
							<div id="collapse'.$vn.'" class="collapse in p-3">
								<div class="form-control" style="font-size: 11px;">
									<div class="col-md-2"><b>FECHA</b></div>
									<div class="col-md-2"><b># LOTE</b></div>
									<div class="col-md-2"><b>CANTIDAD</b></div>
									<div class="col-md-6"><b>FOLIOS</b></div>
								</div>
							';
							$txth .= ($ctipo == "P" || $ctipo == "E") ? $txtif : '';
							$ini = $CLIENTE . $MARCA . str_pad($filah["efolioi"], 7, "0", STR_PAD_LEFT) . "A";
							$fin = $CLIENTE . $MARCA . str_pad($filah["efoliof"], 7, "0", STR_PAD_LEFT) . "A";
							$txth .= '

							  <div class="form-control" style="background-color: #EC9784;font-size: 11px; ">
							    <div class="col-md-4"><b><center>HOLOGRAMAS SIN DATOS DE ENVASADO</center></b></div>
									<div class="col-md-2"><b><center>'.@$filah["resta"].'</center></b></div>
							    <div class="col-md-6">'.$ini.'-'.$fin.'</div>
								</div>
									';
					}
					//$txth .= ($ctipo == "E") ? $txtff : '';
					$ctipo = @$filah["estatus"]; // EL TIPO DE FILA INSERTADO


				}
		}
		$txth .= $txtff;
		$txth .= '
					</tbody>
				</table>
			</div>';
	} else {
		$txth = ' SIN DATOS ';
	}
	
	
	//
	$conexion->close();
	echo json_encode(array("status" => "OK", "msj" => "", "estatus"=>$rowm->estatus, "arr_exs" => $arr_exs,"num_res"=>$num_res, "arrHistorico" => @$arrHistorico, 'txth' => @$txth, 'estatusHistorico' => @$estatusHistorico ));
	//echo json_encode(array("status" => "OK", "msj" => "", "estatus"=>$rowm->estatus, "arr_exs" => $arr_exs,"num_res"=>$num_res ));
}
catch (Exception $e) {
	echo json_encode(array("status" => "error", "msj" => "Error en la base de datos: " . $e->getMessage()));
	$conexion->close();
}
?>

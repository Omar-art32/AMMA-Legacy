<?php
include('../../common/conexion.php');
$conexion->autocommit(FALSE);
session_start();
$id_ses=$_POST['id_ses'];
$usr=$_SESSION[$id_ses]['s_username'];
$cteRec=$_POST['cteRec'];
$cteProv=$_POST['cteProv'];
$tipoR=$_POST['tipoR'];
$tipo_vigencia=$_POST['tipo_vigencia'];
$vigencia_ini=$_POST['vigencia_ini'];
$vigencia_fin=$_POST['vigencia_fin'];

$obs=utf8_decode($_POST['obs']);
$status="";
$msj="";
if($tipoR=='E' or $tipoR=='PE')
{
	$arrMarcas=json_decode($_POST['marcas'],true);   
}
	switch($tipoR)
	{
		case 'P':
		{	
			$arr_ext=checa_existe($conexion, $cteRec ,$cteProv, $tipoR,'','');
			if($arr_ext['cont']>0){
				$status="error";
				$msj=$arr_ext['msj'];
				
			}else{
				$arr_ins=inserta_relacion($conexion, $cteRec, $cteProv, $tipoR, '', $obs, $usr,'',$tipo_vigencia,$vigencia_ini,$vigencia_fin);
				if($arr_ins['status']=='OK'){
			       $status="OK";
				   $msj=$arr_ins['msj'];
				}else{
				   $status=$arr_ins['status'];
				   $msj=$arr_ins['msj'];
				}
			}
			break;
		}
		case 'E':
		{	
		    $num_m=1;
			foreach($arrMarcas as $h => $id)
			{
				$marca=$arrMarcas[$h]['cve'];
				$arr_ext=checa_existe($conexion, $cteRec ,$cteProv, $tipoR,$marca,$arrMarcas);
				if($arr_ext['cont']>0){
					$status="OK";
					$msj.=$arr_ext['msj'].'<br>';					
				}else{
					$arr_ins=inserta_relacion($conexion, $cteRec, $cteProv, $tipoR, $marca, $obs, $usr, $arrMarcas,$tipo_vigencia,$vigencia_ini,$vigencia_fin);
					if($arr_ins['status']=='OK'){
					   $status="OK";
					   $msj.=$arr_ins['msj'].'<br>';
					}else{
					   $status=$arr_ins['status'];
					   $msj.=$arr_ins['msj'].'<br>';
					}
				}
			}			
			break;
		}
		case 'PE':
		{
			$tipoR='P';
			$arr_ext=checa_existe($conexion, $cteRec ,$cteProv, $tipoR,'','');
			if($arr_ext['cont']>0){
				$status="OK";
				$msj.=$arr_ext['msj'].'<br>';
				
			}else{
				$arr_ins=inserta_relacion($conexion, $cteRec, $cteProv, $tipoR, '', $obs, $usr,'','','','');
				if($arr_ins['status']=='OK'){
			       $status="OK";
				   $msj.=$arr_ins['msj'].'<br>';
				}else{
				   $status=$arr_ins['status'];
				   $msj.=$arr_ins['msj'].'<br>';
				}
			}	
		    $num_m=1;
			$tipoR='E';			
			foreach($arrMarcas as $h => $id)
			{
				$marca=$arrMarcas[$h]['cve'];
				$arr_ext=checa_existe($conexion, $cteRec ,$cteProv, $tipoR,$marca,$arrMarcas);
				if($arr_ext['cont']>0){
					$status="OK";
					$msj.=$arr_ext['msj'].'<br>';					
				}else{
					$arr_ins=inserta_relacion($conexion, $cteRec, $cteProv, $tipoR, $marca, $obs, $usr,$arrMarcas,$tipo_vigencia,$vigencia_ini,$vigencia_fin);
					if($arr_ins['status']=='OK'){
					   $status="OK";
					   $msj.=$arr_ins['msj'].'<br>';
					}else{
					   $status=$arr_ins['status'];
					   $msj.=$arr_ins['msj'].'<br>';
					}
				}
			}			
			break;
		}


		case 'V':
		{	
			$arr_ext=checa_existe($conexion, $cteRec ,$cteProv, $tipoR,'','');
			if($arr_ext['cont']>0){
				$status="error";
				$msj=$arr_ext['msj'];
				
			}else{
				$arr_ins=inserta_relacion($conexion, $cteRec, $cteProv, $tipoR, '', $obs, $usr,'',$tipo_vigencia,$vigencia_ini,$vigencia_fin);

				if($arr_ins['status']=='OK'){
			       $status="OK";
				   $msj=$arr_ins['msj'];
				}else{
				   $status=$arr_ins['status'];
				   $msj=$arr_ins['msj'];
				}
			}
			break;
		}			
	}
	$conexion->commit();
	$conexion->close();
	echo json_encode(array("status" => $status, "msj" => $msj));

function checa_existe($con, $cte_r ,$cte_p, $tip_rel,$mca, $arrM)
{
	try{
		$msj="";
		$adMsj="";
		$sqlCheckE="SELECT count(*) from clientes_relaciones where cte_rec=? and cte_prov=? and tipo_rel=? and marca=?";
		$stCheckE=$con->prepare($sqlCheckE);
		$stCheckE->bind_param('ssss',$cte_r,$cte_p,$tip_rel,$mca);
		if(!$stCheckE->execute())throw new Exception('No se pudo consultar la existencia de un relacion previa');
		$stCheckE->bind_result($count);	
		$stCheckE->fetch();
		$stCheckE->close();
		$relacion=getTipo($tip_rel);
		if($mca!=''){
			$nom_marca=$arrM[$mca]['mca'];
			$adMsj="para la marca<b> $mca - $nom_marca </b>";
		}
		if($count>0) $msj="Ya existe una relacion<b> $relacion </b>entre los asociados:<b> $cte_r </b>y<b> $cte_p </b>".$adMsj;
		return array("status" => 'OK', "cont" => $count,"msj" => $msj);
	}catch (Exception $e){		
		return array("status" => 'error',"cont" => 2, "msj" => $e->getMessage());	
	 }
}
function inserta_relacion($con, $cte_r ,$cte_p, $tip_rel, $mca, $observ, $usuario, $arrM,$tipo_vigencia,$vigencia_ini,$vigencia_fin)
{
	try{
		$adMsj="";
		$sqlInsRel="INSERT INTO clientes_relaciones(cte_rec,cte_prov,tipo_rel,marca, obs,usr_reg,fecha_reg,tipo_vig,fecha_ini,fecha_fin) VALUES(?,?,?,?,?,?,NOW(),?,?,?)";	
	    $stInsRel=$con->prepare($sqlInsRel);
		$stInsRel->bind_param('sssssssss',$cte_r,$cte_p,$tip_rel,$mca,$observ,$usuario,$tipo_vigencia,$vigencia_ini,$vigencia_fin);
		if($mca!=''){
			$nom_marca=$arrM[$mca]['mca'];
			$adMsj="para la marca<b> $mca - $nom_marca </b>";
		}
		$relacion=getTipo($tip_rel);
		if(!$stInsRel->execute())throw new Exception("No se ha podido crear la relacion<b> $relacion </b>entre los asociados:<b> $cte_r </b>y<b> $cte_r </b>".$adMsj);		
		$msj="La relacion<b> $relacion </b>entre  los asociados:<b> $cte_r </b>y<b> $cte_p </b> $adMsj </b>se creó satisfactoriamente";
		return array("status" => 'OK', "msj" => $msj);
	}catch (Exception $e){
		$con->rollback();
		return array("status" => 'error', "msj" => $e->getMessage());	
	 }	
}
function getTipo($t)
{
	switch($t)
	{
	case 'P': return 'PRODUCTOR';
	case 'E': return 'ENVASADOR';
	}
	
}
?>
 





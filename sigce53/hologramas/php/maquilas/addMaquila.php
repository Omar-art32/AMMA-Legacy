<?php
include('../../../common/conexion.php');
$conexion->autocommit(FALSE);
session_start();
$id_ses=$_POST['id_ses'];
$usr=$_SESSION[$id_ses]['s_username'];
$cteRec=$_POST['cteRec'];
$cteProv=$_POST['cteProv'];
$tipoR=$_POST['tipoR'];
$obs=utf8_encode($_POST['obs']);
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
			$arr_ext=checa_existe($conexion, $cteRec ,$cteProv, $tipoR,'');
			if($arr_ext['cont']>0){
				$status="error";
				$msj=$arr_ext['msj'];
				
			}else{
				$arr_ins=inserta_relacion($conexion, $cteRec, $cteProv, $tipoR, '', $obs, $usr);
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
				$marca=$arrMarcas[$h];
				$arr_ext=checa_existe($conexion, $cteRec ,$cteProv, $tipoR,$marca);
				if($arr_ext['cont']>0){
					$status="error";
					$msj.=$arr_ext['msj'].'<br>';					
				}else{
					$arr_ins=inserta_relacion($conexion, $cteRec, $cteProv, $tipoR, $marca, $obs, $usr);
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
	}
	$conexion->commit();
	$conexion->close();
	echo json_encode(array("status" => $status, "msj" => $msj));

function checa_existe($con, $cte_r ,$cte_p, $tip_rel,$mca)
{
	try{
		$msj="";
		$sqlCheckE="SELECT count(*) from clientes_relaciones where cte_rec=? and cte_prov=? and tipo_rel=? and marca=?";
		$stCheckE=$con->prepare($sqlCheckE);
		$stCheckE->bind_param('ssss',$cte_r,$cte_p,$tip_rel,$mca);
		if(!$stCheckE->execute())throw new Exception('No se pudo consultar la existencia de un relacion previa');
		$stCheckE->bind_result($count);	
		$stCheckE->fetch();
		$stCheckE->close();
		if($count>0) $msj="Ya existe una relacion entre $cte_r y $cte_p";
		return array("status" => 'OK', "cont" => $count,"msj" => $msj);
	}catch (Exception $e){		
		return array("status" => 'error',"cont" => 2, "msj" => $e->getMessage());	
	 }
}
function inserta_relacion($con, $cte_r ,$cte_p, $tip_rel, $mca, $observ,$usuario)
{
	try{
		$adMsj="";
		$sqlInsRel="INSERT INTO clientes_relaciones(cte_rec,cte_prov,tipo_rel,marca, obs,usr_reg,fecha_reg) VALUES(?,?,?,?,?,?,NOW())";	
	    $stInsRel=$con->prepare($sqlInsRel);
		$stInsRel->bind_param('ssssss',$cte_r,$cte_p,$tip_rel,$mca,$observ,$usuario);
		if($mca!='')$adMsj="para la marca '{$mca}' ";
		$relacion=getTipo($tip_rel);
		if(!$stInsRel->execute())throw new Exception("No se ha podido crear la relacion '{$relacion}'  entre '{$cte_r}' y '{$cte_r}'".$adMsj);		
		$msj="La relacion '{$relacion}' entre '{$cte_r}' y '{$cte_p}' '{$adMsj}'se creo satisfactoriamente";
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
 





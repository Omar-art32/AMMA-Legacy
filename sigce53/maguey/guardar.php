<?php
  //include ("php/registro/conexion.php");
  include("../common/conexion.php");
  $conexion->set_charset("utf8");
  ini_set('date.timezone', 'America/Mexico_City');
  //$now=date("Y-m-d");
  //$now="'".$now."'";
  $now="NOW()";
  // datos del archvo
  $nombre = $_FILES['archivo']['name'];
  $tipo = $_FILES['archivo']['type'];
  $tamanio = $_FILES['archivo']['size'];
  $ruta =$_FILES['archivo']['tmp_name'];
  $destino = "";
  //poligono


  $loc=(isset($_POST['local']))?$_POST['local']:0; //id_localidad
  $sta=(isset($_POST['state']))?$_POST['state']:0;  //no.asociado
  $par=$_POST['paraje']; //nombre del paraje
  $lati=(isset($_POST['lat']))?$_POST['lat']:0; //latitud
  $lon=(isset($_POST['lng']))?$_POST['lng']:0;//longitud

  $ten=$_POST['tenencia'];//tenecia de la tierra
  $supe=(isset($_POST['superficie']))?$_POST['superficie']:0; //superficie del predio
  $refu='';//$_POST['referenciau']; //referencia ubicacion
  $usu= (isset($_POST['usufruto']))?$_POST['usufruto']:0; //usufruto de ñla tierra
  $ref=$_POST['referencia2']; //referencia del asociado
  $nombre_asociado=$_POST['abbrev']; //id_localidad
  $fec = date('Y-m-d', strtotime($_POST['fecha']));
  
  $cam=$_POST['campo'];// representante en campo
  $status_predio='1'; //$_POST['status_predio'];// estatus del predio
  $cboxGuia = $_POST['cboxGuia'];// Cantidad de Guías :: antes estatus del predio
  $cboxMCR = $_POST['cboxMCR'];// tipo de registro de maguey
  $SelServicio = $_POST['SelServicio'];// tipo de registro de maguey
  $idus = $_POST['idus'];

  // IMÁGENES PARA CONSTANCIA
  $nombrei1 = $_FILES['imagen1']['name'];
  $tipoi1 = $_FILES['imagen1']['type'];
  $tamanioi1 = $_FILES['imagen1']['size'];
  $rutai1 =$_FILES['imagen1']['tmp_name'];

  $nombrei2 = $_FILES['imagen2']['name'];
  $tipoi2 = $_FILES['imagen2']['type'];
  $tamanioi2 = $_FILES['imagen2']['size'];
  $rutai2 =$_FILES['imagen2']['tmp_name'];

  $pol=null;
  /*if($cboxMCR==1){
  $rut = $_FILES["poligono"]["tmp_name"];
  if (file_exists($rut)){
    $kml = file_get_contents($rut);
    $coordinates = "<coordinates>";
    $coordinates2 = "</coordinates>";
    if(strpos($kml,$coordinates)!==false){
        $pos=strpos($kml,$coordinates);
        $a=substr($kml,$pos+strlen($coordinates));
        if(strpos($a,$coordinates2)!==false){
          $npos=strpos($a,$coordinates2);
          $b=substr($a,0,$npos);
          $poligono = trim($b);
        }else{
          $poligono = trim($a);
        }
    }
   }


  //cambio del poligono
  $resultado = str_replace(",0 ", "*", $poligono);
  $resultado = str_replace(",", " ", $resultado);
  $resultado = str_replace("*", ",", $resultado);
  if(substr($resultado, -1)==","){
    $resultado = substr($resultado, 0, -1);
  }else if(substr($resultado, -1)=="0"){
    $resultado = substr($resultado, 0, -2);
  }
  $pol=$resultado;//poligono
  }*/









try{
    $conexion->autocommit(FALSE);
    if($nombre != ""){
      $extencion          = substr($nombre, strrpos($nombre, '.'));
      $random_Number      = rand(0, 9999999999);
      $nuevoNombre        = "docpro_".$random_Number.$extencion;
      $destino = "docpro/" . $nuevoNombre;
      if(!move_uploaded_file($ruta, $destino)) throw new Exception("Error al subir la foto el documento de propiedad.");
    }

    $consulta="SELECT SUBSTR(id_paraje,2,length(id_paraje)) id FROM paraje WHERE id = (SELECT MAX(id) FROM paraje) ORDER BY id DESC";
    $consultaid = $conexion->query($consulta);
    if($consultaid==false) throw new Exception("Error al obtener id paraje");
    if ($consultaid->num_rows > 0){
        $id="P";
        while ($row = $consultaid->fetch_array(MYSQLI_ASSOC)){
          //$id .=$row['idparaje']; //concatenamos el los options para luego ser insertado en el HTML
            if($row['id'] > 0) {
              $id .= ($row['id']+1); //concatenamos el los options para luego ser insertado en el HTML
            } else {
              $id .= 1;
            }
        }
    }

    // GUARDANDO IMÁGENES PARA CONSTANCIA
    if($nombrei1 != "") {
        $ext          = substr($nombrei1, strrpos($nombrei1, '.'));
        $nuevoNombre        = $id . "_01".$ext;
        $destino1 = "constancia/imgconstancia/" . $nuevoNombre;
        if(!move_uploaded_file($rutai1, $destino1)) throw new Exception("Error al subir la foto de la ubicación 1."); 
    }
    if($nombrei2 != "") {
        $ext          = substr($nombrei1, strrpos($nombrei1, '.'));
        $nuevoNombre        = $id . "_02".$ext;
        $destino2 = "constancia/imgconstancia/" . $nuevoNombre;
        if(!move_uploaded_file($rutai2, $destino2)) throw new Exception("Error al subir la foto de la ubicación 2."); 
    }

    //
    if( $ref=="") {
      $datoparaje="('$id',    '$loc',                           '$sta', '$par',               '$lati',
                    '$lon',   GEOMFROMTEXT('POLYGON(($pol))'),  '$ten', '$supe',              '$destino',
                    '$refu',  '$usu',                           $now,   '$nombre_asociado',   '$fec',
                    '$cam',   '1',                              '1',    '$cboxMCR',           '$status_predio',
                    $idus,    '$SelServicio')";
    } else {
      $datoparaje="('$id',    '$loc',                           '$sta', '$par',     '$lati',
                    '$lon',   GEOMFROMTEXT('POLYGON(($pol))'),  '$ten', '$supe',    '$destino',
                    '$refu',  '$usu',                           $now,   '$ref',     '$fec',
                    '$cam',   '1',                              '1',    '$cboxMCR', '$status_predio',
                    $idus,    '$SelServicio')";
    }
    $sqlparaje="INSERT INTO paraje (id_paraje,  id_localidad, id_cliente,   paraje,               lat,
                                    lng,        poligono,     tenencia,     superficie,           docpro,
                                    referencia, usufruto,     fecha,        nombrep,              fecha_paraje,
                                    rcampo,     status,       tipo,         maguey_con_registro,  status_predio,
                                    id_us,      servicio) VALUES ".$datoparaje;
                                  echo $sqlparaje;
    $result=$conexion->query($sqlparaje);
    if($result==false) throw new Exception("Error al insertar en paraje ".$sqlparaje);
    //$id_parajito=$conexion->insert_id;
    $id_parajito = $id;
    $data = json_decode($_POST['tMaguey'], true);
    foreach($data as $value){
      // $value[3] :: ESPECIE EN TEXTO
      // $value[6] :: ID ESPECIE
      $sqlplantas="('$value[0]','$value[1]','$value[2]','$value[6]','$value[4]','$value[5]',$now,'1','$value[4]','$id_parajito', $idus)";
      $sqlplantas="insert into existenciaplanta(regmaguey,dis_surcometros,dis_planmetros,id_comun,cantidadini,edad,fecha_registro,status,existenciaplantas,id_paraje, id_us)VALUES".$sqlplantas;
      $ps=$conexion->query($sqlplantas);
      if($ps==false) throw new Exception("Error al realizar el registro en existenciaplantas ".$sqlplantas);
    }
    $datoconst="($now,' ','$id_parajito','1', $idus)";
    $sqlconstancia="insert into constancias(fecha,constancia,id_paraje,status, id_us)values".$datoconst;
    $rescons=$conexion->query($sqlconstancia);
    if($rescons==false) throw new Exception("Error al realizar el registro en constancias ".$sqlconstancia);
    //$consulta="SELECT MAX(id)+1 as id FROM cextracciones WHERE 1";

    //GUARDAR GUÍAS :: G - GUÍAS PREDIO :: GP - GUÍAS DE PLANTULAS (VIVERO)
    $consulta = "SELECT SUBSTR(id_extraccion,2,length(id_extraccion)) id FROM cextracciones 
    WHERE id = (SELECT MAX(id) FROM cextracciones WHERE SUBSTRING(id_extraccion, 1, 2) != 'GP' ) 
    ORDER BY id DESC; ";
    $consultaid = $conexion->query($consulta);
    if($consultaid==false) throw new Exception("Error al obtener id paraje");
    if ($consultaid->num_rows > 0){
        $idG="G";
        while ($row = $consultaid->fetch_array(MYSQLI_ASSOC)){
          //$id .=$row['idparaje']; //concatenamos el los options para luego ser insertado en el HTML
            if($row['id'] > 0) {
              $idG .= ($row['id']+1); //concatenamos el los options para luego ser insertado en el HTML
              $idS = ($row['id']+1);
            } else {
              $idG .= 1;
              $idS = 1;
            }
        }
    }
    $datoextracc = "";
    for($i = 0; $i < $cboxGuia; $i++) {
        $datoextracc .= ($datoextracc != "") ? ",": "";
        $datoextracc .= "('G".($idS+$i)."','$id_parajito','1',$now,' ',$idus)";
    }
    $datoextracc="INSERT INTO cextracciones(id_extraccion,id_paraje,status,fecha,constancia, id_us) VALUES ".$datoextracc;
    $resextrac=$conexion->query($datoextracc);
    if($resextrac==false) throw new Exception("Error al realizar el registro en extracciones ".$datoextracc);

    /*if($cboxGuia==1){
      $datoextracc="('$idG','$id_parajito','1',$now,' ', $idus)";
      $datoextracc="insert into cextracciones(id_extraccion,id_paraje,status,fecha,constancia, id_us)values".$datoextracc;
      $resextrac=$conexion->query($datoextracc);
      if($resextrac==false) throw new Exception("Error al realizar el registro en extracciones ".$datoextracc);
    } else{
      foreach($data as $value){
        if ($value[5]>4){
          $datoextracc="('G".($idS)."','$id_parajito','1',$now,' ',$idus),('G".($idS+1)."','$id_parajito','1',$now,' ',$idus),('G".($idS+2)."','$id_parajito','1',$now,' ',$idus),('G".($idS+3)."','$id_parajito','1',$now,' ',$idus),('G".($idS+4)."','$id_parajito','1',$now,' ',$idus)";
          $datoextracc="insert into cextracciones(id_extraccion,id_paraje,status,fecha,constancia, id_us)values".$datoextracc;
          $resextrac=$conexion->query($datoextracc);
          if($resextrac==false) throw new Exception("Error al realizar el registro en extracciones ".$datoextracc);
        }
      }
    }*/
    
    $conexion->commit();
    $conexion->close();
    echo "Registro realizado correctamente";
}catch (mysqli_sql_exception $e) {
  $conexion->rollback();
  $conexion->close();
  echo "Error en la base de datos: " . $e->getMessage();
}
?>

<?php
  //include ("php/registro/conexion.php");
  include("../common/conexion.php");
  $conexion->set_charset("utf8");
  //foto1
  $nombre = $_FILES['vfoto1']['name'];
  $tipo = $_FILES['vfoto1']['type'];
  $tamanio = $_FILES['vfoto1']['size'];
  $ruta = $_FILES['vfoto1']['tmp_name'];
  $destino = "";
  //foto2
  $nombre1 = $_FILES['vfoto2']['name'];
  $tipo1 = $_FILES['vfoto2']['type'];
  $tamanio1 = $_FILES['vfoto2']['size'];
  $ruta1 =$_FILES['vfoto2']['tmp_name'];
  $destino1 = "";
  $idus = $_POST['idus'];
  
  // FOTOS DEL MAPA
  $nombre3 =  $_FILES['vfoto3']['name'];
  $tipo3 =    $_FILES['vfoto3']['type'];
  $tamanio3 = $_FILES['vfoto3']['size'];
  $ruta3 =    $_FILES['vfoto3']['tmp_name'];

  $nombre4 =  $_FILES['vfoto4']['name'];
  $tipo4 =    $_FILES['vfoto4']['type'];
  $tamanio4 = $_FILES['vfoto4']['size'];
  $ruta4 =    $_FILES['vfoto4']['tmp_name'];
  // ------------------------------------------------------------------
  

  $loc=$_POST['vlocal']; //id_localidad
  $sta=$_POST['vstate'];  //no.asociado
  $par=$_POST['vparaje']; //nombre del paraje
  $lati=$_POST['vlat']; //latitud
  $lon=$_POST['vlng'];//longitud
  $refu=$_POST['vreferenciau']; //referencia ubicacion
  $ref=$_POST['vreferencia2']; //referencia del asociado
  $fec = date('Y-m-d',  strtotime($_POST['vfecha']));
  ini_set('date.timezone', 'America/Mexico_City');
  $now=date("Y-m-d");
  $cam=$_POST['vcampo'];// representante en campo
  //$status_predio=$_POST['status_predio'];// representante en campo  
  $status_predio = 1;

  try{
    $conexion->autocommit(FALSE);
    if($nombre != ""){
      $extencion          = substr($nombre, strrpos($nombre, '.'));
      $random_Number      = rand(0, 9999999999);
      $nuevoNombre        = "vivero_".$random_Number.$extencion;
      $destino = "fotosvive/" . $nuevoNombre;
      if(!move_uploaded_file($ruta, $destino)) throw new Exception("Error al subir la foto 1.");
    }
    if($nombre1 != ""){
      $extencion1          = substr($nombre1, strrpos($nombre1, '.'));
      $random_Number1      = rand(0, 9999999999);
      $nuevoNombre1        = "vivero_".$random_Number1.$extencion1;
      $destino1 = "fotosvive1/" . $nuevoNombre1;
      if(!move_uploaded_file($ruta1, $destino1)) throw new Exception("Error al subir la foto 2.");
    }

    //obtenemos el id del paraje para insertar
    $consulta="SELECT SUBSTR(id_paraje,2,length(id_paraje)) id FROM paraje_vivero WHERE id = (SELECT MAX(id) FROM paraje_vivero) ORDER BY id DESC";
    $consultaid = $conexion->query($consulta);
    if ($consultaid->num_rows > 0){
      $id="V";
      while ($row = $consultaid->fetch_array(MYSQLI_ASSOC)){
        if($row['id'] > 0) 
          $id .=($row['id'] + 1); //concatenamos el los options para luego ser insertado en el HTML
        else
          $id .= 1;
      }
    }else{
      $id="V1";
      //echo "No hubo resultados";
    }

    // FOTOS DEL MAPA
    if($nombre3 != ""){
      $ext          = substr($nombre3, strrpos($nombre3, '.'));
      $nuevoNombre  = $id."_01".$ext;
      $destino3      = "constancia/imgconstancia/" . $nuevoNombre;
      if(!move_uploaded_file($ruta3, $destino)) throw new Exception("Error al subir la foto de la ubicación 1.");
    }

    if($nombre4 != ""){
      $ext          = substr($nombre4, strrpos($nombre4, '.'));
      $nuevoNombre  = $id."_02".$ext;
      $destino4      = "constancia/imgconstancia/" . $nuevoNombre;
      if(!move_uploaded_file($ruta4, $destino)) throw new Exception("Error al subir la foto de la ubicación 1.");
    }


    // insertamos los datos del paraje
    $datoparaje="('$id','$loc','$sta','$par','$lati','$lon','','$refu','$now','$ref','$fec','$cam','1','2','$destino','$destino1','$status_predio')";
    $sqlparaje="INSERT INTO paraje_vivero (id_paraje,id_localidad,id_cliente,paraje,lat,lng,docpro,referencia,fecha,nombrep,fecha_paraje,rcampo,status,tipo,foto1,foto2,status_predio) VALUES ".$datoparaje;
    $result=$conexion->query($sqlparaje);
    if($result==false) throw new Exception("Error al insertar en paraje_vivero ".$sqlparaje);
    //ingresamos las plantas
    $id_parajito=$conexion->insert_id;
    $data = json_decode($_POST['tMaguey'], true);
    foreach($data as $value){
      $fechita = date('Y-m-d',strtotime($value[4]));
      $sqlplantas="('$id','$value[0]','$value[1]','$value[2]','$fechita','$value[3]','$now','1','$value[3]')";
      $sqlplantas="INSERT INTO existenciaplanta_vivero(id_paraje,regmaguey,origen,id_comun,fecha_siembra,cantidadini,fecha_registro,status,existenciaplantas) VALUES ".$sqlplantas;
      $ps=$conexion->query($sqlplantas);
      if($ps==false) throw new Exception("Error al realizar el registro en existenciaplantas ".$sqlplantas);
    }
    //insertarmos las constancias
    $datoconst="('$now',' ','$id','1')";
    $sqlconstancia="INSERT INTO constancias_vivero(fecha,constancia,id_paraje,status) VALUES ".$datoconst;
    $rescons=$conexion->query($sqlconstancia);
    if($rescons==false) throw new Exception("Error al realizar el registro en constancias ".$sqlconstancia);
    //echo json_encode(array("status" => "correcto", "msj" => "Registro realizado correctamente"));
    $conexion->commit();
    $conexion->close();
    echo "Registro realizado correctamente";
  }catch (mysqli_sql_exception $e) {
    //echo json_encode(array("status" => "error", "msj" => "Error en la base de datos: " . $e->getMessage()));
    $conexion->rollback();
    $conexion->close();
    echo "Error en la base de datos: " . $e->getMessage();
  }
?>
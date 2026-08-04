<?php
if (isset($_GET["action"]) && !empty($_GET["action"])) {
  $action = $_GET["action"];
  switch($action) {
    case "predios"       : listadoPredios();           break;
    case "getOpciones"      : getOpciones();                 break;
    case "getMunicipios"      : getMunicipios();                 break;
    case "getLocalidades"      : getLocalidades();                 break;
    case "getDatosPredio"      : getDatosPredio();                 break;
    case "suggest"                : suggestClientes();     break;
  }
}

if (isset($_POST["action"]) && !empty($_POST["action"])) {
  $action = $_POST["action"];
  switch($action) {
    case "registraPredio" : registraPredio();  break;
    
  }
}

function suggestClientes(){
  try {
    include("../../common/conexion.php");
    $conexion->set_charset("utf8");
    $return_arr = array();
    $busca      = $_GET['term'];

    // echo "string";

    $sql = $conexion->prepare("SELECT c.no_cliente, c.nombre,     c.asociado,   c.rfc,             c.tipo_persona,  
                                      c.magueyero,  c.mezcalero,  c.envasador,  c.comercializador, c.comercializador_bc, 
                                      c.viverista 
                                 FROM clientes c
                                WHERE c.status = '1' AND c.no_cliente LIKE '%{$busca}%' limit 5");
    if (!$sql) throw new Exception("Ocurrio un error al actualizar la muestra (ERROR:03) $conexion->error");
    if (!$sql->execute()) throw new Exception("Ocurrio un error al actualizar la muestra (ERROR:04) $conexion->error");
    $sql->store_result();
    $sql->bind_result($id,        $nombre,    $asociado,    $rfc,             $tipo_persona,  
                      $magueyero, $mezcalero, $envasador,   $comercializador, $comercializador_bc, 
                      $viverista);
    while ($sql->fetch()) {
      $row_array['id']                  = $id;
      $row_array['value']              = $id;
      $row_array['nombre']              = $nombre;
      $row_array['asociado']            = $asociado;
      $row_array['rfc']                 = $rfc;
      $row_array['tipo_persona']        = $tipo_persona;
      $row_array['magueyero']           = $magueyero;
      $row_array['mezcalero']           = $mezcalero;
      $row_array['envasador']           = $envasador;
      $row_array['comercializador']     = $comercializador;
      $row_array['comercializador_bc']  = $comercializador_bc;
      array_push($return_arr,$row_array);
    }
    echo json_encode($return_arr);
    $sql->close();

  } catch (\Exception $e) {
    echo $e->getMessage();
  }
}


function listadoPredios(){
  try {
    include('../../common/conexion.php');
	  $conexion->set_charset("utf8");
    $limit        = $_GET['limit'];
    $offset       = $_GET['offset'];
    $WHERE    = ($_GET['no_control'] != "") ? " WHERE p.id_cliente = '".$_GET['no_control']."' ": "";
    if($_GET['texto'] !== "") {
      $condTexto = ($_GET['texto'] != "") ? " ( p.paraje LIKE '%".$_GET['texto']."%' || p.usufruto LIKE '%".$_GET['texto']."%' || p.nombrep LIKE '%".$_GET['texto']."%' 
                                                || p.rcampo LIKE '%".$_GET['texto']."%' || l.localidad LIKE '%".$_GET['texto']."%' || mun.nombre LIKE '%".$_GET['texto']."%' 
                                                || es.nombre LIKE '%".$_GET['texto']."%' || p.id_paraje LIKE '%".$_GET['texto']."%' )": "";
      $WHERE    .= (($WHERE !== "") ? " AND  ": " WHERE ") . $condTexto;
    }
    $tipo_registro = $_GET['tipo_registro'];
    if($tipo_registro !== ""){
      $cond_tipo_registro = "";
      if($tipo_registro === '1'){
        $cond_tipo_registro = " p.maguey_con_registro = '1' ";
      } elseif($tipo_registro === '2'){
        $cond_tipo_registro = " p.maguey_con_registro = '2' AND p.servicio = 'NORMAL' ";
      } elseif($tipo_registro === '3'){
        $cond_tipo_registro = " p.maguey_con_registro = '2' AND p.servicio = 'EXCLUSIVO' ";
      } 
      $cond_tipo_registro = ($cond_tipo_registro !== "") ? " ($cond_tipo_registro) ": "";
      $WHERE .= (($WHERE !== "") ? " AND ": " WHERE ") . $cond_tipo_registro;
    }
    
    $atributo = $_GET['atributo'];
    $condAtt = ($atributo !== "") ? " WHERE atributo_id IN ($atributo) " : "";
    $leftInner = ($condAtt !== "") ? " INNER ": " LEFT ";
    $joinAtt = " $leftInner JOIN (
      SELECT id_paraje, GROUP_CONCAT(atributo_id) atributos FROM parajes_atributos_asignar $condAtt GROUP BY id_paraje
    ) paa ON paa.id_paraje = p.id_paraje";

    $array  = array();
    $count  = 0;

    /*$sql = $conexion->prepare("SELECT COUNT(id_paraje) FROM paraje");
    if (!$sql) throw new Exception(json_encode(array("codigo" => 1,"ref" => "ERR:EMI01","msg" => $conexion->error)));
    if (!$sql->execute()) throw new Exception(json_encode(array("codigo" => 1,"ref" => "ERR:EMI02","msg" => $conexion->error)));
    $sql->store_result();
    $sql->bind_result($numRegistros);
    $sql->fetch();
    $sql->close();

    setlocale(LC_MONETARY, 'en_US');*/
    $sql = $conexion->prepare("SELECT p.id_paraje,  p.id_cliente, p.paraje,   p.lat,    p.lng, 
                                      p.tenencia,   p.usufruto,   p.nombrep,  p.rcampo, p.maguey_con_registro,
                                      p.id,         c.nombre,     l.localidad,mun.nombre,   DATE(p.fecharegistro) p_fecharegistro, 
                                      es.nombre,    p_guias.veces,p.servicio, p.numa,   p.superficie,
                                      p.nombrep,    p.rcampo,     paa.atributos, 
                                      (SELECT COUNT(*) FROM paraje p	
                                        INNER JOIN clientes c ON p.id_cliente = c.no_cliente 
                                        LEFT JOIN localidades l ON p.id_localidad = l.id 
                                        LEFT JOIN municipios mun ON mun.id = l.MunicipioID
                                        LEFT JOIN estados es ON es.clave = mun.estado
                                        LEFT JOIN (
                                          SELECT id_paraje, COUNT(id_extraccion) veces FROM cextracciones GROUP BY id_paraje
                                        ) p_guias ON p_guias.id_paraje = p.id_paraje 
                                        $joinAtt
                                        $WHERE)
                                FROM paraje p	
                                INNER JOIN clientes c ON p.id_cliente = c.no_cliente 
                                LEFT JOIN localidades l ON p.id_localidad = l.id 
                                LEFT JOIN municipios mun ON mun.id = l.MunicipioID
                                LEFT JOIN estados es ON es.clave = mun.estado
                                LEFT JOIN (
                                  SELECT id_paraje, COUNT(id_extraccion) veces FROM cextracciones GROUP BY id_paraje
                                ) p_guias ON p_guias.id_paraje = p.id_paraje 
                                $joinAtt
                                $WHERE 
                                ORDER BY p.id ASC
                                LIMIT $limit OFFSET  $offset;");
    
    if (!$sql) throw new Exception(json_encode(array("codigo" => 1,"ref" => "ERR:EMI03","msg" => $conexion->error)));
    //$sql->bind_param("s",$inCliente);
    if (!$sql->execute()) throw new Exception(json_encode(array("codigo" => 1,"ref" => "ERR:EMI04","msg" => $conexion->error)));
    $sql->store_result();
    $sql->bind_result($id_paraje,  $id_cliente,     $paraje,   $lat,    $lng, 
                      $tenencia,   $usufruto,       $nombrep,  $rcampo, $maguey_con_registro,
                      $id,         $nombre_cliente, $localidad, $municipio, $p_fecharegistro, 
                      $estado,     $guias_veces,    $servicio,  $numa,   $superficie,
                      $nombrep,    $rcampo,         $atributos, $total_registros);  
    while ($sql->fetch()) {
      $array[$count]["id"]             = $id;
      $array[$count]["id_paraje"]      = $id_paraje;
      $array[$count]["id_cliente"]     = $id_cliente;
      $array[$count]["nombre_cliente"] = $nombre_cliente;
      $array[$count]["paraje"]         = $paraje;
      $array[$count]["lat"]            = $lat;
      $array[$count]["lng"]            = $lng;
      $array[$count]["tenencia"]       = $tenencia;
      $array[$count]["usufruto"]       = $usufruto;
      $array[$count]["nombrep"]        = $nombrep;
      $array[$count]["rcampo"]         = $rcampo;
      $array[$count]["superficie"]     = $superficie;
      $array[$count]["localidad"]      = $localidad;
      $array[$count]["municipio"]      = $municipio;
      $array[$count]["estado"]         = $estado;
      $array[$count]["guias_veces"]    = $guias_veces;
      $array[$count]["atributos"]      = $atributos;
      $txtmcr = "";
      if($maguey_con_registro == 1){
          $txtmcr = "EN SITIO";
      } elseif($maguey_con_registro == 2){
          $txtmcr = "DOCUMENTAL";
      }
      $txtmcr .= ($servicio != "") ? " ".$servicio : "";
      $array[$count]["registro"] = $txtmcr;
      $array[$count]["origen"] = ($numa > 0) ? "EXTERNO": "LOCAL";
      $count++;
    }
    $sql->close();


    $json["total"]  = $total_registros;//$numRegistros;
    $json["rows"]   = $array;
    echo json_encode($json);


  } catch (\Exception $e) {
    $conexion->close();
    echo $e->getMessage();
    header('HTTP/1.1 500 Internal Server Booboo');
    header('Content-Type: application/json; charset=UTF-8');
    die();
  }
}
function getDatosPredio(){
  try {
    include("../../common/conexion.php");
    $conexion->set_charset("utf8");

    $idIn         = isset($_GET["id"])?$_GET["id"]:null;
    $usuarioIn    = isset($_GET["u"])?$_GET["u"]:null;

    if ($idIn === null) {
      throw new Exception(json_encode(array("codigo" => 1,"ref" => "ERR:01","msg" => "No es posible consultar la cotización")));
    }
    $generales = array();
  

    $sql = $conexion->prepare("SELECT p.id,           p.id_paraje, p.id_localidad, p.id_cliente,          p.paraje,
                                      p.lat,          p.lng,       p.tenencia,     p.superficie,          p.docpro,
                                      p.referencia,   p.usufruto,  p.fecha,        p.nombrep,             p.fecha_paraje,
                                      p.rcampo,       p.status,    p.tipo,         p.maguey_con_registro, p.servicio,
                                      p.status_predio
              FROM paraje p
              WHERE p.id = ?");
    if (!$sql) throw new Exception("Ocurrio un error al actualizar la muestra (ERROR:01) $conexion->error");
    $sql->bind_param("i",$idIn);
    if (!$sql->execute()) throw new Exception("Ocurrio un error al actualizar la muestra (ERROR:02) $conexion->error");
    $sql->store_result();
    $sql->bind_result($id,           $id_paraje, $id_localidad, $id_cliente,          $paraje,
                      $lat,          $lng,       $tenencia,     $superficie,          $docpro,
                      $referencia,   $usufruto,  $fecha,        $nombrep,             $fecha_paraje,
                      $rcampo,       $status,    $tipo,         $maguey_con_registro, $servicio,
                      $status_predio);
    $sql->fetch();
    $predio = array();
    $predio["id"]    = $id;
    $predio["id_paraje"] = $id_paraje;
    $predio["paraje"] = $paraje;
    $predio["lat"] = $lat;
    $predio["lng"] = $lng;
    $predio["tenencia"] = $tenencia;
    $predio["superficie"] = $superficie;
    $predio["usufruto"] = $usufruto;
    $predio["nombrep"] = $nombrep;
    $predio["rcampo"] = $rcampo;
    $predio["servicio"] = $servicio;
    $predio["maguey_con_registro"] = $maguey_con_registro;
    $sql->close();

    $sql = $conexion->prepare("SELECT pa.id, paa.id, paa.fecha, paa.observaciones, paa.estatus, paa.nivel
              FROM paraje p
              LEFT JOIN parajes_atributos_asignar paa ON p.id_paraje = paa.id_paraje AND paa.estatus = '1'
              INNER JOIN paraje_atributo pa ON paa.atributo_id = pa.id 
              -- INNER JOIN parajes_atributos_fotos paf ON paa.id = paf.id_paa  AND paf.estatus = '1'
              WHERE p.id_paraje = ? and paa.estatus = '1'");
    if (!$sql) throw new Exception("Ocurrio un error al actualizar la muestra (ERROR:01) $conexion->error");
    $sql->bind_param("s",$id_paraje);
    if (!$sql->execute()) throw new Exception("Ocurrio un error al actualizar la muestra (ERROR:02) $conexion->error");
    $sql->store_result();
    $sql->bind_result($id, $id_paa, $fecha, $observaciones, $estatus, $nivel);
    $atributos = array();
    $con = 0;
    while ($sql->fetch()) {
      //$predio["atributos"][] = array("id" => $id, "fecha" => $fecha, "observaciones" => $observaciones, "estatus" => $estatus);
      $atributos[$con]["id"] = $id;
      $atributos[$con]["id_paa"] = $id_paa;
      $atributos[$con]["fecha"] = $fecha;
      $atributos[$con]["observaciones"] = $observaciones;
      $atributos[$con]["estatus"] = $estatus;
      $atributos[$con]["nivel"] = $nivel;
      $con++;
      //array("id" => $id, "id_paa" => $id_paa, "fecha" => $fecha, "observaciones" => $observaciones, "estatus" => $estatus);
    }
    $sql->close();
    foreach ($atributos as $key => $value) {
      //print_r($value["id_paa"]);
      //$predio["atributos"][] = $value;
      $sql = $conexion->prepare("SELECT id, id_paa, nombre, ruta, estatus
              FROM parajes_atributos_fotos 
              WHERE id_paa = ? and estatus = '1'");
      if (!$sql) throw new Exception("Ocurrio un error al actualizar la muestra (ERROR:01) $conexion->error");
      $sql->bind_param("i", $value["id_paa"]);
      if (!$sql->execute()) throw new Exception("Ocurrio un error al actualizar la muestra (ERROR:02) $conexion->error");
      $sql->store_result();
      $sql->bind_result($id, $id_paa, $nombre, $ruta, $estatus);
      $fotos = array();
      while ($sql->fetch()) {
        $fotos[] = array("id" => $id, "nombre" => $nombre, "ruta" => $ruta, "estatus" => true);
        //$atributos[$key]["fotos"] = array("id" => $id, "id_paa" => $id_paa, "nombre" => $nombre, "nombre_bd" => $nombre_bd, "ruta" => $ruta, "estatus" => $estatus);
      }
      $atributos[$key]["fotos"] = $fotos;
      $sql->close();
    }

    //$predio["atributos"] = $atributos;
    $jsonPredio = json_encode($predio);
    $jsonAtributos = json_encode($atributos);
    
    echo json_encode(array("codigo" => 0, "datosPredio" => $jsonPredio, "datosAtributos" => $jsonAtributos));
    // , "correos" => $arrCorreos

  } catch (\Exception $e) {
    $conexion->close();
    echo $e->getMessage();
    header('HTTP/1.1 500 Internal Server Booboo');
    header('Content-Type: application/json; charset=UTF-8');
    die();
  }
}
/**************************************************************************************************
**************************************************************************************************/
function registraPredio(){
  try {
    include('../../common/conexion.php');

    $conexion->set_charset("utf8");
    $conexion->autocommit(FALSE);
    $registro   = json_decode($_POST['registro']); 
    //$documentos = json_decode($_POST['documentos']); 
    $atributos   = json_decode($_POST['atributos']); 
    $inUsuario  = isset($_POST["usuario"])?$_POST["usuario"]:null;
    //$documentos = json_decode($_POST['documentos']); 
    $tipo       = isset($_POST["tipo"])?$_POST["tipo"]:null;

    //$estado = strtoupper($registro->Estado);
    if($registro->id > 0) {
      /*$sql = $conexion->prepare("UPDATE `clientes` SET rfc = ?,                  curp = ?,         razon_social = ?,       nombre_comercial = ?,    apellido_paterno = ?,
                                                          apellido_materno = ?,  calle = ?,        no_exterior = ?,        no_interior = ?,         colonia = ?,
                                                          localidad = ?,         municipio = ?,    tipo_persona = ?,       tels = ?,                Estado = ?, 
                                                          regimen_fiscal = ?,    codigo_postal = ?,moneda = ?,             nuevomod = '1',          usuario_mod = ?,  
                                                          ultima_mod = NOW(),    contacto = ?,     telefono_contacto = ?,  sociedad_mercantil = ?
                                 WHERE id = ? ");
      
      if (!$sql) throw new Exception(json_encode(array("codigo" => 1,"ref" => "ERR:02","msg" => $conexion->error)));
      $sql->bind_param("ssssssssssssssssssssssi",$registro->rfc,             $registro->curp,       $registro->razon_social,  $registro->nombre_comercial,    $registro->apellido_paterno,
                                       $registro->apellido_materno, $registro->calle,         $registro->no_exterior,  $registro->no_interior,         $registro->colonia,
                                       $registro->localidad,        $registro->municipio,     $registro->tipo_persona, $registro->tels,                $registro->estado,  
                                       $registro->regimen_fiscal,   $registro->codigo_postal,    $registro->moneda,    $inUsuario,                    $registro->contacto,
                                       $registro->telefono_contacto,$registro->sociedad_mercantil,$registro->id);
      if (!$sql->execute()) throw new Exception(json_encode(array("codigo" => 1,"ref" => "ERR:03","msg" => $conexion->error)));
      $_ID_CLIENTE_OAX = $registro->id;
      $sql->close();*/

      foreach($atributos as $atributo) {
        $estatusFotos = "0";
        $idAtributo = $atributo->id;
        if($atributo->estatus) {
          if($atributo->id_paa > 0) {
            $sql = $conexion->prepare("UPDATE parajes_atributos_asignar set fecha = ?, observaciones = ?, estatus = ?, us_modif = ?, fecha_modif = NOW(), tipo = 'PUE', nivel = ? WHERE id = ? ");
            if (!$sql) throw new Exception(json_encode(array("codigo" => "0021","error" => $conexion->error)));
            $sql->bind_param("sssiii", $atributo->fecha, $atributo->observaciones, $atributo->estatus, $inUsuario, $atributo->nivel,  $atributo->id_paa );
            if (!$sql->execute()) throw new Exception(json_encode(array("codigo" => "0022","error" => $conexion->error)));
            $sql->close();
            $indexDoc = "documentos" . $idAtributo;
            $ID_PAA = $atributo->id_paa;
          } else {
            $idAtributo = $atributo->id;
            $sql = $conexion->prepare("INSERT INTO parajes_atributos_asignar (id_paraje, atributo_id, fecha, observaciones, estatus, id_us, tipo, nivel) VALUES (?,?,?,?,'1',?,'PUE',?) ");
            if (!$sql) throw new Exception(json_encode(array("codigo" => "0021","error" => $conexion->error)));
            $sql->bind_param("sissii", $registro->id_paraje, $idAtributo, $atributo->fecha, $atributo->observaciones, $inUsuario, $atributo->nivel);
            if (!$sql->execute()) throw new Exception(json_encode(array("codigo" => "0022","error" => $conexion->error)));
            $sql->close();
            $ID_PAA = $conexion->insert_id;
            $indexDoc = "documentos" . $idAtributo;
          }
          //REVISAR SI HAY ARCHIVOS A IMPORTAR
          if(isset($_FILES[$indexDoc])) {
            foreach($_FILES[$indexDoc]['name'] as $index => $value){
      
              $name     = $conexion->real_escape_string($_FILES[$indexDoc]['name'][$index]);
              $tmp_name = $_FILES[$indexDoc]['tmp_name'][$index];
              
              $extencion 					= substr($name, strrpos($name, '.'));
              $random_Number      = rand(0, 9999999999);
              $nuevoNombre 		    = "documento_".$random_Number.$extencion;
              
              $sql = $conexion->prepare("INSERT INTO `parajes_atributos_fotos`(id_paa, nombre, ruta, id_us) VALUES (?,?,?,?) ");
              if (!$sql) throw new Exception(json_encode(array("codigo" => "0021","error" => $conexion->error)));
              $sql->bind_param("issi", $ID_PAA, $name, $nuevoNombre, $inUsuario);
              if (!$sql->execute()) throw new Exception(json_encode(array("codigo" => "0022","error" => $conexion->error)));
              $sql->close();
      
              $path = $_SERVER['DOCUMENT_ROOT']."/sigce/nmaguey/images/fotosAtributos";
      
              if (!file_exists($path)) {
                if (!mkdir($path, 0777, true)) {
                  throw new Exception(json_encode(array("codigo" => "0023","error" => error_get_last(),"path" => $path)));
                }
              }
      
              $rutaDestino = $path.'/'.$nuevoNombre;
              if(!move_uploaded_file($tmp_name,$rutaDestino  )) {
                throw new Exception(json_encode(array("codigo" => "0024","error" => error_get_last(),"path" => $rutaDestino)));
              }
          
            }
          }
        } else {
          if($atributo->id_paa > 0) {
            $sql = $conexion->prepare("UPDATE parajes_atributos_asignar set fecha = ?, observaciones = ?, estatus = ?, us_modif = ?, fecha_modif = NOW(), tipo = 'PUE', nivel = ? WHERE id = ? ");
            if (!$sql) throw new Exception(json_encode(array("codigo" => "0021","error" => $conexion->error)));
            $sql->bind_param("sssiii", $atributo->fecha, $atributo->observaciones, $atributo->estatus, $inUsuario, $atributo->id_paa, $atributo->nivel );
            if (!$sql->execute()) throw new Exception(json_encode(array("codigo" => "0022","error" => $conexion->error)));
            $sql->close();
            $ID_PAA = $conexion->insert_id;
            $indexDoc = "documentos" . $idAtributo;
          }
          //$estatusFotos = '0';
        }
        // DESKTOP-CACV27P
        if(isset($atributo->fotos) && is_array($atributo->fotos)) {
          $documentos = $atributo->fotos;
          foreach($documentos as $indice => $docs) {
            $estatusFotos = (!$atributo->estatus) ? '0' : $docs->estatus;
            $sql = $conexion->prepare("UPDATE parajes_atributos_fotos set estatus = ?, us_modif = ?, fecha_modif = NOW() WHERE id = ? ");
            if (!$sql) throw new Exception(json_encode(array("codigo" => "0021","error" => $conexion->error)));
            $sql->bind_param("sii", $estatusFotos, $inUsuario, $docs->id);
            if (!$sql->execute()) throw new Exception(json_encode(array("codigo" => "0022","error" => $conexion->error)));
            $sql->close();
          }
        }
      }
    } else { // NUEVO PREDIO
      $sql = $conexion->prepare("INSERT INTO `paraje`(paraje, usufruto) VALUES(?,?)");
      if (!$sql) throw new Exception(json_encode(array("codigo" => 1,"ref" => "ERR:03","msg" => "Error al registrar cliente $conexion->error")));
      $sql->bind_param("ss",$registro->paraje,            $registro->usufruto);
      if (!$sql->execute()) throw new Exception(json_encode(array("codigo" => 1,"ref" => "ERR:04","msg" => "Error al registrar cliente $conexion->error")));
      $_ID_PARAJE = $conexion->insert_id;
      $sql->close();

      foreach($atributos as $atributo) {
        if($atributo->estatus) {
          $idAtributo = $atributo->id;
          $sql = $conexion->prepare("INSERT INTO parajes_atributos_asignar (id_paraje, atributo_id, fecha, observaciones, estatus, id_us) VALUES (?,?,?,?,'1',?) ");
          if (!$sql) throw new Exception(json_encode(array("codigo" => "0021","error" => $conexion->error)));
          $sql->bind_param("sissi", $registro->id_paraje, $idAtributo, $atributo->fecha, $atributo->observaciones, $inUsuario );
          if (!$sql->execute()) throw new Exception(json_encode(array("codigo" => "0022","error" => $conexion->error)));
          $sql->close();
          $ID_PAA = $conexion->insert_id;
          $indexDoc = "documentos" . $idAtributo;
        
          //REVISAR SI HAY ARCHIVOS A IMPORTAR
          if(isset($_FILES[$indexDoc])) {
            foreach($_FILES[$indexDoc]['name'] as $index => $value){
      
              $name     = $conexion->real_escape_string($_FILES[$indexDoc]['name'][$index]);
              $tmp_name = $_FILES[$indexDoc]['tmp_name'][$index];
              
              $extencion 					= substr($name, strrpos($name, '.'));
              $random_Number      = rand(0, 9999999999);
              $nuevoNombre 		    = "documento_".$random_Number.$extencion;
              
              $sql = $conexion->prepare("INSERT INTO `parajes_atributos_fotos`(id_paa, nombre, ruta, id_us) VALUES (?,?,?,?) ");
              if (!$sql) throw new Exception(json_encode(array("codigo" => "0021","error" => $conexion->error)));
              $sql->bind_param("issi", $ID_PAA, $name, $nuevoNombre, $inUsuario);
              if (!$sql->execute()) throw new Exception(json_encode(array("codigo" => "0022","error" => $conexion->error)));
              $sql->close();
      
              $path = $_SERVER['DOCUMENT_ROOT']."/sigce/nmaguey/images/fotosAtributos";
      
              if (!file_exists($path)) {
                if (!mkdir($path, 0777, true)) {
                  throw new Exception(json_encode(array("codigo" => "0023","error" => error_get_last(),"path" => $path)));
                }
              }
      
              $rutaDestino = $path.'/'.$nuevoNombre;
              if(!move_uploaded_file($tmp_name,$rutaDestino  )) {
                throw new Exception(json_encode(array("codigo" => "0024","error" => error_get_last(),"path" => $rutaDestino)));
              }
          
            }
          }
        }
      }
    }

    /*if(isset($_FILES)) {
			foreach($_FILES as $file){
				if($file['error'] == 0) {
					$name = $conexion->real_escape_string($file['name']);
					$mime = $conexion->real_escape_string($file['type']);
					$data = $conexion->real_escape_string(file_get_contents($file['tmp_name']));*/

    /*if(isset($_FILES['documentos'])) {
      foreach($_FILES['documentos']['name'] as $index => $value){

        $name     = $conexion->real_escape_string($_FILES['documentos']['name'][$index]);
        $tmp_name = $_FILES['documentos']['tmp_name'][$index];
        
        $extencion 					= substr($name, strrpos($name, '.'));
        $random_Number      = rand(0, 9999999999);
        $nuevoNombre 		    = "documento_".$random_Number.$extencion;
        
        $sql = $conexion->prepare("INSERT INTO `parajes_atributos_fotos`(nombre, nombre_bd, id_us) VALUES (?,?,?) ");
        if (!$sql) throw new Exception(json_encode(array("codigo" => "0021","error" => $conexion->error)));
        $sql->bind_param("ssi", $name, $nuevoNombre, $inUsuario);
        if (!$sql->execute()) throw new Exception(json_encode(array("codigo" => "0022","error" => $conexion->error)));
        $sql->close();

        $path = $_SERVER['DOCUMENT_ROOT']."/sigce/maguey/images/fotosAtributos";

        if (!file_exists($path)) {
          if (!mkdir($path, 0777, true)) {
            throw new Exception(json_encode(array("codigo" => "0023","error" => error_get_last(),"path" => $path)));
          }
        }

        $rutaDestino = $path.'/'.$nuevoNombre;
        if(!move_uploaded_file($tmp_name,$rutaDestino  )) {
          throw new Exception(json_encode(array("codigo" => "0024","error" => error_get_last(),"path" => $rutaDestino)));
        }
    
      }
		}*/

    if (!$conexion->commit()) {
      $conexion->rollback();
      throw new Exception(json_encode(array("codigo" => 1,"ref" => "ERR:500","msg" => $conexion->error)));
			exit();
		}

    $return_arr = array();
    
   

    echo json_encode(array("codigo" => 0,"status" => "correcto"));

  } catch (\Exception $e) {
    $conexion->rollback();
    $conexion->close();
    echo $e->getMessage();
    header('HTTP/1.1 500 Internal Server Booboo');
    header('Content-Type: application/json; charset=UTF-8');
    die();
  }
}

function getOpciones(){
  try {
      include('../../common/conexion.php');
      $conexion->set_charset("utf8");

      $sql = $conexion->prepare("SELECT clave, nombre FROM estados ");
      if (!$sql) throw new Exception(json_encode(array("codigo" => 1, "ref" => "ERR:02", "msg" => $conexion->error)));
      if (!$sql->execute()) throw new Exception(json_encode(array("codigo" => 1, "ref" => "ERR:03", "msg" => $conexion->error)));
      $sql->store_result();
      $sql->bind_result($id, $nombre);
      $estados = array();
      $con = 0;
      while($sql->fetch()){
        $estados[$con]["id"]    = $id;
        $estados[$con]["value"] = $nombre;
        $con++;
      }
      $sql->close();
      $jsonEdos = json_encode($estados);

      $sql = $conexion->prepare("SELECT id, atributo, abreviado, detalles, img FROM paraje_atributo WHERE estatus = '1' ");
      if (!$sql) throw new Exception(json_encode(array("codigo" => 1, "ref" => "ERR:02", "msg" => $conexion->error)));
      if (!$sql->execute()) throw new Exception(json_encode(array("codigo" => 1, "ref" => "ERR:03", "msg" => $conexion->error)));
      $sql->store_result();
      $sql->bind_result($id, $atributo, $abreviado, $detalles, $img);
      $atributos = array();
      $con = 0;
      while($sql->fetch()){
        $atributos[$con]["id"]    = $id;
        $atributos[$con]["value"] = $atributo;
        $atributos[$con]["abreviado"] = $abreviado;
        $atributos[$con]["detalles"] = $detalles;
        $atributos[$con]["img"] = $img;
        $atributos[$con]["fecha"] = "";
        $atributos[$con]["estatus"] = false;
        $atributos[$con]["id_paa"] = 0;
        $con++;
      }
      $sql->close();
      $jsonAtributos = json_encode($atributos);
      

      echo json_encode(array("codigo" => 0, 'estados' => $jsonEdos, 'atributos' => $atributos));

  } catch (\Exception $e) {
      // Manejar excepciones y enviar respuesta de error
      $conexion->close();
      header('HTTP/1.1 500 Internal Server Error');
      header('Content-Type: application/json; charset=UTF-8');
      echo $e->getMessage();
      die();
  }
}

function getMunicipios(){
  try {
      include('../../common/conexion.php');
      $conexion->set_charset("utf8");
      $estado = json_decode($_GET['estado']); 

      $sql = $conexion->prepare("SELECT id, estado, nombre FROM municipios WHERE estado = ? ");
      if (!$sql) throw new Exception(json_encode(array("codigo" => 1, "ref" => "ERR:02", "msg" => $conexion->error)));
      $sql->bind_param("i", $estado);
      if (!$sql->execute()) throw new Exception(json_encode(array("codigo" => 1, "ref" => "ERR:03", "msg" => $conexion->error)));
      $sql->store_result();
      $sql->bind_result($id, $estado, $nombre);
      $municipios = array();
      $con = 0;
      while($sql->fetch()){
        $municipios[$con]["id"]    = $id;
        $municipios[$con]["value"] = $nombre;
        $municipios[$con]["estado"] = $estado;
        $con++;
      }
      $sql->close();
      $jsonMpios = json_encode($municipios);

      echo json_encode(array("codigo" => 0, 'municipios' => $jsonMpios));

  } catch (\Exception $e) {
      // Manejar excepciones y enviar respuesta de error
      $conexion->close();
      header('HTTP/1.1 500 Internal Server Error');
      header('Content-Type: application/json; charset=UTF-8');
      echo $e->getMessage();
      die();
  }
}

function getLocalidades(){
  try {
      include('../../common/conexion.php');
      $conexion->set_charset("utf8");
      $municipio = json_decode($_GET['municipio']); 

      $sql = $conexion->prepare("SELECT id, MunicipioID, localidad FROM localidades WHERE MunicipioID = ? ");
      if (!$sql) throw new Exception(json_encode(array("codigo" => 1, "ref" => "ERR:02", "msg" => $conexion->error)));
      $sql->bind_param("i", $municipio);
      if (!$sql->execute()) throw new Exception(json_encode(array("codigo" => 1, "ref" => "ERR:03", "msg" => $conexion->error)));
      $sql->store_result();
      $sql->bind_result($id, $MunicipioID, $localidad);
      $localidades = array();
      $con = 0;
      while($sql->fetch()){
        $localidades[$con]["id"]    = $id;
        $localidades[$con]["value"] = $localidad;
        $localidades[$con]["municipio"] = $MunicipioID;
        $con++;
      }
      $sql->close();
      $jsonLoc = json_encode($localidades);

      echo json_encode(array("codigo" => 0, 'localidades' => $jsonLoc));

  } catch (\Exception $e) {
      // Manejar excepciones y enviar respuesta de error
      $conexion->close();
      header('HTTP/1.1 500 Internal Server Error');
      header('Content-Type: application/json; charset=UTF-8');
      echo $e->getMessage();
      die();
  }
}


?>
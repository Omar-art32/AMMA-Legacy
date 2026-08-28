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
    // [CAMBIO OBLIGATORIO PHP 8] $_GET['term'] sin verificar generaba
    // Warning: Undefined array key si el parámetro no llegaba en la URL.
    $busca      = $_GET['term'] ?? '';

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
      // [CAMBIO — acentos] Los datos de "clientes" están guardados en ISO-8859-1;
      // se convierten a UTF-8 para que se muestren bien los acentos/eñes,
      // mismo fix aplicado en el módulo de hologramas.
      $row_array['nombre']              = mb_convert_encoding($nombre ?? '', 'UTF-8', 'ISO-8859-1');
      $row_array['asociado']            = mb_convert_encoding($asociado ?? '', 'UTF-8', 'ISO-8859-1');
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
    // [CAMBIO OBLIGATORIO PHP 8] Todas las lecturas de $_GET de este bloque
    // accedían directo a la clave sin verificar su existencia.
    $limit        = $_GET['limit'] ?? 10;
    $offset       = $_GET['offset'] ?? 0;
    $no_control   = $_GET['no_control'] ?? '';
    $texto        = $_GET['texto'] ?? '';
    $tipo_registro = $_GET['tipo_registro'] ?? '';
    $atributo     = $_GET['atributo'] ?? '';

    $WHERE    = ($no_control != "") ? " WHERE p.id_cliente = '".$no_control."' ": "";
    if($texto !== "") {
      $condTexto = " ( p.paraje LIKE '%".$texto."%' || p.usufruto LIKE '%".$texto."%' || p.nombrep LIKE '%".$texto."%' 
                                                || p.rcampo LIKE '%".$texto."%' || l.localidad LIKE '%".$texto."%' || mun.nombre LIKE '%".$texto."%' 
                                                || es.nombre LIKE '%".$texto."%' || p.id_paraje LIKE '%".$texto."%' )";
      $WHERE    .= (($WHERE !== "") ? " AND  ": " WHERE ") . $condTexto;
    }
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
    
    $condAtt = ($atributo !== "") ? " WHERE atributo_id IN ($atributo) " : "";
    $leftInner = ($condAtt !== "") ? " INNER ": " LEFT ";
    $joinAtt = " $leftInner JOIN (
      SELECT id_paraje, GROUP_CONCAT(atributo_id) atributos FROM parajes_atributos_asignar $condAtt GROUP BY id_paraje
    ) paa ON paa.id_paraje = p.id_paraje";

    $array  = array();
    $count  = 0;

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
      // [CAMBIO — acentos] Conversión de ISO-8859-1 a UTF-8 en los campos de texto
      // que pueden contener acentos/eñes.
      $array[$count]["nombre_cliente"] = mb_convert_encoding($nombre_cliente ?? '', 'UTF-8', 'ISO-8859-1');
      $array[$count]["paraje"]         = mb_convert_encoding($paraje ?? '', 'UTF-8', 'ISO-8859-1');
      $array[$count]["lat"]            = $lat;
      $array[$count]["lng"]            = $lng;
      $array[$count]["tenencia"]       = $tenencia;
      $array[$count]["usufruto"]       = mb_convert_encoding($usufruto ?? '', 'UTF-8', 'ISO-8859-1');
      $array[$count]["nombrep"]        = mb_convert_encoding($nombrep ?? '', 'UTF-8', 'ISO-8859-1');
      $array[$count]["rcampo"]         = mb_convert_encoding($rcampo ?? '', 'UTF-8', 'ISO-8859-1');
      $array[$count]["superficie"]     = $superficie;
      $array[$count]["localidad"]      = mb_convert_encoding($localidad ?? '', 'UTF-8', 'ISO-8859-1');
      $array[$count]["municipio"]      = mb_convert_encoding($municipio ?? '', 'UTF-8', 'ISO-8859-1');
      $array[$count]["estado"]         = mb_convert_encoding($estado ?? '', 'UTF-8', 'ISO-8859-1');
      $array[$count]["guias_veces"]    = $guias_veces;
      $array[$count]["atributos"]      = mb_convert_encoding($atributos ?? '', 'UTF-8', 'ISO-8859-1');
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


    $json["total"]  = $total_registros;
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
    // [CAMBIO — acentos] Conversión de ISO-8859-1 a UTF-8.
    $predio["paraje"] = mb_convert_encoding($paraje ?? '', 'UTF-8', 'ISO-8859-1');
    $predio["lat"] = $lat;
    $predio["lng"] = $lng;
    $predio["tenencia"] = $tenencia;
    $predio["superficie"] = $superficie;
    $predio["usufruto"] = mb_convert_encoding($usufruto ?? '', 'UTF-8', 'ISO-8859-1');
    $predio["nombrep"] = mb_convert_encoding($nombrep ?? '', 'UTF-8', 'ISO-8859-1');
    $predio["rcampo"] = mb_convert_encoding($rcampo ?? '', 'UTF-8', 'ISO-8859-1');
    $predio["servicio"] = $servicio;
    $predio["maguey_con_registro"] = $maguey_con_registro;
    $sql->close();

    $sql = $conexion->prepare("SELECT pa.id, paa.id, paa.fecha, paa.observaciones, paa.estatus, paa.nivel
              FROM paraje p
              LEFT JOIN parajes_atributos_asignar paa ON p.id_paraje = paa.id_paraje AND paa.estatus = '1'
              INNER JOIN paraje_atributo pa ON paa.atributo_id = pa.id 
              WHERE p.id_paraje = ? and paa.estatus = '1'");
    if (!$sql) throw new Exception("Ocurrio un error al actualizar la muestra (ERROR:01) $conexion->error");
    $sql->bind_param("s",$id_paraje);
    if (!$sql->execute()) throw new Exception("Ocurrio un error al actualizar la muestra (ERROR:02) $conexion->error");
    $sql->store_result();
    $sql->bind_result($id, $id_paa, $fecha, $observaciones, $estatus, $nivel);
    $atributos = array();
    $con = 0;
    while ($sql->fetch()) {
      $atributos[$con]["id"] = $id;
      $atributos[$con]["id_paa"] = $id_paa;
      $atributos[$con]["fecha"] = $fecha;
      // [CAMBIO — acentos] Conversión de ISO-8859-1 a UTF-8.
      $atributos[$con]["observaciones"] = mb_convert_encoding($observaciones ?? '', 'UTF-8', 'ISO-8859-1');
      $atributos[$con]["estatus"] = $estatus;
      $atributos[$con]["nivel"] = $nivel;
      $con++;
    }
    $sql->close();
    foreach ($atributos as $key => $value) {
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
      }
      $atributos[$key]["fotos"] = $fotos;
      $sql->close();
    }

    $jsonPredio = json_encode($predio);
    $jsonAtributos = json_encode($atributos);
    
    echo json_encode(array("codigo" => 0, "datosPredio" => $jsonPredio, "datosAtributos" => $jsonAtributos));

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
    // [CAMBIO OBLIGATORIO PHP 8] $_POST['registro'] y $_POST['atributos'] se leían
    // sin verificar que existieran. Si faltaban, json_decode(null) además genera
    // aviso Deprecated en PHP 8.1+ (el primer parámetro debe ser string).
    $registro   = json_decode($_POST['registro'] ?? 'null');
    $atributos   = json_decode($_POST['atributos'] ?? '[]');
    $inUsuario  = isset($_POST["usuario"])?$_POST["usuario"]:null;
    $tipo       = isset($_POST["tipo"])?$_POST["tipo"]:null;

    if (!is_array($atributos)) {
      $atributos = array();
    }

    if($registro->id > 0) {

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
        }
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
        // [CAMBIO — acentos] Conversión de ISO-8859-1 a UTF-8.
        $estados[$con]["value"] = mb_convert_encoding($nombre ?? '', 'UTF-8', 'ISO-8859-1');
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
        // [CAMBIO — acentos] Estos campos son justo los que se ven mal en el
        // combo "Atributos" de la captura (ej. "ORG??NICO").
        $atributos[$con]["value"] = mb_convert_encoding($atributo ?? '', 'UTF-8', 'ISO-8859-1');
        $atributos[$con]["abreviado"] = mb_convert_encoding($abreviado ?? '', 'UTF-8', 'ISO-8859-1');
        $atributos[$con]["detalles"] = mb_convert_encoding($detalles ?? '', 'UTF-8', 'ISO-8859-1');
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
      // [CAMBIO OBLIGATORIO PHP 8] $_GET['estado'] sin verificar.
      $estado = json_decode($_GET['estado'] ?? 'null'); 

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
        // [CAMBIO — acentos] Conversión de ISO-8859-1 a UTF-8.
        $municipios[$con]["value"] = mb_convert_encoding($nombre ?? '', 'UTF-8', 'ISO-8859-1');
        $municipios[$con]["estado"] = $estado;
        $con++;
      }
      $sql->close();
      $jsonMpios = json_encode($municipios);

      echo json_encode(array("codigo" => 0, 'municipios' => $jsonMpios));

  } catch (\Exception $e) {
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
      // [CAMBIO OBLIGATORIO PHP 8] $_GET['municipio'] sin verificar.
      $municipio = json_decode($_GET['municipio'] ?? 'null'); 

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
        // [CAMBIO — acentos] Conversión de ISO-8859-1 a UTF-8.
        $localidades[$con]["value"] = mb_convert_encoding($localidad ?? '', 'UTF-8', 'ISO-8859-1');
        $localidades[$con]["municipio"] = $MunicipioID;
        $con++;
      }
      $sql->close();
      $jsonLoc = json_encode($localidades);

      echo json_encode(array("codigo" => 0, 'localidades' => $jsonLoc));

  } catch (\Exception $e) {
      $conexion->close();
      header('HTTP/1.1 500 Internal Server Error');
      header('Content-Type: application/json; charset=UTF-8');
      echo $e->getMessage();
      die();
  }
}


?>
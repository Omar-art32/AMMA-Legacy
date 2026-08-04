<?php
  //include ("php/registro/conexion.php");
  /*include("../common/conexion.php");
  ini_set('date.timezone', 'America/Mexico_City');
  $fechahoy = date("Y-m-d"); 
  $fecha2 = new DateTime($fechahoy);
  $sql = "SELECT * FROM  existenciaplanta";
  $res=mysqli_query($conexion,$sql)or die(mysql_error());
  while($fila=mysqli_fetch_array($res)){
    $fecha1 = new DateTime($fila['fecha_registro']);
    $fecha = $fecha1->diff($fecha2);
    $diferencia=$fecha->y;
    if($diferencia>0){
      $sql2 = "SELECT anios FROM anios_sumados WHERE id_plantas='$fila[id_plantas]'";
      $res2=mysqli_query($conexion,$sql2)or die(mysqli_error());
      while($fila2=mysqli_fetch_array($res2)){
        if($diferencia!=$fila2['anios']){
          $upd_anios = "UPDATE anios_sumados SET anios=$diferencia where id_plantas='$fila[id_plantas]'";
          mysqli_query($conexion,$upd_anios)or die(mysqli_error());
          $upd_edad = "UPDATE existenciaplanta SET edad=edad+1 where id_plantas='$fila[id_plantas]'";
          mysqli_query($conexion,$upd_edad)or die(mysqli_error());
        }
      }
      $total = mysqli_num_rows(mysqli_query($conexion,$sql2));
      if($total==0){
        $sql3 = "INSERT INTO `anios_sumados`(`id_plantas`, `anios`) VALUES ('$fila[id_plantas]',$diferencia)";
        mysqli_query($conexion,$sql3)or die(mysqli_error());
        $upd_edad2 = "UPDATE existenciaplanta SET edad=edad+1 where id_plantas='$fila[id_plantas]'";
        mysqli_query($conexion,$upd_edad2)or die(mysqli_error());
      }
    }
  }
  mysqli_close($conexion);*/

  
include("../common/conexion.php");

// 1. Actualizamos los registros que YA existen.
// Sumamos a la edad la diferencia exacta de años que faltaban por registrar.
$sql_update_existentes = "
    UPDATE existenciaplanta e
    JOIN anios_sumados a ON e.id_plantas = a.id_plantas
    SET 
        e.edad = e.edad + (TIMESTAMPDIFF(YEAR, e.fecha_registro, CURDATE()) - a.anios),
        a.anios = TIMESTAMPDIFF(YEAR, e.fecha_registro, CURDATE())
    WHERE TIMESTAMPDIFF(YEAR, e.fecha_registro, CURDATE()) > a.anios
";
mysqli_query($conexion, $sql_update_existentes) or die(mysqli_error($conexion));

// 2. Actualizamos la edad de las plantas NUEVAS (las que no están en anios_sumados).
// Les sumamos todos los años que han pasado desde su fecha de registro.
$sql_update_nuevos = "
    UPDATE existenciaplanta e
    LEFT JOIN anios_sumados a ON e.id_plantas = a.id_plantas
    SET e.edad = e.edad + TIMESTAMPDIFF(YEAR, e.fecha_registro, CURDATE())
    WHERE TIMESTAMPDIFF(YEAR, e.fecha_registro, CURDATE()) > 0
      AND a.id_plantas IS NULL
";
mysqli_query($conexion, $sql_update_nuevos) or die(mysqli_error($conexion));

// 3. Insertamos los registros nuevos en "anios_sumados".
$sql_insert_nuevos = "
    INSERT INTO anios_sumados (id_plantas, anios)
    SELECT e.id_plantas, TIMESTAMPDIFF(YEAR, e.fecha_registro, CURDATE())
    FROM existenciaplanta e
    LEFT JOIN anios_sumados a ON e.id_plantas = a.id_plantas
    WHERE TIMESTAMPDIFF(YEAR, e.fecha_registro, CURDATE()) > 0
      AND a.id_plantas IS NULL
";
mysqli_query($conexion, $sql_insert_nuevos) or die(mysqli_error($conexion));

mysqli_close($conexion);
?>
?>
<?php 
//include('../php/registro/conexion.php');
include("../../common/conexion.php");
$listado="SELECT clientes.no_cliente,clientes.nombre,paraje.id_paraje from clientes
 Inner Join paraje on paraje.id_cliente=clientes.no_cliente 
 inner join constancias on paraje.id_paraje=constancias.id_paraje  WHERE paraje.poligono is not null AND paraje.tipo=1 group by paraje.id_paraje";
$listar= $conexion->query($listado);

?>

 <script type="text/javascript" language="javascript" src="js/jslistado.js"></script>



            <table cellpadding="0" cellspacing="0" border="0" class="display" id="tabla_lista" >
                <thead>
                    <tr>
                        <th align="center">NO.PARAJE</th><!--Estado-->
                        <th align="center">NO.CLIENTE</th>
                        <th align="center">NOMBRE</th>
                        <th align="center">CONSTANCIA</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th></th>
                        <th></th>
                       
                     
                    </tr>
                </tfoot>
                  <tbody>
                    <?php

                   while($reg=mysqli_fetch_array($listar))
                   {
                               echo '<tr>';
                               echo '<td align="center">'.mb_convert_encoding($reg['id_paraje'], "UTF-8").'</td>';
                               echo '<td align="center">'.mb_convert_encoding($reg['no_cliente'], "UTF-8").'</td>';
							   echo '<td align="center">'.$reg['nombre'].'</td>';
							  /* echo '<td>'.'<img src="images/pdf-icon.png"/>'.'</td>';*/
							  echo "<td><a href=\"constancia/reporte_historial.php?id=".$reg['id_paraje']."\" target='_blank'><img src='images/pdf-icon.png'></a></td>";
							   
                          echo '</tr>';
                        }

                          $conexion->close();
                    ?>
                <tbody>
            </table>

<?php 
include('../php/registro/conexion.php');
$listado="SELECT clientes.no_cliente,clientes.nombre,paraje_vivero.id_paraje from clientes
 Inner Join paraje_vivero on paraje_vivero.id_cliente=clientes.no_cliente 
 inner join constancias_vivero on paraje_vivero.id_paraje=constancias_vivero.id_paraje WHERE paraje_vivero.tipo=2";
$listar= $conexion->query($listado);

?>

 <script type="text/javascript" language="javascript" src="js/jslistado3.js"></script>



            <table cellpadding="0" cellspacing="0" border="0" class="display" id="tabla_lista3" >
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
							  echo "<td><a href=\"constancia/reporte_historial3.php?id=".$reg['id_paraje']."\" target='_blank'><img src='images/pdf-icon.png'></a></td>";
							   
                               echo '</tr>';

                     
                        }

                          $conexion->close();
		
                    ?>
                <tbody>
            </table>

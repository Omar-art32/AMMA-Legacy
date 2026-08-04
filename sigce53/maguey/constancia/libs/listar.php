<?php 
include('../../php/registro/conexion.php');
$listado="SELECT no_cliente, nombre,id_paraje from paraje Inner Join clientes on id_cliente=no_cliente";
$listar= $conexion->query($listado);

?>

 <script type="text/javascript" language="javascript" src="js/jslistado.js"></script>



            <table cellpadding="0" cellspacing="0" border="0" class="display" id="tabla_lista">
                <thead>
                    <tr>
                        <th>NO.PARAJE</th><!--Estado-->
                        <th>NO.CLIENTE</th>
                        <th>NOMBRE</th>
                        <th>CONSTANCIA</th>
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
                               echo '<td >'.mb_convert_encoding($reg['id_paraje'], "UTF-8").'</td>';
                               echo '<td>'.mb_convert_encoding($reg['no_cliente'], "UTF-8").'</td>';
							   echo '<td>'.mb_convert_encoding($reg['nombre'], "UTF-8").'</td>';
							  /* echo '<td>'.'<img src="images/pdf-icon.png"/>'.'</td>';*/
							  echo "<td><a href=\"reporte_historial.php?id=".$reg['id_paraje']."\" target='_blank'><img src='images/pdf-icon.png'></a></td>";
							   
                               echo '</tr>';
                     
                        }
		
                    ?>
                <tbody>
            </table>

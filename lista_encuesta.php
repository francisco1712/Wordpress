<?php 
    global $wpdb;

    $tabla = "{$wpdb->prefix}encuestas";
    $tabla2 = "{$wpdb->prefix}encuestas_detalle";

    if(isset($_POST['btnguardar'])){

      $nombre = $_POST['txtnombre'];
      $query = "SELECT Encuestaid FROM $tabla ORDER BY Encuestaid DESC LIMIT 1";
      $resultado = $wpdb->get_results($query,ARRAY_A);
      $proximoId = $resultado[0]['Encuestaid'] + 1;

 /*      if (count($resultado)==0) {
        $proximoId = 1;
    } else{
        $proximoId = $resultado[0]['EncuestaId'] + 1;
    } */
  
      $shortcode = "[ENC id='$proximoId' ]";
      $datos = [
        'Encuestaid' => null,
        'Nombre' => $nombre,
        'Shortcode' => $shortcode
      ];
      $respuesta = $wpdb->insert($tabla,$datos);

      if($respuesta){
        $listapreguntas = $_POST['name'];
        $i = 0;
        foreach ($listapreguntas as $key => $value) {
          # code...
          $tipo = $_POST['type'][$i];
          $datos2 = [
            'DetalleId' => null,
            'Encuestaid' => $proximoId,
            'Pregunta' => $value,
            'Tipo' => $tipo

          ];
          $wpdb->insert($tabla2,$datos2);
          $i++;
        }
      }

      print_r($resultado);

    }
    $query4 = "SELECT Nombre, Pregunta, Respuesta from wpencuestas inner join wpencuestas_detalle on wpencuestas.Encuestaid = wpencuestas_detalle.Encuestaid inner join wpencuestas_respuestas on wpencuestas_respuestas.DetalleId = wpencuestas_detalle.DetalleId";
    $lista = $wpdb->get_results($query4,ARRAY_A);
    
    $query = "SELECT * FROM $tabla";
    $lista_encuesta = $wpdb->get_results($query,ARRAY_A);
    if(empty($lista_encuesta)){
      $lista_encuesta = array();
    }
?>
<div class="wrap">
    <?php

        echo "<h1 class='wp-heading-inline'>" . get_admin_page_title() . "</h1>";
    ?>
    <a id="btnuevo" class="page-title-action">Añadir nueva</a>
    <br><br><br>

    <table class="wp-list-table widefat fixed striped pages">
        <thead>
            <th> Nombre de la encuesta </th>
            <th> Shortcode </th>
            <th> Acciones </th>
        </thead>
        <tbody id="the-list">
        
            <?php
                foreach ($lista_encuesta as $key => $value) {
                    $id = $value['Encuestaid'];
                    $nombre = $value['Nombre'];
                    $shortcode = $value['ShortCode'];
                    echo "
                        <tr>
                            <td>$nombre</td>
                            <td>$shortcode</td>
                            <td>
                                <a data-ver='$id' id='btnestadisticas' class='page-title-action'>Ver estadisticas</a>
                                <a data-id='$id' class='page-title-action'>Borrar</a>
                            </td>
                        </tr>
                    ";
                } 
         
            ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="modalnuevo" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document" style="margin: 30px auto; width: 1000px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Nueva encuesta</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="post">
        <div class="modal-body">
          <div class="form-group">
            <label for="txtnombre" class="col-sm-4 col-form-label">Nombre de la encuesta</label>
            <div class="col-sm-8">
              <input type="text" name="txtnombre" id="txtnombre" style="width:100%">
            </div>
          </div>
          <hr>
          <h4>Preguntas</h4>
          <br>
          <table id="camposdinamicos">
            <tr> 
              <td> 
                <label for="txtnombre" class="coo-form-label" style="margin-left: 5px;">Pregunta</label>
          
              </td>
              <td>
                <input type="text" name="name[]" id="name" class="form-control name_list">
              
              </td>
              <td>
                <select name="type[]" id="type" class="form-control type_list">
                  <option value="1" select>SI - NO</option>
                  <option value="2">Rango 0 -5</option>
                  <option value="3">Respuesta corta</option>
                </select>
              </td>
              <td>
                <button name="add" id="add" class="btn btn-success" style="margin-left: 5px;">Agregar mas</button>
              </td>
            </tr>

          </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary" name="btnguardar" id="btnguardar">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Ver estadisticas -->
<div class="modal fade" id="modalestadisticas" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="margin: 30px auto; width: 1000px;" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLongTitle">Estadisticas</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<table class="wp-list-table widefat fixed striped pages">
			<thead>
				<th>Encuesta</th>
				<th>Pregunta</th>
				<th>Respuesta</th>
			</thead>
			<tbody id="the-list">
				<?php
					foreach ($lista as $key => $value) {
						$nombreencuesta = $value['Nombre'];
						$nombrepregunta = $value['Pregunta'];
						$nombrerespuesta = $value['Respuesta'];
						echo "
							<tr>
								<td>$nombreencuesta</td>
								<td>$nombrepregunta</td>
								<td>$nombrerespuesta</td>
							</tr>
						";
					}
				?>
			</tbody>
		</table>
	  </div>
    </div>
  </div>
</div>


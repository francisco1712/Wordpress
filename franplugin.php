<?php
/*
Plugin Name: Test Plugin
Description: Esto es un plugin de pruebas
Version: 0.0.1
*/

require_once dirname(__FILE__) . '/clases/codigocorto.class.php';

function Activar(){
    global $wpdb;
    $sql="CREATE TABLE IF NOT EXISTS {$wpdb->prefix}encuestas(
       `Encuestaid` INT NOT NULL AUTO_INCREMENT,
        `Nombre` VARCHAR(45) NULL,
        `ShortCode` VARCHAR(45) NULL,
        PRIMARY KEY(`Encuestaid`));";
    $wpdb->query($sql);
    $sql2="CREATE TABLE IF NOT EXISTS {$wpdb->prefix}encuestas_detalle(
        `DetalleId` INT NOT NULL AUTO_INCREMENT,
        `Encuestaid` INT NULL,
        `Pregunta` VARCHAR(45) NULL,
        `Tipo` VARCHAR(45) NULL,
        PRIMARY KEY(`DetalleId`),
        CONSTRAINT `FK_Encuestaid` FOREIGN KEY (Encuestaid)
        REFERENCES  {$wpdb->prefix}encuestas(Encuestaid) ON DELETE CASCADE
        );";
    $wpdb->query($sql2);
    $sql3="CREATE TABLE IF NOT EXISTS {$wpdb->prefix}encuestas_respuestas(
        `RespuestaId` INT NOT NULL AUTO_INCREMENT,
        `DetalleId` INT NULL,
        `Respuesta` VARCHAR(45) NULL,
        `Codigo` VARCHAR(45) NULL, 
        PRIMARY KEY(`RespuestaId`),
        CONSTRAINT `FK_DetalleId` FOREIGN KEY (`DetalleId`)
        REFERENCES  {$wpdb->prefix}encuestas_detalle(DetalleId) ON DELETE CASCADE
         );";
    $wpdb->query($sql3);
}

function Desactivar(){

}

register_activation_hook(__FILE__,'Activar');
register_deactivation_hook(__FILE__,'Desactivar');

add_action('admin_menu', 'CrearMenu');

function CrearMenu(){
    add_menu_page(
        'Encuestas',//Titulo de la pagina
        'Encuestas Menu',//Titulo del menu
        'manage_options',//Capability
        plugin_dir_path(__FILE__).'admin/lista_encuesta.php', //slug
        null,//Funcion del contenido
        plugin_dir_url(__FILE__).'admin/img/logo.png',
        '1' //priority
    );

    add_submenu_page(
        'fn_menu', //parent
        'Ajustes', // Titulo pagina
        'Ajustes', //Titulo menu
        'manage_options',
        'fn_menu_ajustes',
        'Submenu'
    );
}

function MostrarContenido(){
}

function Submenu(){

}

function EncolarBootstrapJS($hook){
    echo "<script>console.log('$hook')</script>";
    if($hook != "francisco-plugin/admin/lista_encuesta.php"){
        return;
    }
    wp_enqueue_script('bootstrapJs',plugins_url('admin/bootstrap/js/bootstrap.min.js',__FILE__),array('jquery'));
}
add_action('admin_enqueue_scripts','EncolarBootstrapJS');

function EncolarBootstrapCSS($hook){
    if($hook != "francisco-plugin/admin/lista_encuesta.php"){
        return;
    }
    wp_enqueue_style('bootstrapCSS',plugins_url('admin/bootstrap/css/bootstrap.min.css',__FILE__));
}
add_action('admin_enqueue_scripts','EncolarBootstrapCSS');

function EncolarJS($hook){
    if($hook != "francisco-plugin/admin/lista_encuesta.php"){
        return;
    }
    wp_enqueue_script('JsExterno',plugins_url('admin/js/lista_encuesta.js',__FILE__),array('jquery'));
    wp_localize_script('JsExterno','SolicitudesAjax',[
        'url' => admin_url('admin-ajax.php'),
        'seguridad' => wp_create_nonce('seg')
    ]);
}
add_action('admin_enqueue_scripts','EncolarJS');

//AJAX

function EliminarEncuesta(){
    $nonce = $_POST['nonce'];
    if(!wp_verify_nonce($nonce, 'seg')){
        die('no tiene permisos para ejecutar ese ajax');
    }

    $id = $_POST['id'];
    global $wpdb;
    $tabla = "{$wpdb->prefix}encuestas";
    $tabla2 = "{$wpdb->prefix}encuestas_detalle";
    $wpdb->delete($tabla,array('Encuestaid' => $id));
    $wpdb->delete($tabla2,array('Encuestaid' => $id));
    return true;
}

add_action('wp_ajax_peticioneliminar', 'EliminarEncuesta');


//shortcode

function imprimirshortcode($atts){
    $_short = new codigocorto;
    //obtener el id por parametro
    $id= $atts['id'];
    //Programar las acciones del boton
    if(isset($_POST['btnguardar'])){
        $listadePreguntas = $_short->ObtenerEncuestaDetalle($id);
        $codigo = uniqid();
        foreach ($listadePreguntas as $key => $value) {
           $idpregunta = $value['DetalleId'];
           if(isset($_POST[$idpregunta])){
               $valortxt = $_POST[$idpregunta];
               $datos = [
                   'DetalleId' => $idpregunta,
                   'Codigo' => $codigo,
                   'Respuesta' => $valortxt
               ];
               $_short->GuardarDetalle($datos);
           }
        }
        return " Encuesta enviada exitosamente";
    }
    //Imprimir el formulario
    $html = $_short->Armador($id);
    return $html;
}

add_shortcode("ENC","imprimirshortcode");
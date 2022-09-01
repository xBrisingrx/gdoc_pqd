<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Documentos extends CI_Controller {

	function __construct() {
    parent::__construct();
    $this->load->model('Persona_model');
    $this->load->model('Perfil_model');
    $this->load->model('Perfiles_Personas_model');
    $this->load->model('Perfiles_Atributos_model');
    $this->load->model('Atributos_Personas_model');
    $this->load->model('Renovaciones_Atributos_model');
    $this->load->model('Vehiculo_model');
    $this->load->model('Perfiles_Vehiculos_model');
    $this->load->model('Atributos_Vehiculos_model');
    $this->load->model('Renovaciones_Atributos_Vehiculo_model');
    $this->load->model('Atributo_model');
		date_default_timezone_set('America/Argentina/Buenos_Aires');
    if ( empty( $this->session->nombre_usuario ) ) {
	  	redirect('Login');
	  }
	}
	
  function index() {
    $data['tipo'] = 1;
    $data['personas'] = $this->Persona_model->get();
    $data['atributos'] = $this->Atributo_model->get('tipo', 1);
    $title['title'] = 'Documentos';
    $this->load->view('layout/header',$title);
    $this->load->view('layout/nav');
    $this->load->view('sistema/documentos/index',$data);
    $this->load->view('layout/footer');
  }

  function registro_vehiculos() {
    $data['tipo'] = 2;
    $data['vehiculos'] = $this->Vehiculo_model->get();
    $data['internos'] = $this->Vehiculo_model->get_internos();
    $data['atributos'] = $this->Atributo_model->get('tipo', 2);
    $data['aseguradoras'] = $this->DButil->get('aseguradoras', array( 'activo' => true ));
    $data['asignaciones'] = $this->DButil->get('asignaciones_vehiculo', array( 'activo' => true ));
    $title['title'] = 'Documentos';
    $this->load->view('layout/header',$title);
    $this->load->view('layout/nav');
    $this->load->view('sistema/documentos/vehiculos',$data);
    $this->load->view('layout/footer');
  }

  function get_perfiles($tipo, $id = null ){ /* Obtengo los perfiles de una persona o vehiculo */
    $model = ($tipo == 1 ) ? 'Perfiles_Personas_model' : 'Perfiles_Vehiculos_model';
    $tipo_id = ($tipo == 1 ) ? 'persona_id' : 'vehiculo_id';
    $data = array();
    if ($id != null) {
      $perfiles = $this->$model->get($tipo_id, $id);
      foreach ($perfiles as $p) {
        $row = array();
        $row[] = $p->nombre_perfil;
        $row[] = date('d-m-Y', strtotime($p->fecha_inicio_vigencia));
        $row[] = ' ';
        $data[] = $row;
      }
    } else {
      $data[] = array(' ',' ',' ');
    }
    echo json_encode( array("data" => $data) );
  }

  function get_atributos($tipo, $id = null) { /* Obtengo los atributos de una persona o vehiculo */
    $model = ($tipo == 1 ) ? 'Atributos_Personas_model' : 'Atributos_Vehiculos_model';
    $data = array();
    if ($id != null) {
      $atributos = $this->$model->get($id);
      // Acomodo el array que va a ir a la tabla de atributos
      foreach ($atributos as $attr) {
        $row = array();
        // Obtenemos la renovacion con vencimiento mas lejano
        $renovacion = $this->$model->get_ultima_renovacion($attr->id);
        $class_btn_cargar = ($attr->cargado) ? 'btn u-btn-orange btn-xs' : 'btn u-btn-indigo btn-xs' ;
        $icon = ($attr->cargado) ? 'fa fa-edit' : 'fa fa-plus';
        $row[] = $attr->nombre;
        $row[] = $attr->categoria;
        $row[] = ($attr->tiene_vencimiento) ? 'Si' : 'No';
        $row[] = ($attr->cargado && $attr->tiene_vencimiento) ? date('d-m-Y', strtotime($attr->fecha_vencimiento)) : ' ';
        $row[] = ($attr->permite_pdf) ? 'Si' : 'No';
        if (isset($renovacion->id)) {
          $row[] = '<button class="btn btn-sm u-btn-purple g-mr-5 g-mb-5" title="Ver archivos" onclick="modal_archivos('."'".$tipo."',".$renovacion->id.')" ><i class="fa fa-file"></i></button>';
        } else {
          $row[] = '';
        }
        $row[] = '<button class="'.$class_btn_cargar.'" title="Cargar documentacion" onclick="modal_cargar_atributo('."'".$attr->id."'".')" ><i class="'.$icon.'"></i></button></button> <button class="btn u-btn-red btn-xs ml-2" title="Eliminar" onclick="modal_eliminar_atributo('."'".$attr->id."'".')" ><i class="fa fa-trash"></i></button>';
        $row[] = $attr->cargado;
        $row[] = $attr->id;
        // Si esta cargado el atributo asignamos la cantidad de dias que faltan para q se venza
        $row[] = ($attr->cargado) ? $this->comparar_fechas( $attr->fecha_vencimiento ) : ' ' ;
        // Agregamos un row con las imagenes del vehiculo , no tienen el mismo tratamiento que los atributos por ese motivo lo agrego manual
        $data[] = $row;
      }
      if ($tipo == 2 && $id != NULL) {
        $data[] = array('Imagenes',' ',' ',' ',' ',' ','<button class="btn u-btn-pink btn-xs" title="Ver imagenes" 
          onclick="modal_imagenes('."'".$id."'".')" ><i class="fa fa-eye"></i></button>', '1' );
        $data[] = array('Seguros',' ',' ',' ',' ',' ','<button class="btn u-btn-indigo btn-xs" title="Listar seguros" 
          onclick="modal_seguros_vehiculos('."'".$id."'".')" ><i class="fa fa-shield"></i></button>', '1' );
        $data[] = array('Asignaciones',' ',' ',' ',' ',' ','<button class="btn u-btn-teal btn-xs" title="Listar asignaciones" 
          onclick="modal_asignaciones_vehiculos('."'".$id."'".')" ><i class="fa fa-location-arrow"></i></button>', '1' );
      }
    } else {
      $data[] = array(' ',' ',' ',' ',' ',' ',' ',' ');
    }
    echo json_encode(array("data" => $data));
  }

  function get_renovaciones_atributo($tipo, $atributo_tipo_id) { /* Obtengo las renovaciones de atributos de una persona o vehiculo */
    $model = ($tipo == 1 ) ? 'Atributos_Personas_model' : 'Atributos_Vehiculos_model';
    $renovaciones = $this->$model->get_renovaciones($atributo_tipo_id);
    $data = array();
    foreach ($renovaciones as $r) {
      $row = array();
      if ($r->tiene_vencimiento) {
        $row[] = date('d-m-Y', strtotime($r->fecha_renovacion));
        $row[] = date('d-m-Y', strtotime($r->fecha_vencimiento));
      } else {
        $row[] = '';
        $row[] = '';
      }
      $row[] = '<button class="btn btn-sm u-btn-purple g-mr-5 g-mb-5" title="Ver archivos" onclick="modal_archivos('."'".$tipo."',".$r->id.')" ><i class="fa fa-file"></i></button>';
      $datos_editar = $r->id.','.$r->fecha_renovacion.','.$r->fecha_vencimiento;
      if ($r->tiene_vencimiento) {
        $row[] = '<button class="btn btn-sm u-btn-orange g-mr-5 g-mb-5" title="Editar" onclick="editar_renovacion('."'".$datos_editar."'".')" ><i class="fa fa-edit"></i></button> <button class="btn btn-sm u-btn-red g-mr-5 g-mb-5" title="Eliminar" onclick="modal_eliminar_renovacion('."'".$r->id."'".')" ><i class="fa fa-trash"></i></button>';
      } else {
        $row[] = '<button class="btn btn-sm u-btn-red g-mr-5 g-mb-5" title="Eliminar" onclick="modal_eliminar_renovacion('."'".$r->id."'".')" ><i class="fa fa-trash"></i></button>';
      }
      $data[] = $row;
    }
    echo json_encode(array("data" => $data));
  }

  function cargar_atributo($tipo) {
    // Carga de documentacion atributos personas/vehiculos
    $folder = ($tipo == 1) ? 'renovaciones/personas' : 'renovaciones/vehiculos';
    $tabla = ($tipo == 1) ? 'renovaciones_atributos' : 'renovaciones_atributos_vehiculos';
    $model = ($tipo == 1) ? 'Renovaciones_Atributos_model' : 'Renovaciones_Atributos_Vehiculo_model';
    $atributo_tipo_id = ($tipo == 1) ? 'atributo_persona_id' : 'atributo_vehiculo_id';
    $response = [];
    $this->_validate_rules( $this->input->post('vence') );
    if ( $this->form_validation->run() == FALSE ) {
        echo json_encode( array( 'status' => 'error', 
                                 'msg' => validation_errors(), 
                                 'errors' => validation_errors(),
                                 'attr' => $this->input->post() ) );
    } else {
      $atributo = array(
        $atributo_tipo_id => $this->input->post('atributo_data_id'),
        'fecha_renovacion' => $this->input->post('fecha_renovacion'),
        'fecha_vencimiento' => $this->input->post('fecha_vencimiento'),
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
        'user_created_id' => $this->session->userdata('id'),
        'user_last_updated_id' => $this->session->userdata('id'),
        'activo' => true );
      $atributo = $this->security->xss_clean($atributo);
        $this->db->trans_begin();
          if ($this->$model->insert_entry($atributo)) {
            // Ya se cargo la informacion de la renovacion 
            $tabla_id = $this->DButil->get_last_id( $tabla );
            $estado_archivos = array('status' => TRUE);
            if ($_FILES['files']) {
              $estado_archivos = $this->Fileutil->subir_multiples_archivos( $_FILES['files'], $folder, $tabla, $tabla_id, 'renovacion' );
            }
            if ($estado_archivos['status'] === TRUE) {
              $response =  array( 'status' => 'success', 'msg' => 'Carga exitosa');
            } else {
              $response =  array( 'status' => 'error', 'msg' => $estado_archivos['error_msg'] );
            }
          } else {
            $response =  array( 'status' => 'error', 'msg' => 'No se pudo cargar el atributo' );
          }
      if ( ($this->db->trans_status() === FALSE) || ( $response['status'] == 'error' ) ) {
        $this->db->trans_rollback();
      }
      else {
        $this->db->trans_commit();
      }
      echo json_encode($response);    
    } 
  }

  function get_archivos($tipo ,$registro_id, $tabla = null){
    if ($tabla == 'null') {
      $tabla = ($tipo == 1) ? 'renovaciones_atributos' : 'renovaciones_atributos_vehiculos';
    }
    $carpeta = ($tipo == 1) ? 'personas' : 'vehiculos';
    $archivo = $this->DButil->get_archivo($tabla, $registro_id, "assets/uploads/renovaciones_atributos/$carpeta");
    echo json_encode( array(
      'archivo' => $archivo,
      'archivos' => $this->DButil->get('archivos', array('tabla' => $tabla, 'tabla_id' => $registro_id, 'activo' => true) ),
      'tabla' => $tabla
    ));
  }

  function agregar_archivos_a_renovacion(){
    $tabla = ( $this->input->post('tipo') == 1 ) ? 'renovaciones_atributos' : 'renovaciones_atributos_vehiculos';
    $folder = ($this->input->post('tipo') == 1) ? 'renovaciones/personas' : 'renovaciones/vehiculos';
    $tabla_id = $this->input->post('registro_id');
    $archivos_subidos = $this->Fileutil->subir_multiples_archivos( $_FILES['files'], $folder, $tabla, $tabla_id, 'renovacion' );
    if ($archivos_subidos['status'] === TRUE) {
      echo json_encode( array( 'status' => 'success', 'msg' => 'Carga exitosa', 'archivos_nuevos' => $archivos_subidos['archivos'], 'tabla' => $tabla, 'folder' => $folder, 'tipo' =>  $this->input->post('tipo')) );
    } else {
      echo json_encode( array( 'status' => 'error', 'msg' => $archivos_subidos['error_msg'], 'errores' => $archivos_subidos ) );
    }
  }

  function actualizar_renovacion_atributo($tipo) {
    $folder = ($tipo == 1) ? 'renovaciones/personas' : 'renovaciones/vehiculos';
    $tabla = ($tipo == 1) ? 'renovaciones_atributos' : 'renovaciones_atributos_vehiculos';
    $model = ($tipo == 1) ? 'Renovaciones_Atributos_model' : 'Renovaciones_Atributos_Vehiculo_model';
    $atributo_tipo_id = ($tipo == 1) ? 'atributo_persona_id' : 'atributo_vehiculo_id';
    $this->_validate_rules( $this->input->post('vence'), TRUE );
    if ( $this->form_validation->run() == FALSE ) {
        echo json_encode( array( 'status' => 'error', 
                                 'msg' => validation_errors(), 
                                 'errors' => validation_errors() ) );
    } else {
      $tabla_id = $this->input->post('id');
      $renovacion = array(
        'fecha_renovacion' => $this->input->post('fecha_renovacion'),
        'fecha_vencimiento' => $this->input->post('fecha_vencimiento'),
        'updated_at' => date('Y-m-d H:i:s'),
        'user_last_updated_id' => $this->session->userdata('id'));
      $renovacion = $this->security->xss_clean($renovacion);
      if ( $this->$model->update_entry($tabla_id, $renovacion) ) {
        $estado_archivos['status'] = TRUE;
        if ( isset($_FILES['files']) ) {
          $estado_archivos = $this->Fileutil->subir_multiples_archivos( $_FILES['files'], $folder, $tabla, $tabla_id, 'renovacion' );
        }
        if ($estado_archivos['status'] === TRUE) {
          echo json_encode( array( 'status' => 'success', 'msg' => 'Modificacion exitosa') );
        } else {
          echo json_encode( array( 'status' => 'error', 'msg' => $estado_archivos['errors_msg'] ) );
        }
      } else {
        echo json_encode( array( 'status' => 'error', 'msg' => 'No se pudo actualizar la informacion' ) );
      }
    }
  }

  function destroy_renovacion() {
    $model = ( $this->input->post('tipo') == 1 ) ? 'Renovaciones_Atributos_model' : 'Renovaciones_Atributos_Vehiculo_model';
    if ( $this->$model->destroy_entry( $this->input->post('id') ) ) {
      echo json_encode( array( 'status' => 'success', 'msg' => 'Renovacion eliminada' ) );
    } else {
      echo json_encode( array( 'status' => 'error', 'msg' => 'No se pudo eliminar la renovacion' ) );
    }
  } // end destroy renovacion

  function eliminar_archivos_a_renovacion(){
    if ($this->input->post('id') != '0') {
      if ( $this->DButil->destroy_entry('archivos',  $this->input->post('id') ) ) {
        echo json_encode( array( 'status' => 'success', 'msg' => 'Archivo eliminado' ) );
      } else {
        echo json_encode( array( 'status' => 'error', 'msg' => 'No se pudo eliminar el archivos' ) );
      }
    } else {
      echo json_encode( array( 'status' => 'info', 'msg' => 'Este archivo no puede ser eliminado' ) );
    }
  }

  function _validate_rules($vence, $edit = false) {
    if ($vence == 'Si') {
      $this->form_validation->set_rules('fecha_renovacion', 'fecha renovacion', 'required|callback_valid_date');
      $this->form_validation->set_rules('fecha_vencimiento', 'fecha vencimiento', 'required|callback_valid_date');
    }
    if ($edit) {
      $this->form_validation->set_rules('id', 'ID', 'required');
    } else {
      $this->form_validation->set_rules( 'atributo_data_id', 'Atributo', 'required');
    }
  }

  function valid_date($date){
    $data = strtotime( $date );
    $invalid_date = strtotime( "0001-01-01" );
    $invalid_date2 = strtotime("1980-01-01");
    if ( ($data == $invalid_date) || ( $data < $invalid_date2 ) ) {
      $this->form_validation->set_message('valid_date', 'La %s ingresada no es valida');
      return FALSE;
    } else {
      return TRUE;
    }
  }

  function comparar_fechas($fecha) {
    $fecha_aux = date('Y-m-d' , strtotime($fecha));
    $date1 = date_create($fecha_aux);
    $date2 =  new DateTime("now");
    $intervalo = date_diff($date2, $date1);
    return $intervalo->format('%R%a');
  }

}
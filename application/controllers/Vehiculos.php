<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vehiculos extends CI_Controller {

	public function __construct() {
    parent::__construct();
    $this->load->model('Vehiculo_model');
    $this->load->model('Empresa_model');
    $this->load->model('Perfil_model');
    // $this->load->model('Imagenes_Vehiculos_model');
    date_default_timezone_set('America/Argentina/Buenos_Aires');
    if ( empty( $this->session->nombre_usuario ) ) {
      redirect('Login');
    }
  }

  function index() {
    $title['title'] = 'Vehiculos';
    $data['vehiculos'] = $this->Vehiculo_model->get();
    $data['motivos_baja'] = $this->Vehiculo_model->get_motivos_baja();
    $data['empresas'] = $this->Empresa_model->get('tipo', 2);
    $data['perfiles'] = $this->Perfil_model->get('tipo', 2);
    $data['asignaciones'] = $this->DButil->get('asignaciones_vehiculo', array('activo' => true));
    $this->load->view('layout/header',$title);
    $this->load->view('layout/nav');
    $this->load->view('sistema/vehiculos/index',$data);
    $this->load->view('layout/footer');
  }

  function show($id){
    echo json_encode( $this->Vehiculo_model->get( 'id', $id )[0] );
  }

  function new() {
    $title['title'] = 'Alta de vehiculo';
    $data['empresas'] = $this->Empresa_model->get('tipo', '2');
    $data['asignaciones'] = $this->DButil->get('asignaciones_vehiculo', array('activo' => true));
    // $data['interno_sugerido'] = $this->Vehiculo_model->get_ultimo_interno() + 1;
    $this->load->view('layout/header',$title);
    $this->load->view('layout/nav');
    $this->load->view('sistema/vehiculos/new', $data);
    $this->load->view('layout/footer');
  }

  function create() {
  	$this->_validate_rules();
    if ( $this->form_validation->run() == FALSE ) {
      echo json_encode( array( 'status' => 'error', 'msg' => validation_errors() ) );
    } else {
      $vehiculo = array(
        'interno' => $this->input->post('interno'),
        'dominio' => $this->input->post('dominio'),
        'anio' => $this->input->post('anio'),
        'patentamiento' => $this->input->post('patentamiento'),
        'marca_id' => $this->input->post('marca_id'),
        'modelo_id' => $this->input->post('modelo_id'),
        'tipo_id' => $this->input->post('tipo_id'),
        'n_chasis' => $this->input->post('chasis'),
        'n_motor' => $this->input->post('motor'),
        'cant_asientos' => $this->input->post('asientos'),
        'empresa_id' => 1,
        'observaciones' => $this->input->post('observaciones'),
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
        'user_created_id' => $this->session->userdata('id'),
        'user_last_updated_id' => $this->session->userdata('id')
      );
      $vehiculo = $this->security->xss_clean($vehiculo);
      $response = [];
      $this->db->trans_begin();
        if ($this->Vehiculo_model->insert_entry('vehiculos', $vehiculo)) {
          $response = array( 'status' => 'success', 'msg' => 'Vehiculo creado' );
          $tabla_id = $this->Vehiculo_model->get_last_id();
          if ( !empty( $_FILES['imagenes']['name'][0] ) ) {
            $estado_archivos = $this->Fileutil->subir_multiples_archivos( $_FILES['imagenes'], 'vehiculos/img', 'vehiculos', $tabla_id, 'imagenes' );

            if ($estado_archivos['status'] === TRUE) {
              $response =  array( 'status' => 'success', 'msg' => 'Carga exitosa');
            } else {
              $response =  array( 'status' => 'error', 'msg' => $estado_archivos['error_msg'] );
            }
          }
          // if ( !empty( $this->input->post('asignacion') ) ) {
          //   $entry = array( 
          //     'vehiculo_id' => $last_id,
          //     'asignacion_id' => $this->input->post('asignacion'),
          //     'fecha_alta' => $this->input->post('fecha_alta_asignacion'),
          //     'created_at' => date('Y-m-d H:i:s'),
          //     'updated_at' => date('Y-m-d H:i:s'),
          //     'user_created_id' => $this->session->userdata('id'),
          //     'user_last_updated_id' => $this->session->userdata('id'));
          //   $this->DButil->insert_entry( 'vehiculos_asignaciones', $entry );
          // }
        } else {
          $this->session->set_flashdata('errors', 'No se pudo registrar el vehiculo.');
          $response = array( 'status' => 'error', 'msg' => 'Ocurrio un error al registrar el vehiculo' );
        }
      if ( ($this->db->trans_status() === FALSE) || ( $response['status'] == 'error' ) ) {
        $this->db->trans_rollback();
      }
      else {
        $this->db->trans_commit();
      }
      echo json_encode( $response );
    }
  }

  function edit($id){
    echo json_encode( $this->DButil->get_for_id('vehiculos', $id ) );
  }

  function update() {
    $this->_validate_rules( 'edit' );
    if ( $this->form_validation->run() == FALSE ) {
      echo json_encode( array('status'=>'error', 'msg'=>validation_errors()) );
    } else {
      $id = $this->input->post('id');
      $vehiculo = array(
        'interno' => $this->input->post('interno'),
        'dominio' => $this->input->post('dominio'),
        'anio' => $this->input->post('anio'),
        'patentamiento' => $this->input->post('patentamiento'),
        'marca_id' => $this->input->post('marca_id'),
        'modelo_id' => $this->input->post('modelo_id'),
        'tipo_id' => $this->input->post('tipo_id'),
        'n_chasis' => $this->input->post('chasis'),
        'n_motor' => $this->input->post('motor'),
        'cant_asientos' => $this->input->post('asientos'),
        'observaciones' => $this->input->post('observaciones'),
        'updated_at' => date('Y-m-d H:i:s'),
        'user_last_updated_id' => $this->session->userdata('id')
      );
      $data = $this->security->xss_clean($vehiculo);
      if ($this->Vehiculo_model->update_entry($id, $data)) {
        if ( !empty( $_FILES['imagenes']['name'][0] ) ) {
          $msg_images = $this->Fileutil->subir_multiples_archivos( $_FILES['imagenes'], 'vehiculos/img', 'vehiculos', $id, 'imagenes' );
        }
        echo json_encode( array('status'=>'success', 'msg'=> $msg_images) );
      } else {
        echo json_encode( array('status'=>'error', 'msg'=> 'No se pudieron actualizar los datos') );
      }
    }
  }

  function destroy() {
    $this->form_validation->set_rules('motivo_baja_vehiculo', 'Motivo', 'required');
    $this->form_validation->set_rules('detalle_baja_vehiculo', 'Detalle', 'required|min_length[5]');
    if ($this->form_validation->run() == FALSE) {
      echo json_encode( array( 'status' => 'error', 'msg'  => 'Faltan datos') );
    } else {
      $entry = array(
        'vehiculo_id' => $this->input->post('vehiculo_id'),
        'motivo_baja_id' => $this->input->post('motivo_baja_vehiculo'),
        'detalle' => $this->input->post('detalle_baja_vehiculo'),
        'fecha_baja' => $this->input->post('fecha_baja'),
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
        'user_created_id' => $this->session->userdata('id'),
        'user_last_updated_id' => $this->session->userdata('id')
      );
      $entry = $this->security->xss_clean($entry);
      if ($this->Vehiculo_model->destroy($entry)) {
        echo json_encode( array( 'status' => 'success', 'msg'  => 'Vehiculo dado de baja') );
      } else {
        echo json_encode( array( 'status' => 'error', 'msg'  => 'Ocurrio un error al eliminar el registro') );
      }
    } // end if form_validation
  } // end destroy_vehiculos

  function upload_images( $imagenes, $vehiculo_id ) {
    $response = array();
    $fileCount = count($_FILES['imagenes']['name']);
    $path = "assets/uploads/vehiculos/img/".date('Y').'/'.date('m').'/'.$vehiculo_id;
    if ( !file_exists( $path ) ) {
      mkdir( $path, 0777, true );
      $response[] = 'se creo la carpeta';
    }
    
    for ($i=0; $i < $fileCount; $i++) {
      $extension = pathinfo($imagenes['name'][$i])['extension'];
      $filename = $this->Fileutil->gererar_nombre_archivo($path, $extension);
      $imagen = array(
        'tabla' => 'vehiculos',
        'tabla_id' => $vehiculo_id,
        'columna' => 'imagenes',
        'path' => $path.'/'.$filename,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
        'user_created_id' => $this->session->userdata('id'),
        'user_last_updated_id' => $this->session->userdata('id')
      );
      if ($this->DButil->insert_entry('archivos',$imagen)) {
        if ( move_uploaded_file($imagenes['tmp_name'][$i], $path.'/'.$filename) ) {
          $response[] = 'Error: '.$imagenes['error'][$i];
        }
      }
    }
    return $response;
  }

  function ajax_upload_imagen(){
    $vehiculo_id = $this->input->post('vehiculo_id');
    $folder = "vehiculos/img/";
    $response = $this->Fileutil->subir_multiples_archivos( $_FILES['imagenes'], $folder, 'vehiculos', $vehiculo_id, 'imagenes' );
    if ($response['status'] === TRUE) {
      echo json_encode( array( 'status' => 'success', 'msg' => 'Imagenes cargadas', 'imagenes' => $response['archivos'] ) );
    } else {
      echo json_encode( array( 'status' => 'error', 'msg' => 'No se pudieron subir las imagenes' ) );
    }
  }

  function eliminar_imagen(){
    if ( $this->DButil->destroy_entry('archivos', $this->input->post('imagen_id')) ) {
      echo json_encode( array( 'status' => 'success', 'msg' => 'Imagenes eliminada' ) );
    } else {
      echo json_encode( array( 'status' => 'error', 'msg' => 'No se pudo eliminar la imagen' ) );
    }
  }

  function num_interno_libre( ) {
    // Verificamos que el num interno no este en uso
    $interno = $this->Vehiculo_model->get( 'interno', $_POST['interno'] );
    if ( !isset( $_POST['vehiculo_id'] ) ) {
      // Estamos creando un vehiculo
      if ( count($interno) > 0 ) {
        echo 'false';
      } else {
        echo 'true';
      }
    } else {
      // Estamos editando un vehiculo
      if ( ( count($interno) > 0 ) && ( $interno[0]->id != $_POST['vehiculo_id'] ) ) {
        echo 'false';
      } else {
        echo 'true';
      }
    }
  }

  function list() {
    $vehiculos = $this->Vehiculo_model->get();
    $data = array();

    foreach ($vehiculos as $v) {
      $row = array();
      $row[] = $v->interno;
      $row[] = $v->dominio;
      $row[] = $v->anio;
      $row[] = $v->marca;
      $row[] = $v->modelo;
      $row[] = $v->tipo;
      $row[] = $v->n_chasis;
      $row[] = $v->n_motor;
      $row[] = $v->cant_asientos;
      $row[] = $v->empresa;
      $row[] = $v->observaciones;
      if ($this->session->userdata('rol') == 1) {
      $row[] = '<button class="btn btn-sm u-btn-primary" title="Editar" onclick="modal_edit_vehiculo('."'".$v->id."'".')" >
                <i class="fa fa-edit"></i></button>
                <button class="btn btn-sm u-btn-red " title="Eliminar"
                onclick="modal_delete('."'".$v->id."'".')" ><i class="fa fa-trash-o"></i></button>';
      }
      
      $data[] = $row;
    }
    echo json_encode(array("data" => $data));
  }

  function get_imagenes($id) {
      echo json_encode( $this->DButil->get('archivos', array('tabla' => 'vehiculos', 
        'tabla_id' => $id, 'activo' => true)) );
  }

  function _validate_rules($edit = null){
  	$this->form_validation->set_rules('interno', 'interno', 'required');
    $this->form_validation->set_rules('dominio', 'dominio', 'required');
    $this->form_validation->set_rules('anio', 'anio', 'required');
    $this->form_validation->set_rules('marca_id', 'marca', 'required');
    $this->form_validation->set_rules('modelo_id', 'modelo', 'required');
    $this->form_validation->set_rules('tipo_id', 'tipo', 'required');
    if ($edit != null) {
      $this->form_validation->set_rules('id', 'ID', 'required');
    }
  }


/* Operaciones de modelos/marcas/tipos */
  function get_attr($table, $attr = null,$value = null) {
    if ( $value != null ) {
      $data = $this->Vehiculo_model->get_attr($table.'s_vehiculos', $attr, $value);
    } else {
      $data = $this->Vehiculo_model->get_attr($table.'s_vehiculos');
    }
    echo json_encode($data);
  }

  function list_attr($table) {
    $query = $this->Vehiculo_model->get_attr($table.'s_vehiculos');
    $data = array();

    foreach ($query as $q) {
      $row = array();

      $row[] = $q->nombre;

      if ($table == 'modelo') {
        $row[] = $q->nombre_marca;
      }
      $row[] = '<button class="btn btn-sm u-btn-primary mr-2" title="Editar" onclick="edit_attr_vehiculo('."'".$q->id."'".')" ><i class="fa fa-edit"></i></button> <button class="btn btn-sm u-btn-red" title="Eliminar" onclick="modal_delete_attr_vehiculo
      ('."'".$table."','".$q->id."'".')" ><i class="fa fa-trash-o"></i></button>';
      $data[] = $row;
    }
    echo json_encode(array("data" => $data));
  }

  function create_attr_vehiculo($table) {
    $this->_validate_attr($table.'s_vehiculos');

     if ($this->form_validation->run() == FALSE) { // Validamos. Si algo salio mal..
            echo json_encode(array('status' => 'error', 'msg' => validation_errors() )); //devolvemos los errores
        } else {
        	$data = array(
            'nombre' => $this->input->post('nombre'),
            'created_at' => date('Y-m-d H:i:s'),
						'updated_at' => date('Y-m-d H:i:s'),
						'user_created_id' => $this->session->userdata('id'),
						'user_last_updated_id' => $this->session->userdata('id')
          );
	        if ($table == 'modelo') {
	          $data['marca_vehiculo_id'] = $this->input->post('marca_id');
	       	}
        if ($this->DButil->insert_entry($table.'s_vehiculos', $data)) {
          echo json_encode( array('status' => 'success', 'msg' => 'Datos registrados') );
        } else {
          echo json_encode( array('status' => 'success', 'msg' => 'No se pudieron registrar los datos') );
        }
      }
  }

  function destroy_atributo($table, $id) { /* modelo o tipo vehiculo */
    if ($this->DButil->destroy_entry($table, $id)) {
      echo json_encode( array( 'status' => 'success', 'msg' => 'Registro eliminado' ) );
    } else {
      echo json_encode( array( 'status' => 'error', 'msg' => 'No se pudo eliminar el registro' ) );
    }
  }

  function destroy_marca($id) {
    if ($this->Vehiculo_model->destroy_marca($id)) {
      echo json_encode( array( 'status' => 'success', 'msg' => 'Marca eliminada' ) );
    } else {
      echo json_encode( array( 'status' => 'error', 'msg' => 'No se pudo eliminar la marca' ) );
    }
  }

  function _validate_attr($table){ 
    if ($table != 'modelos_vehiculos') {
    $this->form_validation->set_rules('nombre', 'Nombre', 'required|is_unique['.$table.'.nombre]|trim');
  } else {
    $this->form_validation->set_rules('nombre', 'Nombre', 'required|callback_modelo_vehiculo_unico|trim');
    $this->form_validation->set_rules('marca_id', 'Marca', 'required|trim');
  }

    $this->form_validation->set_message('is_unique', 'Este nombre ya esta en uso');
    $this->form_validation->set_message('required', 'Campo %s es obligatorio');
  }

  function modelo_vehiculo_unico() {
    // Verifico que el valor de un campo sea unico
    $name = $this->input->post('nombre');
    $marca_id =  $this->input->post('marca_id');
    // Si el modelo ingresado no se encuentra en la BD
    if ($this->Vehiculo_model->modelo_vehiculo_unico($marca_id, $name)) {
      return true;
    } else {
      $this->form_validation->set_message('modelo_vehiculo_unico', 'Este modelo ya se encuentra registrado');
      return false;
    }
  }

}
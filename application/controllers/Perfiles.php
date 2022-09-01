<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Perfiles extends CI_Controller {

	function __construct() {
	  parent::__construct();
	  $this->load->model('Perfil_model');
	  $this->load->model('Persona_model');
	  $this->load->model('Vehiculo_model');
	  date_default_timezone_set('America/Argentina/Buenos_Aires');
	  if ( empty( $this->session->nombre_usuario ) ) {
	  	redirect('Login');
	  }
	}

	function index($tipo){
		$title['title'] = ($tipo == 1) ? 'Perfiles de Personal' : 'Perfiles de vehiculos';
		$data['nombre_perfil'] = ($tipo == 1) ? 'Personal' : 'Vechículos';
		$data['tipo_perfil'] = $tipo;
		$data['perfiles'] = $this->Perfil_model->get( array('tipo',$tipo) );
		// $data['perfiles_atributos'] = $this->Perfiles_Atributos_model->get('tipo',$tipo);
		$this->load->view('layout/header',$title);
		$this->load->view('layout/nav');
		$this->load->view('sistema/perfiles/index',$data);
		$this->load->view('layout/footer');
	}

	function create() {
		$this->form_validation->set_rules('tipo', 'Tipo', 'required|callback_tipo_valido');
		$this->form_validation->set_rules('nombre', 'Nombre perfil', "required");
		if ($this->form_validation->run() == FALSE) {
			echo json_encode( array( 'status' => 'error', 'msg' => validation_errors() ) );
		} else {
			$tipo = $this->input->post('tipo');
			$nombre = $this->input->post('nombre');
			if (!$this->existe($nombre,$tipo)) {
				$perfil = array(
					'tipo' => $this->input->post('tipo'),
					'nombre' => $this->input->post('nombre'),
					'descripcion' => $this->input->post('descripcion'),
					'fecha_inicio_vigencia' => $this->input->post('fecha_inicio_vigencia'),
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s'),
					'user_created_id' => $this->session->userdata('id'),
					'user_last_updated_id' => $this->session->userdata('id')
				);
				$perfil = $this->security->xss_clean($perfil);
				if ( $this->Perfil_model->insert_entry($perfil) ) {
					echo json_encode( array('status' => 'success', 'msg' => 'Perfil registrado con éxito') );
				} else {
					echo json_encode( array('status' => 'error', 'msg' => 'Ocurrio un error, no se pudo crear el perfil') );
				}
			} else {
				echo json_encode( array('status' => 'info', 'msg' => 'Este perfil ya se encuentra registrado') );
			}
		}
	}

	function edit($id) {
		echo json_encode( $this->DButil->get_for_id( 'perfiles', $id ) );
	}

	function update() {
		$this->form_validation->set_rules('tipo', 'Tipo', 'required|callback_tipo_valido');
		$this->form_validation->set_rules('nombre', 'Nombre perfil', "required");
		$this->form_validation->set_rules('id', 'ID', "required");
		if ($this->form_validation->run() == FALSE) {
			echo json_encode( array( 'status' => 'error', 'msg' => validation_errors() ) );
		} else {
			$id = $this->input->post('id');
			$tipo = $this->input->post('tipo');
			$nombre = $this->input->post('nombre');
			if (!$this->existe($nombre,$tipo, $id)) {
				$perfil = array(
					'tipo' => $this->input->post('tipo'),
					'nombre' => $this->input->post('nombre'),
					'descripcion' => $this->input->post('descripcion'),
					'fecha_inicio_vigencia' => $this->input->post('fecha_inicio_vigencia'),
					'updated_at' => date('Y-m-d H:i:s'),
					'user_last_updated_id' => $this->session->userdata('id')
				);
				$perfil = $this->security->xss_clean($perfil);
				if ($this->Perfil_model->update_entry($id, $perfil)) {
					echo json_encode( array('status' => 'success', 'msg' => 'Perfil modificado con éxito') );
				} else {
					echo json_encode( array('status' => 'error', 'msg' => 'Ocurrio un error, no se pudo modificar el perfil') );
				}
			} else {
				echo json_encode( array('status' => 'info', 'msg' => 'Este perfil ya se encuentra registrado') );
			}
		}
	}

	function destroy($id) {
		if ($this->Perfil_model->destroy($id)) {
			echo json_encode( array('status' => 'success', 'msg' => 'Perfil eliminado') );
		} else {
			echo json_encode( array('status' => 'error', 'msg' => 'No se pudo eliminar el perfil') );
		}
	}

	function ajax_list_perfiles($tipo) {
		$perfiles = $this->DButil->get( 'perfiles', array('tipo'=> $tipo) );
		$data = array();
		foreach ($perfiles as $p) {
			if ( $this->session->userdata('rol') == 1 ) {
				if ($p->activo) {
					$botones = '<button class="btn btn-sm u-btn-primary " title="Editar" onclick="edit_profile('."'".$p->id."'".')" ><i class="fa fa-edit"></i></button> <button class="btn btn-sm u-btn-red"  title="Eliminar" onclick="modal_destroy_profile('."'".$p->id."'".')" ><i class="fa fa-trash-o"></i></button>';
				} else {
					$botones = '<button class="btn btn-sm u-btn-primary" title="Editar" onclick="edit_profile('."'".$p->id."'".')" disabled ><i class="fa fa-edit"></i></button> <button class="btn btn-sm u-btn-aqua" title="Reactivar" onclick="reactivate_profile('."'".$p->id."'".')" ><i class="fa fa-retweet"></i></button>';
				}
			} else {
				$botones = ' ';
			}

			$row = array(
				'nombre' => $p->nombre.' '.$tipo,
				'descripcion' => $p->descripcion,
				'fecha_inicio_vigencia' => ( $p->fecha_inicio_vigencia != '0000-00-00' ) ? date('d-m-Y', strtotime($p->fecha_inicio_vigencia)) : '',
				'fecha_baja' => (!$p->activo) ? date('d-m-Y', strtotime($p->updated_at)) : '',
				'acciones' => $botones
			);

			$data[] = $row;
		}
		echo json_encode( array( "data" => $data ) );
	}

	function ajax_get_profiles($tipo) {
		$profiles = $this->Perfil_model->get('tipo',$tipo);
		echo ( json_encode($profiles) );
	}

	function existe($name = null, $tipo = null, $id = null){
		// chequeo que el atributo no exista
		// si llega una variable como null es q estoy consultando via ajax
		if ($name == null || $tipo == null) {
			$entry = $this->Perfil_model->existe( $_POST['nombre'], $_POST['tipo'] );
			if ($_POST['id'] != '') {
				echo ( isset($entry) && $entry->id != $_POST['id'] ) ? 'false' : 'true';
			} else {
				echo ( isset($entry) != NULL ) ? 'false' : 'true';
			}
		} else {
			$entry = $this->Perfil_model->existe($name, $tipo);

			if ($id != null) {
				// Si el id no es nulo es que estamos editando
				return ( isset($entry) && $entry->id != $id);
			} else {
				return ( isset($entry) != NULL );
			}
		}
	}

	function tipo_valido($value) {
		if ($value == 1 || $value == 2) {
			return TRUE;
		} else {
			$this->form_validation->set_message('tipo_valido', 'Se envio un valor invalido');
      return FALSE;
		}
	} 

}
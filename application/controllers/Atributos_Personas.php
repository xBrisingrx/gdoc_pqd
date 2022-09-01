<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Atributos_Personas extends CI_Controller {

	public function __construct() {
	  parent::__construct();
	  $this->load->model('Atributos_Personas_model');
	  date_default_timezone_set('America/Argentina/Buenos_Aires');
	  if ( empty( $this->session->nombre_usuario ) ) {
	  	redirect('Login');
	  }
	}

	function create(){
		$entry = array(
			'persona_id' => $this->input->post('data_id'),
			'atributo_id' => $this->input->post('atributo_id'),
			'personalizado' => true,
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s'),
			'user_created_id' => $this->session->userdata('id'),
      'user_last_updated_id' => $this->session->userdata('id'),
			'activo'   => TRUE,
			'cargado' => false
		);
		$entry = $this->security->xss_clean($entry);
		if ( !$this->existe($entry['persona_id'], $entry['atributo_id']) ) {
			if ($this->Atributos_Personas_model->insert_personalizado($entry)) {
				echo json_encode( array('status' => 'success', 'msg' => 'Atributo asignado') );
			} else {
				echo json_encode( array('status' => 'error', 'msg' => 'No se pudo asignar el atributo') );
			}
		} else {
			echo json_encode( array('status' => 'error', 'msg' => 'Esta persona ya tiene asignado este atributo') ); 
		}
	}

	function disable(){
		if( $this->Atributos_Personas_model->disable_personalizado( $this->input->post('atributo_data_id') ) ){
			echo json_encode( array('status' => 'success', 'msg' => 'Atributo eliminado') );
		} else {
			echo json_encode( array('status' => 'error', 'msg' => 'No se pudo eliminar el atributo') );
		}
	}

	function existe($persona_id, $atributo_id){
		if( $this->Atributos_Personas_model->existe( $atributo_id, $persona_id ) ){
			$entry = $this->Atributos_Personas_model->get_atributo_persona($persona_id, $atributo_id);
			return $entry->activo;
		} else {
			return false;
		}
	}
}
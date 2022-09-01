<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Atributos_Vehiculos extends CI_Controller {

	public function __construct() {
	  parent::__construct();
	  $this->load->model('Atributos_Vehiculos_model');
	  date_default_timezone_set('America/Argentina/Buenos_Aires');
	  if ( empty( $this->session->nombre_usuario ) ) {
	  	redirect('Login');
	  }
	}

	function create(){
		$entry = array(
			'vehiculo_id' => $this->input->post('data_id'),
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
		if ( !$this->existe($entry['vehiculo_id'], $entry['atributo_id']) ) {
			if ($this->Atributos_Vehiculos_model->insert_personalizado($entry)) {
				echo json_encode( array('status' => 'success', 'msg' => 'Atributo asignado') );
			} else {
				echo json_encode( array('status' => 'error', 'msg' => 'No se pudo asignar el atributo') );
			}
		} else {
			echo json_encode( array('status' => 'info', 'msg' => 'Esta vehiculo ya tiene asignado este atributo') ); 
		}
	}

	function disable(){
		if( $this->Atributos_Vehiculos_model->disable_personalizado( $this->input->post('atributo_data_id') ) ){
			echo json_encode( array('status' => 'success', 'msg' => 'Atributo eliminado') );
		} else {
			echo json_encode( array('status' => 'error', 'msg' => 'No se pudo eliminar el atributo') );
		}
	}

	function existe($vehiculo_id, $atributo_id){
		if( $this->Atributos_Vehiculos_model->exists( $atributo_id, $vehiculo_id ) ){
			$entry = $this->Atributos_Vehiculos_model->get_atributo_vehiculo($vehiculo_id, $atributo_id);
			return $entry->activo;
		} else {
			return false;
		}
	}
}
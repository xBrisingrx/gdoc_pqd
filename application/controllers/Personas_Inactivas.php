<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Personas_Inactivas extends CI_Controller {

	# Controlador para el historial de personas dados de baja

	function __construct() {
	  parent::__construct();
	  $this->load->model('Persona_model');
	  $this->load->model('Personas_Inactivas_model');
	  date_default_timezone_set('America/Argentina/Buenos_Aires');
	}

	function index() {
		$title['title'] = 'Personas inactivas';
		$data['personal_inactivo'] = $this->Personas_Inactivas_model->get();
		$this->load->view('layout/header',$title);
		$this->load->view('layout/nav');
		$this->load->view('sistema/personas_inactivas/index', $data);
		$this->load->view('layout/footer');
	}

	function get($persona_id) {
		// Obtengo el historial de bajas/reactivacion de un persona
		$personas = $this->Personas_Inactivas_model->get_historial_persona($persona_id);
		$data = array();
		foreach ($personas as $persona) {
			$row = array();
			$row[] = $persona->motivo;
			$row[] = date( 'd-m-Y', strtotime($persona->fecha_baja) );
			$row[] = $persona->detalle;
			$row[] = ($persona->fecha_alta != null) ? date( 'd-m-Y', strtotime($persona->fecha_alta) ) : ' ';
			$row[] = $persona->detalle_alta;

			$data[] = $row;
		}
		echo json_encode( array('data'=>$data) );
	}
	function reactivar( )  {
    // Lo que hacemos es activar el persona y el registro de personas baja lo desactivamos
    $this->form_validation->set_rules('id', 'identificador de registro', 'required');
    $this->form_validation->set_rules('persona_id', 'identificador de persona', 'required');
    $this->form_validation->set_rules('detalle', 'Detalle', 'required|min_length[5]');
    $this->form_validation->set_rules('fecha_alta', 'Fecha', 'required');
    if ($this->form_validation->run() == FALSE) {
      echo json_encode( array( 'status' => 'error', 'msg'  => 'Faltan datos') );
    } else {
    	$entry_id = $_POST['id'];
	    $entry = array( 
	      'persona_id' => $_POST['persona_id'],
	      'detalle_alta' => $_POST['detalle'],
	      'fecha_alta' => $_POST['fecha_alta'],
	      'updated_at' => date('Y-m-d H:i:s'),
	      'user_last_updated_id' => $this->session->userdata('id'),
	      'activo' => false
	    );
	    $data = $this->security->xss_clean($entry);
	    if ( $this->Persona_model->reactivar( $entry_id, $data ) ) {
	      echo json_encode( array( 'status' => 'success', 'msg'  => 'Persona reactivada') );
	    } else {
	     echo json_encode( array( 'status' => 'error', 'msg'  => 'No se pudo reactivar a la persona') );
	    }
    }
  }
// Obtengo los datos de mi tabla y los devuelvo en formato json para insertar en datatables
	function ajax_list() {
		$personas = $this->Personas_Inactivas_model->get();
		$data = array();
		foreach ($personas as $persona) {

			$row = array();
			$row[] = $persona->id;
			$row[] = $persona->interno;
			$row[] = $persona->dominio;
			$row[] = $persona->anio;
			$row[] = "";
			$row[] = $persona->tipo;
			$row[] = $persona->empresa;
			$row[] = $persona->motivo;
			$row[] = $persona->detalle;
			$row[] = $persona->fecha_baja;
			$row[] = '<button class="btn btn-sm u-btn-primary g-mr-10" title="Editar" onclick="edit_user('."'".$u->id."'".')" ><i class="fa fa-edit"></i></button> <button class="btn btn-sm u-btn-red" title="Eliminar" onclick="delete_user('."'".$u->id."'".')" ><i class="fa fa-trash-o"></i></button>';

			$data[] = $row;
		}

	  $output = array("data" => $data);
		echo json_encode($output);
	}

	function corregir_personas_inactivas() {
		$personas = $this->Persona_model->getBajas();
		foreach( $personas as $persona) {
			$entry = array(
				'persona_id' => $persona->id,
				'motivo_baja_id' => $persona->motivo_baja_id,
				'fecha_baja' => $persona->fecha_baja,
				'detalle' => ' ',
				'user_created_id' => $this->session->userdata('id')
			);
			$this->Personas_Inactivas_model->insert_entry($entry);
		}
	}

}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vehiculos_Inactivos extends CI_Controller {

	# Controlador para el historial de vehiculos dados de baja

	function __construct() {
	  parent::__construct();
	  $this->load->model('Vehiculo_model');
	  $this->load->model('Vehiculo_inactivo_model');
	  date_default_timezone_set('America/Argentina/Buenos_Aires');
	}

	function index() {
		$title['title'] = 'Vehiculos inactivos';
		$data['vehiculos'] = $this->Vehiculo_inactivo_model->get();
		$this->load->view('layout/header',$title);
		$this->load->view('layout/nav');
		$this->load->view('sistema/vehiculos_inactivos/index', $data);
		$this->load->view('layout/footer');
	}

	function get($vehiculo_id) {
		// Obtengo el historial de bajas/reactivacion de un vehiculo
		$vehiculos = $this->Vehiculo_inactivo_model->get_historial_vehiculo($vehiculo_id);
		$data = array();
		foreach ($vehiculos as $vehiculo) {
			$row = array();
			$row[] = $vehiculo->motivo;
			$row[] = date( 'd-m-Y', strtotime($vehiculo->fecha_baja) );
			$row[] = $vehiculo->detalle;
			$row[] = ($vehiculo->fecha_alta != null) ? date( 'd-m-Y', strtotime($vehiculo->fecha_alta) ) : ' ';
			$row[] = $vehiculo->detalle_alta;

			$data[] = $row;
		}
		echo json_encode( array('data'=>$data) );
	}
	function reactivar( )  {
    // Lo que hacemos es activar el vehiculo y el registro de vehiculos baja lo desactivamos
    $this->form_validation->set_rules('id', 'identificador de registro', 'required');
    $this->form_validation->set_rules('vehiculo_id', 'identificador del vehiculo', 'required');
    $this->form_validation->set_rules('detalle', 'Detalle', 'required|min_length[5]');
    $this->form_validation->set_rules('fecha_alta', 'Fecha', 'required');
    if ($this->form_validation->run() == FALSE) {
      echo json_encode( array( 'status' => 'error', 'msg'  => 'Faltan datos') );
    } else {
    	$entry_id = $_POST['id'];
	    $entry = array( 
	      'vehiculo_id' => $_POST['vehiculo_id'],
	      'detalle_alta' => $_POST['detalle'],
	      'fecha_alta' => $_POST['fecha_alta'],
	      'updated_at' => date('Y-m-d H:i:s'),
	      'user_last_updated_id' => $this->session->userdata('id'),
	      'activo' => false
	    );

	    if ( $this->Vehiculo_model->reactivar( $entry_id, $entry ) ) {
	      echo json_encode( array( 'status' => 'success', 'msg'  => 'Vehiculo reactivado') );
	    } else {
	     echo json_encode( array( 'status' => 'error', 'msg'  => 'No se pudo reactivar el vehiculo') );
	    }
    }
  }
// Obtengo los datos de mi tabla y los devuelvo en formato json para insertar en datatables
	function ajax_list() {
		$vehiculos = $this->Vehiculo_inactivo_model->get();
		$data = array();
		foreach ($vehiculos as $vehiculo) {

			$row = array();
			$row[] = $vehiculo->id;
			$row[] = $vehiculo->interno;
			$row[] = $vehiculo->dominio;
			$row[] = $vehiculo->anio;
			$row[] = "$vehiculo->marca $vehiculo->modelo";
			$row[] = $vehiculo->tipo;
			$row[] = $vehiculo->empresa;
			$row[] = $vehiculo->motivo;
			$row[] = $vehiculo->detalle;
			$row[] = $vehiculo->fecha_baja;
			$row[] = '<button class="btn btn-sm u-btn-primary g-mr-10" title="Editar" onclick="edit_user('."'".$u->id."'".')" ><i class="fa fa-edit"></i></button> <button class="btn btn-sm u-btn-red" title="Eliminar" onclick="delete_user('."'".$u->id."'".')" ><i class="fa fa-trash-o"></i></button>';

			$data[] = $row;
		}
		echo json_encode(array("data" => $data));
	}

}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Periodo_entregas extends CI_Controller {

	// Agrupamos la ropa que se entrega por periodos

	function __construct() {
	  parent::__construct();
	  $this->load->model('Periodo_entrega_ropa_model');
	  date_default_timezone_set('America/Argentina/Buenos_Aires');
	}

	function index(){
		$title['title'] = 'Entregas';
		$this->load->view('layout/header',$title);
		$this->load->view('layout/nav');
		$this->load->view('sistema/periodo_entregas/index');
		$this->load->view('layout/footer');
	}

	function list() {
		$periodo_entregas = $this->DButil->get('periodo_entregas', array('activo' => true) );
		$data = array();
		foreach ($periodo_entregas as $a) {
			$row = array();
			$row[] = $a->nombre;
			$row[] = $a->duracion;
			$row[] = $a->descripcion;
			if ($this->session->userdata('rol') == 1) {
				$row[] = '<button class="btn btn-sm u-btn-blue mr-2" title="Agregar prenda" onclick="modal_add_prenda('."'".$a->id."'".')" ><i class="fa fa-plus"></i></button> <button class="btn btn-sm u-btn-purple mr-2" title="Ver prendas" onclick="modal_show_prenda('."'".$a->id."'".')" ><i class="fa fa-eye"></i></button>';
			} else {
				$row[] = '';
			}
			$data[] = $row;
		}
		echo json_encode(array("data" => $data));
	}

	function create(){
		$this->_validate_rules();
		if ( $this->form_validation->run() == FALSE ) {
			echo json_encode(array('status' => 'error', 'msg' => validation_errors() )); 
		} else {
			if ( !$this->existe( $this->input->post('nombre') )  ) {
				$entry = array(
					'nombre' => $this->input->post('nombre'),
					'duracion' => $this->input->post('duracion'),
					'descripcion' => $this->input->post('descripcion'),
					'created_at' => date('Y-m-d H:i:s'),
	        'updated_at' => date('Y-m-d H:i:s'),
	        'user_created_id' => $this->session->userdata('id'),
	        'user_last_updated_id' => $this->session->userdata('id')
				);
				$entry = $this->security->xss_clean($entry);
				if ($this->DButil->insert_entry('periodo_entregas', $entry)) {
					 echo json_encode(array('status' => 'success', 'msg' => 'Registro exitoso' )); 
				} else {
					echo json_encode(array('status' => 'error', 'msg' => 'Error al crear aseguradora' )); 
				}
			} else {
				echo json_encode(array('status' => 'existe', 'msg' => 'Esta aseguradora ya se encuentra registrada' )); 
			}
		}
	}

	function edit($id){
		echo json_encode( $this->DButil->get_for_id('periodo_entregas', $id) );
	}

	function update(){
		$this->_validate_rules(true);
		if ( $this->form_validation->run() == FALSE ) {
			echo json_encode(array('status' => 'error', 'msg' => validation_errors() )); 
		} else {
			$id = $this->input->post('id');
			$nombre = $this->input->post('nombre');
			if ( !$this->existe( $nombre, $id ) ) {
				$entry = array(
					'nombre' => $nombre,
					'duracion' => $this->input->post('duracion'),
					'descripcion' => $this->input->post('descripcion'),
					'updated_at' => date('Y-m-d H:i:s'),
					'user_last_updated_id' => $this->session->userdata('id')
				);
				$entry = $this->security->xss_clean($entry);
				if ($this->DButil->update_entry('periodo_entregas', $id, $entry)) {
					 echo json_encode(array('status' => 'success', 'msg' => 'Datos actualizados' )); 
				} else {
					echo json_encode(array('status' => 'error', 'msg' => 'No se pudieron actualizar los datos' )); 
				}
			} else {
				echo json_encode(array('status' => 'existe', 'msg' => 'Esta aseguradora ya se encuentra registrada' )); 
			}
		}
	}

	function destroy($id){
		if ($this->DButil->destroy_entry('periodo_entregas', $id)) {
			echo json_encode(array('status' => 'success', 'msg' => 'Aseguradora eliminada' )); 
		} else {
			echo json_encode(array('status' => 'error', 'msg' => 'No se pudo eliminar la aseguradora' )); 
		}
	}

	function _validate_rules( $edit = false ){
		$this->form_validation->set_rules('nombre', 'Nombre', 'required');
		$this->form_validation->set_rules('duracion', 'Duración', 'required');
		if ($edit) {
			$this->form_validation->set_rules('id', 'id', 'required');
		}
	}

	function existe($nombre, $id = null ){
		return $this->DButil->existe('periodo_entregas', 'nombre', $nombre, $id);
	}

}

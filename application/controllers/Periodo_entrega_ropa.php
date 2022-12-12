<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Periodo_entrega_ropa extends CI_Controller {

	// administro la relacion entre periodo entrega y ropa

	function __construct() {
	  parent::__construct();
	  date_default_timezone_set('America/Argentina/Buenos_Aires');
	}

	function index(){
		$title['title'] = 'Periodo de entrega - ropa';
		$this->load->view('layout/header',$title);
		$this->load->view('layout/nav');
		$this->load->view('sistema/periodo_entega_ropa/index');
		$this->load->view('layout/footer');
	}

	function list(){
		$periodo_entega_ropa = $this->DButil->get('periodo_entega_ropa', array('activo' => true) );
		$data = array();
		foreach ($periodo_entega_ropa as $a) {
			$row = array();
			$row[] = $a->periodo_entrega;
			$row[] = $a->ropa;
			if ($this->session->userdata('rol') == 1) {
				$row[] = '<button class="btn btn-sm u-btn-primary mr-2" title="Editar" onclick="modal_edit_periodo_entega_ropa('."'".$a->id."'".')" ><i class="fa fa-edit"></i></button> <button class="btn btn-sm u-btn-red " title="Eliminar" onclick="modal_destroy_periodo_entega_ropa('."'".$a->id."'".')" ><i class="fa fa-trash-o"></i></button>';
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
					'ropa_id' => $this->input->post('ropa_id'),
					'periodo_entrega_id' => $this->input->post('periodo_entrega_id'),
					'created_at' => date('Y-m-d H:i:s'),
	        'updated_at' => date('Y-m-d H:i:s'),
	        'user_created_id' => $this->session->userdata('id'),
	        'user_last_updated_id' => $this->session->userdata('id')
				);
				$entry = $this->security->xss_clean($entry);
				if ($this->DButil->insert_entry('periodo_entega_ropa', $entry)) {
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
		echo json_encode( $this->DButil->get_for_id('periodo_entega_ropa', $id) );
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
					'ropa_id' => $this->input->post('ropa_id'),
					'periodo_entrega_id' => $this->input->post('periodo_entrega_id'),
					'updated_at' => date('Y-m-d H:i:s'),
					'user_last_updated_id' => $this->session->userdata('id')
				);
				$entry = $this->security->xss_clean($entry);
				if ($this->DButil->update_entry('periodo_entega_ropa', $id, $entry)) {
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
		if ($this->DButil->destroy_entry('periodo_entega_ropa', $id)) {
			echo json_encode(array('status' => 'success', 'msg' => 'Aseguradora eliminada' )); 
		} else {
			echo json_encode(array('status' => 'error', 'msg' => 'No se pudo eliminar la aseguradora' )); 
		}
	}

	function _validate_rules( $edit = false ){
		$this->form_validation->set_rules('ropa_id', 'Ropa', 'required');
		$this->form_validation->set_rules('periodo_entrega_id', 'Pack', 'required');
		if ($edit) {
			$this->form_validation->set_rules('id', 'id', 'required');
		}
	}

	function existe($nombre, $id = null ){
		return $this->DButil->existe('periodo_entega_ropa', 'nombre', $nombre, $id);
	}



}

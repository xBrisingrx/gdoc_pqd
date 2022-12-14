<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ropa extends CI_Controller {

	function __construct() {
	  parent::__construct();
	  date_default_timezone_set('America/Argentina/Buenos_Aires');
	}

	function index(){
		$title['title'] = 'Ropa';
		$this->load->view('layout/header',$title);
		$this->load->view('layout/nav');
		$this->load->view('sistema/ropa/index');
		$this->load->view('layout/footer');
	}

	function list(){
		$ropa = $this->DButil->get('ropa', array('activo' => true) );
		$data = array();
		foreach ($ropa as $a) {
			$row = array();
			$row[] = $a->nombre;
			$row[] = $a->descripcion;
			if ($this->session->userdata('rol') == 1) {
				$row[] = '<button class="btn btn-sm u-btn-primary mr-2" title="Editar" onclick="modal_edit_ropa('."'".$a->id."'".')" ><i class="fa fa-edit"></i></button> <button class="btn btn-sm u-btn-red " title="Eliminar" onclick="modal_destroy_ropa('."'".$a->id."'".')" ><i class="fa fa-trash-o"></i></button>';
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
					'created_at' => date('Y-m-d H:i:s'),
	        'updated_at' => date('Y-m-d H:i:s'),
	        'user_created_id' => $this->session->userdata('id'),
	        'user_last_updated_id' => $this->session->userdata('id')
				);
				$entry = $this->security->xss_clean($entry);
				if ($this->DButil->insert_entry('ropa', $entry)) {
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
		echo json_encode( $this->DButil->get_for_id('ropa', $id) );
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
					'updated_at' => date('Y-m-d H:i:s'),
					'user_last_updated_id' => $this->session->userdata('id')
				);
				$entry = $this->security->xss_clean($entry);
				if ($this->DButil->update_entry('ropa', $id, $entry)) {
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
		if ($this->DButil->destroy_entry('ropa', $id)) {
			echo json_encode(array('status' => 'success', 'msg' => 'Aseguradora eliminada' )); 
		} else {
			echo json_encode(array('status' => 'error', 'msg' => 'No se pudo eliminar la aseguradora' )); 
		}
	}

	function _validate_rules( $edit = false ){
		$this->form_validation->set_rules('nombre', 'Nombre', 'required');
		if ($edit) {
			$this->form_validation->set_rules('id', 'id', 'required');
		}
	}

	function existe($nombre, $id = null ){
		return $this->DButil->existe('ropa', 'nombre', $nombre, $id);
	}

}

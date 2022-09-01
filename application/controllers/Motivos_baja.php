<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Motivos_baja extends CI_Controller {

	function __construct() {
	  parent::__construct();
	  $this->load->model('Motivos_baja_model');
	  date_default_timezone_set('America/Argentina/Buenos_Aires');
	}

	function administrar($tipo = null) {
		if ($tipo != null) {
			$title['title'] = ($tipo == 1) ? 'Motivos baja de Personal' : 'Motivos baja de vehiculos';
			$data['title_table'] = ($tipo == 1) ? 'Listado motivos de baja de Personal' : 'Listado motivos de baja de vehiculos';
			$data['label_tipo_motivo'] = ($tipo == 1) ? 'Personal' : 'Vechículos';
			$data['tipo_motivo'] = $tipo;
			$data['motivos'] = $this->Motivos_baja_model->get('tipo', $tipo);
			$this->load->view('layout/header',$title);
			$this->load->view('layout/nav');
			$this->load->view('sistema/motivos_baja/index',$data);
			$this->load->view('layout/footer');
		} else {
			$this->load->view('layout/header');
			$this->load->view('layout/nav');
			$this->load->view('layout/404');
			$this->load->view('layout/footer');
		}
	}

	function get( $id = null ) {
		if ( $id != null) {
			$motivos = $this->Motivos_baja_model->get('id', $id);
		} else {
			$motivos = $this->Motivos_baja_model->get();
		}

		echo json_encode($motivos);
	}

	function ajax_list($tipo) {
		$motivos = $this->Motivos_baja_model->get('tipo', $tipo);
		$data = array();

		foreach ($motivos as $e) {
			$row = array();
			$row[] = $e->motivo;
			$row[] = $e->descripcion;
			if ($this->session->userdata('rol') == 1) {
				$row[] = '<button class="btn btn-sm u-btn-primary mr-2" title="Editar" onclick="edit_motivo('."'".$e->id."'".')" ><i class="fa fa-edit"></i></button> <button class="btn btn-sm u-btn-red" title="Eliminar" onclick="delete_motivo('."'".$e->id."'".')" ><i class="fa fa-trash-o"></i></button>';
			}
			$data[] = $row;
		}
		echo json_encode(array("data" => $data));
	}

	function create() {
		$this->form_validation->set_rules('motivo', 'Motivo', 'required');
		$this->form_validation->set_rules('tipo', 'Tipo motivo', 'required');
		if ($this->form_validation->run() == FALSE) {
			echo json_encode( array( 'status' => 'error', 'msg' => validation_errors() ) );
		} else {
			$tipo = $this->input->post('tipo');
			$motivo = $this->input->post('motivo');
			if (!$this->existe_motivo($tipo, $motivo)) {
				$entry = array(
					'tipo' => $tipo,
					'motivo' => $motivo,
					'descripcion' => $this->input->post('descripcion'),
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s'),
					'user_created_id' => $this->session->userdata('id'),
					'user_last_updated_id' => $this->session->userdata('id')
				);
				$entry = $this->security->xss_clean($entry);
				if ($this->Motivos_baja_model->insert_entry($entry)) {
					echo json_encode(array('status' => 'success', 'msg' => 'Motivo de baja registrado'));
				} else {
					echo json_encode(array('status' => 'error', 'msg' => 'Error: no se pudo registrar el motivo de baja'));
				}
			} else {
				echo json_encode(array('status' => 'existe', 'msg' => 'Este motivo ya se encuentra registrado'));
			}
		}
	}

	function edit($id) {
		$motivo = $this->Motivos_baja_model->get('id',$id);
		echo json_encode($motivo);
	}

	function update() {
		$this->form_validation->set_rules('motivo', 'Motivo', 'required');
		$this->form_validation->set_rules('tipo', 'Tipo motivo', 'required');
		$this->form_validation->set_rules('id', 'id', 'required');
		if ($this->form_validation->run() == FALSE) {
			echo json_encode( array( 'status' => 'error', 'msg' => validation_errors() ) );
		} else {
			$id = $this->input->post('id');
			$tipo = $this->input->post('tipo');
			$motivo = $this->input->post('motivo');
			if (!$this->existe_motivo($tipo, $motivo, $id)) {
				$entry = array(
					'tipo' => $tipo,
					'motivo' => $motivo,
					'descripcion' => $this->input->post('descripcion'),
					'updated_at' => date('Y-m-d H:i:s'),
					'user_last_updated_id' => $this->session->userdata('id')
				);
				$entry = $this->security->xss_clean($entry);
				if ($this->Motivos_baja_model->update_entry($id, $entry)) {
					echo json_encode(array('status' => 'success', 'msg' => 'Motivo de baja registrado'));
				} else {
					echo json_encode(array('status' => 'error', 'msg' => 'Error: no se pudo registrar el motivo de baja'));
				}
			} else {
				echo json_encode(array('status' => 'existe', 'msg' => 'Este motivo ya se encuentra registrado'));
			}
		}
	}

	function existe_motivo( $tipo, $motivo , $id = null ) {
		$motivo = $this->Motivos_baja_model->existe_motivo($tipo, $motivo);
		if ($id == null) {
			return  isset($motivo->id);
		} else {
			return (  isset($motivo->id) ) && ( $motivo->id != $id );
		}
	}

  function destroy( $id ) {
    if ( $this->Motivos_baja_model->destroy( $id ) ) {
      echo json_encode(array('status' => 'success', 'msg' => 'Motivo eliminado'));
    } else {
      echo json_encode(array('status' => 'error', 'msg' => 'No se pudo eliminar el motivo'));
    }
  }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Empresas extends CI_Controller {

	public function __construct() {
	  parent::__construct();
	  $this->load->model('Empresa_model');
	  date_default_timezone_set('America/Argentina/Buenos_Aires');
	}

	function administrar( $tipo = null ) {
		if ($tipo != null) {
			$title['title'] = ($tipo == 1) ? 'Empresas de Personal' : 'Empresas de vehiculos';
			$data['label_tipo_empresa'] = ($tipo == 1) ? 'personal' : 'vechículos';
			$data['tipo_empresa'] = $tipo;
			$data['empresas'] = $this->Empresa_model->get('tipo', $tipo);
			$this->load->view('layout/header',$title);
			$this->load->view('layout/nav');
			$this->load->view('sistema/empresas/index',$data);
			$this->load->view('layout/footer');
		} else {
			$this->load->view('layout/header');
			$this->load->view('layout/nav');
			$this->load->view('layout/404');
			$this->load->view('layout/footer');
		}
	}

	function get( $id = null, $tipo = null ) {
		if ( $id != null) {
			$empresas = $this->Empresa_model->get('id', $id);
		} else {
			$empresas = $this->Empresa_model->get();
		}
		echo json_encode($empresas);
	}

	function ajax_list($tipo) {
		$empresas = $this->Empresa_model->get('tipo', $tipo);
		$data = array();
		foreach ($empresas as $e) {
			$row = array();
			$row[] = $e->nombre;
			$row[] = $e->descripcion;
			if ($this->session->userdata('rol') == 1) {
				$row[] = '<button class="btn btn-sm u-btn-primary mr-2" title="Editar" onclick="edit_empresa('."'".$e->id."'".')" ><i class="fa fa-edit"></i></button> <button class="btn btn-sm u-btn-red " title="Eliminar" onclick="delete_empresa('."'".$e->id."'".')" ><i class="fa fa-trash-o"></i></button>';
			} else {
				$row[] = '';
			}
			$data[] = $row;
		}
		echo json_encode(array("data" => $data));
	}

	function create() {
		$this->form_validation->set_rules('nombre', 'Nombre', "required");
		$this->form_validation->set_rules('tipo', 'Tipo', "required");
		if ($this->form_validation->run() == FALSE) {
			echo json_encode( array( 'status' => 'error', 'msg' => validation_errors() ) );
		} else {
			$tipo = $this->input->post('tipo');
			$nombre = $this->input->post('nombre');
			if ( !$this->existe_empresa($tipo, $nombre) ) {
				$empresa = array(
					'tipo' => $tipo,
					'nombre' => $nombre,
					'descripcion' => $this->input->post('descripcion'),
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s'),
					'user_created_id' => $this->session->userdata('id'),
					'user_last_updated_id' => $this->session->userdata('id')
				);
				$empresa = $this->security->xss_clean($empresa);
				if ($this->Empresa_model->insert_entry($empresa)) {
					echo json_encode(array('status' => 'success', 'msg' => 'Empresa creada con exito'));
				} else {
					echo json_encode(array('status' => 'error', 'msg' => 'No se pudo guardar la informacion'));
				}
			} else {
				echo json_encode(array('status' => 'existe', 'msg' => 'Esta empresa ya se encuentra registrada'));
			}
		}
	} // end create

	function edit($id) {
		echo json_encode( $this->Empresa_model->get('id',$id) );
	}

	function update() {
		$this->form_validation->set_rules('nombre', 'Nombre', "required");
		$this->form_validation->set_rules('tipo', 'Tipo', "required");
		$this->form_validation->set_rules('id', 'ID', "required");
		if ($this->form_validation->run() == FALSE) {
			echo json_encode( array( 'status' => 'error', 'msg' => validation_errors() ) );
		} else {
			$id = $this->input->post('id');
			$tipo = $this->input->post('tipo');
			$nombre = $this->input->post('nombre');
			if (!$this->existe_empresa($tipo, $nombre, $id)) {
				$empresa = array(
					'nombre' => $nombre,
					'descripcion' => $this->input->post('descripcion'),
					'updated_at' => date('Y-m-d H:i:s'),
					'user_last_updated_id' => $this->session->userdata('id')
				);
				$empresa = $this->security->xss_clean($empresa);
				if ($this->Empresa_model->update_entry($id, $empresa)) {
					echo json_encode(array('status' => 'success', 'msg' => 'Datos actualizados'));
				} else {
					echo json_encode(array('status' => 'error', 'msg' => 'No se pudo guardar la informacion'));
				}
			} else {
				echo json_encode(array('status' => 'existe', 'msg' => 'Esta empresa ya existe'));
			}
		}
	}

	function existe_empresa( $tipo, $nombre , $id = null ) {
		$empresa = $this->Empresa_model->existe_empresa($tipo, $nombre);
		if ($id == null) {
			return  isset($empresa->id);
		} else {
			return ( isset($empresa->id) ) && ( $empresa->id != $id );
		}
	}

  function destroy( $id ) {
    if ( $this->Empresa_model->destroy( $id ) ) {
      echo json_encode( array('status'=>'success', 'msg'=>'Empresa dada de baja') );
    } else {
      echo json_encode( array('error'=>'error', 'msg'=>'No se pudo dar de baja la empresa') );
    }
  }
}

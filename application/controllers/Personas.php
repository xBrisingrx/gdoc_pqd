<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Personas extends CI_Controller {
	
	function __construct() {
	  parent::__construct();
	  date_default_timezone_set('America/Argentina/Buenos_Aires');
	  $this->load->model(array('Empresa_model', 'Persona_model','Motivos_baja_model', 'Perfil_model'));
    $this->load->library('upload');
    if ( empty( $this->session->nombre_usuario ) ) {
	  	redirect('Login');
	  }
	}

	function index() {
		$title['title'] = 'Personas';
		$data = array(
				'personas' => $this->Persona_model->getData('activo', true),
				'empresas' => $this->Empresa_model->get('tipo', 1),
				'motivos_baja' => $this->Motivos_baja_model->get('tipo', 1),
				'perfiles' => $this->Perfil_model->get('tipo', 1)
		);
		$this->load->view('layout/header',$title);
		$this->load->view('layout/nav');
		$this->load->view('sistema/personas/index',$data);
		$this->load->view('layout/footer');
		// $this->backup_semanal();
	}

	function show($id){ 
		echo json_encode( $this->DButil->get_for_id('personas', $id) );
	}

	function new( $error = null ) {
		if ($this->session->userdata('rol') != 1) {
			$this->index();
		} else {
			$title['title'] = 'Alta de persona';
			$data['empresas'] = $this->Empresa_model->get('tipo', 1);
			// Le mando el legajo sugerido , seria el ultimo + 1
			$ultimo_legajo = $this->Persona_model->get_ultimo_legajo() + 1;
			$data['ultimo_legajo'] = $ultimo_legajo;
			$data['error'] = $error;
			$this->load->view('layout/header',$title);
			$this->load->view('layout/nav');
			$this->load->view('sistema/personas/new',$data);
			$this->load->view('layout/footer');
		}
	}

	function create() {
		if ($this->session->userdata('rol') != 1) {
			$this->index();
		} else {
			// Rules
			$this->_validate_rules();

			// Mensajes personalizados

			if ( $this->form_validation->run() == FALSE ) {
				echo json_encode( validation_errors() );
			} else {
				$persona = $this->_set_persona_data();
				$response = [];
				$this->db->trans_begin();
					if ($this->Persona_model->insert_entry($persona)) {
						$tabla_id = $this->DButil->get_last_id( 'personas' );
						$estado_archivos = array('status' => TRUE);
		        if ( isset( $_FILES['pdf_dni'] ) ) {
		          $estado_archivos = $this->Fileutil->subir_archivo( $_FILES['pdf_dni'], 'personas', 'personas', $tabla_id, 'pdf_dni' );
		        }
		        if ( isset( $_FILES['pdf_cuil'] ) ) {
		          $estado_archivos = $this->Fileutil->subir_archivo( $_FILES['pdf_cuil'], 'personas', 'personas', $tabla_id, 'pdf_cuil' );
		        }
		        if ( isset( $_FILES['pdf_alta_temprana'] ) ) {
		          $estado_archivos = $this->Fileutil->subir_archivo( $_FILES['pdf_alta_temprana'], 'personas', 'personas', $tabla_id, 'pdf_alta_temprana' );
		        }

		        if ( $estado_archivos['status'] == FALSE ) {
		        	$response = array( 'status' => 'error', 'msg' => $estado_archivos['error_msg'] );
		        } else {
		        	$response = array( 'status' => 'success', 'msg' => 'Persona registrada con exito' );
		        }
					} else {
						$response = array( 'status' => 'error','msg' => 'No se pudo registrar la informacion' );
					}
				if ( ($this->db->trans_status() === FALSE) || ( $response['status'] == 'error' ) ) {
	        $this->db->trans_rollback();
	      }
	      else {
	        $this->db->trans_commit();
	      }
	      echo json_encode( $response );
				}
		}
	}

	function edit( $id ) {
		if ($this->session->userdata('rol') != 1) {
			$this->index();
		} else {
			$persona = $this->Persona_model->get('id',$id);
			$title['title'] = 'Edición de persona';
			$data['persona'] = $persona[0];
			$data['empresas'] = $this->Empresa_model->get('tipo', 1);
			$this->load->view('layout/header',$title);
			$this->load->view('layout/nav');
			$this->load->view('sistema/personas/edit',$data);
			$this->load->view('layout/footer');
		}
	}

	function update() {
		if ($this->session->userdata('rol') != 1) {
			$this->index();
		} else {
			$this->_validate_rules(TRUE);
			if ( $this->form_validation->run() == FALSE ) {
				echo json_encode( validation_errors() );
			} else {
				$id = $this->input->post('id');
				$persona = $this->_set_persona_data();
				$response = [];
				$estado_archivos = array('status' => TRUE);
				$this->db->trans_begin();
				if ($this->Persona_model->update_entry($id, $persona)) {
					if ( isset( $_FILES['pdf_dni'] ) ) {
	          if ( $this->Persona_model->tiene_archivo( $id,'pdf_dni') ) {
	          	$archivo_id = $this->db->get_where('archivos', array( 'tabla' => 'personas', 'tabla_id' => $id, 'columna' => 'pdf_dni', 'activo' => true ))->row()->id;
	          	$estado_archivos = $this->Fileutil->actualizar_archivo( $archivo_id, $_FILES['pdf_dni'] );
	          } else {
	          	$estado_archivos = $this->Fileutil->subir_archivo( $_FILES['pdf_dni'], 'personas', 'personas', $id, 'pdf_dni' );
	          }
	        }
	        if ( isset( $_FILES['pdf_cuil'] ) ) {
	          if ( $this->Persona_model->tiene_archivo($id,'pdf_cuil') ) {
	          	$archivo_id = $this->db->get_where('archivos', array( 'tabla' => 'personas', 'tabla_id' =>$id, 'columna' => 'pdf_cuil', 'activo' => true ))->row()->id;
	          	$estado_archivos = $this->Fileutil->actualizar_archivo( $archivo_id, $_FILES['pdf_cuil'] );
	          } else {
	          	$estado_archivos = $this->Fileutil->subir_archivo( $_FILES['pdf_cuil'], 'personas', 'personas', $id, 'pdf_cuil' );
	          }
	        }
	        if ( isset( $_FILES['pdf_alta_temprana'] ) ) {
	          if ( $this->Persona_model->tiene_archivo($id,'pdf_alta_temprana') ) {
	          	$archivo_id = $this->db->get_where('archivos', array( 'tabla' => 'personas', 'tabla_id' =>$id, 'columna' => 'pdf_alta_temprana', 'activo' => true ))->row()->id;
	          	$estado_archivos = $this->Fileutil->actualizar_archivo( $archivo_id, $_FILES['pdf_alta_temprana'] );
	          } else {
	          	$estado_archivos = $this->Fileutil->subir_archivo( $_FILES['pdf_alta_temprana'], 'personas', 'personas', $id, 'pdf_alta_temprana' );
	          }
	        }

	        if ($estado_archivos['status']) {
	        	$response = array( 'status' => 'success', 'data' => $persona );
	        } else {
	        	$response = array( 'status' => 'error', 'data' => $estado_archivos['error_msg'] );
	        }
				} else {
					$response = array( 'status' => 'error','msg' => 'No se pudieron actualizar los datos');
				}

				if ( ($this->db->trans_status() === FALSE) || ( $response['status'] == 'error' ) ) {
	        $this->db->trans_rollback();
	      }
	      else {
	        $this->db->trans_commit();
	      }
		      echo json_encode( $response );
				}
			
		}
	}

	function subir_pdf() {
		$ruta = 'assets/uploads';
		if (!file_exists($ruta)) {
			if (!mkdir($ruta, 0777, true)) {
				$response['status'] = false;
				$response['msg'] = 'Error al crear la carpeta => '.$ruta;
				return $response;
			}
		}
		$config['upload_path']  	= $ruta;
		$config['allowed_types']	= 'pdf|jpg|png|jpeg';
		$config['max_size']     	= 30024;
		$config['overwrite']			= true;
		$config['file_name']			= $_POST['nombre'];

		$this->upload->initialize($config);
		if ( ! $this->upload->do_upload('file')) {
			echo json_encode( $this->upload->display_errors() );
		}
		else {
			echo 'success';
		}
	}

	function destroy() {
    $this->form_validation->set_rules('motivo_baja_id', 'Motivo', 'required');
    $this->form_validation->set_rules('persona_id', 'Persona', 'required');
    $this->form_validation->set_rules('detalle', 'Detalle', 'required|min_length[5]');
    if ($this->form_validation->run() == FALSE) {
      echo json_encode( array( 'status' => 'error', 'msg'  => 'Faltan datos') );
    } else {
      $entry = array(
	      'persona_id' => $this->input->post('persona_id'),
	      'motivo_baja_id' => $this->input->post('motivo_baja_id'),
	      'detalle' => $this->input->post('detalle'),
	      'fecha_baja' => $this->input->post('fecha_baja'),
	      'user_created_id' => $this->session->userdata('id'),
	      'user_last_updated_id' => $this->session->userdata('id'),
	      'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
      );
      $entry = $this->security->xss_clean($entry);
      if ($this->Persona_model->destroy($entry)) {
        echo json_encode( array( 'status' => 'success', 'msg'  => 'Persona dada de baja') );
      } else {
        echo json_encode( array( 'status' => 'error', 'msg'  => 'Ocurrio un error al eliminar el registro') );
      }
    } // end if form_validation
  } // end destroy

  function _validate_rules($is_edit = false){
  	if ($is_edit) {
  		$this->form_validation->set_rules('id', 'ID', 'required');
  	}
		$this->form_validation->set_rules('n_legajo', 'Legajo', 'required');
		$this->form_validation->set_rules('nombre', 'Nombre', 'min_length[3]|required');
		$this->form_validation->set_rules('dni', 'DNI', 'required');
		$this->form_validation->set_rules('apellido', 'Apellido', 'required');

		// Valores seteados
		set_value('legajo');
		set_value('nombre');
		set_value('apellido');
		set_value('DNI');
		set_value('telefono');
		set_value('domicilio');
		set_value('cuit');
		set_value('pdf_dni');
		set_value('num_tramite');
  }

  function _set_persona_data() {
  	$persona = array(
			'n_legajo' => $this->input->post('n_legajo'),
			'apellido' => $this->input->post('apellido'),
			'nombre'   => $this->input->post('nombre'),
			'email'   => $this->input->post('email'),
			'dni' 		 => $this->input->post('dni'),
			'dni_tiene_vencimiento' => ( $this->input->post('dni_tiene_vencimiento') == 'true' ) ? true : false,
			'fecha_vencimiento_dni' => $this->input->post('fecha_vencimiento_dni'),
			'num_tramite' => $this->input->post('num_tramite'),
			'cuil' => $this->input->post('cuil'),
			'fecha_nacimiento' => $this->input->post('fecha_nacimiento'),
			'nacionalidad' => $this->input->post('nacionalidad'),
			'domicilio' => $this->input->post('domicilio'),
			'telefono' => $this->input->post('telefono'),
			'empresa_id' => 1,
			'fecha_inicio_actividad' => $this->input->post('fecha_inicio_actividad'),
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s'),
			'user_created_id' => $this->session->userdata('id'),
			'user_last_updated_id' => $this->session->userdata('id')
		);
		$persona = $this->security->xss_clean($persona);
		return $persona;
  }

  
}
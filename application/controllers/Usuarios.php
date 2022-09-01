<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios extends CI_Controller {

	public function __construct() {
	  parent::__construct();
	  $this->load->model('Usuario_model');
	  date_default_timezone_set('America/Argentina/Buenos_Aires');
	}

	public function index() {
		$title['title'] = 'Usuarios';
		$this->load->view('layout/header',$title);
		$this->load->view('layout/nav');
		$this->load->view('sistema/usuarios/index');
		$this->load->view('layout/footer');
	}

	public function create() {
		if ($this->session->userdata('rol') != 1) {
			echo json_encode( array('status' => 'info', 'msg' => 'No tiene permisos para realizar esta accion') );
		} else {
			$this->form_validation->set_rules('nombre_usuario', 'Nombre de usuario', 'required|is_unique[usuarios.nombre_usuario]');
			$this->form_validation->set_rules('nombre', 'Nombre', 'required');
			$this->form_validation->set_rules('rol', 'Rol', 'required');
			$this->form_validation->set_rules('password', 'Contraseña', "required");
			$this->form_validation->set_rules('passconf', 'Contraseña', "required|matches[password]");
			if ($this->form_validation->run() == FALSE) {
				echo json_encode( array( 'status' => 'error', 'msg' => validation_errors() ) );
			} else {
				$usuario = array(
					'nombre'=> $this->input->post('nombre'),
					'email'	 	 => $this->input->post('email'),
					'rol'	 	 => $this->input->post('rol'),
					'nombre_usuario' 		 => $this->input->post('nombre_usuario'),
					'password' 		 => $this->encryption->encrypt($this->input->post('password')),
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s'),
				);
				$usuario = $this->security->xss_clean($usuario);
				if ($this->Usuario_model->insert_entry($usuario)) {
					echo json_encode( array('status' => 'success', 'msg' => 'Usuario registrado') );
				} else {
					echo json_encode( array('status' => 'error', 'msg' => 'No se pudo registrar al usuario') );
				}
			} // create user
		} // if rol_user
	} 

	public function edit($id) {
		if ($this->session->userdata('rol') != 1) {
			echo json_encode( array('status' => 'info', 'msg' => 'No tiene permisos para realizar esta accion') );
		} else {
			$usuario = $this->Usuario_model->get('id',$id);
			echo json_encode( array( 'status' => 'success', 'usuario' => $usuario ) );
		}
	}

	function update() {
		if ($this->session->userdata('rol') != 1) {
			echo json_encode( array('status' => 'info', 'msg' => 'No tiene permisos para realizar esta accion') );
		} else {
			$id = $this->input->post('id');
			$username = $this->input->post('nombre_usuario');
			$this->form_validation->set_rules('nombre_usuario', 'Nombre de usuario', "required|callback_nombre_usuario_ocupado[$username , $id]");
			$this->form_validation->set_rules('nombre', 'Nombre', 'required');
			$this->form_validation->set_rules('rol', 'Rol', 'required');
			if ( !empty( $this->input->post('password') ) ) {
				$this->form_validation->set_rules('password', 'Contraseña', "required");
				$this->form_validation->set_rules('passconf', 'Contraseña', "required|matches[password]");
			}
			if ($this->form_validation->run() == FALSE) {
				echo json_encode( array( 'status' => 'error', 'msg' => validation_errors() ) );
				} else {
				$usuario = array();
				$usuario['nombre']= $this->input->post('nombre');
				$usuario['email']	= $this->input->post('email');
				$usuario['rol']	= $this->input->post('rol');
				$usuario['nombre_usuario'] = $this->input->post('nombre_usuario');
				if ( !empty( $this->input->post('password') ) ) {
					$usuario['password'] 	= $this->encryption->encrypt($this->input->post('password'));
				}
				$usuario['updated_at']= date('Y-m-d H:i:s');
				
				$usuario = $this->security->xss_clean($usuario);
				if ($this->Usuario_model->update_entry($id, $usuario)) {
					echo json_encode( array( 'status' => 'success', 'msg' => 'Datos del usuario actualizados' ) );
				} else {
					echo json_encode( array( 'status' => 'error', 'msg' => 'No se pudieron actualizar los datos' ) );
				}
			} // form validation success
		} // if user_rol
	} // update

	function destroy($id) {
		if ($this->session->userdata('rol') != 1) {
			echo json_encode( array( 'status' => 'info', 'msg' => 'No tiene permisos para realizar esta acción' ) );
		} elseif( $this->session->userdata('id') != $id ) {
			if ( $this->Usuario_model->destroy($id) ) {
				echo json_encode( array( 'status' => 'success', 'msg' => 'Usuario eliminado' ) );
			} else {
				echo json_encode( array( 'status' => 'error', 'msg' => 'Ocurrio un error, no se pudo eliminar al usuario' ) );
			}
		} else {
			echo json_encode( array( 'status' => 'info', 'msg' => 'No puedes eliminar a tu mismo usuario' ) );
		}
	}


// Obtengo los datos de mi tabla y los devuelvo en formato json para insertar en datatables
	public function ajax_list() {
		$usuarios = $this->Usuario_model->get();
		$data = array();

		foreach ($usuarios as $u) {
			$row = array();
			$row[] = $u->nombre;
			$row[] = $u->email;
			$row[] = $u->nombre_usuario;
			$row[] = ($u->rol == 1) ? 'Administrador' : 'Consulta';
			if ($this->session->userdata('rol') == 1) {
				$row[] = '<button class="btn btn-sm u-btn-primary g-mr-4" title="Editar" onclick="edit_user('."'".$u->id."'".')" ><i class="fa fa-edit"></i></button> <button class="btn btn-sm u-btn-red" title="Eliminar" onclick="delete_user('."'".$u->id."'".')" ><i class="fa fa-trash-o"></i></button>';
			} else {
				$row[] = '';
			}
			$data[] = $row;
		}
		echo json_encode( array("data" => $data) );
	}

	function nombre_usuario_ocupado($username, $id ){
		$id = str_replace(array("$username", " ", ","), '', $id);
		$usuario = $this->Usuario_model->nombre_usuario_ocupado($username);
		if ( isset($usuario) && ( $usuario->id != $id ) ) {
			$this->form_validation->set_message('nombre_usuario_ocupado', "El nombre de usuario ya pertenece a otro usuario"  );
	    return FALSE;
		} else {
			return TRUE;
		}
	}

}
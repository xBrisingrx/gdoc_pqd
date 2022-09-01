<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	function __construct() {
	    parent::__construct();
	    $this->load->model('Usuario_model');
	}

	function index() {
		if ( !empty( $this->session->userdata('nombre_usuario') ) ) {
			redirect( base_url() );
		} else {
			$title['title'] = 'Login';
			$data['token'] = $this->token();
			$this->load->view('layout/header',$title);
			$this->load->view('sistema/login/login', $data);
		}
	}

	function login() {
		// Verifico que el token se haya generado en el sistema
		if ($this->input->post('token') && $this->input->post('token') == $this->session->userdata('token')) {
			// Verificamos que ingrese los datos
			$this->form_validation->set_rules('username', 'Nombre de usuario', 'required');
			$this->form_validation->set_rules('password', 'Contraseña', 'required');
			if ($this->form_validation->run() == FALSE) {
				// Si no ingresa usuario o contraseña
				$this->session->set_flashdata('error-login', 'Fallo form');
				redirect('Login');
			} else {
				// Ingreso el usuario y la contraseña
				$usuario = array(
									 'nombre_usuario' => $this->input->post('username'),
									 'password' => $this->input->post('password')
				);
				$usuario = $this->security->xss_clean($usuario);

				if ($this->Usuario_model->datosCorrectos($usuario)) {
					// El usuario existe y los datos son correctos
					$datosSesion = $this->Usuario_model->getDataSesion($usuario);
					$this->session->set_userdata($datosSesion);
					$this->session->set_flashdata('success', 'Bienvenido al sistema');
					redirect(base_url());
				} else {
					// El usuario no existe o los datos no son correctos
					$this->session->set_flashdata('error-login', 'Usuario y/ó contraseña incorrecto');
					redirect('Login');
				}
			}
		} else {
			// El usuario no existe o los datos no son correctos
			$this->session->set_flashdata('error-login', 'Token invalido');
			redirect('Login');
		}
	}

	function logout() {
		$this->session->sess_destroy();
		redirect('Login');
	}

	function token() {
		$token = md5(uniqid(rand(),true));
		$this->session->set_userdata('token',$token);
		return $token;
	}

	function key() {
		// Genero una cadena aleatoria para usar como llave de encriptacion
		$key = bin2hex($this->encryption->create_key(16));
		echo $key;
	}
}
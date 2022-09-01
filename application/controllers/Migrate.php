<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Migrate extends CI_Controller
{

  public function index() {
    $this->load->library('migration');

    if ($this->migration->current() === FALSE) {
      echo $this->migration->error_string();
    } else {
      echo $this->migration->current();
    }
  }

  function data_default(){
    $usuario = array(
          'nombre'=> 'mssi',
          'email'    => 'soporte@maurosampaoli.com.ar',
          'rol'    => 1,
          'nombre_usuario'     => 'mssi',
          'password'     => $this->encryption->encrypt('mauro01David'),
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
        );
    if ($this->DButil->insert_entry('usuarios',$usuario)) {
      echo "Exito";
    } else {
      echo ":(";
    }
  }

}
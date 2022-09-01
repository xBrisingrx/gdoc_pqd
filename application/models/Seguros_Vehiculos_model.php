<?php

class Seguros_Vehiculos_model extends CI_Model {

  // Tipo atributo: 1 para personal 2 para vehiculos
  protected $table = 'seguros_vehiculos';
  function __construct() {
    parent::__construct();
    $this->load->model('Perfiles_Atributos_model');
    $this->load->model('Atributos_Personas_model');
  }

  function get_seguros_vehiculo($vehiculo_id){
    $this->db->select('vehiculos.id as vehiculo_id, sv.id, aseguradoras.nombre, sv.poliza, sv.fecha_alta, sv.vencimiento')
              ->from("$this->table AS sv")
                ->join('vehiculos', 'vehiculos.id = sv.vehiculo_id')
                ->join('aseguradoras', 'aseguradoras.id = sv.aseguradora_id')
                  ->where('sv.activo', TRUE)
                  ->where('vehiculos.id', $vehiculo_id);
    return $this->db->get()->result();
  }

  function insert_entry($attribute) {
    return $this->db->insert($this->table, $attribute);
  }

  function update_entry($id, $perfil) {
    $this->db->where('id', $id);
    return $this->db->update($this->table, $perfil);
  }

  function destroy($id){

  }

  function existe( $name, $tipo, $atributo_id = null ) {
    $this->db->select('id, nombre, tipo')
                ->from($this->table)
                  ->where('nombre', $name)
                  ->where('tipo', $tipo);
    if ($atributo_id != null) {
      $this->db->where('id !=', $atributo_id);
    }
    $query = $this->db->get();
    return ( $query->num_rows() > 0 );
  }
}
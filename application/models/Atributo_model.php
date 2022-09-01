<?php

class Atributo_model extends CI_Model {

  // Tipo atributo: 1 para personal 2 para vehiculos
  protected $table = 'atributos';
  function __construct() {
    parent::__construct();
    $this->load->model('Perfiles_Atributos_model');
    $this->load->model('Atributos_Personas_model');
  }

  function get($attr = null, $valor = null) {
    $this->db->select('*')
              ->from($this->table)
                ->where('atributos.activo', true);
    if($attr != null and $valor != null) {
      $this->db->where('atributos.'.$attr, $valor);
    }
    $this->db->order_by('nombre', 'asc');
    return $this->db->get()->result();
  }

  function get_nombre_id( $tipo, $ids = null ) {
    $atributo_ids = $ids;

    $this->db->select('id, nombre')
               ->from($this->table)
                 ->where('activo', TRUE)
                 ->where('tipo', $tipo);

    if ( $ids != null ) {
      $this->db->where('id = ', $ids[0]);
      for ($i=1; $i < count($ids) ; $i++) { 
        $this->db->or_where('id = ', $ids[$i]);
      }
    }

    $this->db->order_by('nombre', 'asc');
    return $this->db->get()->result();
  }

  function insert_entry($attribute) {
    return $this->db->insert('atributos', $attribute);
  }

  function update_entry($id, $perfil) {
    $this->db->where('id', $id);
    return $this->db->update('atributos', $perfil);
  }

  function destroy($id){
    /* Si desactivo un atributo, tengo que desactivarlo en todos los perfiles y personas/vehiculos asociados a esos perfiles */ 
    $atributo = $this->db->get_where('atributos', array('id' => $id))->row();
    $tipo_nombre = ( $atributo->tipo == 1 ) ? 'personas' : 'vehiculos';
    $date = date('Y-m-d H:i:s');
    $user_id = $this->session->userdata('id');
    $atributo->activo = false;
    $atributo->user_last_updated_id = $user_id;
    $atributo->updated_at = $date;
    $this->db->trans_begin();
      $this->db->where('id', $id);
      $this->db->update('atributos', $atributo);
      // selecciono los perfiles con este atributo
      $perfiles_afectados = $this->db->get_where( 'perfiles_atributos', array( 'atributo_id' => $id, 'activo' => true ) )->result();
      // selecciono a los registros (personas/vehiculos) que tengan asignados este atributo
      $registros_afectados = $this->db->get_where( "atributos_$tipo_nombre", array( 'atributo_id' => $id, 'activo' => true ) )->result();

      foreach($perfiles_afectados as $p){
        $p->activo = false;
        $p->user_last_updated_id = $user_id;
        $p->updated_at = $date;
        $this->db->where('id', $p->id);
        $this->db->update('perfiles_atributos', $p);
      }

      foreach($registros_afectados as $r){
        $r->activo = false;
        $r->user_last_updated_id = $user_id;
        $r->updated_at = $date;
        $this->db->where('id', $r->id);
        $this->db->update("atributos_$tipo_nombre", $r);
      }
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return false;
    } else {
      $this->db->trans_commit();
      return true;
    }
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
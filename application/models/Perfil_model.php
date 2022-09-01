<?php 

class Perfil_model extends CI_Model {
// Tipo perfil: 1 para personal 2 para vehiculos
  protected $table = 'perfiles';
  function __construct() {
    parent::__construct();
    $this->load->model('Perfiles_Atributos_model');
  }

  function get($attr = null, $valor = null) {
    $this->db->select('*')
                ->from($this->table)
                    ->where('perfiles.activo', true);
    if ($attr != null and $valor != null) {
      $this->db->where("perfiles.$attr", $valor);
    }
    return $this->db->order_by('nombre', 'asc')->get()->result();
  } // end GET 

  function insert_entry($perfil) {
    return $this->db->insert('perfiles', $perfil);
  }

  function update_entry($id, $perfil) {
    $this->db->where('id', $id);
    return $this->db->update('perfiles', $perfil);
  }

  function destroy($id){
    /* Si desactivo un perfil, tengo que desactivarlo en todos los perfiles_atributos y personas/vehiculos asociados a esos perfiles */ 
    $perfil = $this->db->get_where('perfiles', array('id' => $id))->row();
    $perfil->activo = false;
    $perfil->user_last_updated_id = $this->session->userdata('id');
    $perfil->updated_at = date('Y-m-d H:i:s');
    $this->db->where('id', $id);
    $this->db->trans_begin();
      if ( $this->db->update('perfiles', $perfil) ) {
        $perfiles_atributos = $this->db->get_where('perfiles_atributos', array('perfil_id' => $id, 'activo' => TRUE))->result();
        foreach ($perfiles_atributos as $p) { 
          $this->Perfiles_Atributos_model->destroy($p->id);
        }
      }
    $this->db->trans_complete();
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return false;
    } else {
      $this->db->trans_commit();
      return true;
    }
  }

  function existe($name, $tipo) {
    return $this->db->get_where(
                          $this->table,
                          array('nombre' => $name, 'tipo' => $tipo))->row();
  }
  
}

<?php

class Persona_model extends CI_Model {

  protected $table = 'personas';

  function __construct() {
    parent::__construct();
  }

  function get($attr = null, $valor = null) {
    if($attr != null and $valor != null) {
      $query = $this->db->select('*')
                          ->from('personas')
                            ->where('personas.'.$attr, $valor)
                              ->where('personas.activo', true)
                                ->order_by('apellido', 'ASC')
                                  ->get();
      return $query->result();
    } else {
      return $this->db->get_where('personas', array('activo' => true))->result();
    }
  }

  function getData( $attr = null, $valor = null ) {
    return $this->db->select('personas.id, personas.n_legajo, personas.nombre, personas.alta_pdf_path, personas.apellido, personas.dni, personas.num_tramite, personas.dni_tiene_vencimiento, personas.fecha_vencimiento_dni,personas.dni_pdf_path, personas.cuil, 
                              personas.cuil_pdf_path, personas.fecha_nacimiento, personas.nacionalidad, personas.domicilio,personas.telefono, empresas.nombre as nombre_empresa,personas.fecha_inicio_actividad, personas.activo')
                      ->from('personas')
                        ->join('empresas', 'personas.empresa_id = empresas.id')
                          ->where('personas.'.$attr, $valor)
                            ->get()->result();
  }

  function getBajas() {
    return $this->db->select('personas.id,personas.n_legajo, personas.nombre, personas.apellido, personas.dni, empresas.nombre as nombre_empresa, 
                              motivos_baja.motivo as motivo_baja, personas.fecha_baja, personas.motivo_baja_id')
                      ->from('personas')
                        ->join('empresas', 'personas.empresa_id = empresas.id')
                        ->join('motivos_baja', 'personas.motivo_baja_id = motivos_baja.id')
                          ->where('personas.activo', false)
                            ->get()->result();
  }

  function insert_entry($persona) {
    return $this->db->insert('personas', $persona);
  }

  function update_entry($id, $persona) {
    $this->db->where('id', $id);
    return $this->db->update('personas', $persona);
  }

  function destroy($persona) {
    // Dejamos la persona inactiva y registramos el moviniento en tabla personas_inactivas
    $entry = $this->db->get_where('personas', array('id' => $persona['persona_id']))->row();
    $entry->activo = false;
    $entry->updated_at = date('Y-m-d H:i:s');
    $entry->user_last_updated_id = $this->session->userdata('id');
    $this->db->where('id', $persona['persona_id']);
    return ( $this->db->update('personas', $entry) ) && ( $this->db->insert('personas_inactivas', $persona) );
  }

  function reactivar( $id, $entry ) {
    $this->db->trans_start();
    // Se reactiva la persona
    $this->db->query('UPDATE personas SET activo = 1 WHERE id = '.$entry['persona_id']);
    // Se desactiva el registro persona_inactivo
    $this->db->where('id', $id);
    $this->db->update('personas_inactivas', $entry);
    $this->db->trans_complete();

    if ($this->db->trans_status() == FALSE) {
      $this->db->trans_rollback();
      return false;
    } else {
      $this->db->trans_commit();
      return true;
    }
  }

  function get_ultimo_legajo() {
    $query = $this->db->select('*')
                        ->from('personas')
                          ->where('activo', true)
                            ->limit(1)
                              ->order_by('n_legajo', 'DESC')
                                ->get()->row();
    return ( isset( $query->n_legajo ) ) ? $query->n_legajo : 0;
  }

  function get_data_excel( $filtros ) { /* obtengo la informacion para el excel de listado de personas */
    $query = $this->db->select('concat(personas.nombre,personas.apellido) as nombre_completo,personas.n_legajo,personas.activo, empresas.nombre as empresa, 
                                perfiles.nombre as perfil, perfiles_personas.perfil_id, personas.fecha_inicio_actividad')
                        ->from('personas')
                          ->join('empresas', 'personas.empresa_id = empresas.id')
                          ->join('perfiles_personas', 'personas.id = perfiles_personas.persona_id', 'left')
                          ->join('perfiles', 'perfiles.id = perfiles_personas.perfil_id', 'left')
                            ->where('personas.activo', $filtros['activo']);
    if ( $filtros['empresa_id'] != 0 ) {
      $query = $this->db->where( 'empresas.id', $filtros['empresa_id']);
    }

    if ( $filtros['perfil_id'] != 0 ) {
      $query = $this->db->where( 'perfiles.id', $filtros['perfil_id']);
    }

    if ( isset( $filtros['motivo_baja_id'] ) ) {
      $query = $this->db->where( 'personas.motivo_baja_id', $filtros['motivo_baja_id']);
    }

    $query = $this->db->order_by( 'personas.apellido', 'ASC' );
    return $query->get()->result();
  }

  function tiene_archivo($persona_id, $nombre_archivo){
    return !empty($this->db->get_where('archivos', 
      array( 'tabla'=> 'personas', 'tabla_id'=>$persona_id, 'columna'=>$nombre_archivo, 'activo'=> true ) )->row()); 
  }

  function get_archivo($persona_id, $nombre_archivo){
    return $this->db->get_where('archivos', 
      array( 'tabla'=> 'personas', 'tabla_id'=>$persona_id, 'columna'=>$nombre_archivo, 'activo'=> true ) )->row()->path;
  }

}

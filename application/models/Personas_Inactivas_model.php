<?php

class Personas_Inactivas_model extends CI_Model {

  protected $table = 'personas_inactivas';

  function __construct() {
    parent::__construct();
  }

  function get() {
    $this->db->select("$this->table.id,$this->table.fecha_baja, $this->table.detalle, $this->table.persona_id,
                       $this->table.activo, personas.n_legajo, personas.apellido, personas.nombre, personas.dni, motivos_baja.motivo,
                            empresas.nombre as empresa")
                    ->from($this->table)
                      ->join('personas', "$this->table.persona_id = personas.id")
                      ->join('motivos_baja',"$this->table.motivo_baja_id = motivos_baja.id" )
                      ->join('empresas', 'empresas.id = personas.empresa_id')
                        ->where("$this->table.activo", true)
                          ->order_by('n_legajo', 'ASC');
    return $this->db->get()->result();
  }

  function get_historial_persona($persona_id) {
    $this->db->select("$this->table.id, $this->table.fecha_baja, $this->table.detalle, $this->table.fecha_alta, $this->table.detalle_alta, motivos_baja.motivo")
               ->from($this->table)
                ->join('motivos_baja', "$this->table.motivo_baja_id = motivos_baja.id")
                  ->where("$this->table.persona_id", $persona_id)
                    ->order_by('id', 'DESC');
    return $this->db->get()->result();
  }

  function insert_entry($entry) {
    return $this->db->insert($this->table, $entry);
  }

  function update_entry( $id, $entry ) {
    $this->db->where('id', $id);
    return $this->db->update($this->table, $entry);
  }

  function destroy($table, $id) {
      $query = $this->db->get_where($table, array('id' => $id))->row();
      $query->activo = false;
      $query->update_at = date('Y-m-d H:i:s');

      $this->db->where('id', $id);
      return $this->db->update($table, $query);
  }

  function reactivar( $entry ) {
    $this->db->trans_start();
    $this->db->query('UPDATE personas SET activo = 1 WHERE id = '.$entry['persona_id']);
    $this->db->query('UPDATE detalle_bajas SET activo = 0 WHERE id = '.$entry['detalle_baja_id']);
    $this->db->trans_complete();

    if ($this->db->trans_status() == FALSE) {
      $this->db->trans_rollback();
      return false;
    } else {
      $this->db->trans_commit();
      return true;
    }
  }

  function get_ultimo_interno() {
    $query = $this->db->select('*')
                        ->from('personas')
                          ->where('activo', true)
                            ->limit(1)
                              ->order_by('interno', 'DESC')
                                ->get()->row();
    return $query->interno;
  }

  function get_motivos_baja() {
    return $this->db->get_where('motivos_baja', array('tipo' => 2, 'activo' => true))->result();
  }

}

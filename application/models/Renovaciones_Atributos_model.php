<?php

class Renovaciones_Atributos_model extends CI_Model {

  protected $table = 'renovaciones_atributos';

  public function __construct() {
    parent::__construct();
  }

  function get( $attr = null, $valor = null ) {
    if( $attr != null and $valor != null ) {
      return $this->db->select('*')
                        ->from($this->table)
                          ->where($this->table.'.'.$attr, $valor)
                            ->order_by($this->table.'.fecha_vencimiento', 'DESC')
                              ->get()->result();
    }
  }


  public function insert_entry($entry) {
    $atributo = $this->db->get_where( 'atributos_personas', array( 'id' => $entry['atributo_persona_id'] ) )->row();
    $this->db->trans_begin();
      $this->db->insert($this->table, $entry);
      if ( ( !$atributo->cargado ) OR ( $atributo->fecha_vencimiento < $entry['fecha_vencimiento'] )) {
      /* Verifico si ya existe una renovacion cargada en este atributo, de no existir tiene que cargarla */
      /* Verificamos que la fecha de vencimiento de la renovacion ingresada sea mayor a la que ya esta ingresada */
      /* en caso de que se ingrese una renovacion antigua */
        $atributo_persona = array(
          'cargado' => true,
          'updated_at' => date('Y-m-d H:i:s'),
          'user_created_id' => $this->session->userdata('id'),
          'user_last_updated_id' => $this->session->userdata('id'),
          'fecha_vencimiento' => $entry['fecha_vencimiento']
        );
        $this->db->where('id', $entry['atributo_persona_id']);
        $this->db->update('atributos_personas', $atributo_persona);
      }
      if ($this->db->trans_status() === FALSE)
      {
        $this->db->trans_rollback();
        return false;
      }
      else
      {
        $this->db->trans_commit();
        return true;
      }
  }

  function update_entry($id, $entry){
    $renovacion = $this->db->get_where($this->table, array('id' => $id))->row();
    $atributo_persona = $this->db->get_where('atributos_personas', array('id' => $renovacion->atributo_persona_id))->row();
    $atributo = $this->db->get_where('atributos', array('id' => $atributo_persona->atributo_id))->row();
    if ($atributo_persona->fecha_vencimiento <= $entry['fecha_vencimiento'] OR !$atributo->tiene_vencimiento) {
      $atributo_persona_update = array(
        'user_last_updated_id' => $this->session->userdata('id'),
        'updated_at' => date('Y-m-d H:i:s'),
        'fecha_vencimiento' => $entry['fecha_vencimiento']
      );
      $this->db->where('id', $atributo_persona->id);
      $this->db->update('atributos_personas', $atributo_persona_update);
    }

    $this->db->where('id', $id);
    return $this->db->update($this->table, $entry);
  }

  function destroy_entry($id) {
    $entry = $this->db->get_where($this->table, array('id' => $id))->row();
    $entry->user_last_updated_id = $this->session->userdata('id');
    $entry->updated_at = date('Y-m-d H:i:s');
    $entry->activo = false;
    $atributo_persona = $this->db->get_where('atributos_personas', array('id' => $entry->atributo_persona_id))->row();
    $atributo = $this->db->get_where('atributos', array('id' => $atributo_persona->atributo_id))->row();
    $this->db->trans_start();
      // *** primero deshabilito mi renovacion ***
      $this->db->where('id', $id);
      $this->db->update($this->table, $entry);
      // *** chequeo que mi renovacion no sea la mas nueva o la unica que tenia el atributo ***
      // *** en caso de ser la mas nueva (que seria la actual del atributo) actualizamos por la siguiente
      // *** si era la unica, el atributo se queda sin renovaciones , en ese caso hay q ponerlo como q no tiene nada cargado
      if ($atributo_persona->fecha_vencimiento >= $entry->fecha_vencimiento ) {
        $renovaciones_activas = $this->get_renovacion_mas_nueva($atributo_persona->id);
        if (!empty( $renovaciones_activas )) {
          $atributo_persona_update = array(
            'user_last_updated_id' => $this->session->userdata('id'),
            'updated_at' => date('Y-m-d H:i:s'),
            'fecha_vencimiento' => $renovaciones_activas->fecha_vencimiento,
            'pdf_path' => $renovaciones_activas->pdf_path
          );
        } else {
          $atributo_persona_update = array(
            'user_last_updated_id' => $this->session->userdata('id'),
            'updated_at' => date('Y-m-d H:i:s'),
            'cargado' => false,
            'pdf_path' => ''
          );
        } // if !empty( $renovaciones_activas )
        $this->db->where('id', $atributo_persona->id);
        $this->db->update('atributos_personas', $atributo_persona_update);
      } // if test vencimiento
    $this->db->trans_complete();
    if ($this->db->trans_status() === FALSE) {
      return FALSE;
    } else {
      return TRUE;
    }
  }

  // Consultamos si un registro ya existe
  function exists($perfil_id, $persona_id) {
    $query = $this->db->get_where(
                          $this->table,
                          array('perfil_id' => $perfil_id, 'persona_id' => $persona_id));

    if ( $query->num_rows() == 1 ) {
      return true;
    } else {
      return false;
    }
  }

  function get_renovacion_mas_nueva($atributo_persona_id){
    // lo que hago es buscar la renovacion con la fecha de vencimiento mas alta
    return $this->db->select('*')
                        ->from($this->table)
                          ->where('atributo_persona_id', $atributo_persona_id)
                          ->where('activo', true)
                            ->limit(1)
                              ->order_by('fecha_vencimiento', 'DESC')
                                ->get()->row();
  }

}

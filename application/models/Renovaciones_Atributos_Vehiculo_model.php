<?php

class Renovaciones_Atributos_Vehiculo_model extends CI_Model {

  protected $table = 'renovaciones_atributos_vehiculos';

  public function __construct()
  {
    parent::__construct();
  }

  public function get( $attr = null, $valor = null )
  {
    if( $attr != null and $valor != null )
    {
      return $this->db->select('*')
                        ->from($this->table)
                          ->where($this->table.'.'.$attr, $valor)
                            ->order_by($this->table.'.fecha_vencimiento', 'DESC')
                              ->get()->result();
    }
  }


  function insert_entry($entry) {
    $atributo = $this->db->get_where( 'atributos_vehiculos', array( 'id' => $entry['atributo_vehiculo_id'] ) )->row();
    $this->db->trans_begin();
      $this->db->insert($this->table, $entry);
      if ( ( !$atributo->cargado ) OR ( $atributo->fecha_vencimiento < $entry['fecha_vencimiento'] )) {
      /* Verifico si ya existe una renovacion cargada en este atributo, de no existir tiene que cargarla */
      /* Verificamos que la fecha de vencimiento de la renovacion ingresada sea mayor a la que ya esta ingresada */
      /* en caso de que se ingrese una renovacion antigua */
        $atributo_vehiculo = array(
          'cargado' => true,
          'updated_at' => date('Y-m-d H:i:s'),
          'user_created_id' => $this->session->userdata('id'),
          'user_last_updated_id' => $this->session->userdata('id'),
          'fecha_vencimiento' => $entry['fecha_vencimiento']
        );
        $this->db->where('id', $entry['atributo_vehiculo_id']);
        $this->db->update('atributos_vehiculos', $atributo_vehiculo);
      }
      if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        return false;
      }
      else {
        $this->db->trans_commit();
        return true;
      }
  }

  function update_entry($id, $entry) {
    $renovacion = $this->db->get_where($this->table, array('id' => $id))->row();
    $atributo_vehiculo = $this->db->get_where('atributos_vehiculos', array('id' => $renovacion->atributo_vehiculo_id))->row();
    $atributo = $this->db->get_where('atributos', array('id' => $atributo_vehiculo->atributo_id))->row();
    if ($atributo_vehiculo->fecha_vencimiento <= $entry['fecha_vencimiento'] OR !$atributo->tiene_vencimiento) {
      // Si no tiene vencimiento siempre hay que actualizar el PDF
      $atributo_vehiculo_update = array(
        'user_last_updated_id' => $this->session->userdata('id'),
        'updated_at' => date('Y-m-d H:i:s'),
        'fecha_vencimiento' => $entry['fecha_vencimiento']
      );
      $this->db->where('id', $atributo_vehiculo->id);
      $this->db->update('atributos_vehiculos', $atributo_vehiculo_update);
    }

    $this->db->where('id', $id);
    return $this->db->update($this->table, $entry);
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

  function destroy_entry($id) {
    $renovacion = $this->db->get_where($this->table, array('id' => $id))->row();
    $renovacion->user_last_updated_id = $this->session->userdata('id');
    $renovacion->updated_at = date('Y-m-d H:i:s');
    $renovacion->activo = false;
    $atributo_vehiculo = $this->db->get_where('atributos_vehiculos', array('id' => $renovacion->atributo_vehiculo_id))->row();
    $atributo = $this->db->get_where('atributos', array('id' => $atributo_vehiculo->atributo_id))->row();
    $this->db->trans_begin();
      // *** primero deshabilito mi renovacion ***
      $this->db->where('id', $id);
      $this->db->update($this->table, $renovacion);
      // *** chequeo que mi renovacion no sea la mas nueva o la unica que tenia el atributo ***
      // *** en caso de ser la mas nueva (que seria la actual del atributo) actualizamos por la siguiente
      // *** si era la unica, el atributo se queda sin renovaciones , en ese caso hay q ponerlo como q no tiene nada cargado
      if ( $atributo_vehiculo->fecha_vencimiento >= $renovacion->fecha_vencimiento ) {
        $renovaciones_activas = $this->get_renovacion_mas_nueva($atributo_vehiculo->id);
        if (!empty( $renovaciones_activas )) {
          $atributo_vehiculo_update = array(
            'user_last_updated_id' => $this->session->userdata('id'),
            'updated_at' => date('Y-m-d H:i:s'),
            'fecha_vencimiento' => $renovaciones_activas->fecha_vencimiento,
            'pdf_path' => $renovaciones_activas->pdf_path
          );
        } else {
          $atributo_vehiculo_update = array(
            'user_last_updated_id' => $this->session->userdata('id'),
            'updated_at' => date('Y-m-d H:i:s'),
            'cargado' => false,
            'pdf_path' => ''
          );
        } // if !empty( $renovaciones_activas )
        $this->db->where('id', $atributo_vehiculo->id);
        $this->db->update('atributos_vehiculos', $atributo_vehiculo_update);
      } // if test vencimiento
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return false;
    }
    else {
      $this->db->trans_commit();
      return true;
    }
  }

  function get_for_id($renovacion_id) {
    return $this->db->select('renovaciones_atributos_vehiculos.id, renovaciones_atributos_vehiculos.atributo_vehiculo_id,
                              renovaciones_atributos_vehiculos.fecha_renovacion, renovaciones_atributos_vehiculos.fecha_vencimiento,
                              atributos.tiene_vencimiento AS vence')
                      ->from('renovaciones_atributos_vehiculos')
                        ->join('atributos_vehiculos', 'atributos_vehiculos.id = renovaciones_atributos_vehiculos.atributo_vehiculo_id')
                        ->join('atributos', 'atributos_vehiculos.atributo_id = atributos.id')
                          ->where('renovaciones_atributos_vehiculos.id', $renovacion_id)
                            ->get()->row();
  }

  function get_renovacion_mas_nueva($atributo_vehiculo_id){
    // lo que hago es buscar la renovacion con la fecha de vencimiento mas alta
    return $this->db->select('*')
                          ->from($this->table)
                            ->where('atributo_vehiculo_id', $atributo_vehiculo_id)
                            ->where('activo', true)
                              ->limit(1)
                                ->order_by($this->table.'.fecha_vencimiento', 'DESC')
                                  ->get()->row();
  }

}

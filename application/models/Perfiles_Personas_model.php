<?php

class Perfiles_Personas_model extends CI_Model {

  protected $table = 'perfiles_personas';

  public function __construct() {
    parent::__construct();
    $this->load->model('Atributos_Personas_model');
  }

  function get($attr = null, $valor = null) {
    $this->db->select('perfiles_personas.id, perfiles_personas.persona_id, perfiles_personas.perfil_id,
                               personas.nombre as nombre_persona, personas.apellido as apellido_persona, personas.dni, personas.cuil,
                               perfiles.nombre as nombre_perfil, perfiles_personas.fecha_inicio_vigencia, perfiles_personas.activo')
                          ->from('perfiles_personas')
                            ->join('personas', 'personas.id = perfiles_personas.persona_id')
                            ->join('perfiles', 'perfiles.id = perfiles_personas.perfil_id')
                              ->where('personas.activo', true)
                              ->where('perfiles_personas.activo', TRUE);
    if( $attr != null and $valor != null ) {
      $this->db->where( "perfiles_personas.$attr", $valor );
    }
    return $this->db->get()->result();
  }

  function get_perfil_asignado( $persona_id, $perfil_id ) {
    return $this->db->get_where( $this->table,
                                 array( 'perfil_id' => $perfil_id, 'persona_id' => $persona_id ) )->row();
  }

  function insert_entry($entry) {
    $this->db->trans_start();

    $this->db->insert($this->table, $entry);
    $atributos_del_perfil = $this->db->get_where( 'perfiles_atributos', array( 'perfil_id' => $entry['perfil_id'], 'activo'=> true ) )->result();
    $this->activar_atributos_de_un_perfil($atributos_del_perfil, $entry['persona_id'] );

    $this->db->trans_complete();
    if ($this->db->trans_status() === FALSE) {
      return FALSE;
    } else {
      return TRUE;
    }
  }

  public function update_entry($id, $entry) {
    /* 
      perfil_actual = perfil que ya tiene asignado, es el q se va a cambiar
      perfil_nuevo = es el perfil que queda, viene en $entry

      Cambio el perfil de la persona, para poder hacer eso tengo que dar de baja los atributos que tenia en el anterior perfil
      verifico que los atributos del perfil que se cambia no esten en otros perfiles activos que tenga la persona, si no comparte ningun
      atributo con otro perfil se da de baja

      1: se eliminan los atributos del perfil que se va a cambiar y se da de baja el perfil
      2: se asigna el nuevo perfil
      3: asignamos los atributos del nuevo perfil asignado
    */
    $date_at = date('Y-m-d H:i:s');
    $user_id = $this->session->userdata('id');
    $perfil_actual = $this->db->get_where( $this->table, array( 'id' => $id ) )->row();

    $this->db->trans_start();
      $atributos_perfil_actual = $this->db->get_where( 'perfiles_atributos', array( 'perfil_id' => $perfil_actual->perfil_id, 'activo'=> true ) )->result();
      $atributos_perfil_nuevo = $this->db->get_where( 'perfiles_atributos', array( 'perfil_id' => $entry['perfil_id'], 'activo'=> true ) )->result();

      foreach($atributos_perfil_actual as $atributo) {
        // Obtengo atributos del perfil que se da de baja para desactivarlos de la persona
        if ( !$this->atributo_en_mas_de_un_perfil( $entry['persona_id'], $atributo->atributo_id ) ) {
          $atributo_persona = $this->db->get_where('atributos_personas', 
                                          array('persona_id' => $entry['persona_id'], 
                                                'atributo_id' => $atributo->atributo_id ))
                                            ->row();
          // Verificamos que el atributo no haya sido eliminado o agregado manualmente
          // esos son atributos "custom" que solo se pueden volver a modificar a mano
          if ( !$atributo_persona->personalizado && ($atributo_persona->activo == true) ) {
            $atributo_persona->updated_at = $date_at;
            $atributo_persona->user_last_updated_id = $user_id;
            $atributo_persona->activo = false;

            $this->db->where('id', $atributo_persona->id);
            $this->db->update('atributos_personas', $atributo_persona);
          }
        }
      }

      // damos de baja el perfil que tiene ahora
      $perfil_actual->activo = false;
      $perfil_actual->user_last_updated_id = $user_id;
      $perfil_actual->updated_at = $date_at;
      $this->db->where('id', $perfil_actual->id);
      $this->db->update($this->table, $perfil_actual);
      // asignamos el perfil por el que se actualizo
      $entry['created_at'] = $date_at;
      $entry['user_created_id'] = $user_id;
      $this->db->insert($this->table, $entry);
      // Asignamos los atributos del nuevo perfil
      $this->activar_atributos_de_un_perfil($atributos_perfil_nuevo, $entry['persona_id'] );

    $this->db->trans_complete();
    if ($this->db->trans_status() === FALSE) {
      return FALSE;
    } else {
      return TRUE;
    }
  }

  function reactivar($persona_id, $perfil_id, $fecha_inicio_vigencia){
    // Reactivamos el perfil y los atributos asociados al perfil
    $this->db->trans_start();

      $entry = $this->db->get_where($this->table, array('persona_id' => $persona_id, 'perfil_id' => $perfil_id))->row();
      $entry->activo = true;
      $entry->fecha_inicio_vigencia = $fecha_inicio_vigencia;
      $entry->updated_at = date('Y-m-d H:i:s');
      $entry->user_last_updated_id = $this->session->userdata('id');

      $perfil_atributos = $this->db->get_where( 'perfiles_atributos', array('perfil_id' => $perfil_id, 'activo' => true ))->result();
      $this->activar_atributos_de_un_perfil($perfil_atributos, $entry->persona_id);
      $this->db->where('id', $entry->id);
      $this->db->update($this->table, $entry);

    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE) {
      return FALSE;
    } else {
      return TRUE;
    }
  }

  function destroy($id) {
    $entry = $this->db->get_where($this->table, array('id' => $id))->row();
    $entry->activo = false;
    $entry->updated_at = date('Y-m-d H:i:s');
    $entry->user_last_updated_id = $this->session->userdata('id');

    $atributos_a_desactivar = $this->db->get_where('perfiles_atributos', array('perfil_id' => $entry->perfil_id, 'activo'=> true ))->result();

    $this->db->trans_start();
    // damos de baja los atributos del perfil a desactivar
      $this->desactivar_atributos_de_un_perfil( $atributos_a_desactivar, $entry->persona_id );
      // damos de baja el perfil
      $this->db->where('id', $id);
      $this->db->update($this->table, $entry);
      
    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE) {
      return FALSE;
    } else {
      return TRUE;
    }
  }

  // Consultamos si un registro ya existe
  function existe_activo( $persona_id, $perfil_id, $id = null ){
    $this->db->select('*')->from($this->table)
               ->where('perfil_id', $perfil_id)
               ->where('persona_id', $persona_id)
               ->where('activo', TRUE);
    if ($id != null) {
      $this->db->where('id', $id);
    }
    $data = $this->db->get()->result();
    return !empty($data);
  }

  function existe_asignacion( $persona_id, $perfil_id ) {
    return $this->db->get_where( $this->table,
                                 array( 'perfil_id' => $perfil_id, 'persona_id' => $persona_id ) )->result();
  }

  function atributo_en_mas_de_un_perfil( $persona_id, $atributo_id ) {
  /* consulto en cuantos perfiles que tiene asignados la persona se encuentra este atributo */
  $cuento = $this->db->select('perfiles_atributos.atributo_id, COUNT(perfiles_atributos.atributo_id) as cuento')
                       ->from($this->table)
                        ->join('perfiles_atributos', "perfiles_atributos.perfil_id = perfiles_personas.perfil_id")
                          ->where( "perfiles_personas.activo", true )
                          ->where("perfiles_personas.persona_id", $persona_id)
                          ->where("perfiles_atributos.atributo_id", $atributo_id)
                            ->group_by('perfiles_atributos.atributo_id')->get()->row();

    return ( $cuento->cuento > 1 );
  }

  function desactivar_atributos_de_un_perfil($perfil_atributos, $persona_id ) {
    $date_at = date('Y-m-d H:i:s');
    $user_id = $this->session->userdata('id');

    foreach($perfil_atributos as $atributo) {
        if ( !$this->atributo_en_mas_de_un_perfil( $persona_id, $atributo->atributo_id ) ) {
          $atributo_persona = $this->db->get_where('atributos_personas', 
                                          array('persona_id' => $persona_id, 
                                                'atributo_id' => $atributo->atributo_id ))
                                            ->row();
          // Verificamos que el atributo no haya sido eliminado o agregado manualmente
          // esos son atributos "custom" que solo se pueden volver a modificar a mano
          if ( !$atributo_persona->personalizado && ($atributo_persona->activo == true) ) {
            $atributo_persona->updated_at = $date_at;
            $atributo_persona->user_last_updated_id = $user_id;
            $atributo_persona->activo = false;

            $this->db->where('id', $atributo_persona->id);
            $this->db->update('atributos_personas', $atributo_persona);
          }
        }
      }
  }

  function activar_atributos_de_un_perfil($perfil_atributos, $persona_id ) {
    $date_at = date('Y-m-d H:i:s');
    $user_id = $this->session->userdata('id');

    foreach($perfil_atributos as $atributo) {
      $atributo_persona = $this->db->get_where('atributos_personas', 
                                      array('persona_id' => $persona_id, 
                                            'atributo_id' => $atributo->atributo_id ))
                                        ->row();
      if ( !empty($atributo_persona) ) {
        if ( !$atributo_persona->personalizado && ($atributo_persona->activo == false) ) {
          $atributo_persona->updated_at = $date_at;
          $atributo_persona->user_last_updated_id = $user_id;
          $atributo_persona->activo = true;

          $this->db->where('id', $atributo_persona->id);
          $this->db->update('atributos_personas', $atributo_persona);
        }
      } else {
         $atributo_persona = array(
            'atributo_id' => $atributo->atributo_id,
            'persona_id' => $persona_id,
            'cargado' => false,
            'created_at' => $date_at,
            'updated_at' => $date_at,
            'user_created_id' => $user_id,
            'user_last_updated_id' => $user_id,
            'activo' => TRUE,
            'personalizado' => 0
          );
        $this->db->insert('atributos_personas', $atributo_persona);
      }
    } // foreach
  }

}

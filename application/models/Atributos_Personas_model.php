<?php

class Atributos_Personas_model extends CI_Model {

// Tipo perfil: 1 para vehiculol 2 para vehiculos
  protected $table = 'atributos_personas';

  public function __construct() {
    parent::__construct();
  }

  public function get($valor = null) {
    if($valor != null) {
      $this->db->select('atributos.nombre, atributos.categoria, atributos.tiene_vencimiento,
                         atributos.permite_pdf,
                         atributos_personas.id, atributos_personas.pdf_path,
                         atributos_personas.cargado, atributos_personas.fecha_vencimiento')
                  ->from('atributos_personas')
                    ->join('atributos', 'atributos_personas.atributo_id = atributos.id')
                        ->join('personas', 'atributos_personas.persona_id = personas.id')
                          ->where('personas.id', $valor)
                          ->where('atributos_personas.activo', TRUE);
        return $this->db->get()->result();
    }
  }

  function get_attr_persona($persona_id = null) {
    if ($persona_id != null) {
      return $this->db->get_where($this->table, array('persona_id' => $persona_id))->result();
    } else {
      echo 'no hay resultados';
    }
  }

  function get_atributo_persona($persona_id, $atributo_id){
    return $this->db->get_where($this->table, array( 'persona_id' => $persona_id, 'atributo_id' => $atributo_id ))->row();
  } 

  // Obtenemos las renovaciones de un atributo asignado
  function get_renovaciones($atributo_persona_id) {
    $this->db->select('renovaciones_atributos.id,renovaciones_atributos.fecha_renovacion, 
                       renovaciones_atributos.fecha_vencimiento, renovaciones_atributos.pdf_path, 
                       atributos.tiene_vencimiento')
                ->from('renovaciones_atributos')
                  ->join('atributos_personas', 'atributos_personas.id = renovaciones_atributos.atributo_persona_id')
                  ->join('atributos', 'atributos.id = atributos_personas.atributo_id')
                    ->where('renovaciones_atributos.atributo_persona_id', $atributo_persona_id)
                    ->where('renovaciones_atributos.activo', true)
                      ->order_by('fecha_vencimiento', 'DESC');
    return $this->db->get()->result();
  }

  function get_ultima_renovacion($atributo_persona_id){ // obtengo la ultima renovacion de este atributo
    return $this->db->select('id')
                      ->from('renovaciones_atributos')
                        ->where('atributo_persona_id', $atributo_persona_id)
                        ->where('activo', TRUE)
                          ->order_by('fecha_vencimiento', 'DESC')
                            ->get()->row();
  }

  // Consultamos si un registro ya existe
  function existe( $persona_id,$atributo_id ) {
    $query = $this->db->get_where(
                          $this->table,
                          array('atributo_id' => $atributo_id, 'persona_id' => $persona_id));
    if ( $query->num_rows() == 1 ) {
      return true;
    } else {
      return false;
    }
  }

  // Nos llega un array con los atributos asociados al perfil que se le carga a una persona
  // Cargado por defecto en FALSE, eso se carga en Documentos
  // fecha_vencimiento y pdf_path son campos que se cargan en documentos
  public function insert_entry($atributos_perfil, $persona_id) {
    $date_at = date('Y-m-d H:i:s');
    $user_id = $this->session->userdata('id');

    $this->db->trans_start();
      foreach ($atributos_perfil as $attr) {
        if (!$this->existe($persona_id, $attr->attribute_id)) {
          $entry = array(
            'atributo_id' => $attr->attribute_id,
            'persona_id' => $persona_id,
            'created_at' => $date_at,
            'updated_at' => $c,
            'user_created_id' => $user_id,
            'user_last_updated_id' => $user_id,
          );
          $this->db->insert($this->table, $entry);
        } else {
          // Existe el registro, verificamos que no este dado de baja
          $atributo_persona = $this->db->get_where( $this->table, array('atributo_id' => $attr->attribute_id, 'persona_id' => $persona_id) )->row();
          if ( !$atributo_persona->activo && !$atributo_persona->personalizado ) {
            $atributo_persona->activo = true;
            $atributo_persona->updated_at = $date_at;
            $atributo_persona->user_last_updated_id = $user_id;
            $this->db->where('id', $atributo_persona->id);
            $this->db->update($this->table, $atributo_persona);
          }
        }
      }
    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE) {
      return FALSE;
    } else {
      return TRUE;
    }
  }

  function insert_personalizado($entry){
    if (!$this->existe($entry['atributo_id'], $entry['persona_id'])) {
      return $this->db->insert($this->table, $entry);
    } else {
      $data = $this->db->get_where( $this->table, array( 'persona_id'=> $entry['persona_id'], 'atributo_id'=> $entry['atributo_id'] ) )->row();
      $data->activo = $entry['activo'];
      $data->personalizado = $entry['personalizado'];
      $data->updated_at = $entry['updated_at'];
      $data->user_last_updated_id =$entry['user_last_updated_id'];
      $this->db->where('id', $data->id);
      return $this->db->update($this->table, $data);
    }
  }

  function disable_personalizado($id) {
    $entry = $this->db->get_where($this->table, array('id' => $id) )->row();
    $entry->activo = false;
    $entry->personalizado = true;
    $entry->updated_at = date('Y-m-d H:i:s');
    $entry->user_last_updated_id = $this->session->userdata('id');
    $this->db->where('id', $entry->id);
    return $this->db->update($this->table, $entry);
  }

  public function update_entry($atributos_perfil, $persona_id, $atributos_a_actualizar) {
    /* atributos_perfil son los atributos q se le van a asignar luego de modificar el perfil */
    /* atributos_a_actualizar son los atributos que ya tenia asignados */
    $date_at = date('Y-m-d H:i:s');
    $user_id = $this->session->userdata('id');
    $corresponde_atributo = false;
    foreach ($atributos_a_actualizar as $attr_a_actualizar) {
      foreach ($atributos_perfil as $attr) {
        if ($attr_a_actualizar->atributo_id == $attr->attribute_id) {
          $corresponde_atributo = true;
        }
      }
      /* si corresponde atributo es true significa que el atributo que ya tenia asignado no debe eliminarse */
      if (!$corresponde_atributo && !$attr_a_actualizar->personalizado) {
        $this->destroy( $attr_a_actualizar->id );
      }
      $corresponde_atributo = false;
    }

    $this->db->trans_start();
      foreach ($atributos_perfil as $attr) {
        if ( !$this->existe($attr->attribute_id, $persona_id) ) {
          $entry = array(
            'atributo_id' => $attr->attribute_id,
            'persona_id' => $persona_id,
            'cargado' => false,
            'created_at' => $date_at,
            'updated_at' => $date_at,
            'user_created_id' => $user_id,
            'user_last_updated_id' => $user_id,
            'activo' => TRUE
          );
          $this->db->insert($this->table, $entry);
        } else {
          // Obtenemos el atributo asignado de la persona
          $entry_update = $this->db->get_where($this->table, array('atributo_id' => $attr->attribute_id, 'persona_id' =>  $persona_id))->row();
          // si no esta activo se actualiza
          if (!$entry_update->activo && !$entry_update->personalizado) {
            $entry_update->activo = true;
            $entry_update->updated_at = $date_at;
            $entry_update->user_last_updated_id = $user_id;
            $this->db->where('id', $entry_update->id);
            $this->db->update($this->table, $entry_update);
          }
        }
      }
    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE) {
      return FALSE;
    } else {
      $this->activar_atributos_perfiles_asignados($persona_id);
    }
  }

  function destroy($id) {
    $entry = $this->db->get_where($this->table, array('id' => $id))->row();
    $entry->activo = false;
    $entry->updated_at = date('Y-m-d H:i:s');

    $this->db->where('id', $id);
    return $this->db->update($this->table, $entry);
  }

  // Consultas para informes
  function informe_matriz($fecha_inicio = null, $fecha_fin = null, $atributo_ids = null) {
    $this->db->select('personas.id,personas.n_legajo, personas.nombre as nombre_persona, personas.apellido as apellido_persona, personas.dni,
                       atributos.nombre as nombre_atributo,
                       atributos_personas.fecha_vencimiento, atributos_personas.cargado, atributos.tiene_vencimiento,atributos.id as atributo_id')
                ->from('atributos_personas')
                  ->join('personas', 'personas.id = atributos_personas.persona_id')
                    ->join('atributos', 'atributos.id = atributos_personas.atributo_id')
                      ->where('atributos_personas.activo', TRUE)
                      ->where('personas.activo', TRUE);

    if ($fecha_inicio != null && $fecha_fin != null) {
      $this->db->where('atributos_personas.fecha_vencimiento >=', $fecha_inicio );
      $this->db->where('atributos_personas.fecha_vencimiento <=', $fecha_fin );
    }

    if ( $atributo_ids != null ) {
      $this->db->where('atributos_personas.atributo_id', $atributo_ids[0]);
      for ($i=1; $i < count($atributo_ids) ; $i++) { 
        $this->db->or_where('atributos_personas.atributo_id = ', $atributo_ids[$i]);
      }
    }

    $this->db->order_by('personas.id', 'ASC');
    $this->db->order_by('atributos.nombre', 'ASC');
    return $this->db->get()->result();
  }

  function activar_atributos_perfiles_asignados($persona_id){
    $this->db->trans_start();
      $perfiles_persona = $this->db->get_where('perfiles_personas', array('persona_id' => $persona_id, 'activo' => true))->result();
      foreach($perfiles_persona as $perfil_persona) {
        $perfiles_atributos = $this->db->get_where('perfiles_atributos', array('perfil_id' => $perfil_persona->perfil_id, 'activo' => true))->result();
        foreach ($perfiles_atributos as $atributo) {
          $atributo_persona = $this->db->get_where($this->table, 
                                            array('persona_id' => $persona_id, 
                                                  'atributo_id' => $atributo->atributo_id ,
                                                  'activo' => false,
                                                  'personalizado'=> false))->row();
          if ( isset( $atributo_persona->id ) ) {
            $atributo_persona->activo = true;
            $this->db->where('id', $atributo_persona->id);
            $this->db->update($this->table, $atributo_persona);
          }
        }
      }
    $this->db->trans_complete();
    if ($this->db->trans_status() === FALSE) {
      return FALSE;
    } else {
      return TRUE;
    }
  }

} // end class
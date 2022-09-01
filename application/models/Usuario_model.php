<?php
class Usuario_model extends CI_Model {

	protected $table = 'usuarios';
  public function __construct() {
    parent::__construct();
  }

	// ====== Existe usuario
	public function existe($nombre_usuario) {
		$query = $this->db->get_where('usuarios', array('nombre_usuario' => $nombre_usuario));
		if ($query->num_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}
	// ===== Verificar que los datos sean correctos
	public function datosCorrectos($usuario)
	{
		$correcto = FALSE;
		if ($this->existe($usuario['nombre_usuario'])) {
			$query = $this->db->get_where('usuarios', array('nombre_usuario' => $usuario['nombre_usuario'], 'activo' => TRUE));
		  $password_correcto = $usuario['password'] == $this->encryption->decrypt($query->row()->password);
		  if ( $password_correcto && ($query->num_rows() == 1) ) {
		  	$correcto = TRUE;
		  }
		}
		return $correcto;
	}

	// ======= Obtener todos los datos del usuario
  function get($attr = null, $valor = null)
  {
  	if($attr != null and $valor != null)
  	{
  		$query = $this->db->get_where('usuarios', array($attr => $valor, 'activo' => true));
      if ($query->num_rows() == 1 ) {
        return $query->row();
      } else {
        return $query->result();
      }
  	} else
    	{
    		return $this->db->get_where('usuarios',array('activo' => true))->result();
    	}
  }

  // Obtengo los datos necesarios del usuario para cargar en las coockies
	public function getDataSesion($usuario)
	{
		$query = $this->db->select("id, nombre_usuario, nombre, email, rol")
          							 ->from("usuarios")
          								 ->where("nombre_usuario", $usuario['nombre_usuario'])
                            ->get();
    return $query->row_array();
	}
	// ====== ALTA de un usuario
	public function insert_entry($usuario)
	{
		return $this->db->insert('usuarios',$usuario);
	}

  function destroy($id)
  {
      $usuario = $this->db->get_where('usuarios', array('id' => $id))->row();
      $usuario->activo = false;
      $usuario->updated_at = date('Y-m-d H:i:s');

      $this->db->where('id', $id);
      return $this->db->update('usuarios', $usuario);
  }

  function update_entry($id, $usuario) {
    $this->db->where('id', $id);
    return $this->db->update('usuarios', $usuario);
  }

  function nombre_usuario_ocupado($nombre) {
    return $this->db->get_where( $this->table, array('nombre_usuario' => $nombre ) )->row();
  } 

}
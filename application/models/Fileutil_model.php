<?php

class Fileutil_model extends CI_Model {

  public function __construct() {
    parent::__construct();
    $this->load->library('upload');
  }

  function gererar_nombre_archivo($path, $extension){
    // genero un nombre random para un archivo y verifico que no haya algun archivo con ese mismo nombre en la carpeta
    $filename = '';
    do {
      $str=rand();
      $filename = md5($str);
      $filename = "$filename.$extension";
    } while ( file_exists( "$path/$filename" ) );
    return $filename;
  }

  function subir_multiples_archivos( $files, $folder, $tabla, $tabla_id, $columna ) {
  /*files son los archivos a subir 
    folder es la carpeta dentro de uploads donde van a ir a parar mis archivos
    tabla es la tabla a la que corresponde ese archivo (personas, renovaciones...)
    tabla_id es el id de la tabla a la que corresponden los archivos
    columna es el nombre al que pertenece el archivo (personas tienen 3 tipos de archivos que son dni, cuil, alta temprana. Con columna haces la direnciacion) */
    $response = array( 'status' => TRUE, 'msg' => 'Archivos subidos', 'archivos' => array() );

    $fileCount = count($files['name']);
    $path = "assets/uploads/$folder/".date('Y').'/'.date('m').'/'.$tabla_id;
    if ( !file_exists( $path ) ) {
      if (!mkdir( $path, 0777, true )) {
        $response['error_msg'] = 'se creo la carpeta';
        $response['status'] = FALSE;
        return $response;
      }
    }
    $this->db->trans_start();
      for ($i=0; $i < $fileCount; $i++) {
        $response[$i] = $files['tmp_name'][$i];
        $extension = pathinfo($files['name'][$i])['extension'];
        $filename = $this->gererar_nombre_archivo($path, $extension);
        $archivo = array(
          'tabla' => $tabla,
          'tabla_id' => $tabla_id,
          'columna' => $columna,
          'path' => $path.'/'.$filename,
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
          'user_created_id' => $this->session->userdata('id'),
          'user_last_updated_id' => $this->session->userdata('id')
        );
        if ($this->DButil->insert_entry('archivos',$archivo)) {
          $_FILES['file']['name'] = $files['name'][$i];
          $_FILES['file']['type'] = $files['type'][$i];
          $_FILES['file']['tmp_name'] = $files['tmp_name'][$i];
          $_FILES['file']['error'] = $files['error'][$i];
          $_FILES['file']['size'] = $files['size'][$i];

          $config['upload_path']    = $path;
          $config['allowed_types']  = 'pdf|jpg|png|jpeg';
          $config['max_size']       = 9216;
          $config['overwrite']      = true;
          $config['file_name']      = $filename;

          $this->upload->initialize($config);
          if ( ! $this->upload->do_upload( 'file' ) ) {
            $response['error_msg'] = $this->upload->display_errors();
            $response['status'] = FALSE;
            return $response;
          } else {
            $response['archivos'][] = array( 'id' => $this->DButil->get_last_id('archivos'), 'path' => $archivo['path'] );
          }
        } else {
          $response['error_msg'] = 'Error al registrar archivo en BD';
          $response['status'] = FALSE;
          return $response;
        }
      }
    $this->db->trans_complete();
    if ($this->db->trans_status() === FALSE) {
      $response['status'] = FALSE;
      $response['error_msg'] = 'Error al registrar archivo en BD';
    } else {
      $response['status'] = TRUE;
    }
    return $response;
  }

  function subir_archivo($file, $folder, $tabla, $tabla_id, $columna){
    $path = "assets/uploads/$folder/".date('Y').'/'.date('m').'/'.$tabla_id;
    if ( !file_exists( $path ) ) {
      if (!mkdir( $path, 0777, true )) {
        $response['error_msg'] = 'se creo la carpeta';
        $response['status'] = FALSE;
        return $response;
      }
    }

    $response = array( 'status' => TRUE, 'msg' => 'Archivos subidos', 'archivos' => array() );
    $extension = pathinfo($file['name'])['extension'];
    $filename = $this->gererar_nombre_archivo($path, $extension);
    $archivo = array(
      'tabla' => $tabla,
      'tabla_id' => $tabla_id,
      'columna' => $columna,
      'path' => $path.'/'.$filename,
      'created_at' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s'),
      'user_created_id' => $this->session->userdata('id'),
      'user_last_updated_id' => $this->session->userdata('id')
    );
    if ($this->DButil->insert_entry('archivos',$archivo)) {
      $_FILES['file']['name'] = $file['name'];
      $_FILES['file']['type'] = $file['type'];
      $_FILES['file']['tmp_name'] = $file['tmp_name'];
      $_FILES['file']['error'] = $file['error'];
      $_FILES['file']['size'] = $file['size'];

      $config['upload_path']    = $path;
      $config['allowed_types']  = 'pdf|jpg|png|jpeg';
      $config['max_size']       = 9216;
      $config['overwrite']      = true;
      $config['file_name']      = $filename;

      $this->upload->initialize($config);
      if ( ! $this->upload->do_upload( 'file' ) ) {
        $response['error_msg'] = $this->upload->display_errors();
        $response['status'] = FALSE;
        return $response;
      } else {
        $response['archivos'] = array( 'id' => $this->DButil->get_last_id('archivos'), 'path' => $archivo['path'] );
      }
    } else {
      $response['error_msg'] = 'Error al registrar archivo en BD';
      $response['status'] = FALSE;
      return $response;
    }
  }

  function actualizar_archivo( $archivo_id, $file ) {
    $response = array( 'status' => TRUE, 'msg' => 'Archivos subidos', 'archivos' => array() );
    $archivo = $this->DButil->get_for_id( 'archivos' ,$archivo_id);
    $path = "assets/uploads/personas/".date('Y').'/'.date('m').'/'.$archivo->tabla_id;
    $extension = pathinfo($file['name'])['extension'];
    $filename = $this->gererar_nombre_archivo($path, $extension);

    $archivo->path = $path.'/'.$filename;
    if ($this->DButil->update_entry('archivos', $archivo_id ,$archivo)) {
      $_FILES['file']['name'] = $file['name'];
      $_FILES['file']['type'] = $file['type'];
      $_FILES['file']['tmp_name'] = $file['tmp_name'];
      $_FILES['file']['error'] = $file['error'];
      $_FILES['file']['size'] = $file['size'];

      $config['upload_path']    = $path;
      $config['allowed_types']  = 'pdf|jpg|png|jpeg';
      $config['max_size']       = 9216;
      $config['overwrite']      = true;
      $config['file_name']      = $filename;

      $this->upload->initialize($config);
      if ( ! $this->upload->do_upload( 'file' ) ) {
        $response['error_msg'] = $this->upload->display_errors();
        $response['status'] = FALSE;
        return $response;
      } else {
        $response['archivos'] = array( 'id' => $this->DButil->get_last_id('archivos'), 'path' => $archivo->path );
      }
     }else {
      $response['error_msg'] = 'Error al registrar archivo en BD';
      $response['status'] = FALSE;
      return $response;
    }
  }

  function mover_archivos($files, $folder, $tabla, $tabla_id, $columna){
    
  }

  function uploadImage(){ 

   

      $data = [];

   

      $count = count($_FILES['files']['name']);

    

      for($i=0;$i<$count;$i++){

    

        if(!empty($_FILES['files']['name'][$i])){
          $_FILES['file']['name'] = $files['name'][$i];
          $_FILES['file']['type'] = $files['type'][$i];
          $_FILES['file']['tmp_name'] = $files['tmp_name'][$i];
          $_FILES['file']['error'] = $files['error'][$i];
          $_FILES['file']['size'] = $files['size'][$i];

          $config['upload_path'] = 'uploads/'; 
          $config['allowed_types'] = 'jpg|jpeg|png|gif';

          $config['max_size'] = '5000';

          $config['file_name'] = $_FILES['files']['name'][$i];

   

          $this->load->library('upload',$config); 

    

          if($this->upload->do_upload('file')){

            $uploadData = $this->upload->data();

            $filename = $uploadData['file_name'];

   

            $data['totalFiles'][] = $filename;

          }

        }
      }
      $this->load->view('imageUploadForm', $data); 
  }


function a(){
  if ( !move_uploaded_file($files['tmp_name'][$i], $path.'/'.$filename) ) {
          $response['error_msg'] = 'Error: '.$files['error'][$i];
          $response['status'] = FALSE;
          return $response;
  } else {
    $response['archivos'][] = array( 'id' => $this->DButil->get_last_id('archivos'), 'path' => $archivo['path'] );
  }

}

}
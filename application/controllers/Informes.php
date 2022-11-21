<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Informes extends CI_Controller {

  public function __construct(){
    parent::__construct();
    $this->load->model('Persona_model');
    $this->load->model('Vehiculo_model');
    $this->load->model('Atributo_model');
    $this->load->model('Atributos_Personas_model');
    $this->load->model('Atributos_Vehiculos_model');
    $this->load->model('Perfil_model');
    $this->load->model('Empresa_model');
    $this->load->model('Seguros_Vehiculos_model');
    date_default_timezone_set('America/Argentina/Buenos_Aires');
    if ( empty( $this->session->nombre_usuario ) ) {
      redirect('Login');
    }
  }

  public function personal() {
    $title['title'] = 'Informes';
    $data['atributos_personas'] = $this->Atributo_model->get_nombre_id( 1 );

    $this->load->view('layout/header',$title);
    $this->load->view('layout/nav');
    $this->load->view('sistema/informes/informes_personal', $data);
    $this->load->view('layout/footer');
  }

  public function vehiculos() {
    $title['title'] = 'Informes';
    $data['atributos_vehiculos'] = $this->Atributo_model->get_nombre_id( 2 );

    $this->load->view('layout/header',$title);
    $this->load->view('layout/nav');
    $this->load->view('sistema/informes/informes_vehiculos', $data);
    $this->load->view('layout/footer');
  }

  function generar_excel($data, $letra_fin,  $cant_col, $titulo_informe, $titulo_hoja, 
                         $titulo_columna, $nombres_indices, $col_colores , $filename)
  {
    // La uso para indicar a que celda aplicarle los metodos
    $letra = 'A';
    /* " col_colores " son los campos que van sin estilo en el informe, en pesonas es nombre y legajo, 
       en vehiculo es el numero de interno. Por eso indicamos a partir de que letra de columna empiezan a comparar los colores */  
    
    // Obtengo la ultima letra de mis columnas
    if ($cant_col <= 26) {
        $letra_fin = chr($cant_col + 64);
    } else {
      // chr() retorna un numero ascii, el abecedario esta entre el 65 y el 90
      $resultado = intdiv($cant_col, 26);
      $primer_letra = chr($resultado + 64);
      $segunda_letra = chr( $cant_col + 64 - ( 26*$resultado ) );

      $letra_fin = $primer_letra.$segunda_letra;
    }

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $styleArray = array( 
                  'allBorders' => 
                    [ 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => [ 'rgb' => '000000' ] ] 
                  );
    $styleGrey = array( 
                  'allBorders' => 
                    [ 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => [ 'rgb' => 'A9A9A9' ] ] 
                  );
    $styleTitle = array(
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
             'startColor' => [
                 'rgb' => 'F28A8C'
             ]
            );

    $sheet = $spreadsheet->getActiveSheet();
    $sheet->getStyle('A2')->getBorders()
            ->applyFromArray( $styleArray ); 

    if ($data > 0) {
      $sheet->setTitle($titulo_hoja);

      // Contador de filas en 2 porque la 1 se usa para el titulo
      $contador = 2;

      // Titulo en el excel
      $sheet->setCellValue('A1', $titulo_informe);
      $sheet->mergeCells('A1:'.$letra_fin.'1');
      $sheet->getStyle('A1:'.$letra_fin.'1')->applyFromArray($styleArray);
      $sheet->getStyle("A1")->getFont()->setBold(true);
      $sheet->getStyle('A1')->getFill()->applyFromArray($styleTitle);

      // Aplicamos el ancho a las columnas, estilo de fuente y titulo de columnas
      foreach ($titulo_columna as $titulo_col) {
        $sheet->getColumnDimension($letra)->setWidth(20);
        $sheet->getStyle($letra."{$contador}")->getFont()->applyFromArray( [ 'bold' => true ] );
        $sheet->setCellValue($letra."{$contador}", $titulo_col);
        $letra++;
      }
      // Celdas con bordes
      $sheet->getStyle("A2:".$letra_fin.'2')->getBorders()->applyFromArray( $styleArray );

      //Definimos la data del cuerpo.
      foreach($data as $d) {
        $letra = 'A';
         //Incrementamos una fila más, para ir a la siguiente.
         $contador++;
         //Informacion de las filas de la consulta.
        foreach ($nombres_indices as $indice) {
          $sheet->setCellValue( $letra."{$contador}", $d[ $indice ] );
          if ( $letra >= $col_colores ) {
            $sheet->getStyle($letra."{$contador}")->getFill()->applyFromArray($this->estilo_celda($d[ $indice ]));
          }

          $sheet->getStyle("A{$contador}:".$letra_fin."{$contador}")->getBorders()->applyFromArray( $styleArray );
            $letra++;
        }
      }
      $writer = new Xlsx($spreadsheet);

      header('Content-Type: application/vnd.ms-excel');
      header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
      header('Cache-Control: max-age=0');
      
      $writer->save('php://output');  // download file
    } else{
      echo "No se han encontrado resultados";
      exit;
    }
  }

  function estilo_celda($data) {
    $verde = '2df000';
    $naranja = 'ff8900';
    $rojo = 'ff0000';
    $blanco = 'ffffff';
    $amarillo = 'f6ff34';

    $estilo = array(
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
             'startColor' => [
                 'rgb' => $blanco
             ]
            );

    if ( ($data == 'No corresponde') OR ($data == '---') OR ($data == '') ) {
      $estilo['startColor']['rgb'] = $blanco;
    } elseif ( $data == 'No cargado' ) {
      $estilo['startColor']['rgb'] = $amarillo;
    } else {
      $dias_a_vencer = $this->comparar_fechas($data);
      if ( $dias_a_vencer >= 30 || $data == 'Cargado' ) {
        $estilo['startColor']['rgb'] = $verde;
      } elseif ( $dias_a_vencer >= 15 ) {
        $estilo['startColor']['rgb'] = $naranja;
      } else {
        $estilo['startColor']['rgb'] = $rojo;
      }
    }
    return $estilo;
  }

  function comparar_fechas($fecha) {
    $fecha_aux = date('Y-m-d' , strtotime($fecha));
    $date1 = date_create($fecha_aux);
    $date2 =  new DateTime("now");
    $intervalo = date_diff($date2, $date1);
    return $intervalo->format('%R%a');
  }
  
  function informe_vehiculos_entre_fechas( ){
    $fecha_inicio = $this->input->post('fecha_inicio');
    $fecha_fin = $this->input->post('fecha_fin');
    $atributo_id = $this->input->post('atributo_id');

    $this->Informe_matriz_vehiculos($fecha_inicio, $fecha_fin, $atributo_id);
  }

  function informe_matriz( ) {
    // aca me puede venir ningun, uno o muchos ids de atributos
    $atributo_ids = (  isset($_GET['atributo_id']) ) ? $_GET['atributo_id'] : null;
    $fecha_inicio = ( $this->input->get('fecha_inicio') != '' ) ? $this->input->get('fecha_inicio') : null;
    $fecha_fin = ( $this->input->get('fecha_fin') != '' ) ? $this->input->get('fecha_fin') : null;

    $titulo_excel = 'Informe vencimientos de personal';
    // Columnas que aparecen en el excel
    $titulo_columna = array('LEGAJO', 'APELLIDO/S, NOMBRE/S (DNI)');
    // De indice uso el ID del atributo
    $nombres_indices = array( 'legajo', 'nombre_completo');

    $atributos = $this->Atributo_model->get_nombre_id(1, $atributo_ids);

    if ($fecha_inicio != null && $fecha_fin != null) {
      $desde = date('d/m/Y', strtotime($fecha_inicio));
      $hasta = date('d/m/Y', strtotime($fecha_fin));
      $titulo_excel = "$titulo_excel entre las fechas $desde - $hasta";
    }

    foreach ($atributos as $atributo) {
      array_push($titulo_columna, $atributo->nombre);
      array_push($nombres_indices, $atributo->id);
    }
    $numero_columnas = count($nombres_indices);

    $this->generar_excel(
          $this->atributos_personas($fecha_inicio, $fecha_fin, $atributo_ids),
          'K', $numero_columnas,  $titulo_excel,'Informe_matriz',
          $titulo_columna, $nombres_indices, 'C' , 'vencimiento_personal'
    );
  }

  function informe_matriz_vehiculos( ) {
    $atributo_ids = (  isset($_GET['atributo_id']) ) ? $_GET['atributo_id'] : 0;
    $fecha_inicio = ( $this->input->get('fecha_inicio') != '' ) ? $this->input->get('fecha_inicio') : null;
    $fecha_fin = ( $this->input->get('fecha_fin') != '' ) ? $this->input->get('fecha_fin') : null;
    
    $titulo_excel = 'Informe vencimientos de vehiculos';
    $titulo_columna = array('INTERNO');
    // Nombre de attr que uso para indices
    $nombres_indices = array( 'interno');

    $atributos = $this->Atributo_model->get_nombre_id(2, $atributo_ids);

    if ($fecha_inicio != null && $fecha_fin != null) {
      $desde = date('d/m/Y', strtotime($fecha_inicio));
      $hasta = date('d/m/Y', strtotime($fecha_fin));
      $titulo_excel = "$titulo_excel entre las fechas $desde - $hasta";
    }

    foreach ($atributos as $atributo) {
      array_push($titulo_columna, $atributo->nombre);
      array_push($nombres_indices, $atributo->id);
    }
    // agregamos seguros
    $aseguradoras = $this->DButil->get_with_select('aseguradoras', 'id, nombre');
    
    foreach ($aseguradoras as $aseguradora) {
      array_push($titulo_columna, $aseguradora->nombre);
      array_push($nombres_indices, "aseguradora_$aseguradora->id");
    }

    $numero_columnas = count($nombres_indices);
    $this->generar_excel(
          $this->atributos_vehiculos($fecha_inicio, $fecha_fin, $atributo_ids),
          'K', $numero_columnas,  $titulo_excel,'Informe_matriz',
          $titulo_columna, $nombres_indices, 'B' , 'vencimientos_vehiculos'
    );
  }

  function atributos_vehiculos($fecha_inicio = null, $fecha_fin = null, $atributo_ids = null) {
    // array con la informacion a mostrar en el excel
    $datos_informe = array();
    // array base de indices
    $cuerpo_array = array( 'interno' => '');

    $data = $this->Atributos_Vehiculos_model->informe_matriz( $fecha_inicio, $fecha_fin, $atributo_ids );

    $atributos = $this->Atributo_model->get_nombre_id( 2, $atributo_ids );

    foreach ($atributos as $atributo) {
      $cuerpo_array[$atributo->id] = "---";
    }

    $aseguradoras = $this->DButil->get_with_select('aseguradoras', 'id, nombre');
    foreach ($aseguradoras as $aseguradora) {
      $cuerpo_array["aseguradora_$aseguradora->id"] = "---";
    }

    $row = $cuerpo_array;
    $row['interno'] = 'No hay informacion para mostrar';
    for ($i=0; $i < count($data); $i++) {
      if ($i == 0) {
        $id_anterior = $data[$i]->id;
        $row['interno'] = $data[$i]->interno;
        $row = $this->obtener_fecha_vencimiento($row, $data[$i]);
        // Agregamos los seguros al primer registro , este se repite x las dudas
        // ese registro tenga un solo vencimiento
        $vencimientos = $this->Seguros_Vehiculos_model->get_seguros_vehiculo($data[$i]->id);
        foreach ($vencimientos as $v) {
          $row["aseguradora_$v->aseguradora_id"] = date('d-m-Y', strtotime($v->vencimiento));
        }
      } elseif ( $id_anterior != $data[$i]->id ) {
        // antes de cambiar de vehiculo le cargamos los vencimientos de seguros
        $vencimientos = $this->Seguros_Vehiculos_model->get_seguros_vehiculo($data[$i]->id);
        foreach ($vencimientos as $v) {
          $row["aseguradora_$v->aseguradora_id"] = date('d-m-Y', strtotime($v->vencimiento));
        }
        $datos_informe[] = $row;
        $id_anterior = $data[$i]->id;
        $row = $cuerpo_array;
        $row['interno'] = $data[$i]->interno;
        $row = $this->obtener_fecha_vencimiento($row, $data[$i]);
      } else {
        $row = $this->obtener_fecha_vencimiento($row, $data[$i]);
      }
    }
    $datos_informe[] = $row;

    return $datos_informe;
  }

  function atributos_personas( $fecha_inicio = null, $fecha_fin = null, $atributo_ids = null) {
    // array con la informacion a mostrar en el excel
    $datos_informe = array();
    // array base de indices
    $cuerpo_array = array( 'legajo' => '', 'nombre_completo' => '');
    // info de los vencimientos de cada persona
    $data = $this->Atributos_Personas_model->informe_matriz( $fecha_inicio, $fecha_fin, $atributo_ids );

    $atributos = $this->Atributo_model->get_nombre_id(1, $atributo_ids);

    foreach ($atributos as $atributo) {
      $cuerpo_array[$atributo->id] = "---";
    }
    $row = $cuerpo_array;
    $row['legajo'] = 'No hay informacion para mostrar';
    for ($i=0; $i < count($data); $i++) {
      if ($i == 0) {
        $id_anterior = $data[$i]->id;
        $row['legajo'] = $data[$i]->n_legajo;
        $row['nombre_completo'] = $data[$i]->apellido_persona.' '.$data[$i]->nombre_persona.' '.$data[$i]->dni;
        $row = $this->obtener_fecha_vencimiento($row, $data[$i]);
      } elseif ( $id_anterior != $data[$i]->id ) {
        $datos_informe[] = $row;
        $row = $cuerpo_array;
        $id_anterior = $data[$i]->id;
        $row['legajo'] = $data[$i]->n_legajo;
        $row['nombre_completo'] = $data[$i]->apellido_persona.' '.$data[$i]->nombre_persona.' '.$data[$i]->dni;
        $row = $this->obtener_fecha_vencimiento($row, $data[$i]);
      } else {
        $row = $this->obtener_fecha_vencimiento($row, $data[$i]);
      }
    }
    $datos_informe[] = $row;

    return $datos_informe;
  }

  function obtener_fecha_vencimiento($array,$data) {
    if ($data->tiene_vencimiento == '1') {
      // El attr vence
      $dato = ($data->cargado) ? date('d-m-Y', strtotime($data->fecha_vencimiento)) : 'No cargado';
    } else {
      // Como no vence, solo importasi esta o no cargado
      $dato = ($data->cargado) ? 'Cargado' : 'No cargado';
    }
    $array[$data->atributo_id] = $dato;
    return $array;
  }

  function listado_personas() {
    $filtros = array(
      'activo' => $this->input->get('activas'),
      'empresa_id' => $this->input->get('empresa'),
      'perfil_id' => $this->input->get('perfil'),
    );
    $titulo_excel = 'Listado personal';
    // Columnas que aparecen en el excel
    $titulo_columna = array('LEGAJO', 'APELLIDO/S, NOMBRE/S (DNI)');
    // De indice uso el ID del atributo
    $nombres_indices = array( 'legajo', 'nombre_completo');

    // $atributos = $this->Atributo_model->get_nombre_id(1, $atributo_ids);

    if ($filtros['activo'] == 0) {
      $titulo_excel = "$titulo_excel inactivo";
    }

    // foreach ($atributos as $atributo) {
    //   array_push($titulo_columna, $atributo->nombre);
    //   array_push($nombres_indices, $atributo->id);
    // }
    $numero_columnas = count($nombres_indices);

    $this->generar_excel(
          $this->Persona_model->get_data_excel( $filtros ),
          'K', $numero_columnas,  $titulo_excel,'listado_personal',
          $titulo_columna, $nombres_indices, 'C' , 'listado_personal'
    );
  }
}

<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_atributos extends CI_Migration {

  public function up() {
    $this->dbforge->add_field(array(
      'id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'unsigned' => TRUE,
        'auto_increment' => TRUE
      ),
      'tipo' => array(
        'type' => 'INT',
        'constraint' => 11
      ),
      'nombre' => array(
        'type' => 'varchar',
        'constraint' => '100',
      ),
      'descripcion' => array(
        'type' => 'TEXT',
        'null' => TRUE
      ),
      'dato_obligatorio' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
      ),
      'categoria' => array(
        'type' => 'varchar',
        'constraint' => '30',
      ),
      'tiene_vencimiento' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
      ),
      'permite_pdf' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
      ),
      'observaciones' => array(
        'type' => 'TEXT',
        'null' => TRUE
      ),
      'metodologia_renovacion' => array(
        'type' => 'TEXT',
        'null' => TRUE
      ),
      'fecha_inicio_vigencia' => array(
        'type' => 'date'
      ),
      'presenta_resumen_mensual' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
      ),
      'tipo_vencimiento' => array(
        'type' => 'varchar',
        'constraint' => '10',
      ),
      'periodo_vencimiento' => array(
        'type' => 'INT',
        'constraint' => 11
      ),
      'permite_modificar_proximo_vencimiento' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
      ),
      'created_at datetime default current_timestamp',
      'updated_at datetime default current_timestamp on update current_timestamp',
      'user_created_id' => array(
              'type' => 'INT',
              'constraint' => 11,
              'unsigned' => TRUE
      ),
      'user_last_updated_id' => array(
              'type' => 'INT',
              'constraint' => 11,
              'unsigned' => TRUE
      ),
      'activo' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
                'default'=>1,
      ),
      'CONSTRAINT atributo_usuario_cfk FOREIGN KEY(`user_created_id`) REFERENCES `usuarios`(`id`)',
      'CONSTRAINT atributo_usuario_ufk FOREIGN KEY(`user_last_updated_id`) REFERENCES `usuarios`(`id`)'
    ));
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('atributos');
  }

  public function down() {
    $this->dbforge->drop_table('atributos');
  }
}

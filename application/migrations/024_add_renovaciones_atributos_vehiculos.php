<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_renovaciones_atributos_vehiculos extends CI_Migration {

  public function up() {
    $this->dbforge->add_field(array(
      'id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'unsigned' => TRUE,
        'auto_increment' => TRUE
      ),
      'atributo_vehiculo_id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'unsigned' => TRUE
      ),
      'fecha_renovacion' => array(
        'type' => 'date',
        'null' => TRUE
      ),
      'fecha_vencimiento' => array(
        'type' => 'date',
        'null' => TRUE
      ),
      'observaciones' => array(
        'type' => 'varchar',
        'constraint' => '200',
        'null' => TRUE,
      ),
      'pdf_path' => array(
        'type' => 'varchar',
        'constraint' => '200',
        'null' => TRUE,
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
      'CONSTRAINT renovacion_atributo_vehiculo_usuario_cfk FOREIGN KEY(`user_created_id`) REFERENCES `usuarios`(`id`)',
      'CONSTRAINT renovacion_atributo_vehiculo_usuario_ufk FOREIGN KEY(`user_last_updated_id`) REFERENCES `usuarios`(`id`)',
      'CONSTRAINT renovacion_atributo_atributo_vehiculo_fk FOREIGN KEY(`atributo_vehiculo_id`) REFERENCES `atributos_vehiculos`(`id`)',
    ));
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('renovaciones_atributos_vehiculos');
  }

  public function down() {
    $this->dbforge->drop_table('renovaciones_atributos_vehiculos');
  }
}
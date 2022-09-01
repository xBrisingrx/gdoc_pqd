<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_seguros_vehiculos extends CI_Migration {

  public function up() {
    $this->dbforge->add_field(array(
      'id' => array(
              'type' => 'INT',
              'constraint' => 11,
              'unsigned' => TRUE,
              'auto_increment' => TRUE
      ),
      'vehiculo_id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'unsigned' => TRUE
      ),
      'aseguradora_id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'unsigned' => TRUE
      ),
      'poliza' => array(
        'type' => 'varchar',
        'constraint' => '100'
      ),
      'fecha_alta' => array(
        'type' => 'date'
      ),
      'vencimiento' => array(
        'type' => 'date'
      ),
      'descripcion' => array(
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
      'CONSTRAINT seguro_vehiculo_usuario_cfk FOREIGN KEY(`user_created_id`) REFERENCES `usuarios`(`id`)',
      'CONSTRAINT seguro_vehiculo_usuario_ufk FOREIGN KEY(`user_last_updated_id`) REFERENCES `usuarios`(`id`)',
      'CONSTRAINT seguro_vehiculo_vehiculo_fk FOREIGN KEY(`vehiculo_id`) REFERENCES `vehiculos`(`id`)',
      'CONSTRAINT seguro_vehiculo_aseguradora_fk FOREIGN KEY(`aseguradora_id`) REFERENCES `aseguradoras`(`id`)',
    ));
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('seguros_vehiculos');
  }

  public function down() {
    $this->dbforge->drop_table('seguros_vehiculos');
  }
}
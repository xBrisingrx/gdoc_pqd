<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_vehiculos extends CI_Migration {

  public function up() {
    $this->dbforge->add_field(array(
      'id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'unsigned' => TRUE,
        'auto_increment' => TRUE
      ),
      'interno' => array(
        'type' => 'VARCHAR',
        'constraint' => '20'
      ),
      'dominio' => array(
        'type' => 'VARCHAR',
        'constraint' => '50'
      ),
      'anio' => array(
        'type' => 'INT',
        'constraint' => 11,
        'null' => TRUE
      ),
      'n_chasis' => array(
        'type' => 'VARCHAR',
        'constraint' => '100',
        'null' => TRUE
      ),
      'n_motor' => array(
        'type' => 'VARCHAR',
        'constraint' => '100',
        'null' => TRUE
      ),
      'cant_asientos' => array(
        'type' => 'VARCHAR',
        'constraint' => '10',
        'null' => TRUE
      ),
      'observaciones' => array(
        'type' => 'TEXT',
        'null' => TRUE
      ),
      'patentamiento' => array(
        'type' => 'VARCHAR',
        'constraint' => '200',
        'null' => TRUE
      ),
      'empresa_id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'unsigned' => TRUE
      ),
      'marca_id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'unsigned' => TRUE
      ),
      'modelo_id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'unsigned' => TRUE
      ),
      'tipo_id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'unsigned' => TRUE
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
      'CONSTRAINT vehiculo_usuario_cfk FOREIGN KEY(`user_created_id`) REFERENCES `usuarios`(`id`)',
      'CONSTRAINT vehiculo_usuario_ufk FOREIGN KEY(`user_last_updated_id`) REFERENCES `usuarios`(`id`)',
      'CONSTRAINT vehiculo_empresa_fk FOREIGN KEY(`empresa_id`) REFERENCES `empresas`(`id`)',
      'CONSTRAINT vehiculo_marca_fk FOREIGN KEY(`marca_id`) REFERENCES `marcas_vehiculos`(`id`)',
      'CONSTRAINT vehiculo_modelo_fk FOREIGN KEY(`modelo_id`) REFERENCES `modelos_vehiculos`(`id`)',
      'CONSTRAINT vehiculo_tipo_fk FOREIGN KEY(`tipo_id`) REFERENCES `tipos_vehiculos`(`id`)'
    ));
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('vehiculos');
  }

  public function down() {
    $this->dbforge->drop_table('vehiculos');
  }
}
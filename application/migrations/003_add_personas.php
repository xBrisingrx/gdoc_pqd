<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_personas extends CI_Migration {

  public function up() {
    $this->dbforge->add_field(array(
      'id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'unsigned' => TRUE,
        'auto_increment' => TRUE
      ),
      'n_legajo' => array(
        'type' => 'INT',
        'constraint' => 11
      ),
      'nombre' => array(
        'type' => 'VARCHAR',
        'constraint' => '60',
      ),
      'apellido' => array(
        'type' => 'VARCHAR',
        'constraint' => '60',
      ),
      'dni' => array(
        'type' => 'BIGINT',
        'constraint' => 11,
      ),
      'dni_tiene_vencimiento' => array(
        'type' => 'TINYINT',
        'constraint' => 1,
        'default'=>1,
      ),
      'fecha_vencimiento_dni' => array(
        'type' => 'date',
        'null' => TRUE
      ),
      'dni_pdf_path' => array(
        'type' => 'VARCHAR',
        'constraint' => '60',
        'null' => TRUE
      ),
      'num_tramite' => array(
        'type' => 'VARCHAR',
        'constraint' => '60',
        'null' => TRUE
      ),
      'cuil' => array(
        'type' => 'VARCHAR',
        'constraint' => '15',
        'null' => TRUE
      ),
      'cuil_pdf_path' => array(
        'type' => 'VARCHAR',
        'constraint' => '60',
        'null' => TRUE
      ),
      'fecha_nacimiento' => array(
        'type' => 'date',
        'null' => TRUE
      ),
      'domicilio' => array(
        'type' => 'VARCHAR',
        'constraint' => '60',
        'null' => TRUE
      ),
      'telefono' => array(
        'type' => 'VARCHAR',
        'constraint' => '60',
        'null' => TRUE
      ),
      'fecha_inicio_actividad' => array(
        'type' => 'date',
        'null' => TRUE
      ),
      'empresa_id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'unsigned' => TRUE
      ),
      'email' => array(
        'type' => 'VARCHAR',
        'constraint' => '60',
        'null' => TRUE
      ),
      'nacionalidad' => array(
        'type' => 'VARCHAR',
        'constraint' => '60',
        'null' => TRUE
      ),
      'alta_pdf_path' => array(
        'type' => 'VARCHAR',
        'constraint' => '60',
        'null' => TRUE
      ),
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
      'created_at datetime default current_timestamp',
      'updated_at datetime default current_timestamp on update current_timestamp',
      'activo' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
                'default'=>1,
      ),
      'CONSTRAINT persona_empresa_fk FOREIGN KEY(`empresa_id`) REFERENCES `empresas`(`id`)',
      'CONSTRAINT persona_usuario_cfk FOREIGN KEY(`user_created_id`) REFERENCES `usuarios`(`id`)',
      'CONSTRAINT persona_usuario_ufk FOREIGN KEY(`user_last_updated_id`) REFERENCES `usuarios`(`id`)'
    ));
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('personas');
  }

  public function down() {
    $this->dbforge->drop_table('personas');
  }
}

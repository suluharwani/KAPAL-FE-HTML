<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTourPackagesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'package_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'package_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'duration' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false
            ],
            'price' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'null' => false
            ],
            'image_url' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],
            'island_slug' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true
            ],
            'inclusions' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'exclusions' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'itinerary' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true
            ]
        ]);

        $this->forge->addPrimaryKey('package_id');
        $this->forge->createTable('tour_packages');
    }

    public function down()
    {
        $this->forge->dropTable('tour_packages');
    }
}
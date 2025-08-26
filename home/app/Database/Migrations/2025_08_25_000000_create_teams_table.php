<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTeamsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'team_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false
            ],
            'position' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false
            ],
            'image' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],
            'bio' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'social_facebook' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],
            'social_twitter' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],
            'social_instagram' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],
            'social_linkedin' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1
            ],
            'display_order' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0
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

        $this->forge->addPrimaryKey('team_id');
        $this->forge->createTable('teams');
        
        // Insert sample data
        $this->db->table('teams')->insertBatch([
            [
                'name' => 'Ahmad Santoso',
                'position' => 'Founder & CEO',
                'image' => 'uploads/team/ahmad-santoso.jpg',
                'bio' => 'Pendiri Raja Ampat Boat Services dengan pengalaman lebih dari 15 tahun di industri maritim dan pariwisata.',
                'social_facebook' => 'https://facebook.com/ahmad.santoso',
                'social_twitter' => 'https://twitter.com/ahmad_santoso',
                'social_instagram' => 'https://instagram.com/ahmad.santoso',
                'social_linkedin' => 'https://linkedin.com/in/ahmad-santoso',
                'is_active' => 1,
                'display_order' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Maria Wijaya',
                'position' => 'Operational Manager',
                'image' => 'uploads/team/maria-wijaya.jpg',
                'bio' => 'Ahli logistik dan operasional dengan sertifikasi keselamatan maritim internasional.',
                'social_facebook' => 'https://facebook.com/maria.wijaya',
                'social_twitter' => 'https://twitter.com/maria_wijaya',
                'social_instagram' => 'https://instagram.com/maria.wijaya',
                'social_linkedin' => 'https://linkedin.com/in/maria-wijaya',
                'is_active' => 1,
                'display_order' => 2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Budi Pratama',
                'position' => 'Head Captain',
                'image' => 'uploads/team/budi-pratama.jpg',
                'bio' => 'Nakhoda berpengalaman dengan pengetahuan mendalam tentang perairan Raja Ampat.',
                'social_facebook' => 'https://facebook.com/budi.pratama',
                'social_twitter' => 'https://twitter.com/budi_pratama',
                'social_instagram' => 'https://instagram.com/budi.pratama',
                'social_linkedin' => 'https://linkedin.com/in/budi-pratama',
                'is_active' => 1,
                'display_order' => 3,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Sari Dewi',
                'position' => 'Customer Service Manager',
                'image' => 'uploads/team/sari-dewi.jpg',
                'bio' => 'Spesialis layanan pelanggan yang memastikan setiap tamu mendapatkan pengalaman terbaik.',
                'social_facebook' => 'https://facebook.com/sari.dewi',
                'social_twitter' => 'https://twitter.com/sari_dewi',
                'social_instagram' => 'https://instagram.com/sari.dewi',
                'social_linkedin' => 'https://linkedin.com/in/sari-dewi',
                'is_active' => 1,
                'display_order' => 4,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('teams');
    }
}
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Creates the two tables that back the WhatsApp-OTP authentication flow
 * used by the mobile app and API v1:
 *
 *   api_otps   – stores OTP hashes, attempts, and expiry timestamps
 *   api_tokens – stores access / refresh token pairs for authenticated sessions
 */
class CreateWhatsAppAuthTables extends Migration
{
    public function up()
    {
        // ----------------------------------------------------------------
        // api_otps
        // ----------------------------------------------------------------
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'otp_hash' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'comment'    => 'SHA-256 hash of the 6-digit OTP',
            ],
            'attempts' => [
                'type'       => 'TINYINT',
                'unsigned'   => true,
                'default'    => 0,
            ],
            'expires_at' => [
                'type' => 'DATETIME',
            ],
            'resend_available_at' => [
                'type' => 'DATETIME',
            ],
            'verified_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => 45,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['phone', 'expires_at'], false, false, 'idx_otps_phone_expires');
        $this->forge->createTable('api_otps', true);

        // ----------------------------------------------------------------
        // api_tokens
        // ----------------------------------------------------------------
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'access_token' => [
                'type'       => 'VARCHAR',
                'constraint' => 128,
            ],
            'refresh_token' => [
                'type'       => 'VARCHAR',
                'constraint' => 128,
            ],
            'device_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'device_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'default'    => 'mobile',
            ],
            'access_token_expires_at' => [
                'type' => 'DATETIME',
            ],
            'refresh_token_expires_at' => [
                'type' => 'DATETIME',
            ],
            'revoked_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
            'updated_at' => [
                'type' => 'DATETIME',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id', false, false, 'idx_tokens_user');
        $this->forge->addKey('access_token', false, false, 'idx_tokens_access');
        $this->forge->addKey('refresh_token', false, false, 'idx_tokens_refresh');
        $this->forge->createTable('api_tokens', true);
    }

    public function down()
    {
        $this->forge->dropTable('api_tokens', true);
        $this->forge->dropTable('api_otps', true);
    }
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class DevicePushTokenModel extends Model
{
    protected $table            = 'device_push_tokens';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'device_token',
        'platform',
        'user_id',
        'device_model',
        'is_active',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Register or update a device push token.
     */
    public function registerToken(string $token, string $platform = 'android', ?int $userId = null, ?string $deviceModel = null): bool
    {
        \App\Libraries\PushDispatcher::ensureTokenTable();
        $existing = $this->where('device_token', $token)->first();

        $data = [
            'device_token' => $token,
            'platform'     => strtolower($platform),
            'user_id'      => $userId,
            'device_model' => $deviceModel,
            'is_active'    => 1,
            'updated_at'   => date('Y-m-d H:i:s'),
        ];

        if ($existing) {
            return (bool)$this->update($existing['id'], $data);
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        return (bool)$this->insert($data);
    }

    /**
     * Get all active registered device tokens for broadcasting.
     */
    public function getActiveTokens(?string $platform = null): array
    {
        \App\Libraries\PushDispatcher::ensureTokenTable();
        $builder = $this->where('is_active', 1);
        if ($platform) {
            $builder->where('platform', strtolower($platform));
        }
        return $builder->findAll() ?: [];
    }
}


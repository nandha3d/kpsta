<?php

namespace App\Controllers\Api\V1;

use App\Models\Donation_model;
use CodeIgniter\HTTP\ResponseInterface;

class DonationController extends BaseApiController
{
    /**
     * POST /api/v1/donations
     * Initialize donation and return order info.
     */
    public function initiate(): ResponseInterface
    {
        $json = $this->request->getJSON(true) ?? $this->request->getPost();

        $name = trim((string)($json['name'] ?? ''));
        $email = trim((string)($json['email'] ?? ''));
        $phone = trim((string)($json['phone'] ?? ''));
        $amount = (float)($json['amount'] ?? 0);
        $district = trim((string)($json['district'] ?? ''));
        $designation = trim((string)($json['designation'] ?? ''));
        $pan = trim((string)($json['pan'] ?? ''));

        $errors = [];
        if (empty($name)) {
            $errors['name'] = ['Name is required.'];
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = ['Valid email is required.'];
        }
        if (empty($phone)) {
            $errors['phone'] = ['Phone number is required.'];
        }
        if ($amount < 10) {
            $errors['amount'] = ['Minimum donation amount is ₹10.'];
        }

        if (!empty($errors)) {
            return $this->respondValidationFailed($errors);
        }

        $token = 'KPSTA_DON_' . strtoupper(bin2hex(random_bytes(10)));
        $db = \Config\Database::connect();

        $db->table('donation')->insert([
            'token'       => $token,
            'name'        => $name,
            'email'       => $email,
            'phone'       => $phone,
            'amount'      => $amount,
            'district'    => $district,
            'designation' => $designation,
            'pan'         => $pan,
            'status'      => 'PENDING',
            'created_at'  => date('Y-m-d H:i:s'),
        ]);

        $payload = [
            'token'            => $token,
            'amount'           => $amount,
            'currency'         => 'INR',
            'donor_name'       => $name,
            'donor_email'      => $email,
            'donor_phone'      => $phone,
            'payment_key'      => getenv('RAZORPAY_KEY') ?: 'rzp_test_fkTHmuBoSCPJ5s',
            'status'           => 'PENDING',
        ];

        return $this->respondCreated($payload, 'Donation order initiated successfully.');
    }

    /**
     * GET /api/v1/donations/{token}/status
     */
    public function status(string $token): ResponseInterface
    {
        $db = \Config\Database::connect();
        $record = $db->table('donation')->where('token', $token)->get(1)->getRowArray();

        if (!$record) {
            return $this->respondNotFound('Donation record not found.');
        }

        return $this->respondSuccess([
            'token'      => $record['token'] ?? $token,
            'amount'     => (float)($record['amount'] ?? 0),
            'status'     => $record['status'] ?? 'PENDING',
            'created_at' => $this->formatIsoDate($record['created_at'] ?? null),
        ], 'Donation status retrieved.');
    }
}

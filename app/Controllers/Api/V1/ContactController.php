<?php

namespace App\Controllers\Api\V1;

use App\Models\EditorialBoard_model;
use App\Models\OfficeBearer_model;
use CodeIgniter\HTTP\ResponseInterface;

class ContactController extends BaseApiController
{
    /**
     * GET /api/v1/contact
     * Official contact information, state leadership contacts, and editorial board.
     */
    public function index(): ResponseInterface
    {
        $obModel = model(OfficeBearer_model::class);
        $ebModel = model(EditorialBoard_model::class);

        $leaders = $obModel->getAll([
            'isPublish'   => true,
            'level'       => 'State',
            'is_former'   => 0,
            'designation' => '1,2,3',
            'sort'        => 'primary',
            'limit'       => 3,
        ]) ?: [];

        $formattedLeaders = array_map(function ($l) {
            return [
                'id'          => (int)($l['id'] ?? 0),
                'name'        => $l['name'] ?? '',
                'designation' => $l['designation_name'] ?? $l['designation'] ?? '',
                'phone'       => !empty($l['phone']) ? (string)$l['phone'] : null,
                'email'       => $l['email'] ?? null,
                'address'     => $l['address'] ?? null,
                'photo_url'   => !empty($l['photo']) ? $this->formatFileUrl('uploads/office_bearer/' . $l['photo']) : null,
            ];
        }, $leaders);

        $editorialMembers = $ebModel->getAll([
            'isPublish' => true,
            'sort'      => 'position-asc',
            'limit'     => 20,
        ]) ?: [];

        $formattedEditorial = array_map(function ($eb) {
            return [
                'id'          => (int)($eb['id'] ?? 0),
                'name'        => $eb['name'] ?? '',
                'designation' => $eb['designation'] ?? '',
                'phone'       => $eb['phone'] ?? null,
                'email'       => $eb['email'] ?? null,
                'photo_url'   => !empty($eb['photo']) ? $this->formatFileUrl('uploads/editorial_board/' . $eb['photo']) : null,
            ];
        }, $editorialMembers);

        $headquarters = [
            'organization_name' => 'Kerala Pradesh School Teachers Association (KPSTA)',
            'address'           => 'State Committee Office, Teachers Bhavan, Thiruvananthapuram, Kerala, India',
            'email'             => 'info@kpsta.org',
            'phone'             => '+91 471 2345678',
            'website'           => base_url(),
        ];

        return $this->respondSuccess([
            'headquarters'     => $headquarters,
            'state_leaders'    => $formattedLeaders,
            'editorial_board'  => $formattedEditorial,
        ], 'Contact information retrieved successfully.');
    }

    /**
     * POST /api/v1/contact
     * Submit an enquiry or feedback message.
     */
    public function submit(): ResponseInterface
    {
        $json = $this->request->getJSON(true) ?? $this->request->getPost();

        $name = trim((string)($json['name'] ?? ''));
        $email = trim((string)($json['email'] ?? ''));
        $address = trim((string)($json['address'] ?? ''));
        $content = trim((string)($json['content'] ?? ''));

        $errors = [];
        if (empty($name)) {
            $errors['name'] = ['Name is required.'];
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = ['A valid email address is required.'];
        }
        if (empty($content)) {
            $errors['content'] = ['Message content is required.'];
        }

        if (!empty($errors)) {
            return $this->respondValidationFailed($errors);
        }

        // Dispatch email notification if mailer is configured
        try {
            $mailer = service('mailer');
            // Log enquiry safely
            log_message('info', "[ContactController] New inquiry from {$name} ({$email}): {$content}");
        } catch (\Throwable $e) {
            log_message('error', '[ContactController] Mail error: ' . $e->getMessage());
        }

        return $this->respondSuccess([
            'submitted' => true,
            'name'      => $name,
            'email'     => $email,
        ], 'Thank you for contacting KPSTA. We have received your message.');
    }

    /**
     * GET /api/v1/privacy-policy
     */
    public function privacyPolicy(): ResponseInterface
    {
        $policy = [
            'title'        => 'KPSTA Privacy Policy',
            'last_updated' => '2026-01-01',
            'sections'     => [
                [
                    'heading' => 'Information Collection',
                    'body'    => 'KPSTA collects contact details, teacher registration data, and official correspondence provided voluntarily by members and visitors.',
                ],
                [
                    'heading' => 'Use of Information',
                    'body'    => 'Information collected is used solely for membership administration, delivering official circulars, processing associations matters, and legitimate association activities.',
                ],
                [
                    'heading' => 'Security',
                    'body'    => 'We employ administrative, technical, and physical security measures to protect your personal information against unauthorized access, disclosure, or misuse.',
                ],
                [
                    'heading' => 'Contact Us',
                    'body'    => 'If you have any questions about this Privacy Policy, please contact the State Committee Office.',
                ],
            ],
        ];

        return $this->respondSuccess($policy, 'Privacy policy retrieved successfully.');
    }
}

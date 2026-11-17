<?php

/**
 * GeMEMIS domain configuration: roles, permissions (FR-047 / BR-009) and the
 * status vocabularies quoted in the Functional Requirements Document.
 * Permissions live here and nowhere else — controllers ask `$user->can_do()`.
 */
return [
    'roles' => [
        'system_admin' => 'System Administrator',
        'exhibition_admin' => 'Exhibition Administrator',
        'space_officer' => 'Space Allocation Officer',
        'registration_officer' => 'Registration Officer',
        'finance_officer' => 'Finance Officer',
        'management' => 'Management',
        'customer_service' => 'Customer Service Officer',
        'auditor' => 'Auditor',
        'exhibitor' => 'Exhibitor',
    ],

    'permissions' => [
        'exhibitions.manage' => ['system_admin', 'exhibition_admin'],
        'spaces.manage' => ['system_admin', 'exhibition_admin', 'space_officer'],
        'applications.review' => ['system_admin', 'exhibition_admin', 'registration_officer'],
        'allocations.manage' => ['system_admin', 'exhibition_admin', 'space_officer'],
        // BR-006: changing an allocation that is already confirmed (paid) needs this.
        'allocations.authorize' => ['system_admin', 'exhibition_admin'],
        'finance.manage' => ['system_admin', 'finance_officer'],
        'finance.view' => ['system_admin', 'finance_officer', 'management', 'auditor', 'exhibition_admin'],
        'checkin.manage' => ['system_admin', 'exhibition_admin', 'space_officer', 'customer_service'],
        'exhibitors.view' => ['system_admin', 'exhibition_admin', 'space_officer', 'registration_officer', 'finance_officer', 'management', 'customer_service', 'auditor'],
        'exhibitors.manage' => ['system_admin', 'exhibition_admin', 'registration_officer', 'customer_service'],
        'reports.view' => ['system_admin', 'exhibition_admin', 'space_officer', 'registration_officer', 'finance_officer', 'management', 'auditor'],
        'users.manage' => ['system_admin'],
        'audit.view' => ['system_admin', 'auditor', 'management'],
        'staff' => ['system_admin', 'exhibition_admin', 'space_officer', 'registration_officer', 'finance_officer', 'management', 'customer_service', 'auditor'],
    ],

    'space_statuses' => ['available', 'reserved', 'pending_approval', 'allocated', 'occupied', 'blocked', 'cancelled', 'maintenance'],
    // Statuses an officer may set by hand; the rest follow from allocations.
    'manual_space_statuses' => ['available', 'reserved', 'blocked', 'cancelled', 'maintenance'],
    'application_statuses' => ['draft', 'submitted', 'returned', 'approved', 'rejected', 'pending_payment', 'confirmed', 'cancelled'],
    'invoice_statuses' => ['unpaid', 'partially_paid', 'paid', 'overdue', 'cancelled', 'refunded'],
    'exhibition_statuses' => ['draft', 'published', 'registration_closed', 'ongoing', 'completed', 'cancelled'],

    'auth' => [
        'max_failed_logins' => 5,
        'token_idle_minutes' => 60,
    ],

    // FR-059: shared secret the payment gateway signs its callbacks with.
    'payment_webhook_secret' => env('PAYMENT_WEBHOOK_SECRET'),
    'sms' => ['driver' => env('SMS_DRIVER', 'log'), 'sender' => env('SMS_SENDER', 'GeMEMIS')],
    'frontend_url' => env('FRONTEND_URL', 'http://localhost:3000'),
];

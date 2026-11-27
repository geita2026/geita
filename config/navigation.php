<?php

return [
    'USER' => ['User Administration', 1, 'icon-users', [
        'USER' => ['Users', 'users.index', [
            'View Users' => 'VIEW_USER_SUBMENU',
            'Create Users' => 'CREATE_USER',
            'Update Users' => 'UPDATE_USER',
            'Delete Users' => 'DELETE_USER',
        ]],
        'ROLE' => ['Roles', 'roles.index', [
            'Manage Roles' => 'MANAGE_ROLE',
            'View Roles' => 'VIEW_ROLE',
            'Update Roles' => 'UPDATE_ROLE',
            'Delete Roles' => 'DELETE_ROLE',
        ]],
        'PERMISSION' => ['Permissions', 'permissions.index', [
            'View Permissions' => 'VIEW_PERMISSION_SUBMENU',
            'Create Permissions' => 'CREATE_PERMISSION',
            'Update Permissions' => 'UPDATE_PERMISSION',
            'Delete Permissions' => 'DELETE_PERMISSION',
        ]],
        'AUDIT' => ['Audits', 'audits.index', [
            'View User Logs' => 'VIEW_AUDIT_SUBMENU',
            'Delete User Logs' => 'DELETE_AUDIT',
        ]],
        'CUSTOMER_PASSWORD_RESET' => ['Reset Customer Password', 'resertCustomerPassword.index', [
            'Resert Customer Password' => 'RESET_CUSTOMER_PASSWORD_RESET',
        ]],
    ]],
    'SYSTEM' => ['System Settings', 1, 'icon-cog3', [
        'INCOME_LIST' => ['Service GFS Codes', 'income_list.index', [
            'View Income' => 'VIEW_INCOME_LIST_SUBMENU',
            'Create Income' => 'CREATE_INCOME_LIST',
            'Update Income' => 'UPDATE_INCOME_LIST',
            'Delete Income' => 'DELETE_INCOME_LIST',
        ]],
        'DEFAULT_SETTINGS' => ['Default Settings', 'default_settings.index', [
            'View Default Settings' => 'VIEW_DEFAULT_SETTINGS_SUBMENU',
            'Create Default Settings' => 'CREATE_DEFAULT_SETTINGS',
        ]],
        'LEVEL_ONE_MENU' => ['Level One Menus', 'system_menus_level_one.index', [
            'View All Menu' => 'VIEW_ALL_MENU',
            'Create Menu' => 'CREATE_MENU',
            'Update Menu' => 'UPDATE_MENU',
        ]],
        'LEVEL_TWO_MENU' => ['Level Two Menus', 'system_menus_level_two.index', [
            'View All Menu' => 'VIEW_ALL_MENU',
            'Create Menu' => 'CREATE_MENU',
            'Update Menu' => 'UPDATE_MENU',
            'Delete Menu' => 'DELETE_LEVEL_TWO_MENU',
        ]],
        'API_REQUEST' => ['API Requests', 'api_requests.index', [
            'View All Api Requests' => 'VIEW_API_REQUEST_SUBMENU',
            'Create Api Requests' => 'CREATE_API_REQUEST',
            'Update Api Requests' => 'UPDATE_API_REQUEST',
            'Delete Api Requests' => 'DELETE_API_REQUEST',
        ]],
        'API_LOG' => ['API Logs', 'api_logs.index', [
            'View Api Logs' => 'VIEW_API_LOG_SUBMENU',
            'Delete Api Logs' => 'DELETE_API_LOG',
        ]],
        'INTERGRATION_MAPPING' => ['Intergration Mapping', 'intergration_mapping.index', [
            'View Intergration Mapping' => 'VIEW_INTERGRATION_MAPPING_SUBMENU',
            'Create Intergration Mapping' => 'CREATE_INTERGRATION_MAPPING',
        ]],
    ]],
    'COMPANY' => ['Companies', 1, 'icon-office', [
        'COMPANY' => ['Companies', 'companies.index', [
            'Company Registration' => 'CREATE_COMPANY',
            'Delete Company' => 'DELETE_COMPANY',
        ]],
        'COMPANY_PROFILE' => ['Company Profiles', 'company_profiles.index', [
            'View All Company' => 'VIEW_COMPANY_PROFILE_SUBMENU',
            'View Company' => 'VIEW_COMPANY_PROFILE',
            'Update Company' => 'UPDATE_COMPANY_PROFILE',
        ]],
        'COMPANY_USER' => ['Company Users', 'company_users.index', [
            'view-company-users' => 'VIEW_COMPANY_USER',
            'Update Company Users' => 'UPDATE_COMPANY_USER',
        ]],
        'ASSOCIATE_COMPANY' => ['Associate Companies', 'associate_companies.index', [
            'view-associate-companies' => 'VIEW_ASSOCIATE_COMPANY_SUBMENU',
            'allocate-associates-badges' => 'ALLOCATE_ASSOCIATE_COMPANY',
        ]],
        'COUNTRY' => ['Countries', 'countries.index', [
            'Update Countries' => 'UPDATE_COUNTRY',
            'Delete Countries' => 'DELETE_COUNTRY',
        ]],
        'SECTOR' => ['Sectors', 'sectors.index', [
            'Manage Sectors' => 'MANAGE_SECTOR',
            'Delete Sector' => 'DELETE_SECTOR',
        ]],
    ]],
    'EVENT' => ['Exhibition Events', 1, 'icon-calendar3', [
        'EXHIBITION_EVENT' => ['Exhibition Events', 'Exhibition-Events.index', [
            'View Events' => 'VIEW_EXHIBITION_EVENT_SUBMENU',
            'Create Events' => 'CREATE_EXHIBITION_EVENT',
            'Update Events' => 'UPDATE_EXHIBITION_EVENT',
            'Delete Events' => 'DELETE_EXHIBITION_EVENT',
        ]],
        'EVENT_PROGRAM' => ['Event Programs', 'event_programs.index', [
            'View EventPrograms' => 'VIEW_EVENT_PROGRAM_SUBMENU',
            'Create EventPrograms' => 'CREATE_EVENT_PROGRAM',
            'View Event Programs' => 'VIEW_EVENT_PROGRAM',
            'Update Event Programs' => 'UPDATE_EVENT_PROGRAM',
            'Delete Event Programs' => 'DELETE_EVENT_PROGRAM',
        ]],
        'EVENT_SPACE' => ['Event Spaces', 'event-spaces.index', [
            'View Registered Spaces' => 'VIEW_EVENT_SPACE_SUBMENU',
        ]],
        'CATALOGUE_SETTING' => ['Catalogue Settings', 'catalogue_settings.index', [
            'Manage Catalogue' => 'MANAGE_CATALOGUE_SETTING',
            'Delete Catalogue' => 'DELETE_CATALOGUE_SETTING',
            'Book Space' => 'BOOK_SPACE',
        ]],
        'PRODUCT' => ['Products', 'products.index', [
            'View Products' => 'VIEW_PRODUCT_SUBMENU',
            'Create Products' => 'CREATE_PRODUCT',
            'Update Products' => 'UPDATE_PRODUCT',
            'Delete Products' => 'DELETE_PRODUCT',
        ]],
        'EXHIBITOR_REPORT' => ['Exhibitor Report', 'exhibitors.index', [
            'Manage ExhibitorReports' => 'MANAGE_EXHIBITOR_REPORT',
        ]],
        'EXHIBITION_PRODUCT_REPORT' => ['Exhibition Products', 'exhibition_products.index', [
            'View Product Reports' => 'VIEW_EXHIBITION_PRODUCT_REPORT_SUBMENU',
            'Delete Product Reports' => 'DELETE_EXHIBITION_PRODUCT_REPORT',
        ]],
    ]],
    'SPACE' => ['Space Allocation', 1, 'icon-map5', [
        'BOOKING' => ['Booking', 'booking.index', [
            'Book Space' => 'BOOK_SPACE',
            'View Booking Space Categories' => 'VIEW_BOOKING_SPACE_CATEGORIES',
            'Cancel Booking' => 'CANCEL_BOOKING',
            'View Pending Booking' => 'VIEW_PENDING_BOOKING',
        ]],
        'BOOKING_TRANSFER' => ['Booking Transfer', 'booking_transfer.index', [
            'transfer-booking' => 'TRANSFER_BOOKING_TRANSFER',
            'approve-transfer' => 'APPROVE_BOOKING_TRANSFER',
        ]],
        'SERVICE_BOOKING' => ['Book Services', 'book-services.index', [
            'Book Services' => 'BOOK_SERVICES',
            'Book-all-service' => 'BOOK_ALL_SERVICE',
            'Cancel Service Booking' => 'CANCEL_SERVICE_BOOKING',
        ]],
        'PAVILION' => ['Pavilions', 'pavilions.index', [
            'Manage Spaces' => 'MANAGE_SPACES',
            'Delete Spaces' => 'DELETE_PAVILION',
        ]],
        'BOOTH_CATEGORY' => ['Booth Categories', 'boothCategories.index', [
            'Manage Spaces' => 'MANAGE_SPACES',
            'Create Booth Categories' => 'CREATE_BOOTH_CATEGORY',
            'Update Booth Categories' => 'UPDATE_BOOTH_CATEGORY',
            'Delete Booth Categories' => 'DELETE_BOOTH_CATEGORY',
        ]],
        'EXHIBITION_SPACE_CATEGORY' => ['Space Categories', 'spaceCategories.index', [
            'Manage Spaces' => 'MANAGE_SPACES',
            'Delete SpacesCategory' => 'DELETE_EXHIBITION_SPACE_CATEGORY',
        ]],
        'STREET' => ['Streets', 'streets.index', [
            'Manage Spaces' => 'MANAGE_SPACES',
            'Delete Street' => 'DELETE_STREET',
        ]],
        'ADDITIONAL_SERVICE' => ['Additional Services', 'additional_services.index', [
            'View Service' => 'VIEW_ADDITIONAL_SERVICE_SUBMENU',
            'Create Services' => 'CREATE_ADDITIONAL_SERVICE',
            'Update Services' => 'UPDATE_ADDITIONAL_SERVICE',
            'Delete Services' => 'DELETE_ADDITIONAL_SERVICE',
        ]],
    ]],
    'ACCOUNTING' => ['Accounting', 1, 'icon-calculator', [
        'ADVICE_NOTE' => ['Advice Notes', 'adviceNotes.index', [
            'Manage Invoices' => 'MANAGE_INVOICES',
            'Request ControlNumber' => 'REQUEST_ADVICE_NOTE',
            'Download Invoice' => 'DOWNLOAD_INVOICE',
            'Apply Discount' => 'APPLY_DISCOUNT',
            'Extend Deadline' => 'EXTEND_DEADLINE',
            'Cancel Invoices' => 'CANCEL_INVOICES',
            'approve-discount' => 'APPROVE_ADVICE_NOTE',
        ]],
        'PAYMENT' => ['Payments', 'payments.index', [
            'Manage Payments' => 'MANAGE_PAYMENT',
            'Update Payment Receipt' => 'UPDATE_PAYMENT',
        ]],
        'BANK_ACCOUNT' => ['Bank Accounts', 'bank_accounts.index', [
            'view-bank-accounts' => 'VIEW_BANK_ACCOUNT_SUBMENU',
            'Create Bank Accounts' => 'CREATE_BANK_ACCOUNT',
            'Update Bank Accounts' => 'UPDATE_BANK_ACCOUNT',
            'Delete Bank Accounts' => 'DELETE_BANK_ACCOUNT',
        ]],
        'CANCELED_INVOICE' => ['Canceled Invoices', 'canceled_invoices.index', [
            'View Canceled Invoice' => 'VIEW_CANCELED_INVOICE_SUBMENU',
        ]],
        'INVOICE_WITH_BALANCE' => ['Invoice With Balance', 'invoice_with_balance.index', [
            'View Invoice Balances' => 'VIEW_INVOICE_WITH_BALANCE_SUBMENU',
        ]],
    ]],
    'BADGE' => ['Badges', 1, 'icon-vcard', [
        'EXHIBITOR_BADGE' => ['Exhibitor Badges', 'exhibitors_list.index', [
            'View Badges List' => 'VIEW_BADGES_LIST',
            'Create Exhibitors Badges' => 'CREATE_EXHIBITORS_BADGES',
            'Create Member Badges' => 'CREATE_MEMBER_BADGES',
            'View Member Badges' => 'VIEW_MEMBER_BADGES',
            'Block Member Badges' => 'BLOCK_EXHIBITOR_BADGE',
            'Delete Member Badges' => 'DELETE_EXHIBITOR_BADGE',
        ]],
        'BADGE_GROUP' => ['Badge Groups', 'badge_groups.index', [
            'View Badge Group' => 'VIEW_BADGE_GROUP_SUBMENU',
            'Create Badge Group' => 'CREATE_BADGE_GROUP',
            'Update Badge Group' => 'UPDATE_BADGE_GROUP',
            'Delete Badge Group' => 'DELETE_BADGE_GROUP',
        ]],
        'PREPARE_BADGE' => ['Prepare Badges', 'prepare_badges.index', [
            'View Badges List' => 'VIEW_BADGES_LIST',
        ]],
        'PRINT_BADGE' => ['Print Badges', 'print_badges.index', [
            'View Badges List' => 'VIEW_BADGES_LIST',
            'Print Exhibitor Badges' => 'PRINT_PRINT_BADGE',
        ]],
        'REUSE_BADGE' => ['Reuse Badges', 'reuse_badges.index', [
            'View Member Badges' => 'VIEW_MEMBER_BADGES',
        ]],
        'EMERGENCY_BADGE' => ['Emergency Badges', 'emergency_badges.index', [
            'View Badges List' => 'VIEW_BADGES_LIST',
        ]],
    ]],
    'STICKER' => ['Vehicle Stickers', 1, 'icon-car', [
        'STICKER' => ['Stickers', 'stickers.index', [
            'View Stickers' => 'VIEW_STICKER_SUBMENU',
            'Create Stickers' => 'CREATE_STICKER',
            'Update Stickers' => 'UPDATE_STICKER',
            'Delete Stickers' => 'DELETE_STICKER',
        ]],
        'PRINT_STICKER' => ['Print Stickers', 'print_stickers.index', [
            'Print Stickers' => 'PRINT_PRINT_STICKER',
        ]],
    ]],
    'SPONSORSHIP' => ['Sponsorship', 1, 'icon-trophy2', [
        'SPONSORSHIP_PACKAGE' => ['Sponsorships', 'sponsorships.index', [
            'Manage Sponsorship' => 'MANAGE_SPONSORSHIP',
            'Delete SponsorshipPackages' => 'DELETE_SPONSORSHIP_PACKAGE',
        ]],
        'SPONSORSHIP_BOOKING' => ['Sponsorship Bookings', 'sponsorship-bookings.index', [
            'Book Sponsorship' => 'BOOK_SPONSORSHIP',
            'Book Sponsorship Package' => 'BOOK_SPONSORSHIP_PACKAGE',
            'View Sponsorship Packages' => 'VIEW_SPONSORSHIP_BOOKING',
            'Delete Sponsorship Packages' => 'DELETE_SPONSORSHIP_BOOKING',
        ]],
        'SPONSORSHIP_BENEFIT' => ['Sponsorship Benefits', 'sponsorship_benefits.index', [
            'Manage Sponsorship' => 'MANAGE_SPONSORSHIP',
        ]],
        'SPONSORSHIP_SPACE' => ['Sponsorship Spaces', 'sponsorship_spaces.index', [
            'View Sponsorship Spaces' => 'VIEW_SPONSORSHIP_SPACE_SUBMENU',
            'Create Sponsorship Spaces' => 'CREATE_SPONSORSHIP_SPACE',
            'Delete Sponsorship Spaces' => 'DELETE_SPONSORSHIP_SPACE',
        ]],
        'PACKAGE_GROUP' => ['Package Groups', 'package_groups.index', [
            'Manage PackageGroups' => 'MANAGE_PACKAGE_GROUP',
        ]],
        'CUSTOMER_PACKAGE' => ['Customer Packages', 'customer_packages.index', [
            'Manage BookedPackages' => 'MANAGE_CUSTOMER_PACKAGE',
            'Create Customer Packages' => 'CREATE_CUSTOMER_PACKAGE',
            'Update Customer Packages' => 'UPDATE_CUSTOMER_PACKAGE',
            'Delete Customer Packages' => 'DELETE_CUSTOMER_PACKAGE',
        ]],
        'RECOGNIZED_SPONSOR' => ['Recognized Sponsors', 'recoginized_sponsors.index', [
            'View Event Sponsors' => 'VIEW_RECOGNIZED_SPONSOR_SUBMENU',
            'Delete Event Sponsors' => 'DELETE_RECOGNIZED_SPONSOR',
        ]],
    ]],
    'COMMUNICATION' => ['Communication', 1, 'icon-envelop', [
        'BULK_SMS' => ['Bulk Sms', 'bulk_sms.index', [
            'View Bulk Sms' => 'VIEW_BULK_SMS_SUBMENU',
            'Create Bulk Sms' => 'CREATE_BULK_SMS',
            'Update Bulk Sms' => 'UPDATE_BULK_SMS',
            'Delete Bulk Sms' => 'DELETE_BULK_SMS',
        ]],
        'CUSTOMER_GROUP' => ['Customer Groups', 'customer_groups.index', [
            'View Customer Group' => 'VIEW_CUSTOMER_GROUP_SUBMENU',
            'Create Customer Group' => 'CREATE_CUSTOMER_GROUP',
            'Update Customer Group' => 'UPDATE_CUSTOMER_GROUP',
            'Delete Customer Group' => 'DELETE_CUSTOMER_GROUP',
        ]],
        'MESSAGING' => ['Messaging', 'messaging.index', [
            'View All Messages' => 'VIEW_MESSAGING_SUBMENU',
            'Delete Messages' => 'DELETE_MESSAGING',
        ]],
        'TEXT_ADVERTISEMENT' => ['Text Advertisements', 'text_advertisements.index', [
            'Manage Advatisment' => 'MANAGE_ADVATISMENT',
            'Manage Advatisments' => 'MANAGE_ADVATISMENTS',
        ]],
       
    ]],
    'REPORT' => ['Reports', 1, 'icon-stats-growth', [
        'INVOICE_REPORT' => ['Invoice Reports', 'invoice-reports.index', [
            'Manage InvoiceReports' => 'MANAGE_INVOICE_REPORTS',
            'Manage CollectionReports' => 'MANAGE_COLLECTION_REPORTS',
        ]],
        'ADVICE_NOTE_REPORT' => ['Advice Note Reports', 'adviceNote-reports.index', [
            'View Invoice Reports' => 'VIEW_ADVICE_NOTE_REPORT_SUBMENU',
        ]],
        'SPACE_BOOKING_REPORT' => ['Space Booking Report', 'space-booking-report.index', [
            'view-space-booking-report' => 'VIEW_SPACE_BOOKING_REPORT_SUBMENU',
            'Manage SpaceReports' => 'MANAGE_SPACE_BOOKING_REPORT',
        ]],
        'COLLECTION_REPORT' => ['Collection Reports', 'collection_reports.index', [
            'Manage CollectionReports' => 'MANAGE_COLLECTION_REPORTS',
        ]],
        'PAVILION_REPORT' => ['Pavilion Reports', 'pavilionReports.index', [
            'View Pavilion Reports' => 'VIEW_PAVILION_REPORT_SUBMENU',
        ]],
        'GROUND_VISITOR' => ['Ground Visitors', 'ground_visitors.index', [
            'View Visitors' => 'VIEW_VISITORS',
        ]],
    ]],
    'PERMIT' => ['Event Permits', 2, 'icon-file-check', [
        'PERMIT_APPLICATION' => ['Apply Permit', 'apply_permit.index', [
            'Manage MyApplications' => 'MANAGE_PERMIT_APPLICATION',
            'Delete MyApplications' => 'DELETE_PERMIT_APPLICATION',
        ]],
        'APPLICATION_REVIEW' => ['Review Applications', 'review_applications.index', [
            'Review Applications' => 'REVIEW_APPLICATIONS',
            'Review AllApplications' => 'REVIEW_ALL_APPLICATIONS',
            'Verify Application' => 'VERIFY_APPLICATION_REVIEW',
            'Review StageTwo' => 'REVIEW_STAGE_TWO',
            'Review StageThree' => 'REVIEW_STAGE_THREE',
            'Review StageFour' => 'REVIEW_STAGE_FOUR',
        ]],
        'PERMIT_INVOICE' => ['Permit Invoices', 'app_invoices.index', [
            'Manage Invoices' => 'MANAGE_INVOICES',
            'Delete Invoices' => 'DELETE_PERMIT_INVOICE',
        ]],
        'PERMIT_EVENT_TYPE' => ['Event Types', 'event_types.index', [
            'Manage EventTypes' => 'MANAGE_PERMIT_EVENT_TYPE',
        ]],
    ]],
    'TRADE_PROMOTION' => ['Trade Promotion', 2, 'icon-megaphone', [
        'PROMOTION_EVENT' => ['Promotion Events', 'promotion_events.index', [
            'Manage PromotionEvents' => 'MANAGE_PROMOTION_EVENTS',
            'Manage PromotionEvent' => 'MANAGE_PROMOTION_EVENT',
            'Delete PromotionEvents' => 'DELETE_PROMOTION_EVENT',
        ]],
        'EVENT_PARTICIPANT' => ['Event Participants', 'event_participants.index', [
            'Manage EventParticipants' => 'MANAGE_EVENT_PARTICIPANT',
            'manage EventParticipants' => 'MANAGE_EVENT_PARTICIPANT',
            'Delete EventParticipants' => 'DELETE_EVENT_PARTICIPANT',
        ]],
    ]],
];

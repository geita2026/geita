<?php

namespace App\Support;

use App\Models\Exhibitor;
use Illuminate\Validation\Rule;

/**
 * Who an exhibitor is, collected when they apply (or edit their profile):
 *
 * - individual: a person exhibiting without a registered business. Billed in
 *   their own name; no organisation name, registration number or TIN.
 * - business: a registered organisation, with its registration number.
 *
 * One set of rules for the application form, the profile page and staff
 * capturing an application on someone's behalf.
 */
class ExhibitorProfile
{
    public const TYPES = ['individual', 'business'];

    public static function rules(?string $type): array
    {
        $rules = [
            'entity_type' => ['required', Rule::in(self::TYPES)],
            'phone' => 'required|string|max:30',
            'country' => ['required', Rule::in(config('countries'))],
            'physical_address' => 'nullable|string|max:255',
            'business_sector' => 'nullable|string|max:255',
            'business_activity' => 'nullable|string|max:2000',
        ];

        return $type === 'business'
            ? $rules + [
                'organization_name' => 'required|string|max:255',
                'registration_number' => 'required|string|max:100',
                'tin' => 'nullable|string|max:50',
                'contact_person' => 'required|string|max:255',
            ]
            : $rules + ['national_id' => 'nullable|string|max:40'];
    }

    /** Applies validated profile data. An individual's "organisation" is themselves. */
    public static function apply(Exhibitor $exhibitor, array $data, string $personName): Exhibitor
    {
        if ($data['entity_type'] === 'individual') {
            $data['organization_name'] = $personName;
            $data['contact_person'] = $personName;
            $data['registration_number'] = null;
            $data['tin'] = null;
        } else {
            $data['national_id'] = null;
        }
        $exhibitor->update($data);

        return $exhibitor;
    }
}

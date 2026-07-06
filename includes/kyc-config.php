<?php
/**
 * KYC Dynamic Configuration
 * Country profiles from bank_operating_country with optional admin custom field override.
 */

if (!function_exists('kycMapCountryNameToCode')) {
    function kycMapCountryNameToCode($countryName) {
        $normalized = strtolower(trim((string)$countryName));
        $map = [
            'united states' => 'US',
            'united states of america' => 'US',
            'usa' => 'US',
            'us' => 'US',
            'canada' => 'CA',
            'ca' => 'CA',
            'united kingdom' => 'GB',
            'great britain' => 'GB',
            'england' => 'GB',
            'uk' => 'GB',
            'gb' => 'GB',
        ];
        return $map[$normalized] ?? 'US';
    }
}

if (!function_exists('getKycCountryProfiles')) {
    function getKycCountryProfiles() {
        return [
            'US' => [
                'code' => 'US',
                'name' => 'United States',
                'subtitle' => 'Complete your identity verification following U.S. banking standards',
                'identity_key' => 'ssn',
                'identity_label' => 'SSN/ITIN',
                'identity_placeholder' => 'XXX-XX-XXXX',
                'identity_maxlength' => 11,
                'identity_pattern' => '/^\d{3}-\d{2}-\d{4}$/',
                'identity_column' => 'ssn',
                'identity_encrypted' => true,
                'state_label' => 'State',
                'zip_label' => 'ZIP Code',
                'default_country' => 'United States',
                'id_issued_state_label' => 'ID Issued State',
                'id_issued_country_default' => 'United States',
                'id_types' => [
                    'drivers_license' => "Driver's License",
                    'state_id' => 'State ID',
                    'passport' => 'U.S. Passport',
                    'military_id' => 'Military ID',
                ],
            ],
            'CA' => [
                'code' => 'CA',
                'name' => 'Canada',
                'subtitle' => 'Complete your identity verification following Canadian banking standards',
                'identity_key' => 'ssn',
                'identity_label' => 'SIN (Social Insurance Number)',
                'identity_placeholder' => 'XXX-XXX-XXX',
                'identity_maxlength' => 11,
                'identity_pattern' => '/^\d{3}-\d{3}-\d{3}$/',
                'identity_column' => 'ssn',
                'identity_encrypted' => true,
                'state_label' => 'Province',
                'zip_label' => 'Postal Code',
                'default_country' => 'Canada',
                'id_issued_state_label' => 'ID Issued Province',
                'id_issued_country_default' => 'Canada',
                'id_types' => [
                    'drivers_license' => "Driver's Licence",
                    'passport' => 'Canadian Passport',
                    'provincial_id' => 'Provincial Photo ID',
                ],
            ],
            'GB' => [
                'code' => 'GB',
                'name' => 'United Kingdom',
                'subtitle' => 'Complete your identity verification following UK banking standards',
                'identity_key' => 'ssn',
                'identity_label' => 'National Insurance Number',
                'identity_placeholder' => 'AB 12 34 56 C',
                'identity_maxlength' => 13,
                'identity_pattern' => '/^[A-CEGHJ-PR-TW-Z]{2}\d{6}[A-D]$/i',
                'identity_column' => 'ssn',
                'identity_encrypted' => true,
                'state_label' => 'County',
                'zip_label' => 'Postcode',
                'default_country' => 'United Kingdom',
                'id_issued_state_label' => 'ID Issued County',
                'id_issued_country_default' => 'United Kingdom',
                'id_types' => [
                    'passport' => 'UK Passport',
                    'driving_licence' => 'Driving Licence',
                ],
            ],
        ];
    }
}

if (!function_exists('getKycActiveCountryCode')) {
    function getKycActiveCountryCode() {
        $countryName = getSystemSetting('bank_operating_country', 'United States');
        return kycMapCountryNameToCode($countryName);
    }
}

if (!function_exists('getKycActiveProfile')) {
    function getKycActiveProfile() {
        $profiles = getKycCountryProfiles();
        $code = getKycActiveCountryCode();
        return $profiles[$code] ?? $profiles['US'];
    }
}

if (!function_exists('parseKycCustomFields')) {
    function parseKycCustomFields($json) {
        if (empty($json)) {
            return [];
        }
        $decoded = is_array($json) ? $json : json_decode($json, true);
        if (!is_array($decoded)) {
            return [];
        }
        $fields = [];
        foreach ($decoded as $field) {
            if (!is_array($field) || empty($field['key']) || empty($field['label'])) {
                continue;
            }
            $fields[] = [
                'key' => preg_replace('/[^a-z0-9_]/i', '', (string)$field['key']),
                'label' => (string)$field['label'],
                'type' => in_array($field['type'] ?? 'text', ['text', 'textarea', 'date', 'select', 'file'], true)
                    ? $field['type'] : 'text',
                'required' => !empty($field['required']),
                'pattern' => !empty($field['pattern']) ? (string)$field['pattern'] : null,
                'placeholder' => (string)($field['placeholder'] ?? ''),
                'options' => is_array($field['options'] ?? null) ? $field['options'] : [],
                'step' => in_array($field['step'] ?? 'personal', ['personal', 'compliance'], true)
                    ? $field['step'] : 'personal',
            ];
        }
        return $fields;
    }
}

if (!function_exists('isKycCustomFieldsEnabled')) {
    function isKycCustomFieldsEnabled() {
        return getSystemSetting('kyc_use_custom_fields', '0') === '1';
    }
}

if (!function_exists('getKycFieldsForUser')) {
    /**
     * @return array Active KYC field configuration for a user
     */
    function getKycFieldsForUser($userId) {
        $profile = getKycActiveProfile();
        $useCustom = isKycCustomFieldsEnabled();
        $customFields = $useCustom ? parseKycCustomFields(getSystemSetting('kyc_custom_fields', '[]')) : [];

        $config = [
            'country_code' => $profile['code'],
            'country_name' => $profile['name'],
            'subtitle' => $profile['subtitle'],
            'use_custom' => $useCustom && !empty($customFields),
            'profile' => $profile,
            'custom_fields' => $customFields,
            'show_government_id_step' => !($useCustom && !empty($customFields)),
            'document_fields' => [
                ['key' => 'id_document_front', 'label' => 'Upload ID Front', 'required' => true],
                ['key' => 'id_document_back', 'label' => 'Upload ID Back', 'required' => false],
                ['key' => 'proof_of_address', 'label' => 'Upload Proof of Address', 'required' => true,
                 'hint' => 'Utility bill, bank statement, or lease agreement (dated within last 3 months)'],
                ['key' => 'signature_image', 'label' => 'Digital Signature', 'required' => false,
                 'hint' => 'Upload your digital signature (optional)'],
            ],
            'compliance_fields' => [
                ['key' => 'source_of_funds', 'label' => 'Source of Funds', 'required' => true,
                 'placeholder' => 'Describe where your funds come from (e.g., employment, investments, inheritance, etc.)'],
                ['key' => 'account_purpose', 'label' => 'Account Purpose', 'required' => true,
                 'placeholder' => 'Describe the purpose of this account (e.g., personal savings, business operations, etc.)'],
            ],
        ];

        if ($config['use_custom']) {
            $config['subtitle'] = 'Complete your identity verification with the required information for your account';
        }

        return $config;
    }
}

if (!function_exists('getKycFieldLabels')) {
    /**
     * Flat map of field keys to human-readable labels for summaries and review.
     */
    function getKycFieldLabels($userId = null) {
        $config = getKycFieldsForUser($userId ?? ($_SESSION['user_id'] ?? 0));
        $profile = $config['profile'];
        $labels = [
            'full_legal_name' => 'Full Legal Name',
            'date_of_birth' => 'Date of Birth',
            'ssn' => $profile['identity_label'],
            'residential_address' => 'Street Address',
            'residential_city' => 'City',
            'residential_state' => $profile['state_label'],
            'residential_zip' => $profile['zip_label'],
            'residential_country' => 'Country',
            'id_type' => 'ID Type',
            'id_number' => 'ID Number',
            'id_issued_date' => 'ID Issued Date',
            'id_expiry_date' => 'ID Expiry Date',
            'id_issued_state' => $profile['id_issued_state_label'],
            'id_issued_country' => 'ID Issued Country',
            'id_document_front' => 'ID Front Document',
            'id_document_back' => 'ID Back Document',
            'proof_of_address' => 'Proof of Address',
            'signature_image' => 'Digital Signature',
            'source_of_funds' => 'Source of Funds',
            'account_purpose' => 'Account Purpose',
        ];

        foreach ($config['custom_fields'] as $field) {
            $labels[$field['key']] = $field['label'];
        }

        return $labels;
    }
}

if (!function_exists('getKycExtraFieldsFromRecord')) {
    function getKycExtraFieldsFromRecord($kycRecord) {
        if (!is_array($kycRecord) || empty($kycRecord['extra_fields'])) {
            return [];
        }
        $decoded = is_array($kycRecord['extra_fields'])
            ? $kycRecord['extra_fields']
            : json_decode($kycRecord['extra_fields'], true);
        return is_array($decoded) ? $decoded : [];
    }
}

if (!function_exists('getKycFieldValue')) {
    function getKycFieldValue($kycRecord, $key, $extraFields = null) {
        if (!is_array($kycRecord)) {
            return '';
        }
        if (array_key_exists($key, $kycRecord) && $kycRecord[$key] !== null && $kycRecord[$key] !== '') {
            if ($key === 'ssn') {
                return '[On file]';
            }
            return $kycRecord[$key];
        }
        if ($extraFields === null) {
            $extraFields = getKycExtraFieldsFromRecord($kycRecord);
        }
        return $extraFields[$key] ?? '';
    }
}

if (!function_exists('formatKycIdTypeLabel')) {
    function formatKycIdTypeLabel($idType, $userId = null) {
        if (empty($idType)) {
            return 'N/A';
        }
        $config = getKycFieldsForUser($userId ?? ($_SESSION['user_id'] ?? 0));
        $idTypes = $config['profile']['id_types'] ?? [];
        return $idTypes[$idType] ?? ucfirst(str_replace('_', ' ', (string)$idType));
    }
}

if (!function_exists('validateKycSubmission')) {
    /**
     * Validate KYC POST data and uploaded files.
     *
     * @param array $data POST data (sanitized)
     * @param array $files $_FILES
     * @param array|null $existingKyc Existing KYC record for resubmission
     * @param int|null $userId User ID
     * @return array ['valid' => bool, 'errors' => string[]]
     */
    function validateKycSubmission($data, $files = [], $existingKyc = null, $userId = null) {
        $errors = [];
        $config = getKycFieldsForUser($userId ?? ($data['user_id'] ?? 0));
        $profile = $config['profile'];
        $isResubmit = is_array($existingKyc) && !empty($existingKyc);

        if (empty(trim($data['full_legal_name'] ?? ''))) {
            $errors[] = 'Full legal name is required.';
        }
        if (empty($data['date_of_birth'] ?? '')) {
            $errors[] = 'Date of birth is required.';
        }

        if ($config['use_custom']) {
            foreach ($config['custom_fields'] as $field) {
                $key = $field['key'];
                $value = trim((string)($data[$key] ?? ''));
                if ($field['type'] === 'file') {
                    $hasUpload = isset($files[$key]) && ($files[$key]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK;
                    $hasExisting = $isResubmit && !empty(getKycFieldValue($existingKyc, $key));
                    if ($field['required'] && !$hasUpload && !$hasExisting) {
                        $errors[] = $field['label'] . ' is required.';
                    }
                    continue;
                }
                if ($field['required'] && $value === '') {
                    $errors[] = $field['label'] . ' is required.';
                    continue;
                }
                if ($value !== '' && !empty($field['pattern'])) {
                    $pattern = $field['pattern'];
                    if (@preg_match($pattern, $value) !== 1) {
                        $errors[] = $field['label'] . ' format is invalid.';
                    }
                }
            }
        } else {
            $ssn = trim((string)($data['ssn'] ?? ''));
            $hasExistingSsn = $isResubmit && !empty($existingKyc['ssn']);
            if ($ssn === '' && !$hasExistingSsn) {
                $errors[] = $profile['identity_label'] . ' is required.';
            } elseif ($ssn !== '' && !empty($profile['identity_pattern'])) {
                $normalized = $profile['code'] === 'GB' ? strtoupper(preg_replace('/\s+/', '', $ssn)) : $ssn;
                if (!preg_match($profile['identity_pattern'], $normalized)) {
                    $errors[] = $profile['identity_label'] . ' format is invalid.';
                }
            }

            foreach (['residential_address', 'residential_city', 'residential_state', 'residential_zip', 'residential_country'] as $field) {
                if (empty(trim((string)($data[$field] ?? '')))) {
                    $labels = getKycFieldLabels($userId);
                    $errors[] = ($labels[$field] ?? $field) . ' is required.';
                }
            }

            if (empty($data['id_type'] ?? '')) {
                $errors[] = 'ID type is required.';
            } elseif (!isset($profile['id_types'][$data['id_type']])) {
                $errors[] = 'Invalid ID type selected.';
            }
            if (empty(trim((string)($data['id_number'] ?? '')))) {
                $errors[] = 'ID number is required.';
            }
            foreach (['id_issued_date', 'id_expiry_date'] as $dateField) {
                if (empty($data[$dateField] ?? '')) {
                    $errors[] = getKycFieldLabels($userId)[$dateField] . ' is required.';
                }
            }
            if (empty(trim((string)($data['id_issued_state'] ?? '')))) {
                $errors[] = $profile['id_issued_state_label'] . ' is required.';
            }
            if (empty(trim((string)($data['id_issued_country'] ?? '')))) {
                $errors[] = 'ID issued country is required.';
            }
        }

        foreach ($config['document_fields'] as $docField) {
            if (!$docField['required']) {
                continue;
            }
            $key = $docField['key'];
            $hasUpload = isset($files[$key]) && ($files[$key]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK;
            $hasExisting = $isResubmit && !empty($existingKyc[$key] ?? '');
            if (!$hasUpload && !$hasExisting) {
                $errors[] = $docField['label'] . ' is required.';
            }
        }

        foreach ($config['compliance_fields'] as $field) {
            if ($field['required'] && empty(trim((string)($data[$field['key']] ?? '')))) {
                $errors[] = $field['label'] . ' is required.';
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }
}

if (!function_exists('buildKycSubmissionData')) {
    /**
     * Build KYC database payload from POST, separating core columns and extra_fields JSON.
     */
    function buildKycSubmissionData($post, $files, $existingKyc, $userId) {
        $config = getKycFieldsForUser($userId);
        $profile = $config['profile'];
        $extraFields = getKycExtraFieldsFromRecord($existingKyc);

        $data = [
            'user_id' => $userId,
            'account_type' => Security::sanitize($post['account_type'] ?? 'individual'),
            'full_legal_name' => Security::sanitize($post['full_legal_name'] ?? ''),
            'date_of_birth' => $post['date_of_birth'] ?? null,
            'source_of_funds' => Security::sanitize($post['source_of_funds'] ?? ''),
            'account_purpose' => Security::sanitize($post['account_purpose'] ?? ''),
        ];

        if ($config['use_custom']) {
            $data['ssn'] = is_array($existingKyc) && !empty($existingKyc['ssn']) ? $existingKyc['ssn'] : null;
            $data['residential_address'] = null;
            $data['residential_city'] = null;
            $data['residential_state'] = null;
            $data['residential_country'] = null;
            $data['residential_zip'] = null;
            $data['id_type'] = null;
            $data['id_number'] = null;
            $data['id_issued_date'] = null;
            $data['id_expiry_date'] = null;
            $data['id_issued_state'] = null;
            $data['id_issued_country'] = null;

            foreach ($config['custom_fields'] as $field) {
                $key = $field['key'];
                if ($field['type'] === 'file') {
                    if (isset($files[$key]) && ($files[$key]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                        $upload = uploadFile($files[$key], 'kyc');
                        if ($upload['success']) {
                            $extraFields[$key] = $upload['path'];
                        }
                    } elseif (!empty($extraFields[$key])) {
                        // keep existing
                    }
                    continue;
                }
                $value = Security::sanitize($post[$key] ?? '');
                if ($value !== '') {
                    $extraFields[$key] = $value;
                }
            }
        } else {
            $data['ssn'] = !empty($post['ssn'])
                ? encryptData(Security::sanitize($post['ssn']))
                : (is_array($existingKyc) && !empty($existingKyc['ssn']) ? $existingKyc['ssn'] : null);
            $data['residential_address'] = Security::sanitize($post['residential_address'] ?? '');
            $data['residential_city'] = Security::sanitize($post['residential_city'] ?? '');
            $data['residential_state'] = Security::sanitize($post['residential_state'] ?? '');
            $data['residential_country'] = Security::sanitize($post['residential_country'] ?? $profile['default_country']);
            $data['residential_zip'] = Security::sanitize($post['residential_zip'] ?? '');
            $data['id_type'] = Security::sanitize($post['id_type'] ?? '');
            $data['id_number'] = Security::sanitize($post['id_number'] ?? '');
            $data['id_issued_date'] = $post['id_issued_date'] ?? null;
            $data['id_expiry_date'] = $post['id_expiry_date'] ?? null;
            $data['id_issued_state'] = Security::sanitize($post['id_issued_state'] ?? '');
            $data['id_issued_country'] = Security::sanitize($post['id_issued_country'] ?? $profile['id_issued_country_default']);
        }

        foreach ($config['document_fields'] as $docField) {
            $key = $docField['key'];
            if (isset($files[$key]) && ($files[$key]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                $upload = uploadFile($files[$key], 'kyc');
                if ($upload['success']) {
                    $data[$key] = $upload['path'];
                }
            } elseif (is_array($existingKyc) && !empty($existingKyc[$key])) {
                $data[$key] = $existingKyc[$key];
            }
        }

        $data['extra_fields'] = !empty($extraFields) ? json_encode($extraFields) : null;

        return $data;
    }
}

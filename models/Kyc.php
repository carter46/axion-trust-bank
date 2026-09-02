<?php
class Kyc {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // Get KYC verification for user
    public function findByUserId($userId) {
        $sql = "SELECT * FROM kyc_verifications WHERE user_id = ?";
        $stmt = $this->db->query($sql, [$userId]);
        return $stmt ? $stmt->fetch() : null;
    }

    // Get KYC verification by ID
    public function findById($kycId) {
        $sql = "SELECT * FROM kyc_verifications WHERE id = ? LIMIT 1";
        $stmt = $this->db->query($sql, [$kycId]);
        return $stmt ? $stmt->fetch() : null;
    }
    
    // Get all KYC verifications with user info
    public function getAll($filters = []) {
        $sql = "SELECT kv.*, u.full_name, u.email, u.phone 
                FROM kyc_verifications kv 
                LEFT JOIN users u ON kv.user_id = u.id 
                WHERE 1=1";
        $params = [];
        
        if (isset($filters['status'])) {
            $sql .= " AND kv.status = ?";
            $params[] = $filters['status'];
        }
        
        if (isset($filters['account_type'])) {
            $sql .= " AND kv.account_type = ?";
            $params[] = $filters['account_type'];
        }
        
        $sql .= " ORDER BY kv.submitted_at DESC";
        
        if (isset($filters['limit'])) {
            $sql .= " LIMIT " . intval($filters['limit']);
        }
        
        $stmt = $this->db->query($sql, $params);
        return $stmt ? $stmt->fetchAll() : [];
    }
    
    // Create KYC verification
    public function create($data) {
        try {
            $sql = "INSERT INTO kyc_verifications (
                user_id, account_type, full_legal_name, date_of_birth, ssn,
                residential_address, residential_city, residential_state, residential_country, residential_zip,
                id_type, id_number, id_issued_date, id_expiry_date, id_issued_state, id_issued_country,
                id_document_front, id_document_back, proof_of_address, signature_image,
                business_name, business_address, business_city, business_state, business_country, business_zip,
                ein, business_formation_doc, source_of_funds, account_purpose, extra_fields, status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $params = [
                $data['user_id'],
                $data['account_type'] ?? 'individual',
                $data['full_legal_name'] ?? null,
                $data['date_of_birth'] ?? null,
                $data['ssn'] ?? null,
                $data['residential_address'] ?? null,
                $data['residential_city'] ?? null,
                $data['residential_state'] ?? null,
                $data['residential_country'] ?? null,
                $data['residential_zip'] ?? null,
                $data['id_type'] ?? null,
                $data['id_number'] ?? null,
                $data['id_issued_date'] ?? null,
                $data['id_expiry_date'] ?? null,
                $data['id_issued_state'] ?? null,
                $data['id_issued_country'] ?? null,
                $data['id_document_front'] ?? null,
                $data['id_document_back'] ?? null,
                $data['proof_of_address'] ?? null,
                $data['signature_image'] ?? null,
                $data['business_name'] ?? null,
                $data['business_address'] ?? null,
                $data['business_city'] ?? null,
                $data['business_state'] ?? null,
                $data['business_country'] ?? null,
                $data['business_zip'] ?? null,
                $data['ein'] ?? null,
                $data['business_formation_doc'] ?? null,
                $data['source_of_funds'] ?? null,
                $data['account_purpose'] ?? null,
                $data['extra_fields'] ?? null,
                'pending'
            ];
            
            $this->db->query($sql, $params);
            $kycId = $this->db->getConnection()->lastInsertId();
            
            // Update user's kyc_submitted_at and kyc_status
            $updateUser = "UPDATE users SET kyc_submitted_at = NOW(), kyc_status = 'pending' WHERE id = ?";
            $this->db->query($updateUser, [$data['user_id']]);
            
            return ['success' => true, 'kyc_id' => $kycId];
        } catch (Exception $e) {
            error_log("KYC Create Error: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    // Update KYC verification
    public function update($kycId, $data) {
        try {
            $sql = "UPDATE kyc_verifications SET 
                account_type = ?, full_legal_name = ?, date_of_birth = ?, ssn = ?,
                residential_address = ?, residential_city = ?, residential_state = ?, residential_country = ?, residential_zip = ?,
                id_type = ?, id_number = ?, id_issued_date = ?, id_expiry_date = ?, id_issued_state = ?, id_issued_country = ?,
                id_document_front = ?, id_document_back = ?, proof_of_address = ?, signature_image = ?,
                business_name = ?, business_address = ?, business_city = ?, business_state = ?, business_country = ?, business_zip = ?,
                ein = ?, business_formation_doc = ?, source_of_funds = ?, account_purpose = ?, extra_fields = ?,
                status = 'pending'
                WHERE id = ?";
            
            $this->db->query($sql, [
                $data['account_type'] ?? 'individual',
                $data['full_legal_name'] ?? null,
                $data['date_of_birth'] ?? null,
                $data['ssn'] ?? null,
                $data['residential_address'] ?? null,
                $data['residential_city'] ?? null,
                $data['residential_state'] ?? null,
                $data['residential_country'] ?? null,
                $data['residential_zip'] ?? null,
                $data['id_type'] ?? null,
                $data['id_number'] ?? null,
                $data['id_issued_date'] ?? null,
                $data['id_expiry_date'] ?? null,
                $data['id_issued_state'] ?? null,
                $data['id_issued_country'] ?? null,
                $data['id_document_front'] ?? null,
                $data['id_document_back'] ?? null,
                $data['proof_of_address'] ?? null,
                $data['signature_image'] ?? null,
                $data['business_name'] ?? null,
                $data['business_address'] ?? null,
                $data['business_city'] ?? null,
                $data['business_state'] ?? null,
                $data['business_country'] ?? null,
                $data['business_zip'] ?? null,
                $data['ein'] ?? null,
                $data['business_formation_doc'] ?? null,
                $data['source_of_funds'] ?? null,
                $data['account_purpose'] ?? null,
                $data['extra_fields'] ?? null,
                $kycId
            ]);
            
            $userId = $data['user_id'] ?? null;
            if (!$userId) {
                $stmt = $this->db->query("SELECT user_id FROM kyc_verifications WHERE id = ? LIMIT 1", [$kycId]);
                $row = $stmt ? $stmt->fetch() : null;
                $userId = $row['user_id'] ?? null;
            }
            if ($userId) {
                $updateUser = "UPDATE users SET kyc_status = 'pending', kyc_submitted_at = NOW() WHERE id = ?";
                $this->db->query($updateUser, [$userId]);
            }
            
            return true;
        } catch (Exception $e) {
            error_log("KYC Update Error: " . $e->getMessage());
            return false;
        }
    }

    // Admin update KYC details without changing verification status
    public function adminUpdate($kycId, $data, $adminId, $adminNotes = null) {
        try {
            $existing = $this->findById($kycId);
            if (!$existing) {
                return ['success' => false, 'message' => 'KYC record not found'];
            }

            $sql = "UPDATE kyc_verifications SET
                account_type = ?, full_legal_name = ?, date_of_birth = ?, ssn = ?,
                residential_address = ?, residential_city = ?, residential_state = ?, residential_country = ?, residential_zip = ?,
                id_type = ?, id_number = ?, id_issued_date = ?, id_expiry_date = ?, id_issued_state = ?, id_issued_country = ?,
                id_document_front = ?, id_document_back = ?, proof_of_address = ?, signature_image = ?,
                business_name = ?, business_address = ?, business_city = ?, business_state = ?, business_country = ?, business_zip = ?,
                ein = ?, business_formation_doc = ?, source_of_funds = ?, account_purpose = ?, extra_fields = ?,
                updated_at = NOW()";

            $params = [
                $data['account_type'] ?? $existing['account_type'] ?? 'individual',
                $data['full_legal_name'] ?? null,
                $data['date_of_birth'] ?? null,
                $data['ssn'] ?? $existing['ssn'],
                $data['residential_address'] ?? null,
                $data['residential_city'] ?? null,
                $data['residential_state'] ?? null,
                $data['residential_country'] ?? null,
                $data['residential_zip'] ?? null,
                $data['id_type'] ?? null,
                $data['id_number'] ?? null,
                $data['id_issued_date'] ?? null,
                $data['id_expiry_date'] ?? null,
                $data['id_issued_state'] ?? null,
                $data['id_issued_country'] ?? null,
                $data['id_document_front'] ?? $existing['id_document_front'],
                $data['id_document_back'] ?? $existing['id_document_back'],
                $data['proof_of_address'] ?? $existing['proof_of_address'],
                $data['signature_image'] ?? $existing['signature_image'],
                $data['business_name'] ?? null,
                $data['business_address'] ?? null,
                $data['business_city'] ?? null,
                $data['business_state'] ?? null,
                $data['business_country'] ?? null,
                $data['business_zip'] ?? null,
                $data['ein'] ?? $existing['ein'],
                $data['business_formation_doc'] ?? $existing['business_formation_doc'],
                $data['source_of_funds'] ?? null,
                $data['account_purpose'] ?? null,
                $data['extra_fields'] ?? $existing['extra_fields'],
            ];

            if ($adminNotes !== null && trim($adminNotes) !== '') {
                $sql .= ", admin_notes = ?";
                $params[] = $adminNotes;
            }

            $sql .= " WHERE id = ?";
            $params[] = $kycId;

            $this->db->query($sql, $params);

            $this->db->query(
                "UPDATE users SET full_name = ?, date_of_birth = ? WHERE id = ?",
                [$data['full_legal_name'] ?? $existing['full_legal_name'], $data['date_of_birth'] ?? $existing['date_of_birth'], $existing['user_id']]
            );

            return ['success' => true];
        } catch (Exception $e) {
            error_log("KYC Admin Update Error: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    // Verify KYC (approve)
    public function verify($kycId, $verifiedBy, $adminNotes = null) {
        try {
            $sql = "UPDATE kyc_verifications SET 
                status = 'verified',
                verified_by = ?,
                verified_at = NOW(),
                admin_notes = ?
                WHERE id = ?";
            
            $this->db->query($sql, [$verifiedBy, $adminNotes, $kycId]);
            
            // Update user's kyc_status
            $getUserId = "SELECT user_id FROM kyc_verifications WHERE id = ?";
            $stmt = $this->db->query($getUserId, [$kycId]);
            $kyc = $stmt->fetch();
            
            if ($kyc) {
                $updateUser = "UPDATE users SET kyc_status = 'verified', status = 'active' WHERE id = ?";
                $this->db->query($updateUser, [$kyc['user_id']]);
                
                // Send notification
                $notification = new Notification();
                $notification->create(
                    $kyc['user_id'],
                    'KYC Verification Approved',
                    'Your KYC verification has been approved. You now have full access to all banking services.',
                    'success',
                    '/profile/kyc'
                );
                
                // Send approval email
                try {
                    if (!class_exists('User')) {
                        require_once __DIR__ . '/User.php';
                    }
                    $userModel = new User();
                    $user = $userModel->findById($kyc['user_id']);
                    if ($user) {
                        require_once __DIR__ . '/../includes/email-template.php';
                        require_once __DIR__ . '/../includes/functions.php';
                        $emailTemplate = new EmailTemplate();
                        $kycEmail = $emailTemplate->kycApprovedEmail($user['full_name']);
                        sendEmail($user['email'], 'KYC Approved - ' . getSiteName(), $kycEmail);
                    }
                } catch (Exception $e) {
                    error_log("KYC approval email error: " . $e->getMessage());
                    // Don't fail the approval if email fails
                }
            }
            
            return true;
        } catch (Exception $e) {
            error_log("KYC Verify Error: " . $e->getMessage());
            return false;
        }
    }
    
    // Reject KYC
    public function reject($kycId, $verifiedBy, $rejectionReason, $adminNotes = null) {
        try {
            $sql = "UPDATE kyc_verifications SET 
                status = 'rejected',
                verified_by = ?,
                verified_at = NOW(),
                rejection_reason = ?,
                admin_notes = ?
                WHERE id = ?";
            
            $this->db->query($sql, [$verifiedBy, $rejectionReason, $adminNotes, $kycId]);
            
            // Update user's kyc_status
            $getUserId = "SELECT user_id FROM kyc_verifications WHERE id = ?";
            $stmt = $this->db->query($getUserId, [$kycId]);
            $kyc = $stmt->fetch();
            
            if ($kyc) {
                $updateUser = "UPDATE users SET kyc_status = 'rejected' WHERE id = ?";
                $this->db->query($updateUser, [$kyc['user_id']]);
                
                // Send notification
                $notification = new Notification();
                $notification->create(
                    $kyc['user_id'],
                    'KYC Verification Rejected',
                    'Your KYC verification has been rejected. Reason: ' . $rejectionReason . '. Please update your information and resubmit.',
                    'error',
                    '/profile/kyc'
                );
                
                // Send rejection email
                try {
                    if (!class_exists('User')) {
                        require_once __DIR__ . '/User.php';
                    }
                    $userModel = new User();
                    $user = $userModel->findById($kyc['user_id']);
                    if ($user) {
                        require_once __DIR__ . '/../includes/email-template.php';
                        require_once __DIR__ . '/../includes/functions.php';
                        $emailTemplate = new EmailTemplate();
                        $kycEmail = $emailTemplate->kycRejectedEmail($user['full_name'], $rejectionReason);
                        sendEmail($user['email'], 'KYC Submission - Action Required - ' . getSiteName(), $kycEmail);
                    }
                } catch (Exception $e) {
                    error_log("KYC rejection email error: " . $e->getMessage());
                    // Don't fail the rejection if email fails
                }
            }
            
            return true;
        } catch (Exception $e) {
            error_log("KYC Reject Error: " . $e->getMessage());
            return false;
        }
    }
    
    // Add beneficial owner
    public function addBeneficialOwner($kycId, $data) {
        try {
            $sql = "INSERT INTO kyc_beneficial_owners (
                kyc_verification_id, first_name, last_name, date_of_birth,
                ownership_percentage, id_type, id_number,
                id_document_front, id_document_back, address, city, state, country, zip
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $this->db->query($sql, [
                $kycId,
                $data['first_name'],
                $data['last_name'],
                $data['date_of_birth'] ?? null,
                $data['ownership_percentage'],
                $data['id_type'] ?? null,
                $data['id_number'] ?? null,
                $data['id_document_front'] ?? null,
                $data['id_document_back'] ?? null,
                $data['address'] ?? null,
                $data['city'] ?? null,
                $data['state'] ?? null,
                $data['country'] ?? null,
                $data['zip'] ?? null
            ]);
            
            return true;
        } catch (Exception $e) {
            error_log("Add Beneficial Owner Error: " . $e->getMessage());
            return false;
        }
    }
    
    // Get beneficial owners
    public function getBeneficialOwners($kycId) {
        $sql = "SELECT * FROM kyc_beneficial_owners WHERE kyc_verification_id = ?";
        $stmt = $this->db->query($sql, [$kycId]);
        return $stmt ? $stmt->fetchAll() : [];
    }
}

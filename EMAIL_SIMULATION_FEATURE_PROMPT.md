# Email Simulation Flash Test Feature - Detailed Implementation Prompt

## Overview
Build a comprehensive email simulation testing system for financial transaction alerts. This feature allows admins to test SMTP configuration and email deliverability by simulating various credit alert scenarios with customizable parameters.

## Location & Integration
- **Main Page**: Add new section to `views/admin/email-test.php` (existing test email page)
- **Settings Page**: Create new page `views/admin/email-simulation-settings.php` for managing alert captions and templates
- **Controller**: Extend `controllers/AdminController.php` to handle new routes
- **API Endpoint**: Create `api/send-simulation-email.php` to process simulation requests
- **Database**: Create tables for storing alert captions and email templates

---

## Part 1: Database Schema

### Table 1: `email_simulation_alert_captions`
```sql
CREATE TABLE IF NOT EXISTS `email_simulation_alert_captions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `caption_text` VARCHAR(255) NOT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Default Captions to Insert:**
- "Funds Received"
- "Transaction Successful"
- "Payment Confirmed"
- "Deposit Completed"
- "Transfer Received"
- "Account Credited"

### Table 2: `email_simulation_templates`
```sql
CREATE TABLE IF NOT EXISTS `email_simulation_templates` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `template_name` VARCHAR(100) NOT NULL UNIQUE,
  `primary_color` VARCHAR(7) DEFAULT '#359eb4',
  `secondary_color` VARCHAR(7) DEFAULT '#2a7e90',
  `accent_color` VARCHAR(7) DEFAULT '#10b981',
  `logo_url` VARCHAR(500) DEFAULT NULL,
  `logo_alt_text` VARCHAR(255) DEFAULT 'Bank Logo',
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_active` (`is_active`),
  INDEX `idx_name` (`template_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Default Template to Insert:**
- Template Name: "Default Bank Template"
- Primary Color: #359eb4 (moonstone blue)
- Secondary Color: #2a7e90 (darker blue)
- Accent Color: #10b981 (green for credit)
- Logo URL: Use existing site logo from system settings
- Logo Alt Text: Site name from system settings

---

## Part 2: Simulation Settings Page

### File: `views/admin/email-simulation-settings.php`

**Purpose**: Admin interface to manage alert captions and email templates

**Features Needed:**

#### Section 1: Alert Captions Management
- **Display**: Table/list showing all alert captions with:
  - Caption text
  - Active/Inactive status toggle
  - Edit button
  - Delete button (soft delete - set is_active to 0)
- **Add New Caption Form**:
  - Text input for caption
  - Submit button
- **Edit Modal/Form**:
  - Pre-filled text input
  - Save button
  - Cancel button

#### Section 2: Email Templates Management
- **Display**: Cards/grid showing all templates with:
  - Template name
  - Color preview (show primary, secondary, accent colors as swatches)
  - Logo preview (if exists)
  - Active/Inactive status
  - Edit button
  - Delete button (soft delete)
- **Add New Template Form**:
  - Template name (text input, required, unique)
  - Primary color (color picker, default: #359eb4)
  - Secondary color (color picker, default: #2a7e90)
  - Accent color (color picker, default: #10b981)
  - Logo upload (file input) OR Logo URL (text input) - admin can choose either
  - Logo alt text (text input)
  - Active checkbox (default: checked)
  - Submit button
- **Edit Template Form** (similar to add, but pre-filled):
  - All same fields as add form
  - Pre-populate with existing values
  - Update button

**Styling**: Match existing admin pages (use same card styles, form styles from email-test.php)

**Controller Route**: `/admin/email/simulation-settings`

---

## Part 3: Simulation Flash Test Section

### File: `views/admin/email-test.php` (Add new section)

**Location**: Add after the existing "Send Test Email" card, before closing `</div>` of email-test-container

**Section Title**: "Simulation Flash Test"
**Icon**: `fas fa-flash` or `fas fa-bolt`
**Description**: "Test financial transaction emails with customizable scenarios"

### Form Fields (in order):

1. **Site Name** (Text Input)
   - Label: "Site/Bank Name *"
   - Placeholder: "Enter bank or site name"
   - Default: Current site name from system settings
   - Required: Yes
   - Help text: "This will appear in the email header and signature"

2. **Contact Method** (Dropdown/Select)
   - Label: "Contact Method *"
   - Options:
     - "Email" (value: "email")
     - "WhatsApp" (value: "whatsapp")
   - Required: Yes
   - Default: "email"
   - JavaScript: Show/hide next field based on selection

3. **Contact Information** (Conditional Input)
   - **If Email selected**:
     - Type: Email input
     - Label: "Recipient Email Address *"
     - Placeholder: "user@example.com"
     - Required: Yes (when email is selected)
   - **If WhatsApp selected**:
     - Type: Tel/Text input
     - Label: "WhatsApp Number *"
     - Placeholder: "+1234567890"
     - Pattern: Allow international format
     - Required: Yes (when WhatsApp is selected)
   - Help text: "Enter the contact where you want to receive the test notification"

4. **Amount** (Number Input)
   - Label: "Transaction Amount *"
   - Placeholder: "0.00"
   - Min: 0.01
   - Step: 0.01
   - Required: Yes
   - Help text: "Enter the transaction amount to display in the alert"

5. **Currency Type** (Dropdown/Select)
   - Label: "Currency *"
   - Options: Load from Currency class (use `Currency::getSupportedCurrencies()`)
   - Default: System default currency
   - Required: Yes
   - Help text: "Select the currency for this transaction"

6. **Transaction Status** (Dropdown/Select)
   - Label: "Transaction Status *"
   - Options:
     - "Completed" (value: "completed")
     - "Pending" (value: "pending")
     - "Processing" (value: "processing")
     - "Failed" (value: "failed")
   - Required: Yes
   - Default: "completed"
   - Help text: "Simulate different transaction states"

7. **Alert Caption** (Dropdown/Select)
   - Label: "Alert Caption *"
   - Options: Load from `email_simulation_alert_captions` table (only active ones)
   - Required: Yes
   - First option: "Select an alert caption..."
   - Help text: "Choose from pre-configured alert captions (manage in Simulation Settings)"

8. **Description** (Textarea)
   - Label: "Transaction Description *"
   - Placeholder: "Enter transaction description or details..."
   - Rows: 4
   - Required: Yes
   - Help text: "This description will appear in the email body"

9. **Template** (Dropdown/Select)
   - Label: "Email Template *"
   - Options: Load from `email_simulation_templates` table (only active ones)
   - Display: Template name
   - Required: Yes
   - First option: "Select a template..."
   - Help text: "Choose email template with custom colors and logo (manage in Simulation Settings)"

### Submit Button
- Text: "Send Simulation Email"
- Icon: `fas fa-paper-plane`
- Style: Match existing "Send Test Email" button
- Loading state: Show spinner when sending

### JavaScript Functionality
1. **Contact Method Toggle**:
   - When "Email" selected: Show email input, hide WhatsApp input
   - When "WhatsApp" selected: Show WhatsApp input, hide email input
   - Clear and toggle required attributes appropriately

2. **Form Validation**:
   - Validate all required fields
   - Validate email format if email method selected
   - Validate phone format if WhatsApp selected
   - Validate amount is positive number
   - Show error messages inline

3. **Form Submission**:
   - Prevent default form submission
   - Collect all form data
   - Send AJAX request to `/api/send-simulation-email.php`
   - Show loading state on button
   - Display success/error message in messageContainer
   - Reset form on success (optional)

### Styling
- Match existing test-card styling
- Use same form-group, form-label, form-input classes
- Responsive design for mobile
- Conditional field visibility with smooth transitions

---

## Part 4: API Endpoint

### File: `api/send-simulation-email.php`

**Purpose**: Process simulation email request and send email

**Request Method**: POST
**Content-Type**: application/json

**Expected JSON Payload**:
```json
{
  "site_name": "SecureBank Online",
  "contact_method": "email",
  "contact_value": "admin@example.com",
  "amount": "1000.00",
  "currency": "USD",
  "transaction_status": "completed",
  "alert_caption": "Funds Received",
  "description": "Test transaction description",
  "template_id": 1
}
```

**Processing Steps**:
1. **Validate Request**:
   - Check admin authentication (requireAdmin())
   - Validate all required fields
   - Validate contact method and value format
   - Validate amount is numeric and positive
   - Validate template_id exists and is active

2. **Load Template Data**:
   - Query `email_simulation_templates` table for template_id
   - Get: primary_color, secondary_color, accent_color, logo_url, logo_alt_text, template_name

3. **Format Amount**:
   - Use existing `formatCurrency()` function from `includes/functions.php`
   - Format: amount + currency

4. **Generate Email HTML**:
   - Use credit alert email template structure from `includes/email-template.php`
   - **Customize colors**: Replace default colors with template colors
   - **Customize logo**: Use template logo_url if provided, else use system logo
   - **Customize site name**: Use provided site_name
   - **Include all transaction details**:
     - Transaction Type: CREDIT (always for simulation)
     - Amount: formatted amount with currency
     - From: "Test Simulation" or site_name
     - Date & Time: Current date/time formatted
     - Transaction Ref: Generate random ref (e.g., "SIM-" + timestamp)
     - Available Balance: Calculate fake balance (amount * random multiplier)
     - Alert Caption: Use selected alert caption
     - Description: Use provided description
     - Transaction Status: Display status badge with appropriate color

5. **Email Subject**: 
   - Format: "[{site_name}] Transaction Alert - Credit - {alert_caption}"
   - Example: "[SecureBank Online] Transaction Alert - Credit - Funds Received"

6. **Send Email**:
   - If contact_method is "email": Use existing `sendEmail()` function
   - If contact_method is "whatsapp": 
     - Log WhatsApp notification (don't actually send WhatsApp - just log for now)
     - Return success message indicating WhatsApp would be sent in production
     - OR: Integrate with WhatsApp API if available

7. **Response**:
   ```json
   {
     "success": true,
     "message": "Simulation email sent successfully to admin@example.com",
     "transaction_ref": "SIM-1234567890",
     "sent_via": "email"
   }
   ```
   OR
   ```json
   {
     "success": false,
     "message": "Error message here",
     "errors": ["Field validation errors if any"]
   }
   ```

8. **Logging**:
   - Log activity: `logActivity($_SESSION['user_id'], 'EMAIL_SIMULATION', "Sent simulation email to {contact_value}")`

---

## Part 5: Controller Updates

### File: `controllers/AdminController.php`

**Add new method**:
```php
public function email($subPage = null) {
    requireAdmin();
    
    // Existing code...
    
    // Add new sub-page handler
    elseif ($subPage === 'simulation-settings') {
        $pageTitle = 'Email Simulation Settings - Admin';
        require_once __DIR__ . '/../config/config.php';
        require_once __DIR__ . '/../includes/functions.php';
        include __DIR__ . '/../includes/head.php';
        include __DIR__ . '/../includes/admin-sidebar.php';
        define('EMAIL_SUBPAGE', true);
        include 'views/admin/email-simulation-settings.php';
        echo '</div></div></div>';
        echo '</body></html>';
        return;
    }
    
    // Existing code continues...
}
```

**Add API handler method** (if needed):
```php
public function emailSimulationSettings() {
    requireAdmin();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Handle CRUD operations for alert captions and templates
        // Return JSON responses for AJAX requests
    }
    
    // Handle GET requests for listing data
}
```

---

## Part 6: Email Template Customization

### Modify Credit Alert Template for Simulation

**File**: Create new method in `includes/email-template.php` OR modify existing `creditAlertEmail()` to accept template customization

**New Method**: `simulationCreditAlertEmail()`

**Parameters**:
- `$siteName` - Custom site name
- `$amount` - Transaction amount
- `$currency` - Currency code
- `$alertCaption` - Alert caption text
- `$description` - Transaction description
- `$transactionStatus` - Status (completed, pending, etc.)
- `$templateData` - Array with: primary_color, secondary_color, accent_color, logo_url, logo_alt_text
- `$transactionRef` - Generated transaction reference
- `$balance` - Fake balance amount

**Template Structure**:
- Use existing email template wrapper (from `render()` method)
- **Header**: Use template logo_url or system logo
- **Colors**: 
  - Primary color for headers, buttons
  - Secondary color for borders, accents
  - Accent color for success indicators, positive amounts
- **Content**:
  - Alert Caption as main heading (large, bold, using accent color)
  - Transaction details table (same structure as existing credit alert)
  - Description section (styled box)
  - Status badge (color-coded based on status)
  - Call-to-action button (using primary color)

**Status Badge Colors**:
- Completed: Green (#10b981 or accent color)
- Pending: Yellow/Orange (#f59e0b)
- Processing: Blue (primary color)
- Failed: Red (#ef4444)

---

## Part 7: Navigation Updates

### File: `views/admin/email.php`

**Add new navigation item** in the sub-nav section:
```html
<a href="<?php echo SITE_URL; ?>/admin/email/simulation-settings" class="sub-nav-item">
    <div class="menu-item-left">
        <div class="menu-item-icon">
            <i class="fas fa-cog"></i>
        </div>
        <div class="menu-item-content">
            <h4>Simulation Settings</h4>
            <p>Manage alert captions and email templates for testing</p>
        </div>
    </div>
    <div class="menu-item-arrow">
        <i class="fas fa-chevron-right"></i>
    </div>
</a>
```

---

## Part 8: Database Helper Functions

### Create: `api/email-simulation-api.php` (Optional - for AJAX operations)

**Endpoints**:
1. **GET `/api/email-simulation-api.php?action=list-captions`**
   - Return JSON list of active alert captions

2. **GET `/api/email-simulation-api.php?action=list-templates`**
   - Return JSON list of active templates

3. **POST `/api/email-simulation-api.php?action=add-caption`**
   - Add new alert caption
   - Validate: caption_text required, max 255 chars

4. **POST `/api/email-simulation-api.php?action=update-caption`**
   - Update existing caption
   - Parameters: id, caption_text, is_active

5. **POST `/api/email-simulation-api.php?action=delete-caption`**
   - Soft delete (set is_active = 0)
   - Parameter: id

6. **POST `/api/email-simulation-api.php?action=add-template`**
   - Add new template
   - Validate: template_name required, unique, colors valid hex

7. **POST `/api/email-simulation-api.php?action=update-template`**
   - Update existing template
   - Parameters: id, all template fields

8. **POST `/api/email-simulation-api.php?action=delete-template`**
   - Soft delete template
   - Parameter: id

**All endpoints**:
- Require admin authentication
- Return JSON responses
- Handle errors gracefully
- Log activities

---

## Part 9: Implementation Checklist

### Phase 1: Database Setup
- [ ] Create `email_simulation_alert_captions` table
- [ ] Create `email_simulation_templates` table
- [ ] Insert default alert captions
- [ ] Insert default template

### Phase 2: Settings Page
- [ ] Create `views/admin/email-simulation-settings.php`
- [ ] Build alert captions management UI
- [ ] Build templates management UI
- [ ] Add CRUD API endpoints
- [ ] Add controller route
- [ ] Add navigation link

### Phase 3: Simulation Form
- [ ] Add new section to `email-test.php`
- [ ] Build form with all fields
- [ ] Add JavaScript for contact method toggle
- [ ] Add form validation
- [ ] Style to match existing design

### Phase 4: Email Generation
- [ ] Create `simulationCreditAlertEmail()` method
- [ ] Implement template color customization
- [ ] Implement logo customization
- [ ] Add status badge rendering
- [ ] Format transaction details

### Phase 5: API Integration
- [ ] Create `api/send-simulation-email.php`
- [ ] Implement request validation
- [ ] Implement email sending logic
- [ ] Add error handling
- [ ] Add activity logging

### Phase 6: Testing
- [ ] Test with email contact method
- [ ] Test with WhatsApp contact method
- [ ] Test all transaction statuses
- [ ] Test with different templates
- [ ] Test with different currencies
- [ ] Test form validation
- [ ] Test error scenarios

---

## Part 10: Additional Features (Optional Enhancements)

1. **Template Preview**: Show live preview of email template in settings page
2. **Bulk Testing**: Send multiple simulation emails at once with different parameters
3. **Email History**: Log all simulation emails sent with parameters
4. **Template Duplication**: Clone existing templates
5. **Import/Export**: Export templates and captions for backup
6. **WhatsApp Integration**: Actually send WhatsApp messages (requires WhatsApp Business API)
7. **Scheduled Testing**: Schedule simulation emails to be sent at specific times
8. **Email Analytics**: Track open rates, click rates for simulation emails

---

## Technical Notes

1. **Security**:
   - All endpoints require admin authentication
   - Sanitize all user inputs
   - Validate file uploads for logos
   - Use prepared statements for database queries

2. **Performance**:
   - Cache template and caption lists
   - Optimize database queries with indexes
   - Use AJAX for settings page operations

3. **Error Handling**:
   - Graceful error messages for users
   - Log errors to error log
   - Return appropriate HTTP status codes

4. **Code Reuse**:
   - Reuse existing email template structure
   - Reuse existing form styling
   - Reuse existing validation functions
   - Reuse existing currency formatting

5. **Compatibility**:
   - Ensure works with existing SMTP configuration
   - Ensure works with existing email template system
   - Maintain backward compatibility

---

## File Structure Summary

**New Files to Create**:
1. `views/admin/email-simulation-settings.php`
2. `api/send-simulation-email.php`
3. `api/email-simulation-api.php` (optional)

**Files to Modify**:
1. `views/admin/email-test.php` (add simulation section)
2. `views/admin/email.php` (add navigation link)
3. `controllers/AdminController.php` (add routes)
4. `includes/email-template.php` (add simulation method)

**Database**:
1. Create `email_simulation_alert_captions` table
2. Create `email_simulation_templates` table

---

## Success Criteria

✅ Admin can add/edit/delete alert captions
✅ Admin can add/edit/delete email templates with custom colors and logos
✅ Admin can send simulation emails with all customizable parameters
✅ Simulation emails use selected template colors and logo
✅ Simulation emails display all transaction details correctly
✅ Form validation works for all fields
✅ Contact method toggle works correctly
✅ Email sending works via existing SMTP configuration
✅ All operations are logged for audit trail
✅ UI matches existing admin design patterns
✅ Mobile responsive design

---

**End of Detailed Prompt**


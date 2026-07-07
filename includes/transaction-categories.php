<?php
/**
 * Shared transaction category helpers (structural category + expense_category).
 */

function getValidStructuralCategories(): array
{
    return ['transfer', 'payment', 'deposit', 'withdrawal', 'fee', 'interest', 'loan', 'card', 'other'];
}

function getValidExpenseCategoryDbValues(): array
{
    return [
        'shopping', 'food', 'transport', 'bills', 'entertainment', 'healthcare', 'travel',
        'education', 'salary', 'investment', 'rent', 'insurance', 'gift', 'personal', 'other',
        'bonus', 'refund', 'utility',
    ];
}

function getExpenseCategoryOptions(): array
{
    return [
        ['value' => 'salary', 'label' => 'Salary', 'icon' => '💰'],
        ['value' => 'bonus', 'label' => 'Bonus', 'icon' => '🎁'],
        ['value' => 'transfer', 'label' => 'Transfer', 'icon' => '🔄'],
        ['value' => 'deposit', 'label' => 'Deposit', 'icon' => '📥'],
        ['value' => 'withdrawal', 'label' => 'Withdrawal', 'icon' => '📤'],
        ['value' => 'payment', 'label' => 'Payment', 'icon' => '💳'],
        ['value' => 'refund', 'label' => 'Refund', 'icon' => '↩️'],
        ['value' => 'fee', 'label' => 'Fee', 'icon' => '💸'],
        ['value' => 'interest', 'label' => 'Interest', 'icon' => '📈'],
        ['value' => 'investment', 'label' => 'Investment', 'icon' => '💼'],
        ['value' => 'loan', 'label' => 'Loan', 'icon' => '🏦'],
        ['value' => 'insurance', 'label' => 'Insurance', 'icon' => '🛡️'],
        ['value' => 'utility', 'label' => 'Utility Bill', 'icon' => '💡'],
        ['value' => 'shopping', 'label' => 'Shopping', 'icon' => '🛍️'],
        ['value' => 'entertainment', 'label' => 'Entertainment', 'icon' => '🎬'],
        ['value' => 'food', 'label' => 'Food & Dining', 'icon' => '🍽️'],
        ['value' => 'transportation', 'label' => 'Transportation', 'icon' => '🚗'],
        ['value' => 'healthcare', 'label' => 'Healthcare', 'icon' => '🏥'],
        ['value' => 'education', 'label' => 'Education', 'icon' => '📚'],
        ['value' => 'travel', 'label' => 'Travel', 'icon' => '✈️'],
        ['value' => 'rent', 'label' => 'Rent', 'icon' => '🏠'],
        ['value' => 'other', 'label' => 'Other', 'icon' => '📝'],
    ];
}

function normalizeExpenseCategory(?string $input): ?string
{
    if ($input === null || $input === '') {
        return null;
    }

    $key = strtolower(trim($input));
    $map = [
        'transportation' => 'transport',
        'utility' => 'utility',
        'utilities' => 'utility',
        'bills' => 'bills',
        'transfer' => 'other',
        'deposit' => 'other',
        'withdrawal' => 'other',
        'payment' => 'other',
        'fee' => 'other',
        'interest' => 'other',
        'loan' => 'other',
    ];

    $normalized = $map[$key] ?? $key;
    $valid = getValidExpenseCategoryDbValues();

    if (in_array($normalized, $valid, true)) {
        return $normalized;
    }

    return 'other';
}

function formatExpenseCategoryLabel(?string $value): string
{
    if ($value === null || $value === '') {
        return 'General';
    }

    foreach (getExpenseCategoryOptions() as $opt) {
        if ($opt['value'] === $value) {
            return $opt['label'];
        }
    }

    return ucfirst(str_replace('_', ' ', $value));
}

function formatStructuralCategoryLabel(?string $value): string
{
    if ($value === null || $value === '') {
        return 'Other';
    }
    return ucfirst($value);
}

function getStructuralCategoryForEventType(string $eventType): string
{
    $map = [
        'domestic_transfer' => 'transfer',
        'international_transfer' => 'transfer',
        'internal_transfer' => 'transfer',
        'card_payment' => 'card',
        'bill_payment' => 'payment',
        'subscription' => 'payment',
        'salary_credit' => 'deposit',
        'incoming_domestic_transfer' => 'deposit',
        'incoming_international_transfer' => 'deposit',
        'deposit_credit' => 'deposit',
        'investment_credit' => 'deposit',
        'investment_debit' => 'withdrawal',
        'loan_payment' => 'loan',
        'atm_withdrawal' => 'withdrawal',
        'fee' => 'fee',
        'adjustment_credit' => 'deposit',
        'adjustment_debit' => 'withdrawal',
    ];
    return $map[$eventType] ?? 'other';
}

function categoryNeedsTransferDetails(?string $category, ?array $metadata = null): bool
{
    $metadata = $metadata ?? [];
    if ($category === 'transfer') {
        return true;
    }
    if ($category === 'deposit' && !empty($metadata['transfer_scope'])) {
        return true;
    }
    if (!empty($metadata['transfer_scope']) && in_array($metadata['transfer_scope'], ['domestic', 'international', 'internal'], true)) {
        return true;
    }
    return false;
}

function renderExpenseCategorySelectOptions(?string $selected = null): string
{
    $html = '<option value="">Select Category</option>';
    foreach (getExpenseCategoryOptions() as $opt) {
        $sel = ($selected === $opt['value']) ? ' selected' : '';
        $html .= sprintf(
            '<option value="%s"%s>%s %s</option>',
            htmlspecialchars($opt['value']),
            $sel,
            $opt['icon'],
            htmlspecialchars($opt['label'])
        );
    }
    return $html;
}

<?php
if (!function_exists('isRestrictedStatus') || !function_exists('restrictedAccountMessage')) {
    return;
}

$status = $_SESSION['restricted_status'] ?? '';
if (!isRestrictedStatus($status)) {
    return;
}
?>
<div style="
    margin: 0 0 16px 0;
    padding: 14px 16px;
    border-radius: 12px;
    background: rgba(239, 68, 68, 0.10);
    border: 1px solid rgba(239, 68, 68, 0.25);
    color: #7f1d1d;
    display: flex;
    gap: 10px;
    align-items: flex-start;
">
    <div style="font-size: 18px; line-height: 1;">⚠️</div>
    <div>
        <div style="font-weight: 700; margin-bottom: 2px;">Account Restricted</div>
        <div style="font-weight: 500;"><?php echo htmlspecialchars(restrictedAccountMessage()); ?></div>
    </div>
</div>


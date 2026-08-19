<?php

$type = $__props['type'] ?? 'info';
$slot = $__slot ?? '';
$text = $slot !== ''
    ? $slot
    : ($__props['message'] ?? '');

?>

<div class="component_alert component_alert_<?= htmlspecialchars($type) ?>">
    <?= $text ?>
</div>
<?php

$type = $__props['type'] ?? 'primary';
$slot = $__slot ?? '';
$text = $slot !== ''
    ? $slot
    : ($__props['text'] ?? 'Button');

?>

<button class="btn btn-<?= htmlspecialchars($type) ?>">
    <?= htmlspecialchars($text) ?>
</button>
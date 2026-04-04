<?php if ($msg = flash("success")): ?>
<div class="alert alert-success"><?= $msg ?></div>
<?php endif; ?> <?php if ($msg = flash("error")): ?>
<div class="alert alert-danger"><?= $msg ?></div>
<?php endif; ?>

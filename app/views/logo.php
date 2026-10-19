<?php if (setting('logo_image') !== ''): ?>
<img src="<?= e(media(setting('logo_image'))) ?>" alt="<?= e(setting('site_name')) ?>" class="logo-img">
<?php else: ?>
<img src="<?= url('assets/img/logo-mark.svg') ?>" alt="" class="brand-mark-img" width="38" height="38">
<span><?= e(setting('logo_text_1')) ?><span class="text-gradient"><?= e(setting('logo_text_2')) ?></span></span>
<?php endif; ?>

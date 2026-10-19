<?php
/** Contact details + enquiry form. @var bool $alt  @var string $preselect (service title to pre-select) */
$alt       = $alt ?? false;
$preselect = $preselect ?? '';
$serviceOptions = lines(setting('contact_services'));
$budgetOptions  = lines(setting('contact_budgets'));
?>
  <section class="section<?= $alt ? ' section-alt' : '' ?>" id="enquiry">
    <div class="container">
      <div class="row g-5">
        <div class="col-lg-5" data-aos="fade-right">
          <span class="eyebrow"><?= e(setting('contact_eyebrow')) ?></span>
          <h2 class="display-6 fw-bold mt-2"><?= hl(setting('contact_title')) ?></h2>
          <p class="text-muted-2 mt-3"><?= e(setting('contact_text')) ?></p>
          <div class="contact-info mt-4">
<?php if (setting('contact_email') !== ''): ?>
            <a href="mailto:<?= e(setting('contact_email')) ?>"><i class="bi bi-envelope"></i><div><small>Email us</small><?= e(setting('contact_email')) ?></div></a>
<?php endif; ?>
<?php if (setting('whatsapp_number') !== ''): ?>
            <a href="<?= e(wa_link(setting('whatsapp_message'))) ?>" target="_blank" rel="noopener"><i class="bi bi-whatsapp"></i><div><small>WhatsApp</small><?= e(setting('whatsapp_display', setting('whatsapp_number'))) ?></div></a>
<?php endif; ?>
<?php if (setting('phone') !== ''): ?>
            <a href="tel:<?= e(preg_replace('/[^\d+]/', '', setting('phone'))) ?>"><i class="bi bi-telephone"></i><div><small>Call us</small><?= e(setting('phone')) ?></div></a>
<?php endif; ?>
<?php if (setting('address') !== ''): ?>
            <div class="contact-item"><i class="bi bi-geo-alt"></i><div><small>Visit us</small><?= nl2br(e(setting('address'))) ?></div></div>
<?php endif; ?>
          </div>
        </div>
        <div class="col-lg-7" data-aos="fade-left">
          <form id="contactForm" class="contact-form" action="<?= url('api/contact.php') ?>" method="post" novalidate>
            <input type="text" name="website" class="hp-field" tabindex="-1" autocomplete="off" aria-hidden="true">
            <div class="row g-3">
              <div class="col-md-6">
                <label for="name" class="form-label">Your Name</label>
                <input type="text" class="form-control" id="name" name="name" maxlength="100" required>
                <div class="invalid-feedback">Please enter your name.</div>
              </div>
              <div class="col-md-6">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" maxlength="190" required>
                <div class="invalid-feedback">Please enter a valid email.</div>
              </div>
              <div class="col-md-6">
                <label for="phone" class="form-label">Phone / WhatsApp <span class="text-muted-2 small">(optional)</span></label>
                <input type="tel" class="form-control" id="phone" name="phone" maxlength="50">
              </div>
<?php if ($serviceOptions): ?>
              <div class="col-md-6">
                <label for="service" class="form-label">Service</label>
                <select class="form-select" id="service" name="service">
<?php foreach ($serviceOptions as $o): ?>                  <option<?= $preselect !== '' && stripos($o, $preselect) !== false ? ' selected' : '' ?>><?= e($o) ?></option>
<?php endforeach; ?>
                </select>
              </div>
<?php endif; ?>
<?php if ($budgetOptions): ?>
              <div class="col-md-6">
                <label for="budget" class="form-label">Budget</label>
                <select class="form-select" id="budget" name="budget">
<?php foreach ($budgetOptions as $o): ?>                  <option><?= e($o) ?></option>
<?php endforeach; ?>
                </select>
              </div>
<?php endif; ?>
              <div class="col-12">
                <label for="message" class="form-label">Project Details</label>
                <textarea class="form-control" id="message" name="message" rows="5" maxlength="5000" required></textarea>
                <div class="invalid-feedback">Please tell us a bit about your project.</div>
              </div>
              <div class="col-12">
                <button type="submit" class="btn btn-gradient btn-lg px-5">Send Message <i class="bi bi-send ms-1"></i></button>
                <div class="form-note mt-3" id="formStatus" role="status"></div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>

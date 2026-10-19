<?php
declare(strict_types=1);

/** Insert default settings that don't exist yet, and starter content into empty tables. Safe to re-run. */
function seed_database(): void
{
    foreach (require APP . '/settings_schema.php' as $group) {
        foreach ($group['fields'] as $key => $field) {
            if (!(int) val('SELECT COUNT(*) FROM settings WHERE setting_key = ?', [$key])) {
                q('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)', [$key, $field['default'] ?? '']);
            }
        }
    }

    seed_table('technologies', ['icon', 'name'], [
        ['bi-filetype-html', 'HTML5'], ['bi-bootstrap-fill', 'Bootstrap'], ['bi-filetype-jsx', 'React'],
        ['bi-filetype-php', 'Laravel'], ['bi-wordpress', 'WordPress'], ['bi-bag-fill', 'Shopify'],
        ['bi-filetype-js', 'Node.js'], ['bi-phone-fill', 'Flutter'], ['bi-database-fill', 'MySQL'], ['bi-cloud-fill', 'AWS'],
    ]);

    seed_table('services', ['icon', 'title', 'description', 'link_text', 'link_url'], [
        ['bi-window-stack', 'Website Design & Development', 'Custom, responsive, lightning-fast websites built to impress visitors and turn them into customers.', 'Learn more', '#contact'],
        ['bi-cart3', 'E-commerce Stores', 'Shopify, WooCommerce and custom stores with secure payments, smooth checkout and inventory tools.', 'Learn more', '#contact'],
        ['bi-code-square', 'Custom Web Applications', 'Dashboards, portals, SaaS products and CRMs built with Laravel, React and Node.js.', 'Learn more', '#contact'],
        ['bi-phone', 'Mobile App Development', 'Cross-platform iOS and Android apps with beautiful interfaces and reliable performance.', 'Learn more', '#contact'],
        ['bi-palette2', 'UI / UX Design', 'User research, wireframes and pixel-perfect designs that make your product a joy to use.', 'Learn more', '#contact'],
        ['bi-megaphone', 'SEO & Digital Marketing', 'Rank higher on Google, run profitable ad campaigns and grow your audience on social media.', 'Learn more', '#contact'],
    ]);

    seed_table('stats', ['value', 'suffix', 'label'], [
        [150, '+', 'Projects Delivered'], [80, '+', 'Happy Clients'], [5, '+', 'Years Experience'], [12, '', 'Countries Served'],
    ]);

    seed_table('projects', ['title', 'category', 'description', 'icon', 'color'], [
        ['Fashion Store', 'E-commerce', 'Shopify store with custom theme and 3x faster checkout.', 'bi-bag-heart', 'g-1'],
        ['SaaS Analytics Dashboard', 'Web App', 'Real-time reporting platform built with Laravel & React.', 'bi-speedometer2', 'g-2'],
        ['Real Estate Agency', 'Website', 'Property listings with search, maps and lead capture.', 'bi-building', 'g-3'],
        ['Fitness Tracker App', 'Mobile App', 'Cross-platform workout and nutrition tracking app.', 'bi-heart-pulse', 'g-4'],
        ['Restaurant & Online Ordering', 'Website', 'Menu, table booking and online ordering in one site.', 'bi-cup-hot', 'g-5'],
        ['E-learning Platform', 'Web App', 'Courses, quizzes and certificates for 10k+ students.', 'bi-mortarboard', 'g-6'],
    ]);

    seed_table('process_steps', ['icon', 'title', 'description'], [
        ['bi-chat-dots', 'Discover', 'We learn your goals, audience and competitors.'],
        ['bi-vector-pen', 'Design', 'Wireframes and visual designs you approve before we code.'],
        ['bi-cpu', 'Develop', 'Clean, tested code with weekly progress updates.'],
        ['bi-rocket', 'Launch & Grow', 'Go live, track results and keep improving.'],
    ]);

    // Sample testimonials: replace with real ones from the admin panel before marketing.
    seed_table('testimonials', ['name', 'role', 'quote', 'rating'], [
        ['Client Name', 'CEO, Company', 'Our new website doubled our online enquiries within two months. The team was responsive, creative and delivered ahead of schedule.', 5],
        ['Client Name', 'Founder, Startup', 'They turned a rough idea into a polished web app our customers love. Clear communication throughout the whole project.', 5],
        ['Client Name', 'Owner, Online Store', 'Professional, fast and genuinely invested in our success. Our online store sales are up and the site is so easy to manage.', 5],
    ]);

    seed_table('pricing_plans', ['name', 'subtitle', 'price', 'period', 'features', 'button_text', 'button_url', 'is_featured', 'badge_text'], [
        ['Starter', 'For small businesses & personal brands', '$499', '/project', "Up to 5 pages\nResponsive design\nContact form & basic SEO\n1 month support", 'Get Started', '#contact', 0, ''],
        ['Business', 'For growing companies', '$1,499', '/project', "Up to 15 pages + CMS\nCustom UI/UX design\nAdvanced SEO & analytics\nBlog / e-commerce ready\n3 months support", 'Get Started', '#contact', 1, 'Most Popular'],
        ['Enterprise', 'Custom apps & large platforms', 'Custom', '', "Web / mobile applications\nAPI & third-party integrations\nDedicated project manager\nPriority support & SLA", 'Contact Us', '#contact', 0, ''],
    ]);

    seed_table('faqs', ['question', 'answer'], [
        ['How long does it take to build a website?', 'Most business websites take 2–4 weeks. Larger web apps and e-commerce stores typically take 6–12 weeks depending on features.'],
        ['Will my website work on mobile phones?', 'Yes. Every site we build is fully responsive and tested on phones, tablets and desktops.'],
        ['Can I update the content myself?', "Absolutely. We can build on an easy-to-use CMS like WordPress or a custom admin panel, and we'll show you how to use it."],
        ['Do you offer hosting and maintenance?', 'Yes — we offer hosting setup, security updates, backups and ongoing maintenance plans so you never have to worry.'],
        ['What do you need from me to get started?', "Just a quick call to understand your goals. We'll send a proposal with scope, timeline and price — no obligation."],
    ]);

    $now = now();
    seed_table('pages', ['title', 'slug', 'content', 'meta_description', 'show_in_footer', 'created_at', 'updated_at'], [
        ['Privacy Policy', 'privacy-policy',
            '<p>This page explains how we collect and use information submitted through this website.</p>'
            . '<h2>Information we collect</h2><p>When you use our contact form or subscribe to our newsletter, we collect the details you provide, such as your name, email address and message.</p>'
            . '<h2>How we use it</h2><p>We use this information only to reply to your enquiry and, if you subscribed, to send you our newsletter. We do not sell your data.</p>'
            . '<h2>Contact</h2><p>For any privacy question, email us using the address on our contact section.</p>',
            'How WebExpertees collects and uses your information.', 1, $now, $now],
        ['Terms of Service', 'terms-of-service',
            '<p>By using this website you agree to these terms. Edit this page from the admin panel to add your full terms.</p>',
            'Terms of service for WebExpertees.', 1, $now, $now],
    ]);
}

function seed_table(string $table, array $cols, array $data): void
{
    if ((int) val("SELECT COUNT(*) FROM $table") > 0) {
        return;
    }
    foreach ($data as $i => $values) {
        insert_row($table, array_combine($cols, $values) + ['sort_order' => $i + 1]);
    }
}

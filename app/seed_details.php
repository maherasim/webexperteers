<?php
declare(strict_types=1);

/**
 * Fill detail-page fields (slug, content, features...) where they are empty, and upgrade
 * old one-page "#anchor" links to the multi-page URLs. Never overwrites admin edits.
 */
function seed_details(): void
{
    // Slugs for every row that doesn't have one yet
    foreach (['services', 'projects'] as $table) {
        foreach (rows("SELECT id, title FROM $table WHERE slug IS NULL OR slug = ''") as $r) {
            $slug = slugify($r['title']);
            $n = 1;
            while ((int) val("SELECT COUNT(*) FROM $table WHERE slug = ? AND id <> ?", [$slug, $r['id']])) {
                $slug = slugify($r['title']) . '-' . (++$n);
            }
            q("UPDATE $table SET slug = ? WHERE id = ?", [$slug, $r['id']]);
        }
    }

    foreach (service_details() as $title => [$features, $content]) {
        q("UPDATE services SET features = ? WHERE title = ? AND (features IS NULL OR features = '')", [$features, $title]);
        q("UPDATE services SET content = ? WHERE title = ? AND (content IS NULL OR content = '')", [$content, $title]);
    }
    foreach (project_details() as $title => [$tech, $content]) {
        q("UPDATE projects SET tech_stack = ? WHERE title = ? AND (tech_stack IS NULL OR tech_stack = '')", [$tech, $title]);
        q("UPDATE projects SET content = ? WHERE title = ? AND (content IS NULL OR content = '')", [$content, $title]);
    }

    // One-page anchors -> page URLs (only exact old defaults are changed)
    $links = ['hero_btn1_link' => ['#contact', 'contact'], 'hero_btn2_link' => ['#portfolio', 'portfolio']];
    foreach ($links as $key => [$old, $new]) {
        q('UPDATE settings SET setting_value = ? WHERE setting_key = ? AND setting_value = ?', [$new, $key, $old]);
    }
    q("UPDATE pricing_plans SET button_url = 'contact' WHERE button_url = '#contact'");
    q("UPDATE services SET link_text = 'Learn more' WHERE link_text IN ('Get a quote', '')");
    q("UPDATE services SET link_url = '' WHERE link_url = '#contact'");
}

function service_details(): array
{
    $p = fn(string ...$paras) => '<p>' . implode('</p><p>', $paras) . '</p>';
    return [
        'Website Design & Development' => [
            "Custom design tailored to your brand\nMobile-first, fully responsive layouts\nFast loading and Core Web Vitals optimised\nSEO-ready structure, meta tags and sitemap\nEasy admin panel to update your content\nContact forms, WhatsApp and lead capture\nSSL, security hardening and backups\nTraining and post-launch support",
            $p('Your website is often the first impression a customer has of your business. We design and build professional websites that look great, load fast and clearly explain what you offer — so visitors trust you and get in touch.',
               'Every site is designed from scratch around your brand and goals, then built with clean, modern code. You get an easy admin panel to update text, images and pages yourself, and we make sure everything works perfectly on phones, tablets and desktops.')
            . '<h2>Who it is for</h2>' . $p('Small and growing businesses, startups, professionals and agencies who need a credible online presence that brings in enquiries — whether it is a brand-new site or a redesign of an outdated one.'),
        ],
        'E-commerce Development' => [
            "Shopify, WooCommerce or custom store\nProduct catalogue with categories and filters\nSecure payment gateway integration\nFast, simple checkout flow\nOrder, stock and customer management\nDiscount codes and promotions\nShipping and tax configuration\nSales analytics and reporting",
            $p('We build online stores that make it easy for customers to find products, trust your brand and complete their purchase. From product pages to checkout, every step is designed to reduce friction and increase sales.',
               'Depending on your needs we build on Shopify, WooCommerce or a fully custom platform, and connect the payment gateways, shipping options and tools you need to run your store day to day.')
            . '<h2>Who it is for</h2>' . $p('Retailers moving online, brands launching their first store, and existing shops that need a faster, better-converting website.'),
        ],
        'Custom Web Applications' => [
            "Requirements analysis and planning\nCustom dashboards and admin panels\nUser accounts, roles and permissions\nBooking, CRM and workflow systems\nREST APIs and third-party integrations\nSecure, scalable architecture\nAutomated testing and code reviews\nDeployment, hosting and maintenance",
            $p('When off-the-shelf software does not fit how your business works, we build web applications that do. Dashboards, booking systems, client portals, CRMs and SaaS products — designed around your processes and built to grow with you.',
               'We work with proven technologies such as Laravel, PHP, React and Node.js, and focus on security, performance and clean code that is easy to extend later.')
            . '<h2>How we work</h2>' . $p('We start by mapping your requirements, then deliver in short milestones with regular demos, so you see progress every week and can give feedback early.'),
        ],
        'Mobile App Development' => [
            "iOS and Android from one Flutter codebase\nModern, intuitive app design\nUser accounts and secure login\nPush notifications\nIn-app payments and subscriptions\nConnection to your website or backend API\nApp Store and Google Play submission\nUpdates and ongoing support",
            $p('We build cross-platform mobile apps with Flutter, so your app runs smoothly on both iPhone and Android from a single codebase — saving time and budget without compromising on quality.',
               'From the first wireframe to publishing on the App Store and Google Play, we handle design, development, testing and launch, and connect your app securely to your existing website or systems.'),
        ],
        'UI / UX Design' => [
            "User research and competitor review\nInformation architecture and user flows\nWireframes for every key screen\nHigh-fidelity designs in Figma\nClickable interactive prototypes\nDesign system: colours, type and components\nMobile and desktop layouts\nDeveloper-ready handoff",
            $p('Good design makes your product easy to understand and enjoyable to use. We combine research, clear structure and polished visuals to create interfaces that guide users toward action.',
               'You see and approve every screen as a clickable prototype in Figma before development begins, which keeps projects on budget and avoids costly changes later.'),
        ],
        'WordPress Development' => [
            "Custom WordPress theme development\nPlugin setup and custom plugin development\nEasy-to-use block editor for your team\nWooCommerce stores\nSpeed and Core Web Vitals optimisation\nSecurity hardening and malware protection\nMigration from other platforms\nUpdates, backups and maintenance",
            $p('WordPress powers a large share of the web because it is flexible and easy to manage. We build custom WordPress websites that are fast, secure and simple for your team to update — without bloated themes or unnecessary plugins.',
               'Need an existing WordPress site fixed, sped up or redesigned? We can audit it, clean it up and get it performing properly again.'),
        ],
        'SEO Optimization' => [
            "Technical SEO audit\nKeyword research for your market\nOn-page optimisation of titles and content\nSite speed and Core Web Vitals\nGoogle Search Console and Analytics setup\nLocal SEO and Google Business Profile\nSchema markup and sitemaps\nMonthly progress reports",
            $p('Search engine optimisation helps customers find your business when they search on Google. We fix the technical issues holding your site back, optimise your pages for the right keywords and build a foundation for long-term organic growth.',
               'You get clear reporting on rankings, traffic and enquiries, so you always know what is working and what we are doing next.'),
        ],
        'Digital Marketing' => [
            "Marketing strategy and audience research\nGoogle Search and Display ads\nMeta (Facebook & Instagram) ads\nSocial media content and scheduling\nLanding pages built to convert\nConversion and pixel tracking setup\nA/B testing of ads and pages\nClear monthly reporting",
            $p('We plan and run digital marketing campaigns that bring targeted visitors to your website and turn them into leads and customers. Every campaign is set up with proper tracking, so budget goes to what actually works.',
               'Whether you want more enquiries, more sales or more awareness, we build campaigns around your goals and keep improving them month after month.'),
        ],
        'Maintenance & Support' => [
            "Hosting and domain setup\nProfessional email configuration\nDaily or weekly backups\nSecurity monitoring and updates\nUptime monitoring\nSpeed checks and optimisation\nSmall content and design changes\nPriority support when you need it",
            $p('A website needs regular care to stay fast, secure and up to date. Our maintenance plans take care of the technical side — updates, backups, security and monitoring — so you can focus on running your business.',
               'If something breaks or you need a quick change, you have a team ready to help, without having to search for a new developer every time.'),
        ],
    ];
}

function project_details(): array
{
    $note = '<p><em>This is a sample design concept created to show the kind of work we deliver.</em></p>';
    return [
        'Fashion Store' => [
            "Shopify\nLiquid\nJavaScript\nFigma",
            $note . '<h2>The concept</h2><p>A modern online fashion store focused on clean product presentation and a quick path to checkout.</p>'
            . '<h2>Key features</h2><ul><li>Seasonal sale banner and featured collections</li><li>Product cards with ratings, wishlist and quick add</li><li>Category navigation and filters</li><li>Mobile-first layout and fast checkout</li></ul>',
        ],
        'SaaS Analytics Dashboard' => [
            "Laravel\nReact\nMySQL\nChart.js\nREST API",
            $note . '<h2>The concept</h2><p>A reporting dashboard for a subscription business, giving teams a live view of revenue, users and conversions.</p>'
            . '<h2>Key features</h2><ul><li>KPI cards for revenue, active users, conversion and orders</li><li>Monthly revenue growth chart</li><li>Recent sales feed and goal tracking</li><li>Role-based access and data export</li></ul>',
        ],
        'Real Estate Agency' => [
            "PHP\nBootstrap\nMySQL\nGoogle Maps API",
            $note . '<h2>The concept</h2><p>A property website that helps buyers and renters search listings quickly and contact agents directly.</p>'
            . '<h2>Key features</h2><ul><li>Search by location, property type and price range</li><li>Featured listing cards with beds, baths and area</li><li>Sale, rent and new-listing badges</li><li>Lead capture for agents</li></ul>',
        ],
        'Fitness Tracker App' => [
            "Flutter\nFirebase\niOS\nAndroid",
            $note . '<h2>The concept</h2><p>A cross-platform fitness app that brings activity, workouts and nutrition together in one simple daily view.</p>'
            . '<h2>Key features</h2><ul><li>Daily step ring with calories, heart rate and sleep</li><li>Weekly activity chart and workout history</li><li>Meal logging with calorie targets</li><li>Workout plans and reminders</li></ul>',
        ],
        'Restaurant & Online Ordering' => [
            "WordPress\nWooCommerce\nPHP\nPayment gateway",
            $note . '<h2>The concept</h2><p>A restaurant website that combines the menu, online ordering and table reservations.</p>'
            . '<h2>Key features</h2><ul><li>Menu by category with dish photos and prices</li><li>Cart and online ordering for delivery or pickup</li><li>Table reservation form</li><li>Delivery time and offers highlighted</li></ul>',
        ],
        'E-learning Platform' => [
            "Laravel\nVue.js\nMySQL\nVideo streaming",
            $note . '<h2>The concept</h2><p>An online learning platform where students follow courses, track progress and earn certificates.</p>'
            . '<h2>Key features</h2><ul><li>Continue-learning panel with lesson progress</li><li>Course catalogue by category with ratings</li><li>Progress tracking and certificates</li><li>Premium membership upsell</li></ul>',
        ],
    ];
}

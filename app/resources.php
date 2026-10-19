<?php
/*
 * Admin-managed content types. crud.php builds list/add/edit/delete screens from these definitions.
 * Field types: text, textarea, lines, html, number, checkbox, image, icon, select, slug, email, url.
 */
$active = ['label' => 'Visible on site', 'type' => 'checkbox', 'default' => 1];
$order  = ['label' => 'Display order', 'type' => 'number', 'default' => 0, 'help' => 'Lower numbers show first.'];
$icon   = ['label' => 'Icon', 'type' => 'icon', 'default' => 'bi-star'];

return [
    'services' => [
        'title' => 'Services', 'singular' => 'Service', 'icon' => 'bi-grid-1x2', 'table' => 'services',
        'list' => ['image', 'icon', 'title', 'sort_order', 'is_active'],
        'unique' => ['slug'],
        'view_url' => 'services/{slug}',
        'fields' => [
            'icon'        => $icon,
            'title'       => ['label' => 'Title', 'required' => true],
            'slug'        => ['label' => 'URL slug', 'type' => 'slug', 'from' => 'title', 'help' => 'Page address: /services/your-slug. Leave empty to generate from the title.'],
            'description' => ['label' => 'Short description (cards & Google)', 'type' => 'textarea'],
            'features'    => ['label' => "What's included (one per line)", 'type' => 'lines'],
            'content'     => ['label' => 'Full page content', 'type' => 'html'],
            'link_text'   => ['label' => 'Card link text', 'default' => 'Learn more'],
            'image'       => ['label' => 'Image (optional)', 'type' => 'image', 'help' => 'Shown at the top of the service card. Recommended 1200×750.'],
            'sort_order'  => $order,
            'is_active'   => $active,
        ],
    ],
    'projects' => [
        'title' => 'Portfolio', 'singular' => 'Project', 'icon' => 'bi-briefcase', 'table' => 'projects',
        'list' => ['image', 'title', 'category', 'sort_order', 'is_active'],
        'unique' => ['slug'],
        'view_url' => 'portfolio/{slug}',
        'fields' => [
            'title'       => ['label' => 'Project title', 'required' => true],
            'slug'        => ['label' => 'URL slug', 'type' => 'slug', 'from' => 'title', 'help' => 'Page address: /portfolio/your-slug. Leave empty to generate from the title.'],
            'client'      => ['label' => 'Client name', 'help' => 'Leave empty for sample/concept work — the page then shows "Sample design".'],
            'category'    => ['label' => 'Category', 'help' => 'e.g. Website, E-commerce, Web App, Mobile App. Filter buttons are built from these.'],
            'description' => ['label' => 'Short description', 'type' => 'textarea'],
            'image'       => ['label' => 'Screenshot', 'type' => 'image', 'help' => 'Recommended 1200×800. If empty, the icon and colour are shown instead.'],
            'icon'        => $icon,
            'color'       => ['label' => 'Background colour (when no image)', 'type' => 'select', 'default' => 'g-2', 'options' => [
                'g-1' => 'Pink / Red', 'g-2' => 'Purple', 'g-3' => 'Green', 'g-4' => 'Orange', 'g-5' => 'Violet', 'g-6' => 'Blue',
            ]],
            'url'         => ['label' => 'Live project URL (optional)', 'type' => 'url'],
            'tech_stack'  => ['label' => 'Technologies used (one per line)', 'type' => 'lines'],
            'content'     => ['label' => 'Full case study', 'type' => 'html'],
            'sort_order'  => $order,
            'is_active'   => $active,
        ],
    ],
    'testimonials' => [
        'title' => 'Testimonials', 'singular' => 'Testimonial', 'icon' => 'bi-chat-quote', 'table' => 'testimonials',
        'list' => ['avatar', 'name', 'role', 'sort_order', 'is_active'],
        'fields' => [
            'name'       => ['label' => 'Client name', 'required' => true],
            'role'       => ['label' => 'Role & company', 'help' => 'e.g. CEO, Acme Inc.'],
            'quote'      => ['label' => 'Testimonial', 'type' => 'textarea', 'required' => true],
            'avatar'     => ['label' => 'Photo (optional)', 'type' => 'image'],
            'rating'     => ['label' => 'Star rating', 'type' => 'select', 'default' => '5', 'options' => ['5' => '5 stars', '4' => '4 stars', '3' => '3 stars', '0' => 'Hide stars']],
            'sort_order' => $order,
            'is_active'  => $active,
        ],
    ],
    'pricing_plans' => [
        'title' => 'Pricing Plans', 'singular' => 'Plan', 'icon' => 'bi-tags', 'table' => 'pricing_plans',
        'list' => ['name', 'price', 'is_featured', 'sort_order', 'is_active'],
        'fields' => [
            'name'        => ['label' => 'Plan name', 'required' => true],
            'subtitle'    => ['label' => 'Subtitle'],
            'price'       => ['label' => 'Price', 'help' => 'e.g. $499 or Custom'],
            'period'      => ['label' => 'Price suffix', 'help' => 'e.g. /project or /month'],
            'features'    => ['label' => 'Features (one per line)', 'type' => 'lines'],
            'button_text' => ['label' => 'Button text', 'default' => 'Get Started'],
            'button_url'  => ['label' => 'Button link', 'default' => 'contact', 'help' => 'A page like contact, or a full URL.'],
            'is_featured' => ['label' => 'Highlight this plan', 'type' => 'checkbox', 'default' => 0],
            'badge_text'  => ['label' => 'Badge text (highlighted plan)', 'default' => 'Most Popular'],
            'sort_order'  => $order,
            'is_active'   => $active,
        ],
    ],
    'faqs' => [
        'title' => 'FAQs', 'singular' => 'FAQ', 'icon' => 'bi-question-circle', 'table' => 'faqs',
        'list' => ['question', 'sort_order', 'is_active'],
        'fields' => [
            'question'   => ['label' => 'Question', 'required' => true],
            'answer'     => ['label' => 'Answer', 'type' => 'textarea', 'required' => true],
            'sort_order' => $order,
            'is_active'  => $active,
        ],
    ],
    'process_steps' => [
        'title' => 'Process Steps', 'singular' => 'Step', 'icon' => 'bi-diagram-3', 'table' => 'process_steps',
        'list' => ['icon', 'title', 'sort_order', 'is_active'],
        'fields' => [
            'icon'        => $icon,
            'title'       => ['label' => 'Title', 'required' => true],
            'description' => ['label' => 'Description', 'type' => 'textarea'],
            'sort_order'  => $order,
            'is_active'   => $active,
        ],
    ],
    'stats' => [
        'title' => 'Statistics', 'singular' => 'Statistic', 'icon' => 'bi-bar-chart', 'table' => 'stats',
        'list' => ['value', 'suffix', 'label', 'sort_order', 'is_active'],
        'fields' => [
            'value'      => ['label' => 'Number', 'type' => 'number', 'required' => true],
            'suffix'     => ['label' => 'Suffix', 'help' => 'e.g. + or %'],
            'label'      => ['label' => 'Label', 'required' => true],
            'sort_order' => $order,
            'is_active'  => $active,
        ],
    ],
    'technologies' => [
        'title' => 'Technologies', 'singular' => 'Technology', 'icon' => 'bi-cpu', 'table' => 'technologies',
        'list' => ['icon', 'name', 'sort_order', 'is_active'],
        'fields' => [
            'icon'       => $icon,
            'name'       => ['label' => 'Name', 'required' => true],
            'sort_order' => $order,
            'is_active'  => $active,
        ],
    ],
    'pages' => [
        'title' => 'Pages', 'singular' => 'Page', 'icon' => 'bi-file-earmark-text', 'table' => 'pages',
        'list' => ['title', 'slug', 'show_in_footer', 'is_active'],
        'unique' => ['slug'],
        'timestamps' => ['created_at', 'updated_at'],
        'view_url' => 'page/{slug}',
        'fields' => [
            'title'            => ['label' => 'Page title', 'required' => true],
            'slug'             => ['label' => 'URL slug', 'type' => 'slug', 'from' => 'title', 'help' => 'Leave empty to generate from the title. Page address: /page/your-slug'],
            'content'          => ['label' => 'Content', 'type' => 'html'],
            'meta_description' => ['label' => 'Meta description (Google)', 'type' => 'textarea'],
            'show_in_footer'   => ['label' => 'Link in footer', 'type' => 'checkbox', 'default' => 1],
            'sort_order'       => $order,
            'is_active'        => ['label' => 'Published', 'type' => 'checkbox', 'default' => 1],
        ],
    ],
    'subscribers' => [
        'title' => 'Subscribers', 'singular' => 'Subscriber', 'icon' => 'bi-people', 'table' => 'subscribers',
        'list' => ['email', 'created_at'],
        'order' => 'id DESC',
        'timestamps' => ['created_at'],
        'unique' => ['email'],
        'fields' => [
            'email' => ['label' => 'Email', 'type' => 'email', 'required' => true],
        ],
    ],
];

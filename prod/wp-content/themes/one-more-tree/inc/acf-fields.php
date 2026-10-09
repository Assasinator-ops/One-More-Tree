<?php
/**
 * ACF Field Groups for Homepage
 *
 * Register custom fields for homepage content management
 */

// Only load ACF fields if ACF PRO is active
if (class_exists('ACF') && function_exists('acf_add_local_field_group')):

    // Hero Section Fields
    acf_add_local_field_group([
        'key' => 'group_hero_section',
        'title' => 'Hero Section',
        'fields' => [
            [
                'key' => 'field_hero_mission_badge',
                'label' => 'Mission Badge Text',
                'name' => 'hero_mission_badge',
                'type' => 'text',
                'default_value' => 'Planting trees, restoring forests.',
                'placeholder' => '',
                'wrapper' => [
                    'width' => '50',
                ],
            ],
            [
                'key' => 'field_hero_headline_top',
                'label' => 'Headline (Top)',
                'name' => 'hero_headline_top',
                'type' => 'text',
                'default_value' => 'One More',
                'placeholder' => '',
                'wrapper' => [
                    'width' => '50',
                ],
            ],
            [
                'key' => 'field_hero_headline_emphasis',
                'label' => 'Headline (Emphasis)',
                'name' => 'hero_headline_emphasis',
                'type' => 'text',
                'default_value' => 'Tree',
                'placeholder' => '',
                'wrapper' => [
                    'width' => '50',
                ],
            ],
            [
                'key' => 'field_hero_subtitle',
                'label' => 'Subtitle',
                'name' => 'hero_subtitle',
                'type' => 'textarea',
                'default_value' => 'Planting 1 million trees across Spain by 2040. From a conversation among friends to a movement restoring forests.',
                'rows' => 2,
                'wrapper' => [
                    'width' => '100',
                ],
            ],
            [
                'key' => 'field_hero_primary_cta_text',
                'label' => 'Primary CTA Text',
                'name' => 'hero_primary_cta_text',
                'type' => 'text',
                'default_value' => 'Join the Movement',
                'wrapper' => [
                    'width' => '50',
                ],
            ],
            [
                'key' => 'field_hero_secondary_cta_text',
                'label' => 'Secondary CTA Text',
                'name' => 'hero_secondary_cta_text',
                'type' => 'text',
                'default_value' => 'Watch the Journey',
                'wrapper' => [
                    'width' => '50',
                ],
            ],
            [
                'key' => 'field_hero_trees_count',
                'label' => 'Trees Count',
                'name' => 'hero_trees_count',
                'type' => 'text',
                'default_value' => '1M',
                'wrapper' => [
                    'width' => '33',
                ],
            ],
            [
                'key' => 'field_hero_projects_count',
                'label' => 'Projects Count',
                'name' => 'hero_projects_count',
                'type' => 'text',
                'default_value' => '50+',
                'wrapper' => [
                    'width' => '33',
                ],
            ],
            [
                'key' => 'field_hero_volunteers_count',
                'label' => 'Volunteers Count',
                'name' => 'hero_volunteers_count',
                'type' => 'text',
                'default_value' => '10K+',
                'wrapper' => [
                    'width' => '33',
                ],
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'page_template',
                    'operator' => '==',
                    'value' => 'front-page.php',
                ],
            ],
        ],
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => [],
        'active' => true,
        'description' => '',
    ]);

    // Counter Section Fields
    acf_add_local_field_group([
        'key' => 'group_counter_section',
        'title' => 'Counter Section',
        'fields' => [
            [
                'key' => 'field_counter_trees_tracked',
                'label' => 'Trees Tracked',
                'name' => 'counter_trees_tracked',
                'type' => 'text',
                'default_value' => '1,000,000',
                'wrapper' => [
                    'width' => '33',
                ],
            ],
            [
                'key' => 'field_counter_projects_active',
                'label' => 'Projects Active',
                'name' => 'counter_projects_active',
                'type' => 'text',
                'default_value' => '50+',
                'wrapper' => [
                    'width' => '33',
                ],
            ],
            [
                'key' => 'field_counter_volunteers_engaged',
                'label' => 'Volunteers Engaged',
                'name' => 'counter_volunteers_engaged',
                'type' => 'text',
                'default_value' => '10,000+',
                'wrapper' => [
                    'width' => '33',
                ],
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'page_template',
                    'operator' => '==',
                    'value' => 'front-page.php',
                ],
            ],
        ],
        'menu_order' => 1,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => [],
        'active' => true,
        'description' => '',
    ]);

    // Mission Section Fields
    acf_add_local_field_group([
        'key' => 'group_mission_section',
        'title' => 'Mission Section',
        'fields' => [
            [
                'key' => 'field_mission_europe_first',
                'label' => 'Europe First Description',
                'name' => 'mission_europe_first',
                'type' => 'textarea',
                'default_value' => 'Our mission is rooted in Europe: local forests, local communities, local restoration.',
                'rows' => 2,
                'wrapper' => [
                    'width' => '100',
                ],
            ],
            [
                'key' => 'field_mission_community_power',
                'label' => 'Community Power Description',
                'name' => 'mission_community_power',
                'type' => 'textarea',
                'default_value' => 'Volunteers and partners work together to plant, protect, and nurture every site.',
                'rows' => 2,
                'wrapper' => [
                    'width' => '100',
                ],
            ],
            [
                'key' => 'field_mission_visible_progress',
                'label' => 'Visible Progress Description',
                'name' => 'mission_visible_progress',
                'type' => 'textarea',
                'default_value' => 'Every planting story is documented with images and milestones so supporters see real results.',
                'rows' => 2,
                'wrapper' => [
                    'width' => '100',
                ],
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'page_template',
                    'operator' => '==',
                    'value' => 'front-page.php',
                ],
            ],
        ],
        'menu_order' => 2,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => [],
        'active' => true,
        'description' => '',
    ]);

    // Featured Progress Section Fields
    acf_add_local_field_group([
        'key' => 'group_featured_progress_section',
        'title' => 'Featured Progress Section',
        'fields' => [
            [
                'key' => 'field_featured_progress_heading',
                'label' => 'Featured Progress Heading',
                'name' => 'featured_progress_heading',
                'type' => 'text',
                'default_value' => 'How our first site grew from seedling to canopy',
                'wrapper' => [
                    'width' => '100',
                ],
            ],
            [
                'key' => 'field_featured_progress_description',
                'label' => 'Featured Progress Description',
                'name' => 'featured_progress_description',
                'type' => 'textarea',
                'default_value' => 'We track every planting with a clear timeline and visuals, so supporters can follow the journey from the first sapling to a thriving forest.',
                'rows' => 2,
                'wrapper' => [
                    'width' => '100',
                ],
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'page_template',
                    'operator' => '==',
                    'value' => 'front-page.php',
                ],
            ],
        ],
        'menu_order' => 3,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => [],
        'active' => true,
        'description' => '',
    ]);

    // Donation Section Fields
    acf_add_local_field_group([
        'key' => 'group_donation_section',
        'title' => 'Donation Section',
        'fields' => [
            [
                'key' => 'field_donation_heading',
                'label' => 'Donation Heading',
                'name' => 'donation_heading',
                'type' => 'text',
                'default_value' => 'Give with confidence in a beautiful, interactive way',
                'wrapper' => [
                    'width' => '100',
                ],
            ],
            [
                'key' => 'field_donation_description',
                'label' => 'Donation Description',
                'name' => 'donation_description',
                'type' => 'textarea',
                'default_value' => 'Explore each donation tier with a 3D preview card, then choose the amount that helps grow forests across Spain. This section is designed to feel engaging, warm, and effortless.',
                'rows' => 2,
                'wrapper' => [
                    'width' => '100',
                ],
            ],
            [
                'key' => 'field_donation_tier_1',
                'label' => 'Donation Tier 1',
                'name' => 'donation_tier_1',
                'type' => 'text',
                'default_value' => '$25',
                'wrapper' => [
                    'width' => '33',
                ],
            ],
            [
                'key' => 'field_donation_tier_2',
                'label' => 'Donation Tier 2',
                'name' => 'donation_tier_2',
                'type' => 'text',
                'default_value' => '$50',
                'wrapper' => [
                    'width' => '33',
                ],
            ],
            [
                'key' => 'field_donation_tier_3',
                'label' => 'Donation Tier 3',
                'name' => 'donation_tier_3',
                'type' => 'text',
                'default_value' => '$100',
                'wrapper' => [
                    'width' => '33',
                ],
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'page_template',
                    'operator' => '==',
                    'value' => 'front-page.php',
                ],
            ],
        ],
        'menu_order' => 4,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => [],
        'active' => true,
        'description' => '',
    ]);

    // About Page Hero Section Fields
    acf_add_local_field_group([
        'key' => 'group_about_hero_section',
        'title' => 'About Hero Section',
        'fields' => [
            [
                'key' => 'field_about_hero_title',
                'label' => 'Hero Title',
                'name' => 'about_hero_title',
                'type' => 'text',
                'default_value' => 'Our Story & Mission',
                'wrapper' => [
                    'width' => '100',
                ],
            ],
            [
                'key' => 'field_about_hero_subtitle',
                'label' => 'Hero Subtitle',
                'name' => 'about_hero_subtitle',
                'type' => 'textarea',
                'default_value' => 'Growing forests, building hope in Spain',
                'rows' => 2,
                'wrapper' => [
                    'width' => '100',
                ],
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'page_template',
                    'operator' => '==',
                    'value' => 'page-about.php',
                ],
            ],
        ],
        'menu_order' => 5,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => [],
        'active' => true,
        'description' => '',
    ]);

    // About Values Section Fields
    acf_add_local_field_group([
        'key' => 'group_about_values_section',
        'title' => 'About Values Section',
        'fields' => [
            [
                'key' => 'field_about_section_title',
                'label' => 'Section Title',
                'name' => 'about_section_title',
                'type' => 'text',
                'default_value' => 'Growing forests, building hope in Spain',
                'wrapper' => [
                    'width' => '100',
                ],
            ],
            [
                'key' => 'field_about_section_description',
                'label' => 'Section Description',
                'name' => 'about_section_description',
                'type' => 'textarea',
                'default_value' => 'From a conversation among friends to a movement restoring forests across Spain. We plant trees in schools, sports complexes, senior centers, wildfire-damaged forests, and along the Camino de Santiago.',
                'rows' => 3,
                'wrapper' => [
                    'width' => '100',
                ],
            ],
            [
                'key' => 'field_about_value_1_title',
                'label' => 'Value 1 Title',
                'name' => 'about_value_1_title',
                'type' => 'text',
                'default_value' => 'Start with seeds',
                'wrapper' => [
                    'width' => '50',
                ],
            ],
            [
                'key' => 'field_about_value_1_description',
                'label' => 'Value 1 Description',
                'name' => 'about_value_1_description',
                'type' => 'textarea',
                'default_value' => 'We plant native trees using science-backed methods for long-term growth.',
                'rows' => 2,
                'wrapper' => [
                    'width' => '50',
                ],
            ],
            [
                'key' => 'field_about_value_2_title',
                'label' => 'Value 2 Title',
                'name' => 'about_value_2_title',
                'type' => 'text',
                'default_value' => 'Respect ecosystems',
                'wrapper' => [
                    'width' => '50',
                ],
            ],
            [
                'key' => 'field_about_value_2_description',
                'label' => 'Value 2 Description',
                'name' => 'about_value_2_description',
                'type' => 'textarea',
                'default_value' => 'Restoration must protect biodiversity and strengthen local environments.',
                'rows' => 2,
                'wrapper' => [
                    'width' => '50',
                ],
            ],
            [
                'key' => 'field_about_value_3_title',
                'label' => 'Value 3 Title',
                'name' => 'about_value_3_title',
                'type' => 'text',
                'default_value' => 'Commit for the long run',
                'wrapper' => [
                    'width' => '50',
                ],
            ],
            [
                'key' => 'field_about_value_3_description',
                'label' => 'Value 3 Description',
                'name' => 'about_value_3_description',
                'type' => 'textarea',
                'default_value' => 'We focus on survival rates, maintenance, and measurable outcomes.',
                'rows' => 2,
                'wrapper' => [
                    'width' => '50',
                ],
            ],
            [
                'key' => 'field_about_value_4_title',
                'label' => 'Value 4 Title',
                'name' => 'about_value_4_title',
                'type' => 'text',
                'default_value' => 'Work with communities',
                'wrapper' => [
                    'width' => '50',
                ],
            ],
            [
                'key' => 'field_about_value_4_description',
                'label' => 'Value 4 Description',
                'name' => 'about_value_4_description',
                'type' => 'textarea',
                'default_value' => 'Local partners lead, we support with resources, training, and transparency.',
                'rows' => 2,
                'wrapper' => [
                    'width' => '50',
                ],
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'page_template',
                    'operator' => '==',
                    'value' => 'page-about.php',
                ],
            ],
        ],
        'menu_order' => 6,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => [],
        'active' => true,
        'description' => '',
    ]);

    // About Restoration Section Fields
    acf_add_local_field_group([
        'key' => 'group_about_restoration_section',
        'title' => 'About Restoration Section',
        'fields' => [
            [
                'key' => 'field_restoration_title',
                'label' => 'Restoration Title',
                'name' => 'restoration_title',
                'type' => 'text',
                'default_value' => 'Measurable restoration you can trust',
                'wrapper' => [
                    'width' => '100',
                ],
            ],
            [
                'key' => 'field_restoration_description',
                'label' => 'Restoration Description',
                'name' => 'restoration_description',
                'type' => 'textarea',
                'default_value' => 'From native species selection to long-term monitoring, our programs are designed to maximize survival and restore ecosystems responsibly.',
                'rows' => 2,
                'wrapper' => [
                    'width' => '100',
                ],
            ],
            [
                'key' => 'field_restoration_stat_1_key',
                'label' => 'Stat 1 Key',
                'name' => 'restoration_stat_1_key',
                'type' => 'text',
                'default_value' => '3M',
                'wrapper' => [
                    'width' => '33',
                ],
            ],
            [
                'key' => 'field_restoration_stat_1_text',
                'label' => 'Stat 1 Text',
                'name' => 'restoration_stat_1_text',
                'type' => 'text',
                'default_value' => 'Trees by 2040',
                'wrapper' => [
                    'width' => '33',
                ],
            ],
            [
                'key' => 'field_restoration_stat_2_key',
                'label' => 'Stat 2 Key',
                'name' => 'restoration_stat_2_key',
                'type' => 'text',
                'default_value' => 'Native',
                'wrapper' => [
                    'width' => '33',
                ],
            ],
            [
                'key' => 'field_restoration_stat_2_text',
                'label' => 'Stat 2 Text',
                'name' => 'restoration_stat_2_text',
                'type' => 'text',
                'default_value' => 'Species Focus',
                'wrapper' => [
                    'width' => '33',
                ],
            ],
            [
                'key' => 'field_restoration_stat_3_key',
                'label' => 'Stat 3 Key',
                'name' => 'restoration_stat_3_key',
                'type' => 'text',
                'default_value' => 'Local',
                'wrapper' => [
                    'width' => '33',
                ],
            ],
            [
                'key' => 'field_restoration_stat_3_text',
                'label' => 'Stat 3 Text',
                'name' => 'restoration_stat_3_text',
                'type' => 'text',
                'default_value' => 'Community-led',
                'wrapper' => [
                    'width' => '33',
                ],
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'page_template',
                    'operator' => '==',
                    'value' => 'page-about.php',
                ],
            ],
        ],
        'menu_order' => 7,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => [],
        'active' => true,
        'description' => '',
    ]);

    // About Testimonials Section Fields
    acf_add_local_field_group([
        'key' => 'group_about_testimonials_section',
        'title' => 'About Testimonials Section',
        'fields' => [
            [
                'key' => 'field_testimonials_title',
                'label' => 'Testimonials Title',
                'name' => 'testimonials_title',
                'type' => 'text',
                'default_value' => 'Stories from the field',
                'wrapper' => [
                    'width' => '100',
                ],
            ],
            [
                'key' => 'field_testimonials_description',
                'label' => 'Testimonials Description',
                'name' => 'testimonials_description',
                'type' => 'textarea',
                'default_value' => 'Restoration is more than planting. It is people, knowledge, and long-term care.',
                'rows' => 2,
                'wrapper' => [
                    'width' => '100',
                ],
            ],
            [
                'key' => 'field_testimonial_1_quote',
                'label' => 'Testimonial 1 Quote',
                'name' => 'testimonial_1_quote',
                'type' => 'textarea',
                'default_value' => 'The trees are growing and our community has a long-term project we can truly be proud of.',
                'rows' => 2,
                'wrapper' => [
                    'width' => '50',
                ],
            ],
            [
                'key' => 'field_testimonial_1_name',
                'label' => 'Testimonial 1 Name',
                'name' => 'testimonial_1_name',
                'type' => 'text',
                'default_value' => 'Miriam, Community Lead',
                'wrapper' => [
                    'width' => '50',
                ],
            ],
            [
                'key' => 'field_testimonial_1_meta',
                'label' => 'Testimonial 1 Meta',
                'name' => 'testimonial_1_meta',
                'type' => 'text',
                'default_value' => 'Savannah conservation',
                'wrapper' => [
                    'width' => '50',
                ],
            ],
            [
                'key' => 'field_testimonial_2_quote',
                'label' => 'Testimonial 2 Quote',
                'name' => 'testimonial_2_quote',
                'type' => 'textarea',
                'default_value' => 'Their monitoring helped us understand what worked, so we can improve survival rates every season.',
                'rows' => 2,
                'wrapper' => [
                    'width' => '50',
                ],
            ],
            [
                'key' => 'field_testimonial_2_name',
                'label' => 'Testimonial 2 Name',
                'name' => 'testimonial_2_name',
                'type' => 'text',
                'default_value' => 'Dr. Chen, Research Partner',
                'wrapper' => [
                    'width' => '50',
                ],
            ],
            [
                'key' => 'field_testimonial_2_meta',
                'label' => 'Testimonial 2 Meta',
                'name' => 'testimonial_2_meta',
                'type' => 'text',
                'default_value' => 'Forest restoration',
                'wrapper' => [
                    'width' => '50',
                ],
            ],
            [
                'key' => 'field_testimonial_3_quote',
                'label' => 'Testimonial 3 Quote',
                'name' => 'testimonial_3_quote',
                'type' => 'textarea',
                'default_value' => 'Transparent reporting and local ownership turned restoration into something that lasts.',
                'rows' => 2,
                'wrapper' => [
                    'width' => '50',
                ],
            ],
            [
                'key' => 'field_testimonial_3_name',
                'label' => 'Testimonial 3 Name',
                'name' => 'testimonial_3_name',
                'type' => 'text',
                'default_value' => 'Ayo, Field Coordinator',
                'wrapper' => [
                    'width' => '50',
                ],
            ],
            [
                'key' => 'field_testimonial_3_meta',
                'label' => 'Testimonial 3 Meta',
                'name' => 'testimonial_3_meta',
                'type' => 'text',
                'default_value' => 'Urban greening',
                'wrapper' => [
                    'width' => '50',
                ],
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'page_template',
                    'operator' => '==',
                    'value' => 'page-about.php',
                ],
            ],
        ],
        'menu_order' => 8,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => [],
        'active' => true,
        'description' => '',
    ]);

    // Projects Page Field Group
    acf_add_local_field_group([
        'key' => 'group_projects_page',
        'title' => 'Projects Page Settings',
        'fields' => [
            [
                'key' => 'field_projects_hero_title',
                'label' => 'Hero Title',
                'name' => 'projects_hero_title',
                'type' => 'text',
                'default_value' => 'Our Projects Across Spain',
            ],
            [
                'key' => 'field_projects_hero_subtitle',
                'label' => 'Hero Subtitle',
                'name' => 'projects_hero_subtitle',
                'type' => 'textarea',
                'default_value' => 'Explore our active initiatives and see the impact we\'re making across Spain',
                'rows' => 2,
            ],
            [
                'key' => 'field_projects_section_badge',
                'label' => 'Section Badge',
                'name' => 'projects_section_badge',
                'type' => 'text',
                'default_value' => 'Live Tree Map',
            ],
            [
                'key' => 'field_projects_section_headline',
                'label' => 'Section Headline',
                'name' => 'projects_section_headline',
                'type' => 'text',
                'default_value' => 'Track Trees in Real Time',
            ],
            [
                'key' => 'field_projects_section_description',
                'label' => 'Section Description',
                'name' => 'projects_section_description',
                'type' => 'textarea',
                'default_value' => 'Click markers to explore progress images, planting milestones, and real planting stories across Europe.',
                'rows' => 2,
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'page_template',
                    'operator' => '==',
                    'value' => 'page-projects.php',
                ],
            ],
        ],
    ]);

    // Impact Page Field Group
    acf_add_local_field_group([
        'key' => 'group_impact_page',
        'title' => 'Impact Page Settings',
        'fields' => [
            [
                'key' => 'field_impact_hero_title',
                'label' => 'Hero Title',
                'name' => 'impact_hero_title',
                'type' => 'text',
                'default_value' => 'Our Impact',
            ],
            [
                'key' => 'field_impact_hero_subtitle',
                'label' => 'Hero Subtitle',
                'name' => 'impact_hero_subtitle',
                'type' => 'textarea',
                'default_value' => 'See how our trees grow and the forests we\'re restoring across Spain',
                'rows' => 2,
            ],
            [
                'key' => 'field_impact_section_badge',
                'label' => 'Section Badge',
                'name' => 'impact_section_badge',
                'type' => 'text',
                'default_value' => 'Spain Reforestation Progress',
            ],
            [
                'key' => 'field_impact_headline',
                'label' => 'Impact Headline',
                'name' => 'impact_headline',
                'type' => 'text',
                'default_value' => 'The Numbers Tell Our Story',
            ],
            [
                'key' => 'field_impact_description',
                'label' => 'Impact Description',
                'name' => 'impact_description',
                'type' => 'textarea',
                'default_value' => 'Every tree planted is tracked, verified, and nurtured for long-term survival across ecosystems in Spain.',
                'rows' => 2,
            ],
            [
                'key' => 'field_impact_goal_title',
                'label' => 'Goal Title',
                'name' => 'impact_goal_title',
                'type' => 'text',
                'default_value' => 'Goal: 1,000,000 Trees by 2040',
            ],
            [
                'key' => 'field_impact_goal_subtitle',
                'label' => 'Goal Subtitle',
                'name' => 'impact_goal_subtitle',
                'type' => 'text',
                'default_value' => '153,000 trees to go to reach our first milestone',
            ],
            [
                'key' => 'field_impact_goal_percent',
                'label' => 'Goal Percent',
                'name' => 'impact_goal_percent',
                'type' => 'text',
                'default_value' => '84.7%',
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'page_template',
                    'operator' => '==',
                    'value' => 'page-impact.php',
                ],
            ],
        ],
    ]);

    // FAQ Page Field Group
    acf_add_local_field_group([
        'key' => 'group_faq_page',
        'title' => 'FAQ Page Settings',
        'fields' => [
            [
                'key' => 'field_faq_hero_title',
                'label' => 'Hero Title',
                'name' => 'faq_hero_title',
                'type' => 'text',
                'default_value' => 'Frequently Asked Questions',
            ],
            [
                'key' => 'field_faq_hero_subtitle',
                'label' => 'Hero Subtitle',
                'name' => 'faq_hero_subtitle',
                'type' => 'textarea',
                'default_value' => 'Find answers to common questions about our mission and how to get involved',
                'rows' => 2,
            ],
            [
                'key' => 'field_faq_section_badge',
                'label' => 'Section Badge',
                'name' => 'faq_section_badge',
                'type' => 'text',
                'default_value' => 'FAQ',
            ],
            [
                'key' => 'field_faq_section_headline',
                'label' => 'Section Headline',
                'name' => 'faq_section_headline',
                'type' => 'text',
                'default_value' => 'Questions, answered',
            ],
            [
                'key' => 'field_faq_section_description',
                'label' => 'Section Description',
                'name' => 'faq_section_description',
                'type' => 'textarea',
                'default_value' => 'Clear answers about how we plant, measure impact, and partner with communities.',
                'rows' => 2,
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'page_template',
                    'operator' => '==',
                    'value' => 'page-faq.php',
                ],
            ],
        ],
    ]);

    // Contact Page Field Group
    acf_add_local_field_group([
        'key' => 'group_contact_page',
        'title' => 'Contact Page Settings',
        'fields' => [
            [
                'key' => 'field_contact_hero_title',
                'label' => 'Hero Title',
                'name' => 'contact_hero_title',
                'type' => 'text',
                'default_value' => 'Let us connect',
            ],
            [
                'key' => 'field_contact_hero_subtitle',
                'label' => 'Hero Subtitle',
                'name' => 'contact_hero_subtitle',
                'type' => 'textarea',
                'default_value' => 'Send a message and we will get back to you with guidance on volunteering, partnerships, or donations.',
                'rows' => 2,
            ],
            [
                'key' => 'field_contact_address',
                'label' => 'Physical Address',
                'name' => 'contact_address',
                'type' => 'text',
                'default_value' => 'Calle Gran Vía 28, Madrid, Spain',
            ],
            [
                'key' => 'field_contact_email',
                'label' => 'Contact Email',
                'name' => 'contact_email',
                'type' => 'email',
                'default_value' => 'info@onemoretree.eu',
            ],
            [
                'key' => 'field_contact_phone',
                'label' => 'Contact Phone',
                'name' => 'contact_phone',
                'type' => 'text',
                'default_value' => '+34 912 345 678',
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'page_template',
                    'operator' => '==',
                    'value' => 'page-contact.php',
                ],
            ],
        ],
    ]);

    // Volunteer Page Field Group
    acf_add_local_field_group([
        'key' => 'group_volunteer_page',
        'title' => 'Volunteer Page Settings',
        'fields' => [
            [
                'key' => 'field_volunteer_hero_title',
                'label' => 'Hero Title',
                'name' => 'volunteer_hero_title',
                'type' => 'text',
                'default_value' => 'Help restore forests, together',
            ],
            [
                'key' => 'field_volunteer_hero_subtitle',
                'label' => 'Hero Subtitle',
                'name' => 'volunteer_hero_subtitle',
                'type' => 'textarea',
                'default_value' => 'Sign up to receive local volunteering opportunities and updates.',
                'rows' => 2,
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'page_template',
                    'operator' => '==',
                    'value' => 'page-volunteer.php',
                ],
            ],
        ],
    ]);

    // Get Involved Page Field Group
    acf_add_local_field_group([
        'key' => 'group_get_involved_page',
        'title' => 'Get Involved Page Settings',
        'fields' => [
            [
                'key' => 'field_get_involved_hero_title',
                'label' => 'Hero Title',
                'name' => 'get_involved_hero_title',
                'type' => 'text',
                'default_value' => 'Get Involved',
            ],
            [
                'key' => 'field_get_involved_hero_subtitle',
                'label' => 'Hero Subtitle',
                'name' => 'get_involved_hero_subtitle',
                'type' => 'textarea',
                'default_value' => 'Join our movement. Plant trees, make an impact, and help restore Spain\'s forests',
                'rows' => 2,
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'page_template',
                    'operator' => '==',
                    'value' => 'page-get-involved.php',
                ],
            ],
        ],
    ]);

    // Donate Page Field Group
    acf_add_local_field_group([
        'key' => 'group_donate_page',
        'title' => 'Donate Page Settings',
        'fields' => [
            [
                'key' => 'field_donate_hero_title',
                'label' => 'Hero Title',
                'name' => 'donate_hero_title',
                'type' => 'text',
                'default_value' => 'Support Reforestation',
            ],
            [
                'key' => 'field_donate_hero_subtitle',
                'label' => 'Hero Subtitle',
                'name' => 'donate_hero_subtitle',
                'type' => 'textarea',
                'default_value' => 'Every donation directly funds the planting, irrigation, and protection of native forests in Spain.',
                'rows' => 2,
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'page_template',
                    'operator' => '==',
                    'value' => 'page-donate.php',
                ],
            ],
        ],
    ]);

endif;

/**
 * Helper function to get ACF field value with fallback
 * This function works seamlessly whether ACF is active or settings are managed via WP Admin / Next.js Console
 */
if (!function_exists('get_acf_field')) :
function get_acf_field($field_name, $default = '') {
    if (function_exists('get_field')) {
        $value = get_field($field_name);
        if (!empty($value)) {
            return $value;
        }
    }

    // Dynamic mapping to OneMoreTree admin options
    switch ($field_name) {
        case 'site_disclaimer_message':
            $d = get_option('omt_admin_disclaimer');
            if (!empty($d['message'])) return $d['message'];
            break;

        case 'hero_trees_count':
        case 'counter_trees_tracked':
            $hc = get_option('omt_admin_home_counters');
            if (!empty($hc['treesTracked'])) return number_format($hc['treesTracked']);
            break;

        case 'hero_projects_count':
        case 'counter_projects_active':
            $hc = get_option('omt_admin_home_counters');
            if (!empty($hc['projectsActive'])) return $hc['projectsActive'] . '+';
            break;

        case 'hero_volunteers_count':
        case 'counter_volunteers_engaged':
            $hc = get_option('omt_admin_home_counters');
            if (!empty($hc['volunteersEngaged'])) return number_format($hc['volunteersEngaged']) . '+';
            break;

        case 'contact_address':
            $addr = get_option('omt_contact_address');
            if (!empty($addr)) return $addr;
            break;

        case 'contact_email':
            $email = get_option('omt_contact_email');
            if (!empty($email)) return $email;
            break;

        case 'contact_phone':
            $phone = get_option('omt_contact_phone');
            if (!empty($phone)) return $phone;
            break;

        case 'story_seed_chapter_label':
            $st = get_option('omt_admin_stories');
            if (!empty($st['seed']['chapter'])) return $st['seed']['chapter'];
            break;
        case 'story_seed_headline':
            $st = get_option('omt_admin_stories');
            if (!empty($st['seed']['title'])) return $st['seed']['title'];
            break;
        case 'story_seed_desc_1':
            $st = get_option('omt_admin_stories');
            if (!empty($st['seed']['body1'])) return $st['seed']['body1'];
            break;
        case 'story_seed_desc_2':
            $st = get_option('omt_admin_stories');
            if (!empty($st['seed']['body2'])) return $st['seed']['body2'];
            break;

        case 'story_growth_chapter_label':
            $st = get_option('omt_admin_stories');
            if (!empty($st['growth']['chapter'])) return $st['growth']['chapter'];
            break;
        case 'story_growth_headline':
            $st = get_option('omt_admin_stories');
            if (!empty($st['growth']['title'])) return $st['growth']['title'];
            break;
        case 'story_growth_desc_1':
            $st = get_option('omt_admin_stories');
            if (!empty($st['growth']['body1'])) return $st['growth']['body1'];
            break;
        case 'story_growth_desc_2':
            $st = get_option('omt_admin_stories');
            if (!empty($st['growth']['body2'])) return $st['growth']['body2'];
            break;

        case 'story_mature_chapter_label':
            $st = get_option('omt_admin_stories');
            if (!empty($st['mature']['chapter'])) return $st['mature']['chapter'];
            break;
        case 'story_mature_headline':
            $st = get_option('omt_admin_stories');
            if (!empty($st['mature']['title'])) return $st['mature']['title'];
            break;
        case 'story_mature_desc_1':
            $st = get_option('omt_admin_stories');
            if (!empty($st['mature']['body1'])) return $st['mature']['body1'];
            break;
        case 'story_mature_desc_2':
            $st = get_option('omt_admin_stories');
            if (!empty($st['mature']['body2'])) return $st['mature']['body2'];
            break;

        // NEW: Hero fields
        case 'hero_mission_badge': $d = get_option('omt_admin_hero', []); return $d['mission_badge'] ?? $default;
        case 'hero_headline_top': $d = get_option('omt_admin_hero', []); return $d['headline_top'] ?? $default;
        case 'hero_headline_emphasis': $d = get_option('omt_admin_hero', []); return $d['headline_emphasis'] ?? $default;
        case 'hero_subtitle': $d = get_option('omt_admin_hero', []); return $d['subtitle'] ?? $default;
        case 'hero_primary_cta_text': $d = get_option('omt_admin_hero', []); return $d['primary_cta_text'] ?? $default;
        case 'hero_primary_cta_url': $d = get_option('omt_admin_hero', []); return $d['primary_cta_url'] ?? $default;
        case 'hero_secondary_cta_text': $d = get_option('omt_admin_hero', []); return $d['secondary_cta_text'] ?? $default;
        case 'hero_image_url': $d = get_option('omt_admin_hero', []); return $d['hero_image_url'] ?? $default;
        case 'hero_stat1_value': $d = get_option('omt_admin_hero', []); return $d['stat1_value'] ?? $default;
        case 'hero_stat1_label': $d = get_option('omt_admin_hero', []); return $d['stat1_label'] ?? $default;
        case 'hero_stat2_value': $d = get_option('omt_admin_hero', []); return $d['stat2_value'] ?? $default;
        case 'hero_stat2_label': $d = get_option('omt_admin_hero', []); return $d['stat2_label'] ?? $default;
        case 'hero_stat3_value': $d = get_option('omt_admin_hero', []); return $d['stat3_value'] ?? $default;
        case 'hero_stat3_label': $d = get_option('omt_admin_hero', []); return $d['stat3_label'] ?? $default;

        case 'mission_badge': $d = get_option('omt_admin_mission', []); return $d['badge'] ?? $default;
        case 'mission_headline': $d = get_option('omt_admin_mission', []); return $d['headline'] ?? $default;
        case 'mission_subtitle': $d = get_option('omt_admin_mission', []); return $d['subtitle'] ?? $default;
        case 'mission_card1_icon': $d = get_option('omt_admin_mission', []); return $d['card1_icon'] ?? $default;
        case 'mission_card1_title': $d = get_option('omt_admin_mission', []); return $d['card1_title'] ?? $default;
        case 'mission_card1_desc': $d = get_option('omt_admin_mission', []); return $d['card1_desc'] ?? $default;
        case 'mission_card2_icon': $d = get_option('omt_admin_mission', []); return $d['card2_icon'] ?? $default;
        case 'mission_card2_title': $d = get_option('omt_admin_mission', []); return $d['card2_title'] ?? $default;
        case 'mission_card2_desc': $d = get_option('omt_admin_mission', []); return $d['card2_desc'] ?? $default;
        case 'mission_card3_icon': $d = get_option('omt_admin_mission', []); return $d['card3_icon'] ?? $default;
        case 'mission_card3_title': $d = get_option('omt_admin_mission', []); return $d['card3_title'] ?? $default;
        case 'mission_card3_desc': $d = get_option('omt_admin_mission', []); return $d['card3_desc'] ?? $default;

        case 'about_hero_badge': $d = get_option('omt_admin_about_hero', []); return $d['badge'] ?? $default;
        case 'about_hero_title': $d = get_option('omt_admin_about_hero', []); return $d['title'] ?? $default;
        case 'about_hero_subtitle': $d = get_option('omt_admin_about_hero', []); return $d['subtitle'] ?? $default;

        case 'impact_hero_badge': $d = get_option('omt_admin_impact_hero', []); return $d['badge'] ?? $default;
        case 'impact_hero_title': $d = get_option('omt_admin_impact_hero', []); return $d['title'] ?? $default;
        case 'impact_hero_subtitle': $d = get_option('omt_admin_impact_hero', []); return $d['subtitle'] ?? $default;

        case 'impact_goal_title': $d = get_option('omt_admin_impact_goal', []); return $d['title'] ?? $default;
        case 'impact_goal_target': $d = get_option('omt_admin_impact_goal', []); return $d['target'] ?? $default;
        case 'impact_goal_current': $d = get_option('omt_admin_impact_goal', []); return $d['current'] ?? $default;
        case 'impact_goal_remaining': $d = get_option('omt_admin_impact_goal', []); return $d['remaining_note'] ?? $default;

        case 'donate_hero_badge': $d = get_option('omt_admin_donate_hero', []); return $d['badge'] ?? $default;
        case 'donate_hero_title': $d = get_option('omt_admin_donate_hero', []); return $d['title'] ?? $default;
        case 'donate_hero_subtitle': $d = get_option('omt_admin_donate_hero', []); return $d['subtitle'] ?? $default;

        case 'volunteer_hero_badge': $d = get_option('omt_admin_volunteer_page', []); return $d['hero_badge'] ?? $default;
        case 'volunteer_hero_title': $d = get_option('omt_admin_volunteer_page', []); return $d['hero_title'] ?? $default;
        case 'volunteer_hero_subtitle': $d = get_option('omt_admin_volunteer_page', []); return $d['hero_subtitle'] ?? $default;
        case 'volunteer_form_title': $d = get_option('omt_admin_volunteer_page', []); return $d['form_title'] ?? $default;
        case 'volunteer_form_subtitle': $d = get_option('omt_admin_volunteer_page', []); return $d['form_subtitle'] ?? $default;
        case 'volunteer_interest_options': $d = get_option('omt_admin_volunteer_page', []); return $d['interest_options'] ?? $default;
        case 'volunteer_card1_icon': $d = get_option('omt_admin_volunteer_page', []); return $d['card1_icon'] ?? ($d['cards'][0]['icon'] ?? $default);
        case 'volunteer_card1_title': $d = get_option('omt_admin_volunteer_page', []); return $d['card1_title'] ?? ($d['cards'][0]['title'] ?? $default);
        case 'volunteer_card1_desc': $d = get_option('omt_admin_volunteer_page', []); return $d['card1_desc'] ?? ($d['cards'][0]['desc'] ?? $default);
        case 'volunteer_card2_icon': $d = get_option('omt_admin_volunteer_page', []); return $d['card2_icon'] ?? ($d['cards'][1]['icon'] ?? $default);
        case 'volunteer_card2_title': $d = get_option('omt_admin_volunteer_page', []); return $d['card2_title'] ?? ($d['cards'][1]['title'] ?? $default);
        case 'volunteer_card2_desc': $d = get_option('omt_admin_volunteer_page', []); return $d['card2_desc'] ?? ($d['cards'][1]['desc'] ?? $default);
        case 'volunteer_card3_icon': $d = get_option('omt_admin_volunteer_page', []); return $d['card3_icon'] ?? ($d['cards'][2]['icon'] ?? $default);
        case 'volunteer_card3_title': $d = get_option('omt_admin_volunteer_page', []); return $d['card3_title'] ?? ($d['cards'][2]['title'] ?? $default);
        case 'volunteer_card3_desc': $d = get_option('omt_admin_volunteer_page', []); return $d['card3_desc'] ?? ($d['cards'][2]['desc'] ?? $default);

        case 'contact_hero_badge': $d = get_option('omt_admin_contact_page', []); return $d['hero_badge'] ?? $default;
        case 'contact_hero_title': $d = get_option('omt_admin_contact_page', []); return $d['hero_title'] ?? $default;
        case 'contact_hero_subtitle': $d = get_option('omt_admin_contact_page', []); return $d['hero_subtitle'] ?? $default;
        case 'contact_form_title': $d = get_option('omt_admin_contact_page', []); return $d['form_title'] ?? $default;
        case 'contact_form_subtitle': $d = get_option('omt_admin_contact_page', []); return $d['form_subtitle'] ?? $default;
        case 'contact_btn_text': $d = get_option('omt_admin_contact_page', []); return $d['btn_text'] ?? $default;

        case 'site_title_custom': $d = get_option('omt_admin_site_identity', []); return $d['site_title'] ?? $default;
        case 'site_tagline_custom': $d = get_option('omt_admin_site_identity', []); return $d['tagline'] ?? $default;
        case 'logo_url': $d = get_option('omt_admin_site_identity', []); return $d['logo_url'] ?? $default;
        case 'favicon_url_custom': $d = get_option('omt_admin_site_identity', []); return $d['favicon_url'] ?? $default;

        case 'nav_home': $d = get_option('omt_admin_nav_labels', []); return $d['home'] ?? $default;
        case 'nav_about': $d = get_option('omt_admin_nav_labels', []); return $d['about'] ?? $default;
        case 'nav_projects': $d = get_option('omt_admin_nav_labels', []); return $d['projects'] ?? $default;
        case 'nav_impact': $d = get_option('omt_admin_nav_labels', []); return $d['impact'] ?? $default;
        case 'nav_faq': $d = get_option('omt_admin_nav_labels', []); return $d['faq'] ?? $default;
        case 'nav_get_involved': $d = get_option('omt_admin_nav_labels', []); return $d['get_involved'] ?? $default;
        case 'nav_volunteer': $d = get_option('omt_admin_nav_labels', []); return $d['volunteer'] ?? $default;
        case 'nav_contact': $d = get_option('omt_admin_nav_labels', []); return $d['contact'] ?? $default;
        case 'nav_donate': $d = get_option('omt_admin_nav_labels', []); return $d['donate'] ?? $default;
        case 'nav_cta_text': $d = get_option('omt_admin_nav_labels', []); return $d['cta_text'] ?? $default;
        case 'nav_cta_url': $d = get_option('omt_admin_nav_labels', []); return $d['cta_url'] ?? $default;

        case 'footer_tagline': $d = get_option('omt_admin_footer', []); return $d['tagline'] ?? $default;
        case 'footer_copyright': $d = get_option('omt_admin_footer', []); return $d['copyright'] ?? $default;

        // About Deliverables & Restoration stats
        case 'restoration_title': $d = get_option('omt_admin_about_deliverables', []); return $d['box_title'] ?? $default;
        case 'restoration_description': $d = get_option('omt_admin_about_deliverables', []); return $d['box_desc'] ?? $default;
        case 'restoration_stat_1_key': $d = get_option('omt_admin_about_deliverables', []); return $d['stat1_val'] ?? $default;
        case 'restoration_stat_1_text': $d = get_option('omt_admin_about_deliverables', []); return $d['stat1_label'] ?? $default;
        case 'restoration_stat_2_key': $d = get_option('omt_admin_about_deliverables', []); return $d['stat2_val'] ?? $default;
        case 'restoration_stat_2_text': $d = get_option('omt_admin_about_deliverables', []); return $d['stat2_label'] ?? $default;
        case 'restoration_stat_3_key': $d = get_option('omt_admin_about_deliverables', []); return $d['stat3_val'] ?? $default;
        case 'restoration_stat_3_text': $d = get_option('omt_admin_about_deliverables', []); return $d['stat3_label'] ?? $default;
    }

    return $default;
}
endif;

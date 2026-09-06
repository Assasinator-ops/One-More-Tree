<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package One_More_Tree
 */

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments
 */
function one_more_tree_pingback_header() {
    if (is_singular() && pings_open()) {
        printf('<link rel="pingback" href="%s">', esc_url(get_bloginfo('pingback_url')));
    }
}
add_action('wp_head', 'one_more_tree_pingback_header');

/**
 * Add custom body classes
 */
function one_more_tree_body_classes($classes) {
    if (!is_singular()) {
        $classes[] = 'hfeed';
    }
    return $classes;
}
add_filter('body_class', 'one_more_tree_body_classes');

/**
 * Add custom excerpt length
 */
function one_more_tree_excerpt_length($length) {
    return 24;
}
add_filter('excerpt_length', 'one_more_tree_excerpt_length');

/**
 * Add custom excerpt more text
 */
function one_more_tree_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'one_more_tree_excerpt_more');

/**
 * Seed initial blog posts if none exist
 */
function one_more_tree_seed_sample_posts() {
    $existing = get_posts(['numberposts' => 1, 'post_type' => 'post', 'post_status' => 'publish']);
    // If only the default "Hello world!" post exists or no posts
    if (count($existing) <= 1 && (!empty($existing) && $existing[0]->post_name === 'hello-world' || empty($existing))) {
        // Delete default hello world post
        if (!empty($existing) && $existing[0]->post_name === 'hello-world') {
            wp_delete_post($existing[0]->ID, true);
        }

        $sample_posts = [
            [
                'title'    => 'The Importance of Native Tree Species in Reforestation',
                'slug'     => 'native-tree-species-reforestation',
                'category' => 'Ecology',
                'excerpt'  => 'Understanding why planting native tree species is crucial for restoring ecosystems and supporting biodiversity across Spain.',
                'content'  => '<h2>Why local genetics are the foundation of a lasting forest</h2>
<p>When a forest is lost, the most obvious thing to plant in its place is a tree. The less obvious, and far more important, question is: which tree? Across two decades of restoration work, we have learned that the answer is almost always a native species — and increasingly, a native genotype from a local seed source.</p>
<p>A native tree is one that evolved in a particular region alongside the insects, fungi, birds, and soil microbes that depend on it. Replace it with a fast-growing exotic, and the tree may survive — but the forest around it never will.</p>
<h2>Why exotics underperform over time</h2>
<p>Exotic plantation species can look spectacular in year three and heartbreaking in year fifteen. They often lack the mutualisms that native trees rely on for nutrients, pest resistance, and pollination. Many also fail to support local food webs: an oak might host 500 species of caterpillar, while an equivalent stand of eucalyptus feeds almost none.</p>
<ul>
<li>Native trees sustain 4–10x more insect life than exotics of the same size.</li>
<li>Local seed sources survive regional droughts and frosts 30–60% better.</li>
<li>Mixed-native stands store more carbon per hectare after 20 years than monocultures.</li>
</ul>
<blockquote><p>A forest is not a collection of trees. It is a relationship between trees, and the place that made them.</p></blockquote>
<h2>What we do differently</h2>
<p>Every OneMoreTree project begins with a species audit. We inventory what should be there before we decide what to plant, and we partner with ecologists on the ground to source seed that matches the local ecosystem — not the global catalog.</p>',
            ],
            [
                'title'    => 'Our Journey to Plant 10,000 Trees in Sierra Nevada',
                'slug'     => 'planting-10000-trees-sierra-nevada',
                'category' => 'Field Report',
                'excerpt'  => 'A first-hand account of our recent mountain reforestation project in Andalusia and the impact on local communities.',
                'content'  => '<h2>The Mountain Mission</h2>
<p>The plan was simple: plant 10,000 indigenous trees across three high-altitude zones in the Sierra Nevada mountain range. The reality — as always — was richer and full of inspiring local determination.</p>
<h2>The volunteer coalition</h2>
<p>Fifty-two volunteers, twelve local forest guardians, six nursery botanists, and three coordinators worked together over seven intense days of mountain planting.</p>
<ul>
<li>High-altitude stone pines and native juniper provide natural soil anchors on steep slopes.</li>
<li>Local school groups joined to plant their own class micro-groves.</li>
<li>Micro-catchments were carved around each seedling to capture early morning condensation.</li>
</ul>
<h2>Looking forward</h2>
<p>Monitoring stations installed across the slopes will record soil moisture and sapling vigor over the next five years.</p>',
            ],
            [
                'title'    => 'Urban Forests: Bringing Nature Back to Spanish Cities',
                'slug'     => 'urban-forests-nature-in-spanish-cities',
                'category' => 'Urban Ecology',
                'excerpt'  => 'How mini-forests in urban centers cool streets, clean air, and reconnect city dwellers with nature.',
                'content'  => '<h2>The Urban Heat Island Effect</h2>
<p>Concrete and asphalt trap heat during sunny Mediterranean summers, driving city temperatures up to 8°C higher than surrounding countryside. Dense pocket forests offer a biological air conditioner for neighborhood streets.</p>
<h2>Miyawaki Method in Practice</h2>
<p>By planting native species densely in small urban pockets, trees grow up to 10 times faster, creating self-sustaining urban biodiversity sanctuaries within two to three years.</p>
<ul>
<li>Up to 30 times more noise reduction compared to standard park turf.</li>
<li>Significant particulate matter capture along busy transport arteries.</li>
<li>Community spaces where children learn ecology first-hand.</li>
</ul>',
            ],
            [
                'title'    => 'How Local Communities Are Leading Reforestation Stewardship',
                'slug'     => 'community-led-reforestation',
                'category' => 'Community',
                'excerpt'  => 'Why long-term forest survival depends on neighborhood ownership, stewardship programs, and local leadership.',
                'content'  => '<h2>Restoration Beyond the Planting Day</h2>
<p>Planting a sapling takes five minutes; nurturing it through dry summers requires months of dedicated stewardship. Our community ambassador program connects neighbors directly with designated forest plots.</p>
<h2>Empowering Local Guardians</h2>
<p>Through regular workshops, volunteer watering teams, and digital check-ins, local stewardship networks ensure survival rates exceed 90% across all project sites.</p>',
            ],
            [
                'title'    => 'Using Drones and Remote Sensing to Monitor Forest Growth',
                'slug'     => 'technology-in-forest-monitoring',
                'category' => 'Technology',
                'excerpt'  => 'High-resolution multispectral imagery and AI models give real-time visibility into canopy health and tree density.',
                'content'  => '<h2>From Boots on the Ground to Eyes in the Sky</h2>
<p>Measuring reforestation success across thousands of hectares used to take weeks of manual surveying. Today, lightweight drones equipped with multispectral sensors map entire valleys in hours.</p>
<h2>Tracking Chlorophyll and Vigor</h2>
<p>NDVI (Normalized Difference Vegetation Index) maps reveal tree stress weeks before it becomes visible to the naked eye, allowing targeted irrigation intervention.</p>',
            ],
            [
                'title'    => 'The Science of Carbon Sequestration in Mediterranean Ecosystems',
                'slug'     => 'science-of-carbon-sequestration',
                'category' => 'Climate Science',
                'excerpt'  => 'How native oak, pine, and scrubland systems store carbon below and above ground for centuries.',
                'content'  => '<h2>Below-Ground Carbon Reserves</h2>
<p>While rainforests store much of their carbon in massive trunks and canopy leaves, Mediterranean dryland forests store a remarkable proportion of carbon beneath the soil in extensive root systems.</p>
<h2>Resilience to Drought and Fire</h2>
<p>Deep root networks not only sequester carbon stably over centuries, but also allow rapid regeneration after brushfires, ensuring carbon stays locked safely underground.</p>',
            ],
        ];

        foreach ($sample_posts as $post_data) {
            // Check if post already exists
            if (!get_page_by_path($post_data['slug'], OBJECT, 'post')) {
                // Ensure category exists
                $cat_id = 0;
                $term = term_exists($post_data['category'], 'category');
                if (!$term) {
                    $new_term = wp_insert_term($post_data['category'], 'category');
                    if (!is_wp_error($new_term)) {
                        $cat_id = $new_term['term_id'];
                    }
                } else {
                    $cat_id = is_array($term) ? $term['term_id'] : $term;
                }

                $pid = wp_insert_post([
                    'post_title'    => $post_data['title'],
                    'post_name'     => $post_data['slug'],
                    'post_content'  => $post_data['content'],
                    'post_excerpt'  => $post_data['excerpt'],
                    'post_status'   => 'publish',
                    'post_type'     => 'post',
                    'post_author'   => 1,
                    'post_category' => $cat_id ? [$cat_id] : [],
                ]);
            }
        }
    }
}
add_action('init', 'one_more_tree_seed_sample_posts');

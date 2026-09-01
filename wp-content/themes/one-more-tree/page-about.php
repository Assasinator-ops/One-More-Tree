<?php
/**
 * Template Name: About Page
 *
 * The template for displaying the about page
 *
 * @package One_More_Tree
 */

get_header();
?>

<div class="relative bg-background">
  <main class="relative">
    <!-- Hero Section - Exact mirror of Next.js AboutView -->
    <section id="about-hero" class="relative min-h-[60vh] flex items-center justify-center overflow-hidden page-header-texture">
      <div class="absolute inset-0 bg-gradient-to-b from-forest-50/95 to-background"></div>
      <div class="absolute inset-0 leaf-pattern opacity-20 pointer-events-none"></div>
      <div class="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20 py-20 sm:py-32">
        <div class="max-w-5xl mx-auto text-center">
          <h1 class="font-serif text-4xl sm:text-5xl md:text-6xl font-bold text-foreground mb-6 leading-tight">
            <?php echo get_acf_field('about_hero_title', 'Our Story & Mission'); ?>
          </h1>
          <p class="text-lg sm:text-xl text-foreground/70 max-w-3xl mx-auto">
            <?php echo get_acf_field('about_hero_subtitle', 'Growing forests, building hope in Spain'); ?>
          </p>
        </div>
      </div>
    </section>

    <!-- About Section - Exact mirror of Next.js AboutSection.tsx -->
    <section id="about" class="relative min-h-[70vh] w-full py-20 sm:py-32 overflow-hidden gradient-bg">
      <div class="absolute inset-0 leaf-pattern opacity-20"></div>
      <div class="absolute top-0 right-0 w-[520px] h-[520px] bg-forest-300/15 rounded-full blur-3xl"></div>

      <div class="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20">
        <div class="max-w-7xl mx-auto">
          <div class="text-center mb-14 sm:mb-20">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-forest-100 border border-forest-200 mb-6">
              <svg class="w-4 h-4 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <span class="text-xs sm:text-sm text-forest-700 font-semibold uppercase tracking-wider">
                Our Story
              </span>
            </div>
            <h2 class="font-serif text-3xl sm:text-4xl md:text-5xl font-bold text-foreground mb-4 sm:mb-6 leading-tight">
              <?php echo get_acf_field('about_section_title', 'Growing forests, building hope in Spain'); ?>
            </h2>
            <p class="text-base sm:text-lg text-foreground/60 max-w-2xl mx-auto">
              <?php echo get_acf_field('about_section_description', 'From a conversation among friends to a movement restoring forests across Spain. We plant trees in schools, sports complexes, senior centers, wildfire-damaged forests, and along the Camino de Santiago.'); ?>
            </p>
          </div>

          <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <!-- Value 1 -->
            <div class="glass-card rounded-2xl p-6 sm:p-7 shadow-soft">
              <div class="w-12 h-12 rounded-xl bg-forest-100 flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
              </div>
              <div class="font-serif text-xl font-bold text-foreground mb-2"><?php echo get_acf_field('about_value_1_title', 'Start with seeds'); ?></div>
              <div class="text-sm text-foreground/60 leading-relaxed"><?php echo get_acf_field('about_value_1_description', 'We plant native trees using science-backed methods for long-term growth.'); ?></div>
            </div>

            <!-- Value 2 -->
            <div class="glass-card rounded-2xl p-6 sm:p-7 shadow-soft">
              <div class="w-12 h-12 rounded-xl bg-forest-100 flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div class="font-serif text-xl font-bold text-foreground mb-2"><?php echo get_acf_field('about_value_2_title', 'Respect ecosystems'); ?></div>
              <div class="text-sm text-foreground/60 leading-relaxed"><?php echo get_acf_field('about_value_2_description', 'Restoration must protect biodiversity and strengthen local environments.'); ?></div>
            </div>

            <!-- Value 3 -->
            <div class="glass-card rounded-2xl p-6 sm:p-7 shadow-soft">
              <div class="w-12 h-12 rounded-xl bg-forest-100 flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
              </div>
              <div class="font-serif text-xl font-bold text-foreground mb-2"><?php echo get_acf_field('about_value_3_title', 'Commit for the long run'); ?></div>
              <div class="text-sm text-foreground/60 leading-relaxed"><?php echo get_acf_field('about_value_3_description', 'We focus on survival rates, maintenance, and measurable outcomes.'); ?></div>
            </div>

            <!-- Value 4 -->
            <div class="glass-card rounded-2xl p-6 sm:p-7 shadow-soft">
              <div class="w-12 h-12 rounded-xl bg-forest-100 flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
              </div>
              <div class="font-serif text-xl font-bold text-foreground mb-2"><?php echo get_acf_field('about_value_4_title', 'Work with communities'); ?></div>
              <div class="text-sm text-foreground/60 leading-relaxed"><?php echo get_acf_field('about_value_4_description', 'Local partners lead, we support with resources, training, and transparency.'); ?></div>
            </div>
          </div>

          <!-- Restoration Section -->
          <div class="mt-10 glass-card rounded-3xl p-6 sm:p-10 flex flex-col lg:flex-row gap-8 items-start lg:items-center shadow-medium">
            <div class="flex-1">
              <div class="text-sm text-foreground/50 mb-2">What we deliver</div>
              <div class="font-serif text-2xl sm:text-3xl font-bold text-foreground mb-4">
                <?php echo get_acf_field('restoration_title', 'Measurable restoration you can trust'); ?>
              </div>
              <p class="text-sm sm:text-base text-foreground/60 max-w-2xl leading-relaxed">
                <?php echo get_acf_field('restoration_description', 'From native species selection to long-term monitoring, our programs are designed to maximize survival and restore ecosystems responsibly.'); ?>
              </p>
            </div>
            <div class="flex gap-4">
              <div class="text-center glass-card rounded-2xl p-4 w-[140px] shadow-soft">
                <div class="font-serif text-3xl font-bold text-forest-600 mb-1"><?php echo get_acf_field('restoration_stat_1_key', '3M'); ?></div>
                <div class="text-xs text-foreground/60"><?php echo get_acf_field('restoration_stat_1_text', 'Trees by 2040'); ?></div>
              </div>
              <div class="text-center glass-card rounded-2xl p-4 w-[140px] shadow-soft">
                <div class="font-serif text-3xl font-bold text-forest-600 mb-1"><?php echo get_acf_field('restoration_stat_2_key', 'Native'); ?></div>
                <div class="text-xs text-foreground/60"><?php echo get_acf_field('restoration_stat_2_text', 'Species Focus'); ?></div>
              </div>
              <div class="text-center glass-card rounded-2xl p-4 w-[140px] shadow-soft">
                <div class="font-serif text-3xl font-bold text-forest-600 mb-1"><?php echo get_acf_field('restoration_stat_3_key', 'Local'); ?></div>
                <div class="text-xs text-foreground/60"><?php echo get_acf_field('restoration_stat_3_text', 'Community-led'); ?></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Team Section - Exact mirror of Next.js TeamSection.tsx -->
    <?php
    $admin_team = get_option('omt_admin_team');
    $team_members = !empty($admin_team['members']) ? $admin_team['members'] : [
        ['name' => 'Elena Morales', 'role' => 'Head of Ecology & Native Forestry', 'experience' => '15+ yrs restoration', 'highlight' => '15+ yrs restoration'],
        ['name' => 'David Gomez', 'role' => 'Operations & Community Stewardship', 'experience' => 'Camino route lead', 'highlight' => 'Camino route lead'],
        ['name' => 'Lucia Fernandez', 'role' => 'Field Research & Data Lead', 'experience' => 'Survival tracking', 'highlight' => 'Survival tracking'],
        ['name' => 'Carlos Ruiz', 'role' => 'Local Volunteer Coordinator', 'experience' => '500+ volunteers lead', 'highlight' => '500+ volunteers lead'],
    ];
    $team_stats = !empty($admin_team['stats']) ? $admin_team['stats'] : [
        ['value' => '100%', 'label' => 'Native species focus'],
        ['value' => '15+', 'label' => 'Community partners'],
        ['value' => '4', 'label' => 'Active Spanish regions'],
    ];
    $team_section_title = !empty($admin_team['sectionTitle']) ? $admin_team['sectionTitle'] : get_acf_field('team_section_badge', 'Our Team');
    $team_headline = !empty($admin_team['headline']) ? $admin_team['headline'] : get_acf_field('team_headline', 'People who make planting real');
    $team_description = !empty($admin_team['description']) ? $admin_team['description'] : get_acf_field('team_description', 'From field work to reporting, our team and partners keep restoration accountable and alive.');
    ?>
    <section id="team" class="relative min-h-[65vh] w-full py-20 sm:py-32 overflow-hidden gradient-bg">
      <div class="absolute inset-0 leaf-pattern opacity-20"></div>
      <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[560px] h-[560px] bg-forest-300/15 rounded-full blur-3xl"></div>

      <div class="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20">
        <div class="max-w-7xl mx-auto">
          <div class="text-center mb-14 sm:mb-20">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-forest-100 border border-forest-200 mb-6">
              <svg class="w-4 h-4 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
              <span class="text-xs sm:text-sm text-forest-700 font-semibold uppercase tracking-wider">
                <?php echo esc_html($team_section_title); ?>
              </span>
            </div>
            <h2 class="font-serif text-3xl sm:text-4xl md:text-5xl font-bold text-foreground mb-4 leading-tight">
              <?php echo esc_html($team_headline); ?>
            </h2>
            <p class="text-base sm:text-lg text-foreground/60 max-w-2xl mx-auto">
              <?php echo esc_html($team_description); ?>
            </p>
          </div>

          <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <?php foreach ($team_members as $m): ?>
            <div class="glass-card rounded-3xl p-6 sm:p-7 hover:bg-white transition-all shadow-soft">
              <div class="w-16 h-16 sm:w-18 sm:h-18 rounded-2xl bg-forest-100 flex items-center justify-center mb-4">
                <?php if (!empty($m['image'])): ?>
                  <img src="<?php echo esc_url($m['image']); ?>" alt="<?php echo esc_attr($m['name']); ?>" class="w-full h-full object-cover rounded-2xl">
                <?php else: ?>
                  <svg class="w-6 h-6 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                  </svg>
                <?php endif; ?>
              </div>
              <div class="font-serif text-xl font-bold text-foreground mb-1"><?php echo esc_html($m['name']); ?></div>
              <div class="text-sm text-foreground/60"><?php echo esc_html($m['role']); ?></div>
              <?php if (!empty($m['highlight']) || !empty($m['experience'])): ?>
              <div class="mt-4 flex items-center gap-2 text-xs text-foreground/50">
                <svg class="w-4 h-4 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <?php echo esc_html(!empty($m['highlight']) ? $m['highlight'] : $m['experience']); ?>
              </div>
              <?php endif; ?>
            </div>
            <?php endforeach; ?>
          </div>

          <div class="mt-10 grid md:grid-cols-3 gap-4 sm:gap-6">
            <?php foreach ($team_stats as $s): ?>
            <div class="glass-card rounded-3xl p-6 text-center shadow-soft">
              <div class="font-serif text-3xl font-bold text-forest-600 mb-2"><?php echo esc_html($s['value']); ?></div>
              <div class="text-sm text-foreground/60"><?php echo esc_html($s['label']); ?></div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </section>

    <!-- Testimonials Section - Exact mirror of Next.js TestimonialsSection.tsx -->
    <section id="testimonials" class="relative min-h-[65vh] w-full py-20 sm:py-32 overflow-hidden gradient-bg">
      <div class="absolute inset-0 leaf-pattern opacity-20"></div>
      <div class="absolute bottom-0 right-0 w-[620px] h-[620px] bg-forest-300/15 rounded-full blur-3xl"></div>

      <div class="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20">
        <div class="max-w-7xl mx-auto">
          <div class="text-center mb-14 sm:mb-20">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-forest-100 border border-forest-200 mb-6">
              <svg class="w-4 h-4 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
              </svg>
              <span class="text-xs sm:text-sm text-forest-700 font-semibold uppercase tracking-wider">
                Testimonials
              </span>
            </div>
            <h2 class="font-serif text-3xl sm:text-4xl md:text-5xl font-bold text-foreground mb-4 leading-tight">
              <?php echo get_acf_field('testimonials_title', 'Stories from the field'); ?>
            </h2>
            <p class="text-base sm:text-lg text-foreground/60 max-w-2xl mx-auto">
              <?php echo get_acf_field('testimonials_description', 'Restoration is more than planting. It is people, knowledge, and long-term care.'); ?>
            </p>
          </div>

          <div class="grid md:grid-cols-3 gap-4 sm:gap-6">
            <!-- Testimonial 1 -->
            <div class="glass-card rounded-3xl p-6 sm:p-7 hover:bg-white transition-all shadow-soft">
              <div class="w-12 h-12 rounded-2xl bg-forest-100 flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
              </div>
              <div class="text-foreground/70 leading-relaxed mb-4">"<?php echo get_acf_field('testimonial_1_quote', 'The trees are growing and our community has a long-term project we can truly be proud of.'); ?>"</div>
              <div class="font-serif text-lg font-bold text-foreground mb-1"><?php echo get_acf_field('testimonial_1_name', 'Miriam, Community Lead'); ?></div>
              <div class="text-sm text-foreground/50"><?php echo get_acf_field('testimonial_1_meta', 'Savannah conservation'); ?></div>
            </div>

            <!-- Testimonial 2 -->
            <div class="glass-card rounded-3xl p-6 sm:p-7 hover:bg-white transition-all shadow-soft">
              <div class="w-12 h-12 rounded-2xl bg-forest-100 flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                </svg>
              </div>
              <div class="text-foreground/70 leading-relaxed mb-4">"<?php echo get_acf_field('testimonial_2_quote', 'Their monitoring helped us understand what worked, so we can improve survival rates every season.'); ?>"</div>
              <div class="font-serif text-lg font-bold text-foreground mb-1"><?php echo get_acf_field('testimonial_2_name', 'Dr. Chen, Research Partner'); ?></div>
              <div class="text-sm text-foreground/50"><?php echo get_acf_field('testimonial_2_meta', 'Forest restoration'); ?></div>
            </div>

            <!-- Testimonial 3 -->
            <div class="glass-card rounded-3xl p-6 sm:p-7 hover:bg-white transition-all shadow-soft">
              <div class="w-12 h-12 rounded-2xl bg-forest-100 flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                </svg>
              </div>
              <div class="text-foreground/70 leading-relaxed mb-4">"<?php echo get_acf_field('testimonial_3_quote', 'Transparent reporting and local ownership turned restoration into something that lasts.'); ?>"</div>
              <div class="font-serif text-lg font-bold text-foreground mb-1"><?php echo get_acf_field('testimonial_3_name', 'Ayo, Field Coordinator'); ?></div>
              <div class="text-sm text-foreground/50"><?php echo get_acf_field('testimonial_3_meta', 'Urban greening'); ?></div>
            </div>
          </div>

          <div class="mt-10 text-center">
            <div class="inline-flex items-center gap-3 px-5 py-4 rounded-3xl glass-card shadow-medium">
              <span class="text-sm text-foreground/60">Want to share your story?</span>
              <a
                href="<?php echo esc_url(home_url('/get-involved')); ?>"
                class="text-sm font-semibold text-forest-600 hover:text-forest-700 transition-colors"
              >
                Join the movement
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
</div>

<?php
get_footer();

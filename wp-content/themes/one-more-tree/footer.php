<?php
$current_year = date('Y');

// Pull from unified admin options (same store as wp-admin + /administrator)
$omt_social    = get_option('omt_admin_social', []);
$omt_footer    = get_option('omt_admin_footer', []);
$omt_contact   = [
    'email'   => get_option('omt_contact_email',   'info@onemoretree.eu'),
    'phone'   => get_option('omt_contact_phone',   '+34 912 345 678'),
    'address' => get_option('omt_contact_address', 'Calle Gran Vía 28, Madrid, Spain'),
];

$social_links = array_filter([
    'instagram' => $omt_social['instagram'] ?? get_option('omt_instagram_url', ''),
    'twitter'   => $omt_social['twitter']   ?? get_option('omt_twitter_url', ''),
    'facebook'  => $omt_social['facebook']  ?? get_option('omt_facebook_url', ''),
    'linkedin'  => $omt_social['linkedin']  ?? get_option('omt_linkedin_url', ''),
]);

$footer_tagline   = !empty($omt_footer['tagline'])   ? $omt_footer['tagline']   : 'Restoring forests, one tree at a time.';
$footer_copyright = !empty($omt_footer['copyright']) ? $omt_footer['copyright'] : "© {$current_year} OneMoreTree. All rights reserved.";
$footer_policies  = !empty($omt_footer['policy_links']) ? $omt_footer['policy_links'] : [
    ['label' => 'Privacy Policy',  'url' => '#'],
    ['label' => 'Terms of Service','url' => '#'],
    ['label' => 'Cookie Policy',   'url' => '#'],
];

$newsletter_enabled = get_option('omt_newsletter_enabled', '1') === '1' || get_option('omt_newsletter_enabled', true) === true;
?>
    </div><!-- #content -->

    <!-- Footer - Exact mirror of Next.js Footer.tsx -->
    <footer class="relative w-full py-16 sm:py-20 overflow-hidden border-t border-earth-200 bg-gradient-to-b from-background to-earth-50">
      <div class="absolute inset-0 leaf-pattern opacity-10 pointer-events-none"></div>
      <div class="absolute bottom-0 left-0 right-0 h-1/2 bg-gradient-to-t from-forest-100/30 to-transparent pointer-events-none"></div>

      <div class="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20">
        <div class="mx-auto max-w-7xl w-full overflow-hidden">
          <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-8 lg:gap-12 mb-12 sm:mb-16">
            <!-- Brand -->
            <div class="col-span-2 md:col-span-4 lg:col-span-1">
              <a href="<?php echo esc_url(home_url('/')); ?>" class="flex items-center gap-2 mb-4">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo.png" alt="OneMoreTree" class="h-8 w-auto sm:h-10" />
                <span class="font-serif text-xl font-bold text-foreground">OneMore<span class="text-forest-600">Tree</span></span>
              </a>
              <p class="text-sm text-foreground/60 mb-6"><?php echo esc_html($footer_tagline); ?></p>
              <?php if (!empty($social_links)): ?>
                <div class="flex gap-3">
                  <?php foreach ($social_links as $platform => $url): ?>
                    <a href="<?php echo esc_url($url); ?>" class="w-10 h-10 rounded-lg bg-white hover:bg-forest-50 border border-earth-200 flex items-center justify-center">
                      <?php if ($platform === 'instagram'): ?>
                        <svg class="w-4 h-4 text-foreground/50" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                      <?php elseif ($platform === 'twitter'): ?>
                        <svg class="w-4 h-4 text-foreground/50" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                      <?php elseif ($platform === 'facebook'): ?>
                        <svg class="w-4 h-4 text-foreground/50" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                      <?php elseif ($platform === 'linkedin'): ?>
                        <svg class="w-4 h-4 text-foreground/50" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                      <?php endif; ?>
                    </a>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
            <!-- Organization -->
            <div>
              <h4 class="text-sm font-semibold text-foreground mb-4 uppercase tracking-wider">Organization</h4>
              <ul class="space-y-3">
                <li><a href="<?php echo esc_url(home_url('/about')); ?>" class="text-sm text-foreground/60 hover:text-forest-600">About Us</a></li>
                <li><a href="<?php echo esc_url(home_url('/about#team')); ?>" class="text-sm text-foreground/60 hover:text-forest-600">Our Team</a></li>
                <li><a href="<?php echo esc_url(home_url('/impact')); ?>" class="text-sm text-foreground/60 hover:text-forest-600">Impact</a></li>
                <li><a href="<?php echo esc_url(home_url('/about')); ?>" class="text-sm text-foreground/60 hover:text-forest-600">Press</a></li>
              </ul>
            </div>
            <!-- Programs -->
            <div>
              <h4 class="text-sm font-semibold text-foreground mb-4 uppercase tracking-wider">Programs</h4>
              <ul class="space-y-3">
                <li><a href="<?php echo esc_url(home_url('/projects')); ?>" class="text-sm text-foreground/60 hover:text-forest-600">Reforestation</a></li>
                <li><a href="<?php echo esc_url(home_url('/projects#interactive-spain-map')); ?>" class="text-sm text-foreground/60 hover:text-forest-600">Live Map</a></li>
                <li><a href="<?php echo esc_url(home_url('/faq')); ?>" class="text-sm text-foreground/60 hover:text-forest-600">Education</a></li>
                <li><a href="<?php echo esc_url(home_url('/impact')); ?>" class="text-sm text-foreground/60 hover:text-forest-600">Research</a></li>
              </ul>
            </div>
            <!-- Get Involved -->
            <div>
              <h4 class="text-sm font-semibold text-foreground mb-4 uppercase tracking-wider">Get Involved</h4>
              <ul class="space-y-3">
                <li><a href="<?php echo esc_url(home_url('/donate')); ?>" class="text-sm text-foreground/60 hover:text-forest-600">Donate</a></li>
                <li><a href="<?php echo esc_url(home_url('/volunteer')); ?>" class="text-sm text-foreground/60 hover:text-forest-600">Volunteer</a></li>
                <li><a href="<?php echo esc_url(home_url('/contact')); ?>" class="text-sm text-foreground/60 hover:text-forest-600">Partner</a></li>
                <li><a href="<?php echo esc_url(home_url('/get-involved')); ?>" class="text-sm text-foreground/60 hover:text-forest-600">Get Involved</a></li>
              </ul>
            </div>
            <!-- Resources -->
            <div>
              <h4 class="text-sm font-semibold text-foreground mb-4 uppercase tracking-wider">Resources</h4>
              <ul class="space-y-3">
                <li><a href="<?php echo esc_url(home_url('/blog')); ?>" class="text-sm text-foreground/60 hover:text-forest-600">Blog</a></li>
                <li><a href="<?php echo esc_url(home_url('/contact')); ?>" class="text-sm text-foreground/60 hover:text-forest-600">Contact</a></li>
                <li><a href="<?php echo esc_url(home_url('/volunteer')); ?>" class="text-sm text-foreground/60 hover:text-forest-600">Volunteer</a></li>
              </ul>
            </div>
            <!-- Contact -->
            <div>
              <h4 class="text-sm font-semibold text-foreground mb-4 uppercase tracking-wider">Contact</h4>
              <ul class="space-y-3">
                <li class="flex items-start gap-3">
                  <svg class="w-4 h-4 text-forest-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                  <a href="mailto:<?php echo esc_attr($omt_contact['email']); ?>" class="text-sm text-foreground/60 hover:text-forest-600"><?php echo esc_html($omt_contact['email']); ?></a>
                </li>
                <?php if (!empty($omt_contact['phone'])): ?>
                <li class="flex items-start gap-3">
                  <svg class="w-4 h-4 text-forest-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                  <span class="text-sm text-foreground/60"><?php echo esc_html($omt_contact['phone']); ?></span>
                </li>
                <?php endif; ?>
              </ul>
            </div>
          </div>

          <!-- Newsletter Section in Footer -->
          <?php if ($newsletter_enabled): ?>
          <div class="glass-card rounded-2xl p-6 sm:p-8 mb-12 shadow-medium">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
              <div>
                <h4 class="font-serif text-xl font-bold text-foreground mb-1">Stay in the loop</h4>
                <p class="text-sm text-foreground/60">Get updates on our planting projects and impact reports.</p>
              </div>
              <form id="footer-newsletter-form" class="flex gap-3 flex-col sm:flex-row">
                <input
                  type="email"
                  name="email"
                  required
                  placeholder="Enter your email"
                  class="px-4 py-3 rounded-xl bg-white text-foreground placeholder-foreground/40 border border-earth-200 focus:border-forest-500 focus:outline-none focus:ring-2 focus:ring-forest-200 transition-colors w-full sm:w-64 shadow-soft"
                />
                <button
                  type="submit"
                  class="px-6 py-3 bg-forest-600 hover:bg-forest-700 text-white font-medium rounded-xl transition-all duration-300 shadow-medium hover:shadow-elevated whitespace-nowrap"
                >
                  Subscribe
                </button>
              </form>
            </div>
            <div id="footer-newsletter-status" class="mt-3 text-sm text-foreground/70 hidden"></div>
          </div>
          <?php endif; ?>

          <!-- Bottom Row -->
          <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-8 border-t border-earth-200 text-center">
            <p class="text-sm text-foreground/50">
              <?php echo esc_html($footer_copyright); ?>
            </p>
            <div class="flex gap-6 flex-wrap justify-center">
              <?php foreach ($footer_policies as $policy): ?>
                <a href="<?php echo esc_url($policy['url'] ?? '#'); ?>" class="text-sm text-foreground/50 hover:text-forest-600 transition-colors">
                  <?php echo esc_html($policy['label'] ?? ''); ?>
                </a>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
    </footer>
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>

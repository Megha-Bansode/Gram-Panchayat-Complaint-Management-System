    <!-- ═══════════════════════════════════════════════════════════
     FOOTER
═══════════════════════════════════════════════════════════ -->
    <footer id="footer" class="pt-5 pb-2">
        <div class="container">
            <div class="row g-4">

                <!-- Column 1: Brand -->
                <div class="col-lg-3 col-md-6">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="brand-logo-circle"
                            style="width:44px;height:44px;border-color:rgba(212,175,55,0.3);">
                            <img src="<?php echo $base_path; ?>assets/images/panchayat_logo.png?v=<?php echo time(); ?>" alt="GPCMS Logo" onerror="this.onerror=null; this.src='https://cdn-icons-png.flaticon.com/512/3209/3209994.png';">
                        </div>
                        <div>
                            <div style="font-family:'Playfair Display',serif;font-size:1rem;font-weight:700;color:#fff;">
                                <?php echo __('brand_name'); ?></div>
                            <div style="font-size:0.65rem;color:rgba(255,255,255,0.5);"><?php echo __('brand_sub'); ?></div>
                        </div>
                    </div>
                    <p class="footer-contact-item"
                        style="color:rgba(255,255,255,0.55);font-size:0.82rem;line-height:1.7;">
                        <?php echo __('footer_desc'); ?>
                    </p>
                    <div class="d-flex gap-2 mt-3">
                        <a href="#" class="social-icon" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" class="social-icon" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" class="social-icon" aria-label="Twitter"><i class="fa-brands fa-x-twitter"></i></a>
                        <a href="#" class="social-icon" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
                    </div>
                </div>

                <!-- Column 2: Quick Links -->
                <div class="col-lg-2 col-md-6 col-6">
                    <h5 class="footer-title"><?php echo __('footer_links'); ?></h5>
                    <ul class="list-unstyled d-flex flex-column gap-2">
                        <li><a href="<?php echo $base_path; ?>index.php#home"><?php echo __('nav_home'); ?></a></li>
                        <li><a href="<?php echo $base_path; ?>index.php#quick-services"><?php echo __('nav_services'); ?></a></li>
                        <li><a href="<?php echo $base_path; ?>index.php#schemes"><?php echo __('nav_schemes'); ?></a></li>
                        <li><a href="<?php echo $base_path; ?>index.php#gallery"><?php echo __('nav_gallery'); ?></a></li>
                        <li><a href="<?php echo $base_path; ?>index.php#about"><?php echo __('nav_about'); ?></a></li>
                    </ul>
                </div>

                <!-- Column 3: Help -->
                <div class="col-lg-2 col-md-6 col-6">
                    <h5 class="footer-title"><?php echo __('footer_help'); ?></h5>
                    <ul class="list-unstyled d-flex flex-column gap-2">
                        <li><a href="<?php echo $base_path; ?>index.php#faq">FAQ</a></li>
                        <li><a href="#"><?php echo __('privacy_policy'); ?></a></li>
                        <li><a href="#"><?php echo __('terms'); ?></a></li>
                        <li><a href="<?php echo $base_path; ?>index.php#contact"><?php echo __('nav_contact'); ?></a></li>
                        <li><a href="<?php echo $base_path; ?>login/citizen_login.php"><?php echo __('citizen_login_header'); ?></a></li>
                    </ul>
                </div>

                <!-- Column 4: Contact -->
                <div class="col-lg-3 col-md-6">
                    <h5 class="footer-title"><?php echo __('footer_contact_details'); ?></h5>
                    <div class="d-flex flex-column gap-2">
                        <div class="footer-contact-item"><i class="fa-solid fa-map-location-dot"></i><span><?php echo __('address_val'); ?></span></div>
                        <div class="footer-contact-item"><i class="fa-solid fa-phone"></i><span>+91 98765 43210</span></div>
                        <div class="footer-contact-item"><i class="fa-solid fa-envelope"></i><span>office@grampanchayat.gov.in</span></div>
                        <div class="footer-contact-item"><i class="fa-solid fa-globe"></i><span>www.gpcms.gov.in</span></div>
                    </div>
                </div>

                <!-- Column 5: Emergency Contacts -->
                <div class="col-lg-2 col-md-6 col-6">
                    <h5 class="footer-title"><?php echo __('footer_emergency_title'); ?></h5>
                    <ul class="list-unstyled d-flex flex-column gap-2"
                        style="font-size:0.84rem; color:rgba(255,255,255,0.6);">
                        <li class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-shield-heart" style="color:var(--accent); font-size:0.85rem;"></i>
                            <span><?php echo __('footer_police'); ?>: <strong>100</strong></span>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-truck-medical" style="color:var(--accent); font-size:0.85rem;"></i>
                            <span><?php echo __('footer_ambulance'); ?>: <strong>108</strong></span>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-fire-extinguisher" style="color:var(--accent); font-size:0.85rem;"></i>
                            <span><?php echo __('footer_fire'); ?>: <strong>101</strong></span>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-person-dress" style="color:var(--accent); font-size:0.85rem;"></i>
                            <span><?php echo __('footer_helpline'); ?>: <strong>1091</strong></span>
                        </li>
                    </ul>
                </div>

            </div>

            <hr class="footer-divider">

            <!-- Bottom Bar -->
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 footer-bottom pb-2">
                <span><?php echo __('copyright'); ?></span>
                <div class="d-flex gap-3">
                    <a href="#"><?php echo __('privacy_policy'); ?></a>
                    <a href="#"><?php echo __('terms'); ?></a>
                    <a href="#"><?php echo __('disclaimer'); ?></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script src="<?php echo $base_path; ?>js/landing.js"></script>
</body>

</html>

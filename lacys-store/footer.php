<footer class="ft"><div class="wrap">
<div class="ft-g">
<div><h4>Lacy’s Store</h4><p>Timeless women’s fashion in white and red — designed to make every day feel a little more elegant.</p>
<?php if (isset($_GET['subscribed'])) echo '<p><b>Thanks for subscribing!</b></p>'; ?>
<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>"><input type="hidden" name="action" value="lacys_form"><?php wp_nonce_field('lacys_form'); ?><input type="hidden" name="t" value="news"><input type="hidden" name="n" value="Newsletter"><input type="hidden" name="m" value="Please add me to the newsletter."><input type="email" name="e" placeholder="Your email" required><button class="btn">Join</button></form>
<div class="social"><?php echo lacys_social(); ?></div></div>
<div><h4>Shop</h4><ul><li><a href="<?php echo esc_url(lacys_url('shop')); ?>">All Products</a></li><li><a href="<?php echo esc_url(home_url('/lookbook/')); ?>">Lookbook</a></li><li><a href="<?php echo esc_url(lacys_url('cart')); ?>">Bag</a></li><li><a href="<?php echo esc_url(lacys_url('myaccount')); ?>">My Account</a></li></ul></div>
<div><h4>Help</h4><ul><li><a href="<?php echo esc_url(home_url('/faq-shipping/')); ?>">FAQ &amp; Shipping</a></li><li><a href="<?php echo esc_url(home_url('/about/')); ?>">About Us</a></li><li><a href="<?php echo esc_url(home_url('/contact/')); ?>">Contact</a></li></ul></div>
<div><h4>Visit Us</h4><p><?php echo esc_html(lacys_opt('lacys_address')); ?><br><?php echo esc_html(lacys_opt('lacys_phone')); ?><br><?php echo esc_html(get_option('admin_email')); ?><br>Mon–Sat, 10am–8pm</p></div>
</div>
<div class="ft-bot"><span>© <?php echo date('Y'); ?> Lacy’s Store, Los Angeles. All rights reserved.</span>
<span><a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>">Privacy Policy</a> &nbsp;·&nbsp; <a href="<?php echo esc_url(home_url('/terms-and-conditions/')); ?>">Terms &amp; Conditions</a> &nbsp;·&nbsp; <a href="<?php echo esc_url(home_url('/sitemap/')); ?>">Sitemap</a></span></div>
</div></footer>
<?php wp_footer(); ?></body></html>

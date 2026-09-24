<?php
defined('ABSPATH') || exit;

function lacys_url($k){ return function_exists('wc_get_page_permalink') ? wc_get_page_permalink($k) : home_url("/$k/"); }

add_action('after_setup_theme', function () {
	foreach (['title-tag','post-thumbnails','html5','custom-logo','woocommerce','wc-product-gallery-zoom','wc-product-gallery-lightbox','wc-product-gallery-slider'] as $f) add_theme_support($f);
	register_nav_menu('primary', 'Primary Menu');
});

add_action('wp_enqueue_scripts', function () {
	wp_enqueue_style('lacys-fonts', 'https://fonts.googleapis.com/css2?family=Jost:wght@400;500&family=Playfair+Display:ital,wght@0,500;0,600;1,600&display=swap', [], null);
	wp_enqueue_style('lacys', get_stylesheet_uri(), [], wp_get_theme()->get('Version'));
	wp_enqueue_script('lacys', get_template_directory_uri() . '/main.js', [], '1.0', true);
}, 20);

add_filter('loop_shop_columns', fn() => 4);
add_filter('loop_shop_per_page', fn() => 12);
add_filter('woocommerce_add_to_cart_fragments', function ($f) {
	$f['b.cart-n'] = '<b class="cart-n">' . WC()->cart->get_cart_contents_count() . '</b>';
	return $f;
});

add_action('admin_notices', function () {
	if (!class_exists('WooCommerce')) echo '<div class="notice notice-warning"><p>Lacy’s Store theme needs the <b>WooCommerce</b> plugin. Please install and activate it.</p></div>';
});

/* Contact / newsletter form */
add_shortcode('lacys_contact', function () {
	ob_start();
	if (isset($_GET['sent'])) echo '<p class="notice">Thank you — we’ll be in touch shortly.</p>';
	if (isset($_GET['failed'])) echo '<p class="notice">Sorry, your message could not be sent. Please check your email address and try again.</p>'; ?>
	<form class="lc-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
		<input type="hidden" name="action" value="lacys_form"><?php wp_nonce_field('lacys_form'); ?>
		<input name="hp" tabindex="-1" autocomplete="off" style="display:none"><input name="n" placeholder="Your name" required><input type="email" name="e" placeholder="Email address" required>
		<textarea name="m" rows="5" placeholder="How can we help?" required></textarea><button class="btn">Send message</button>
	</form><?php
	return ob_get_clean();
});
foreach (['admin_post_lacys_form', 'admin_post_nopriv_lacys_form'] as $h) add_action($h, function () {
	check_admin_referer('lacys_form');
	$back = remove_query_arg(['sent', 'failed', 'subscribed'], wp_get_referer() ?: home_url('/'));
	if (!empty($_POST['hp'])) { wp_safe_redirect($back); exit; }
	$n = sanitize_text_field($_POST['n'] ?? ''); $e = sanitize_email($_POST['e'] ?? '');
	$ok = is_email($e) && wp_mail(get_option('admin_email'), 'Lacy’s Store message from ' . $n, sanitize_textarea_field($_POST['m'] ?? '') . "\n\nFrom: $n <$e>", ['Reply-To: ' . $e]);
	$key = $ok ? (($_POST['t'] ?? '') === 'news' ? 'subscribed' : 'sent') : 'failed';
	wp_safe_redirect(add_query_arg($key, 1, $back)); exit;
});

/* One-time setup: pages, menu, sample products */
add_action('admin_init', function () {
	if (!get_option('lacys_setup')) {
		$pages = [
			'home' => ['Home', ''],
			'about' => ['About Us', '<div class="two"><div><h2>Elegance, made personal</h2><p>Lacy’s Store began with a simple idea: women’s fashion should feel effortless, look refined and be within reach. Every piece is chosen for its fabric, fit and finish.</p><p>Our signature palette of crisp white and bold red reflects who we are — classic, confident and a little daring.</p></div><div class="tile">Our Story</div></div><div class="grid3"><div class="card"><h3>Quality first</h3><p>Premium fabrics and careful tailoring.</p></div><div class="card"><h3>Fair pricing</h3><p>Elevated style without the markup.</p></div><div class="card"><h3>Care in every parcel</h3><p>Beautifully packed and quickly shipped.</p></div></div>'],
			'lookbook' => ['Lookbook', '<div class="grid3"><div class="tile">Spring Soirée</div><div class="tile">The Red Edit</div><div class="tile">Weekend Whites</div><div class="tile">Office Chic</div><div class="tile">Evening Glow</div><div class="tile">Layered Looks</div></div>'],
			'faq-shipping' => ['FAQ & Shipping', '<details open><summary>How long does delivery take?</summary><p>Standard delivery takes 3–6 working days. Express options are available at checkout.</p></details><details><summary>Is shipping free?</summary><p>Yes, on all orders over $75.</p></details><details><summary>What is your returns policy?</summary><p>Return unworn items with tags within 30 days for a refund or exchange.</p></details><details><summary>How do I find my size?</summary><p>Check the measurements in each product description, or contact us and we’ll help you choose.</p></details><details><summary>Which payment methods do you accept?</summary><p>Major cards and any gateways enabled in WooCommerce.</p></details>'],
			'contact' => ['Contact', '<div class="two"><div><h2>Say hello</h2><p>Questions about sizing, orders or styling? Send us a message and we’ll reply within one working day.</p>[lacys_info]</div><div>[lacys_contact]</div></div>'],
		];
		foreach ($pages as $slug => $p) if (!get_page_by_path($slug)) {
			$id = wp_insert_post(['post_type'=>'page','post_status'=>'publish','post_name'=>$slug,'post_title'=>$p[0],'post_content'=>$p[1]]);
			if ($slug === 'home') { update_option('show_on_front', 'page'); update_option('page_on_front', $id); }
		}
		$mid = wp_create_nav_menu('Primary');
		if (!is_wp_error($mid)) {
			foreach ([['Home', home_url('/')], ['Shop', lacys_url('shop')], ['Lookbook', home_url('/lookbook/')], ['About', home_url('/about/')], ['FAQ', home_url('/faq-shipping/')], ['Contact', home_url('/contact/')]] as $i)
				wp_update_nav_menu_item($mid, 0, ['menu-item-title'=>$i[0],'menu-item-url'=>$i[1],'menu-item-status'=>'publish']);
			set_theme_mod('nav_menu_locations', ['primary' => $mid]);
		}
		global $wp_rewrite; $wp_rewrite->set_permalink_structure('/%postname%/'); flush_rewrite_rules();
		update_option('lacys_setup', 1);
	}
	if (!get_option('lacys_products') && class_exists('WC_Product_Simple') && taxonomy_exists('product_cat')) {
		$items = ['Rosé Wrap Dress|Dresses|89|69','Scarlet Satin Slip Dress|Dresses|110|','Ivory Silk Blouse|Tops|64|','Pleated Midi Skirt|Skirts|58|46','Crimson Tailored Blazer|Outerwear|135|','Classic Trench Coat|Outerwear|165|','Lace Trim Camisole|Tops|39|','Pearl Drop Earrings|Accessories|28|'];
		foreach ($items as $row) {
			[$name, $cat, $price, $sale] = explode('|', $row);
			$t = term_exists($cat, 'product_cat') ?: wp_insert_term($cat, 'product_cat');
			$p = new WC_Product_Simple();
			$p->set_name($name); $p->set_regular_price($price); if ($sale) $p->set_sale_price($sale);
			$p->set_short_description('Elegant, comfortable and designed to last.'); $p->set_description('A Lacy’s Store signature piece in premium fabric. Edit this product to add photos, sizes and details.');
			if (!is_wp_error($t)) $p->set_category_ids([(int) $t['term_id']]);
			$p->set_status('publish'); $p->save();
		}
		update_option('lacys_products', 1);
	}
});

/* One-time WooCommerce configuration so account, cart and checkout work out of the box */
add_action('admin_init', function () {
	if (get_option('lacys_wc') || !class_exists('WooCommerce') || !function_exists('wc_get_page_id')) return;
	// Accounts: login + register on My Account, guest checkout, customer picks own password (no email needed)
	foreach (['woocommerce_enable_myaccount_registration'=>'yes','woocommerce_enable_guest_checkout'=>'yes','woocommerce_enable_checkout_login_reminder'=>'yes','woocommerce_enable_signup_and_login_from_checkout'=>'yes','woocommerce_registration_generate_password'=>'no'] as $k => $v) update_option($k, $v);
	// Classic shortcode Cart/Checkout: compatible with every payment gateway plugin
	foreach (['cart'=>'[woocommerce_cart]','checkout'=>'[woocommerce_checkout]'] as $k => $sc) {
		$id = wc_get_page_id($k); $post = $id > 0 ? get_post($id) : null;
		if ($post && strpos($post->post_content, 'wp:woocommerce') !== false) wp_update_post(['ID'=>$id,'post_content'=>$sc]);
	}
	// Placeholder payment method so test orders can complete. Add Stripe/PayPal/etc. under WooCommerce > Settings > Payments.
	update_option('woocommerce_cod_settings', ['enabled'=>'yes','title'=>'Cash on delivery','description'=>'Pay when your order arrives.']);
	// Shipping: flat rate, free over $75 (zone 0 = all locations)
	$z = new WC_Shipping_Zone(0);
	if (!$z->get_shipping_methods()) {
		$a = $z->add_shipping_method('flat_rate'); update_option("woocommerce_flat_rate_{$a}_settings", ['title'=>'Standard shipping','tax_status'=>'none','cost'=>'5']);
		$b = $z->add_shipping_method('free_shipping'); update_option("woocommerce_free_shipping_{$b}_settings", ['title'=>'Free shipping','requires'=>'min_amount','min_amount'=>'75']);
	}
	update_option('lacys_wc', 1);
});
// Hide flat rate whenever free shipping is available
add_filter('woocommerce_package_rates', function ($r) {
	foreach ($r as $v) if ($v->method_id === 'free_shipping') return array_filter($r, fn($x) => $x->method_id === 'free_shipping');
	return $r;
});

/* Store details (editable in Appearance > Customize > Store Details) */
function lacys_defaults(){ return ['lacys_facebook'=>'https://www.facebook.com/','lacys_instagram'=>'https://www.instagram.com/','lacys_address'=>'123 Melrose Ave, Los Angeles, CA 90046','lacys_phone'=>'(323) 555-0123']; }
function lacys_opt($k){ $d = lacys_defaults(); return get_theme_mod($k, $d[$k] ?? ''); }
add_action('customize_register', function ($c) {
	$c->add_section('lacys', ['title'=>'Store Details','priority'=>30]);
	foreach (['lacys_facebook'=>'Facebook page URL','lacys_instagram'=>'Instagram profile URL','lacys_address'=>'Store address (Los Angeles)','lacys_phone'=>'Store phone'] as $k => $l) {
		$c->add_setting($k, ['default'=>lacys_defaults()[$k],'sanitize_callback'=>in_array($k, ['lacys_facebook','lacys_instagram']) ? 'esc_url_raw' : 'sanitize_text_field']);
		$c->add_control($k, ['label'=>$l,'section'=>'lacys','type'=>'text']);
	}
});
function lacys_social(){
	$fb = '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M22 12a10 10 0 1 0-11.6 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.5h-1.3c-1.2 0-1.6.8-1.6 1.6V12h2.8l-.4 2.9h-2.3v7A10 10 0 0 0 22 12z"/></svg>';
	$ig = '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>';
	$o = '';
	if ($u = lacys_opt('lacys_facebook')) $o .= '<a href="' . esc_url($u) . '" target="_blank" rel="noopener" aria-label="Facebook">' . $fb . '</a>';
	if ($u = lacys_opt('lacys_instagram')) $o .= '<a href="' . esc_url($u) . '" target="_blank" rel="noopener" aria-label="Instagram">' . $ig . '</a>';
	return $o;
}
add_shortcode('lacys_info', function () {
	return '<ul class="lc-info"><li><b>Visit us</b><br>' . esc_html(lacys_opt('lacys_address')) . '</li><li><b>Call</b><br>' . esc_html(lacys_opt('lacys_phone')) . '</li><li><b>Email</b><br>' . esc_html(get_option('admin_email')) . '</li><li><b>Hours</b><br>Mon–Sat, 10am–8pm</li></ul><div class="social">' . lacys_social() . '</div>';
});

/* Sitemap page (human-readable). WordPress also serves /wp-sitemap.xml for search engines. */
add_shortcode('lacys_sitemap', function () {
	ob_start(); echo '<div class="grid3"><div class="card"><h3>Pages</h3><ul>'; wp_list_pages(['title_li'=>'']); echo '</ul></div>';
	if (taxonomy_exists('product_cat')) { echo '<div class="card"><h3>Shop categories</h3><ul>'; wp_list_categories(['taxonomy'=>'product_cat','title_li'=>'','hide_empty'=>0]); echo '</ul></div>';
		echo '<div class="card"><h3>Products</h3><ul>'; foreach (get_posts(['post_type'=>'product','numberposts'=>60,'post_status'=>'publish']) as $p) echo '<li><a href="' . esc_url(get_permalink($p)) . '">' . esc_html($p->post_title) . '</a></li>'; echo '</ul></div>'; }
	echo '</div><p><a href="' . esc_url(home_url('/wp-sitemap.xml')) . '">XML sitemap for search engines</a></p>';
	return ob_get_clean();
});

/* One-time: Privacy Policy, Terms & Conditions, Sitemap pages */
add_action('admin_init', function () {
	if (get_option('lacys_legal')) return;
	$d = date_i18n('F j, Y');
	$privacy = <<<'H'
<p>This Privacy Policy explains how Lacy’s Store (“we”, “us”), based in Los Angeles, California, collects, uses and protects your information when you visit our website or buy from us.</p>
<h2>Information we collect</h2>
<ul><li>Details you give us: name, email, phone number, billing and shipping address, and order history.</li><li>Payment details, which are handled directly by our payment providers. We do not store full card numbers.</li><li>Technical data such as IP address, browser type and pages visited, collected through cookies.</li></ul>
<h2>How we use your information</h2>
<p>To process and deliver orders, manage your account, provide customer support, send order updates, send marketing emails if you subscribe (you can unsubscribe at any time), improve our website, prevent fraud and meet legal obligations.</p>
<h2>Who we share it with</h2>
<p>Payment processors, shipping carriers, and hosting, email and analytics providers who work on our behalf, and authorities where the law requires. We do not sell your personal information.</p>
<h2>Cookies</h2>
<p>We use essential cookies to keep your bag, login and checkout working. You can block cookies in your browser settings, but parts of the site may stop working.</p>
<h2>Your rights</h2>
<p>You may ask to access, correct or delete your personal information. If you are a California resident, the CCPA/CPRA also gives you the right to know what we collect, to request deletion or correction, and to opt out of the sale or sharing of your data (we do not sell it). Contact us through our Contact page to make a request.</p>
<h2>Retention and security</h2>
<p>We keep information only as long as needed for the purposes above or as the law requires, and use reasonable safeguards to protect it. No online system is completely secure.</p>
<h2>Children</h2>
<p>Our website is not directed to children under 16, and we do not knowingly collect their information.</p>
<h2>Changes and contact</h2>
<p>We may update this policy from time to time; the date above shows the latest version. Questions? Please reach us via our Contact page.</p>
H;
	$terms = <<<'H'
<p>Welcome to Lacy’s Store. By using this website or placing an order, you agree to these Terms & Conditions.</p>
<h2>Using our website</h2>
<p>You agree to use the site lawfully and not to misuse it, interfere with its operation or attempt unauthorized access. If you create an account, keep your login details secure; you are responsible for activity on your account.</p>
<h2>Orders and pricing</h2>
<p>All prices are in US dollars and may change without notice. An order is accepted when we confirm it. We may refuse or cancel an order, for example because of stock issues, pricing errors or suspected fraud, and will refund any payment taken.</p>
<h2>Payment</h2>
<p>Payment is taken at checkout through our secure payment providers. You confirm you are authorized to use the payment method you choose.</p>
<h2>Shipping and delivery</h2>
<p>Delivery times and costs are shown at checkout and on our FAQ & Shipping page. Delivery dates are estimates, not guarantees.</p>
<h2>Returns and refunds</h2>
<p>You may return unworn items with their tags attached within 30 days of delivery for a refund or exchange. Items marked final sale cannot be returned. Refunds go to the original payment method.</p>
<h2>Intellectual property</h2>
<p>All content on this site, including text, images, logos and designs, belongs to Lacy’s Store or its licensors and may not be copied or used without permission.</p>
<h2>Limitation of liability</h2>
<p>To the fullest extent permitted by law, Lacy’s Store is not liable for indirect or consequential losses arising from your use of the site or products. Nothing here limits rights you have under law that cannot be excluded.</p>
<h2>Governing law</h2>
<p>These terms are governed by the laws of the State of California, and disputes will be handled by the courts of Los Angeles County.</p>
<h2>Changes and contact</h2>
<p>We may update these terms at any time; continued use of the site means you accept the changes. Questions? Please reach us via our Contact page.</p>
H;
	$pages = ['privacy-policy'=>['Privacy Policy', '<p><em>Last updated: ' . $d . '</em></p>' . $privacy], 'terms-and-conditions'=>['Terms & Conditions', '<p><em>Last updated: ' . $d . '</em></p>' . $terms], 'sitemap'=>['Sitemap', '[lacys_sitemap]']];
	$ids = [];
	foreach ($pages as $slug => $pg) {
		$ex = get_page_by_path($slug);
		$args = ['post_type'=>'page','post_status'=>'publish','post_name'=>$slug,'post_title'=>$pg[0],'post_content'=>$pg[1]];
		if ($ex && $ex->post_status === 'publish') $ids[$slug] = $ex->ID;
		elseif ($ex) $ids[$slug] = wp_update_post($args + ['ID'=>$ex->ID]);
		else $ids[$slug] = wp_insert_post($args);
	}
	update_option('wp_page_for_privacy_policy', $ids['privacy-policy']);
	update_option('woocommerce_terms_page_id', $ids['terms-and-conditions']);
	$c = get_page_by_path('contact');
	if ($c && strpos($c->post_content, '[lacys_info]') === false) wp_update_post(['ID'=>$c->ID,'post_content'=>str_replace('</p></div><div>[lacys_contact]', '</p>[lacys_info]</div><div>[lacys_contact]', $c->post_content)]);
	update_option('lacys_legal', 1);
});

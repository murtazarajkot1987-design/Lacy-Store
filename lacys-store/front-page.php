<?php get_header(); $shop = lacys_url('shop'); ?>
<section class="hero"><div class="wrap hero-in">
<div><p class="eyebrow">New Season Collection</p><h1>Effortless elegance, <em>made for her.</em></h1>
<p>Refined dresses, tailored tops and timeless pieces — designed to move with you from morning to midnight.</p>
<a class="btn" href="<?php echo esc_url($shop); ?>">Shop the collection</a> <a class="btn ghost" href="<?php echo esc_url(home_url('/lookbook/')); ?>">View lookbook</a></div>
<div class="hero-art" aria-hidden="true">Lacy’s</div></div></section>
<div class="wrap feats"><div><b>Free Shipping</b><span>On orders over $75</span></div><div><b>30-Day Returns</b><span>Hassle-free exchanges</span></div><div><b>Secure Checkout</b><span>Cards &amp; wallets</span></div><div><b>Made to Last</b><span>Quality fabrics</span></div></div>
<?php $cats = taxonomy_exists('product_cat') ? get_terms(['taxonomy'=>'product_cat','hide_empty'=>true,'exclude'=>[get_option('default_product_cat')]]) : []; if ($cats && !is_wp_error($cats)) : ?>
<section class="sec"><div class="wrap"><div class="sec-h"><h2>Shop by Category</h2><p>Find your next favourite piece</p></div><div class="cats">
<?php foreach ($cats as $c) : ?><a class="cat" href="<?php echo esc_url(get_term_link($c)); ?>"><?php echo esc_html($c->name); ?></a><?php endforeach; ?></div></div></section><?php endif; ?>
<section class="sec tint"><div class="wrap"><div class="sec-h"><h2>New Arrivals</h2><p>Fresh from the atelier</p></div><?php echo do_shortcode('[products limit="4" columns="4" orderby="date"]'); ?></div></section>
<section class="promo"><h2>The Red Edit — Up to 30% Off</h2><p>Statement pieces in signature scarlet. Limited time only.</p><a class="btn light" href="<?php echo esc_url($shop); ?>">Shop the sale</a></section>
<section class="sec"><div class="wrap"><div class="sec-h"><h2>Best Sellers</h2><p>Loved by our customers</p></div><?php echo do_shortcode('[products limit="4" columns="4" best_selling="true"]'); ?></div></section>
<section class="sec tint"><div class="wrap"><div class="sec-h"><h2>Kind Words</h2></div><div class="grid3">
<div class="quote"><p>“The fit is perfect and the red dress got compliments all night.”</p><span>Amara K.</span></div>
<div class="quote"><p>“Beautiful quality, fast delivery and gorgeous packaging.”</p><span>Sofia R.</span></div>
<div class="quote"><p>“My go-to store for elegant, easy pieces.”</p><span>Hannah L.</span></div></div></div></section>
<?php get_footer();

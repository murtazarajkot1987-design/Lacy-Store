<!doctype html>
<html <?php language_attributes(); ?>>
<head><meta charset="<?php bloginfo('charset'); ?>"><meta name="viewport" content="width=device-width,initial-scale=1"><?php wp_head(); ?></head>
<body <?php body_class(); ?>><?php wp_body_open(); ?>
<div class="bar">Free shipping on orders over $75 &nbsp;·&nbsp; Easy 30-day returns</div>
<header class="hd"><div class="wrap hd-in">
<button class="burger" aria-label="Menu" aria-expanded="false"><span></span></button>
<a class="logo" href="<?php echo esc_url(home_url('/')); ?>">Lacy’s<em>Store</em></a>
<nav class="nav" aria-label="Primary">
<?php wp_nav_menu(['theme_location'=>'primary','container'=>false,'fallback_cb'=>false]); ?>
<form role="search" action="<?php echo esc_url(home_url('/')); ?>"><input type="search" name="s" placeholder="Search dresses, tops…" aria-label="Search"><input type="hidden" name="post_type" value="product"></form>
</nav>
<div class="acts"><a class="acc" href="<?php echo esc_url(lacys_url('myaccount')); ?>">Account</a>
<a href="<?php echo esc_url(lacys_url('cart')); ?>">Bag<b class="cart-n"><?php echo function_exists('WC') && WC()->cart ? WC()->cart->get_cart_contents_count() : 0; ?></b></a></div>
</div></header>

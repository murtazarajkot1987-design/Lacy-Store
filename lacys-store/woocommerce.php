<?php get_header(); ?>
<main class="wrap pg"><?php if (function_exists('woocommerce_breadcrumb')) woocommerce_breadcrumb(); woocommerce_content(); ?></main>
<?php get_footer();

<?php get_header(); while (have_posts()) : the_post(); ?>
<section class="ph"><div class="wrap"><h1><?php the_title(); ?></h1></div></section>
<main class="wrap pg entry"><?php the_content(); ?></main>
<?php endwhile; get_footer();

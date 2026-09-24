<?php get_header(); ?>
<section class="ph"><div class="wrap"><h1><?php echo is_search() ? 'Search results' : 'Journal'; ?></h1></div></section>
<main class="wrap pg entry">
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<article><h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2><?php the_excerpt(); ?></article>
<?php endwhile; the_posts_pagination(); else : ?><p>Nothing found. <a href="<?php echo esc_url(lacys_url('shop')); ?>">Back to the shop</a></p><?php endif; ?>
</main>
<?php get_footer();

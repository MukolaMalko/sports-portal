<?php
/**
 * Single: News
 */
get_header();
$id  = get_the_ID();
$pub_date = get_post_meta($id, 'publication_date', true) ?: '';
?>
<main class="section">
  <div class="container" style="max-width:800px">
    <?php if (have_posts()): while (have_posts()): the_post(); ?>
      <article>
        <div style="margin-bottom:var(--space-4)">
          <a href="<?php echo get_post_type_archive_link('news'); ?>"
             style="color:var(--color-primary);text-decoration:none">
            ← Всі новини
          </a>
        </div>

        <?php if (has_post_thumbnail()): ?>
          <div style="margin-bottom:var(--space-6);border-radius:var(--radius-lg);overflow:hidden">
            <?php the_post_thumbnail('large', ['style'=>'width:100%;height:auto']); ?>
          </div>
        <?php endif; ?>

        <div class="card-meta" style="margin-bottom:var(--space-3)">
          <?php if ($pub_date): ?>
            <time><?php echo esc_html($pub_date); ?></time>
          <?php endif; ?>
        </div>

        <h1 style="font-size:var(--text-xl);margin-bottom:var(--space-6)">
          <?php the_title(); ?>
        </h1>

        <div style="line-height:1.8;color:var(--color-text)">
          <?php the_content(); ?>
        </div>
      </article>
    <?php endwhile; endif; ?>
  </div>
</main>
<?php get_footer(); ?>
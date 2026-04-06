<?php
/**
 * Archive: News
 */
get_header();

$paged = max(1, intval($_GET['paged'] ?? 1));
$query = new WP_Query([
    'post_type'      => 'news',
    'posts_per_page' => 9,
    'paged'          => $paged,
    'post_status'    => 'publish',
]);

$news = [];
if ($query->have_posts()) {
    while ($query->have_posts()) {
        $query->the_post();
        $id = get_the_ID();
        $news[] = [
            'id'       => $id,
            'title'    => get_the_title(),
            'link'     => get_permalink(),
            'thumb'    => get_the_post_thumbnail_url($id, 'medium') ?: '',
            'excerpt'  => wp_trim_words(get_the_excerpt(), 25),
            'pub_date' => get_post_meta($id, 'publication_date', true) ?: '',
        ];
    }
    wp_reset_postdata();
}
$total_pages = $query->max_num_pages;
?>

<main class="section">
  <div class="container">
    <h1 class="section-title">Новини</h1>
    <p class="section-sub">Будьте в курсі подій</p>

    <div class="cards-grid">
      <?php if (empty($news)): ?>
        <p style="color:var(--color-text-muted);padding:1rem">Новин немає</p>
      <?php else: ?>
        <?php foreach ($news as $n): ?>
          <article class="card">
            <?php if ($n['thumb']): ?>
              <div class="card-thumb">
                <img src="<?php echo esc_url($n['thumb']); ?>"
                     alt="<?php echo esc_attr($n['title']); ?>"
                     loading="lazy" width="400" height="225">
              </div>
            <?php endif; ?>
            <div class="card-body">
              <div class="card-meta">
                <?php if ($n['pub_date']): ?>
                  <time><?php echo esc_html($n['pub_date']); ?></time>
                <?php endif; ?>
              </div>
              <h3><?php echo esc_html($n['title']); ?></h3>
              <p><?php echo esc_html($n['excerpt']); ?></p>
              <a href="<?php echo esc_url($n['link']); ?>"
                 class="btn btn-primary"
                 style="font-size:.85rem;padding:.5rem 1rem">Читати</a>
            </div>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <?php if ($total_pages > 1): ?>
      <div style="display:flex;gap:var(--space-2);justify-content:center;margin-top:var(--space-8)">
        <?php for ($i = 1; $i <= $total_pages; $i++):
          $url = add_query_arg('paged', $i);
        ?>
          <a href="<?php echo esc_url($url); ?>"
             class="btn <?php echo $i === $paged ? 'btn-primary' : 'btn-outline'; ?>"
             style="border:1px solid var(--color-border)">
            <?php echo $i; ?>
          </a>
        <?php endfor; ?>
      </div>
    <?php endif; ?>
  </div>
</main>

<?php get_footer(); ?>
<?php
/**
 * Main Index Template — Homepage
 */
get_header();

$events_query = new WP_Query([
    'post_type'      => 'event',
    'posts_per_page' => 3,
    'post_status'    => 'publish',
]);

$news_query = new WP_Query([
    'post_type'      => 'news',
    'posts_per_page' => 3,
    'post_status'    => 'publish',
]);

$events = [];
if ($events_query->have_posts()) {
    while ($events_query->have_posts()) {
        $events_query->the_post();
        $id = get_the_ID();
        $events[] = [
            'id'    => $id,
            'title' => get_the_title(),
            'link'  => get_permalink(),
            'thumb' => get_the_post_thumbnail_url($id, 'medium') ?: '',
            'acf'   => [
                'event_date'     => get_post_meta($id, 'event_date', true) ?: '',
                'event_location' => get_post_meta($id, 'event_location', true) ?: '',
                'sport_type'     => get_post_meta($id, 'sport_type', true) ?: '',
                'event_status'   => get_post_meta($id, 'event_status', true) ?: 'active',
            ],
        ];
    }
    wp_reset_postdata();
}

$news = [];
if ($news_query->have_posts()) {
    while ($news_query->have_posts()) {
        $news_query->the_post();
        $id = get_the_ID();
        $news[] = [
            'id'      => $id,
            'title'   => get_the_title(),
            'link'    => get_permalink(),
            'thumb'   => get_the_post_thumbnail_url($id, 'medium') ?: '',
            'excerpt' => wp_trim_words(get_the_excerpt(), 20),
            'acf'     => [
                'publication_date' => get_post_meta($id, 'publication_date', true) ?: '',
            ],
        ];
    }
    wp_reset_postdata();
}
?>

<section class="hero">
  <div class="container">
    <h1>Система управління спортивними подіями</h1>
    <p>Реєструйтесь на змагання, слідкуйте за розкладом та результатами в режимі реального часу</p>
    <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap">
      <a href="<?php echo get_post_type_archive_link('event'); ?>" class="btn btn-primary">Переглянути події</a>
      <a href="<?php echo home_url('/results'); ?>" class="btn btn-outline">Результати</a>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <h2 class="section-title">Найближчі події</h2>
    <p class="section-sub">Актуальні змагання та заходи</p>
    <div class="cards-grid">
      <?php if (empty($events)): ?>
        <p style="color:var(--color-text-muted);padding:1rem">Подій немає</p>
      <?php else: ?>
        <?php foreach ($events as $ev):
          $acf    = $ev['acf'];
          $status = $acf['event_status'];
          $statusMap = [
            'active'    => ['class'=>'badge-active',    'label'=>'Активна'],
            'finished'  => ['class'=>'badge-finished',  'label'=>'Завершена'],
            'cancelled' => ['class'=>'badge-cancelled', 'label'=>'Скасована'],
          ];
          $badge = $statusMap[$status] ?? ['class'=>'badge-active','label'=>'Активна'];
        ?>
          <article class="card">
            <?php if ($ev['thumb']): ?>
              <div class="card-thumb">
                <img src="<?php echo esc_url($ev['thumb']); ?>"
                     alt="<?php echo esc_attr($ev['title']); ?>"
                     loading="lazy" width="400" height="225">
              </div>
            <?php endif; ?>
            <div class="card-body">
              <div class="card-meta">
                <?php if ($acf['event_date']): ?>
                  <time><?php echo esc_html($acf['event_date']); ?></time>
                <?php endif; ?>
                <span class="badge <?php echo $badge['class']; ?>">
                  <?php echo $badge['label']; ?>
                </span>
              </div>
              <h3><?php echo esc_html($ev['title']); ?></h3>
              <p><?php echo esc_html($acf['event_location']); ?></p>
              <a href="<?php echo esc_url($ev['link']); ?>"
                 class="btn btn-primary"
                 style="font-size:.85rem;padding:.5rem 1rem">Детальніше</a>
            </div>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<section class="section" style="background:#fff">
  <div class="container">
    <h2 class="section-title">Останні новини</h2>
    <p class="section-sub">Будьте в курсі подій</p>
    <div class="cards-grid">
      <?php if (empty($news)): ?>
        <p style="color:var(--color-text-muted);padding:1rem">Новин немає</p>
      <?php else: ?>
        <?php foreach ($news as $n):
          $acf = $n['acf'];
        ?>
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
                <time><?php echo esc_html($acf['publication_date']); ?></time>
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
  </div>
</section>

<?php get_footer(); ?>
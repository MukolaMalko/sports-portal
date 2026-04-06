<?php
/**
 * Archive: Events
 */
get_header();

// Отримуємо параметри фільтрації
$filter_status = sanitize_text_field($_GET['status'] ?? '');
$filter_search = sanitize_text_field($_GET['search'] ?? '');
$paged         = max(1, intval($_GET['paged'] ?? 1));

// Будуємо запит
$args = [
    'post_type'      => 'event',
    'posts_per_page' => 9,
    'paged'          => $paged,
    'post_status'    => 'publish',
];

if ($filter_search) {
    $args['s'] = $filter_search;
}

$query      = new WP_Query($args);
$total_pages = $query->max_num_pages;

// Збираємо події
$events = [];
while ($query->have_posts()) {
    $query->the_post();
    $acf = function_exists('get_fields') ? get_fields(get_the_ID()) : [];
    // Фільтр по статусу на рівні PHP
    if ($filter_status && ($acf['event_status'] ?? '') !== $filter_status) continue;
    $events[] = [
        'id'       => get_the_ID(),
        'title'    => get_the_title(),
        'link'     => get_permalink(),
        'thumb'    => get_the_post_thumbnail_url(get_the_ID(), 'medium') ?: '',
        'acf'      => $acf ?: [],
    ];
}
wp_reset_postdata();

$status_map = [
    'active'    => ['class' => 'badge-active',    'label' => 'Активна'],
    'finished'  => ['class' => 'badge-finished',  'label' => 'Завершена'],
    'cancelled' => ['class' => 'badge-cancelled', 'label' => 'Скасована'],
];
?>

<main class="section">
  <div class="container">
    <h1 class="section-title">Події</h1>
    <p class="section-sub">Всі спортивні заходи</p>

    <!-- Filters -->
    <form method="GET" action="" style="display:flex;gap:var(--space-3);flex-wrap:wrap;margin-bottom:var(--space-6)">
      <select name="status" class="btn" style="font-weight:500" onchange="this.form.submit()">
        <option value="" <?php selected($filter_status, ''); ?>>Всі статуси</option>
        <option value="active"    <?php selected($filter_status, 'active'); ?>>Активні</option>
        <option value="finished"  <?php selected($filter_status, 'finished'); ?>>Завершені</option>
        <option value="cancelled" <?php selected($filter_status, 'cancelled'); ?>>Скасовані</option>
      </select>
      <input type="text" name="search" placeholder="Пошук..."
             value="<?php echo esc_attr($filter_search); ?>"
             style="max-width:260px"
             onchange="this.form.submit()">
      <button type="submit" class="btn btn-primary">Знайти</button>
      <?php if ($filter_status || $filter_search): ?>
        <a href="<?php echo get_post_type_archive_link('event'); ?>" class="btn btn-outline">Скинути</a>
      <?php endif; ?>
    </form>

    <!-- Events Grid -->
    <div class="cards-grid">
      <?php if (empty($events)): ?>
        <div class="alert alert-info">Подій не знайдено</div>
      <?php else: ?>
        <?php foreach ($events as $ev):
          $acf    = $ev['acf'];
          $status = $acf['event_status'] ?? '';
          $badge  = $status_map[$status] ?? ['class' => 'badge-finished', 'label' => $status];
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
                <?php if (!empty($acf['event_date'])): ?>
                  <time><?php echo esc_html($acf['event_date']); ?></time>
                <?php endif; ?>
                <?php if ($status): ?>
                  <span class="badge <?php echo $badge['class']; ?>"><?php echo $badge['label']; ?></span>
                <?php endif; ?>
              </div>
              <h3><?php echo esc_html($ev['title']); ?></h3>
              <p><?php echo esc_html(($acf['event_location'] ?? '') . ($acf['sport_type'] ? ' · ' . $acf['sport_type'] : '')); ?></p>
              <a href="<?php echo esc_url($ev['link']); ?>" class="btn btn-primary" style="font-size:.85rem;padding:.5rem 1rem">Детальніше</a>
            </div>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
      <div style="display:flex;gap:var(--space-2);justify-content:center;margin-top:var(--space-8)">
        <?php for ($i = 1; $i <= $total_pages; $i++):
          $url = add_query_arg(['paged' => $i, 'status' => $filter_status, 'search' => $filter_search]);
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
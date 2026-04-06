<?php
/**
 * Archive: Results
 */
get_header();

function get_sheet_data($spreadsheet_id, $range) {
    if (!$spreadsheet_id) return [];
    $api_key = defined('GOOGLE_API_KEY') ? GOOGLE_API_KEY : get_option('sports_google_api_key', '');
    if (!$api_key) return [];
    $url  = "https://sheets.googleapis.com/v4/spreadsheets/{$spreadsheet_id}/values/{$range}?key={$api_key}";
    $resp = wp_remote_get($url);
    if (is_wp_error($resp)) return [];
    $body = json_decode(wp_remote_retrieve_body($resp), true);
    return $body['values'] ?? [];
}
?>
<main class="section">
  <div class="container">
    <h1 class="section-title">Результати</h1>
    <p class="section-sub">Підсумки змагань</p>

    <?php
    $query = new WP_Query([
        'post_type'      => 'results',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ]);

    if ($query->have_posts()):
        while ($query->have_posts()):
            $query->the_post();
            $id = get_the_ID();

            $related_event_raw = get_post_meta($id, 'related_event', true);
$related_event_id  = is_array($related_event_raw) ? ($related_event_raw['ID'] ?? 0) : intval($related_event_raw);
            $event_title      = $related_event_id ? get_the_title($related_event_id) : '—';
            $gsheet_range     = get_post_meta($id, 'gsheet_range', true) ?: 'Sheet1!A1:E100';
            $participant      = get_post_meta($id, 'participant_id', true) ?: '—';
            $score            = get_post_meta($id, 'score', true) ?: '—';
            $place            = get_post_meta($id, 'place', true) ?: '—';
            $sheet_id         = $related_event_id ? get_post_meta($related_event_id, 'gsheet_id', true) : '';
            $rows             = get_sheet_data($sheet_id, $gsheet_range);
    ?>
        <div style="margin-bottom:var(--space-8)">
            <h2 style="font-size:1.25rem;margin-bottom:var(--space-4)">
                <?php the_title(); ?> — <?php echo esc_html($event_title); ?>
            </h2>

            <div class="table-wrap">
                <table>
                    <?php if (!empty($rows)): ?>
                        <thead><tr>
                            <?php foreach (($rows[0] ?? []) as $h): ?>
                                <th><?php echo esc_html($h); ?></th>
                            <?php endforeach; ?>
                        </tr></thead>
                        <tbody>
                            <?php foreach (array_slice($rows, 1) as $row): ?>
                                <tr>
                                    <?php foreach ($row as $cell): ?>
                                        <td><?php echo esc_html($cell); ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    <?php else: ?>
                        <thead><tr><th>#</th><th>Учасник</th><th>Рахунок</th><th>Місце</th></tr></thead>
                        <tbody>
                            <tr>
                                <td><?php echo esc_html($place); ?></td>
                                <td><?php echo esc_html($participant); ?></td>
                                <td><?php echo esc_html($score); ?></td>
                                <td><?php echo esc_html($place); ?></td>
                            </tr>
                        </tbody>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    <?php
        endwhile;
        wp_reset_postdata();
    else: ?>
        <div class="alert alert-info">Результатів ще немає</div>
    <?php endif; ?>
  </div>
</main>
<?php get_footer(); ?>
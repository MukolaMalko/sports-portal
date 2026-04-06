<?php
/**
 * Archive: Schedule
 */
get_header();
?>
<main class="section">
  <div class="container">
    <h1 class="section-title">Розклад</h1>
    <p class="section-sub">Графік проведення змагань</p>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Подія</th>
                    <th>Етап</th>
                    <th>Початок</th>
                    <th>Кінець</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $q = new WP_Query([
                    'post_type'      => 'schedule',
                    'posts_per_page' => 50,
                    'orderby'        => 'meta_value',
                    'meta_key'       => 'start_time',
                    'order'          => 'ASC',
                ]);

                if ($q->have_posts()):
                    while ($q->have_posts()):
                        $q->the_post();
                        $id = get_the_ID();

                        $related_event_id = get_post_meta($id, 'related_event', true);
                        $event_title      = $related_event_id ? get_the_title($related_event_id) : '—';
                        $event_link       = $related_event_id ? get_permalink($related_event_id) : '#';
                        $stage            = get_post_meta($id, 'stage', true) ?: '—';
                        $start_time       = get_post_meta($id, 'start_time', true) ?: '—';
                        $end_time         = get_post_meta($id, 'end_time', true) ?: '—';
                ?>
                    <tr>
                        <td><a href="<?php echo esc_url($event_link); ?>"><?php echo esc_html($event_title); ?></a></td>
                        <td><?php echo esc_html($stage); ?></td>
                        <td><?php echo esc_html($start_time); ?></td>
                        <td><?php echo esc_html($end_time); ?></td>
                    </tr>
                <?php
                    endwhile;
                    wp_reset_postdata();
                else: ?>
                    <tr>
                        <td colspan="4" style="text-align:center;color:var(--color-text-muted)">
                            Розклад ще не додано
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
  </div>
</main>
<?php get_footer(); ?>
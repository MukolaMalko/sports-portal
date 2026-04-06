<?php
/**
 * Single Event Template
 */
get_header();
the_post();
$acf = get_fields();
$event_id = get_the_ID();
$status_labels = ['active'=>'Активна','finished'=>'Завершена','cancelled'=>'Скасована'];
$status_badges = ['active'=>'badge-active','finished'=>'badge-finished','cancelled'=>'badge-cancelled'];
$status = $acf['event_status'] ?? 'active';
?>
<main class="section">
  <div class="container" style="max-width:900px">
    <div style="display:flex;align-items:center;gap:var(--space-3);margin-bottom:var(--space-2)">
      <a href="<?php echo home_url('/events'); ?>" style="color:var(--color-text-muted);font-size:.875rem">← Всі події</a>
    </div>

    <?php if (has_post_thumbnail()): ?>
    <div style="border-radius:var(--radius-lg);overflow:hidden;margin-bottom:var(--space-6)">
      <?php the_post_thumbnail('large', ['style'=>'width:100%;height:auto']); ?>
    </div>
    <?php endif; ?>

    <div style="display:flex;gap:var(--space-3);align-items:center;margin-bottom:var(--space-4);flex-wrap:wrap">
      <span class="badge <?php echo esc_attr($status_badges[$status]??'badge-finished'); ?>">
        <?php echo esc_html($status_labels[$status]??$status); ?>
      </span>
      <?php if ($acf['event_date']): ?>
        <time style="font-size:.875rem;color:var(--color-text-muted)"><?php echo esc_html($acf['event_date']); ?></time>
      <?php endif; ?>
      <?php if ($acf['sport_type']): ?>
        <span style="font-size:.875rem;color:var(--color-text-muted)"><?php echo esc_html($acf['sport_type']); ?></span>
      <?php endif; ?>
    </div>

    <h1 style="font-family:var(--font-display);font-size:clamp(1.5rem,4vw,2.5rem);font-weight:700;margin-bottom:var(--space-4)"><?php the_title(); ?></h1>

    <?php if ($acf['event_location']): ?>
    <p style="color:var(--color-text-muted);margin-bottom:var(--space-6)">📍 <?php echo esc_html($acf['event_location']); ?></p>
    <?php endif; ?>

    <div style="line-height:1.8;color:var(--color-text);margin-bottom:var(--space-8)">
      <?php the_content(); ?>
    </div>

    <!-- Registration form -->
    <div style="background:#fff;border:1px solid var(--color-border);border-radius:var(--radius-lg);padding:var(--space-6);margin-bottom:var(--space-8)">
      <h2 style="font-size:1.25rem;margin-bottom:var(--space-4)">Реєстрація на подію</h2>

      <?php if (!is_user_logged_in()): ?>
        <div class="alert alert-info">
          <a href="<?php echo wp_login_url(get_permalink()); ?>">Увійдіть</a>, щоб зареєструватись
        </div>
      <?php else: ?>
        <form id="regForm" onsubmit="submitReg(event)">
          <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
          <div class="form-group">
            <label for="participant_name">ПІБ учасника</label>
            <input type="text" id="participant_name" name="participant_name" required placeholder="Прізвище Ім'я По-батькові">
          </div>
          <div class="form-group">
            <label for="participant_phone">Телефон</label>
            <input type="tel" id="participant_phone" name="participant_phone" placeholder="+380XXXXXXXXX">
          </div>
          <div class="form-group">
            <label for="participant_email">Email</label>
            <input type="email" id="participant_email" name="participant_email" value="<?php echo esc_attr(wp_get_current_user()->user_email); ?>" required>
          </div>
          <div id="regMsg"></div>
          <button type="submit" class="btn btn-primary" id="regBtn">Зареєструватись</button>
        </form>
      <?php endif; ?>
    </div>
  </div>
</main>

<script>
async function submitReg(e) {
  e.preventDefault();
  const form   = document.getElementById('regForm');
  const btn    = document.getElementById('regBtn');
  const msgEl  = document.getElementById('regMsg');
  const api    = (typeof SportsAPI!=='undefined')?SportsAPI.root:'/wp-json/';
  const nonce  = (typeof SportsAPI!=='undefined')?SportsAPI.nonce:'';

  btn.disabled = true;
  btn.textContent = 'Відправляємо…';

  const body = {
    title:  form.participant_name.value,
    status: 'publish',
    fields: {
      related_event: parseInt(form.event_id.value),
      status: 'pending',
      participant_name:  form.participant_name.value,
      participant_email: form.participant_email.value,
      participant_phone: form.participant_phone.value,
    }
  };

  try {
    const resp = await fetch(`${api}wp/v2/applications`, {
      method: 'POST',
      headers: {'Content-Type':'application/json','X-WP-Nonce':nonce},
      body: JSON.stringify(body),
    });
    if (resp.ok) {
      msgEl.innerHTML = '<div class="alert alert-success">✓ Заявку подано успішно!</div>';
      form.reset();
    } else {
      const err = await resp.json();
      msgEl.innerHTML = `<div class="alert alert-error">${err.message||'Помилка'}</div>`;
    }
  } catch(err) {
    msgEl.innerHTML = '<div class="alert alert-error">Мережева помилка</div>';
  } finally {
    btn.disabled = false;
    btn.textContent = 'Зареєструватись';
  }
}
</script>
<?php get_footer(); ?>

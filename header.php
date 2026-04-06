<?php
/**
 * Header Template
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300..700&display=swap" rel="stylesheet">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
  <div class="container">
    <div class="header-inner">
      <a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo">
        Sports<span>Hub</span>
      </a>

      <button class="menu-toggle" aria-label="Меню" aria-expanded="false" id="menuToggle">
        <span></span><span></span><span></span>
      </button>

      <nav class="nav-primary" id="primaryNav" aria-label="Головна навігація">
        <?php wp_nav_menu([
          'theme_location' => 'primary',
          'container'      => false,
          'fallback_cb'    => function() {
            echo '<ul>
              <li><a href="'.home_url('/').'">Головна</a></li>
              <li><a href="'.home_url('/events').'">Події</a></li>
              <li><a href="'.home_url('/schedule').'">Розклад</a></li>
              <li><a href="'.home_url('/results').'">Результати</a></li>
              <li><a href="'.home_url('/news').'">Новини</a></li>
            </ul>';
          },
        ]); ?>
      </nav>
    </div>
  </div>
</header>

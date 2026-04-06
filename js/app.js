/* Sports Theme — Main JS */
document.addEventListener('DOMContentLoaded', function () {

  // ── Mobile menu toggle ──────────────────────────────────────
  const toggle = document.getElementById('menuToggle');
  const nav    = document.getElementById('primaryNav');
  if (toggle && nav) {
    toggle.addEventListener('click', function () {
      const open = nav.classList.toggle('open');
      toggle.setAttribute('aria-expanded', open);
    });
  }

  // ── Active nav link ─────────────────────────────────────────
  const links = document.querySelectorAll('.nav-primary a');
  links.forEach(link => {
    if (link.href === window.location.href) link.classList.add('active');
  });

});

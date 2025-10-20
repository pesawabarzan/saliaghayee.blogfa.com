// assets/js/theme-toggle.js
(function () {
  var KEY = 'site_theme';
  var btn   = document.getElementById('themeToggle');
  var light = document.getElementById('theme-light');
  var dark  = document.getElementById('theme-dark');

  function set(mode) {
    if (!light || !dark) return;
    // روشن/تاریک
    light.disabled = (mode === 'dark');
    dark.disabled  = (mode !== 'dark');
    // برای آینده اگر CSS بر اساس data-theme داشتیم
    document.documentElement.dataset.theme = mode;
    // ذخیره
    try { localStorage.setItem(KEY, mode); } catch (e) {}
    if (btn) btn.textContent = mode === 'dark' ? '🌙' : '☀️';
  }

  function get() {
    try { return localStorage.getItem(KEY) || 'light'; } catch (e) { return 'light'; }
  }

  if (btn) {
    btn.addEventListener('click', function () {
      set(get() === 'dark' ? 'light' : 'dark');
    });
  }

  // init: اگر چیزی ذخیره نشده، پیش‌فرض روشن
  set(get());
})();














// ===== Mobile Nav Toggle =====
(function () {
  const navBtn = document.getElementById('navToggle');
  const nav    = document.getElementById('mainNav');
  const back   = document.getElementById('navBackdrop');

  if (!navBtn || !nav || !back) return;

  const setOpen = (open) => {
    nav.classList.toggle('open', open);
    document.body.classList.toggle('nav-open', open);
    navBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
  };

  navBtn.addEventListener('click', () => setOpen(!nav.classList.contains('open')));
  back.addEventListener('click', () => setOpen(false));
  window.addEventListener('resize', () => { if (window.innerWidth > 720) setOpen(false); });
})();

<?php
require __DIR__ . '/config/bootstrap.php';
$BASE = rtrim($CONFIG['base_url'] ?: base_url(), '/');
$page_title = 'جستجو';
include __DIR__ . '/templates/partials/header.php';
?>
<h1>جستجو</h1>

<form class="search-form" onsubmit="return false" style="max-width:680px">
  <label for="q">عبارت مورد نظر</label>
  <input type="text" id="q" placeholder="مثلاً: یادداشت پژوهشی" autocomplete="off">
</form>

<div id="liveResult" style="margin-top:1rem"></div>

<script>
(function () {
  const BASE = <?= json_encode($BASE) ?>;
  const input = document.getElementById('q');
  const box   = document.getElementById('liveResult');

  let t = null; // debounce timer
  const fmtDate = d => {
    try { return new Date(d.replace(' ', 'T')).toLocaleDateString('fa-IR'); }
    catch { return d; }
  };

  function render(items, total) {
    if (!items.length) {
      box.innerHTML = '<div class="alert" style="max-width:680px">موردی برای نمایش نیست.</div>';
      return;
    }
    const html = items.map(it => `
      <article class="post-card">
        <h2 style="margin:0 0 .25rem">
          <a href="${BASE}/post.php?slug=${encodeURIComponent(it.slug)}">${escapeHtml(it.title)}</a>
        </h2>
        <div class="meta"><span>${fmtDate(it.created_at)}</span></div>
        ${it.cover_image ? `<img src="${escapeAttr(absCover(it.cover_image))}" alt="" style="max-width:100%;border-radius:10px;margin:.5rem 0">` : ``}
        <p>${escapeHtml(it.excerpt || '')}</p>
        <a class="read-more" href="${BASE}/post.php?slug=${encodeURIComponent(it.slug)}">…ادامه</a>
      </article>
    `).join('');
    box.innerHTML = html;
  }

  function absCover(src) {
    if (/^https?:\/\//i.test(src)) return src;
    return BASE + '/' + src.replace(/^\/+/, '');
  }

  function escapeHtml(s) {
    return (s ?? '').replace(/[&<>"']/g, m => ({
      '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'
    }[m]));
  }
  function escapeAttr(s){ return escapeHtml(s); }

  async function query(q) {
    if (!q) { box.innerHTML = ''; return; } // وقتی خالی است، هیچ‌چیز نشان نده
    box.innerHTML = '<div class="muted">در حال جستجو…</div>';
    try {
      const r = await fetch(`${BASE}/search_api.php?q=${encodeURIComponent(q)}`);
      const data = await r.json();
      render(data.items || [], data.total || 0);
    } catch (e) {
      box.innerHTML = '<div class="alert error" style="max-width:680px">خطا در دریافت نتایج.</div>';
    }
  }

  input.addEventListener('input', () => {
    clearTimeout(t);
    t = setTimeout(() => query(input.value.trim()), 300); // دیباونس ۳۰۰ms
  });

  // اگر با ?q=foo آمده‌ایم، مقدار اولیه را بخوانیم
  const urlQ = new URLSearchParams(location.search).get('q') || '';
  if (urlQ) {
    input.value = urlQ;
    query(urlQ);
  }
})();
</script>

<?php include __DIR__ . '/templates/partials/footer.php'; ?>

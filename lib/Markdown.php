<?php
/** lib/Markdown.php
 * مبدل ساده Markdown -> HTML (ایمن، بدون پشتیبانی از HTML خام)
 * پشتیبانی محدود: عناوین (#, ##), bold (**), italic (*), لینک [text](url), لیست -, عددی 1.
 */

function md_to_html(string $md): string {
  $md = str_replace(["
", ""], "
", $md);
  $lines = explode("\n", $md);
  $html = [];
  $in_ul = false; $in_ol = false;

  foreach ($lines as $line) {
    $t = trim($line);

    if ($t === '') {
      if ($in_ul) { $html[] = '</ul>'; $in_ul = false; }
      if ($in_ol) { $html[] = '</ol>'; $in_ol = false; }
      continue;
    }

    if (preg_match('/^#{1,6}\s+(.*)$/u', $t, $m)) {
      if ($in_ul) { $html[] = '</ul>'; $in_ul = false; }
      if ($in_ol) { $html[] = '</ol>'; $in_ol = false; }
      $content = e($m[1]);
      $level = max(1, min(6, mb_strlen(explode(' ', $t)[0])));
      $html[] = "<h{$level}>{$content}</h{$level}>";
      continue;
    }

    if (preg_match('/^\-\s+(.*)$/u', $t, $m)) {
      if ($in_ol) { $html[] = '</ol>'; $in_ol = false; }
      if (!$in_ul) { $in_ul = true; $html[] = '<ul>'; }
      $html[] = '<li>' . inline_md(e($m[1])) . '</li>';
      continue;
    }

    if (preg_match('/^\d+\.\s+(.*)$/u', $t, $m)) {
      if ($in_ul) { $html[] = '</ul>'; $in_ul = false; }
      if (!$in_ol) { $in_ol = true; $html[] = '<ol>'; }
      $html[] = '<li>' . inline_md(e($m[1])) . '</li>';
      continue;
    }

    // پاراگراف معمولی
    $html[] = '<p>' . inline_md(e($t)) . '</p>';
  }

  if ($in_ul) $html[] = '</ul>';
  if ($in_ol) $html[] = '</ol>';

  return implode("\n", $html);
}

function inline_md(string $s): string {
  // **bold**
  $s = preg_replace('/\*\*(.+?)\*\*/u', '<strong>$1</strong>', $s);
  // *italic*
  $s = preg_replace('/\*(.+?)\*/u', '<em>$1</em>', $s);
  // [text](url)
  $s = preg_replace_callback('/\[(.+?)\]\((https?:\/\/[^\s]+)\)/u', function($m) {
    $text = $m[1]; $url = $m[2];
    return '<a href="'.htmlspecialchars($url, ENT_QUOTES, 'UTF-8').'" rel="noopener" target="_blank">'.htmlspecialchars($text, ENT_QUOTES, 'UTF-8').'</a>';
  }, $s);
  return $s;
}

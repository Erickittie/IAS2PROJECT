<?php
declare(strict_types=1);

function h(string $s): string {
  return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function base_path(): string {
  // Works for both:
  // - XAMPP:        /IAS(HILL)/index.php
  // - php -S server: /index.php
  $script = (string)($_SERVER['SCRIPT_NAME'] ?? '');
  if ($script !== '' && preg_match('#^(.*?)/ciphers/#', $script, $m)) return $m[1];
  $dir = rtrim(str_replace('\\', '/', dirname($script)), '/');
  return $dir === '/' ? '' : $dir;
}

function render_header(string $title): void { ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?= h($title) ?></title>
  <style>
    :root{--bg:#0b1020;--card:#111836;--muted:#9aa7c7;--text:#e9eeff;--accent:#7aa2ff;--bad:#ff6b6b;--ok:#3ee27a;--border:#23305f}
    *{box-sizing:border-box} body{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;background:radial-gradient(1200px 600px at 20% 0%,#14235c 0%,transparent 60%),var(--bg);color:var(--text)}
    a{color:var(--accent);text-decoration:none} a:hover{text-decoration:underline}
    .wrap{max-width:1000px;margin:0 auto;padding:24px}
    .top{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:18px}
    .brand{display:flex;flex-direction:column;gap:4px}
    .brand b{letter-spacing:.2px}
    .brand small{color:var(--muted)}
    .card{background:linear-gradient(180deg,rgba(255,255,255,.06),rgba(255,255,255,.03));border:1px solid var(--border);border-radius:16px;padding:18px}
    .grid{display:grid;grid-template-columns:repeat(12,1fr);gap:14px}
    .col-6{grid-column:span 6} .col-12{grid-column:span 12}
    @media (max-width:900px){.col-6{grid-column:span 12}}
    label{display:block;font-size:13px;color:var(--muted);margin:10px 0 6px}
    input,textarea,select{width:100%;padding:10px 12px;border-radius:12px;border:1px solid var(--border);background:#0b1230;color:var(--text);outline:none}
    textarea{min-height:110px;resize:vertical}
    .row{display:flex;gap:10px;flex-wrap:wrap;align-items:center}
    .btn{appearance:none;border:1px solid var(--border);background:#182657;color:var(--text);padding:10px 14px;border-radius:12px;cursor:pointer}
    .btn.primary{background:linear-gradient(180deg,#2a49b6,#203c9a);border-color:#2c4bcc}
    .pill{display:inline-flex;align-items:center;gap:8px;padding:8px 10px;border-radius:999px;border:1px solid var(--border);background:rgba(0,0,0,.2);color:var(--muted);font-size:13px}
    .msg{padding:12px 12px;border-radius:12px;border:1px solid var(--border);background:rgba(0,0,0,.25)}
    .msg.bad{border-color:rgba(255,107,107,.35)} .msg.ok{border-color:rgba(62,226,122,.35)}
    pre{white-space:pre-wrap;word-break:break-word;margin:0;font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;font-size:13px;color:#dbe4ff}
    .muted{color:var(--muted)}
    .footer{margin-top:18px;color:var(--muted);font-size:13px}
    .kbd{font-family:ui-monospace,Menlo,Consolas,monospace;font-size:12px;border:1px solid var(--border);padding:2px 6px;border-radius:8px;background:rgba(0,0,0,.25)}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="top">
      <div class="brand">
        <b><?= h($title) ?></b>
        <small>IAS — Cipher demo site (PHP)</small>
      </div>
      <div class="row">
        <a class="pill" href="<?= h(base_path() . '/index.php') ?>">Menu</a>
        <span class="pill">Tip: use <span class="kbd">A-Z</span> only for Hill</span>
      </div>
    </div>
<?php }

function render_footer(): void { ?>
    <div class="footer">Built for group submission. Keep keys and plaintext small; this is educational, not production crypto.</div>
  </div>
</body>
</html>
<?php } ?>


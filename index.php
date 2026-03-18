<?php
declare(strict_types=1);

require_once __DIR__ . '/common/layout.php';

render_header('Cipher Menu');
?>

<div class="card">
  <div class="grid">
    <div class="col-12">
      <div class="msg">
        <b>Choose a cipher</b>
        <div class="muted">Each cipher page supports encrypt and decrypt. Team members can work independently inside <code>ciphers/</code>.</div>
      </div>
    </div>

    <div class="col-6">
      <div class="card">
        <b>Hill Cipher</b>
        <div class="muted">Matrix-based substitution (mod 26).</div>
        <div style="height:10px"></div>
        <a class="btn primary" href="./ciphers/hill/hill.php">Open Hill</a>
      </div>
    </div>

    <div class="col-6">
      <div class="card">
        <b>Playfair (teammate)</b>
        <div class="muted">Placeholder link for teammate page.</div>
        <div style="height:10px"></div>
        <a class="btn" href="./ciphers/playfair/playfair.php">Open Playfair</a>
      </div>
    </div>

    <div class="col-6">
      <div class="card">
        <b>Affine (teammate)</b>
        <div class="muted">Placeholder link for teammate page.</div>
        <div style="height:10px"></div>
        <a class="btn" href="./ciphers/affine/affine.php">Open Affine</a>
      </div>
    </div>
  </div>
</div>

<?php render_footer(); ?>


<?php
declare(strict_types=1);

require_once __DIR__ . '/../../common/layout.php';

function cleanAZ(string $s): string { return preg_replace('/[^A-Z]/', '', strtoupper($s)) ?? ''; }
function mod26(int $x): int { $r = $x % 26; return $r < 0 ? $r + 26 : $r; }

function invMod26(int $a): ?int {
  // extended Euclid: find x such that (a*x) mod 26 = 1
  $a = mod26($a);
  $t = 0; $newT = 1;
  $r = 26; $newR = $a;
  while ($newR !== 0) {
    $q = intdiv($r, $newR);
    [$t, $newT] = [$newT, $t - $q * $newT];
    [$r, $newR] = [$newR, $r - $q * $newR];
  }
  if ($r !== 1) return null;
  return mod26($t);
}

function keyToMatrix2(string $key): array {
  $k = cleanAZ($key);
  if (strlen($k) !== 4) throw new RuntimeException('Key must be exactly 4 letters (A–Z), e.g. HILL.');
  $v = [];
  for ($i=0;$i<4;$i++) $v[] = ord($k[$i]) - 65;
  return [[ $v[0], $v[1] ], [ $v[2], $v[3] ]];
}

function invMatrix2Mod26(array $K): array {
  $det = (int)$K[0][0] * (int)$K[1][1] - (int)$K[0][1] * (int)$K[1][0];
  $detInv = invMod26($det);
  if ($detInv === null) throw new RuntimeException('Key is not invertible mod 26 (det has no inverse). Choose a different key.');
  // K^-1 = detInv * [ d -b; -c a ] mod 26
  return [
    [ mod26((int)$K[1][1] * $detInv), mod26((-(int)$K[0][1]) * $detInv) ],
    [ mod26((-(int)$K[1][0]) * $detInv), mod26((int)$K[0][0] * $detInv) ],
  ];
}

function hill2(string $text, array $K): array {
  $clean = cleanAZ($text);
  if ($clean === '') throw new RuntimeException('Text must contain at least one A–Z letter.');
  $pad = 0;
  if ((strlen($clean) % 2) === 1) { $clean .= 'X'; $pad = 1; }

  $out = '';
  for ($i=0; $i<strlen($clean); $i+=2) {
    $x0 = ord($clean[$i]) - 65;
    $x1 = ord($clean[$i+1]) - 65;
    $y0 = mod26((int)$K[0][0]*$x0 + (int)$K[0][1]*$x1);
    $y1 = mod26((int)$K[1][0]*$x0 + (int)$K[1][1]*$x1);
    $out .= chr($y0 + 65) . chr($y1 + 65);
  }

  return ['clean' => $clean, 'pad' => $pad, 'result' => $out];
}

$mode = (($_POST['mode'] ?? 'encrypt') === 'decrypt') ? 'decrypt' : 'encrypt';
$text = (string)($_POST['text'] ?? '');
$key  = (string)($_POST['key'] ?? '');

$error = '';
$output = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    $K = keyToMatrix2($key);
    if ($mode === 'decrypt') $K = invMatrix2Mod26($K);
    $output = hill2($text, $K);
  } catch (Throwable $t) {
    $error = $t->getMessage();
  }
}

render_header('Hill Cipher');
?>

<div class="grid">
  <div class="col-12">
    <div class="card">
      <b>Hill cipher (mod 26)</b>
      <div class="muted">Study version: <b>2×2 only</b>, key is <b>4 letters</b> (A–Z). Input is cleaned to A–Z; plaintext pads with X if needed.</div>
    </div>
  </div>

  <div class="col-6">
    <form method="post" class="card">
      <div class="row" style="justify-content:space-between">
        <div class="row">
          <label style="margin:0">
            <span class="muted">Mode</span>
            <select name="mode" style="width:auto;min-width:150px">
              <option value="encrypt" <?= $mode==='encrypt'?'selected':'' ?>>Encrypt</option>
              <option value="decrypt" <?= $mode==='decrypt'?'selected':'' ?>>Decrypt</option>
            </select>
          </label>
        </div>
        <a class="pill" href="../../index.php">Back</a>
      </div>

      <label for="text">Text (A–Z)</label>
      <textarea id="text" name="text" placeholder="Example: ATTACKATDAWN"><?= h($text) ?></textarea>

      <label for="key">Key (4 letters)</label>
      <input id="key" name="key" value="<?= h($key) ?>" placeholder="Example: HILL" />

      <div style="height:12px"></div>
      <button class="btn primary" type="submit"><?= $mode==='encrypt'?'Encrypt':'Decrypt' ?></button>
      <div class="muted" style="margin-top:10px">
        Example key: <span class="kbd">HILL</span>
      </div>
    </form>
  </div>

  <div class="col-6">
    <div class="card">
      <b>Output</b>
      <div style="height:10px"></div>

      <?php if ($error !== ''): ?>
        <div class="msg bad"><b>Error</b><div class="muted"><?= h($error) ?></div></div>
      <?php elseif ($output !== null): ?>
        <div class="msg ok">
          <div class="muted">Cleaned input (after removing non-letters)</div>
          <pre><?= h($output['clean']) ?></pre>
          <div style="height:10px"></div>
          <div class="muted">Result</div>
          <pre><?= h($output['result']) ?></pre>
          <div style="height:10px"></div>
          <div class="muted">Padding added: <?= (int)$output['pad'] ?> X</div>
        </div>
      <?php else: ?>
        <div class="msg"><div class="muted">Submit the form to see results.</div></div>
      <?php endif; ?>

      <div style="height:12px"></div>
      <div class="msg">
        <b>Key rules</b>
        <div class="muted">
          For decryption, the 2×2 key matrix must be invertible mod 26.
          If you get an invertibility error, choose a different key.
        </div>
      </div>
    </div>
  </div>
</div>

<?php render_footer(); ?>


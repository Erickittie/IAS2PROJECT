<?php
declare(strict_types=1);
require_once __DIR__ . '/../../common/layout.php';
render_header('Playfair Cipher');

// --- Helper Functions ---

function generateKeyMatrix(string $key): array {
    $key = strtoupper(preg_replace('/[^A-Z]/', '', $key));
    $key = str_replace('J', 'I', $key);

    $alphabet = range('A', 'Z');
    $alphabet = array_diff($alphabet, ['J']);

    $unique = [];
    foreach (str_split($key) as $char) {
        if (!in_array($char, $unique)) {
            $unique[] = $char;
        }
    }

    foreach ($alphabet as $char) {
        if (!in_array($char, $unique)) {
            $unique[] = $char;
        }
    }

    return array_chunk($unique, 5);
}

function findPosition(array $matrix, string $char): array {
    foreach ($matrix as $row => $line) {
        $col = array_search($char, $line);
        if ($col !== false) return [$row, $col];
    }
    return [-1, -1];
}

function prepareText(string $text): string {
    $text = strtoupper(preg_replace('/[^A-Z]/', '', $text));
    $text = str_replace('J', 'I', $text);

    $result = '';
    for ($i = 0; $i < strlen($text); $i++) {
        $result .= $text[$i];
        if (isset($text[$i+1]) && $text[$i] === $text[$i+1]) {
            $result .= 'X';
        }
    }

    if (strlen($result) % 2 !== 0) {
        $result .= 'X';
    }

    return $result;
}

function encryptPlayfair(string $text, array $matrix): string {
    $text = prepareText($text);
    $cipher = '';

    for ($i = 0; $i < strlen($text); $i += 2) {
        $a = $text[$i];
        $b = $text[$i+1];

        [$r1, $c1] = findPosition($matrix, $a);
        [$r2, $c2] = findPosition($matrix, $b);

        if ($r1 === $r2) {
            $cipher .= $matrix[$r1][($c1 + 1) % 5];
            $cipher .= $matrix[$r2][($c2 + 1) % 5];
        } elseif ($c1 === $c2) {
            $cipher .= $matrix[($r1 + 1) % 5][$c1];
            $cipher .= $matrix[($r2 + 1) % 5][$c2];
        } else {
            $cipher .= $matrix[$r1][$c2];
            $cipher .= $matrix[$r2][$c1];
        }
    }

    return $cipher;
}

function decryptPlayfair(string $text, array $matrix): string {
    $text = strtoupper(preg_replace('/[^A-Z]/', '', $text));
    $plain = '';

    for ($i = 0; $i < strlen($text); $i += 2) {
        $a = $text[$i];
        $b = $text[$i+1];

        [$r1, $c1] = findPosition($matrix, $a);
        [$r2, $c2] = findPosition($matrix, $b);

        if ($r1 === $r2) {
            $plain .= $matrix[$r1][($c1 + 4) % 5];
            $plain .= $matrix[$r2][($c2 + 4) % 5];
        } elseif ($c1 === $c2) {
            $plain .= $matrix[($r1 + 4) % 5][$c1];
            $plain .= $matrix[($r2 + 4) % 5][$c2];
        } else {
            $plain .= $matrix[$r1][$c2];
            $plain .= $matrix[$r2][$c1];
        }
    }

    return $plain;
}

// --- Remove filler X after decryption ---
function cleanDecryptedText(string $text): string {
    $result = '';

    for ($i = 0; $i < strlen($text); $i++) {
        // Remove X between duplicate letters (e.g., LXL → LL)
        if (
            $i > 0 && $i < strlen($text) - 1 &&
            $text[$i] === 'X' &&
            $text[$i - 1] === $text[$i + 1]
        ) {
            continue;
        }

        $result .= $text[$i];
    }

    // Remove trailing X
    if (substr($result, -1) === 'X') {
        $result = substr($result, 0, -1);
    }

    return $result;
}

// --- Form Handling ---
$result = '';
$action = '';
$key = $_POST['key'] ?? '';
$text = $_POST['text'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'encrypt';

    if ($action === 'clear') {
        // Clear everything
        $key = '';
        $text = '';
        $result = '';
    } else {
        // Use previous result as input when decrypting
        if ($action === 'decrypt' && !empty($_POST['last_result'])) {
            $text = $_POST['last_result'];
        }

        $matrix = generateKeyMatrix($key);

        if ($action === 'encrypt') {
            $result = encryptPlayfair($text, $matrix);
        } elseif ($action === 'decrypt') {
            $result = cleanDecryptedText(decryptPlayfair($text, $matrix));
        }
    }
}
?>

<div class="card">
  <b>Playfair Cipher</b>

  <form method="POST">
    <div>
      <label>Key:</label><br>
      <input type="text" name="key" required
      value="<?php echo htmlspecialchars($key); ?>">
    </div>

    <div>
      <label>Text:</label><br>
      <textarea name="text" required><?php echo htmlspecialchars($text); ?></textarea>
    </div>

    <!-- Hidden field to store last result -->
    <input type="hidden" name="last_result" value="<?php echo htmlspecialchars($result); ?>">

    <div style="margin-top:12px;">
      <button class="btn" name="action" value="encrypt">Encrypt</button>
      <button class="btn" name="action" value="decrypt">Decrypt</button>
      <button class="btn" name="action" value="clear">Clear All</button>
    </div>
  </form>

  <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action !== 'clear'): ?>
    <div style="margin-top:15px;">
      <b>Result (<?php echo ucfirst($action); ?>):</b>
      <div class="muted" id="result-div"><?php echo htmlspecialchars($result); ?></div>
    </div>
  <?php endif; ?>

  <div style="height:30px"></div>
  <a class="btn" href="../../index.php">Back to menu</a>
</div>

<?php render_footer(); ?>
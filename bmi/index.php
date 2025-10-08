<?php
$errors = [];
$bmi = null;
$category = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $beratRaw = isset($_POST['berat']) ? str_replace(',', '.', $_POST['berat']) : '';
    $tinggiRaw = isset($_POST['tinggi']) ? str_replace(',', '.', $_POST['tinggi']) : '';

    if ($beratRaw === '' || !is_numeric($beratRaw) || (float)$beratRaw <= 0) {
        $errors[] = 'Berat badan harus diisi dengan angka lebih dari 0.';
    }

    if ($tinggiRaw === '' || !is_numeric($tinggiRaw) || (float)$tinggiRaw <= 0) {
        $errors[] = 'Tinggi badan harus diisi dengan angka lebih dari 0.';
    }

    if (!$errors) {
        $berat = (float)$beratRaw;      
        $tinggiCm = (float)$tinggiRaw;  
        $tinggiM = $tinggiCm / 100.0;   

        if ($tinggiM <= 0) {
            $errors[] = 'Tinggi badan tidak valid.';
        } else {
            $bmi = $berat / ($tinggiM * $tinggiM);

            if ($bmi < 18.5) {
                $category = 'Kurus (Underweight)';
            } elseif ($bmi < 25) {
                $category = 'Normal';
            } elseif ($bmi < 30) {
                $category = 'Kelebihan berat badan (Overweight)';
            } else {
                $category = 'Obesitas';
            }
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kalkulator BMI</title>
    <style>
        :root { --bg:#0f172a; --card:#111827; --text:#e5e7eb; --muted:#9ca3af; --accent:#22c55e; --danger:#ef4444; }
        html, body { height: 100%; }
        body { margin:0; font-family: system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, Noto Sans, Arial, "Apple Color Emoji", "Segoe UI Emoji"; background: linear-gradient(135deg,#0f172a,#111827); color:var(--text); display:grid; place-items:center; }
        .card { width: min(560px, 92vw); background:rgba(17,24,39,.85); border:1px solid rgba(255,255,255,.06); border-radius:14px; box-shadow: 0 10px 30px rgba(0,0,0,.35); overflow:hidden; }
        .card header { padding:22px 22px 10px; border-bottom:1px solid rgba(255,255,255,.06); }
        .card header h1 { margin:0; font-size:22px; letter-spacing:.3px; }
        .card header p { margin:6px 0 0; color:var(--muted); font-size:14px; }
        form { padding:22px; display:grid; gap:16px; }
        .row { display:grid; grid-template-columns: 1fr 1fr; gap:16px; }
        label { display:block; font-size:14px; color:var(--muted); margin-bottom:6px; }
        input[type=number] { width:100%; padding:12px 12px; background:#0b1020; color:var(--text); border:1px solid rgba(255,255,255,.08); border-radius:10px; outline:none; transition: border-color .18s ease, box-shadow .18s ease; }
        input[type=number]:focus { border-color: rgba(34,197,94,.6); box-shadow: 0 0 0 3px rgba(34,197,94,.14); }
        .actions { display:flex; justify-content:flex-end; padding:0 22px 22px; }
        button { background:var(--accent); color:#062e12; border:none; padding:12px 16px; border-radius:10px; cursor:pointer; font-weight:600; letter-spacing:.2px; }
        button:hover { filter:brightness(1.05); }
        .errors { margin:0 22px 16px; padding:12px 14px; border:1px solid rgba(239,68,68,.35); background: rgba(239,68,68,.10); color:#fecaca; border-radius:10px; font-size:14px; }
        .errors ul { margin:6px 0 0 18px; }
        .result { margin:0 22px 22px; padding:16px; border:1px solid rgba(34,197,94,.35); background: rgba(34,197,94,.10); color:#bbf7d0; border-radius:10px; }
        .result h2 { margin:0 0 8px; font-size:18px; }
        .note { color:var(--muted); font-size:12px; margin-top:6px; }
        .footer { padding:12px 22px 20px; color:var(--muted); font-size:12px; }
    </style>
</head>
<body>
<div class="card">
    <header>
        <h1>Kalkulator BMI</h1>
        <p>Masukkan berat (kg) dan tinggi (cm), lalu hitung BMI Anda.</p>
    </header>

    <?php if ($errors): ?>
        <div class="errors">
            <strong>Terjadi kesalahan input:</strong>
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="row">
            <div>
                <label for="berat">Berat Badan (kg)</label>
                <input type="number" id="berat" name="berat" step="0.1" min="0" placeholder="cth: 70.5" value="<?= isset($_POST['berat']) ? htmlspecialchars($_POST['berat'], ENT_QUOTES, 'UTF-8') : '' ?>" required>
            </div>
            <div>
                <label for="tinggi">Tinggi Badan (cm)</label>
                <input type="number" id="tinggi" name="tinggi" step="0.1" min="0" placeholder="cth: 170" value="<?= isset($_POST['tinggi']) ? htmlspecialchars($_POST['tinggi'], ENT_QUOTES, 'UTF-8') : '' ?>" required>
            </div>
        </div>
        <div class="actions">
            <button type="submit">Hitung BMI</button>
        </div>
    </form>

    <?php if ($bmi !== null && !$errors): ?>
        <div class="result">
            <h2>Hasil</h2>
            <p><strong>BMI:</strong> <?= number_format($bmi, 1, ',', '.') ?></p>
            <p><strong>Kategori:</strong> <?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8') ?></p>
            <p class="note">Klasifikasi menggunakan standar WHO (umum):
                &lt;18,5: Kurus • 18,5–24,9: Normal • 25,0–29,9: Overweight • ≥30: Obesitas.
            </p>
        </div>
    <?php endif; ?>

    <div class="footer">
        Tips: Anda dapat menggunakan koma atau titik sebagai pemisah desimal.
    </div>
</div>
</body>
</html>
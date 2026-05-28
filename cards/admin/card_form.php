<?php
require_once '../db.php';

$id   = isset($_GET['id']) ? (int)$_GET['id'] : null;
$card = null;
$erro = '';
$ok   = '';

if ($id) {
    $stmt = db()->prepare("SELECT * FROM cards WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $card = $stmt->get_result()->fetch_assoc();
    if (!$card) { header('Location: index.php'); exit; }
}

$usuarios = db()->query("SELECT id, nome, apelido FROM usuarios ORDER BY nome")->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = [
        'usuario_id'     => (int)($_POST['usuario_id'] ?? 0),
        'codigo'         => trim($_POST['codigo'] ?? ''),
        'nome'           => trim($_POST['nome'] ?? ''),
        'tipo'           => trim($_POST['tipo'] ?? ''),
        'cor_primaria'   => trim($_POST['cor_primaria'] ?? '#a0a0ff'),
        'cor_secundaria' => trim($_POST['cor_secundaria'] ?? '#c0c0ff'),
        'hp'             => (int)($_POST['hp'] ?? 50),
        'categoria'      => trim($_POST['categoria'] ?? ''),
        'altura'         => trim($_POST['altura'] ?? ''),
        'peso'           => trim($_POST['peso'] ?? ''),
        'desc_curta'     => trim($_POST['desc_curta'] ?? ''),
        'desc_longa'     => trim($_POST['desc_longa'] ?? ''),
        'habilidades'    => json_encode(array_filter(array_map('trim', explode(',', $_POST['habilidades'] ?? '')))),
        'fraquezas'      => json_encode(array_filter(array_map('trim', explode(',', $_POST['fraquezas'] ?? '')))),
        'resistencias'   => json_encode(array_filter(array_map('trim', explode(',', $_POST['resistencias'] ?? '')))),
    ];

    $imgAtual = $card['imagem'] ?? null;
    if (!empty($_FILES['imagem']['name'])) {
        $novaImg = uploadImagem($_FILES['imagem'], 'uploads');
        if ($novaImg) {
            $dados['imagem'] = $novaImg;
        } else {
            $erro = 'Erro no upload da imagem. Use JPG, PNG ou WebP até 5MB.';
        }
    } else {
        $dados['imagem'] = $imgAtual;
    }

    if (!$erro) {
        if ($id) {
            // Lógica de UPDATE com array dinâmico em MySQLi
            $cols = array_keys($dados);
            $set = implode(', ', array_map(fn($k) => "$k = ?", $cols));
            $stmt = db()->prepare("UPDATE cards SET $set WHERE id = ?");
            
            $types = str_repeat('s', count($dados)) . 'i'; // MySQLi aceita inteiros passados como string "s", mas o último id é int "i"
            $values = array_values($dados);
            $values[] = $id;
            
            $stmt->bind_param($types, ...$values);
            $stmt->execute();
            $ok = 'Card atualizado com sucesso!';
            $card = array_merge($card, $dados);
        } else {
            // Lógica de INSERT com array dinâmico em MySQLi
            $cols = implode(', ', array_keys($dados));
            $placeholders = implode(', ', array_fill(0, count($dados), '?'));
            $stmt = db()->prepare("INSERT INTO cards ($cols) VALUES ($placeholders)");
            
            $types = str_repeat('s', count($dados));
            $values = array_values($dados);
            
            $stmt->bind_param($types, ...$values);
            $stmt->execute();
            $ok = 'Card criado com sucesso!';
            header('Location: index.php'); exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $id ? 'Editar Card' : 'Novo Card' ?> — PokéCatalog</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;900&family=Noto+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../uploads/style.css">
<link rel="stylesheet" href="./admin.css">
</head>
<body>
<div class="starfield" id="starfield" aria-hidden="true"></div>

<header class="header">
    <div class="header__logo"><?= $id ? '✏ Editar Card' : '＋ Novo Card' ?></div>
    <div class="header__actions">
        <a href="index.php" class="btn btn-outline-sm">← Admin</a>
    </div>
</header>

<main class="admin-main admin-form-layout">
    <div class="admin-form-wrap">
        <?php if ($erro): ?><div class="admin-alert admin-alert--error"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
        <?php if ($ok):   ?><div class="admin-alert admin-alert--ok"><?= htmlspecialchars($ok) ?></div><?php endif; ?>

        <form method="POST" enctype="multipart/form-data" id="card-form">

            <div class="form-group">
                <label>Dono do Card</label>
                <select name="usuario_id" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($usuarios as $u): ?>
                    <option value="<?= $u['id'] ?>" <?= ($card['usuario_id'] ?? '') == $u['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($u['nome']) ?><?= $u['apelido'] ? ' (@'.$u['apelido'].')' : '' ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Código</label>
                    <input type="text" name="codigo" value="<?= htmlspecialchars($card['codigo'] ?? '') ?>" placeholder="001" maxlength="10" required>
                </div>
                <div class="form-group">
                    <label>Nome</label>
                    <input type="text" name="nome" id="f-nome" value="<?= htmlspecialchars($card['nome'] ?? '') ?>" placeholder="Ex: Charmander" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Tipo</label>
                    <input type="text" name="tipo" id="f-tipo" value="<?= htmlspecialchars($card['tipo'] ?? '') ?>" placeholder="fogo, agua, planta, eletrico...">
                </div>
                <div class="form-group">
                    <label>HP (0–120)</label>
                    <input type="number" name="hp" id="f-hp" value="<?= $card['hp'] ?? 50 ?>" min="1" max="120">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Cor Primária</label>
                    <div class="color-pick-wrap">
                        <input type="color" name="cor_primaria" id="f-cor1" value="<?= htmlspecialchars($card['cor_primaria'] ?? '#ff4d00') ?>">
                        <input type="text"  id="f-cor1-hex" value="<?= htmlspecialchars($card['cor_primaria'] ?? '#ff4d00') ?>" maxlength="7" class="color-hex-input">
                    </div>
                </div>
                <div class="form-group">
                    <label>Cor Secundária</label>
                    <div class="color-pick-wrap">
                        <input type="color" name="cor_secundaria" id="f-cor2" value="<?= htmlspecialchars($card['cor_secundaria'] ?? '#ff8c42') ?>">
                        <input type="text"  id="f-cor2-hex" value="<?= htmlspecialchars($card['cor_secundaria'] ?? '#ff8c42') ?>" maxlength="7" class="color-hex-input">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Categoria</label>
                    <input type="text" name="categoria" value="<?= htmlspecialchars($card['categoria'] ?? '') ?>" placeholder="Ex: Lagarto">
                </div>
                <div class="form-group">
                    <label>Altura</label>
                    <input type="text" name="altura" value="<?= htmlspecialchars($card['altura'] ?? '') ?>" placeholder="0.6m">
                </div>
                <div class="form-group">
                    <label>Peso</label>
                    <input type="text" name="peso" value="<?= htmlspecialchars($card['peso'] ?? '') ?>" placeholder="8.5kg">
                </div>
            </div>

            <div class="form-group">
                <label>Descrição Curta <small>(aparece no card)</small></label>
                <textarea name="desc_curta" id="f-desc" rows="2" placeholder="Resumo exibido no card..."><?= htmlspecialchars($card['desc_curta'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label>Descrição Longa <small>(aparece no perfil)</small></label>
                <textarea name="desc_longa" rows="4" placeholder="Descrição completa no perfil..."><?= htmlspecialchars($card['desc_longa'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label>Imagem do Card</label>
                <div class="img-upload-wrap">
                    <input type="file" name="imagem" id="f-img" accept="image/*">
                    <label for="f-img" class="img-upload-label">
                        <?php if ($card['imagem'] ?? null): ?>
                            <img src="../<?= htmlspecialchars($card['imagem']) ?>" id="img-preview" alt="">
                        <?php else: ?>
                            <div id="img-preview-placeholder">📷 Clique para escolher</div>
                            <img id="img-preview" src="" alt="" style="display:none">
                        <?php endif; ?>
                    </label>
                    <p class="form-hint">JPG, PNG ou WebP — máx 5MB. Ou cole uma URL abaixo:</p>
                    <input type="text" name="imagem_url" id="f-img-url" value="" placeholder="https://..." class="form-input-sm">
                </div>
            </div>

            <div class="form-group">
                <label>Habilidades <small>(separe por vírgula)</small></label>
                <input type="text" name="habilidades" value="<?= htmlspecialchars(implode(', ', json_decode($card['habilidades'] ?? '[]', true))) ?>" placeholder="Blaze, Solar Power">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Fraquezas <small>(vírgula)</small></label>
                    <input type="text" name="fraquezas" value="<?= htmlspecialchars(implode(', ', json_decode($card['fraquezas'] ?? '[]', true))) ?>" placeholder="Água, Pedra">
                </div>
                <div class="form-group">
                    <label>Resistências <small>(vírgula)</small></label>
                    <input type="text" name="resistencias" value="<?= htmlspecialchars(implode(', ', json_decode($card['resistencias'] ?? '[]', true))) ?>" placeholder="Fogo, Planta">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><?= $id ? '💾 Salvar Alterações' : '✨ Criar Card' ?></button>
                <a href="index.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>

    <div class="admin-preview-wrap">
        <div class="admin-preview-label">Preview em tempo real</div>
        <div
            class="card card-preview-live"
            id="live-card"
            style="--type-1:<?= htmlspecialchars($card['cor_primaria'] ?? '#ff4d00') ?>;--type-2:<?= htmlspecialchars($card['cor_secundaria'] ?? '#ff8c42') ?>;--type-bg:<?= htmlspecialchars($card['cor_primaria'] ?? '#ff4d00') ?>1f;--type-border:<?= htmlspecialchars($card['cor_primaria'] ?? '#ff4d00') ?>59"
        >
            <div class="card__glow"></div>
            <div class="card__glare"></div>
            <div class="card__img-wrap">
                <span class="card__id" id="prev-codigo">#<?= htmlspecialchars($card['codigo'] ?? '000') ?></span>
                <span class="card__badge" id="prev-tipo"><?= ucfirst(htmlspecialchars($card['tipo'] ?? 'tipo')) ?></span>
                <img class="card__img" id="prev-img"
                    src="<?= htmlspecialchars($card['imagem'] ?? 'https://placehold.co/130x130/1e1e35/a0a0c0?text=?') ?>"
                    alt="" onerror="this.src='https://placehold.co/130x130/1e1e35/a0a0c0?text=?'">
            </div>
            <div class="card__body">
                <div class="card__name" id="prev-nome"><?= htmlspecialchars($card['nome'] ?? 'Nome do Card') ?></div>
                <p class="card__desc" id="prev-desc"><?= htmlspecialchars($card['desc_curta'] ?? 'Descrição aparece aqui...') ?></p>
                <div class="card__stats">
                    <div class="card__hp"><span id="prev-hp"><?= $card['hp'] ?? 50 ?></span> HP</div>
                    <div class="card__hp-bar">
                        <div class="card__hp-fill" id="prev-hp-fill" style="width:<?= min(100, round(($card['hp'] ?? 50)/120*100)) ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
(function(){ const sf=document.getElementById('starfield'); for(let i=0;i<80;i++){const s=document.createElement('div');s.className='star';const z=Math.random()*2+.5;s.style.cssText=`width:${z}px;height:${z}px;left:${Math.random()*100}%;top:${Math.random()*100}%;--d:${2+Math.random()*5}s;animation-delay:${Math.random()*5}s;opacity:${(.1+Math.random()*.4).toFixed(2)}`;sf.appendChild(s);}})();

const lc  = document.getElementById('live-card');

function hexToRgba(hex, a) {
    const r = parseInt(hex.slice(1,3),16), g = parseInt(hex.slice(3,5),16), b = parseInt(hex.slice(5,7),16);
    return `rgba(${r},${g},${b},${a})`;
}
function updateCores() {
    const c1 = document.getElementById('f-cor1').value;
    const c2 = document.getElementById('f-cor2').value;
    lc.style.setProperty('--type-1', c1);
    lc.style.setProperty('--type-2', c2);
    lc.style.setProperty('--type-bg', hexToRgba(c1, 0.12));
    lc.style.setProperty('--type-border', hexToRgba(c1, 0.35));
    document.getElementById('f-cor1-hex').value = c1;
    document.getElementById('f-cor2-hex').value = c2;
}

// Sync color picker <-> hex input
['f-cor1','f-cor2'].forEach(id => {
    document.getElementById(id).addEventListener('input', updateCores);
});
document.getElementById('f-cor1-hex').addEventListener('input', function() {
    if (/^#[0-9a-fA-F]{6}$/.test(this.value)) { document.getElementById('f-cor1').value = this.value; updateCores(); }
});
document.getElementById('f-cor2-hex').addEventListener('input', function() {
    if (/^#[0-9a-fA-F]{6}$/.test(this.value)) { document.getElementById('f-cor2').value = this.value; updateCores(); }
});

// Sync campos de texto -> preview
const binds = [
    ['f-nome', 'prev-nome'],
    ['f-tipo', 'prev-tipo'],
    ['f-desc', 'prev-desc'],
];
binds.forEach(([src, dst]) => {
    const el = document.getElementById(src);
    if (el) el.addEventListener('input', function() {
        document.getElementById(dst).textContent = this.value || '—';
    });
});

// Código
document.querySelector('[name="codigo"]').addEventListener('input', function() {
    document.getElementById('prev-codigo').textContent = '#' + (this.value || '000');
});

// HP
document.getElementById('f-hp').addEventListener('input', function() {
    const v = Math.min(120, Math.max(0, parseInt(this.value) || 0));
    document.getElementById('prev-hp').textContent = v;
    document.getElementById('prev-hp-fill').style.width = Math.round(v/120*100) + '%';
});

// Preview imagem via upload
document.getElementById('f-img').addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        const img = document.getElementById('img-preview');
        img.src = e.target.result;
        img.style.display = 'block';
        const ph = document.getElementById('img-preview-placeholder');
        if (ph) ph.style.display = 'none';
        document.getElementById('prev-img').src = e.target.result;
    };
    reader.readAsDataURL(file);
});

// Preview imagem via URL
document.getElementById('f-img-url').addEventListener('input', function() {
    if (this.value) document.getElementById('prev-img').src = this.value;
});
</script>
</body>
</html>
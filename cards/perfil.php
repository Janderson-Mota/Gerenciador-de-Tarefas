<?php
require_once 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Busca usuário via MySQLi (bind_param com "?")
$stmt = db()->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();

if (!$usuario) { header('Location: index.php'); exit; }

// Busca cards do usuário
$stmtCards = db()->prepare("SELECT * FROM cards WHERE usuario_id = ? ORDER BY criado_em DESC");
$stmtCards->bind_param("i", $id);
$stmtCards->execute();
$cards = $stmtCards->get_result()->fetch_all(MYSQLI_ASSOC);

// Card específico para exibir no destaque
$cardId   = isset($_GET['card']) ? (int)$_GET['card'] : null;
$cardDest = null;
if ($cardId) {
    $stmtDest = db()->prepare("SELECT * FROM cards WHERE id = ? AND usuario_id = ?");
    $stmtDest->bind_param("ii", $cardId, $id);
    $stmtDest->execute();
    $cardDest = $stmtDest->get_result()->fetch_assoc();
}
if (!$cardDest && count($cards) > 0) $cardDest = $cards[0];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($usuario['nome']) ?> — PokéCatalog</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;900&family=Noto+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="./uploads/style.css">
<?php if ($cardDest): ?>
<style>
    :root {
        --profile-cor1: <?= htmlspecialchars($cardDest['cor_primaria'] ?? '#a0a0ff') ?>;
        --profile-cor2: <?= htmlspecialchars($cardDest['cor_secundaria'] ?? '#c0c0ff') ?>;
    }
</style>
<?php endif; ?>
</head>
<body class="profile-page">

<div class="starfield" id="starfield" aria-hidden="true"></div>

<div class="profile-standalone" style="<?= $cardDest ? '--type-1:'.htmlspecialchars($cardDest['cor_primaria']).'  ;--type-2:'.htmlspecialchars($cardDest['cor_secundaria']).';--type-bg:'.htmlspecialchars($cardDest['cor_primaria']).'1f;--type-border:'.htmlspecialchars($cardDest['cor_primaria']).'59' : '' ?>">

    <div class="profile-header">
        <div class="profile-header__text">
            <a href="index.php" class="profile-back">← Voltar ao Catálogo</a>
            <?php if ($usuario['avatar']): ?>
                <img src="<?= htmlspecialchars($usuario['avatar']) ?>" class="profile-avatar" alt="Avatar de <?= htmlspecialchars($usuario['nome']) ?>">
            <?php else: ?>
                <div class="profile-avatar profile-avatar--placeholder"><?= mb_substr($usuario['nome'], 0, 1) ?></div>
            <?php endif; ?>
            <h1 class="profile-header__name"><?= htmlspecialchars($usuario['nome']) ?></h1>
            <?php if ($usuario['apelido']): ?>
                <div class="profile-header__apelido">@<?= htmlspecialchars($usuario['apelido']) ?></div>
            <?php endif; ?>
            <?php if ($usuario['cargo']): ?>
                <span class="profile-header__badge"><?= htmlspecialchars($usuario['cargo']) ?></span>
            <?php endif; ?>
        </div>
        <?php if ($cardDest): ?>
        <img
            class="profile-header__img"
            src="<?= htmlspecialchars($cardDest['imagem'] ?? '') ?>"
            alt="Card em destaque"
            onerror="this.style.display='none'"
        >
        <?php endif; ?>
    </div>

    <div class="profile-content">

        <?php if ($usuario['bio']): ?>
        <section class="profile-section">
            <div class="profile-section-title">Sobre</div>
            <p class="profile-desc"><?= htmlspecialchars($usuario['bio']) ?></p>
        </section>
        <?php endif; ?>

        <?php if ($cardDest): ?>
        <section class="profile-section">
            <div class="profile-section-title">Card em Destaque</div>
            <div class="profile-card-destaque">
                <div class="profile-card-destaque__img-wrap">
                    <img src="<?= htmlspecialchars($cardDest['imagem'] ?? '') ?>" alt="" onerror="this.src='https://placehold.co/120x120/1e1e35/a0a0c0?text=?'">
                </div>
                <div class="profile-card-destaque__info">
                    <div class="profile-card-destaque__codigo">#<?= htmlspecialchars($cardDest['codigo']) ?></div>
                    <div class="profile-card-destaque__nome"><?= htmlspecialchars($cardDest['nome']) ?></div>
                    <span class="profile-header__badge" style="margin-bottom:12px"><?= ucfirst(htmlspecialchars($cardDest['tipo'])) ?></span>
                    <p style="font-size:0.9rem;color:var(--text2);line-height:1.7"><?= htmlspecialchars($cardDest['desc_longa'] ?? $cardDest['desc_curta'] ?? '') ?></p>
                </div>
            </div>

            <div class="profile-stats-grid" style="margin-top:var(--sp3)">
                <div class="profile-stat"><div class="profile-stat__val"><?= $cardDest['hp'] ?></div><div class="profile-stat__label">HP</div></div>
                <?php if ($cardDest['categoria']): ?><div class="profile-stat"><div class="profile-stat__val"><?= htmlspecialchars($cardDest['categoria']) ?></div><div class="profile-stat__label">Categoria</div></div><?php endif; ?>
                <?php if ($cardDest['altura']): ?><div class="profile-stat"><div class="profile-stat__val"><?= htmlspecialchars($cardDest['altura']) ?></div><div class="profile-stat__label">Altura</div></div><?php endif; ?>
                <?php if ($cardDest['peso']): ?><div class="profile-stat"><div class="profile-stat__val"><?= htmlspecialchars($cardDest['peso']) ?></div><div class="profile-stat__label">Peso</div></div><?php endif; ?>
            </div>

            <?php
            $hab = json_decode($cardDest['habilidades'] ?? '[]', true);
            $fra = json_decode($cardDest['fraquezas']   ?? '[]', true);
            $res = json_decode($cardDest['resistencias'] ?? '[]', true);
            ?>
            <?php if ($hab || $fra || $res): ?>
            <div class="profile-two-col" style="margin-top:var(--sp3)">
                <?php if ($hab): ?>
                <section class="profile-section">
                    <div class="profile-section-title">Habilidades</div>
                    <ul class="profile-tags"><?php foreach ($hab as $h): ?><li class="profile-tag profile-tag--ability"><?= htmlspecialchars($h) ?></li><?php endforeach; ?></ul>
                </section>
                <?php endif; ?>
                <?php if ($fra): ?>
                <section class="profile-section">
                    <div class="profile-section-title">Fraquezas</div>
                    <ul class="profile-tags"><?php foreach ($fra as $f): ?><li class="profile-tag profile-tag--weak"><?= htmlspecialchars($f) ?></li><?php endforeach; ?></ul>
                </section>
                <?php endif; ?>
            </div>
            <?php if ($res): ?>
            <section class="profile-section" style="margin-top:0">
                <div class="profile-section-title">Resistências</div>
                <ul class="profile-tags"><?php foreach ($res as $r): ?><li class="profile-tag profile-tag--resist"><?= htmlspecialchars($r) ?></li><?php endforeach; ?></ul>
            </section>
            <?php endif; ?>
            <?php endif; ?>
        </section>
        <?php endif; ?>

        <?php if (count($cards) > 1): ?>
        <section class="profile-section">
            <div class="profile-section-title">Todos os Cards (<?= count($cards) ?>)</div>
            <div class="profile-cards-grid">
                <?php foreach ($cards as $c): ?>
                <a
                    href="perfil.php?id=<?= $id ?>&card=<?= $c['id'] ?>"
                    class="profile-card-thumb <?= $cardDest && $c['id'] == $cardDest['id'] ? 'active' : '' ?>"
                    style="--thumb-cor:<?= htmlspecialchars($c['cor_primaria'] ?? '#a0a0ff') ?>"
                >
                    <img src="<?= htmlspecialchars($c['imagem'] ?? '') ?>" alt="<?= htmlspecialchars($c['nome']) ?>" onerror="this.src='https://placehold.co/60x60/1e1e35/a0a0c0?text=?'">
                    <span><?= htmlspecialchars($c['nome']) ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <div class="profile-section">
            <a href="index.php" class="btn btn-secondary">← Voltar ao Catálogo</a>
            <a href="admin/perfil_edit.php?id=<?= $id ?>" class="btn btn-outline-sm" style="margin-left:8px">✏ Editar Perfil</a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const sf = document.getElementById('starfield');
    for (let i = 0; i < 120; i++) {
        const s = document.createElement('div');
        s.className = 'star';
        const size = Math.random() * 2 + 0.5;
        s.style.cssText = `width:${size}px;height:${size}px;left:${Math.random()*100}%;top:${Math.random()*100}%;--d:${2+Math.random()*5}s;animation-delay:${Math.random()*5}s;opacity:${0.1+Math.random()*0.4}`;
        sf.appendChild(s);
    }
});
</script>
</body>
</html>
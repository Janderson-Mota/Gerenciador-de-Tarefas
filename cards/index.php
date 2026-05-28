<?php
require_once './db.php';

$cards = db()->query("
    SELECT c.*, u.nome AS usuario_nome, u.apelido AS usuario_apelido, u.avatar AS usuario_avatar
    FROM cards c
    JOIN usuarios u ON u.id = c.usuario_id
    ORDER BY c.criado_em DESC
")->fetch_all(MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PokéCatalog — Coleção Virtual</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;900&family=Noto+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="./uploads/style.css">
</head>
<body>

<div class="starfield" id="starfield" aria-hidden="true"></div>

<header class="header">
    <div class="header__logo">PokéCatalog</div>
    <div class="header__sub">Coleção Virtual Interativa</div>
    <div class="header__actions">
        <span class="header__count"><?= count($cards) ?> cartas na coleção</span>
        <a href="admin/" class="btn btn-outline-sm">⚙ Admin</a>
    </div>
</header>

<main class="catalog">
    <div class="grid" id="grid" role="list">

        <?php foreach ($cards as $i => $c):
            $cor1 = htmlspecialchars($c['cor_primaria']   ?? '#a0a0ff');
            $cor2 = htmlspecialchars($c['cor_secundaria'] ?? '#c0c0ff');
            $pct  = min(100, round($c['hp'] / 120 * 100));
            $tipo = htmlspecialchars($c['tipo']);
            $tipoLabel = ucfirst($tipo);
            $tipoClass = in_array($tipo, ['fogo','agua','planta','eletrico']) ? "card-{$tipo}" : 'card-custom';
        ?>
        <article
            class="card <?= $tipoClass ?>"
            tabindex="0"
            role="listitem"
            aria-label="<?= htmlspecialchars($c['nome']) ?>, tipo <?= $tipoLabel ?>, HP <?= $c['hp'] ?>"
            data-id="<?= htmlspecialchars($c['id']) ?>"
            data-codigo="<?= htmlspecialchars($c['codigo']) ?>"
            data-nome="<?= htmlspecialchars($c['nome']) ?>"
            data-tipo="<?= $tipo ?>"
            data-hp="<?= (int)$c['hp'] ?>"
            data-desc="<?= htmlspecialchars($c['desc_curta'] ?? '') ?>"
            data-img="<?= htmlspecialchars($c['imagem'] ?? '') ?>"
            data-categoria="<?= htmlspecialchars($c['categoria'] ?? '') ?>"
            data-altura="<?= htmlspecialchars($c['altura'] ?? '') ?>"
            data-peso="<?= htmlspecialchars($c['peso'] ?? '') ?>"
            data-cor1="<?= $cor1 ?>"
            data-cor2="<?= $cor2 ?>"
            data-usuario="<?= htmlspecialchars($c['usuario_apelido'] ?? $c['usuario_nome']) ?>"
            data-usuario-id="<?= (int)$c['usuario_id'] ?>"
            <?php if ($tipoClass === 'card-custom'): ?>
            style="--type-1:<?= $cor1 ?>;--type-2:<?= $cor2 ?>;--type-bg:<?= $cor1 ?>1f;--type-border:<?= $cor1 ?>59;"
            <?php endif; ?>
            style="animation-delay: <?= $i * 0.07 ?>s"
        >
            <div class="card__glow" aria-hidden="true"></div>
            <div class="card__glare" aria-hidden="true"></div>
            <div class="card__img-wrap">
                <span class="card__id">#<?= htmlspecialchars($c['codigo']) ?></span>
                <span class="card__badge"><?= $tipoLabel ?></span>
                <img
                    class="card__img"
                    src="<?= htmlspecialchars($c['imagem'] ?? 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/0.png') ?>"
                    alt="Ilustração de <?= htmlspecialchars($c['nome']) ?>"
                    loading="lazy"
                    width="130" height="130"
                    onerror="this.src='https://placehold.co/130x130/1e1e35/a0a0c0?text=?'"
                >
            </div>
            <div class="card__body">
                <div class="card__owner">
                    <?php if ($c['usuario_avatar']): ?>
                        <img src="<?= htmlspecialchars($c['usuario_avatar']) ?>" class="card__owner-avatar" alt="">
                    <?php endif; ?>
                    <span><?= htmlspecialchars($c['usuario_apelido'] ?? $c['usuario_nome']) ?></span>
                </div>
                <div class="card__name"><?= htmlspecialchars($c['nome']) ?></div>
                <p class="card__desc"><?= htmlspecialchars($c['desc_curta'] ?? '') ?></p>
                <div class="card__stats">
                    <div class="card__hp"><span><?= $c['hp'] ?></span> HP</div>
                    <div class="card__hp-bar" role="progressbar" aria-valuenow="<?= $c['hp'] ?>" aria-valuemin="0" aria-valuemax="120">
                        <div class="card__hp-fill" style="width:<?= $pct ?>%"></div>
                    </div>
                </div>
            </div>
        </article>
        <?php endforeach; ?>

        <a href="admin/card_form.php" class="card card-create" role="listitem" aria-label="Criar seu card">
            <div class="card-create__inner">
                <div class="card-create__icon">＋</div>
                <div class="card-create__label">Criar seu Card</div>
                <div class="card-create__sub">Personalize tudo</div>
            </div>
        </a>

    </div>
</main>

<div class="modal-overlay" id="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="modal-title" aria-hidden="true">
    <div class="modal" id="modal">
        <button class="btn-close" id="modal-close" aria-label="Fechar modal">✕</button>
        <div class="modal__header">
            <div class="modal__img-wrap">
                <img class="modal__img" id="modal-img" src="" alt="">
            </div>
            <div class="modal__meta">
                <div class="modal__id" id="modal-id"></div>
                <h2 class="modal__name" id="modal-title"></h2>
                <span class="modal__badge" id="modal-badge"></span>
                <div class="modal__owner" id="modal-owner"></div>
            </div>
        </div>
        <div class="modal__body">
            <p class="modal__desc" id="modal-desc"></p>
            <div class="modal__stat-grid">
                <div class="modal__stat"><div class="modal__stat-label">Tipo</div><div class="modal__stat-val" id="modal-tipo"></div></div>
                <div class="modal__stat"><div class="modal__stat-label">Categoria</div><div class="modal__stat-val" id="modal-cat"></div></div>
                <div class="modal__stat"><div class="modal__stat-label">Altura</div><div class="modal__stat-val" id="modal-altura"></div></div>
                <div class="modal__stat"><div class="modal__stat-label">Peso</div><div class="modal__stat-val" id="modal-peso"></div></div>
            </div>
            <div class="modal__hp-section">
                <div class="modal__hp-label"><span>HP</span><span id="modal-hp-val"></span></div>
                <div class="modal__hp-bar" role="progressbar"><div class="modal__hp-fill" id="modal-hp-fill"></div></div>
            </div>
            <div class="modal__actions">
                <a class="btn btn-primary" id="modal-profile-btn" href="#">Ver Perfil Completo →</a>
                <button class="btn btn-secondary" id="modal-close-btn">Fechar</button>
            </div>
        </div>
    </div>
</div>

<script src="./uploads/script.js"></script>
</body>
</html>
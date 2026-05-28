<?php
require_once '../db.php';

// Deletar card
if (isset($_GET['del_card'])) {
    $idCard = (int)$_GET['del_card'];
    $stmt = db()->prepare("DELETE FROM cards WHERE id = ?");
    $stmt->bind_param("i", $idCard);
    $stmt->execute();
    header('Location: index.php'); exit;
}

// Deletar usuário
if (isset($_GET['del_user'])) {
    $idUser = (int)$_GET['del_user'];
    $stmt = db()->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $idUser);
    $stmt->execute();
    header('Location: index.php'); exit;
}

// Extrair listagens
$usuarios = db()->query("SELECT u.*, COUNT(c.id) AS total_cards FROM usuarios u LEFT JOIN cards c ON c.usuario_id = u.id GROUP BY u.id ORDER BY u.criado_em DESC")->fetch_all(MYSQLI_ASSOC);
$cards    = db()->query("SELECT c.*, u.nome AS usuario_nome FROM cards c JOIN usuarios u ON u.id = c.usuario_id ORDER BY c.criado_em DESC")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin — PokéCatalog</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;900&family=Noto+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../uploads/style.css">
<link rel="stylesheet" href="./admin.css">
</head>
<body>
<div class="starfield" id="starfield" aria-hidden="true"></div>

<header class="header">
    <div class="header__logo">⚙ Admin</div>
    <div class="header__sub">PokéCatalog — Painel de Controle</div>
    <div class="header__actions">
        <a href="../index.php" class="btn btn-outline-sm">← Catálogo</a>
    </div>
</header>

<main class="admin-main">

    <div class="admin-actions">
        <a href="card_form.php" class="admin-btn-new">＋ Novo Card</a>
        <a href="perfil_edit.php" class="admin-btn-new admin-btn-new--secondary">＋ Novo Usuário</a>
    </div>

    <section class="admin-section">
        <div class="admin-section__title">Usuários (<?= count($usuarios) ?>)</div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead><tr><th>Avatar</th><th>Nome</th><th>Apelido</th><th>Cargo</th><th>Cards</th><th>Ações</th></tr></thead>
                <tbody>
                <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td>
                        <?php if ($u['avatar']): ?>
                            <img src="../<?= htmlspecialchars($u['avatar']) ?>" class="admin-avatar" alt="">
                        <?php else: ?>
                            <div class="admin-avatar admin-avatar--placeholder"><?= mb_substr($u['nome'],0,1) ?></div>
                        <?php endif; ?>
                    </td>
                    <td><strong><?= htmlspecialchars($u['nome']) ?></strong></td>
                    <td><?= htmlspecialchars($u['apelido'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($u['cargo'] ?? '—') ?></td>
                    <td><span class="admin-badge"><?= $u['total_cards'] ?></span></td>
                    <td class="admin-actions-cell">
                        <a href="perfil_edit.php?id=<?= $u['id'] ?>" class="admin-link">✏ Editar</a>
                        <a href="../perfil.php?id=<?= $u['id'] ?>" class="admin-link" target="_blank">👁 Ver</a>
                        <a href="?del_user=<?= $u['id'] ?>" class="admin-link admin-link--danger" onclick="return confirm('Deletar usuário e todos seus cards?')">✕</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="admin-section">
        <div class="admin-section__title">Cards (<?= count($cards) ?>)</div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead><tr><th>Imagem</th><th>Código</th><th>Nome</th><th>Tipo</th><th>Cor</th><th>HP</th><th>Dono</th><th>Ações</th></tr></thead>
                <tbody>
                <?php foreach ($cards as $c): ?>
                <tr>
                    <td><img src="../<?= htmlspecialchars($c['imagem'] ?? '') ?>" class="admin-thumb" alt="" onerror="this.src='https://placehold.co/48x48/1e1e35/a0a0c0?text=?'"></td>
                    <td><code>#<?= htmlspecialchars($c['codigo']) ?></code></td>
                    <td><strong><?= htmlspecialchars($c['nome']) ?></strong></td>
                    <td><?= htmlspecialchars($c['tipo']) ?></td>
                    <td>
                        <span class="admin-cor-swatch" style="background:<?= htmlspecialchars($c['cor_primaria']) ?>"></span>
                        <span class="admin-cor-swatch" style="background:<?= htmlspecialchars($c['cor_secundaria']) ?>"></span>
                    </td>
                    <td><?= $c['hp'] ?></td>
                    <td><?= htmlspecialchars($c['usuario_nome']) ?></td>
                    <td class="admin-actions-cell">
                        <a href="card_form.php?id=<?= $c['id'] ?>" class="admin-link">✏ Editar</a>
                        <a href="?del_card=<?= $c['id'] ?>" class="admin-link admin-link--danger" onclick="return confirm('Deletar este card?')">✕</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<script>
(function(){ const sf=document.getElementById('starfield'); for(let i=0;i<80;i++){const s=document.createElement('div');s.className='star';const z=Math.random()*2+.5;s.style.cssText=`width:${z}px;height:${z}px;left:${Math.random()*100}%;top:${Math.random()*100}%;--d:${2+Math.random()*5}s;animation-delay:${Math.random()*5}s;opacity:${(.1+Math.random()*.4).toFixed(2)}`;sf.appendChild(s);}})();
</script>
</body>
</html>
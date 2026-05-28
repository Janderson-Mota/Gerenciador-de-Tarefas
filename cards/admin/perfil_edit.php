
<?php
require_once '../db.php';

$id      = isset($_GET['id']) ? (int)$_GET['id'] : null;
$usuario = null;
$erro    = '';
$ok      = '';

if ($id) {
    $stmt = db()->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $usuario = $stmt->get_result()->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = [
        'nome'    => trim($_POST['nome']    ?? ''),
        'apelido' => trim($_POST['apelido'] ?? '') ?: null,
        'email'   => trim($_POST['email']   ?? ''),
        'bio'     => trim($_POST['bio']     ?? '') ?: null,
        'cargo'   => trim($_POST['cargo']   ?? '') ?: null,
    ];

    if (!$dados['nome'] || !$dados['email']) {
        $erro = 'Nome e e-mail são obrigatórios.';
    } else {
        $avatarAtual = $usuario['avatar'] ?? null;
        if (!empty($_FILES['avatar']['name'])) {
            $novoAvatar = uploadImagem($_FILES['avatar'], 'uploads');
            $dados['avatar'] = $novoAvatar ?: $avatarAtual;
        } else {
            $dados['avatar'] = $avatarAtual;
        }

        try {
            if ($id) {
                // UPDATE no mysqli
                $cols = array_keys($dados);
                $set = implode(', ', array_map(fn($k) => "$k = ?", $cols));
                $stmt = db()->prepare("UPDATE usuarios SET $set WHERE id = ?");
                
                $types = str_repeat('s', count($dados)) . 'i';
                $values = array_values($dados);
                $values[] = $id;
                
                $stmt->bind_param($types, ...$values);
                $stmt->execute();
                $ok      = 'Perfil atualizado com sucesso!';
                $usuario = array_merge($usuario, $dados);
            } else {
                // INSERT no mysqli
                $cols = implode(', ', array_keys($dados));
                $placeholders = implode(', ', array_fill(0, count($dados), '?'));
                $stmt = db()->prepare("INSERT INTO usuarios ($cols) VALUES ($placeholders)");
                
                $types = str_repeat('s', count($dados));
                $values = array_values($dados);
                
                $stmt->bind_param($types, ...$values);
                $stmt->execute();
                
                $newId = db()->insert_id;
                header("Location: perfil_edit.php?id={$newId}&criado=1"); exit;
            }
        } catch (\mysqli_sql_exception $e) {
            // Se houver conflito de UNIQUE no e-mail
            $erro = 'E-mail já cadastrado ou erro no banco de dados.';
        } catch (\Exception $e) {
            $erro = 'Ocorreu um erro inesperado ao salvar o perfil.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $id ? 'Editar Perfil' : 'Novo Usuário' ?> — PokéCatalog</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;900&family=Noto+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../uploads/style.css">
<link rel="stylesheet" href="./admin.css">
</head>
<body>
<div class="starfield" id="starfield" aria-hidden="true"></div>

<header class="header">
    <div class="header__logo"><?= $id ? '✏ Editar Perfil' : '＋ Novo Usuário' ?></div>
    <div class="header__actions">
        <a href="index.php" class="btn btn-outline-sm">← Admin</a>
        <?php if ($id): ?>
        <a href="../perfil.php?id=<?= $id ?>" class="btn btn-outline-sm" target="_blank">👁 Ver Perfil</a>
        <?php endif; ?>
    </div>
</header>

<main class="admin-main admin-form-layout admin-form-layout--single">

    <div class="admin-form-wrap">
        <?php if (isset($_GET['criado'])): ?>
            <div class="admin-alert admin-alert--ok">✅ Usuário criado com sucesso!</div>
        <?php endif; ?>
        <?php if ($erro): ?><div class="admin-alert admin-alert--error"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
        <?php if ($ok):   ?><div class="admin-alert admin-alert--ok"><?= htmlspecialchars($ok) ?></div><?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group form-group--center">
                <div class="avatar-upload-wrap">
                    <?php if ($usuario['avatar'] ?? null): ?>
                        <img src="../<?= htmlspecialchars($usuario['avatar']) ?>" class="avatar-preview" id="avatar-preview" alt="">
                    <?php else: ?>
                        <div class="avatar-preview avatar-preview--placeholder" id="avatar-preview"><?= $usuario ? mb_substr($usuario['nome'],0,1) : '?' ?></div>
                    <?php endif; ?>
                    <label for="f-avatar" class="avatar-upload-btn">📷 Trocar foto</label>
                    <input type="file" name="avatar" id="f-avatar" accept="image/*" style="display:none">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Nome Completo *</label>
                    <input type="text" name="nome" value="<?= htmlspecialchars($usuario['nome'] ?? '') ?>" placeholder="Seu nome" required>
                </div>
                <div class="form-group">
                    <label>Apelido</label>
                    <input type="text" name="apelido" value="<?= htmlspecialchars($usuario['apelido'] ?? '') ?>" placeholder="@handle">
                </div>
            </div>

            <div class="form-group">
                <label>E-mail *</label>
                <input type="email" name="email" value="<?= htmlspecialchars($usuario['email'] ?? '') ?>" placeholder="email@exemplo.com" required>
            </div>

            <div class="form-group">
                <label>Cargo / Título</label>
                <input type="text" name="cargo" value="<?= htmlspecialchars($usuario['cargo'] ?? '') ?>" placeholder="Ex: Treinador Pokémon, Designer, Dev...">
            </div>

            <div class="form-group">
                <label>Bio</label>
                <textarea name="bio" rows="4" placeholder="Fale um pouco sobre você..."><?= htmlspecialchars($usuario['bio'] ?? '') ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><?= $id ? '💾 Salvar' : '✨ Criar Usuário' ?></button>
                <a href="index.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>

        <?php if ($id): ?>
        <?php
        $stmtCards = db()->prepare("SELECT * FROM cards WHERE usuario_id = ? ORDER BY criado_em DESC");
        $stmtCards->bind_param("i", $id);
        $stmtCards->execute();
        $cards = $stmtCards->get_result()->fetch_all(MYSQLI_ASSOC);
        ?>
        <?php if ($cards): ?>
        <div class="admin-section" style="margin-top:var(--sp4)">
            <div class="admin-section__title">Cards deste usuário (<?= count($cards) ?>)
                <a href="card_form.php?usuario=<?= $id ?>" class="admin-link" style="margin-left:auto">＋ Novo card</a>
            </div>
            <div class="profile-cards-grid">
                <?php foreach ($cards as $c): ?>
                <a href="card_form.php?id=<?= $c['id'] ?>" class="profile-card-thumb" style="--thumb-cor:<?= htmlspecialchars($c['cor_primaria'] ?? '#a0a0ff') ?>">
                    <img src="../<?= htmlspecialchars($c['imagem'] ?? '') ?>" alt="" onerror="this.src='https://placehold.co/60x60/1e1e35/a0a0c0?text=?'">
                    <span><?= htmlspecialchars($c['nome']) ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</main>

<script>
(function(){ const sf=document.getElementById('starfield'); for(let i=0;i<80;i++){const s=document.createElement('div');s.className='star';const z=Math.random()*2+.5;s.style.cssText=`width:${z}px;height:${z}px;left:${Math.random()*100}%;top:${Math.random()*100}%;--d:${2+Math.random()*5}s;animation-delay:${Math.random()*5}s;opacity:${(.1+Math.random()*.4).toFixed(2)}`;sf.appendChild(s);}})();

// Preview avatar
document.getElementById('f-avatar').addEventListener('change', function() {
    const file = this.files[0]; if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        const el = document.getElementById('avatar-preview');
        if (el.tagName === 'IMG') { el.src = e.target.result; }
        else {
            const img = document.createElement('img');
            img.src = e.target.result; img.className = 'avatar-preview'; img.id = 'avatar-preview';
            el.replaceWith(img);
        }
    };
    reader.readAsDataURL(file);
});
</script>
</body>
</html>
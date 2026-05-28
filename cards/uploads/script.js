/**
 * PokéCatalog — script.js
 * 1. Starfield animado
 * 2. Efeito Tilt 3D com eixos independentes + inércia elástica
 * 3. Efeito Glare holográfico (foil) via --mx / --my
 * 4. Sombra dinâmica via --shX / --shY
 * 5. Modal com focus trap e fechamento por Esc
 */

/* ─── 1. Starfield ─────────────────────────────────────────── */
(function buildStars() {
    const sf = document.getElementById('starfield');
    if (!sf) return;
    for (let i = 0; i < 120; i++) {
        const s = document.createElement('div');
        s.className = 'star';
        const size = Math.random() * 2 + 0.5;
        s.style.cssText = `width:${size}px;height:${size}px;left:${Math.random() * 100}%;top:${Math.random() * 100}%;--d:${2 + Math.random() * 5}s;animation-delay:${Math.random() * 5}s;opacity:${(0.1 + Math.random() * 0.4).toFixed(2)}`;
        sf.appendChild(s);
    }
})();

/* ─── 2, 3 & 4. Tilt 3D + Glare + Sombra Dinâmica ─────────── */
const MAX_TILT_X = 10; // graus no eixo vertical
const MAX_TILT_Y = 14; // graus no eixo horizontal

function handleTilt(e) {
    const card = this;
    const rect = card.getBoundingClientRect();

    // Distância do cursor ao centro da carta
    const dx = e.clientX - (rect.left + rect.width  / 2);
    const dy = e.clientY - (rect.top  + rect.height / 2);

    // Ângulos de rotação (eixos independentes)
    const rotY = +(dx / rect.width  * MAX_TILT_Y).toFixed(2);
    const rotX = +(-dy / rect.height * MAX_TILT_X).toFixed(2);

    // Posição do cursor em % para o glare holográfico
    const mx = ((e.clientX - rect.left) / rect.width  * 100).toFixed(2);
    const my = ((e.clientY - rect.top)  / rect.height * 100).toFixed(2);

    // Deslocamento da sombra oposto ao tilt (reforça profundidade)
    const shX = +(-dx / 12).toFixed(2);
    const shY = +(-dy / 12).toFixed(2);

    // Transição rápida durante o hover para sensação de peso/inércia
    card.style.transition = 'transform 0.1s ease-out, box-shadow 0.1s ease-out';
    card.style.transform  = `perspective(1000px) rotateX(${rotX}deg) rotateY(${rotY}deg) scale3d(1.02, 1.02, 1.02)`;

    card.style.setProperty('--mx',  `${mx}%`);
    card.style.setProperty('--my',  `${my}%`);
    card.style.setProperty('--shX', `${shX}px`);
    card.style.setProperty('--shY', `${shY}px`);
}

function resetTilt() {
    // Retorno suave com efeito elástico ao soltar o mouse
    this.style.transition = 'transform 0.5s cubic-bezier(0.25, 1, 0.5, 1), box-shadow 0.5s ease';
    this.style.transform  = 'perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)';
    this.style.setProperty('--shX', '0px');
    this.style.setProperty('--shY', '8px');
    this.style.setProperty('--mx',  '50%');
    this.style.setProperty('--my',  '50%');
}

/* ─── 5. Modal ──────────────────────────────────────────────── */
const overlay      = document.getElementById('modal-overlay');
const modal        = document.getElementById('modal');
const modalClose   = document.getElementById('modal-close');
const modalCloseBtn= document.getElementById('modal-close-btn');
let lastFocused    = null;

function openModal(data) {
    const pct = Math.min(100, Math.round(Number(data.hp) / 120 * 100));
    modal.className = `modal modal-${data.tipo}`;

    document.getElementById('modal-img').src              = data.img;
    document.getElementById('modal-img').alt              = 'Arte de ' + data.nome;
    document.getElementById('modal-id').textContent       = '#' + data.id;
    document.getElementById('modal-title').textContent    = data.nome;
    document.getElementById('modal-badge').textContent    = data.tipo.charAt(0).toUpperCase() + data.tipo.slice(1);
    document.getElementById('modal-desc').textContent     = data.desc;
    document.getElementById('modal-tipo').textContent     = data.tipo.charAt(0).toUpperCase() + data.tipo.slice(1);
    document.getElementById('modal-cat').textContent      = data.categoria;
    document.getElementById('modal-altura').textContent   = data.altura;
    document.getElementById('modal-peso').textContent     = data.peso;
    document.getElementById('modal-hp-val').textContent   = data.hp + ' / 120';
    document.getElementById('modal-profile-btn').href     = `perfil.php?id=${encodeURIComponent(data.id)}`;

    const fill = document.getElementById('modal-hp-fill');
    fill.style.width = '0%';

    lastFocused = document.activeElement;
    overlay.classList.add('open');
    overlay.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';

    setTimeout(() => {
        fill.style.width = pct + '%';
        modalClose.focus();
    }, 120);
}

function closeModal() {
    overlay.classList.remove('open');
    overlay.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    if (lastFocused) { lastFocused.focus(); lastFocused = null; }
}

function focusTrap(e) {
    if (!overlay.classList.contains('open')) return;
    const focusable = Array.from(
        modal.querySelectorAll('button, a, input, [tabindex="0"]')
    ).filter(el => !el.disabled && el.offsetParent !== null);
    if (!focusable.length) return;

    const first = focusable[0];
    const last  = focusable[focusable.length - 1];

    if (e.key === 'Tab') {
        if      (e.shiftKey  && document.activeElement === first) { e.preventDefault(); last.focus(); }
        else if (!e.shiftKey && document.activeElement === last)  { e.preventDefault(); first.focus(); }
    }
    if (e.key === 'Escape') closeModal();
}

/* ─── Event Listeners ───────────────────────────────────────── */
document.querySelectorAll('.card').forEach(card => {
    const data = { ...card.dataset };

    card.addEventListener('mousemove', handleTilt);
    card.addEventListener('mouseleave', resetTilt);
    card.addEventListener('click', () => openModal(data));
    card.addEventListener('keydown', e => {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); openModal(data); }
    });
});

modalClose.addEventListener('click', closeModal);
modalCloseBtn.addEventListener('click', closeModal);
overlay.addEventListener('click', e => { if (e.target === overlay) closeModal(); });
document.addEventListener('keydown', focusTrap);
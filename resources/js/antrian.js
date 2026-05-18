const app = document.getElementById('antrian-app');

function escapeHtml(text) {
    return String(text ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

function setText(id, value) {
    const el = document.getElementById(id);
    if (el) el.textContent = value;
}

function speakCurrent(current) {
    if (!current || !('speechSynthesis' in window)) return;

    const text = `Ting tong, nomor antrian ${current.nomor_antrian}. ${current.nama}. Silakan menuju layanan ${current.nama_layanan}.`;
    const utter = new SpeechSynthesisUtterance(text);
    utter.lang = 'id-ID';
    utter.rate = 0.95;
    utter.pitch = 1;
    utter.volume = 1;

    window.speechSynthesis.cancel();
    window.speechSynthesis.speak(utter);
}

function renderCurrentCard(current) {
    const el = document.getElementById('board-current');
    if (!el) return;

    if (!current) {
        el.innerHTML = `
            <div class="text-muted">Belum ada panggilan</div>
            <h1 class="display-4 fw-bold mb-0">-</h1>
        `;
        return;
    }

    el.innerHTML = `
        <div class="text-muted">Nomor yang dipanggil</div>
        <h1 class="display-4 fw-bold mb-2">${escapeHtml(current.nomor_antrian)}</h1>
        <p class="mb-1"><strong>Nama:</strong> ${escapeHtml(current.nama)}</p>
        <p class="mb-0"><strong>Layanan:</strong> ${escapeHtml(current.nama_layanan)}</p>
    `;
}

async function refreshSnapshot() {
    if (!app) return;

    const res = await fetch(app.dataset.snapshotUrl, {
        headers: { 'Accept': 'application/json' },
        credentials: 'same-origin',
    });

    if (!res.ok) return;

    const data = await res.json();
    const page = app.dataset.page;

    if (page === 'guest') {
        setText('guest-total', data.stats.total);
        setText('guest-waiting', data.stats.waiting);
        setText('guest-called', data.stats.called);
        setText('guest-done', data.stats.done);
    }

    if (page === 'admin') {
        setText('admin-total', data.stats.total);
        setText('admin-waiting', data.stats.waiting);
        setText('admin-called', data.stats.called);
        setText('admin-skipped', data.stats.skipped);
        setText('admin-done', data.stats.done);

        const rows = document.getElementById('admin-rows');
        if (rows && data.admin_rows_html) rows.innerHTML = data.admin_rows_html;
    }

    if (page === 'board') {
        setText('board-total', data.stats.total);

        const rows = document.getElementById('board-rows');
        if (rows && data.board_rows_html) rows.innerHTML = data.board_rows_html;

        renderCurrentCard(data.current);

        const oldSignature = app.dataset.currentSignature || '';
        const newSignature = data.current_signature || '';

        if (newSignature && newSignature !== oldSignature) {
            const voiceEnabled = app.dataset.voiceEnabled === '1';
            if (voiceEnabled) speakCurrent(data.current);
        }

        app.dataset.currentSignature = newSignature;
    }
}

function initVoiceToggle() {
    const btn = document.getElementById('voice-toggle');
    if (!btn) return;

    app.dataset.voiceEnabled = '0';

    btn.addEventListener('click', () => {
        const enabled = app.dataset.voiceEnabled === '1' ? '0' : '1';
        app.dataset.voiceEnabled = enabled;
        btn.textContent = enabled === '1' ? 'Suara Aktif' : 'Aktifkan Suara';

        if (enabled === '1' && 'speechSynthesis' in window) {
            const utter = new SpeechSynthesisUtterance('Suara antrian aktif');
            utter.lang = 'id-ID';
            window.speechSynthesis.cancel();
            window.speechSynthesis.speak(utter);
        }
    });
}

document.addEventListener('DOMContentLoaded', async () => {
    if (!app) return;

    if (app.dataset.page === 'board') {
        initVoiceToggle();
    }

    await refreshSnapshot();
    setInterval(refreshSnapshot, 2000);
});
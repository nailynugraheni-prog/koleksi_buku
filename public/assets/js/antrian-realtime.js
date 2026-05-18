document.addEventListener('DOMContentLoaded', function () {
    const app = document.getElementById('antrian-app');
    if (!app) return;

    const page = app.dataset.page;
    const snapshotUrl = app.dataset.snapshotUrl;
    let lastSignature = app.dataset.currentSignature || '';

    const voiceToggle = document.getElementById('voice-toggle');
    const audio = document.getElementById('dingdong-audio');
    let voiceEnabled = false;

    function speakText(text) {
        if (!('speechSynthesis' in window)) {
            console.warn('Browser tidak mendukung Web Speech API');
            return;
        }

        window.speechSynthesis.cancel();

        const pesan = new SpeechSynthesisUtterance(text);
        pesan.lang = 'id-ID';
        pesan.rate = 0.85;
        pesan.pitch = 1.0;
        pesan.volume = 1.0;

        window.speechSynthesis.speak(pesan);
    }

    function announce(current) {
        if (!current || !voiceEnabled) return;

        const nomor = current.kode_antrian || current.nomor_antrian || '-';
        const nama = current.nama || '-';
        const layanan = current.nama_layanan || '-';

        const message = `Nomor antrian ${nomor}. ${nama}, silakan masuk ke ${layanan}.`;

        if (audio) {
            audio.currentTime = 0;
            audio.play()
                .then(() => {
                    audio.onended = function () {
                        speakText(message);
                    };
                })
                .catch(() => {
                    speakText(message);
                });
        } else {
            speakText(message);
        }
    }

    async function loadSnapshot() {
        try {
            const response = await fetch(snapshotUrl, {
                headers: { 'Accept': 'application/json' },
                cache: 'no-store'
            });

            if (!response.ok) return;

            const data = await response.json();

            if (page === 'admin') {
                const total = document.getElementById('admin-total');
                const waiting = document.getElementById('admin-waiting');
                const called = document.getElementById('admin-called');
                const skipped = document.getElementById('admin-skipped');
                const done = document.getElementById('admin-done');
                const rows = document.getElementById('admin-rows');

                if (total) total.textContent = data.stats?.total ?? 0;
                if (waiting) waiting.textContent = data.stats?.waiting ?? 0;
                if (called) called.textContent = data.stats?.called ?? 0;
                if (skipped) skipped.textContent = data.stats?.skipped ?? 0;
                if (done) done.textContent = data.stats?.done ?? 0;

                if (rows && data.admin_rows_html) {
                    rows.innerHTML = data.admin_rows_html;
                }
            }

            if (page === 'board') {
                const total = document.getElementById('board-total');
                const currentBox = document.getElementById('board-current');
                const rows = document.getElementById('board-rows');

                if (total) total.textContent = data.stats?.total ?? 0;
                if (currentBox && data.board_current_html) {
                    currentBox.innerHTML = data.board_current_html;
                }
                if (rows && data.board_rows_html) {
                    rows.innerHTML = data.board_rows_html;
                }

                const newSignature = data.current_signature || '';

                if (newSignature && newSignature !== lastSignature) {
                    announce(data.current);
                }

                lastSignature = newSignature;
            }
        } catch (err) {
            console.error('Gagal ambil snapshot:', err);
        }
    }

    if (page === 'board' && voiceToggle) {
        voiceToggle.addEventListener('click', function () {
            voiceEnabled = true;
            voiceToggle.textContent = 'Suara Aktif';
            voiceToggle.disabled = true;

            if (audio) {
                audio.play()
                    .then(() => {
                        audio.pause();
                        audio.currentTime = 0;
                    })
                    .catch(() => {});
            }
        });
    }

    loadSnapshot();
    setInterval(loadSnapshot, 1000);
});
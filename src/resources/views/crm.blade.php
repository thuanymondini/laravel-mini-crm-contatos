<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini CRM — Painel de Contatos</title>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0a0a0f;
            --surface: #12121a;
            --surface-hover: #1a1a26;
            --border: #2a2a3a;
            --border-focus: #6366f1;
            --text: #e2e2ee;
            --text-muted: #7a7a8e;
            --accent: #6366f1;
            --accent-hover: #818cf8;
            --success: #22c55e;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
            --pending: #f59e0b;
            --processing: #3b82f6;
            --active: #22c55e;
            --failed: #ef4444;
            --radius: 10px;
            --mono: 'JetBrains Mono', monospace;
            --sans: 'Outfit', sans-serif;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: var(--sans);
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            line-height: 1.5;
        }

        .layout {
            display: grid;
            grid-template-columns: 420px 1fr;
            min-height: 100vh;
        }

        .sidebar {
            background: var(--surface);
            border-right: 1px solid var(--border);
            padding: 32px 28px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .sidebar h1 {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar h1 span {
            background: var(--accent);
            color: white;
            font-size: 11px;
            padding: 3px 8px;
            border-radius: 20px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .form-section h2 {
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--text-muted);
            margin-bottom: 16px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 14px;
        }

        .form-group label {
            font-size: 13px;
            font-weight: 500;
            color: var(--text-muted);
        }

        .form-group input {
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 10px 14px;
            color: var(--text);
            font-family: var(--sans);
            font-size: 14px;
            transition: border-color 0.2s;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--border-focus);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }

        .btn-row {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 10px 18px;
            border: none;
            border-radius: var(--radius);
            font-family: var(--sans);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            letter-spacing: 0.3px;
        }

        .btn:active { transform: scale(0.97); }

        .btn-primary { background: var(--accent); color: white; }
        .btn-primary:hover { background: var(--accent-hover); }
        .btn-success { background: var(--success); color: white; }
        .btn-success:hover { opacity: 0.9; }
        .btn-warning { background: var(--warning); color: #000; }
        .btn-warning:hover { opacity: 0.9; }
        .btn-danger { background: var(--danger); color: white; }
        .btn-danger:hover { opacity: 0.9; }
        .btn-ghost { background: transparent; color: var(--text-muted); border: 1px solid var(--border); }
        .btn-ghost:hover { border-color: var(--text-muted); color: var(--text); }

        .id-input-row {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .id-input-row input {
            width: 80px;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 10px 14px;
            color: var(--text);
            font-family: var(--mono);
            font-size: 14px;
            text-align: center;
        }

        .id-input-row input:focus {
            outline: none;
            border-color: var(--border-focus);
        }

        .main {
            padding: 32px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .main-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .main-header h2 { font-size: 18px; font-weight: 600; }

        .ws-status {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--text-muted);
            font-family: var(--mono);
        }

        .ws-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--danger);
        }

        .ws-dot.connected { background: var(--success); animation: pulse 2s infinite; }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }

        .channels-list {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 8px;
        }

        .channel-tag {
            background: rgba(99, 102, 241, 0.15);
            color: var(--accent);
            font-family: var(--mono);
            font-size: 11px;
            padding: 3px 8px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .channel-tag .close {
            cursor: pointer;
            opacity: 0.6;
            font-size: 13px;
        }

        .channel-tag .close:hover { opacity: 1; }

        .table-wrapper {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
        }

        table { width: 100%; border-collapse: collapse; font-size: 14px; }

        thead th {
            background: var(--bg);
            padding: 12px 16px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border);
        }

        tbody td {
            padding: 12px 16px;
            border-bottom: 1px solid var(--border);
            font-family: var(--mono);
            font-size: 13px;
        }

        tbody tr:hover { background: var(--surface-hover); }
        tbody tr:last-child td { border-bottom: none; }

        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pending    { background: rgba(245,158,11,0.15); color: var(--pending); }
        .status-processing { background: rgba(59,130,246,0.15); color: var(--processing); }
        .status-active     { background: rgba(34,197,94,0.15); color: var(--active); }
        .status-failed     { background: rgba(239,68,68,0.15); color: var(--failed); }

        .score-cell { font-weight: 600; color: var(--accent); }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-muted);
        }

        .log-section h3 {
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--text-muted);
            margin-bottom: 12px;
        }

        .log-box {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 16px;
            max-height: 260px;
            overflow-y: auto;
            font-family: var(--mono);
            font-size: 12px;
            line-height: 1.8;
        }

        .log-entry {
            padding: 4px 0;
            border-bottom: 1px solid rgba(42,42,58,0.5);
        }

        .log-entry:last-child { border-bottom: none; }

        .log-time { color: var(--text-muted); }
        .log-method { font-weight: 600; }
        .log-method.GET { color: var(--info); }
        .log-method.POST { color: var(--success); }
        .log-method.PUT { color: var(--warning); }
        .log-method.DELETE { color: var(--danger); }
        .log-method.WS { color: #a855f7; }
        .log-status-ok { color: var(--success); }
        .log-status-err { color: var(--danger); }

        .pagination-info {
            font-size: 13px;
            color: var(--text-muted);
            font-family: var(--mono);
        }

        .divider {
            border: none;
            border-top: 1px solid var(--border);
            margin: 4px 0;
        }

        @media (max-width: 900px) {
            .layout { grid-template-columns: 1fr; }
            .sidebar { border-right: none; border-bottom: 1px solid var(--border); }
        }
    </style>
</head>
<body>
    <div class="layout">
        <aside class="sidebar">
            <h1>Mini CRM <span>DDD</span></h1>

            <div class="form-section">
                <h2>Criar / Atualizar Contato</h2>
                <div class="form-group">
                    <label>Nome</label>
                    <input type="text" id="name" placeholder="João Silva">
                </div>
                <div class="form-group">
                    <label>E-mail</label>
                    <input type="email" id="email" placeholder="joao@empresa.com.br">
                </div>
                <div class="form-group">
                    <label>Telefone</label>
                    <input type="text" id="phone" placeholder="11999999999">
                </div>
                <div class="btn-row">
                    <button class="btn btn-primary" onclick="createContact()">Criar</button>
                    <button class="btn btn-warning" onclick="updateContact()">Atualizar</button>
                    <button class="btn btn-ghost" onclick="clearForm()">Limpar</button>
                </div>
            </div>

            <hr class="divider">

            <div class="form-section">
                <h2>Ações por ID</h2>
                <div class="id-input-row">
                    <input type="number" id="action-id" placeholder="ID" min="1">
                    <button class="btn btn-primary" onclick="showContact()">Ver</button>
                    <button class="btn btn-success" onclick="processScore()">Score</button>
                    <button class="btn btn-danger" onclick="deleteContact()">Excluir</button>
                </div>
            </div>

            <hr class="divider">

            <div class="form-section">
                <h2>Listar Contatos</h2>
                <div class="btn-row">
                    <button class="btn btn-primary" onclick="listContacts()">Carregar Lista</button>
                </div>
            </div>

            <hr class="divider">

            <div class="form-section">
                <h2>WebSocket (Reverb)</h2>
                <div class="ws-status">
                    <div class="ws-dot" id="ws-dot"></div>
                    <span id="ws-status-text">desconectado</span>
                </div>
                <div class="channels-list" id="channels-list"></div>
            </div>
        </aside>

        <main class="main">
            <div class="main-header">
                <h2>Contatos</h2>
                <div class="pagination-info" id="pagination-info"></div>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Telefone</th>
                            <th>Score</th>
                            <th>Status</th>
                            <th>Processado em</th>
                        </tr>
                    </thead>
                    <tbody id="contacts-table">
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <p>Clique em "Carregar Lista" para ver os contatos</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="log-section">
                <h3>Log de Requisições</h3>
                <div class="log-box" id="log-box"></div>
            </div>
        </main>
    </div>

    <script src="https://js.pusher.com/8.0/pusher.min.js"></script>
    <script>
        const API = '/api';
        let pusher = null;
        let channels = {};

        // ── Pusher / Reverb ──

        function initPusher() {
            if (pusher) return;
            pusher = new Pusher("{{ env('REVERB_APP_KEY', 'my-app-key') }}", {
                wsHost: window.location.hostname,
                wsPort: 80,
                wssPort: 443,
                forceTLS: false,
                enabledTransports: ['ws'],
                cluster: 'mt1',
            });

            pusher.connection.bind('connected', () => {
                document.getElementById('ws-dot').classList.add('connected');
                document.getElementById('ws-status-text').textContent = 'conectado';
            });

            pusher.connection.bind('disconnected', () => {
                document.getElementById('ws-dot').classList.remove('connected');
                document.getElementById('ws-status-text').textContent = 'desconectado';
            });
        }

        function subscribeToContact(id) {
            if (!id) return;
            initPusher();
            const name = `contacts.${id}`;
            if (channels[name]) return;

            const channel = pusher.subscribe(name);
            channel.bind('ContactScoreProcessed', (data) => {
                logWs(`#${data.contactId} → score ${data.score}, status ${data.status}`);
                listContacts();
            });

            channels[name] = channel;
            renderChannelTags();
        }

        function unsubscribeFromContact(name) {
            if (channels[name] && pusher) {
                pusher.unsubscribe(name);
                delete channels[name];
                renderChannelTags();
            }
        }

        function subscribeAllProcessing(contacts) {
            contacts
                .filter(c => c.status === 'processing' || c.status === 'pending')
                .forEach(c => subscribeToContact(c.id));
        }

        function renderChannelTags() {
            const container = document.getElementById('channels-list');
            const names = Object.keys(channels);
            if (names.length === 0) {
                container.innerHTML = '';
                return;
            }
            container.innerHTML = names.map(name =>
                `<div class="channel-tag">
                    ${name}
                    <span class="close" onclick="unsubscribeFromContact('${name}')">✕</span>
                </div>`
            ).join('');
        }

        // ── API ──

        async function api(method, path, body = null) {
            const opts = {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
            };
            if (body) opts.body = JSON.stringify(body);

            const start = performance.now();
            const res = await fetch(`${API}${path}`, opts);
            const ms = Math.round(performance.now() - start);

            let data = null;
            const text = await res.text();
            if (text) {
                try { data = JSON.parse(text); } catch { data = text; }
            }

            log(method, path, res.status, ms);
            return { status: res.status, data };
        }

        function log(method, path, status, ms) {
            const box = document.getElementById('log-box');
            const time = new Date().toLocaleTimeString('pt-BR');
            const ok = status < 400;
            const entry = document.createElement('div');
            entry.className = 'log-entry';
            entry.innerHTML = `
                <span class="log-time">${time}</span>
                <span class="log-method ${method}">${method}</span>
                ${path}
                <span class="${ok ? 'log-status-ok' : 'log-status-err'}">${status}</span>
                <span class="log-time">${ms}ms</span>
            `;
            box.prepend(entry);
        }

        function logWs(message) {
            const box = document.getElementById('log-box');
            const time = new Date().toLocaleTimeString('pt-BR');
            const entry = document.createElement('div');
            entry.className = 'log-entry';
            entry.innerHTML = `
                <span class="log-time">${time}</span>
                <span class="log-method WS">WS</span>
                <span class="log-status-ok">${message}</span>
            `;
            box.prepend(entry);
        }

        // ── Form ──

        function getForm() {
            return {
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                phone: document.getElementById('phone').value,
            };
        }

        function clearForm() {
            document.getElementById('name').value = '';
            document.getElementById('email').value = '';
            document.getElementById('phone').value = '';
            document.getElementById('action-id').value = '';
        }

        function fillForm(contact) {
            document.getElementById('name').value = contact.name || '';
            document.getElementById('email').value = contact.email || '';
            document.getElementById('phone').value = contact.phone || '';
            document.getElementById('action-id').value = contact.id || '';
        }

        function getActionId() {
            const id = document.getElementById('action-id').value;
            if (!id) { alert('Informe o ID'); return null; }
            return id;
        }

        // ── CRUD ──

        async function createContact() {
            const { status, data } = await api('POST', '/contacts', getForm());
            if (status === 201) {
                const contact = data.data || data;
                clearForm();
                listContacts();
                subscribeToContact(contact.id);
            } else if (data?.errors) {
                alert('Erros: ' + JSON.stringify(data.errors));
            }
        }

        async function updateContact() {
            const id = getActionId();
            if (!id) return;
            const { status, data } = await api('PUT', `/contacts/${id}`, getForm());
            if (status === 200) {
                listContacts();
            } else if (data?.errors) {
                alert('Erros: ' + JSON.stringify(data.errors));
            }
        }

        async function showContact() {
            const id = getActionId();
            if (!id) return;
            const { status, data } = await api('GET', `/contacts/${id}`);
            if (status === 200) {
                fillForm(data.data || data);
            }
        }

        async function deleteContact() {
            const id = getActionId();
            if (!id) return;
            if (!confirm(`Excluir contato #${id}?`)) return;
            const { status } = await api('DELETE', `/contacts/${id}`);
            if (status === 204) {
                unsubscribeFromContact(`contacts.${id}`);
                clearForm();
                listContacts();
            }
        }

        async function processScore() {
            const id = getActionId();
            if (!id) return;
            const { status } = await api('POST', `/contacts/${id}/process-score`);
            if (status === 202) {
                subscribeToContact(id);
            }
        }

        async function listContacts() {
            const { status, data } = await api('GET', '/contacts');
            if (status !== 200) return;

            const contacts = data.data || [];
            const meta = data.meta || {};
            const tbody = document.getElementById('contacts-table');

            if (contacts.length === 0) {
                tbody.innerHTML = `<tr><td colspan="7"><div class="empty-state"><p>Nenhum contato encontrado</p></div></td></tr>`;
                return;
            }

            tbody.innerHTML = contacts.map(c => `
                <tr onclick='fillForm(${JSON.stringify(c).replace(/'/g, "&#39;")})' style="cursor:pointer;">
                    <td>${c.id}</td>
                    <td style="font-family:var(--sans)">${c.name}</td>
                    <td>${c.email}</td>
                    <td>${c.phone}</td>
                    <td class="score-cell">${c.score}</td>
                    <td><span class="status-badge status-${c.status}">${c.status}</span></td>
                    <td>${c.processed_at ? new Date(c.processed_at).toLocaleString('pt-BR') : '—'}</td>
                </tr>
            `).join('');

            const info = document.getElementById('pagination-info');
            if (meta.total !== undefined) {
                info.textContent = `${meta.total} contatos · página ${meta.current_page}/${meta.last_page}`;
            }

            subscribeAllProcessing(contacts);
        }

        // ── Auto-load ──
        listContacts();
    </script>
</body>
</html>

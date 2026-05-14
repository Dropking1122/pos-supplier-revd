<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tidak Ada Koneksi — POS Supplier</title>
    <meta name="theme-color" content="#4f46e5">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #0f172a;
            color: #f1f5f9;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0;
            padding: 24px;
            text-align: center;
        }

        .icon-wrap {
            width: 96px;
            height: 96px;
            background: #1e293b;
            border-radius: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 28px;
            box-shadow: 0 0 0 1px #334155;
        }

        .icon-wrap svg { width: 48px; height: 48px; color: #6366f1; }

        h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #f1f5f9;
            margin-bottom: 10px;
        }

        p {
            font-size: 0.95rem;
            color: #94a3b8;
            line-height: 1.6;
            max-width: 320px;
            margin-bottom: 36px;
        }

        .btn-retry {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #4f46e5;
            color: white;
            border: none;
            border-radius: 12px;
            padding: 12px 28px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s, transform 0.1s;
            text-decoration: none;
        }
        .btn-retry:hover  { background: #4338ca; }
        .btn-retry:active { transform: scale(0.97); }

        .btn-retry svg { width: 18px; height: 18px; }

        .divider { width: 40px; height: 1px; background: #1e293b; margin: 28px auto; }

        .tips {
            font-size: 0.8rem;
            color: #475569;
            max-width: 280px;
        }
        .tips strong { color: #64748b; }

        .status-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #ef4444;
            margin-right: 6px;
            animation: pulse 1.5s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        .connection-badge {
            display: inline-flex;
            align-items: center;
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 100px;
            padding: 5px 14px;
            font-size: 0.78rem;
            color: #94a3b8;
            margin-bottom: 24px;
        }
    </style>
</head>
<body>
    <div class="icon-wrap">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                  d="M18.364 5.636a9 9 0 010 12.728M15.536 8.464a5 5 0 010 7.072M12 12h.01M3 3l18 18"/>
        </svg>
    </div>

    <div class="connection-badge">
        <span class="status-dot"></span>
        Tidak ada koneksi internet
    </div>

    <h1>Ups, Offline!</h1>
    <p>Sepertinya koneksi internet Anda terputus.<br>
       Periksa Wi-Fi atau data seluler, lalu coba lagi.</p>

    <button class="btn-retry" onclick="retryConnection()">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
        Coba Lagi
    </button>

    <div class="divider"></div>

    <p class="tips">
        <strong>Tip:</strong> Beberapa halaman yang sebelumnya dikunjungi mungkin masih bisa diakses saat offline.
    </p>

    <script>
        function retryConnection() {
            const btn = document.querySelector('.btn-retry');
            btn.style.opacity = '0.6';
            btn.style.pointerEvents = 'none';
            setTimeout(() => window.location.reload(), 300);
        }

        // Auto-redirect when connection restored
        window.addEventListener('online', () => {
            setTimeout(() => window.location.href = '/', 500);
        });
    </script>
</body>
</html>

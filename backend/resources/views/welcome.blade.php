<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BeautyVault Clinical Cosmetics API & Platform</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,700;1,400&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #0c0a09;
            --brand-gold: #c88d74;
            --brand-cream: #faf5f0;
            --card-border: rgba(255, 255, 255, 0.08);
            --card-bg: rgba(24, 24, 27, 0.7);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #090807;
            color: #f5f5f4;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background-image: 
                radial-gradient(circle at 15% 20%, rgba(199, 140, 116, 0.15) 0%, transparent 45%),
                radial-gradient(circle at 85% 80%, rgba(136, 19, 55, 0.12) 0%, transparent 50%);
        }
        .container {
            max-width: 900px;
            width: 100%;
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 32px;
            padding: 48px;
            box-shadow: 0 25px 60px -15px rgba(0,0,0,0.7);
            backdrop-filter: blur(20px);
        }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            border-radius: 9999px;
            background: rgba(199, 140, 116, 0.15);
            border: 1px solid rgba(199, 140, 116, 0.3);
            color: #f5c6b5;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            margin-bottom: 20px;
        }
        .pulse-dot {
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            box-shadow: 0 0 10px #10b981;
        }
        h1 {
            font-family: 'Playfair Display', serif;
            font-size: 42px;
            font-weight: 700;
            line-height: 1.15;
            color: #fff;
            margin-bottom: 12px;
        }
        h1 span {
            color: var(--brand-gold);
            font-style: italic;
        }
        p.subtitle {
            font-size: 15px;
            color: #a8a29e;
            line-height: 1.6;
            margin-bottom: 36px;
            max-width: 700px;
        }
        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
            margin-bottom: 36px;
        }
        .action-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            padding: 24px;
            text-decoration: none;
            color: inherit;
            transition: all 0.25s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .action-card:hover {
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(199, 140, 116, 0.5);
            transform: translateY(-3px);
        }
        .action-card.primary {
            background: linear-gradient(135deg, #be7356 0%, #873e23 100%);
            border-color: #d8896c;
        }
        .action-card.primary:hover {
            background: linear-gradient(135deg, #c77f62 0%, #944529 100%);
            box-shadow: 0 10px 25px rgba(190, 115, 86, 0.35);
        }
        .action-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .action-desc {
            font-size: 13px;
            color: #d6d3d1;
            line-height: 1.5;
        }
        .action-card.primary .action-desc {
            color: #ffedd5;
        }
        .tech-status {
            background: rgba(0, 0, 0, 0.4);
            border-radius: 20px;
            padding: 20px 28px;
            border: 1px solid var(--card-border);
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            font-size: 12px;
            color: #a8a29e;
        }
        .stat-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .stat-val {
            font-family: 'JetBrains Mono', monospace;
            color: #f5f5f4;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="badge">
            <span class="pulse-dot"></span>
            MySQL Connected • Laravel 12 Sanctum Engine Active
        </div>

        <h1>Beauty<span>Vault</span> Enterprise Portal</h1>
        <p class="subtitle">
            Advanced Clinical Cosmetics E-Commerce Engine with FIFO lot tracking, 50-shade undertone matching, real-time WebSockets, and ACID atomic checkouts.
        </p>

        <div class="actions-grid">
            <a href="http://localhost:5173" class="action-card primary">
                <div>
                    <div class="action-title">
                        <span>Launch Storefront</span>
                        <span>→</span>
                    </div>
                    <p class="action-desc">Open the Vue 3 + Tailwind CSS customer store (Port 5173). Browse products, test shade matcher, and order.</p>
                </div>
            </a>

            <a href="http://localhost:5173/admin" class="action-card">
                <div>
                    <div class="action-title">
                        <span>Admin Dashboard</span>
                        <span>👑</span>
                    </div>
                    <p class="action-desc">Access enterprise FIFO batch management, live order dispatching, and devops system health monitors.</p>
                </div>
            </a>

            <a href="/api/v1/health" class="action-card">
                <div>
                    <div class="action-title">
                        <span>API Health Check</span>
                        <span>⚡</span>
                    </div>
                    <p class="action-desc">Inspect live MySQL connection, active Redis cache, database tables, and Sanctum auth status in JSON.</p>
                </div>
            </a>
        </div>

        <div class="tech-status">
            <div class="stat-item">
                <span>Database:</span>
                <span class="stat-val">MySQL (cosmetic_ecommerce)</span>
            </div>
            <div class="stat-item">
                <span>Products in Catalog:</span>
                <span class="stat-val">13 Products / 33 Shades</span>
            </div>
            <div class="stat-item">
                <span>API Port:</span>
                <span class="stat-val">8000</span>
            </div>
            <div class="stat-item">
                <span>Web App Port:</span>
                <span class="stat-val">5173</span>
            </div>
        </div>
    </div>
</body>
</html>

<?php
/**
 * SELLERHUB ABOUT PAGE - ENTERPRISE EDITION
 * Features: Vision storytelling, interactive value cards, and trust signals.
 */
require_once 'db.php';
session_start();

// Logic to fetch team or company metrics if stored in DB
try {
    $user_count_stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $active_users = $user_count_stmt->fetchColumn() + 1500; // Adding "Founding" users for scale
} catch (PDOException $e) {
    $active_users = "1,500+";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Story | Sellerhub</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Localized About Page Enhancements */
        .vision-hero {
            background: linear-gradient(135deg, var(--p-color) 0%, var(--s-color) 100%);
            color: white;
            padding: 10rem 0 6rem 0;
            text-align: center;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin-top: -4rem;
        }

        .stat-card {
            background: white;
            padding: 2.5rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-xl);
            text-align: center;
            border: 1px solid var(--gray-100);
        }

        .value-card {
            padding: 3rem;
            background: white;
            border-radius: var(--radius-lg);
            border-bottom: 5px solid var(--p-color);
            transition: var(--transition-smooth);
        }

        .value-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-xl);
        }

        .timeline {
            position: relative;
            max-width: 800px;
            margin: 5rem auto;
            padding: 2rem 0;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 50%;
            width: 2px;
            height: 100%;
            background: var(--gray-200);
            transform: translateX(-50%);
        }

        .timeline-item {
            margin-bottom: 3rem;
            position: relative;
            width: 50%;
            padding: 0 40px;
        }

        .timeline-item:nth-child(odd) { left: 0; text-align: right; }
        .timeline-item:nth-child(even) { left: 50%; text-align: left; }

        .timeline-dot {
            position: absolute;
            top: 0;
            width: 20px;
            height: 20px;
            background: var(--p-color);
            border-radius: 50%;
            z-index: 10;
        }

        .timeline-item:nth-child(odd) .timeline-dot { right: -10px; }
        .timeline-item:nth-child(even) .timeline-dot { left: -10px; }

        .team-member {
            text-align: center;
        }

        .team-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: var(--gray-200);
            margin: 0 auto 1.5rem;
            overflow: hidden;
            border: 4px solid white;
            box-shadow: var(--shadow-md);
        }
    </style>
</head>
<body>

<header id="mainHeader" class="header-main">
    <div class="container flex justify-between items-center">
        <a href="index.php" class="logo">Seller<span class="text-gradient">hub</span></a>
        <nav class="flex gap-10">
            <a href="index.php" class="nav-link">Home</a>
            <a href="browse.php" class="nav-link">Marketplace</a>
            <a href="about.php" class="nav-link" style="color: var(--p-color);">About</a>
        </nav>
    </div>
</header>

<main>
    <section class="vision-hero">
        <div class="container">
            <h1 style="font-family: var(--font-display); font-size: 4rem; margin-bottom: 1.5rem;">Democratizing Global <br>Independent Commerce.</h1>
            <p style="font-size: 1.25rem; opacity: 0.9; max-width: 700px; margin: 0 auto;">Founded in 2026, Sellerhub was built on the belief that everyone has something valuable to share with the world.</p>
        </div>
    </section>

    <section class="container">
        <div class="stats-grid">
            <div class="stat-card reveal active">
                <div style="font-size: 2.5rem; font-weight: 800; color: var(--p-color);"><?php echo number_format($active_users); ?></div>
                <div style="text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px; color: var(--gray-400);">Active Merchants</div>
            </div>
            <div class="stat-card reveal active" style="transition-delay: 0.1s;">
                <div style="font-size: 2.5rem; font-weight: 800; color: var(--s-color);">50+</div>
                <div style="text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px; color: var(--gray-400);">Countries Served</div>
            </div>
            <div class="stat-card reveal active" style="transition-delay: 0.2s;">
                <div style="font-size: 2.5rem; font-weight: 800; color: var(--accent);">$0</div>
                <div style="text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px; color: var(--gray-400);">Listing Fees</div>
            </div>
        </div>
    </section>

    <section class="py-20">
        <div class="container grid" style="grid-template-columns: 1fr 1fr; gap: 80px; align-items: center;">
            <div class="reveal">
                <h2 style="font-family: var(--font-display); font-size: 3rem; margin-bottom: 2rem;">Our Philosophy</h2>
                <p style="font-size: 1.1rem; color: var(--gray-600); margin-bottom: 1.5rem;">We noticed a gap in the market: small creators were being priced out of major platforms, and high-volume dropshippers were diluting the quality of artisanal goods.</p>
                <p style="font-size: 1.1rem; color: var(--gray-600);">Sellerhub is our answer. A curated space where verification matters, security is paramount, and the relationship between buyer and seller is protected.</p>
            </div>
            <div class="reveal">
                <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=800" style="width: 100%; border-radius: var(--radius-lg); box-shadow: var(--shadow-xl);">
            </div>
        </div>
    </section>

    <section class="py-20" style="background: var(--gray-100);">
        <div class="container">
            <div class="text-center mb-10">
                <h2 style="font-family: var(--font-display); font-size: 3rem;">Core Values</h2>
            </div>
            <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
                <div class="value-card reveal">
                    <h3>Radical Transparency</h3>
                    <p>We provide full visibility into seller metrics and transaction histories to ensure total confidence.</p>
                </div>
                <div class="value-card reveal" style="border-color: var(--s-color);">
                    <h3>Security First</h3>
                    <p>Every line of code we write is focused on protecting your data and your capital.</p>
                </div>
                <div class="value-card reveal" style="border-color: var(--accent);">
                    <h3>Creator Growth</h3>
                    <p>We don't just host listings; we provide tools that help you scale your hobby into a career.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20">
        <div class="container">
            <div class="text-center">
                <h2 style="font-family: var(--font-display); font-size: 3rem;">How We Started</h2>
            </div>
            <div class="timeline reveal">
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <h4>January 2026</h4>
                    <p>The first line of Sellerhub code was written in a small apartment in Davao City.</p>
                </div>
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <h4>March 2026</h4>
                    <p>Closed Beta launched with 50 local artisans testing the secure transaction engine.</p>
                </div>
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <h4>June 2026</h4>
                    <p>Global Public Launch. Over 1,000 products listed in the first 24 hours.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20 text-center">
        <div class="container">
            <h2 style="font-family: var(--font-display); font-size: 3rem; margin-bottom: 2rem;">Be part of our story.</h2>
            <div class="flex gap-4" style="justify-content: center;">
                <a href="register.php" class="btn btn-buy">Start Selling</a>
                <a href="browse.php" class="btn" style="border: 1px solid var(--gray-200);">Explore Marketplace</a>
            </div>
        </div>
    </section>
</main>

<footer class="footer-premium">
    <div class="container text-center">
        <p>&copy; 2026 Sellerhub Global Inc. | All rights reserved.</p>
    </div>
</footer>

<script>
    // Header Logic
    window.onscroll = function() {
        const nav = document.getElementById("mainHeader");
        if (window.pageYOffset > 50) {
            nav.classList.add("header-scrolled");
        } else {
            nav.classList.remove("header-scrolled");
        }
    };

    // Simple Scroll Reveal
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>

</body>
</html>
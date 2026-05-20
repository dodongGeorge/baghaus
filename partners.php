<?php
/**
 * SELLERHUB PARTNER ECOSYSTEM
 * Features: Partner grid, Tiered categorization, and Lead generation form.
 */
require_once 'db.php';
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global Partners | Sellerhub</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .partner-hero {
            background: var(--gray-900);
            color: white;
            padding: 10rem 0 6rem 0;
            position: relative;
            overflow: hidden;
        }

        .partner-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin: 4rem 0;
        }

        .partner-card {
            background: white;
            padding: 3rem 2rem;
            border-radius: var(--radius-lg);
            text-align: center;
            border: 1px solid var(--gray-100);
            transition: var(--transition-smooth);
        }

        .partner-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-xl);
            border-color: var(--p-color);
        }

        .partner-logo-placeholder {
            width: 80px;
            height: 80px;
            background: var(--gray-50);
            border-radius: 15px;
            margin: 0 auto 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            color: var(--gray-400);
            font-size: 1.5rem;
        }

        .tier-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            margin-bottom: 1rem;
        }

        .tier-platinum { background: var(--p-soft); color: var(--p-color); }
        .tier-gold { background: #fffbeb; color: #b45309; }
    </style>
</head>
<body>

<header id="mainHeader" class="header-main">
    <div class="container flex justify-between items-center">
        <a href="index.php" class="logo">Seller<span class="text-gradient">hub</span></a>
        <nav class="flex gap-10">
            <a href="browse.php" class="nav-link">Marketplace</a>
            <a href="about.php" class="nav-link">About</a>
            <a href="partners.php" class="nav-link active" style="color: var(--p-color);">Partners</a>
        </nav>
    </div>
</header>

<main>
    <section class="partner-hero">
        <div class="container">
            <h1 style="font-family: var(--font-display); font-size: 4rem;">Powering the <br><span class="text-gradient">Ecosystem.</span></h1>
            <p style="max-width: 600px; opacity: 0.7; font-size: 1.2rem; margin-top: 1rem;">We collaborate with world-class technology and logistics providers to ensure your business runs smoothly.</p>
        </div>
    </section>

    <section class="container py-20">
        <h2 style="font-family: var(--font-display); font-size: 2.5rem; margin-bottom: 3rem;">Strategic Alliances</h2>
        
        <div class="partner-grid">
            <div class="partner-card reveal active">
                <span class="tier-badge tier-platinum">Logistics Provider</span>
                <div class="partner-logo-placeholder">ShipX</div>
                <h3>ShipX Global</h3>
                <p style="font-size: 0.9rem; color: var(--gray-600);">Next-day delivery and automated tracking for all Sellerhub merchants.</p>
            </div>

            <div class="partner-card reveal active" style="transition-delay: 0.1s;">
                <span class="tier-badge tier-platinum">Payments</span>
                <div class="partner-logo-placeholder">Vault</div>
                <h3>VaultPay</h3>
                <p style="font-size: 0.9rem; color: var(--gray-600);">Enterprise-grade escrow and multi-currency settlement systems.</p>
            </div>

            <div class="partner-card reveal active" style="transition-delay: 0.2s;">
                <span class="tier-badge tier-gold">Integration</span>
                <div class="partner-logo-placeholder">API.io</div>
                <h3>ConnectFlow</h3>
                <p style="font-size: 0.9rem; color: var(--gray-600);">Synchronize your Sellerhub inventory with external POS systems.</p>
            </div>

            <div class="partner-card reveal active" style="transition-delay: 0.3s;">
                <span class="tier-badge tier-gold">Marketing</span>
                <div class="partner-logo-placeholder">AdZ</div>
                <h3>AdBoost</h3>
                <p style="font-size: 0.9rem; color: var(--gray-600);">AI-driven ad placements to help your products reach the right audience.</p>
            </div>
        </div>
    </section>

    <section class="py-20" style="background: var(--gray-50);">
        <div class="container">
            <div class="form-card grid" style="grid-template-columns: 1fr 1fr; gap: 60px; align-items: center;">
                <div>
                    <h2 style="font-family: var(--font-display); font-size: 2.5rem;">Become a Partner</h2>
                    <p style="margin: 1.5rem 0; color: var(--gray-600);">Are you a service provider or software company looking to reach 1,500+ active merchants? Let's build together.</p>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 0.5rem;">✅ Access to Marketplace API</li>
                        <li style="margin-bottom: 0.5rem;">✅ Co-marketing Opportunities</li>
                        <li style="margin-bottom: 0.5rem;">✅ Direct Merchant Referrals</li>
                    </ul>
                </div>
                <form onsubmit="event.preventDefault(); alert('Application Sent!');">
                    <div class="form-group">
                        <label>Company Name</label>
                        <input type="text" placeholder="e.g. Logistics Inc." required>
                    </div>
                    <div class="form-group">
                        <label>Contact Email</label>
                        <input type="email" placeholder="partners@company.com" required>
                    </div>
                    <div class="form-group">
                        <label>Partner Type</label>
                        <select>
                            <option>Technology / API</option>
                            <option>Logistics / Shipping</option>
                            <option>Financial Services</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-buy w-full">Apply to Partner Program</button>
                </form>
            </div>
        </div>
    </section>
</main>

<footer class="footer-premium">
    <div class="container text-center">
        <p>&copy; 2026 Sellerhub Global Inc. &bull; <a href="about.php" style="color:white;">Back to Mission</a></p>
    </div>
</footer>

<script>
    window.onscroll = function() {
        const nav = document.getElementById("mainHeader");
        if (window.pageYOffset > 50) {
            nav.classList.add('header-scrolled');
        } else {
            nav.classList.remove('header-scrolled');
        }
    };
</script>

</body>
</html>
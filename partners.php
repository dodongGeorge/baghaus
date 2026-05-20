<?php
require_once 'db.php';
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Partners | Baghaus</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .partners-hero {
      background: var(--espresso);
      padding: calc(var(--nav-height) + 4rem) 0 5rem;
      position: relative; overflow: hidden;
    }
    .partners-hero::before {
      content: '';
      position: absolute; inset: 0;
      background: url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=1400&q=50') center/cover no-repeat;
      opacity: .1;
    }
    .partners-hero .container { position: relative; z-index: 1; }
    .partners-eyebrow {
      font-size: .68rem; font-weight: 700; letter-spacing: .2em;
      text-transform: uppercase; color: var(--gold); margin-bottom: .75rem;
    }
    .partners-hero h1 {
      font-family: var(--font-display);
      font-size: clamp(2.8rem, 5vw, 5rem);
      font-weight: 300; color: var(--cream); line-height: 1.08; margin-bottom: 1rem;
    }
    .partners-hero h1 em { font-style: italic; color: var(--gold-light); }
    .partners-hero p { font-size: .95rem; color: rgba(255,255,255,.55); max-width: 500px; line-height: 1.85; }

    .partner-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
      gap: 1.5rem;
    }
    .partner-card {
      background: var(--white);
      border-radius: var(--radius-lg);
      border: 1px solid var(--gray-200);
      padding: 2.25rem 2rem;
      text-align: center;
      transition: all .3s var(--ease-out);
      box-shadow: var(--shadow-sm);
    }
    .partner-card:hover { transform: translateY(-6px); box-shadow: var(--shadow-lg); border-color: var(--gold); }
    .partner-logo {
      width: 72px; height: 72px;
      background: var(--cream-dark);
      border-radius: var(--radius-md);
      display: flex; align-items: center; justify-content: center;
      font-weight: 800; font-size: 1.1rem;
      color: var(--espresso); margin: 0 auto 1.25rem;
      border: 1px solid var(--gray-200);
    }
    .tier-badge {
      display: inline-block;
      padding: .22rem .75rem;
      border-radius: 50px;
      font-size: .65rem; font-weight: 700;
      letter-spacing: .08em; text-transform: uppercase;
      margin-bottom: .9rem;
    }
    .tier-platinum { background: var(--cream-dark); color: var(--brown); border: 1px solid var(--gray-200); }
    .tier-gold     { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
    .partner-card h3 {
      font-family: var(--font-display);
      font-size: 1.2rem; font-weight: 500;
      color: var(--espresso); margin-bottom: .5rem;
    }
    .partner-card p { font-size: .84rem; color: var(--gray-600); line-height: 1.75; }

    .apply-section {
      background: var(--cream-dark);
    }
    .apply-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 5rem;
      align-items: center;
    }
    .apply-perk {
      display: flex; align-items: center; gap: .85rem;
      font-size: .9rem; color: var(--gray-600);
      margin-bottom: .75rem;
    }
    .apply-perk-icon {
      width: 30px; height: 30px; flex-shrink: 0;
      background: var(--white); border: 1px solid var(--gray-200);
      border-radius: 50%; display: flex; align-items: center;
      justify-content: center; font-size: .85rem;
    }

    /* Toast notification */
    .toast {
      position: fixed;
      bottom: 2rem; right: 2rem;
      background: var(--espresso);
      color: var(--cream);
      padding: 1.1rem 1.5rem;
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow-lg);
      display: flex; align-items: flex-start; gap: 1rem;
      max-width: 360px;
      z-index: 9999;
      transform: translateY(120%);
      opacity: 0;
      transition: all .4s cubic-bezier(.34,1.56,.64,1);
      border-left: 4px solid var(--gold);
    }
    .toast.show { transform: translateY(0); opacity: 1; }
    .toast-icon { font-size: 1.5rem; flex-shrink: 0; margin-top: .1rem; }
    .toast-title { font-family: var(--font-display); font-size: 1rem; font-weight: 600; margin-bottom: .2rem; }
    .toast-body  { font-size: .8rem; color: rgba(255,255,255,.65); line-height: 1.6; }
    .toast-close {
      margin-left: auto; flex-shrink: 0;
      background: none; border: none;
      color: rgba(255,255,255,.4); font-size: 1.1rem;
      cursor: pointer; padding: 0; line-height: 1;
      transition: color .2s;
    }
    .toast-close:hover { color: var(--cream); }

    @media (max-width: 860px) {
      .apply-grid { grid-template-columns: 1fr; gap: 2.5rem; }
    }
  </style>
</head>
<body>

<?php include 'nav.php'; ?>

<!-- HERO -->
<section class="partners-hero">
  <div class="container">
    <div class="partners-eyebrow">Partner Ecosystem</div>
    <h1>Powering the<br><em>Ecosystem.</em></h1>
    <p>We collaborate with world-class technology and logistics providers to keep Baghaus running smoothly for every buyer and seller.</p>
  </div>
</section>

<!-- PARTNERS GRID -->
<section class="py-20">
  <div class="container">
    <div style="text-align:center;margin-bottom:3rem">
      <span style="font-size:.68rem;font-weight:700;letter-spacing:.2em;text-transform:uppercase;color:var(--gold);display:block;margin-bottom:.5rem">Our Alliances</span>
      <h2 style="font-family:var(--font-display);font-size:clamp(1.9rem,3vw,2.8rem);font-weight:300;color:var(--espresso)">Strategic <em style="font-style:italic;color:var(--gold)">Partners</em></h2>
    </div>
    <div class="partner-grid">
      <div class="partner-card">
        <span class="tier-badge tier-platinum">Logistics</span>
        <div class="partner-logo">ShipX</div>
        <h3>ShipX Global</h3>
        <p>Next-day delivery and automated tracking for all Baghaus merchants across the Philippines.</p>
      </div>
      <div class="partner-card">
        <span class="tier-badge tier-platinum">Payments</span>
        <div class="partner-logo">Vault</div>
        <h3>VaultPay</h3>
        <p>Enterprise-grade escrow and multi-currency settlement — GCash, Maya, Bank Transfer & COD.</p>
      </div>
      <div class="partner-card">
        <span class="tier-badge tier-gold">Integration</span>
        <div class="partner-logo">API</div>
        <h3>ConnectFlow</h3>
        <p>Synchronize your Baghaus inventory with external POS and e-commerce systems seamlessly.</p>
      </div>
      <div class="partner-card">
        <span class="tier-badge tier-gold">Marketing</span>
        <div class="partner-logo">AdZ</div>
        <h3>AdBoost</h3>
        <p>AI-driven ad placements to help your bag listings reach the right audience at the right time.</p>
      </div>
    </div>
  </div>
</section>

<!-- BECOME A PARTNER -->
<section class="py-20 apply-section">
  <div class="container">
    <div class="apply-grid">
      <div>
        <span style="font-size:.68rem;font-weight:700;letter-spacing:.2em;text-transform:uppercase;color:var(--gold);display:block;margin-bottom:.75rem">Work with us</span>
        <h2 style="font-family:var(--font-display);font-size:clamp(2rem,3vw,3rem);font-weight:300;color:var(--espresso);margin-bottom:1.25rem;line-height:1.1">Become a <em style="font-style:italic;color:var(--gold)">Partner</em></h2>
        <p style="font-size:.9rem;color:var(--gray-600);line-height:1.85;margin-bottom:2rem">Are you a service provider or software company looking to reach 1,500+ active merchants? Let's build together.</p>
        <div class="apply-perk"><div class="apply-perk-icon">🔌</div> Access to Marketplace API</div>
        <div class="apply-perk"><div class="apply-perk-icon">📣</div> Co-marketing Opportunities</div>
        <div class="apply-perk"><div class="apply-perk-icon">🤝</div> Direct Merchant Referrals</div>
      </div>

      <div style="background:var(--white);border-radius:var(--radius-xl);border:1px solid var(--gray-200);padding:2.5rem;box-shadow:var(--shadow-md)">
        <h3 style="font-family:var(--font-display);font-size:1.5rem;font-weight:400;color:var(--espresso);margin-bottom:1.5rem">Apply to Partner Program</h3>
        <div id="partnerForm">
          <div class="form-group">
            <label>Company Name</label>
            <input type="text" id="companyName" placeholder="e.g. Logistics Inc." required>
          </div>
          <div class="form-group">
            <label>Contact Email</label>
            <input type="email" id="contactEmail" placeholder="partners@company.com" required>
          </div>
          <div class="form-group">
            <label>Partner Type</label>
            <select id="partnerType">
              <option>Technology / API</option>
              <option>Logistics / Shipping</option>
              <option>Financial Services</option>
              <option>Other</option>
            </select>
          </div>
          <button type="button" onclick="submitPartnerForm()" class="btn btn-buy" style="width:100%;padding:1rem;font-size:.875rem">
            Apply to Partner Program
          </button>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include 'footer.php'; ?>

<!-- TOAST NOTIFICATION -->
<div class="toast" id="partnerToast">
  <div class="toast-icon">✦</div>
  <div>
    <div class="toast-title">Application Received!</div>
    <div class="toast-body" id="toastBody">Thanks for your interest. We'll be in touch within 2–3 business days.</div>
  </div>
  <button class="toast-close" onclick="closeToast()">✕</button>
</div>

<script>
function submitPartnerForm() {
  const company = document.getElementById('companyName').value.trim();
  const email   = document.getElementById('contactEmail').value.trim();
  const type    = document.getElementById('partnerType').value;

  if (!company || !email) {
    document.getElementById('companyName').style.borderColor = !company ? '#ef4444' : '';
    document.getElementById('contactEmail').style.borderColor = !email ? '#ef4444' : '';
    return;
  }

  // Reset border colors
  document.getElementById('companyName').style.borderColor = '';
  document.getElementById('contactEmail').style.borderColor = '';

  // Update toast message with their details
  document.getElementById('toastBody').textContent =
    company + ' (' + type + ') — we\'ll reach you at ' + email + ' within 2–3 business days.';

  // Clear the form
  document.getElementById('companyName').value = '';
  document.getElementById('contactEmail').value = '';

  // Show toast
  const toast = document.getElementById('partnerToast');
  toast.classList.add('show');

  // Auto-dismiss after 5 seconds
  setTimeout(closeToast, 5000);
}

function closeToast() {
  document.getElementById('partnerToast').classList.remove('show');
}
</script>

</body>
</html>
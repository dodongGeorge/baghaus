<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Terms of Service | Baghaus</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .terms-hero {
      background: var(--espresso);
      padding: calc(var(--nav-height) + 4rem) 0 4rem;
      text-align: center;
    }
    .terms-eyebrow {
      font-size: .68rem;
      font-weight: 700;
      letter-spacing: .2em;
      text-transform: uppercase;
      color: var(--gold);
      margin-bottom: .75rem;
    }
    .terms-hero h1 {
      font-family: var(--font-display);
      font-size: clamp(2.2rem, 4vw, 3.5rem);
      font-weight: 300;
      color: var(--cream);
      line-height: 1.1;
      margin-bottom: .75rem;
    }
    .terms-hero h1 em { color: var(--gold-light); font-style: italic; }
    .terms-meta {
      font-size: .8rem;
      color: rgba(255,255,255,.45);
      letter-spacing: .06em;
    }

    .terms-body {
      max-width: 760px;
      margin: 0 auto;
      padding: 5rem 2rem 6rem;
    }
    .back-link {
      display: inline-flex;
      align-items: center;
      gap: .5rem;
      font-size: .8rem;
      font-weight: 700;
      letter-spacing: .1em;
      text-transform: uppercase;
      color: var(--brown);
      text-decoration: none;
      margin-bottom: 3rem;
      transition: color .2s;
    }
    .back-link:hover { color: var(--gold); }
    .back-link::before { content: '←'; font-size: 1rem; }

    .terms-section { margin-bottom: 2.75rem; }
    .terms-section h2 {
      font-family: var(--font-display);
      font-size: 1.4rem;
      font-weight: 400;
      color: var(--espresso);
      margin-bottom: .75rem;
      padding-bottom: .6rem;
      border-bottom: 1px solid var(--gray-200);
    }
    .terms-section p {
      font-size: .925rem;
      color: var(--gray-600);
      line-height: 1.85;
      margin-bottom: .75rem;
    }
    .terms-section ul {
      margin: .5rem 0 .75rem 1.25rem;
      display: flex;
      flex-direction: column;
      gap: .4rem;
    }
    .terms-section ul li {
      font-size: .925rem;
      color: var(--gray-600);
      line-height: 1.75;
    }
    .terms-section ul li::marker { color: var(--gold); }
  </style>
</head>
<body>

<?php include 'nav.php'; ?>

<!-- HERO -->
<div class="terms-hero">
  <div class="terms-eyebrow">Legal</div>
  <h1>Terms of <em>Service</em></h1>
  <p class="terms-meta">Last updated: <?php echo date("F d, Y"); ?></p>
</div>

<!-- CONTENT -->
<div class="terms-body">
  <a href="index.php" class="back-link">Back to Home</a>

  <div class="terms-section">
    <h2>1. Acceptance of Terms</h2>
    <p>By accessing or using Baghaus, you agree to be bound by these Terms of Service. If you do not agree to all of these terms, do not use our platform.</p>
  </div>

  <div class="terms-section">
    <h2>2. User Accounts</h2>
    <p>To list items or make purchases, you must register for an account. You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account.</p>
  </div>

  <div class="terms-section">
    <h2>3. Listing Guidelines</h2>
    <p>Sellers agree that all items listed are legal, authentic, and accurately described. Baghaus reserves the right to remove any listing that violates our community standards, including but not limited to:</p>
    <ul>
      <li>Prohibited or illegal substances.</li>
      <li>Counterfeit or trademark-infringing goods.</li>
      <li>Misleading or fraudulent descriptions.</li>
    </ul>
  </div>

  <div class="terms-section">
    <h2>4. Fees and Payments</h2>
    <p>Baghaus provides a transparent platform. Any transaction fees or service charges will be clearly displayed before a transaction is finalized. We are not responsible for disputes arising from external payment processors.</p>
  </div>

  <div class="terms-section">
    <h2>5. Limitation of Liability</h2>
    <p>Baghaus is a marketplace facilitator. We are not responsible for the quality, safety, or legality of items advertised, the truth or accuracy of listings, or the ability of sellers to sell items.</p>
  </div>

  <div class="terms-section">
    <h2>6. Changes to Terms</h2>
    <p>We may modify these terms at any time. Your continued use of the platform following the posting of changes will mean that you accept and agree to the changes.</p>
  </div>
</div>

<?php include 'footer.php'; ?>

</body>
</html>
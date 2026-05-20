<?php
require_once 'db.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$message = "";
$message_type = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT id, username, password_hash FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['username'] = $user['username'];
                header("Location: index.php");
                exit;
            } else {
                $message      = "Invalid email or password.";
                $message_type = "error";
            }
        } catch (PDOException $e) {
            $message      = "Login error: " . $e->getMessage();
            $message_type = "error";
        }
    } else {
        $message      = "Please fill in all fields.";
        $message_type = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Baghaus</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .auth-page {
      min-height: 100vh;
      display: grid;
      grid-template-columns: 1fr 1fr;
    }
    .auth-visual {
      background: var(--espresso);
      position: relative;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      justify-content: flex-end;
      padding: 4rem;
    }
    .auth-visual img {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      opacity: .35;
    }
    .auth-visual-content { position: relative; z-index: 1; }
    .auth-visual-eyebrow {
      font-size: .68rem;
      font-weight: 700;
      letter-spacing: .2em;
      text-transform: uppercase;
      color: var(--gold);
      margin-bottom: .75rem;
    }
    .auth-visual-title {
      font-family: var(--font-display);
      font-size: clamp(2.2rem, 3.5vw, 3.5rem);
      font-weight: 300;
      color: var(--cream);
      line-height: 1.1;
      margin-bottom: 1rem;
    }
    .auth-visual-title em { color: var(--gold-light); font-style: italic; }
    .auth-visual-sub {
      font-size: .9rem;
      color: rgba(255,255,255,.6);
      max-width: 320px;
      line-height: 1.8;
    }
    .auth-form-side {
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 3rem 2rem;
      background: var(--cream);
    }
    .auth-form-inner {
      width: 100%;
      max-width: 400px;
    }
    .auth-logo {
      font-family: var(--font-display);
      font-size: 1.6rem;
      font-weight: 600;
      color: var(--espresso);
      text-decoration: none;
      display: inline-block;
      margin-bottom: 2.5rem;
    }
    .auth-logo span { color: var(--gold); }
    .auth-heading {
      font-family: var(--font-display);
      font-size: 2rem;
      font-weight: 300;
      color: var(--espresso);
      margin-bottom: .4rem;
    }
    .auth-sub {
      font-size: .875rem;
      color: var(--gray-400);
      margin-bottom: 2rem;
    }
    .auth-sub a { color: var(--brown); font-weight: 600; text-decoration: none; transition: color .2s; }
    .auth-sub a:hover { color: var(--gold); }
    .auth-alert {
      padding: .85rem 1.1rem;
      border-radius: var(--radius-md);
      font-size: .875rem;
      font-weight: 500;
      margin-bottom: 1.25rem;
    }
    .auth-alert.error   { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    .auth-alert.success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .auth-form-inner .form-group { margin-bottom: 1.1rem; }
    .auth-form-inner label {
      display: block;
      font-size: .72rem;
      font-weight: 700;
      letter-spacing: .1em;
      text-transform: uppercase;
      color: var(--gray-600);
      margin-bottom: .45rem;
    }
    .auth-form-inner input {
      width: 100%;
      padding: .875rem 1rem;
      border: 1.5px solid var(--gray-200);
      border-radius: var(--radius-md);
      font-family: var(--font-main);
      font-size: .95rem;
      color: var(--espresso);
      background: var(--white);
      transition: border-color .2s, box-shadow .2s;
      box-sizing: border-box;
    }
    .auth-form-inner input:focus {
      outline: none;
      border-color: var(--gold);
      box-shadow: 0 0 0 3px rgba(201,168,76,.15);
    }
    .btn-auth { width: 100%; padding: .95rem; margin-top: .75rem; font-size: .875rem; }
    .auth-footer {
      text-align: center;
      margin-top: 1.75rem;
      font-size: .875rem;
      color: var(--gray-400);
    }
    .auth-footer a { color: var(--brown); font-weight: 600; text-decoration: none; transition: color .2s; }
    .auth-footer a:hover { color: var(--gold); }
    @media (max-width: 768px) {
      .auth-page { grid-template-columns: 1fr; }
      .auth-visual { display: none; }
      .auth-form-side { padding: 2rem 1.25rem; }
    }
  </style>
</head>
<body>

<div class="auth-page">

  <!-- LEFT: visual panel -->
  <div class="auth-visual">
    <img src="https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=1200&q=80" alt="Luxury bags">
    <div class="auth-visual-content">
      <div class="auth-visual-eyebrow">Welcome back</div>
      <h1 class="auth-visual-title">Your next<br>favourite bag<br>is <em>waiting.</em></h1>
      <p class="auth-visual-sub">Log in to browse exclusive listings, manage your bags, and track your orders.</p>
    </div>
  </div>

  <!-- RIGHT: form panel -->
  <div class="auth-form-side">
    <div class="auth-form-inner">

      <a href="index.php" class="auth-logo">Bag<span>haus</span></a>

      <h2 class="auth-heading">Sign in</h2>
      <p class="auth-sub">Don't have an account? <a href="register.php">Create one free &rarr;</a></p>

      <?php if ($message): ?>
        <div class="auth-alert <?php echo $message_type ?>">
          <?php echo htmlspecialchars($message) ?>
        </div>
      <?php endif ?>

      <form action="login.php" method="POST" autocomplete="on">
        <div class="form-group">
          <label for="email">Email Address</label>
          <input type="email" id="email" name="email"
                 placeholder="you@example.com"
                 value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"
                 required autocomplete="email">
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password"
                 placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
                 required autocomplete="current-password">
        </div>
        <button type="submit" class="btn btn-buy btn-auth">Log In</button>
      </form>

      <div class="auth-footer">
        New to Baghaus? <a href="register.php">Create a free account</a>
      </div>

    </div>
  </div>

</div>

</body>
</html>
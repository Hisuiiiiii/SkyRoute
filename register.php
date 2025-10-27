<?php
session_start();
require 'config.php';
$errors = [];

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if(strlen($username) < 3) $errors[] = 'Username must be at least 3 characters.';
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email format.';
    if(strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
    if($password !== $confirm) $errors[] = 'Passwords do not match.';

    if(empty($errors)){
        $stmt = $conn->prepare('SELECT id FROM users WHERE username=? OR email=? LIMIT 1');
        $stmt->bind_param('ss', $username, $email);
        $stmt->execute();
        if($stmt->get_result()->fetch_assoc()){
            $errors[] = 'Username or email already registered.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare('INSERT INTO users (username,email,password_hash) VALUES (?,?,?)');
            $stmt->bind_param('sss', $username, $email, $hash);
            $stmt->execute();
            $_SESSION['user'] = ['id'=>$conn->insert_id,'username'=>$username,'email'=>$email];
            header('Location: index.php'); exit;
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Sign Up | Flight Schedule</title>
<style>
:root{
  --primary: #007bff;
  --primary-dark: #0056b3;
  --white: #ffffff;
  --text: #1b1f23;
  --muted: #6b7280;
  --border: #e6e9ee;
  --card-bg: rgba(255,255,255,0.96);
  --shadow: 0 12px 30px rgba(15,23,42,0.08);
  --radius: 14px;
  --accent-glow: rgba(0,123,255,0.12);
}

body{
  font-family:"Inter","Segoe UI",Roboto,sans-serif;
  margin:0;
  min-height:100vh;
  display:flex;
  align-items:center;
  justify-content:center;
  background:
    linear-gradient(135deg, rgba(3,146,246,0.06), rgba(0,0,0,0.02)),
    url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1600&q=80') center/cover no-repeat;
  position:relative;
  color:var(--text);
}
body::before{
  content:"";
  position:absolute;
  inset:0;
  background:rgba(255,255,255,0.82);
  backdrop-filter:blur(6px);
  z-index:0;
}

/* Auth card */
.auth-card{
  position:relative;
  z-index:1;
  width:100%;
  max-width:440px;
  background:var(--card-bg);
  border-radius:var(--radius);
  box-shadow:var(--shadow);
  padding:40px 36px;
  text-align:center;
}

/* Title section */
.logo{
  display:flex;
  align-items:center;
  justify-content:center;
  gap:12px;
  font-weight:700;
  color:var(--primary-dark);
  font-size:1.6rem;
  margin-bottom:6px;
}
.logo img{height:34px;width:34px;object-fit:contain;border-radius:6px}
.auth-card h2{
  margin:6px 0 22px;
  font-weight:600;
  color:#333;
  font-size:1.1rem;
}

/* Form */
.form{
  max-width:340px;
  margin:0 auto;
  text-align:left;
}
.input{
  width:100%;
  padding:12px 14px;
  margin-bottom:6px;
  border-radius:10px;
  border:1px solid var(--border);
  font-size:15px;
  box-sizing:border-box;
  background:#fff;
  transition:box-shadow .18s ease,border-color .12s ease,transform .06s ease;
}
.input:focus{
  border-color:var(--primary);
  box-shadow:0 6px 18px var(--accent-glow);
  outline:none;
  transform:translateY(-1px);
}
.note{
  font-size:13px;
  color:var(--muted);
  margin-bottom:12px;
}

/* Button */
.btn{
  width:100%;
  display:inline-block;
  padding:12px 14px;
  border-radius:10px;
  border:none;
  background:linear-gradient(180deg,var(--primary),var(--primary-dark));
  color:var(--white);
  font-weight:700;
  font-size:15px;
  cursor:pointer;
  transition:transform .12s ease, box-shadow .12s ease;
  box-shadow:0 6px 18px rgba(3,146,246,0.18);
}
.btn:hover{ transform:translateY(-2px); box-shadow:0 12px 26px rgba(3,146,246,0.22); }

/* Error display */
.error{
  background:#fff5f5;
  color:#b42318;
  border:1px solid #ffdddd;
  padding:12px 14px;
  border-radius:10px;
  margin-bottom:14px;
  font-size:14px;
}
.error ul{margin:0;padding-left:18px;}

/* Link */
.link{
  text-align:center;
  margin-top:18px;
  color:var(--muted);
  font-size:14px;
}
.link a{color:var(--primary);font-weight:600;text-decoration:none;}
.link a:hover{text-decoration:underline;}

@media(max-width:420px){
  .auth-card{padding:28px 18px;}
  .form{max-width:100%;}
}
</style>
</head>
<body>
  <div class="auth-card">
    <div class="logo"><span>SkyRoute</span></div>
    <h2>Create your account</h2>

    <?php if($errors): ?>
      <div class="error"><ul><?php foreach($errors as $e) echo "<li>".htmlspecialchars($e)."</li>"; ?></ul></div>
    <?php endif; ?>

    <form method="post" class="form">
      <input class="input" name="username" placeholder="Username" value="<?=htmlspecialchars($_POST['username'] ?? '')?>" required>
      <div class="note">Username must be at least 3 characters long.</div>

      <input class="input" name="email" type="email" placeholder="Email address" value="<?=htmlspecialchars($_POST['email'] ?? '')?>" required>
      <div class="note">Enter a valid email (e.g., name@example.com).</div>

      <input class="input" name="password" type="password" placeholder="Password" required>
      <div class="note">Password must be at least 6 characters long.</div>

      <input class="input" name="confirm" type="password" placeholder="Confirm Password" required>
      <div class="note">   </div>

      <button class="btn">Sign Up</button>
    </form>

    <div class="link">Already have an account? <a href="login.php">Log in</a></div>
  </div>
  <?php include 'footer.php'; ?>
</body>
</html>

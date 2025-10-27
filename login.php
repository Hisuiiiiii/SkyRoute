<?php
session_start();
require 'config.php';

// Handle login errors
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user'] = $user;
        header("Location: index.php");
        exit;
    } else {
        $errors[] = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login | Flight Schedule</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
:root {
  --primary: #007bff;
  --primary-dark: #0056b3;
  --white: #ffffff;
  --text: #1b1f23;
  --border: #e0e6ed;
  --shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
  --radius: 14px;
}
body {
  font-family: "Inter", "Segoe UI", Roboto, sans-serif;
  margin: 0;
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  color: var(--text);
  background-image: url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1600&q=80');
  background-size: cover;
  position: relative;
}
body::before {
  content: "";
  position: absolute;
  inset: 0;
  background: rgba(255, 255, 255, 0.75);
  backdrop-filter: blur(6px);
  z-index: 0;
}
.auth-card {
  position: relative;
  z-index: 1;
  background: rgba(255, 255, 255, 0.95);
  padding: 40px;
  border-radius: var(--radius);
  box-shadow: var(--shadow);
  max-width: 400px;
  width: 100%;
  text-align: center;
}
.logo {
  font-size: 32px;
  font-weight: 700;
  color: var(--primary);
  margin-bottom: 20px;
}
.form { text-align: left; }
.input {
  width: 92.5%;
  padding: 12px 14px;
  margin-bottom: 14px;
  border-radius: var(--radius);
  border: 1px solid var(--border);
  font-size: 15px;
}
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
.divider {
  margin: 20px 0;
  text-align: center;
  color: #666;
  font-size: 14px;
  position: relative;
}
.divider::before, .divider::after {
  content: "";
  position: absolute;
  width: 40%;
  height: 1px;
  background: #ccc;
  top: 50%;
}
.divider::before { left: 0; }
.divider::after { right: 0; }
#googleBtn {
  display: flex;
  justify-content: center;
  margin-top: 15px;
}

#googleBtn img {
  width: 22px;
  height: 22px;
  margin-right: 10px;
}
.error {
  background: #ffeaea;
  color: #b00020;
  padding: 10px 14px;
  border-radius: var(--radius);
  font-size: 14px;
  text-align: left;
  margin-bottom: 16px;
}

</style>
<script src="https://accounts.google.com/gsi/client" async defer></script>
</head>
<body>
    <div class="auth-card">
        <div class="logo">SkyRoute</div>
        <h3>Sign in to Continue</h3>

        <?php if ($errors): ?>
          <div class="error"><ul><?php foreach($errors as $e) echo "<li>".htmlspecialchars($e)."</li>"; ?></ul></div>
        <?php endif; ?>

        <form method="post" class="form">
            <input class="input" name="email" type="email" placeholder="Email" required>
            <input class="input" name="password" type="password" placeholder="Password" required>
            <button class="btn">Log In</button>
        </form>

        <div class="divider">OR</div>

        <div id="googleBtn" class="google-icon-only"></div>

        <script>
            google.accounts.id.renderButton(
             document.getElementById("googleBtn"),
            {
      theme: "outline", // required field but we’ll override it
      size: "large",
      type: "icon", // forces only the Google "G" icon
      shape: "circle" // makes it round
            }
             );
        </script>


        <div class="link" style="margin-top: 15px;">Don't have an account? <a href="register.php">Sign up</a></div>
    </div>

<script>
function handleCredentialResponse(response) {
    fetch("google_callback.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ credential: response.credential })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.location.href = "index.php";
        } else {
            alert("Google login failed.");
        }
    });
}

window.onload = function () {
    google.accounts.id.initialize({
        client_id: "325178256509-b6t40c9rl7u41m2thdspk84ldog38gp6.apps.googleusercontent.com",
        callback: handleCredentialResponse
    });
    google.accounts.id.renderButton(
        document.getElementById("googleBtn"),
        {
            type: "icon", // <--- this shows only the Google icon
            theme: "outline",
            size: "large",
            shape: "circle"
        }
    );
};
</script>
<?php include 'footer.php'; ?>

</body>
</html>

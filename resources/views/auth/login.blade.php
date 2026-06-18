<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — Face React Dashboard</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
  *{box-sizing:border-box;margin:0;padding:0}
  body{font-family:'Inter',sans-serif;background:#0f1117;min-height:100vh;display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden}
  body::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse at 30% 20%,rgba(34,197,94,.08) 0%,transparent 50%),radial-gradient(ellipse at 70% 80%,rgba(59,130,246,.08) 0%,transparent 50%);pointer-events:none}
  .card{background:#1a1d27;border:1px solid rgba(255,255,255,.08);border-radius:20px;padding:48px 40px;width:100%;max-width:420px;position:relative;z-index:1;box-shadow:0 32px 64px rgba(0,0,0,.5)}
  .logo{display:flex;align-items:center;gap:12px;margin-bottom:36px;justify-content:center}
  .logo-icon{width:48px;height:48px;background:linear-gradient(135deg,#22c55e,#16a34a);border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:24px;box-shadow:0 8px 20px rgba(34,197,94,.3)}
  .logo-text h1{font-size:20px;font-weight:700;color:#f1f5f9;letter-spacing:-.3px}
  .logo-text p{font-size:12px;color:#64748b;margin-top:2px}
  h2{font-size:26px;font-weight:700;color:#f1f5f9;margin-bottom:6px;text-align:center}
  .subtitle{font-size:14px;color:#64748b;text-align:center;margin-bottom:32px}
  .field{margin-bottom:20px}
  label{display:block;font-size:13px;font-weight:500;color:#94a3b8;margin-bottom:8px}
  input[type=text],input[type=password]{width:100%;background:#0f1117;border:1px solid rgba(255,255,255,.1);border-radius:10px;padding:13px 16px;color:#f1f5f9;font-size:14px;font-family:'Inter',sans-serif;transition:.2s;outline:none}
  input[type=text]:focus,input[type=password]:focus{border-color:#22c55e;box-shadow:0 0 0 3px rgba(34,197,94,.12)}
  .error-msg{background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.25);border-radius:8px;padding:10px 14px;color:#fca5a5;font-size:13px;margin-bottom:20px}
  .remember{display:flex;align-items:center;gap:8px;margin-bottom:24px}
  .remember input{width:16px;height:16px;accent-color:#22c55e;cursor:pointer}
  .remember label{font-size:13px;color:#64748b;margin:0;cursor:pointer;font-weight:400}
  .btn{width:100%;background:linear-gradient(135deg,#22c55e,#16a34a);color:#fff;border:none;border-radius:10px;padding:14px;font-size:15px;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif;transition:.2s;letter-spacing:.3px}
  .btn:hover{transform:translateY(-1px);box-shadow:0 8px 20px rgba(34,197,94,.35)}
  .btn:active{transform:translateY(0)}
  .hint{text-align:center;font-size:12px;color:#475569;margin-top:20px}
  .hint span{color:#22c55e;font-weight:500}
</style>
</head>
<body>
<div class="card">
  <div class="logo">
    <div class="logo-icon">📊</div>
    <div class="logo-text">
      <h1>Face React</h1>
      <p>Feedback Manager</p>
    </div>
  </div>
  <h2>Welcome back</h2>
  <p class="subtitle">Sign in to access the admin dashboard</p>

  @if($errors->any())
  <div class="error-msg">{{ $errors->first() }}</div>
  @endif

  <form method="POST" action="{{ route('login.post') }}">
    @csrf
    <div class="field">
      <label for="username">Username</label>
      <input type="text" id="username" name="username" value="{{ old('username') }}" placeholder="Enter your username" autocomplete="username" autofocus required>
    </div>
    <div class="field">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" placeholder="Enter your password" autocomplete="current-password" required>
    </div>
    <div class="remember">
      <input type="checkbox" id="remember" name="remember">
      <label for="remember">Remember me</label>
    </div>
    <button type="submit" class="btn">Sign in to Dashboard</button>
  </form>
  <p class="hint">Secured by <span>Face React</span> Admin System</p>
</div>
</body>
</html>

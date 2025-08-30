<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>LOGIN • KSU Gilang Gemilang</title>

  <!-- Inter (opsional) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

  <style>
    :root{
      /* Palet – silakan sesuaikan dengan brand guide */
      --brand-navy: #0f2e4e;     /* latar kanan  */
      --brand-navy-2: #143a63;   /* gradasi navy */
      --brand-gold: #f2c94c;     /* tombol emas  */
      --brand-green:#1e7c4a;     /* aksen hijau  */
      --ink-700:   #1f2937;
      --ink-500:   #6b7280;
      --paper:     #f7f9fc;
      --ring:      rgba(15,46,78,.25);
    }

    *{ box-sizing: border-box; }
    html,body{ height:100%; }
    body{
      margin:0; font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans";
      color:var(--ink-700); background: var(--paper);
    }

    .layout{ min-height:100dvh; display:grid; grid-template-columns: 1.2fr 1fr; }
    @media (max-width: 1024px){ .layout{ grid-template-columns: 1fr; } .left{ display:none; } }

    /* LEFT: Slideshow */
    .left{ position:relative; overflow:hidden; background:#eaf0f7; }
    .hero{ position:absolute; inset:0; background-size:cover; background-position:center; transition:opacity .6s ease; }
    .hero.fade-out{ opacity:0; }
    .left .logo-ribbon{
      position:absolute; top:24px; left:24px; display:flex; gap:.75rem; align-items:center;
      background: rgba(255,255,255,.75); padding:.5rem .75rem; border-radius:999px; backdrop-filter: blur(6px);
    }
    .left .logo-ribbon img{ height:36px; width:auto; display:block; }
    .left .headline{
      position:absolute; left:7%; bottom:6%; color:#0e2240; max-width:min(650px, 80%);
      background: rgba(255,255,255,.85); padding:18px 22px; border-radius:14px; line-height:1.45;
      box-shadow: 0 10px 24px rgba(13,41,73,.12); border:1px solid rgba(15,46,78,.06);
      font-weight:700; font-size: clamp(18px, 2vw, 22px);
    }

    /* RIGHT: Auth Card */
    .right{
      display:grid; place-items:center; padding: clamp(16px, 2.5vw, 36px);
      background:
        radial-gradient(1200px 700px at 120% -10%, rgba(255,255,255,.06), transparent 50%),
        radial-gradient(800px 500px at -20% 120%, rgba(255,255,255,.04), transparent 50%),
        linear-gradient(180deg, var(--brand-navy) 0%, var(--brand-navy-2) 100%);
    }
    .card{
      width:min(460px, 92vw); background:#fff; color:var(--ink-700);
      border-radius:18px; padding:28px 28px 24px;
      box-shadow: 0 20px 50px rgba(0,0,0,.25), 0 2px 6px rgba(0,0,0,.08);
      border: 1px solid rgba(17,24,39,.06);
    }
    .brand{ display:flex; align-items:center; gap:12px; margin-bottom:10px; }
    .brand img{ height:48px; width:auto; display:block; }
    .brand h1{ font-size:18px; line-height:1.2; margin:0; letter-spacing:.4px; }
    .muted{ color:var(--ink-500); font-size:12.5px; }
    .spacer{ height:8px; }

    .split{ display:flex; gap:10px; margin-top:10px; }
    .seg{
      flex:1; text-align:center; padding:8px 10px; cursor:pointer; user-select:none;
      border-radius:10px; border:1px solid #e5e7eb; color:#374151; background:#fff; transition:all .2s ease;
    }
    .seg[aria-selected="true"]{ background:var(--brand-navy); color:#fff; border-color:var(--brand-navy); }

    label{ display:block; font-weight:600; font-size:13px; margin:12px 0 6px; }
    .field{
      height:44px; width:100%; border-radius:12px; border:1px solid #e5e7eb; padding:0 14px;
      outline:none; font-size:14px; color:#111827; background:#fff; box-shadow:0 0 0 0 var(--ring);
      transition: box-shadow .15s ease, border-color .15s ease;
    }
    .field:focus{ border-color:#c7d2fe; box-shadow:0 0 0 4px var(--ring); }

    .row{ display:flex; align-items:center; justify-content:space-between; gap:8px; margin-top:10px;}
    .btn{
      height:46px; padding:0 16px; width:100%; border-radius:12px; border:0;
      font-weight:700; letter-spacing:.3px; cursor:pointer; transition: transform .02s ease, filter .2s ease;
    }
    .btn-primary{
      background: linear-gradient(180deg, var(--brand-gold), #e8b92f); color:#1d232a; text-shadow:0 1px 0 rgba(255,255,255,.25);
      box-shadow: 0 10px 20px rgba(242,201,76,.25), inset 0 -1px 0 rgba(0,0,0,.08);
    }
    .btn-primary:hover{ filter: brightness(1.05); }
    .btn:active{ transform: translateY(1px); }

    .hint{ margin-top:10px; text-align:center; font-size:12.5px; color:var(--ink-500); }
    .footer-note{ margin-top:12px; text-align:center; color:#9ca3af; font-size:11.5px; }
  </style>
</head>
<body>
  <main class="layout">
    <!-- LEFT: Slideshow -->
    <section class="left" aria-hidden="true">
      <div class="hero" id="hero" style="background-image:url('{{ asset('img/bg.png') }}')"></div>
      <div class="logo-ribbon">
        <img src="{{ asset('img/LOGO-GG.png') }}" alt="Logo KSU GG" />
        <strong style="color:#0f2e4e">KSU Gilang Gemilang</strong>
      </div>
      <div class="headline">KSU Gilang Gemilang – Loan Origination System</div>
    </section>

    <!-- RIGHT: Auth Card -->
    <section class="right">
      <form method="POST" action="{{ route('login') }}" class="card" autocomplete="on">
        @csrf

        <div class="brand">
          <img src="{{ asset('img/LOGO-GG.png') }}" alt="Logo" />
          <div>
            <h1>KSU GILANG GEMILANG</h1>
            <div class="muted">Loan Origination System</div>
          </div>
        </div>

        <div class="spacer"></div>

        <label for="username">Username</label>
        <input id="username" name="email" class="field"
               value="{{ old('email') }}" required autofocus />

        <label for="password">Password</label>
        <input id="password" name="password" type="password" class="field"/>

        @error('email')   <div class="muted" style="color:#dc2626;margin-top:6px">{{ $message }}</div> @enderror
        @error('password')<div class="muted" style="color:#dc2626;margin-top:6px">{{ $message }}</div> @enderror

        <div class="row">
          <label style="display:flex; gap:8px; align-items:center; margin: 8px 0 0;">
            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
            <span class="muted">Ingat saya</span>
          </label>
          @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="muted" style="text-decoration:none">Lupa password?</a>
          @endif
        </div>

        <div class="spacer"></div>
        <button class="btn btn-primary" type="submit">Masuk</button>
        <div class="footer-note">© {{ date('Y') }} KSU Gilang Gemilang</div>
      </form>
    </section>
  </main>

  <script>
    // Toggle role
    (function(){
      const admin = document.getElementById('seg-admin');
      const user  = document.getElementById('seg-user');
      const role  = document.getElementById('roleInput');
      function sel(who){
        admin.setAttribute('aria-selected', who==='admin');
        user .setAttribute('aria-selected', who==='user');
        role.value = who;
      }
      admin.addEventListener('click', ()=> sel('admin'));
      user .addEventListener('click', ()=> sel('user'));
    })();

    // Slideshow 3 detik
    (function(){
      const el = document.getElementById('hero');
      const slides = [
        "{{ asset('img/bg.png') }}",
        "{{ asset('img/anggota.jpg') }}",
      ];
      let i = 0;
      setInterval(()=>{
        el.classList.add('fade-out');
        setTimeout(()=>{
          i = (i+1) % slides.length;
          el.style.backgroundImage = `url('${slides[i]}')`;
          el.classList.remove('fade-out');
        }, 400);
      }, 3000);
    })();
  </script>
</body>
</html>

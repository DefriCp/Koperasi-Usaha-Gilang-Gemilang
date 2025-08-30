@php $b = config('app.brand'); @endphp
<style>
  :root{
    --brand-navy:  {{ $b['navy']  }};
    --brand-navy2: {{ $b['navy2'] }};
    --brand-gold:  {{ $b['gold']  }};
    --brand-green: {{ $b['green'] }};
    --paper:       {{ $b['paper'] }};
    --ink:         {{ $b['ink']   }};
    --muted:       {{ $b['muted'] }};
    --ring:        {{ $b['ring']  }};
  }
  .gg-hero{
    background:
      radial-gradient(900px 420px at -10% -10%, rgba(255,255,255,.08), transparent 50%),
      radial-gradient(600px 400px at 110% 110%, rgba(255,255,255,.06), transparent 50%),
      linear-gradient(180deg, var(--brand-navy), var(--brand-navy2));
    color:#fff;
  }
  .gg-card{
    background:#fff; border:1px solid rgba(17,24,39,.06); border-radius:16px;
    box-shadow: 0 10px 30px rgba(15,46,78,.06);
  }
  .gg-pill{
    display:inline-flex; align-items:center; gap:.5rem; padding:.4rem .7rem; border-radius:999px;
    background: rgba(255,255,255,.12); color:#fff; border:1px solid rgba(255,255,255,.22);
  }
  .gg-kpi{
    border-left:6px solid var(--brand-gold);
  }
  .gg-kpi-2{
    border-left:6px solid var(--brand-green);
  }
  .gg-button{
    display:inline-flex; align-items:center; justify-content:center; gap:.5rem; height:44px;
    padding:0 16px; border-radius:12px; border:1px solid #e5e7eb; background:#fff; font-weight:600;
    transition: filter .15s ease, transform .02s ease;
  }
  .gg-button:hover{ filter:brightness(1.03); }
  .gg-button:active{ transform:translateY(1px); }
  .gg-primary{
    background: linear-gradient(180deg, var(--brand-gold), #e0b735); color:#1d232a; border:0;
    box-shadow: 0 10px 20px rgba(242,201,76,.25), inset 0 -1px 0 rgba(0,0,0,.08);
  }
  .gg-link{ color:var(--brand-navy2); font-weight:600; text-decoration:none; }
  .gg-link:hover{ text-decoration:underline; }
</style>

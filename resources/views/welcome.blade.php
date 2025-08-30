<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>KSU Gilang Gemilang — LOS</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body
  class="antialiased text-brand-ink
         bg-stone-100
         bg-[radial-gradient(1200px_620px_at_18%_0%,#f2ede7_0%,transparent_70%)]
">

  {{-- NAVBAR --}}
  <header class="sticky top-0 z-30 bg-white/80 backdrop-blur supports-[backdrop-filter]:bg-white/60 border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
      <a href="{{ url('/') }}" class="flex items-center gap-3 group">
        <div class="size-9 rounded-xl bg-gradient-to-br from-brand-orange to-amber-400 grid place-content-center text-white font-black">GG</div>
        <div class="-space-y-0.5">
          <div class="font-extrabold tracking-wide text-brand-ink group-hover:text-brand-green transition">KU GILANG GEMILANG</div>
          <div class="text-[11px] text-gray-500 -mt-0.5">Loan Origination System</div>
        </div>
      </a>

      <div class="flex items-center gap-3">
        <a href="{{ route('login') }}" class="btn-ghost">Login</a>
      </div>
    </div>
  </header>

  {{-- HERO --}}
  <section class="hero-wave">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 md:py-20 grid md:grid-cols-12 gap-10">
      <div class="md:col-span-7">
        <div class="brand-bar mb-6"></div>
        <h1 class="text-4xl sm:text-5xl font-extrabold leading-tight text-brand-ink">
          WELCOME TO <span class="text-brand-orange">OUR COMPANY</span>
        </h1>
        <p class="mt-5 text-lg leading-8 text-gray-700">
          Koperasi Usaha Gilang Gemilang (KU GG) adalah badan usaha berbadan hukum koperasi
          yang berdiri pada <span class="font-semibold">26 April 2007</span> di Bekasi. Kami bekerja
          berdasarkan tata kelola yang <span class="font-semibold text-brand-green">amanah</span>, mandiri, dan tangguh.
        </p>

        <div class="mt-6 flex flex-wrap items-center gap-3">
          <span class="chip">Amanah</span>
          <span class="chip">Mandiri</span>
          <span class="chip">Tangguh</span>
        </div>

        <div class="mt-8 flex flex-wrap gap-3">
          <a href="{{ route('login') }}" class="btn-brand">Masuk Aplikasi</a>
          <a href="mailto:gilanggemilang.kp@gmail.com" class="btn-ghost">Hubungi Kami</a>
        </div>
      </div>

      {{-- Ilustrasi vektor: kotak gradien + logo KU GG di tengah --}}
      <div class="md:col-span-5 relative">
        {{-- dot dekor atas --}}
        <div class="absolute -top-6 -right-2 w-28 h-28 dot-grid rounded-xl opacity-60"></div>

        <div
          class="relative aspect-[4/3] rounded-3xl shadow-soft
                 bg-gradient-to-br from-brand-orange to-amber-300
                 ring-1 ring-black/5 grid place-items-center overflow-hidden"
        >
          {{-- subtle glow di belakang logo --}}
          <div class="absolute h-40 w-40 md:h-64 md:w-64 rounded-full bg-white/20 blur-3xl"></div>

          {{-- LOGO KU GG (besar & responsif) --}}
          <img
            src="{{ asset('img/LOGO-GG.png') }}"
            alt="KSU Gilang Gemilang"
            class="relative h-44 w-44 md:h-56 md:w-56 lg:h-64 lg:w-64
                   object-contain drop-shadow-xl select-none pointer-events-none"
          />
        </div>

        {{-- dot dekor bawah --}}
        <div class="absolute -bottom-8 -left-6 w-24 h-24 rounded-xl dot-grid opacity-60"></div>
      </div>
    </div>
  </section>

  {{-- UNIT USAHA --}}
  <section id="unit-usaha" class="py-12 md:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <h2 class="text-2xl md:text-3xl font-extrabold text-brand-ink">UNIT USAHA</h2>
      <div class="brand-bar my-4"></div>

      <div class="grid md:grid-cols-2 gap-6">
        <article class="card p-6 reveal">
          <div class="flex items-center gap-3">
            <span class="text-brand-orange font-extrabold text-3xl">01.</span>
            <h3 class="text-xl font-bold">CHANNELING</h3>
          </div>
          <p class="mt-3 text-gray-700">
            Pola penyaluran kredit dari bank kepada debitur melalui lembaga keuangan lain
            (BPR, BPRS, koperasi, atau multifinance). <span class="font-semibold text-brand-green">Cepat,
            terstruktur, terukur.</span>
          </p>
        </article>

        <article class="card p-6 reveal delay-150">
          <div class="flex items-center gap-3">
            <span class="text-brand-orange font-extrabold text-3xl">02.</span>
            <h3 class="text-xl font-bold">FRONTING</h3>
          </div>
          <p class="mt-3 text-gray-700">
            Pembiayaan menggunakan perantara bank/lembaga keuangan dengan tata kelola
            yang <span class="font-semibold">standar industri</span> dan dokumentasi rapi.
          </p>
        </article>
      </div>
    </div>
  </section>

  {{-- TAHAPAN KERJASAMA --}}
  <section id="steps" class="py-12 md:py-16 bg-brand-sand/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <h2 class="text-2xl md:text-3xl font-extrabold text-brand-ink">TAHAPAN KERJASAMA</h2>
      <div class="brand-bar my-4"></div>

      <div class="grid md:grid-cols-3 gap-6">
        <div class="card p-6 reveal">
          <h3 class="font-semibold text-brand-green">Perjanjian Kerjasama</h3>
          <ul class="mt-3 space-y-1.5 text-gray-700 list-disc list-inside">
            <li>Perjanjian & kredit pensiun</li>
            <li>Penetapan RAC & administrasi dokumen</li>
          </ul>
        </div>
        <div class="card p-6 reveal delay-150">
          <h3 class="font-semibold text-brand-green">Administrasi</h3>
          <ul class="mt-3 space-y-1.5 text-gray-700 list-disc list-inside">
            <li>Plafon & kuasa substitusi</li>
            <li>Draft perjanjian</li>
          </ul>
        </div>
        <div class="card p-6 reveal delay-300">
          <h3 class="font-semibold text-brand-green">Wilayah Kerja</h3>
          <p class="mt-3 text-gray-700">Penetapan wilayah mitra channeling.</p>
        </div>
      </div>
    </div>
  </section>

  {{-- INFORMASI KREDIT PENSIUN --}}
  <section class="py-12 md:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <h2 class="text-2xl md:text-3xl font-extrabold text-brand-ink">INFORMASI KREDIT PENSIUN</h2>
      <div class="brand-bar my-4"></div>

      <div class="grid md:grid-cols-12 gap-6">
        <div class="md:col-span-7 card p-6 reveal">
          <ul class="space-y-2 text-gray-700 list-disc list-inside">
            <li>Verifikasi & otentikasi penerima manfaat pensiun</li>
            <li>Proses cek dokumen, SLIK, dan mutasi rekening</li>
            <li>Jaminan: Surat Keputusan Pensiun (SKEP)</li>
          </ul>
          <div class="mt-4 flex flex-wrap gap-2">
            <span class="chip border-green-500 text-green-700">Pensiunan Sendiri ✓</span>
            <span class="chip border-green-500 text-green-700">Janda/Duda ✓</span>
            <span class="chip border-red-400 text-red-600">Pensiunan Anak ✕</span>
            <span class="chip border-red-400 text-red-600">Orangtua ✕</span>
          </div>
        </div>

        <div class="md:col-span-5 card p-6 reveal delay-150">
          <h3 class="font-semibold text-brand-green">Kenapa KU GG?</h3>
          <ul class="mt-3 space-y-2 text-gray-700">
            <li>⏱️ Proses <b>gesit</b> & transparan</li>
            <li>🧩 Sistem LOS terintegrasi end-to-end</li>
            <li>🛡️ Tata kelola <b>amanah</b> & kepatuhan</li>
          </ul>
        </div>
      </div>
    </div>
  </section>

  {{-- KEUNGGULAN/STAT --}}
  <section class="py-12 md:py-16 bg-brand-green/5">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <h2 class="text-2xl md:text-3xl font-extrabold text-brand-ink">KEUNGGULAN BISNIS CHANNELING</h2>
      <div class="brand-bar my-4"></div>

      <div class="grid sm:grid-cols-3 gap-6">
        <div class="card p-6 text-center reveal">
          <div class="text-5xl font-black text-brand-orange">76</div>
          <div class="mt-2 text-gray-600">Titik Layanan</div>
        </div>
        <div class="card p-6 text-center reveal delay-150">
          <div class="text-5xl font-black text-brand-orange">188</div>
          <div class="mt-2 text-gray-600">Marketing Aktif</div>
        </div>
        <div class="card p-6 text-center reveal delay-300">
          <div class="text-5xl font-black text-brand-orange">&gt;5</div>
          <div class="mt-2 text-gray-600">Tahun Pengalaman</div>
        </div>
      </div>
    </div>
  </section>

  {{-- PARTNERS --}}
  <section class="py-12 md:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <h2 class="text-2xl md:text-3xl font-extrabold text-brand-ink">OUR PARTNERS</h2>
      <div class="brand-bar my-4"></div>

      <div class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
        @foreach (['KB Bukopin','Bank Mandiri Taspen','Bank MNC','PT. POS','BPR KS','BPR VIMA','BPR Hasamitira','BTN','Reliance','BWS'] as $p)
          <div class="card py-4 px-3 text-center text-sm font-semibold text-gray-700">{{ $p }}</div>
        @endforeach
      </div>
    </div>
  </section>

  {{-- FOOTER / CONTACT --}}
  <footer class="bg-brand-ink text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 grid sm:grid-cols-2 gap-6">
      <div>
        <div class="text-2xl font-extrabold">KSU Gilang Gemilang</div>
        <p class="mt-2 text-white/80 text-sm leading-6">
          Kantor Pusat Operasional: JL. H Terin No. 15, Kel. Pangakalan, Jati Baru, Kec. Cinere, Kota Depok 16514
        </p>
      </div>
      <div class="sm:text-right">
        <div class="text-brand-orange font-bold">CONTACT US</div>
        <div class="mt-1 text-white/80">021-22761361</div>
        <a class="underline hover:text-brand-orange" href="mailto:gilanggemilang.kp@gmail.com">gilanggemilang.kp@gmail.com</a>
      </div>
    </div>
  </footer>

  <script>
    // animasi reveal saat discroll
    const io = new IntersectionObserver((entries) => {
      entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('show'); });
    }, { threshold: .12 });
    document.querySelectorAll('.reveal').forEach(el => io.observe(el));
  </script>
</body>
</html>

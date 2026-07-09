<!DOCTYPE html>
<html lang="id" x-data="adminLayout()"
      :class="darkMode ? 'dark' : ''">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — {{ \App\Models\Setting::get('app_name', 'AbsenKID') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
    <style>
        /* Override body background */
        body { background-color: #EDF2F7; }
        body.dark { background-color: #0f1e2d; }
    </style>
</head>
<body class="dark:text-gray-100 min-h-screen font-sans transition-colors duration-200">

{{-- ── SIDEBAR ── --}}
<aside class="fixed inset-y-0 left-0 z-50 flex flex-col shadow-2xl transform transition-all duration-300"
       style="background:#244C6B"
       :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0', sidebarCollapsed ? 'lg:w-20' : 'w-64']">

    {{-- Logo area --}}
    <div class="flex items-center gap-3 px-5 py-[18px] relative" style="background:rgba(0,0,0,0.15); border-bottom:1px solid rgba(255,255,255,0.08)"
         :class="sidebarCollapsed ? 'lg:px-0 lg:justify-center' : ''">
        <img src="{{ asset('images/dharma-group.png') }}"
             class="h-7 w-auto object-contain"
             style="filter: drop-shadow(0 1px 3px rgba(255,255,255,0.5)) drop-shadow(0 0 8px rgba(255,255,255,0.25))"
             alt="Dharma Group">
        {{-- Collapse toggle (desktop only) --}}
        <button @click="toggleSidebar()"
                class="hidden lg:flex absolute -right-4 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full items-center justify-center text-lg font-bold transition-transform hover:scale-110"
                style="background:#D03F42; color:#fff; box-shadow:0 2px 10px rgba(0,0,0,0.4); border:2px solid #244C6B"
                :class="sidebarCollapsed ? 'rotate-180' : ''"
                title="Ciutkan/Lebarkan menu">❮</button>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto overflow-x-hidden px-3 py-4 space-y-5">

        <div>
            <p class="px-3 mb-1.5 text-xs font-bold uppercase tracking-[0.12em] whitespace-nowrap" style="color:rgba(123,145,161,0.7)" :class="sidebarCollapsed ? 'lg:hidden' : ''">Menu</p>
            @php
                $mainLinks = [
                    ['route' => 'admin.dashboard',              'icon' => '🏠', 'label' => 'Dashboard'],
                    ['route' => 'admin.invitations.index',       'icon' => '🎫', 'label' => 'Undangan'],
                    ['route' => 'admin.invitations.sendHistory', 'icon' => '📤', 'label' => 'Kirim Undangan'],
                    ['route' => 'admin.attendances.index',       'icon' => '✅', 'label' => 'Absensi'],
                    ['route' => 'admin.doorprizes.spin',         'icon' => '🎲', 'label' => 'Doorprize'],
                    ['route' => 'admin.doorprizes.winners',      'icon' => '🏆', 'label' => 'Pemenang'],
                ];
            @endphp
            <div class="space-y-0.5">
            @foreach($mainLinks as $link)
                @php $active = request()->routeIs($link['route'].'*'); @endphp
                <a href="{{ route($link['route']) }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all"
                   :class="sidebarCollapsed ? 'lg:justify-center lg:px-2' : ''"
                   :title="sidebarCollapsed ? '{{ $link['label'] }}' : ''"
                   style="{{ $active
                       ? 'background:#D03F42; color:#fff; box-shadow:0 2px 12px rgba(208,63,66,0.4)'
                       : 'color:rgba(255,255,255,0.65)' }}"
                   @if(!$active) onmouseover="this.style.background='rgba(255,255,255,0.1)';this.style.color='#fff'"
                                 onmouseout="this.style.background='transparent';this.style.color='rgba(255,255,255,0.65)'" @endif>
                    <span class="text-base shrink-0">{{ $link['icon'] }}</span>
                    <span class="whitespace-nowrap" :class="sidebarCollapsed ? 'lg:hidden' : ''">{{ $link['label'] }}</span>
                </a>
            @endforeach
            </div>
        </div>

        @if(auth()->user()->hasRole('admin'))
        <div>
            <p class="px-3 mb-1.5 text-xs font-bold uppercase tracking-[0.12em] whitespace-nowrap" style="color:rgba(123,145,161,0.7)" :class="sidebarCollapsed ? 'lg:hidden' : ''">Pengaturan</p>
            @php
                $settingLinks = [
                    ['route' => 'admin.events.index',    'icon' => '📅', 'label' => 'Events'],
                    ['route' => 'admin.employees.index', 'icon' => '👥', 'label' => 'Master Karyawan'],
                    ['route' => 'admin.subcos.index',    'icon' => '🏢', 'label' => 'Master SubCo'],
                    ['route' => 'admin.settings.index',  'icon' => '⚙️', 'label' => 'Pengaturan Sistem'],
                ];
            @endphp
            <div class="space-y-0.5">
            @foreach($settingLinks as $link)
                @php $active = request()->routeIs($link['route'].'*'); @endphp
                <a href="{{ route($link['route']) }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all"
                   :class="sidebarCollapsed ? 'lg:justify-center lg:px-2' : ''"
                   :title="sidebarCollapsed ? '{{ $link['label'] }}' : ''"
                   style="{{ $active
                       ? 'background:#D03F42; color:#fff; box-shadow:0 2px 12px rgba(208,63,66,0.4)'
                       : 'color:rgba(255,255,255,0.65)' }}"
                   @if(!$active) onmouseover="this.style.background='rgba(255,255,255,0.1)';this.style.color='#fff'"
                                 onmouseout="this.style.background='transparent';this.style.color='rgba(255,255,255,0.65)'" @endif>
                    <span class="text-base shrink-0">{{ $link['icon'] }}</span>
                    <span class="whitespace-nowrap" :class="sidebarCollapsed ? 'lg:hidden' : ''">{{ $link['label'] }}</span>
                </a>
            @endforeach
            </div>
        </div>
        @endif
    </nav>

    {{-- Bottom --}}
    <div class="px-4 py-4" style="border-top:1px solid rgba(255,255,255,0.08); background:rgba(0,0,0,0.15)">
        <a href="{{ route('home') }}" class="flex items-center gap-2 text-xs mb-2.5 transition-colors"
           style="color:rgba(255,255,255,0.45)"
           :class="sidebarCollapsed ? 'lg:justify-center' : ''"
           :title="sidebarCollapsed ? 'Lihat Landing Page' : ''"
           onmouseover="this.style.color='rgba(255,255,255,0.8)'"
           onmouseout="this.style.color='rgba(255,255,255,0.45)'">
            <span>🌐</span><span class="whitespace-nowrap" :class="sidebarCollapsed ? 'lg:hidden' : ''">Lihat Landing Page</span>
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="flex items-center gap-2 text-xs transition-colors w-full text-left"
                    style="color:rgba(208,63,66,0.75)"
                    :class="sidebarCollapsed ? 'lg:justify-center' : ''"
                    :title="sidebarCollapsed ? 'Logout' : ''"
                    onmouseover="this.style.color='#D03F42'"
                    onmouseout="this.style.color='rgba(208,63,66,0.75)'">
                <span>🚪</span><span class="whitespace-nowrap" :class="sidebarCollapsed ? 'lg:hidden' : ''">Logout ({{ auth()->user()->name }})</span>
            </button>
        </form>
    </div>
</aside>

{{-- Overlay mobile --}}
<div x-show="sidebarOpen" @click="sidebarOpen=false"
     class="fixed inset-0 bg-black/50 z-40 lg:hidden" x-cloak></div>

{{-- ── MAIN CONTENT ── --}}
<div class="min-h-screen flex flex-col transition-all duration-300" :class="sidebarCollapsed ? 'lg:pl-20' : 'lg:pl-64'">

    {{-- ── Top bar — NAVY (match sidebar) ── --}}
    <header class="sticky top-0 z-30 flex items-center justify-between px-5 py-0 shadow-md"
            style="background:#244C6B; height:58px; border-bottom:1px solid rgba(0,0,0,0.15)">
        <div class="flex items-center gap-3">
            <button @click="sidebarOpen=!sidebarOpen"
                    class="lg:hidden p-2 rounded-lg transition"
                    style="color:rgba(255,255,255,0.7)"
                    onmouseover="this.style.background='rgba(255,255,255,0.1)'"
                    onmouseout="this.style.background='transparent'">☰</button>
            <h1 class="text-sm font-semibold text-white/90">@yield('page-title', 'Dashboard')</h1>
        </div>
        <div class="flex items-center gap-3">
            {{-- Dark mode toggle --}}
            <button @click="toggleDark()"
                    class="p-2 rounded-lg text-lg transition text-white/60 hover:text-white hover:bg-white/10"
                    title="Toggle Dark/Light">
                <span x-show="!darkMode">🌙</span>
                <span x-show="darkMode" x-cloak>☀️</span>
            </button>
            {{-- User info --}}
            <div class="flex items-center gap-2 pl-3" style="border-left:1px solid rgba(255,255,255,0.15)">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold text-white"
                     style="background:rgba(208,63,66,0.6)">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <span class="text-sm text-white/80 hidden sm:block">{{ auth()->user()->name }}</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold"
                      style="{{ auth()->user()->hasRole('admin')
                          ? 'background:rgba(208,63,66,0.25); color:#ef9899'
                          : 'background:rgba(52,211,153,0.2); color:#6ee7b7' }}">
                    {{ auth()->user()->getRoleNames()->first() }}
                </span>
            </div>
        </div>
    </header>

    {{-- Page content --}}
    <main class="flex-1 p-4 md:p-6">
        @if(session('success'))
            <div class="mb-4 px-4 py-3 rounded-xl text-sm flex items-center gap-2"
                 style="background:#dcfce7; border:1px solid #86efac; color:#15803d">
                ✅ {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 px-4 py-3 rounded-xl text-sm flex items-center gap-2"
                 style="background:rgba(208,63,66,0.08); border:1px solid rgba(208,63,66,0.3); color:#D03F42">
                ❌ {{ session('error') }}
            </div>
        @endif
        @yield('content')
    </main>
</div>

@stack('scripts')
<script>
function adminLayout() {
    return {
        sidebarOpen: false,
        sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
        toggleSidebar() {
            this.sidebarCollapsed = !this.sidebarCollapsed;
            localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
        },
        darkMode: localStorage.getItem('darkMode') !== null
            ? localStorage.getItem('darkMode') === 'true'
            : {{ \App\Models\Setting::get('app_mode','light') === 'dark' ? 'true' : 'false' }},
        toggleDark() {
            this.darkMode = !this.darkMode;
            localStorage.setItem('darkMode', this.darkMode);
            fetch('/admin/set-mode', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                body: JSON.stringify({ mode: this.darkMode ? 'dark' : 'light' })
            });
        }
    }
}
</script>

{{-- Auto-logout setelah 3 menit tidak ada aktivitas (dilacak lintas-tab lewat localStorage,
     supaya tab lain yang masih aktif tidak ikut ke-logout gara-gara satu tab idle) --}}
<form id="auto-logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
    @csrf
</form>
<script>
(function () {
    var TIMEOUT_MS = 3 * 60 * 1000; // 3 menit
    var STORAGE_KEY = 'absenkid_admin_last_activity';
    var timer;

    function markActivity() {
        try { localStorage.setItem(STORAGE_KEY, Date.now().toString()); } catch (e) {}
    }

    function checkIdle() {
        var last = parseInt(localStorage.getItem(STORAGE_KEY) || '0', 10) || Date.now();
        var elapsed = Date.now() - last;
        if (elapsed >= TIMEOUT_MS) {
            document.getElementById('auto-logout-form').submit();
        } else {
            clearTimeout(timer);
            timer = setTimeout(checkIdle, TIMEOUT_MS - elapsed + 500);
        }
    }

    ['mousemove', 'keydown', 'click', 'touchstart', 'scroll'].forEach(function (evt) {
        document.addEventListener(evt, markActivity, { passive: true });
    });
    markActivity();
    timer = setTimeout(checkIdle, TIMEOUT_MS);
})();
</script>
</body>
</html>

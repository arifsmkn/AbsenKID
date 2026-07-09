@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'auth-input w-full px-4 py-2.5 rounded-xl shadow-sm transition-colors focus:outline-none focus:ring-2']) }}
       style="background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.15); color:#ffffff; --tw-ring-color:#244C6B"
       onfocus="this.style.borderColor='#40647E'" onblur="this.style.borderColor='rgba(255,255,255,0.15)'">

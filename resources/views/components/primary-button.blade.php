<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-5 py-2.5 rounded-xl font-semibold text-sm text-white transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-transparent']) }}
        style="background:#D03F42; --tw-ring-color:#D03F42" onmouseover="this.style.background='#b8363a'" onmouseout="this.style.background='#D03F42'">
    {{ $slot }}
</button>

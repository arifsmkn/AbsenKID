<x-guest-layout>
    <div class="text-center mb-6">
        <h1 class="text-xl font-bold text-white">Masuk ke Admin Panel</h1>
        <p class="text-sm text-blue-300/70 mt-1">Kelola event &amp; absensi Dharma Group</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4 text-green-400" :status="session('status')" />

    @if($errors->has('session'))
        <div class="mb-4 px-4 py-3 rounded-xl text-sm" style="background:rgba(208,63,66,0.1); border:1px solid rgba(208,63,66,0.3); color:#ef9899">
            ⚠️ {{ $errors->first('session') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-1.5" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@dharmagroup.co.id" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="mt-1.5"
                            type="password"
                            name="password"
                            required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-white/20 bg-white/5 text-red-500 focus:ring-red-500/50" name="remember">
                <span class="ms-2 text-sm text-white/60">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-blue-300/80 hover:text-white transition-colors" href="{{ route('password.request') }}">
                    {{ __('Lupa password?') }}
                </a>
            @endif
        </div>

        <x-primary-button class="w-full mt-2">
            {{ __('Masuk') }}
        </x-primary-button>
    </form>
</x-guest-layout>

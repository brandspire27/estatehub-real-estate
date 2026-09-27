@extends('layouts.app')

@section('title', 'Sign In — EstateHub')

@section('content')
<div class="min-h-[80vh] flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-slate-50">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="text-center">
            <div class="inline-flex w-12 h-12 rounded-2xl bg-brand-600 text-white items-center justify-center text-2xl shadow-lg shadow-brand-500/25 mb-3">
                <i class="fa-solid fa-city"></i>
            </div>
            <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Welcome back</h2>
            <p class="mt-2 text-sm text-slate-500">
                Sign in to manage your properties, requirements, or matches
            </p>
        </div>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-6 sm:px-10 shadow-xl rounded-3xl border border-slate-200/80">
            
            <form class="space-y-5" action="{{ route('login') }}" method="POST">
                @csrf

                <!-- Email or Phone -->
                <div>
                    <label for="login" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        Email Address or Phone Number
                    </label>
                    <div class="relative">
                        <i class="fa-regular fa-user absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input id="login" name="login" type="text" value="{{ old('login') }}" required autofocus
                               placeholder="e.g. admin@estatehub.com or 9876543210"
                               class="w-full pl-10 pr-3 py-2.5 rounded-xl border border-slate-300 text-sm font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 @error('login') border-rose-500 @enderror">
                    </div>
                    @error('login')
                        <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        Password
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input id="password" name="password" type="password" required
                               placeholder="••••••••"
                               class="w-full pl-10 pr-3 py-2.5 rounded-xl border border-slate-300 text-sm font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                    </div>
                </div>

                <!-- Remember Me & Forgot -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded text-brand-600 focus:ring-brand-500 border-slate-300">
                        <span class="ml-2 text-xs font-medium text-slate-600">Remember me</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" class="w-full py-3 px-4 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm shadow-md shadow-brand-500/25 transition duration-200">
                        Sign In to Account
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center text-xs text-slate-500">
                Don't have an account yet?
                <a href="{{ route('register') }}" class="font-bold text-brand-600 hover:text-brand-700 ml-1">
                    Register Free
                </a>
            </div>

            <!-- Demo Credentials Helper Box -->
            <div class="mt-8 pt-6 border-t border-slate-100">
                <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Quick Demo Accounts (Password: <code class="text-slate-800">password</code>)</span>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <button type="button" onclick="fillLogin('admin@estatehub.com', 'password')" class="p-2 rounded-lg bg-slate-50 hover:bg-slate-100 border border-slate-200 text-left transition">
                        <div class="font-bold text-slate-800">Admin</div>
                        <div class="text-[10px] text-slate-500 truncate">admin@estatehub.com</div>
                    </button>
                    <button type="button" onclick="fillLogin('staff@realestate.com', 'password')" class="p-2 rounded-lg bg-slate-50 hover:bg-slate-100 border border-slate-200 text-left transition">
                        <div class="font-bold text-slate-800">Staff</div>
                        <div class="text-[10px] text-slate-500 truncate">staff@realestate.com</div>
                    </button>
                    <button type="button" onclick="fillLogin('seller@realestate.com', 'password')" class="p-2 rounded-lg bg-slate-50 hover:bg-slate-100 border border-slate-200 text-left transition">
                        <div class="font-bold text-slate-800">Seller</div>
                        <div class="text-[10px] text-slate-500 truncate">seller@realestate.com</div>
                    </button>
                    <button type="button" onclick="fillLogin('buyer@realestate.com', 'password')" class="p-2 rounded-lg bg-slate-50 hover:bg-slate-100 border border-slate-200 text-left transition">
                        <div class="font-bold text-slate-800">Buyer</div>
                        <div class="text-[10px] text-slate-500 truncate">buyer@realestate.com</div>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
function fillLogin(login, password) {
    document.getElementById('login').value = login;
    document.getElementById('password').value = password;
}
</script>
@endsection

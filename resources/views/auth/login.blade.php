<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-slate-100 py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-indigo-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>

        <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-3xl shadow-2xl relative z-10 border border-slate-100">
            
            <div class="text-center">
                <div class="w-20 h-20 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto shadow-lg mb-4 transform -rotate-3 hover:rotate-0 transition-transform duration-300">
                    <i class="fa-solid fa-map-location-dot text-white text-3xl"></i>
                </div>
                <h2 class="text-3xl font-black text-slate-800 tracking-tight">Sistem Monitoring</h2>
                <p class="mt-2 text-sm font-bold text-slate-500 uppercase tracking-widest">Berkas & Aset Nganjuk</p>
            </div>

            @if (session('error'))
                <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl flex items-start gap-3 text-sm font-bold shadow-sm">
                    <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <form class="mt-8 space-y-6" action="{{ route('login') }}" method="POST">
                @csrf
                
                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-[11px] font-extrabold text-slate-600 uppercase tracking-wider mb-1">Alamat Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-regular fa-envelope text-slate-400"></i>
                            </div>
                            <input id="email" name="email" type="email" autocomplete="email" required class="block w-full pl-10 pr-3 py-3 border border-slate-200 rounded-xl text-slate-900 bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white focus:border-transparent text-sm font-semibold transition-all" placeholder="masukkan email anda...">
                        </div>
                        @error('email') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-[11px] font-extrabold text-slate-600 uppercase tracking-wider mb-1">Kata Sandi</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-lock text-slate-400"></i>
                            </div>
                            <input id="password" name="password" type="password" autocomplete="current-password" required class="block w-full pl-10 pr-3 py-3 border border-slate-200 rounded-xl text-slate-900 bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white focus:border-transparent text-sm font-semibold transition-all" placeholder="••••••••">
                        </div>
                        @error('password') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex items-center justify-between mt-4">
                    <div class="flex items-center">
                        <input id="remember_me" name="remember" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-slate-300 rounded cursor-pointer">
                        <label for="remember_me" class="ml-2 block text-xs font-bold text-slate-600 cursor-pointer">Ingat Saya</label>
                    </div>

                    <div class="text-xs">
                        <a href="#" class="font-bold text-blue-600 hover:text-blue-500 transition-colors">Lupa Password?</a>
                    </div>
                </div>

                <div>
                    <button type="submit" class="group relative w-full flex justify-center py-3.5 px-4 border border-transparent text-sm font-extrabold rounded-xl text-white bg-slate-800 hover:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-900 transition-all shadow-lg hover:shadow-xl overflow-hidden">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fa-solid fa-right-to-bracket text-slate-500 group-hover:text-slate-400 transition-colors"></i>
                        </span>
                        Masuk ke Sistem
                    </button>
                </div>
                
                <div class="text-center mt-4">
                    <p class="text-xs font-medium text-slate-500">Belum memiliki akun Mitra? <a href="{{ route('register') }}" class="font-bold text-blue-600 hover:underline">Daftar sekarang</a></p>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
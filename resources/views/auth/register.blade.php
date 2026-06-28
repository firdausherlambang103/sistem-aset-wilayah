<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-slate-100 py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-indigo-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>

        <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-3xl shadow-2xl relative z-10 border border-slate-100">
            
            <div class="text-center">
                <div class="w-20 h-20 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center mx-auto shadow-lg mb-4 transform rotate-3 hover:rotate-0 transition-transform duration-300">
                    <i class="fa-solid fa-user-plus text-white text-3xl"></i>
                </div>
                <h2 class="text-3xl font-black text-slate-800 tracking-tight">Daftar Mitra</h2>
                <p class="mt-2 text-sm font-bold text-slate-500 uppercase tracking-widest">Sistem Aset Nganjuk</p>
            </div>

            <form class="mt-8 space-y-5" action="{{ route('register') }}" method="POST">
                @csrf
                
                <div>
                    <label for="email" class="block text-[11px] font-extrabold text-slate-600 uppercase tracking-wider mb-1">Alamat Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-regular fa-envelope text-slate-400"></i>
                        </div>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username" class="block w-full pl-10 pr-3 py-3 border border-slate-200 rounded-xl text-slate-900 bg-slate-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:border-transparent text-sm font-semibold transition-all" placeholder="email@perusahaan.com">
                    </div>
                    @error('email') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="password" class="block text-[11px] font-extrabold text-slate-600 uppercase tracking-wider mb-1">Kata Sandi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock text-slate-400"></i>
                        </div>
                        <input id="password" name="password" type="password" required autocomplete="new-password" class="block w-full pl-10 pr-3 py-3 border border-slate-200 rounded-xl text-slate-900 bg-slate-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:border-transparent text-sm font-semibold transition-all" placeholder="Minimal 8 karakter">
                    </div>
                    @error('password') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-[11px] font-extrabold text-slate-600 uppercase tracking-wider mb-1">Konfirmasi Kata Sandi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-shield-check text-slate-400"></i>
                        </div>
                        <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="block w-full pl-10 pr-3 py-3 border border-slate-200 rounded-xl text-slate-900 bg-slate-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:border-transparent text-sm font-semibold transition-all" placeholder="Ulangi kata sandi">
                    </div>
                    @error('password_confirmation') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="pt-2">
                    <button type="submit" class="group relative w-full flex justify-center py-3.5 px-4 border border-transparent text-sm font-extrabold rounded-xl text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all shadow-lg hover:shadow-xl overflow-hidden">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fa-solid fa-user-check text-emerald-400 group-hover:text-emerald-300 transition-colors"></i>
                        </span>
                        Daftar sebagai Mitra Baru
                    </button>
                </div>
                
                <div class="text-center mt-4">
                    <p class="text-xs font-medium text-slate-500">Sudah memiliki akun? <a href="{{ route('login') }}" class="font-bold text-emerald-600 hover:underline">Masuk di sini</a></p>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
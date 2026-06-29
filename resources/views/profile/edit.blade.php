<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <i class="fa-solid fa-user-gear mr-2 text-blue-600"></i> {{ __('Pengaturan Profil') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="p-4 sm:p-8 bg-white shadow-sm sm:rounded-2xl border border-slate-100">
                <section class="max-w-xl">
                    <header>
                        <h2 class="text-lg font-bold text-slate-900">Informasi Profil</h2>
                        <p class="mt-1 text-sm text-slate-500">Perbarui informasi profil dan alamat email akun Anda.</p>
                    </header>

                    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
                        @csrf
                        @method('patch')

                        <div>
                            <label class="block font-bold text-xs text-slate-700 mb-1" for="name">NAMA LENGKAP</label>
                            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name"
                                   class="w-full bg-white border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 p-2.5 outline-none font-semibold transition-all" />
                            @error('name')
                                <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block font-bold text-xs text-slate-700 mb-1" for="email">ALAMAT EMAIL</label>
                            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
                                   class="w-full bg-white border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 p-2.5 outline-none font-semibold transition-all" />
                            @error('email')
                                <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center gap-4 pt-2">
                            <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-blue-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition shadow-sm">
                                <i class="fa-solid fa-save mr-2"></i> Simpan Profil
                            </button>

                            @if (session('status') === 'profile-updated')
                                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="text-sm text-emerald-600 font-bold">
                                    <i class="fa-solid fa-check"></i> Tersimpan.
                                </p>
                            @endif
                        </div>
                    </form>
                </section>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow-sm sm:rounded-2xl border border-slate-100">
                <section class="max-w-xl">
                    <header>
                        <h2 class="text-lg font-bold text-slate-900">Perbarui Password</h2>
                        <p class="mt-1 text-sm text-slate-500">Pastikan akun Anda menggunakan password yang panjang dan acak agar tetap aman.</p>
                    </header>

                    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
                        @csrf
                        @method('put')

                        <div>
                            <label class="block font-bold text-xs text-slate-700 mb-1" for="current_password">PASSWORD SAAT INI</label>
                            <input id="current_password" name="current_password" type="password" autocomplete="current-password"
                                   class="w-full bg-white border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 p-2.5 outline-none transition-all" />
                            @if ($errors->updatePassword->has('current_password'))
                                <p class="mt-2 text-sm text-red-600 font-medium">{{ $errors->updatePassword->first('current_password') }}</p>
                            @endif
                        </div>

                        <div>
                            <label class="block font-bold text-xs text-slate-700 mb-1" for="password">PASSWORD BARU</label>
                            <input id="password" name="password" type="password" autocomplete="new-password"
                                   class="w-full bg-white border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 p-2.5 outline-none transition-all" />
                            @if ($errors->updatePassword->has('password'))
                                <p class="mt-2 text-sm text-red-600 font-medium">{{ $errors->updatePassword->first('password') }}</p>
                            @endif
                        </div>

                        <div>
                            <label class="block font-bold text-xs text-slate-700 mb-1" for="password_confirmation">KONFIRMASI PASSWORD BARU</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                                   class="w-full bg-white border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 p-2.5 outline-none transition-all" />
                            @if ($errors->updatePassword->has('password_confirmation'))
                                <p class="mt-2 text-sm text-red-600 font-medium">{{ $errors->updatePassword->first('password_confirmation') }}</p>
                            @endif
                        </div>

                        <div class="flex items-center gap-4 pt-2">
                            <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-slate-800 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-slate-700 transition shadow-sm">
                                <i class="fa-solid fa-key mr-2"></i> Perbarui Password
                            </button>

                            @if (session('status') === 'password-updated')
                                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="text-sm text-emerald-600 font-bold">
                                    <i class="fa-solid fa-check"></i> Password diperbarui.
                                </p>
                            @endif
                        </div>
                    </form>
                </section>
            </div>

            <div class="p-4 sm:p-8 bg-rose-50 shadow-sm sm:rounded-2xl border border-rose-100">
                <section class="max-w-xl">
                    <header>
                        <h2 class="text-lg font-bold text-rose-700">Hapus Akun</h2>
                        <p class="mt-1 text-sm text-rose-600">Peringatan: Setelah akun Anda dihapus, semua data akan hilang secara permanen.</p>
                    </header>

                    <form method="post" action="{{ route('profile.destroy') }}" class="mt-6 space-y-6" onsubmit="return confirm('Apakah Anda benar-benar yakin ingin menghapus akun ini secara permanen?');">
                        @csrf
                        @method('delete')
                        
                        <div>
                            <label class="block font-bold text-xs text-rose-800 mb-1" for="delete_password">PASSWORD KONFIRMASI</label>
                            <input id="delete_password" name="password" type="password" required placeholder="Masukkan password Anda untuk mengkonfirmasi"
                                   class="w-full bg-white border border-rose-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 p-2.5 outline-none transition-all" />
                            @if ($errors->userDeletion->has('password'))
                                <p class="mt-2 text-sm text-rose-600 font-bold">{{ $errors->userDeletion->first('password') }}</p>
                            @endif
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-rose-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-rose-700 transition shadow-sm">
                                <i class="fa-solid fa-trash mr-2"></i> Hapus Akun Permanen
                            </button>
                        </div>
                    </form>
                </section>
            </div>

        </div>
    </div>
</x-app-layout>
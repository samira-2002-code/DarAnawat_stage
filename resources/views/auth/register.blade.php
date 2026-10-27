@extends('layouts.app')

@section('title', __('site.auth.register_title') . ' | Dar Anawat')

@section('content')
    <main class="min-h-screen bg-[#f9f5ef] px-4 py-28 text-[#1d2d2a]">
        <section class="mx-auto max-w-xl rounded-[2rem] border border-[#e7ddca] bg-[#fffdf9] p-6 shadow-[var(--shadow)] sm:p-10">
            <p class="section-kicker">{{ __('site.auth.kicker') }}</p>
            <h1 class="section-title text-4xl">{{ __('site.auth.register_title') }}</h1>
            <p class="mt-4 text-sm leading-6 text-[#4d5b56]">{{ __('site.auth.register_intro') }}</p>

            @if ($errors->any())
                <div class="mt-6 rounded-2xl border border-[#e7b6ae] bg-[#fff1ef] p-5 text-sm text-[#7b2e24]" role="alert">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register.store') }}" method="POST" class="mt-8 space-y-5">
                @csrf
                <div>
                    <label for="name" class="mb-2 block text-sm font-medium">{{ __('site.auth.name') }}</label>
                    <input id="name" name="name" type="text" autocomplete="name" required maxlength="120" value="{{ old('name') }}" class="form-field">
                </div>
                <div>
                    <label for="email" class="mb-2 block text-sm font-medium">{{ __('site.auth.email') }}</label>
                    <input id="email" name="email" type="email" autocomplete="email" required maxlength="254" value="{{ old('email') }}" class="form-field">
                </div>
                <div>
                    <label for="password" class="mb-2 block text-sm font-medium">{{ __('site.auth.password') }}</label>
                    <input id="password" name="password" type="password" autocomplete="new-password" required minlength="12" class="form-field">
                </div>
                <div>
                    <label for="password_confirmation" class="mb-2 block text-sm font-medium">{{ __('site.auth.password_confirmation') }}</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required minlength="12" class="form-field">
                </div>
                <button type="submit" class="gold-button">{{ __('site.auth.register_button') }}</button>
            </form>

            <p class="mt-6 text-sm text-[#4d5b56]">
                {{ __('site.auth.have_account') }}
                <a href="{{ route('login') }}" class="font-semibold text-[#8c6632] underline">{{ __('site.auth.login_link') }}</a>
            </p>
        </section>
    </main>
@endsection

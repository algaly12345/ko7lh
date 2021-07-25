@extends('layouts.front')

    @push('styles')
        <link href="front/css/login-register.css" rel="stylesheet">
    @endpush

    @section('content')
        <!-- BOOKING SECTION START -->
        <section class="booking_step_section">
            <div class="container">
                <div class="row">
                    <div class="col-12 booking_step_heading text-center">
                        <h1>@lang('app.welcomeTo') <span>{{$frontThemeSettings->title }}</span> !</h1>
                    </div>
                    <div class="form_wrapper mx-auto position-relative">
                        <form action="{{ route('login') }}" method="post">
                            @csrf

                            <span class="form_icon"><i class="zmdi zmdi-key"></i></span>

                            <div class="form-group">
                                <input type="email" name="email" id="email" class="form-control form-control-lg {{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" required autofocus placeholder="@lang('app.email')*" id="username" aria-describedby="username">
                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group mt-4">
                                <input type="password" id="password" class="form-control form-control-lg {{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required placeholder="@lang('app.password')*" id="Password" aria-describedby="Password">
                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>


                            <div class="remember_box mt-3 d-flex justify-content-between">
                                <input name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} type="checkbox">
                                <label for="remember" class="mb-3">
                                    <span></span>@lang('app.rememberMe')
                                </label>
                                <a href="{{ route('password.request') }}">@lang('app.forgotPassword')</a>
                            </div>

                            <button type="submit" class="btn btn-dark mx-auto d-block mt-4">@lang('app.signIn')</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
        <!-- BOOKING SECTION END -->
    @endsection

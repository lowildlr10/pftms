@extends('layouts.app')

@section('login-content')

<div class="view">
    <!-- Mask & flexbox options-->
    <div class="mask d-flex justify-content-center align-items-center">

        <!-- Content -->
        <div class="container">

            <!--Grid row-->
            <div class="row wow animated fadeIn ">

                <!--Grid column-->
                <div id="login-title" class="col-sm-12 col-md-6 mb-4 white-text text-center text-md-left p-4 hidden-xs">
                    <h2 class="font-weight-bold text-center">
                        <img class="d-inline-block align-top mb-3" src="{{ asset('images/logo/pftms-logo-v2.svg') }}"
                             height="85" alt="dost logo"><br>
                        <p>Procurement & Financial Transaction Management System</p>
                    </h2>
                    <p align="center">v3.0.0</p>
                    <hr class="hr-light">
                    <p align="center">
                        <strong>Developed by DOST - CAR MIS Unit</strong><br>
                    </p>
                </div>
                <!--Grid column-->

                <!--Grid column-->
                <div id="login-content" class="col-sm-12 col-md-6 col-xl-5 mb-4">

                    <!--Card-->
                    <div class="card login-card">

                        <!--Card content-->
                        <div class="card-body">

                            <!-- Form -->
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <!-- Heading -->
                                <h6 class="dark-grey-text text-center p-2">
                                    <strong><i class="fas fa-key"></i> LOG-IN CREDENTIALS</strong>
                                </h6>
                                <hr>
                                <div class="md-form">
                                    <i class="fa fa-user prefix mdb-color-text"></i>
                                    <input type="text" id="username" class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}"
                                           name="username" value="{{ old('username') }}" autocomplete="off" required autofocus>

                                    @if ($errors->has('username'))
                                        <span class="invalid-feedback ml-5" role="alert">
                                            <strong>{{ $errors->first('username') }}</strong>
                                        </span>
                                    @endif

                                    <label for="username">
                                        Username
                                    </label>
                                </div>
                                <div class="md-form">
                                    <i class="fa fa-lock prefix mdb-color-text"></i>
                                    <input id="form2" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                           name="password" type="password" required>

                                    @if ($errors->has('password'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif

                                    <label for="form2">Password</label>
                                </div>
                                <fieldset class="form-check">
                                    <input type="checkbox" class="form-check-input" id="checkbox1"
                                           name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label for="checkbox1" class="form-check-label dark-grey-text">Remember Me</label>
                                </fieldset>
                                <hr>
                                <div class="login-buttons text-center">
                                    <button class="btn btn-mdb-color btn-md">
                                        <i class="fas fa-door-open"></i> Log-in
                                    </button>
                                    <a class="btn btn-link btn-md" href="{{ route('profile-registration') }}">
                                        Click Here to Register
                                    </a>
                                    <!--
                                    <a class="btn btn-link btn-md" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                    -->
                                    <hr>
                                    <p class="d-none d-block m-0">
                                        <!--
                                        <strong>Not yet registered?</strong>
                                        <a class="btn btn-link btn-md" href="{{ route('profile-registration') }}">
                                            Click Here to Register
                                        </a>
                                        -->
                                    </p>
                                </div>
                            </form>
                            <!-- Form -->

                        </div>

                    </div>
                    <!--/.Card-->

                </div>
                <!--Grid column-->

            </div>
            <!--Grid row-->

        </div>
        <!-- Content -->

    </div>
    <!-- Mask & flexbox options-->
</div>

<!--
<div class="modal fade" id="modalRegisterForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header text-center">
                <h4 class="modal-title w-100 font-weight-bold">Sign up</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body mx-3">
                <div class="md-form mb-5">
                    <i class="fa fa-user prefix grey-text"></i>
                    <input type="text" id="orangeForm-name" class="form-control validate">
                    <label data-error="wrong" data-success="right" for="orangeForm-name">Firstname</label>
                </div>
                <div class="md-form mb-5">
                    <i class="fa fa-user prefix grey-text"></i>
                    <input type="email" id="orangeForm-email" class="form-control validate">
                    <label data-error="wrong" data-success="right" for="orangeForm-email">Middlename</label>
                </div>
                <div class="md-form mb-5">
                    <i class="fa fa-user prefix grey-text"></i>
                    <input type="email" id="orangeForm-email" class="form-control validate">
                    <label data-error="wrong" data-success="right" for="orangeForm-email">Lastname</label>
                </div>

                <hr>

                <div class="md-form mb-5">
                    <i class="fa fa-user prefix grey-text"></i>
                    <input type="email" id="orangeForm-email" class="form-control validate">
                    <label data-error="wrong" data-success="right" for="orangeForm-email">Username</label>
                </div>
                <div class="md-form mb-5">
                    <i class="fa fa-user prefix grey-text"></i>
                    <input type="email" id="orangeForm-email" class="form-control validate">
                    <label data-error="wrong" data-success="right" for="orangeForm-email">Email</label>
                </div>
                <div class="md-form mb-4">
                    <i class="fa fa-lock prefix grey-text"></i>
                    <input type="password" id="orangeForm-pass" class="form-control validate">
                    <label data-error="wrong" data-success="right" for="orangeForm-pass">Password</label>
                </div>
                <div class="md-form mb-4">
                    <i class="fa fa-lock prefix grey-text"></i>
                    <input type="password" id="orangeForm-pass" class="form-control validate">
                    <label data-error="wrong" data-success="right" for="orangeForm-pass">Confirm password</label>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-center">
                <button class="btn btn-success">Sign up</button>
            </div>
        </div>
    </div>
</div>
-->

@endsection

@section('custom-js')

@if (!empty(session("success")))
    @include('modals.alert')
    <script type="text/javascript">
        $(function() {
            $('#modal-success').modal();
        });
    </script>
@elseif (!empty(session("warning")))
    @include('modals.alert')
    <script type="text/javascript">
        $(function() {
            $('#modal-warning').modal();
        });
    </script>
@elseif (!empty(session("failed")))
    @include('modals.alert')
    <script type="text/javascript">
        $(function() {
            $('#modal-failed').modal();
        });
    </script>
@endif

@endsection

@extends('layouts.app')

@section('login-content')

<div id="module-body" class="container-fluid mt-4">
    <div class="page-wrapper default-theme sidebar-bg">
        <main class="page-content pt-5">
            <form method="POST" id="form-store" action="{{ route('profile-store') }}"
                enctype="multipart/form-data">
                @csrf

                <div class="row wow animated fadeIn">
                    <section class="mb-5 col-12 d-flex justify-content-center">
                        <div class="card text-white mdb-color darken-3">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <strong>
                                        <i class="fas fa-user-plus"></i> Profile Registration
                                    </strong>
                                </h5>
                                <hr class="white">

                                <!-- Table with panel -->
                                <div class="card card-cascade narrower mt-5">

                                    <!--Card image-->
                                    <div class="view view-cascade gradient-card-header unique-color
                                                narrower py-2 mx-4 mb-3 d-flex justify-content-between
                                                align-items-center" style="height: 59px;">
                                        <div>
                                            <a href="{{ url('/') }}" class="btn btn-outline-white btn-rounded btn-sm px-2">
                                                <i class="fas fa-backspace"></i> Cancel
                                            </a>
                                        </div>
                                        <div></div>
                                        <div>
                                            <a class="btn btn-outline-white btn-rounded btn-sm px-2"
                                            onclick="$(this).register();">
                                                <i class="fas fa-user-plus"></i> Register
                                            </a>
                                        </div>
                                    </div>
                                    <!--/Card image-->

                                    <div class="px-4 mdb-color-text">
                                        <div class="card z-depth-1 mb-3">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-4 text-center">
                                                        <div class="file-field">
                                                            <img id="img-upload" class="img-thumbnail rounded-circle z-depth-1-half avatar-pic"
                                                                src="{{ asset('images/avatar/male.png') }}"
                                                                style="width: 16em;">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <div class="md-form mt-5">
                                                            <input type="text" name="emp_id" class="form-control required"
                                                                id="emp-id">
                                                            <label for="emp-id">
                                                                Employee ID <span class="red-text">*</span>
                                                            </label>
                                                        </div>

                                                        @if ($errors->has('avatar'))

                                                        <span class="help-block">
                                                            <strong class="red-text">
                                                                The avatar should not exceed maximun dimension of "900x900px".
                                                            </strong>
                                                        </span>

                                                        @endif

                                                        <div class="md-form">
                                                            <div class="file-field">
                                                                <div class="btn btn-primary btn-sm waves-effect float-left">
                                                                    <span>Browse File</span>
                                                                    <input type="file" id="img-input" name="avatar" accept="image/jpeg">
                                                                </div>
                                                                <div class="file-path-wrapper">
                                                                    <input class="file-path validate" type="text" placeholder="Upload your file"
                                                                        value=""
                                                                        readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="card z-depth-1 mb-3">
                                            <div class="card-body">
                                                <h4>Full Name</h4>
                                                <hr>

                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="md-form">
                                                            <input type="text" name="firstname" class="form-control required"
                                                                id="firstname">
                                                            <label for="firstname">
                                                                First Name <span class="red-text">*</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="md-form">
                                                            <input type="text" name="middlename" class="form-control"
                                                                id="middlename">
                                                            <label for="middlename">
                                                                Middle Name
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="md-form">
                                                            <input type="text" name="lastname" class="form-control required"
                                                                id="lastname">
                                                            <label for="lastname">
                                                                Last Name <span class="red-text">*</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="card z-depth-1 mb-3">
                                            <div class="card-body">
                                                <h4>Address Details</h4>
                                                <hr>

                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="md-form">
                                                            <input type="text" name="address" class="form-control required"
                                                                id="address">
                                                            <label for="address">
                                                                Address <span class="red-text">*</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="md-form">
                                                            <select id="sel-region" class="mdb-select md-form required" searchable="Search here.."
                                                                    name="region">
                                                                <option value="" disabled selected>Choose a region *</option>

                                                                @if (!empty($regions))
                                                                    @foreach ($regions as $reg)
                                                                <option value="{{ $reg->id }}">{{ $reg->region_name }}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="md-form" id="province-section">
                                                            <select id="sel-province" class="mdb-select md-form required" searchable="Search here.."
                                                                    name="province">
                                                                <option value="" disabled selected>Choose a province *</option>
                                                                <option value="" disabled>No data</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="card z-depth-1 mb-3">
                                            <div class="card-body">
                                                <h4>Contact Details</h4>
                                                <hr>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="md-form">
                                                            <input type="text" name="mobile_no" class="form-control required"
                                                                id="mobile-no">
                                                            <label for="mobile-no">
                                                                Mobile Number <span class="red-text">*</span>
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="md-form">
                                                            <input type="email" name="email" class="form-control required"
                                                                id="email">
                                                            <label for="email">
                                                                E-mail Address <span class="red-text">*</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="card z-depth-1 mb-3">
                                            <div class="card-body">
                                                <h4>Login Details</h4>
                                                <hr>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="md-form">
                                                            <input type="text" name="username" class="form-control required"
                                                                id="username">
                                                            <label for="username">
                                                                User Name <span class="red-text">*</span>
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="md-form">
                                                            <input id="password" type="password" name="password"
                                                                   class="form-control required">
                                                            <label for="password">
                                                                Password  <span class="red-text">*</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="card z-depth-1 mb-3">
                                            <div class="card-body">
                                                <h4>Other Details</h4>
                                                <hr>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="md-form">
                                                            <input type="text" name="position" class="form-control required"
                                                                id="position">
                                                            <label for="position">
                                                                Position <span class="red-text">*</span>
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="md-form">
                                                            <select class="mdb-select md-form required" searchable="Search here.."
                                                                    name="division">
                                                                <option value="" disabled selected>Choose a division *</option>

                                                                @if (!empty($divisions))
                                                                    @foreach ($divisions as $div)
                                                                <option value="{{ $div->id }}">{{ $div->division_name }}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-6">
                                                        <div class="md-form">
                                                            <select class="mdb-select md-form required" searchable="Search here.."
                                                                    name="emp_type">
                                                                <option value="" disabled selected>Choose an employment type *</option>
                                                                <option value="regular">Regular</option>
                                                                <option value="contractual">Contractual</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="md-form">
                                                            <select class="mdb-select md-form required" searchable="Search here.."
                                                                    name="gender">
                                                                <option value="" disabled selected>Choose a gender *</option>
                                                                <option value="male">Male</option>
                                                                <option value="female">Female</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="card z-depth-1 mb-3">
                                            <div class="card-body">
                                                <h4>e-Signature</h4>
                                                <hr>

                                                <div class="row">
                                                    <div class="col-md-4 signature-thumbnail text-center">
                                                        <div class="file-field">
                                                            <div class="signature-container z-depth-1-half mb-4">
                                                                <img id="sig-upload" class="img-thumbnail"
                                                                    src="{{ asset('images/placeholder.jpg') }}"
                                                                    style="width: 16em;">
                                                                <a>
                                                                <div class="mask waves-effect waves-light rgba-white-slight"></div>
                                                                </a>

                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-8">

                                                        @if ($errors->has('signature'))
                                                        <span class="help-block">
                                                            <strong class="red-text">
                                                                Signature should not exceed "500kb" file size.
                                                            </strong>
                                                        </span>
                                                        @endif

                                                        <div class="md-form">
                                                            <div class="file-field">
                                                                <div class="btn btn-primary btn-sm waves-effect float-left">
                                                                    <span>Browse File</span>
                                                                    <input type="file" id="sig-input" name="signature" accept="image/png">
                                                                </div>
                                                                <div class="file-path-wrapper">
                                                                    <input class="file-path validate" type="text" placeholder="Upload your file"
                                                                        value=""
                                                                        readonly>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Table with panel -->

                            </div>
                        </div>
                    </section>
                </div>
            </form>
        </main>
    </div>
    <!-- page-wrapper -->
</div>

@endsection

@section('custom-js')

<script src="{{ asset('assets/js/input-validation.js') }}"></script>
<script src="{{ asset('assets/js/profile.js') }}"></script>

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

@extends('layouts.app')

@section('main-content')

<form method="POST" id="form-update" action="{{ route('profile-update') }}"
      enctype="multipart/form-data">
    @csrf

    <div class="row wow animated fadeIn">
        <section class="mb-5 col-12 d-flex justify-content-center">
            <div class="card text-white mdb-color darken-3">
                <div class="card-body">
                    <h5 class="card-title">
                        <strong>
                            <i class="fas fa-user-edit"></i> Edit Profile
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
                                <a href="{{ route('profile') }}" class="btn btn-outline-white btn-rounded btn-sm px-2">
                                    <i class="fas fa-backspace"></i> Cancel
                                </a>
                            </div>
                            <div></div>
                            <div>
                                <a class="btn btn-outline-white btn-rounded btn-sm px-2"
                                   onclick="$(this).update();">
                                    <i class="fas fa-user-check"></i> Update
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

                                                @if ($gender == 'male' && empty($avatar))
                                                <img id="img-upload" class="img-thumbnail rounded-circle z-depth-1-half avatar-pic"
                                                     src="{{ asset('images/avatar/male.png') }}"
                                                     style="width: 16em;">
                                                @elseif ($gender == 'female' && empty($avatar))
                                                <img id="img-upload" class="img-thumbnail rounded-circle z-depth-1-half avatar-pic"
                                                     src="{{ asset('images/avatar/female.png') }}"
                                                     style="width: 16em;">
                                                @else
                                                <img id="img-upload" class="img-thumbnail rounded-circle z-depth-1-half avatar-pic"
                                                     src="{{ url($avatar) }}"
                                                     style="width: 16em;">
                                                @endif

                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="md-form mt-5">
                                                <input type="text" name="emp_id" class="form-control required"
                                                       value="{{ $employeeID }}" id="emp-id">
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
                                                               value="{{ str_replace('storage/images/employees/avatars/', '', $avatar) }}"
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
                                                       value="{{ $firstname }}" id="firstname">
                                                <label for="firstname">
                                                    First Name <span class="red-text">*</span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="md-form">
                                                <input type="text" name="middlename" class="form-control"
                                                       value="{{ $middlename }}" class="{{ !empty($middlename) ? 'active' : '' }}"
                                                       id="middlename">
                                                <label for="middlename">
                                                    Middle Name
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="md-form">
                                                <input type="text" name="lastname" class="form-control required"
                                                       value="{{ $lastname }}" id="lastname">
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
                                                       value="{{ $address }}" id="address">
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
                                                    <option value="{{ $reg->id }}" {{ ($reg->id == $region) ? 'selected' : '' }}>
                                                        {{ $reg->region_name }}
                                                    </option>
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

                                                    @if (!empty($provinces))
                                                        @foreach ($provinces as $prov)
                                                            @if ($prov->region == $region)
                                                    <option value="{{ $prov->id }}" {{ ($prov->id == $province) ? 'selected' : '' }}>
                                                        {{ $prov->province_name }}
                                                    </option>
                                                            @endif
                                                        @endforeach
                                                    @endif
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
                                                       value="{{ $mobileNo }}" id="mobile-no">
                                                <label for="mobile-no">
                                                    Mobile Number <span class="red-text">*</span>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="md-form">
                                                <input type="email" name="email" class="form-control required"
                                                       value="{{ $email }}" id="email">
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
                                                       value="{{ $username }}" id="username">
                                                <label for="username">
                                                    User Name <span class="red-text">*</span>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="md-form">
                                                <input id="password" type="password" name="password" class="form-control">
                                                <label for="password">
                                                    Password  <span class="red-text">*</span>
                                                    <span class="red-text" style="font-size: 10px;">
                                                        (Leave this blank if you want to retain your password)
                                                    </span>
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
                                                       value="{{ $position }}" id="position">
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
                                                    <option value="{{ $div->id }}" {{ $div->id == $division ? 'selected': '' }}>
                                                        {{ $div->division_name }}
                                                    </option>
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
                                                    <option value="regular" {{ $employType == 'regular' ? 'selected': '' }}>
                                                        Regular
                                                    </option>
                                                    <option value="contractual" {{ $employType == 'contractual' ? 'selected': '' }}>
                                                        Contractual
                                                    </option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="md-form">
                                                <select class="mdb-select md-form required" searchable="Search here.."
                                                        name="gender">
                                                    <option value="" disabled selected>Choose a gender *</option>
                                                    <option value="male" {{ ($gender == 'male') ? 'selected' : '' }}>
                                                        Male
                                                    </option>
                                                    <option value="female" {{ ($gender == 'female') ? 'selected' : '' }}>
                                                        Female
                                                    </option>
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

                                                    @if (!empty($signature))
                                                    <img id="sig-upload" class="img-thumbnail"
                                                         src="{{ url($signature) }}"
                                                         style="width: 16em;">
                                                    @else
                                                    <img id="sig-upload" class="img-thumbnail"
                                                         src="{{ asset('images/placeholder.jpg') }}"
                                                         style="width: 16em;">
                                                    @endif

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
                                                               value="{{ str_replace('storage/images/employees/signatures/', '', $signature) }}"
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

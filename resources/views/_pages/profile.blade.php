@extends('layouts.app')

@section('main-content')

<form method="POST" id="form-update" action="{{ url('profile/update/'.$employee->emp_id) }}"
      enctype="multipart/form-data">
    @csrf

    <div class="row wow animated fadeIn">
        <section class="mb-5 col-12 d-flex justify-content-center">
            <div class="card text-white mdb-color darken-3">
                <div class="card-body">
                    <h5 class="card-title">
                        <strong>
                            <i class="fas fa-user"></i> Profile
                        </strong>
                    </h5>
                    <hr class="white">
                    <ul class="breadcrumb mdb-color darken-3 mb-4">
                        <li>
                            <i class="fa fa-caret-right mx-2" aria-hidden="true"></i>
                        </li>
                        <li class="active">
                            <a href="{{ url('profile') }}" class="waves-effect waves-light cyan-text">
                                Profile
                            </a>
                        </li>
                    </ul>

                    <!-- Table with panel -->
                    <div class="card card-cascade narrower">

                        <!--Card image-->
                        <div class="view view-cascade gradient-card-header unique-color
                                    narrower py-2 mx-4 mb-3 d-flex justify-content-between
                                    align-items-center" style="height: 59px;">
                            <div></div>
                            <div>
                                <a href="{{ url('profile') }}" class="btn btn-outline-white btn-rounded btn-sm px-2">
                                    <i class="fas fa-sync-alt fa-pulse"></i>
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

                                                @if ($employee->gender == 'male' && empty($employee->avatar))
                                                <img id="img-upload" class="img-thumbnail rounded-circle z-depth-1-half avatar-pic"
                                                     src="{{ asset('images/avatar/male.png') }}"
                                                     style="width: 16em;">
                                                @elseif ($employee->gender == 'female' && empty($employee->avatar))
                                                <img id="img-upload" class="img-thumbnail rounded-circle z-depth-1-half avatar-pic"
                                                     src="{{ asset('images/avatar/female.png') }}"
                                                     style="width: 16em;">
                                                @else
                                                <img id="img-upload" class="img-thumbnail rounded-circle z-depth-1-half avatar-pic"
                                                     src="{{ url($employee->avatar) }}"
                                                     style="width: 16em;">
                                                @endif

                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="md-form mt-5">
                                                <input type="text" name="emp_id" class="form-control required"
                                                       value="{{ $employee->emp_id }}" id="emp-id">
                                                <label for="emp-id">Employee ID</label>
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
                                                               value="{{ str_replace('storage/images/employees/avatars/', '', $employee->avatar) }}"
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
                                                       value="{{ $employee->firstname }}" id="firstname">
                                                <label for="firstname">First Name</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="md-form">
                                                <input type="text" name="middlename" class="form-control"
                                                       value="{{ $employee->middlename }}" id="middlename">
                                                <label for="middlename">Middle Name</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="md-form">
                                                <input type="text" name="lastname" class="form-control required"
                                                       value="{{ $employee->lastname }}" id="lastname">
                                                <label for="lastname">Last Name</label>
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
                                                       value="{{ $employee->address }}" id="address">
                                                <label for="address">Address</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label><strong class="">Region: </strong></label>
                                                <select class="browser-default custom-select required" name="region">

                                                    @if (!empty($regions))
                                                        @foreach ($regions as $region)
                                                            @if ($region->id == $employee->region_id)
                                                    <option value="{{ $region->id }}" selected="selected">
                                                        {{ $region->region }}
                                                    </option>
                                                            @else
                                                    <option value="{{ $region->id }}">
                                                        {{ $region->region }}
                                                    </option>
                                                            @endif
                                                        @endforeach
                                                    @endif

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label><strong class="">Province: </strong></label>
                                                <select class="browser-default custom-select required" name="province">

                                                    @if (!empty($provinces))
                                                        @foreach ($provinces as $province)
                                                            @if ($province->id == $employee->province_id)
                                                    <option value="{{ $province->id }}" selected="selected">
                                                        {{ $province->province }}
                                                    </option>
                                                            @else
                                                    <option value="{{ $province->id }}">
                                                        {{ $province->province }}
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
                                                       value="{{ $employee->mobile_no }}" id="mobile-no">
                                                <label for="mobile-no">Mobile Number</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="md-form">
                                                <input type="email" name="email" class="form-control required"
                                                       value="{{ $employee->email }}" id="email">
                                                <label for="email">E-mail Address</label>
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
                                                       value="{{ $employee->username }}" id="username">
                                                <label for="username">User Name</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="md-form">
                                                <input id="password" type="password" name="password" class="form-control">
                                                <label for="password">
                                                    Password
                                                    <span class="red-text" style="font-size: 10px;">
                                                        (Leave this blank if you want to retain your password)
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label><strong class="">Role: </strong></label>
                                                <select class="browser-default custom-select required" name="role">

                                                    @if (!empty($roles))
                                                        @foreach ($roles as $role)
                                                            @if ($role->id == $employee->role)
                                                    <option value="{{ $role->id }}" selected="selected">{{ $role->role }}</option>
                                                            @else
                                                    <option value="{{ $role->id }}">{{ $role->role }}</option>
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
                                    <h4>Other Details</h4>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="md-form">
                                                <input type="text" name="position" class="form-control required"
                                                       value="{{ $employee->position }}" id="position">
                                                <label for="position">Position</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label><strong>Division: </strong></label>
                                                <select class="browser-default custom-select required" name="division">

                                                    @if (!empty($divisions))
                                                        @foreach ($divisions as $division)
                                                            @if ($division->id == $employee->division_id)
                                                    <option value="{{ $division->id }}" selected="selected">
                                                        {{ $division->division }}
                                                    </option>
                                                            @else
                                                    <option value="{{ $division->id }}">
                                                        {{ $division->division }}
                                                    </option>
                                                            @endif
                                                        @endforeach
                                                    @endif

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label><strong>Employment Type: </strong></label>
                                                <select class="browser-default custom-select required" name="emp_type">
                                                    <option value="regular" {{ $employee->emp_type == 'regular' ? 'selected': '' }}>
                                                        Regular
                                                    </option>
                                                    <option value="contractual" {{ $employee->emp_type == 'contractual' ? 'selected': '' }}>
                                                        Contractual
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label><strong>Gender: </strong></label>
                                                <select class="browser-default custom-select required" name="gender">
                                                    <option value="male" <?php if ($employee->gender == 'male') {echo "selected";} ?>>
                                                        Male
                                                    </option>
                                                    <option value="female" <?php if ($employee->gender == 'female') {echo "selected";} ?>>
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

                                                    @if (!empty($employee->signature))
                                                    <img id="sig-upload" class="img-thumbnail"
                                                         src="{{ url($employee->signature) }}"
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
                                                               value="{{ str_replace('storage/images/employees/signatures/', '', $employee->signature) }}"
                                                               readonly>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-orange btn-block mb-3"
                                    onclick="$(this).update('{{ $employee->emp_id }}');">
                                <i class="fas fa-edit"></i> Update
                            </button>
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

<script src="{{ asset('assets/js/profile.js') }}"></script>

@if (!empty(session("success")))
    @include('layouts.partials.modals.alert')
    <script type="text/javascript">
        $(function() {
            $('#modal-success').modal();
        });
    </script>
@elseif (!empty(session("warning")))
    @include('layouts.partials.modals.alert')
    <script type="text/javascript">
        $(function() {
            $('#modal-warning').modal();
        });
    </script>
@elseif (!empty(session("failed")))
    @include('layouts.partials.modals.alert')
    <script type="text/javascript">
        $(function() {
            $('#modal-failed').modal();
        });
    </script>
@endif

@endsection

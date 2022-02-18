@extends('layouts.app')

@section('main-content')

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
                <!-- Table with panel -->
                <div class="card card-cascade narrower mt-5">
                    <!--Card image-->
                    <div class="view view-cascade gradient-card-header unique-color
                                narrower py-2 mx-4 mb-3 d-flex justify-content-between
                                align-items-center" style="height: 59px;">
                        <div></div>
                        <div>
                            <a href="{{ route('profile-show-edit') }}" class="btn btn-outline-white btn-rounded btn-sm px-2">
                                <i class="fas fa-user-edit"></i> Edit Profile
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
                                            <input type="text" class="form-control" id="emp-id"
                                                   value="{{ $employeeID }}" readonly>
                                            <label for="emp-id">Employee ID</label>
                                        </div>
                                        <div class="md-form">
                                            <input type="text" class="form-control" id="last-login"
                                                   value="{{ $lastLogin }}" readonly>
                                            <label for="last-login">Last Login</label>
                                        </div>
                                        <div>
                                            <b>Role/s: </b>
                                             {{ Auth::user()->getEmployee(Auth::user()->id)->roleName }}
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
                                            <input type="text" class="form-control" id="firstname"
                                                   value="{{ $firstname }}" readonly>
                                            <label for="firstname">First Name</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="md-form">
                                            <input type="text" class="form-control" id="middlename"
                                                   value="{{ $middlename }}" readonly>
                                            <label for="middlename">Middle Name</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="md-form">
                                            <input type="text" class="form-control" id="lastname"
                                                   value="{{ $lastname }}" readonly>
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
                                            <input type="text" id="address" class="form-control"
                                                   value="{{ $address }}" readonly>
                                            <label for="address">Address</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="md-form">
                                            <input type="text" id="region" class="form-control"
                                                   value="{{ $region }}" readonly>
                                            <label for="region">Region</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="md-form">
                                            <input type="text" id="province" class="form-control"
                                                   value="{{ $province }}" readonly>
                                            <label for="province">Province</label>
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
                                            <input type="text" class="form-control"
                                                   value="{{ $mobileNo }}" id="mobile-no" readonly>
                                            <label for="mobile-no">Mobile Number</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="md-form">
                                            <input type="email" class="form-control"
                                                   value="{{ $email }}" id="email">
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
                                            <input type="text" class="form-control"
                                                   value="{{ $username }}" id="username" readonly>
                                            <label for="username">User Name</label>
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
                                            <input type="text" class="form-control"
                                                   value="{{ $position }}" id="position" readonly>
                                            <label for="position">Position</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="md-form">
                                            <input type="text" class="form-control"
                                                   value="{{ $division }}" id="division" readonly>
                                            <label for="division">Division</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="md-form">
                                            <input type="text" class="form-control" readonly
                                                   value="{{ ($employType == 'contractual') ? 'Contractual' : 'Regular' }}"
                                                   id="employ-type">
                                            <label for="employ-type">Employement Type</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="md-form">
                                            <input type="text" class="form-control" readonly
                                                   value="{{ ($gender == 'male') ? 'Male' : 'Female' }}"
                                                   id="gender">
                                            <label for="gender">Gender</label>
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
                                    <div class="col-md-12 signature-thumbnail text-center">
                                        <div class="file-field">
                                            <div class="signature-container z-depth-1-half mb-4 p-2">
                                                @if (!empty($signature))
                                                <img id="sig-upload" class="img-thumbnail"
                                                     src="{{ url($signature) }}"
                                                     style="width: 16em;">
                                                @else
                                                <img id="sig-upload" class="img-thumbnail"
                                                     src="{{ asset('images/placeholder.jpg') }}"
                                                     style="width: 16em;" alt="No e-Signature">
                                                @endif
                                                <a>
                                                    <div class="mask waves-effect waves-light rgba-white-slight"></div>
                                                </a>
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

@endsection

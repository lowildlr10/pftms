<form id="form-update" method="POST" action="{{ route('emp-account-update', ['id' => $id]) }}"
      enctype="multipart/form-data">
    @csrf

    <div class="mdb-color-text">
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
                                   id="emp-id" value="{{ $employeeID }}">
                            <label for="emp-id" class="{{ !empty($employeeID) ? 'active' : '' }}">
                                Employee ID <span class="red-text">*</span>
                            </label>
                        </div>

                        <div class="md-form">
                            <div class="file-field">
                                <div class="btn btn-primary btn-sm waves-effect float-left">
                                    <i class="fas fa-upload"></i>
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
                    <div class="col-md-12">
                        <div class="md-form">
                            <input type="text" name="firstname" class="form-control required"
                                   id="firstname" value="{{ $firstname }}">
                            <label for="firstname" class="{{ !empty($firstname) ? 'active' : '' }}">
                                First Name <span class="red-text">*</span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="md-form">
                            <input type="text" name="middlename" class="form-control"
                                   id="middlename" value="{{ $middlename }}">
                            <label for="middlename" class="{{ !empty($middlename) ? 'active' : '' }}">
                                Middle Name
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="md-form">
                            <input type="text" name="lastname" class="form-control required"
                                   id="lastname" value="{{ $lastname }}">
                            <label for="lastname" class="{{ !empty($lastname) ? 'active' : '' }}">
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
                                   id="address" value="{{ $address }}">
                            <label for="address" class="{{ !empty($address) ? 'active' : '' }}">
                                Address <span class="red-text">*</span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
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

                    <div class="col-md-12">
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
                    <div class="col-md-12">
                        <div class="md-form">
                            <input type="text" name="mobile_no" class="form-control required"
                                   id="mobile-no" value="{{ $mobileNo }}">
                            <label for="mobile-no" class="{{ !empty($mobileNo) ? 'active' : '' }}">
                                Mobile Number <span class="red-text">*</span>
                            </label>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="md-form">
                            <input type="email" name="email" class="form-control required"
                                   id="email" value="{{ $email }}">
                            <label for="email" class="{{ !empty($email) ? 'active' : '' }}">
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
                    <div class="col-md-12">
                        <div class="md-form">
                            <input type="text" name="username" class="form-control required"
                                   id="username" value="{{ $username }}">
                            <label for="username" class="{{ !empty($username) ? 'active' : '' }}">
                                User Name <span class="red-text">*</span>
                            </label>
                        </div>
                    </div>

                    <div class="col-md-12">
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

                    <div class="col-md-12">
                        <div class="md-form">
                            <select class="mdb-select md-form required" searchable="Search here.."
                                    name="roles[]" multiple>
                                <option value="" disabled selected>Choose a role/s</option>

                                @if (!empty($roles))
                                    @foreach ($roles as $rol)
                                <option value="{{ $rol->id }}" {{ in_array($rol->id, $role) ? 'selected': '' }}>
                                    {{ $rol->role }}
                                </option>
                                    @endforeach
                                @endif
                            </select>
                            <label class="mdb-main-label">
                                Roles <span class="red-text">*</span>
                            </label>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="md-form">
                            <select class="mdb-select md-form" searchable="Search here.."
                                    name="groups[]" multiple>
                                <option value="" disabled selected>Choose a group/s</option>

                                @if (!empty($groups))
                                    @foreach ($groups as $grp)
                                <option value="{{ $grp->id }}" {{ in_array($grp->id, $group) ? 'selected': '' }}>
                                    {{ $grp->group_name }}
                                </option>
                                    @endforeach
                                @endif
                            </select>
                            <label class="mdb-main-label">
                                Groups
                            </label>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="md-form">
                            <select class="mdb-select md-form required" searchable="Search here.."
                                    name="is_active">
                                <option value="" disabled selected>Choose active status</option>
                                <option value="y" {{ $isActive == 'y' ? 'selected': '' }}>
                                    Yes
                                </option>
                                <option value="n" {{ $isActive == 'n' ? 'selected': '' }}>
                                    No
                                </option>
                            </select>
                            <label class="mdb-main-label">
                                Active Status <span class="red-text">*</span>
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
                    <div class="col-md-12">
                        <div class="md-form">
                            <input type="text" name="position" class="form-control required"
                                   id="position" value="{{ $position }}">
                            <label for="position" class="{{ !empty($position) ? 'active' : '' }}">
                                Position <span class="red-text">*</span>
                            </label>
                        </div>
                    </div>

                    <div class="col-md-12">
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
                            <label class="mdb-main-label">
                                Division <span class="red-text">*</span>
                            </label>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="md-form">
                            <select class="mdb-select md-form" searchable="Search here.."
                                    name="unit">
                                <option value="" disabled selected>Choose a unit</option>

                                @if (!empty($units))
                                    @foreach ($units as $uni)
                                <option value="{{ $uni->id }}" {{ $uni->id == $unit ? 'selected': '' }}>
                                    {{ $uni->unit_name }}
                                </option>
                                    @endforeach
                                @endif
                            </select>
                            <label class="mdb-main-label">
                                Unit
                            </label>
                        </div>
                    </div>

                    <div class="col-md-12">
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

                    <div class="col-md-12">
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
                            <label class="mdb-main-label">
                                Gender <span class="red-text">*</span>
                            </label>
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
                        <div class="md-form">
                            <div class="file-field">
                                <div class="btn btn-primary btn-sm waves-effect float-left">
                                    <i class="fas fa-upload"></i>
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
</form>

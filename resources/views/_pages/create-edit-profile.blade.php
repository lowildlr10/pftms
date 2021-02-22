@if ($toggle == 'create')
<form method="POST" id="form-create-update" action="{{ url('profile/store') }}"
      enctype="multipart/form-data" class="row wow animated fadeIn">
@else
<form method="POST" id="form-create-update" action="{{ url('profile/update/'.$data->emp_id) }}"
      enctype="multipart/form-data" class="row wow animated fadeIn">
@endif

    @csrf
    <div class="card z-depth-1 mb-3 w-100">
        <div class="card-body">
            <div class="row">
                <div class="col-4 text-center">
                    <div class="file-field">

                        @if ($toggle == 'create')
                        <img id="img-upload" class="img-thumbnail rounded-circle z-depth-1-half avatar-pic"
                             src="{{ asset('images/avatar/male.png') }}"
                             style="width: 16em;">
                        @else
                            @if ($data->gender == 'male' && empty($data->avatar))
                        <img id="img-upload" class="img-thumbnail rounded-circle z-depth-1-half avatar-pic"
                             src="{{ asset('images/avatar/male.png') }}"
                             style="width: 16em;">
                            @elseif ($data->gender == 'female' && empty($data->avatar))
                        <img id="img-upload" class="img-thumbnail rounded-circle z-depth-1-half avatar-pic"
                             src="{{ asset('images/avatar/female.png') }}"
                             style="width: 16em;">
                            @else
                        <img id="img-upload" class="img-thumbnail rounded-circle z-depth-1-half avatar-pic"
                             src="{{ url($data->avatar) }}"
                             style="width: 16em;">
                            @endif
                        @endif

                    </div>
                </div>
                <div class="col-8">
                    <div class="md-form mt-5">
                        <input type="text" name="emp_id" class="form-control required"
                               value="{{ $data->emp_id }}" id="emp-id">
                        <label for="emp-id" {!! !empty($data->emp_id) ? 'class="active"': '' !!}>
                            Employee ID
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
                                       value="{{ str_replace('storage/images/employees/avatars/', '', $data->avatar) }}"
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
                <div class="col-12">
                    <div class="md-form">
                        <input type="text" name="firstname" class="form-control required"
                               value="{{ $data->firstname }}" id="firstname">
                        <label for="firstname" {!! !empty($data->firstname) ? 'class="active"': '' !!}>
                            First Name
                        </label>
                    </div>
                </div>
                <div class="col-12">
                    <div class="md-form">
                        <input type="text" name="middlename" class="form-control"
                               value="{{ $data->middlename }}" id="middlename">
                        <label for="middlename" {!! !empty($data->middlename) ? 'class="active"': '' !!}>
                            Middle Name
                        </label>
                    </div>
                </div>
                <div class="col-12">
                    <div class="md-form">
                        <input type="text" name="lastname" class="form-control required"
                               value="{{ $data->lastname }}" id="lastname">
                        <label for="lastname" {!! !empty($data->lastname) ? 'class="active"': '' !!}>
                            Last Name
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
                <div class="col-12">
                    <div class="md-form">
                        <input type="text" name="address" class="form-control required"
                               value="{{ $data->address }}" id="address">
                        <label for="address" {!! !empty($data->address) ? 'class="active"': '' !!}>
                            Address
                        </label>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label><strong class="">Region: </strong></label>
                        <select class="browser-default custom-select required" name="region">
                            @if (!empty($regions))
                                @foreach ($regions as $region)
                                    @if ($region->id == $data->region_id)
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
                <div class="col-6">
                    <div class="form-group">
                        <label><strong class="">Province: </strong></label>
                        <select class="browser-default custom-select required" name="province">
                            @if (!empty($provinces))
                                @foreach ($provinces as $province)
                                    @if ($province->id == $data->province_id)
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

    <div class="card z-depth-1 mb-3 w-100">
        <div class="card-body">
            <h4>Contact Details</h4>
            <hr>
            <div class="row">
                <div class="col-6">
                    <div class="md-form">
                        <input type="text" name="mobile_no" class="form-control required"
                               value="{{ $data->mobile_no }}" id="mobile-no">
                        <label for="mobile-no" {!! !empty($data->mobile_no) ? 'class="active"': '' !!}>
                            Mobile Number
                        </label>
                    </div>
                </div>
                <div class="col-6">
                    <div class="md-form">
                        <input type="email" name="email" class="form-control required"
                               value="{{ $data->email }}" id="email">
                        <label for="email" {!! !empty($data->email) ? 'class="active"': '' !!}>
                            E-mail Address
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
                <div class="col-6">
                    <div class="md-form">
                        <input type="text" name="username" class="form-control required"
                               value="{{ $data->username }}" id="username">
                        <label for="username" {!! !empty($data->username) ? 'class="active"': '' !!}>
                            User Name
                        </label>
                    </div>
                </div>
                <div class="col-6">
                    <div class="md-form">

                        @if ($toggle == 'create')
                        <input id="password" type="password" name="password" class="form-control required">
                        <label for="password">
                            Password
                        </label>
                        @else
                        <input id="password" type="password" name="password" class="form-control">
                        <label for="password">
                            Password
                            <span class="red-text" style="font-size: 10px;">
                                (Leave this blank if you want to retain your password)
                            </span>
                        </label>
                        @endif


                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label><strong class="">Role: </strong></label>
                        <select class="browser-default custom-select required" name="role">

                            @if (!empty($roles))
                                @foreach ($roles as $role)
                                    @if ($role->id == $data->role)
                            <option value="{{ $role->id }}" selected="selected">{{ $role->role }}</option>
                                    @else
                            <option value="{{ $role->id }}">{{ $role->role }}</option>
                                    @endif
                                @endforeach
                            @endif

                        </select>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label><strong class="">Status: </strong></label>
                        <select class="browser-default custom-select required" name="active">
                            <option value="">-- Select active status --</option>

                            @if ($data->active == 'y')
                            <option value="y" selected="selected">Active</option>
                            <option value="n">Inactive</option>
                            @elseif ($data->active == 'n')
                            <option value="y">Active</option>
                            <option value="n" selected="selected">Inactive</option>
                            @else
                            <option value="y">Active</option>
                            <option value="n">Inactive</option>
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
                <div class="col-6">
                    <div class="md-form">
                        <input type="text" name="position" class="form-control required"
                               value="{{ $data->position }}" id="position">
                        <label for="position" {!! !empty($data->position) ? 'class="active"': '' !!}>
                            Position
                        </label>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label><strong>Division: </strong></label>
                        <select class="browser-default custom-select required" name="division">

                            @if (!empty($divisions))
                                @foreach ($divisions as $division)
                                    @if ($division->id == $data->division_id)
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
                            <option value="regular" {{ $data->emp_type == 'regular' ? 'selected': '' }}>
                                Regular
                            </option>
                            <option value="contractual" {{ $data->emp_type == 'contractual' ? 'selected': '' }}>
                                Contractual
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label><strong>Gender: </strong></label>
                        <select class="browser-default custom-select required" name="gender">
                            <option value="male" {{ $data->gender == 'male' ? 'selected': '' }}>
                                Male
                            </option>
                            <option value="female" {{ $data->gender == 'female' ? 'selected': '' }}>
                                Female
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label><strong>User Group: </strong></label>
                        <select class="browser-default custom-select" name="group">
                            <option value="0">None</option>

                            @if (count($userGroups) > 0)
                                @foreach ($userGroups as $group)
                            <option value="{{ $group->group_id }}" {{ $data->group == $group->group_id ? 'selected': '' }}>
                                {{ $group->group_name }} {{ $group->group_head }}
                            </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card z-depth-1 mb-3 w-100">
        <div class="card-body">
            <h4>e-Signature</h4>
            <hr>
            <div class="row">
                <div class="col-4 signature-thumbnail text-center">
                    <div class="file-field">
                        <div class="signature-container z-depth-1-half mb-4">

                            @if (!empty($data->signature))
                            <img id="sig-upload" class="img-thumbnail"
                                 src="{{ url($data->signature) }}"
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
                <div class="col-8">

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
                                       value="{{ str_replace('storage/images/employees/signatures/', '', $data->signature) }}"
                                       readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card w-100">
        <div class="card-body">
            @if ($toggle == 'create')
            <button id="btn-create-update" type="button" class="btn btn-blue btn-block"
                    onclick="$(this).createUpdate();">
                <i class="fas fa-user-plus"></i> Create
            </button>
            @else
            <button id="btn-create-update" type="button" class="btn btn-orange btn-block"
                    onclick="$(this).createUpdate();">
                <i class="fas fa-user-edit"></i> Edit
            </button>
            @endif
        </div>
    </div>
</form>


<script type="text/javascript">
    $(function() {
        // Image Upload
        $('.btn-file-avatar :file').change(function() {
            var input = $(this),
                label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
                input.trigger('fileselect', [label]);
        });

        $('.btn-file-avatar :file').on('fileselect', function(event, label) {
            var input = $(this).parents('.input-group').find(':text'),
                log = label;

            if( input.length ) {
                input.val(log);
            } else {
                if (log) {
                    alert(log);
                }
            }
        });

        $('.btn-file-signature :file').change(function() {
            var input = $(this),
                label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
                input.trigger('fileselect', [label]);
        });

        $('.btn-file-signature :file').on('fileselect', function(event, label) {
            var input = $(this).parents('.input-group').find(':text'),
                log = label;

            if( input.length ) {
                input.val(log);
            } else {
                if (log) {
                    alert(log);
                }
            }

        });

        function readURL(input, toggle) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    if (toggle == 'avatar') {
                        $('#img-upload').attr('src', e.target.result);
                    } else if (toggle == 'signature') {
                        $('#sig-upload').attr('src', e.target.result);
                    }
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#img-input").change(function(){
            readURL(this, "avatar");
        });

        $("#sig-input").change(function(){
            readURL(this, "signature");
        });
    });
</script>

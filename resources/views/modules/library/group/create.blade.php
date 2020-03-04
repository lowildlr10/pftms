<form id="form-store" method="POST" action="{{ route('emp-group-store') }}">
    @csrf

    <div class="md-form">
        <input type="text" id="group-name" class="form-control required"
               name="group_name">
        <label for="group-name">
            Employee Group Name <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
        <select class="mdb-select md-form" searchable="Search here.."
                name="group_head">
            <option value="" disabled selected>Choose group head</option>
            <option>-- None --</option>

            @if (count($employees) > 0)
                @foreach ($employees as $emp)
            <option value="{{ $emp->id }}">
                {!! $emp->name !!} [{!! $emp->position !!}]
            </option>
                @endforeach
            @endif
        </select>
    </div>
</form>

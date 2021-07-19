<form id="form-store" method="POST" action="{{ route('emp-unit-store') }}">
    @csrf

    <div class="md-form">
        <select class="mdb-select crud-select md-form required" searchable="Search here.."
                name="division">
            <option value="" disabled selected>Choose a division</option>

            @if (count($divisions) > 0)
                @foreach ($divisions as $divsion)
            <option value="{{ $divsion->id }}">
                {!! $divsion->division_name !!}
            </option>
                @endforeach
            @endif
        </select>
        <label class="mdb-main-label">
            Division <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
        <input type="text" id="unit-name" class="form-control required"
               name="unit_name">
        <label for="unit-name">
            Unit Name <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
        <select class="mdb-select crud-select md-form" searchable="Search here.."
                name="unit_head">
            <option value="" disabled selected>Choose a unit head</option>

            @if (count($users) > 0)
                @foreach ($users as $usr)
            <option value="{{ $usr->id }}">
                {!! $usr->firstname !!} {!! $usr->lastname !!}
            </option>
                @endforeach
            @endif
        </select>
        <label class="mdb-main-label">
            Unit Head
        </label>
    </div>
</form>

<form id="form-store" method="POST" action="{{ route('project-store') }}">
    @csrf

    <div class="md-form">
        <input type="text" id="project-title" class="form-control required"
               name="project_title">
        <label for="project-title">
            Project Title <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
        <select class="mdb-select md-form required" searchable="Search here.."
                name="industry_sector">
            <option value="" disabled selected>Choose Industry/Sector</option>
            <option value="">-- None --</option>

            @if (count($industries) > 0)
                @foreach ($industries as $industry)
            <option value="{{ $industry->id }}">
                {!! $industry->sector_name !!}
            </option>
                @endforeach
            @endif
        </select>
        <label class="mdb-main-label">
            Industry/Sector <span class="red-text">*</span>
        </label>
    </div><br>


    <h4>Project Duration</h4>
    <hr>
    <div class="md-form">
        <input type="date" id="date-from" class="form-control required"
               name="date_from">
        <label for="date-from" class="active">
            From <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
        <input type="date" id="date-to" class="form-control required"
               name="date_from">
        <label for="date-to" class="active">
            To <span class="red-text">*</span>
        </label>
    </div><br>


</form>

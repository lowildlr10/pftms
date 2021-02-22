<form id="form-create-update" method="POST" action="{{ url('libraries/store/project') }}">
    @csrf

    <input type="hidden" name="key" value="{{ $key }}">

    <div class="form-group">
        <label>Reference Code:</label>
        <input class="form-control z-depth-1 required" type="text" name="reference_code" 
               value="{{ $referenceCode }}" placeholder="Enter reference code...">
    </div>
    <div class="form-group">
        <label>Project/Charging:</label>
        <input class="form-control z-depth-1 required" type="text" name="project" 
               value="{{ $project }}" placeholder="Enter funding/charging...">
    </div>
    <hr>
    <div class="text-center mt-4">
        <button type="button" id="btn-create-update" type="submit" onclick="$(this).createUpdate();"
                class="btn waves-effect btn-block"></button>
    </div>
</form>
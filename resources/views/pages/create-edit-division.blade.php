<form id="form-create-update" method="POST" action="{{ url('libraries/store/division') }}">
    @csrf

    <div class="form-group">
        <label>Division:</label>
        <input class="form-control z-depth-1 required" type="text" name="division" 
               value="{{ $division }}" placeholder="Enter division...">
        <input type="hidden" name="key" value="{{ $key }}">
    </div>
    <hr>
    <div class="text-center mt-4">
        <button type="button" id="btn-create-update" type="submit" onclick="$(this).createUpdate();"
                class="btn waves-effect btn-block"></button>
    </div>
</form>
<form id="form-create-update" method="POST" action="{{ url('libraries/store/user_group') }}">
    @csrf

    <div class="form-group">
        <label>Group Name:</label>
        <input class="form-control z-depth-1 required" type="text" name="group_name"
               value="{{ $groupName }}" placeholder="Enter group name...">
        <input type="hidden" name="key" value="{{ $key }}">
    </div>
    <div class="form-group">
        <label>Group Head:</label>

        <select name="group_head" class="browser-default custom-select z-depth-1 required">
            <option value="">-- Select a group head --</option>
        @if (count($users) > 0)
            @foreach ($users as $user)
            <option value="{{ $user->id }}" {{ $user->id == $groupHead ? 'selected': '' }}>
                {{ $user->name }}
            </option>
            @endforeach
        @endif
        </select>
    </div>
    <hr>
    <div class="text-center mt-4">
        <button type="button" id="btn-create-update" type="submit" onclick="$(this).createUpdate();"
                class="btn waves-effect btn-block"></button>
    </div>
</form>

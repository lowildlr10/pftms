<form id="form-update" method="POST" action="{{ route('project-update', ['id' => $id]) }}">
    @csrf

    <div class="md-form">
        <input type="text" id="project-title" class="form-control required"
               name="project_title" value="{{ $funding }}">
        <label for="project-title" class="{{ !empty($funding) ? 'active' : '' }}">
            Project Title <span class="red-text">*</span>
        </label>
    </div>
</form>

<form id="form-update" class="wow animated fadeIn d-flex justify-content-center" method="POST"
      action="{{ route('summary-update', ['id' => $id]) }}">
    @csrf
    <div class="card w-responsive w-responsive">
        <div class="card-body py-1">
            <div class="row">
                <div class="col-md-4">
                    <div class="md-form form-sm">
                        <input type="text" id="project-name" name="project_name"
                               class="form-control form-control-sm required" value="{{ $projectName }}">
                        <label for="project_name" class="active">
                            <span class="red-text">* </span>
                            <b>Project name</b>
                        </label>
                    </div>
                </div>
                <div class="col-md-4"></div>
                <div class="col-md-4"></div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-4">

                </div>
                <div class="col-md-4">

                </div>
                <div class="col-md-4">

                </div>
            </div>
        </div>
    </div>
</form>

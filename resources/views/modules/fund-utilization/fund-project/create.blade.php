<form id="form-store" class="wow animated fadeIn d-flex justify-content-center" method="POST"
      action="{{ route('summary-store') }}">
    @csrf
    <div class="card w-responsive">
        <div class="card-body py-1">
             <div class="row">
                <div class="col-md-6">
                    <div class="md-form form-sm">
                        <input type="text" id="project-name" name="project_name"
                               class="form-control form-control-sm required">
                        <label for="project_name">
                            <span class="red-text">* </span>
                            <b>Project name</b>
                        </label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="md-form form-sm">
                        <input type="text" id="project-name" name="project_name"
                               class="form-control form-control-sm required">
                        <label for="project_name">
                            <span class="red-text">* </span>
                            <b>A</b>
                        </label>
                    </div>
                </div>
             </div>
        </div>
    </div>
</form>

<form id="form-store" class="wow animated fadeIn d-flex justify-content-center" method="POST"
      action="{{ route('fund-project-lib-store') }}">
    @csrf
    <div class="card w-responsive">
        <div class="card-body">
            <h4>Project & Date Covered</h4>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <div class="md-form form-sm">
                        <select class="mdb-select crud-select md-form required" searchable="Search here.."
                                name="project">
                            <option value="" disabled selected>Choose a project</option>

                            @if (count($projects) > 0)
                                @foreach ($projects as $project)
                            <option value="{{ $project->id }}">
                                {!! $project->project_name !!}
                            </option>
                                @endforeach
                            @endif
                        </select>
                        <label class="mdb-main-label">
                            <span class="red-text">* </span>
                            <b>Project</b>
                        </label>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="md-form form-sm">
                        <input type="date" id="date_from" name="date_from"
                               class="form-control form-control-sm required">
                        <label for="date_from" class="active mt-3">
                            <span class="red-text">* </span>
                            <b>Date From</b>
                        </label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="md-form form-sm">
                        <input type="date" id="date_to" name="date_to"
                               class="form-control form-control-sm required">
                        <label for="date_to" class="active mt-3">
                            <span class="red-text">* </span>
                            <b>Date To</b>
                        </label>
                    </div>
                </div>
            </div><br>

            <h4>Proposed Budget</h4>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <div class="md-form form-sm">
                        <input type="number" id="to" name="to"
                               class="form-control form-control-sm required">
                        <label for="to">
                            <span class="red-text">* </span>
                            <b>Budget</b>
                        </label>
                    </div>
                </div>
            </div><br>

            <h4>Line-Items</h4>
            <hr>
            <div class="col-md-12 px-0 table-responsive">
                <table class="table table-sm table-hover table-bordered">
                    <thead class="text-center">
                        <tr>
                            <th class="align-middle" width="30%">
                                <b>
                                    <span class="red-text">* </span> Allotment Name
                                </b>
                            </th>
                            <th class="align-middle" width="26%">
                                <b>
                                    <span class="red-text">* </span> Allotment Classification
                                </b>
                            </th>
                            <th class="align-middle" width="26%">
                                <b>
                                    MOOE Account Title
                                </b>
                            </th>
                            <th class="align-middle" width="15%">
                                <b>
                                    <span class="red-text">* </span> Alloted Budget
                                </b>
                            </th>
                            <th width="3%"></th>
                        </tr>
                        </tr>
                    </thead>
                    <tbody>
                        <tr id="item-row-1" class="item-row">
                            <td>
                                <div class="md-form form-sm my-0">
                                    <input type="text" placeholder=" Value..." name="date_issue[]"
                                           class="form-control required form-control-sm date-issue"
                                           id="date-issue-0">
                                </div>
                            </td>
                            <td>
                                <div class="md-form my-0">
                                    <select class="mdb-select required allot-class-tokenizer" multiple="multiple"
                                            name="allotment_classes[0][]"></select>
                                </div>
                            </td>
                            <td>
                                <div class="md-form my-0">
                                    <select class="mdb-select mooe-tokenizer" multiple="multiple"
                                            name="mooe_account_titles[0][]"></select>
                                </div>
                            </td>
                            <td>
                                <div class="md-form form-sm my-0">
                                    <input type="number" placeholder=" Value..." name="allotment_co[]"
                                           class="form-control required form-control-sm allotment-co"
                                           id="allotment-co-0" min="0"
                                           onkeyup="$(this).computeAll()"
                                           onchange="$(this).computeAll()">
                                </div>
                            </td>
                            <td>
                                <a onclick="$(this).deleteRow('#item-row-1');"
                                   class="btn btn-outline-red px-1 py-0">
                                    <i class="fas fa-minus-circle"></i>
                                </a>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="12">
                                <a class="btn btn-outline-light-blue btn-sm btn-block z-depth-0"
                                   onclick="$(this).addRow('.item-row');">
                                    + Add Item
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>

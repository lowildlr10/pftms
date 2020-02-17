<div class="modal fade bottom z-depth-3" id="top-fluid-modal" tabindex="-1"
     role="dialog" data-backdrop="true" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-primary" role="document">
        <form method="POST" action="{{ url()->current() }}">
            @csrf

            <!--Content-->
            <div class="modal-content">

                <!--Body-->
                <div class="modal-body mdb-color p-2">
                    <div class="card">
                        <div class="card-body pb-2 pt-0">
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- Search form -->
                                    <div class="form-inline md-form form-sm">
                                        <i class="fa fa-search" aria-hidden="true"></i>
                                        <input name="search" class="form-control ml-3 w-75" type="text"
                                               placeholder="Search (optional)">
                                    </div>
                                </div>
                                <div class="col-md-12">

                                    @if (stripos(url()->current(), 'procurement/pr'))
                                    <div class="form-inline md-form form-sm mt-0 mb-0">
                                        <i class="fas fa-filter" aria-hidden="true"></i>
                                        <select class="mdb-select-filter md-form colorful-select dropdown-danger ml-3 w-75"
                                                name="filter">
                                            <option value="0" selected>&#9758;  All</option>
                                            <option value="1">&#9758;  Pending</option>
                                            <option value="2">&#9758;  Disapproved</option>
                                            <option value="3">&#9758;  Cancelled</option>
                                            <option value="4">&#9758;  Closed</option>
                                            <option value="5">&#9758;  For Canvass</option>
                                            <option value="6">&#9758;  For PO/JO</option>
                                        </select>
                                    </div>
                                    @elseif (stripos(url()->current(), 'libraries/suppliers'))
                                    <div class="form-inline md-form form-sm mt-0 mb-0">
                                        <i class="fas fa-filter" aria-hidden="true"></i>
                                        <select class="mdb-select-filter md-form colorful-select dropdown-danger ml-3 w-75"
                                                name="filter">

                                            @if ($filter == 0)
                                            <option value="0" selected="selected"> &#9758;  All </option>
                                            @else
                                            <option value="0"> &#9758;  All </option>
                                            @endif

                                            @if (!empty($classifications))
                                                @foreach ($classifications as $class)
                                                    @if ($class->id == $filter)
                                                    <option value="{{ $class->id }}" selected="selected"> &#9758;  {{ $class->classification }} </option>
                                                    @else
                                                    <option value="{{ $class->id }}"> &#9758;  {{ $class->classification }} </option>
                                                    @endif
                                                @endforeach
                                            @endif

                                        </select>
                                    </div>
                                    @endif

                                </div>
                                <div class="col-md-12">
                                    <div class="row d-flex justify-content-end align-items-center mr-3 mt-4">
                                        <button class="btn btn-mdb-color btn-md btn-info btn-sm waves-effect waves-light"
                                                type="submit">
                                            <i class="far fa-check-circle"></i> Search
                                        </button>
                                        <button class="btn btn-md btn-outline-mdb-color btn-sm waves-effect"
                                                data-dismiss="modal" type="button">
                                            Close
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!--/.Content-->
        </form>
    </div>
</div>

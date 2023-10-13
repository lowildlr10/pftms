@extends('layouts.app')

@section('main-content')

<link href="{{ asset('datatables/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<link href="{{ asset('datatables/css/bootstrap.css') }}" rel="stylesheet">

<div class="row wow animated fadeIn">
    <section class="mb-5 col-12 module-container">
        <div class="card mdb-color darken-3">
            <div class="card-body">
                <h5 class="card-title white-text">
                    <strong>
                        <i class="fas fa-box"></i> &#8594;
                        Inventory of Property Acknowledgement Receipt (PAR)
                    </strong>
                </h5>
                <hr class="white hidden-xs">
                <ul class="breadcrumb mdb-color darken-3 mb-0 p-1 white-text hidden-xs">
                    <li>
                        <i class="fa fa-caret-right mx-2" aria-hidden="true"></i>
                    </li>
                    <li class="active">
                        <a href="/par" class="waves-effect waves-light cyan-text">
                            Inventory of Property Acknowledgement Receipt (PAR)
                        </a>
                    </li>
                </ul>

                <!-- Table with panel -->
                <div class="card card-cascade narrower">

                    <!--Card image-->
                    <div class="gradient-card-header unique-color
                                narrower py-2 px-2 mb-1 d-flex justify-content-between
                                align-items-center">
                        <div>
                           {{-- <button type="button" class="btn btn-outline-white btn-rounded btn-sm px-2" data-toggle="modal" data-target=".modal">
                                    <i class="fas fa-pencil-alt"></i> Create
                           </button> --}}
                        </div>
                        <div>

                            <a href="/par" class="btn btn-outline-white btn-rounded btn-sm px-2">
                                <i class="fas fa-sync-alt fa-pulse"></i>
                            </a>
                        </div>
                    </div>
                    <!--/Card image-->

                    <div class="px-2">
                        <div class="table-wrapper table-responsive border rounded">

                            <!--Table-->
                            <table id="Table" class="table table-striped table-bordered table-hover" style="width:100%">

                                <!--Table head-->
                                <thead class="mdb-color darken-3 mb-0 p-1 white-text">
						            <tr>
						                <th>Description</th>
                                        <th>PR_NO</th>
                                        <th>Inventory No.</th>
						                <th>Quantity</th>
                                        <th>Unit Value</th>
                                        <th>Total Cost</th>
                                        <th>Funding</th>
                                        <th>Acquistion Date</th>
                                        <th>Classification Name</th>
                                        <th>Issued To</th>
                                        <th>Care off To</th>
                                        <th>Date of Issuance</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                        {{-- <th>Action</th> --}}
						            </tr>
						        </thead>
						        <tbody>

                                    @foreach ($description as $description)

						            <tr>
						                <td>
                                            <input type="hidden" id="id" value="{!! $description->id !!}">

                                            <span class="item_class">{!! $description->description !!}</span>
                                        </td>
                                        <td>
                                            <span class="date"> {{$description->pr_no}}  </span>
                                        </td>
						                <td>
                                            <span class="date"> {{$description->inventory_no}}  </span>
                                        </td>
                                        <td>
                                            <span class="date"> {{$description->quantity}}</span>
                                        </td>
                                        <td>
                                            <span class="date"> {{$description->unit_cost}} </span>
                                        </td>
                                        <td>
                                            <span class="date"> {{$description->total_cost}} </span>
                                        </td>
                                        <td>
                                            <span class="date"> {{$description->sector_name}} </span>
                                        </td>
                                        <td>
                                            <span class="date"> {{$description->date_po}} </span>
                                        </td>
                                        <td>
                                            <span class="date"> {{$description->classification_name}}  </span>
                                        </td>
                                        <td>
                                            <span class="date"> {{$description->firstname}}, {{$description->lastname}}  </span>
                                        </td>
                                        <td>
                                            <span class="care_of_to"> {{$description->care_of_to}}  </span>
                                        </td>

                                        <td>
                                            <span class="date_of_issuance"> {{$description->date_of_issuance}}  </span>
                                        </td>

                                        <td>
                                            <span class="status"> {{$description->status}}  </span>
                                        </td>
                                        <td>

                                            <a type="button" class="btn-floating btn-sm btn-orange p-2 waves-effect material-tooltip-main mr-0 jel-update-user" title="Update" data-placement="left" align="center">
                                                <i class="fas fa-edit"></i>
                                              {{-- <a class="btn-floating btn-sm btn-red p-2 waves-effect material-tooltip-main mr-0 jel-delate-user"
                                                   data-toggle="tooltip" data-placement="left" align="center" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </a> --}}
                                            </td>
						            </tr>
                                @endforeach
                                <!--Table body-->
							    </tbody>
						        <tfoot class="mdb-color darken-3 mb-0 p-1 white-text">
						            <tr>
						                <th>Description</th>
                                        <th>PR_NO</th>
                                        <th>Inventory No.</th>
						                <th>Quantity</th>
                                        <th>Unit Value</th>
                                        <th>Total Cost</th>
                                        <th>Funding</th>
                                        <th>Acquistion Date</th>
                                        <th>Classification Name</th>
                                        <th>Issued To</th>
                                        <th>Care off To</th>
                                        <th>Date of Issuance</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                        {{-- <th>Action</th> --}}
						            </tr>
						        </tfoot>
                            </table>
                            <!--Table-->
                        </div>
                    </div>
                    <div class="mt-3">
                    </div>
                </div>
                <!-- Table with panel -->
            </div>
        </div>
    </section>
</div>

<!-- update -->
<div class="modal" id="update-user-mdl" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header stylish-color-dark white-text">
          <h5 class="modal-title" id="exampleModalLabel">Update </h5>
          <button type="button" class="close white-text" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
         <form action="/updatepar" method="post" autocomplete="off" enctype="multipart/form-data">
        {!! csrf_field() !!}
        <div class="modal-body">
             <div class="form-row">
                <input type="hidden" id="update-id" name="id" value="">
                <label for="inputPassword3">Care of To <span style="color: red;">*</span></label>
                <div class="col-sm-12 md-form form-sm">
                  <input type="text" class="form-control" id="u-care_of_to" placeholder="Search here.." name="care_of_to" list="list-item-class" id="input-datalist" required="">
                    <datalist id="list-item-class">
                      @foreach ($emp_accounts as $item_class)
                      <option value="{!! $item_class->firstname!!} {!! $item_class->lastname !!}">
                      @endforeach
                    </datalist>
                </div>
                <label for="inputPassword3">Date of Issuance <span style="color: red;">*</span></label>
                <div class="col-sm-12 md-form form-sm">
                      <input type="date" class="form-control" id="u-date_of_issuance" name="date_of_issuance" required="">
                    </div>
                    <label for="inputPassword3">Status <span style="color: red;">*</span></label>
                    <div class="col-sm-12 md-form form-sm">
                          <input type="text" class="form-control" id="u-status" name="status" required="">
                        </div>
             </div>
        </div>
              <div class="modal-footer rgba-stylish-strong p-1">
                <button type="button" class="btn btn btn-light btn-sm waves-effect" data-dismiss="modal">
                    <i class="far fa-window-close"></i> Close</button>
                <button type="submit" class="btn btn-orange btn-sm waves-effect waves-light">
                    <i class="fas fa-pencil-alt"></i> Update</button>
        </div>
      </div>
                </form>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
  <!-- /.modal -->


@endsection

@section('custom-js')

<script src="{{ asset('datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('datatables/js/dataTables.bootstrap4.min.js') }}"></script>

<script>
    $('body').on('click', '.jel-update-user', function(event) {
      event.preventDefault();

    //delete the value
    $('#u-care_of_to').val('');
    $('#u-date_of_issuance').val('');
    $('#u-status').val('');

    //find closest tr
    var trJel = $(this).closest('tr');
    //get the value and declare variables
    var care_of_to = $(trJel).find('.care_of_to').html();

    var id = $(trJel).find('#id').val();
    var date_of_issuance = $(trJel).find('.date_of_issuance').html();
    var status = $(trJel).find('.status').html();

    //add value of the input fields

    $('#update-id').val(id);
    $('#u-care_of_to').val(care_of_to);
    $('#u-date_of_issuance').val(date_of_issuance);
    $('#u-status').val(status);

    $("#update-user-mdl").modal("show");

    });

            </script>

<script type="text/javascript">
	// Call the dataTables jQuery plugin
        $(document).ready(function() {
          $('#Table').DataTable();
        });
	</script>

@endsection

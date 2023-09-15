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
                        Requisition and Issue Slip (RIS)
                    </strong>
                </h5>
                <hr class="white hidden-xs">
                <ul class="breadcrumb mdb-color darken-3 mb-0 p-1 white-text hidden-xs">
                    <li>
                        <i class="fa fa-caret-right mx-2" aria-hidden="true"></i>
                    </li>
                    <li class="active">
                        <a href="/ris" class="waves-effect waves-light cyan-text">
                            Requisition and Issue Slip (RIS)
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

                            <a href="/ris" class="btn btn-outline-white btn-rounded btn-sm px-2">
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
                                        {{-- <th>Action</th> --}}
						            </tr>
						        </thead>
						        <tbody>

                                    @foreach ($_ris as $_ris)

						            <tr>
						                <td>
                                        <input type="hidden" id="id" value="">
                                            <span class="item_class">{!! $_ris->description !!}</span>
                                        </td>
                                        <td>
                                            <span class="date"> {{$_ris->pr_no}}  </span>
                                        </td>
						                <td>
                                            <span class="date"> {{$_ris->inventory_no}}  </span>
                                        </td>
                                        <td>
                                            <span class="date"> {{$_ris->quantity}}</span>
                                        </td>
                                        <td>
                                            <span class="date"> {{$_ris->unit_cost}} </span>
                                        </td>
                                        <td>
                                            <span class="date"> {{$_ris->total_cost}} </span>
                                        </td>
                                        <td>
                                            <span class="date"> {{$_ris->sector_name}} </span>
                                        </td>
                                        <td>
                                            <span class="date"> {{$_ris->date_po}} </span>
                                        </td>
                                        <td>
                                            <span class="date"> {{$_ris->classification_name}}  </span>
                                        </td>
                                        <td>
                                            <span class="date"> {{$_ris->firstname}},{{$_ris->lastname}}  </span>
                                        </td>
						                {{-- <td class="inline">

                                        <a type="button" class="btn-floating btn-sm btn-orange p-2 waves-effect material-tooltip-main mr-0 jel-update-user" title="Update" data-placement="left" align="center">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                            <a class="btn-floating btn-sm btn-red p-2 waves-effect material-tooltip-main mr-0 jel-delate-user"
                                               data-toggle="tooltip" data-placement="left" align="center" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td> --}}
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

<!--Create Modal -->
            <div class="modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true"  data-keyboard="false" data-backdrop="static">
              <div class="modal-dialog" role="document">
                 <div class="modal-content">
                  <div class="modal-header stylish-color-dark white-text">
                    <h5 class="modal-title" id="exampleModalLabel">Create Equipment Name (Classifications)</h5>
                    <button type="button" class="close white-text" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <form action="/create" method="post" autocomplete="off" enctype="multipart/form-data">
                        {!! csrf_field() !!}
                  <div class="form-row">
                    <div class="md-form form-sm  col-md-12">
                        <label for="inputEmail4">Equipment Name (Classifications) <span style="color: red;">*</span></label>
                      <input type="text" name="item_class" class="form-control" id="inputEmail4" required="">
                    </div>
                </div>
            </div>
                  <div class="modal-footer rgba-stylish-strong p-1">
                    <button type="button" class="btn btn btn-light btn-sm waves-effect" data-dismiss="modal">
                        <i class="far fa-window-close"></i> Close</button>
                    <button type="submit" class="btn btn-success btn-sm waves-effect waves-light">
                        <i class="fas fa-file-import"></i> Create</button>
                  </div>
                </form>
               </div>
              </div>
            </div>

<!-- update -->
      <div class="modal" id="update-user-mdl" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header stylish-color-dark white-text">
              <h5 class="modal-title" id="exampleModalLabel">Update Equipment Name (Classifications)</h5>
              <button type="button" class="close white-text" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
             <form action="/update" method="post" autocomplete="off" enctype="multipart/form-data">
            {!! csrf_field() !!}
            <div class="modal-body">
                 <div class="form-row">
                    <label for="inputPassword3">Equipment Name (Classifications) <span style="color: red;">*</span></label>
                    <div class="col-sm-12 md-form form-sm">
                    <input type="hidden" id="update-id" name="id" value="">
                      <input type="text" class="form-control" id="u-item_class" name="item_class" required="">
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

      <!-- DELETE -->
      <div class="modal" id="delate-user-mdl" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header modal-header danger-color-dark white-text">
              <h5 class="modal-title"><span class="fas fa-trash"> </span> Delete Equipment Name (Classifications)</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form action="/delete" method="post">
                 {!! csrf_field() !!}
                     <input type="hidden" id="delate_id" name="id" value="">
                    <h6 class="sndbox-del-con">Are You Sure To Delete?</h6>
            </div>
            <div class="modal-footer p-1">
              <button type="button" class="btn btn btn-light btn-sm waves-effect" data-dismiss="modal"><span class="fas fa-window-close"></span> Close</button>
              <button type="submit" class="btn btn-red btn-sm waves-effect waves-light"><i class="fas fa-trash"></i>  Delete</button>
            </div>
          </form>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->


@endsection

@section('custom-js')

<script src="{{ asset('datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('datatables/js/dataTables.bootstrap4.min.js') }}"></script>

<script type="text/javascript">
	// Call the dataTables jQuery plugin
        $(document).ready(function() {
          $('#Table').DataTable();
        });
	</script>

@endsection

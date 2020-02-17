@extends('layouts.app')

@section('main-content')

<div class="row wow animated fadeIn d-flex justify-content-center p-2">
    <section class="">
        <div class="row m-5">
            <div class="card mb-5">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i id="btn-icon" class="fas fa-database"></i> PIS to PFMS Migrator
                        </h5>
                    </div>
                    <div class="card-body">

                        <!-- Grid row -->
                        <div class="row">
                            <!-- Grid column -->
                            <div class="col-md-12">
                                <!-- Material input -->
                                <div class="md-form form-group">
                                    <input type="text" class="form-control" id="servername" 
                                           placeholder="Default 'localhost'" value="localhost">
                                    <label for="servername">Hostname</label>
                                </div>
                            </div>
                            <!-- Grid column -->
                        </div>
                        <!-- Grid row -->

                        <!-- Grid row -->
                        <div class="form-row">
                            <!-- Grid column -->
                            <div class="col-md-6">
                                <!-- Material input -->
                                <div class="md-form form-group">
                                    <input type="text" class="form-control" id="username" 
                                           placeholder="Username">
                                    <label for="username">Username</label>
                                </div>
                            </div>
                            <!-- Grid column -->

                            <!-- Grid column -->
                            <div class="col-md-6">
                                <!-- Material input -->
                                <div class="md-form form-group">
                                    <input type="password" class="form-control" id="password"
                                           placeholder="Password">
                                    <label for="password">Password</label>
                                </div>
                            </div>
                            <!-- Grid column -->
                        </div>
                        <!-- Grid row -->
        
                        <!-- Grid row -->
                        <div class="row">
                            <!-- Grid column -->
                            <div class="col-md-12">
                                <!-- Material input -->
                                <div class="md-form">
                                    <div class="file-field small">
                                        <div class="btn btn-primary btn-sm float-left">
                                            <span>Choose file</span>
                                            <input type="file" id="db-file" accept=".sql">
                                        </div>
                                        <div class="file-path-wrapper">
                                            <input class="file-path validate" type="text" 
                                                   placeholder="Upload updated PIS Database" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Grid column -->
                        </div>
                        <!-- Grid row -->

                        <!-- Grid row -->
                        <div class="row">
                            <!-- Grid column -->
                            <div class="col-md-12">
                                <div class="progress md-progress" style="height: 20px">
                                    <div id="migrate-progress" class="progress-bar progress-bar-animated" 
                                         role="progressbar" style="width: 0%; height: 20px" aria-valuenow="0" 
                                         aria-valuemin="0" aria-valuemax="100">
                                        0%
                                    </div>
                                </div>
                                <div class="form-group">
                                    <textarea class="form-control rounded-0 black lime-text" id="txt-logs" rows="10" 
                                              style="resize: none; font-size: 0.75em;" readonly></textarea>
                                </div>

                                <button id="btn-migrate"
                                        class="btn btn-outline-black btn-block btn-md btn-rounded waves-effect" 
                                        onclick="$(this).migrate();">
                                    <i class="fas fa-database"></i>
                                    <strong> Migrate</strong>
                                </button>
                            </div>
                            <!-- Grid column -->

                        </div>
                        <!-- Grid row -->
                    
                    </div>
                </div>
        </div>
    </section>
</div>

@endsection

@section('custom-js')

<script src="{{ asset('assets/js/migrator.js') }}"></script>

@endsection
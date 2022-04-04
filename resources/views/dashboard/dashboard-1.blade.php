<div class="row mt-5">
    <div class="col-xl-3 col-md-6 mb-3">
        <a href="#" target="_blank">
            <a href="{{ url('procurement/pr?keyword=pending') }}" target="_blank">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="text-muted fw-normal mt-0 text-truncate">Pending</h5>
                                <h3 class="my-2 py-1">{{ $data->str_total_pending }}</h3>
                                <p class="mb-0 text-muted">
                                    <span class="text-gray"><b>{{ $data->str_pending }}</b></span>
                                    <span class="text-nowrap">this month</span>
                                </p>
                            </div>
                            <div class="avatar-sm">
                                <span class="avatar-title bg-soft-primary rounded">
                                    <i class="ri-stack-line font-20 text-primary"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <a href="{{ url('procurement/pr?keyword=approved') }}" target="_blank">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="text-muted fw-normal mt-0 text-truncate">Approved</h5>
                            <h3 class="my-2 py-1">{{ $data->str_total_approved }}</h3>
                            <p class="mb-0 text-muted">
                                <span class="text-success">{{ $data->str_approved }}</span>
                                <span class="text-nowrap">this month</span>
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-primary rounded">
                                <i class="ri-slideshow-2-line font-20 text-primary"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <a href="{{ url('procurement/pr?keyword=disapproved') }}" target="_blank">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="text-muted fw-normal mt-0 text-truncate">Disapproved</h5>
                            <h3 class="my-2 py-1">{{ $data->str_total_disapproved }}</h3>
                            <p class="mb-0 text-muted">
                                <span class="text-dark me-2">{{ $data->str_disapproved }}</span>
                                <span class="text-nowrap">this month</span>
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-primary rounded">
                                <i class="ri-hand-heart-line font-20 text-primary"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <a href="{{ url('procurement/pr?keyword=cancelled') }}" target="_blank">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="text-muted fw-normal mt-0 text-truncate">Cancelled</h5>
                            <h3 class="my-2 py-1">{{ $data->str_total_cancelled }}</h3>
                            <p class="mb-0 text-muted">
                                <span class="text-danger">{{ $data->str_cancelled }}</span>
                                <span class="text-nowrap">this month</span>
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-primary rounded">
                                <i class="ri-money-dollar-box-line font-20 text-primary"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="row">
    <div class="col-xl-3 col-md-6 mb-3">
        <a href="{{ url('procurement/po-jo?keyword=for_delivery') }}" target="_blank">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="text-muted fw-normal mt-0 text-truncate">For Delivery</h5>
                            <h3 class="my-2 py-1">{{ $data->str_total_for_delivery }}</h3>
                            <p class="mb-0 text-muted">
                                <span class="text-success">{{ $data->str_for_delivery }}</span>
                                <span class="text-nowrap">this month</span>
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-primary rounded">
                                <i class="ri-stack-line font-20 text-primary"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <a href="{{ url('procurement/po-jo?keyword=for_inspection') }}" target="_blank">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="text-muted fw-normal mt-0 text-truncate">Delivered/For Inspection</h5>
                            <h3 class="my-2 py-1">{{ $data->str_total_delivered }}</h3>
                            <p class="mb-0 text-muted">
                                <span class="text-success">{{ $data->str_delivered }}</span>
                                <span class="text-nowrap">this month</span>
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-primary rounded">
                                <i class="ri-slideshow-2-line font-20 text-primary"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="row mt-5">
    <div class="col-xl-6 col-md-12 mb-3">
        <div class="card">
            <div class="card-body">
                <canvas id="proc-chart-1" height="200"></canvas>
            </div>
        </div>
    </div>

    <div class="col-xl-6 col-md-12 mb-3">
        <div class="card">
            <div class="card-body">
                <canvas id="proc-chart-2" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<br>

<script>
    _chart1 = document.getElementById('proc-chart-1').getContext('2d');
    _chart2 = document.getElementById('proc-chart-2').getContext('2d');
    chart1 = new Chart(_chart1, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Approved', 'Disapproved', 'Cancelled', 'For Delivery', 'Delivered/For Inspection'],
            datasets: [{
                label: 'Total',
                data: [
                    {{ $data->total_pending }},
                    {{ $data->total_approved }},
                    {{ $data->total_disapproved }},
                    {{ $data->total_cancelled }},
                    {{ $data->total_for_delivery }},
                    {{ $data->total_delivered }}
                ],
                backgroundColor: [
                    '#DBDCDA',
                    '#52B7E5',
                    '#374040',
                    '#7C3405',
                    '#478AC3',
                    '#31475E'
                ]
            }]
        },
        options: {
            legend: {
                display: true,
                position: 'right',
            },
            title: {
                display: true,
                text: 'Total',
                position: 'top',
                fontSize: 20,
                padding: 20
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    chart2 = new Chart(_chart2, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Approved', 'Disapproved', 'Cancelled', 'For Delivery', 'Delivered/For Inspection'],
            datasets: [{
                label: 'Monthly Total',
                data: [
                    {{ $data->pending }},
                    {{ $data->approved }},
                    {{ $data->disapproved }},
                    {{ $data->cancelled }},
                    {{ $data->for_delivery }},
                    {{ $data->delivered }}
                ],
                backgroundColor: [
                    '#DBDCDA',
                    '#52B7E5',
                    '#374040',
                    '#7C3405',
                    '#478AC3',
                    '#31475E'
                ]
            }]
        },
        options: {
            legend: {
                display: true,
                position: 'right',
            },
            title: {
                display: true,
                text: 'Monthly Total',
                position: 'top',
                fontSize: 20,
                padding: 20
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

@extends('backend.layouts.app')

@section('title', 'Dashboard - Dashtreme Admin')

@section('styles')
<style>
    .traffic-summary,
    .sales-summary {
        padding: 1rem;
    }

    .traffic-source {
        padding: 1rem;
        border-radius: 5px;
        background: rgba(255, 255, 255, 0.1);
        margin-bottom: 1rem;
    }

    .progress-data {
        background: rgba(255, 255, 255, 0.1);
        padding: 1rem;
        border-radius: 5px;
    }
</style>
@endsection

@section('content')
<!--Start Dashboard Content-->
<div class="card mt-3">
    <div class="card-content">
        <div class="row row-group m-0">
            <div class="col-12 col-lg-6 col-xl-3 border-light">
                <div class="card-body">
                    <h5 class="text-white mb-0">9526 <span class="float-right"><i
                                class="fa fa-shopping-cart"></i></span></h5>
                    <div class="progress my-3" style="height:3px;">
                        <div class="progress-bar" style="width:55%"></div>
                    </div>
                    <p class="mb-0 text-white small-font">Total Orders <span class="float-right">+4.2% <i
                                class="zmdi zmdi-long-arrow-up"></i></span></p>
                </div>
            </div>
            <div class="col-12 col-lg-6 col-xl-3 border-light">
                <div class="card-body">
                    <h5 class="text-white mb-0">8323 <span class="float-right"><i class="fa fa-usd"></i></span></h5>
                    <div class="progress my-3" style="height:3px;">
                        <div class="progress-bar" style="width:55%"></div>
                    </div>
                    <p class="mb-0 text-white small-font">Total Revenue <span class="float-right">+1.2% <i
                                class="zmdi zmdi-long-arrow-up"></i></span></p>
                </div>
            </div>
            <div class="col-12 col-lg-6 col-xl-3 border-light">
                <div class="card-body">
                    <h5 class="text-white mb-0">6200 <span class="float-right"><i class="fa fa-eye"></i></span></h5>
                    <div class="progress my-3" style="height:3px;">
                        <div class="progress-bar" style="width:55%"></div>
                    </div>
                    <p class="mb-0 text-white small-font">Visitors <span class="float-right">+5.2% <i
                                class="zmdi zmdi-long-arrow-up"></i></span></p>
                </div>
            </div>
            <div class="col-12 col-lg-6 col-xl-3 border-light">
                <div class="card-body">
                    <h5 class="text-white mb-0">5630 <span class="float-right"><i class="fa fa-envira"></i></span></h5>
                    <div class="progress my-3" style="height:3px;">
                        <div class="progress-bar" style="width:55%"></div>
                    </div>
                    <p class="mb-0 text-white small-font">Messages <span class="float-right">+2.2% <i
                                class="zmdi zmdi-long-arrow-up"></i></span></p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-lg-8 col-xl-8">
        <div class="card">
            <div class="card-header">Site Traffic Summary
                <div class="card-action">
                    <div class="dropdown">
                        <a href="javascript:void();" class="dropdown-toggle dropdown-toggle-nocaret"
                            data-toggle="dropdown">
                            <i class="icon-options"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="javascript:void();">Action</a>
                            <a class="dropdown-item" href="javascript:void();">Another action</a>
                            <a class="dropdown-item" href="javascript:void();">Something else here</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="javascript:void();">Separated link</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="traffic-summary">
                    <div class="row mb-3">
                        <div class="col-12">
                            <h4 class="mb-0">Traffic Overview</h4>
                            <p class="text-muted">Last 30 days statistics</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <div class="traffic-source">
                                <h5>Direct</h5>
                                <p class="mb-0">45% <span class="text-success"><i class="fa fa-arrow-up"></i> 5%</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="traffic-source">
                                <h5>Referral</h5>
                                <p class="mb-0">30% <span class="text-danger"><i class="fa fa-arrow-down"></i> 2%</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="traffic-source">
                                <h5>Social</h5>
                                <p class="mb-0">25% <span class="text-success"><i class="fa fa-arrow-up"></i> 8%</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row m-0 row-group text-center border-top border-light-3">
                <div class="col-12 col-lg-4">
                    <div class="p-3">
                        <h5 class="mb-0">45.87M</h5>
                        <small class="mb-0">Overall Visitor <span> <i class="fa fa-arrow-up"></i> 2.43%</span></small>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="p-3">
                        <h5 class="mb-0">15:48</h5>
                        <small class="mb-0">Visitor Duration <span> <i class="fa fa-arrow-up"></i> 12.65%</span></small>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="p-3">
                        <h5 class="mb-0">245.65</h5>
                        <small class="mb-0">Pages/Visit <span> <i class="fa fa-arrow-up"></i> 5.62%</span></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-4 col-xl-4">
        <div class="card">
            <div class="card-header">Sales Summary
                <div class="card-action">
                    <div class="dropdown">
                        <a href="javascript:void();" class="dropdown-toggle dropdown-toggle-nocaret"
                            data-toggle="dropdown">
                            <i class="icon-options"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="javascript:void();">Action</a>
                            <a class="dropdown-item" href="javascript:void();">Another action</a>
                            <a class="dropdown-item" href="javascript:void();">Something else here</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="javascript:void();">Separated link</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="sales-summary">
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="sales-data">
                                <h5 class="mb-1">Total Sales</h5>
                                <h3 class="mb-1">$24,500</h3>
                                <p class="mb-0 text-success"><i class="fa fa-arrow-up"></i> 12% This Month</p>
                            </div>
                        </div>
                    </div>
                    <div class="sales-categories">
                        <div class="progress-data mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <h6 class="mb-0">Direct</h6>
                                <h6 class="mb-0">$5,856</h6>
                            </div>
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar" role="progressbar" style="width: 55%"></div>
                            </div>
                        </div>
                        <div class="progress-data mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <h6 class="mb-0">Affiliate</h6>
                                <h6 class="mb-0">$2,602</h6>
                            </div>
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar" role="progressbar" style="width: 25%"></div>
                            </div>
                        </div>
                        <div class="progress-data mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <h6 class="mb-0">E-mail</h6>
                                <h6 class="mb-0">$1,802</h6>
                            </div>
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar" role="progressbar" style="width: 15%"></div>
                            </div>
                        </div>
                        <div class="progress-data">
                            <div class="d-flex justify-content-between mb-1">
                                <h6 class="mb-0">Other</h6>
                                <h6 class="mb-0">$1,105</h6>
                            </div>
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar" role="progressbar" style="width: 5%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--End Row-->

<div class="row">
    <div class="col-12 col-lg-12">
        <div class="card">
            <div class="card-header">Recent Order Tables
                <div class="card-action">
                    <div class="dropdown">
                        <a href="javascript:void();" class="dropdown-toggle dropdown-toggle-nocaret"
                            data-toggle="dropdown">
                            <i class="icon-options"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="javascript:void();">Action</a>
                            <a class="dropdown-item" href="javascript:void();">Another action</a>
                            <a class="dropdown-item" href="javascript:void();">Something else here</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="javascript:void();">Separated link</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table align-items-center table-flush table-borderless">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Photo</th>
                            <th>Product ID</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Shipping</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Iphone 5</td>
                            <td><img src="https://as1.ftcdn.net/jpg/01/17/74/42/220_F_117744270_RcWaPulPITQhQZSQHcJV0zLVGzgU17PJ.jpg"
                                    class="product-img" alt="product img"></td>
                            <td>#9405822</td>
                            <td>$ 1250.00</td>
                            <td>03 Aug 2017</td>
                            <td>
                                <div class="progress shadow" style="height: 3px;">
                                    <div class="progress-bar" role="progressbar" style="width: 90%"></div>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>Earphone GL</td>
                            <td><img src="https://as1.ftcdn.net/jpg/01/17/74/42/220_F_117744270_RcWaPulPITQhQZSQHcJV0zLVGzgU17PJ.jpg"
                                    class="product-img" alt="product img"></td>
                            <td>#9405820</td>
                            <td>$ 1500.00</td>
                            <td>03 Aug 2017</td>
                            <td>
                                <div class="progress shadow" style="height: 3px;">
                                    <div class="progress-bar" role="progressbar" style="width: 60%"></div>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>HD Hand Camera</td>
                            <td><img src="https://as1.ftcdn.net/jpg/01/17/74/42/220_F_117744270_RcWaPulPITQhQZSQHcJV0zLVGzgU17PJ.jpg"
                                    class="product-img" alt="product img"></td>
                            <td>#9405830</td>
                            <td>$ 1400.00</td>
                            <td>03 Aug 2017</td>
                            <td>
                                <div class="progress shadow" style="height: 3px;">
                                    <div class="progress-bar" role="progressbar" style="width: 70%"></div>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>Classic Shoes</td>
                            <td><img src="https://as1.ftcdn.net/jpg/01/17/74/42/220_F_117744270_RcWaPulPITQhQZSQHcJV0zLVGzgU17PJ.jpg"
                                    class="product-img" alt="product img"></td>
                            <td>#9405825</td>
                            <td>$ 1200.00</td>
                            <td>03 Aug 2017</td>
                            <td>
                                <div class="progress shadow" style="height: 3px;">
                                    <div class="progress-bar" role="progressbar" style="width: 100%"></div>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>Hand Watch</td>
                            <td><img src="https://as1.ftcdn.net/jpg/01/17/74/42/220_F_117744270_RcWaPulPITQhQZSQHcJV0zLVGzgU17PJ.jpg"
                                    class="product-img" alt="product img"></td>
                            <td>#9405840</td>
                            <td>$ 1800.00</td>
                            <td>03 Aug 2017</td>
                            <td>
                                <div class="progress shadow" style="height: 3px;">
                                    <div class="progress-bar" role="progressbar" style="width: 40%"></div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!--End Row-->

<!--End Dashboard Content-->
@endsection

@section('scripts')
<!-- Sparkline JS -->
<script src="{{ asset('assets/plugins/sparkline-charts/jquery.sparkline.min.js') }}"></script>
@endsection
@include('layouts.header')

<div class="row">
    <div class="col-3">
        <x-dashboard-card :value="'496'" :label="'Total Admins'" :svg="asset('assets/images/d-user-icon.svg')"></x-dashboard-card>
    </div>
    <div class="col-3">
        <x-dashboard-card :value="'$87,561'" :label="'Total Revenue'" :svg="asset('assets/images/d-dollar.svg')"></x-dashboard-card>
    </div>
    <div class="col-3">
        <x-dashboard-card :value="'247'" :label="'Total Orders'" :svg="asset('assets/images/d-orders.svg')"></x-dashboard-card>
    </div>
    <div class="col-3">
        <x-dashboard-card :value="'827'" :label="'Total Customers'" :svg="asset('assets/images/d-user-icon.svg')"></x-dashboard-card>
    </div>
</div>


@include('layouts.footer')
@include('layouts.header')
<div class="container-fluid">
    @if(session()->has('user_details'))
    <p>Welcome, {{ session('user_details')['name'] }}!</p>
    <p>{{ session('user_details')['role'] }}</p>
    <!-- Access other user details as needed -->
    @endif
    <div class="row">
        <div class="col-12 col-sm-12 col-lg-6 col-xl-3 mb-2">
            <x-dashboard-card :value="'496'" :label="'Total Admins'" :svg="asset('assets/images/d-user-icon.svg')"></x-dashboard-card>
        </div>
        <div class="col-12 col-sm-12 col-lg-6 col-xl-3 mb-2">
            <x-dashboard-card :value="'827'" :label="'Total Customers'" :svg="asset('assets/images/d-user-icon.svg')"></x-dashboard-card>
        </div>
        <div class="col-12 col-sm-12 col-lg-6 col-xl-3 mb-2">
            <x-dashboard-card :value="'$87,561'" :label="'Total Revenue'" :svg="asset('assets/images/d-dollar.svg')"></x-dashboard-card>
        </div>
        <div class="col-12 col-sm-12 col-lg-6 col-xl-3 mb-2">
            <x-dashboard-card :value="'247'" :label="'Total Orders'" :svg="asset('assets/images/d-orders.svg')"></x-dashboard-card>
        </div>
    </div>
    <div class="pt-2" style="border-radius: 20px;">
        <div id="chartContainer" style="height: 100%; width: 100%;"></div>
    </div>
</div>
<script>

</script>

@include('layouts.footer')
@include('layouts.header')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-sm-12 col-lg-6 col-xl-3 mb-2">
            <x-dashboard-card :value="'459'" :label="'Total Customers'" :svg="asset('assets/images/d-user-icon.svg')"></x-dashboard-card>
        </div>
        <div class="col-12 col-sm-12 col-lg-6 col-xl-3 mb-2">
            <x-dashboard-card :value="'123'" :label="'Unassigned'" :svg="asset('assets/images/d-dollar.svg')"></x-dashboard-card>
        </div>
        <div class="col-12 col-sm-12 col-lg-6 col-xl-3 mb-2">
            <x-dashboard-card :value="'247'" :label="'Active'" :svg="asset('assets/images/d-orders.svg')"></x-dashboard-card>
        </div>
        <div class="col-12 col-sm-12 col-lg-6 col-xl-3 mb-2">
            <x-dashboard-card :value="'872'" :label="'Pending'" :svg="asset('assets/images/d-user-icon.svg')"></x-dashboard-card>
        </div>
    </div>
    <div class="px-0  box-shadow mt-4 ">
        <div class="row">
            <div class="col-8">
                <div class="mx-3 my-3">
                    <h4>Customers List</h4>
                </div>
            </div>
            <div class="col-4 text-center my-3 pl-5">
                <x-add-button :value="'+ Add Customers'" :dataTarget="'#add-customer'"></x-add-button>
                <x-modal :modalId="'add-customer'" :formAction="'customers/store'" :editData="''"></x-modal>
            </div>
        </div>
        <div class="table-responsive">
            <table class="display" id="myTable">
                <thead class="thead-color ">
                    <tr>
                        <th>Photo</th>
                        <th>Company Name </th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Address</th>
                        <th>Social Links</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customers as $customer)
                    <tr>
                        <td>
                            <img class="img-size" src="{{asset('storage/customer_images/'.$customer->customer_image)}}" alt="image">
                        </td>
                        <td>{{$customer->customer_name}}</td>
                        <td>{{$customer->customer_email}}</td>
                        <td>{{$customer->customer_phone}}</td>
                        <td>{{$customer->customer_address}}</td>
                        <td>{{$customer->customer_social_links}}</td>
                        <td>{{ $customer->customer_status }}</td>
                        <td>
                        <a href="{{ route('customer.delete', ['id' => $customer->customer_id]) }}">
                            <button class="btn p-0" style="background: none;">
                                <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle opacity="0.1" cx="18" cy="18" r="18" fill="#DF6F79" />
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M23.4905 13.743C23.7356 13.743 23.9396 13.9465 23.9396 14.2054V14.4448C23.9396 14.6975 23.7356 14.9072 23.4905 14.9072H13.0493C12.8036 14.9072 12.5996 14.6975 12.5996 14.4448V14.2054C12.5996 13.9465 12.8036 13.743 13.0493 13.743H14.8862C15.2594 13.743 15.5841 13.4778 15.6681 13.1036L15.7642 12.6739C15.9137 12.0887 16.4058 11.7 16.9688 11.7H19.5704C20.1273 11.7 20.6249 12.0887 20.7688 12.6431L20.8718 13.1029C20.9551 13.4778 21.2798 13.743 21.6536 13.743H23.4905ZM22.5573 22.4943C22.7491 20.707 23.0849 16.4609 23.0849 16.418C23.0971 16.2883 23.0548 16.1654 22.9709 16.0665C22.8808 15.9739 22.7669 15.9191 22.6412 15.9191H13.9028C13.7766 15.9191 13.6565 15.9739 13.5732 16.0665C13.4886 16.1654 13.447 16.2883 13.4531 16.418C13.4542 16.4259 13.4663 16.5755 13.4864 16.8255C13.5759 17.9364 13.8251 21.0303 13.9861 22.4943C14.1001 23.5729 14.8078 24.2507 15.8328 24.2753C16.6238 24.2936 17.4387 24.2999 18.272 24.2999C19.0569 24.2999 19.854 24.2936 20.6696 24.2753C21.7302 24.257 22.4372 23.5911 22.5573 22.4943Z" fill="#D11A2A" />
                                </svg>
                            </button>
                        </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@include('layouts.footer')
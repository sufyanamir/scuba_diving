@include('layouts.header')

<div class="card px-0  box-shadow mt-4 ">
    <div class="row">
        <div class="col-12 col-xl-10">
            <div class="mx-3 my-3">
                <h4>Company List</h4>
            </div>
        </div>
        <div class="col-12 col-xl-2 my-3 text-right pr-5">
            <x-add-button :value="'+ Add Company'" :dataTarget="'#add-company'"></x-add-button>
            <x-modal :modalId="'add-company'" :formAction="'company/store'" :editData="''"></x-modal>
        </div>
    </div>
    <div class="table-responsive">
        <table class="display" id="myTable">
            <thead class="thead-color ">
                <tr>
                    <th></th>
                    <th>Requested Name</th>
                    <th>Company Name</th>
                    <th>E-mail</th>
                    <th>Address</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($requests as $item)
                <tr>
                    <td>
                        <img class="img-size" src="{{'assets/images/nav-user.svg'}}" alt="image">
                    </td>
                    <td>{{ $item->req_name }}</td>
                    <td>{{ $item->req_company_name }}</td>
                    <td>{{ $item->req_email }}</td>
                    <td>{{ $item->req_address }}</td>
                    <td>
                        <form action="{{ url('/requests/approve/'.$item->req_id) }}" method="post">
                            <button class="btn">
                                <i class="fa-regular fa-thumbs-up"></i>
                            </button>
                        </form>
                        <button class="btn">
                            <i class="fa-regular fa-thumbs-down"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <br>
    </div>
</div>

@include('layouts.footer')
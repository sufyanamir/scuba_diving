@include('layouts.header')

<div class="card px-0  box-shadow mt-4 ">
    <div class="row">
        <div class="col-12 col-xl-10">
            <div class="mx-3 my-3">
                <h4>Request List</h4>
            </div>
        </div>
        <div class="col-12 col-xl-2 my-3 text-right pr-5">
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
                        <form class="d-inline" action="{{ url('/requests/approve/'.$item->req_id) }}" method="post">
                            @csrf
                            <button class="btn">
                                <i class="fa-regular fa-thumbs-up"></i>
                            </button>
                        </form>
                        <form class="d-inline" action="{{ route('request.delete', ['id' => $item->req_id]) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn">
                                <i class="fa-regular fa-thumbs-down"></i>
                            </button>
                        </form>

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <br>
    </div>
</div>

@include('layouts.footer')
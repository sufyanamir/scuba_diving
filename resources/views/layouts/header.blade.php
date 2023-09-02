<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <!-- bootstrap -->

    <!-- style -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <!-- style -->

    <!-- dataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <!-- dataTables -->


    <style>
        body {
            font-family: "Lato", sans-serif;
            margin: 0 0 0 8px;
            background: #023d5f;
        }

        .sidebar {
            padding-top: 10px !important;
            height: 100%;
            width: 200PX;
            position: fixed;
            z-index: 1;
            top: 0;
            left: 0;
            background: rgb(0, 163, 255);
            background: linear-gradient(90deg, rgba(0, 163, 255, 1) 10%, rgba(2, 61, 95, 1) 100%);
            overflow-x: hidden;
            transition: 0.5s;
            padding-top: 60px;
        }

        .sidebar a {
            padding: 8px 8px 8px 32px;
            text-decoration: none;
            font-size: 20px;
            color: #FFFF;
            display: block;
            transition: 0.3s;
            margin: 20px;
            border-radius: 10px;
        }

        .sidebar a.link:hover {
            background-color: #FFFF;
            color: black;
        }

        .sidebar a:hover .white-img {

            display: none;
        }

        .sidebar a:hover .dark-img {


            visibility: visible;
            position: static;
        }


        .dark-img {

            visibility: hidden;
            position: absolute;
        }

        .sidebar .closebtn {
            position: absolute;
            top: 0;
            right: 25px;
            font-size: 36px;
            margin-left: 50px;
        }

        .openbtn {
            font-size: 20px;
            cursor: pointer;
            background-color: #111;
            color: white;
            padding: 10px 15px;
            border: none;
        }

        .openbtn:hover {
            background-color: #444;
        }

        #main {
            transition: margin-left .5s;
            padding: 16px;
            /* margin-left: 250px; */
        }

        /* On smaller screens, where height is less than 450px, change the style of the sidenav (less padding and a smaller font size) */
        @media screen and (max-height: 450px) {
            .sidebar {
                padding-top: 15px;
            }

            .sidebar a {
                font-size: 18px;
            }
        }

        .main-panel {
            margin-left: 200px;
            border-radius: 30px 0 0 30px;
            background-color: #f5F5F5;
            height: 100vh;
        }
    </style>
</head>

<body>

    <div id="mySidebar" class="sidebar">
        <div align="center">
            <img src="{{ asset('assets/images/company-logo.svg') }}" alt="Image">
        </div>
        <!-- <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">×</a> -->
        @if(session()->has('user_details'))
        <a href="/dashboard" class="link">
            <img src="{{ asset('assets/images/d-white.svg') }}" class="white-img mb-1" alt="Image">
            <img src="{{ asset('assets/images/d-dark.svg') }}" class="dark-img mb-1" alt="Image">
            Dashboard
        </a>
        @if(session('user_details')['role'] == '0')
        <a href="/company" class="link">
            <img src="{{ asset('assets/images/c-white.svg') }}" class="white-img mb-1" alt="Image">
            <img src="{{ asset('assets/images/c-dark.svg') }}" class="dark-img mb-1" alt="Image">
            Companny
        </a>
        @endif
        @if(session('user_details')['role'] == '1')
        <a href="/staff" class="link">
            <img src="{{ asset('assets/images/c-white.svg') }}" class="white-img mb-1" alt="Image">
            <img src="{{ asset('assets/images/c-dark.svg') }}" class="dark-img mb-1" alt="Image">
            Staff
        </a>
        @endif
        @if(session('user_details')['role'] == '1')
        <a href="/services" class="link">
            <img src="{{ asset('assets/images/p-white.svg') }}" class="white-img mb-1" alt="Image">
            <img src="{{ asset('assets/images/p-dark.svg') }}" class="dark-img mb-1" alt="Image">
            Services
        </a>
        @endif
        @if(session('user_details')['role'] == '1')
        <a href="/customers" class="link">
            <img src="{{ asset('assets/images/u-white.svg') }}" class="white-img mb-1" alt="Image">
            <img src="{{ asset('assets/images/u-dark.svg') }}" class="dark-img mb-1" alt="Image">
            Customers
        </a>
        @endif
        <a href="/logout">
            <button class="btn">
                <svg width="22" height="21" viewBox="0 0 22 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13.7907 5.75V3.375C13.7907 2.74511 13.5457 2.14102 13.1096 1.69562C12.6734 1.25022 12.0819 1 11.4651 1H3.32558C2.7088 1 2.11728 1.25022 1.68115 1.69562C1.24502 2.14102 1 2.74511 1 3.375V17.625C1 18.2549 1.24502 18.859 1.68115 19.3044C2.11728 19.7498 2.7088 20 3.32558 20H11.4651C12.0819 20 12.6734 19.7498 13.1096 19.3044C13.5457 18.859 13.7907 18.2549 13.7907 17.625V15.25" stroke="#452C88" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M4.72095 10.5H21M21 10.5L17.5116 6.9375M21 10.5L17.5116 14.0625" stroke="#452C88" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
                <span style="color: #452C88;">Logout</span>
            </button>
        </a>
        @endif
    </div>
    <div class="main-panel" style="overflow-x: auto;">
        <div id="main">
            <!-- <button class="openbtn" onclick="openNav()">☰ Open Sidebar</button> -->
            <nav>
                <div class="row p-2">
                    <div class="col-9">

                    </div>
                    <div class="col-3 d-flex justify-content-evenly">
                        <form action="/lang_change" method="post">
                            @csrf
                            <select id="lang-select" class="form-control mx-2" style="width: 90%; height: 80%;" name="lang" onchange="this.form.submit()">
                                <option value="en" {{ session()->get('locale') == 'en' ? 'selected' : '' }}>English</option>
                                <option value="es" {{ session()->get('locale') == 'th' ? 'selected' : '' }}>Thai</option>
                            </select>
                        </form>
                        <div class="mx-2 my-auto" style="position: relative;">
                            <div style="position: absolute; display: flex; justify-content: center; bottom: 70%; left: 40%;">
                                <span class="badge badge-danger" style="width: 20px; height: 20px; border-radius: 50px;">4</span>
                            </div>
                            <img src="{{  asset('assets/images/bell.svg')  }}" style="position: initial;" alt="image">
                        </div>
                        <div class="mx-2 pb-2">
                            <img src="{{ asset('assets/images/nav-user.svg') }}" alt="image">
                        </div>
                    </div>
                </div>
            </nav>
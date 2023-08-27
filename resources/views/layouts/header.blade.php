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

        .sidebar a:hover {
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
            border-radius: 50px 0 0 50px;
            background-color: white;
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
        <a href="/dashboard">
            <img src="{{ asset('assets/images/d-white.svg') }}" class="white-img mb-1" alt="Image">
            <img src="{{ asset('assets/images/d-dark.svg') }}" class="dark-img mb-1" alt="Image">
            Dashboard
        </a>
        <a href="/company">
            <img src="{{ asset('assets/images/c-white.svg') }}" class="white-img mb-1" alt="Image">
            <img src="{{ asset('assets/images/c-dark.svg') }}" class="dark-img mb-1" alt="Image">
            Companny
        </a>
        <a href="/staff">
            <img src="{{ asset('assets/images/c-white.svg') }}" class="white-img mb-1" alt="Image">
            <img src="{{ asset('assets/images/c-dark.svg') }}" class="dark-img mb-1" alt="Image">
            Staff
        </a>
        <a href="/services">
            <img src="{{ asset('assets/images/p-white.svg') }}" class="white-img mb-1" alt="Image">
            <img src="{{ asset('assets/images/p-dark.svg') }}" class="dark-img mb-1" alt="Image">
            Services
        </a>
        <a href="/customers">
            <img src="{{ asset('assets/images/u-white.svg') }}" class="white-img mb-1" alt="Image">
            <img src="{{ asset('assets/images/u-dark.svg') }}" class="dark-img mb-1" alt="Image">
            Customers
        </a>
    </div>
    <div class="main-panel">
        <div id="main">
            <!-- <button class="openbtn" onclick="openNav()">☰ Open Sidebar</button> -->
            <nav>
                <div class="row p-2">
                    <div class="col-9">

                    </div>
                    <div class="col-3 d-flex justify-content-evenly">
                        <select name="" id="" class="form-control mx-2" style="width: 50%; height: 80%;">
                            <option value="">English</option>
                        </select>
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
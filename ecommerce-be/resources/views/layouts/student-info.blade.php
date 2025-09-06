<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name=description content="Intellisense Institute of Technology s a private technical-vocational institution accredited by the Technical Education Skills Development Authority (TESDA) and the Department of Education (DepEd) offering skills training in computer">
    <meta name=format-detection content="(032) 316 5477">
    <title>@yield('title')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <!-- 新 Bootstrap 核心 CSS 文件 -->

    @stack('css')
    @stack('scripts')
    <style>
        h3 {
            text-align: center;
        }

        #main-menu {}

        .list-group-item {
            border: none;
            padding: 20px;
        }

        a.list-group-item {}

        a.list-group-item:hover,
        a.list-group-item:focus {}

        a.list-group-item.active,
        a.list-group-item.active:hover,
        a.list-group-item.active:focus {
            border: none;
        }

        .list-group-item:first-child,
        .list-group-item:last-child {
            border-radius: 0;
        }

        .list-group-level1 .list-group-item {
            padding-left: 40px;

        }

        .list-group-level2 .list-group-item {
            padding-left: 70px;
        }

        .main-info {
            display: flex;
        }

        .form-control-dark {
            border-color: var(--bs-gray);
        }

        .form-control-dark:focus {
            border-color: #fff;
            box-shadow: 0 0 0 .25rem rgba(255, 255, 255, .25);
        }

        .text-small {
            font-size: 85%;
        }

        .dropdown-toggle {
            outline: 0;
        }
    </style>
</head>

<body>
    
    <main>

        <div class="container">
            <header class="d-flex flex-wrap justify-content-center py-3 mb-4 border-bottom">
                <a href="/api/students-information/qr-code/{{$id}}" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-dark text-decoration-none">
                    <span class="fs-4">Student's Information System</span>
                </a>

                <ul class="nav nav-pills">
                    <li class="nav-item"><a href="/api/attendance-login/{{$id}}" class="nav-link " aria-current="page">Login</a></li>
                    <li class="nav-item"><a href="/api/attendance-logout/{{$id}}" class="nav-link">Logout</a></li>
                    <li class="nav-item"><a class="nav-link " aria-current="page">{{ $currentTime }}</a></li>
                </ul>
            </header>
        </div>
    </main>
    <div class="container">
        <div class="row">
            <div class="col">
                <strong>Name: </strong> {{ $ce->enrollee->lastname . ', ' . $ce->enrollee->firstname }}
            </div>
            <div class="col">
                <strong>LRN: </strong> {{ $ce->enrollee->lrn }}
            </div>
            <div class="col">
                <strong>Semester: </strong> {{$ce->semester->name}}
            </div>



        </div>


        <div class="row">
            <div class="col">
                <strong>School Year: </strong> {{ $ce->schoolYear->sy }}
            </div>
            <div class="col">
                <strong>Course: </strong> {{ $course }}
            </div>
            <div class="col">
                <strong>Birthday: </strong> {{ $ce->enrollee->birthday }}
            </div>



        </div>
        <div class="row">
            <div class="col">
                <strong>Parent/Guardian: </strong> {{ $ce->enrollee->guardians_name }}
            </div>
            <div class="col">
                <strong>Address: </strong> {{ $ce->enrollee->present_address }}
            </div>
            <div class="col">
                <strong>Contact Number: </strong> {{ $ce->enrollee->mobile }}
            </div>

        </div>

        <br />

        <div class="row">
            @if(isset($isSuccess))
                @if($isSuccess == true)
                <div class="alert alert-success" role="alert">
                    {{ $message }}
                </div>
                @else
                <div class="alert alert-danger" role="alert">
                    {{ $message }}
                </div>
                @endif
            @endif
        </div>

        <div class="row">
            <div class="col-md-3">
            <ul class="nav flex-column">
            <li class="nav-item" style="margin-bottom: 5px;">
                <a class="btn btn-secondary" aria-current="page" href="/api/student-attendance/{{$id}}">Attendance </a>
            </li>
            <li class="nav-item">
                <a class="btn btn-primary" aria-current="page" href="/api/report-cards/qr-code/{{$id}}">Report Card</a>
            </li>
            
        </ul>
            </div>
            <div class="col-md-9">
                <div>
                    @yield('content')
                </div>
            </div>
        </div>


    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>

</html>
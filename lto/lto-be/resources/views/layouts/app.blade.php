<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name=description content="Intellisense Institute of Technology s a private technical-vocational institution accredited by the Technical Education Skills Development Authority (TESDA) and the Department of Education (DepEd) offering skills training in computer"><meta name=format-detection content="(032) 316 5477">
        <title>@yield('title')</title>

                <!-- CSS only -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">

        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
       <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
       <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
                <!-- JS, Popper.js, and jQuery -->
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
        <script type="text/javascript" src="{{ asset('js/l.js') }}"></script>
        <script
      data-ad-client="ca-pub-3293172728817874"
      async
      src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"
    ></script>
    </head>
    <body>
        <header>
            <nav class="navbar fixed-top navbar-expand-lg navbar-dark" style="background-color: #176A05;">
                <a href="/">
                    <img src="{{ asset('images/logo.png') }}" class="logo" />
                </a>
                <button class="navbar-toggler " type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
                    <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                        <li class="nav-item {{ Request::path() ==  '/' ? 'active' : ''  }}">
                            <a class="nav-link" href="/">HOME </a>
                        </li>
                        <li class="nav-item {{ Request::path() ==  'about-us' ? 'active' : ''  }}">
                            <a class="nav-link" href="/about-us">ABOUT US</a>
                        </li>
                        <li class="nav-item {{ Request::path() ==  '/spa/admission' ? 'active' : ''  }}">
                            <a class="nav-link" href="/shs-admission">ADMISSION</a>
                        </li>
                        <li class="nav-item {{ Request::path() ==  'news' ? 'active' : ''  }}">
                            <a class="nav-link" href="/news">NEWS</a>
                        </li>
                        <li class="nav-item {{ Request::path() ==  '/spa/register' ? 'active' : ''  }}">
                            <a class="nav-link" href="/register">REGISTER</a>
                        </li>
                        <li class="nav-item {{ Request::path() ==  '/spa/login' ? 'active' : ''  }}">
                            <a class="nav-link" href="/login" >LOGIN</a>
                        </li>
                    </ul>
                    <form class="form-inline my-2 my-lg-0" method="get" action="news">
                    <input class="form-control mr-sm-2" type="search" placeholder="Search News" name="filter" value="{{ old('filter') }}">
                    <button class="btn h-search my-2 my-sm-0" type="submit">Search</button>
                    </form>
                </div>
            </nav>
        </header>
        <div>
            @yield('content')
        </div>
        <footer id="footer">
        <br />
        <br />
		<div class="container-fluid">
			<div class="row text-center text-xs-center text-sm-left text-md-left">
				<div class="col-xs-12 col-sm-5">
					<ul class="list-unstyled quick-links">
						<li><a href="{{ url('/') }}">| Home |</a> </li>
						<li><a href="{{ url('/about-us') }}">About Us |</a> </li>
						<li><a href="{{ url('/spa/admission') }}">Admission |</a> </li>
						<li><a href="{{ url('/news') }}">News |</a></li>
                        <li><a href="{{ url('/albums') }}">Albums |</a></li>
                        <li><a href="{{ url('/spa/register') }}">Register |</a></li>
                        <li><a href="{{ url('/spa/login') }}">Login |</a></li>
					</ul>
				</div>
				<div class="col-xs-12 col-sm-7">
					<div class="col-xs-12 col-sm-12 col-md-12 mt-2 mt-sm-2 text-center text-white">
                        <p><u><a href="{{ url('/') }}">Intellisense Institute of Technology</a></u> is a private technical-vocational institution accredited by the Technical Education Skills Development Authority (TESDA) and the Department of Education (DepEd)</p>
                        <p class="h6">© All right Reversed.<a class="text-green ml-2" href="{{ url('/') }}" target="_blank">IITCEBU 2020</a></p>
				    </div>
				</div>

			</div>
		</div>
	</footer>
    </body>
</html>

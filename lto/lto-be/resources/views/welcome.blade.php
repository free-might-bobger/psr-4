@extends('layouts.app')
@section('title', 'Intellisense Institute of Technology')
@section('content')
<section class="jumbotron h-j1 jumbotron-fluid" >
            <div class="container-fluid">
                <br />
                <br />
                 <h1 class="display-4 text-center">Intellisense Institute of Technology</h1>
                    <p class="lead text-center">Technology is everywhere and in every part of our culture.
        It affects how we live, work, play, and most importantly learn.</p>
                    <hr class="my-4">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-7 col-sm-12">
                                <p>Together with technology, we at IIT enhance learning by providing well-rounded education through the following:</p>
                                <ul>
                                    <li>Providing each student with hands-on computer experience</li>
                                    <li>Making available community classrooms where students see actual workplace and job</li>
                                    <li>Organizing life coaching, motivational sessions all year round</li>
                                    <li>Simulated sessions in job search activities like portfolio planning, construction and job interviews.</li>
                                </ul>

                                <p >IIT students generally take interests in <strong>Information Technology (IT) </strong>.  Our students belong to the underprivileged to middle class segment of society.  Approximately 50% are working in full time and/or part time jobs.</p>
                                <p >We cultivate in our students the values of <strong>INNOVATIVENESS, INTEGRITY & TEAMWORK.</strong></p>



                                <p>Technology in the classroom and on campus can make a difference in Education if used properly.
                                    It will prepare students for their future careers where it starts HERE @IIT!
                                    K12 READY!</p>
                                <a class="btn btn-primary btn-lg" href="/spa/shs-admission" role="button">Enroll Now</a>
                                <br />
                                <br />
                            </div>
                            <div class="col-md-5 col-sm-12">
                            <img src="{{ asset('images/iitcebu-enrollment.jpg') }}" class="img-thumbnail" alt="Enrollment"/>
                            </div>
                        </div>
                    </div>
            </div>

        </section>
        <section class="jumbotron h-j2 text-white jumbotron-fluid">
            <div class="container-fluid">

                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-4 col-sm-12">
                            <img src="{{ asset('images/courses-offered.jpg') }}" class="img-thumbnail" alt="Enrollment"/>
                            </div>
                            <div class="col-md-8 col-sm-12">
                                <h2 class="display-5 ">YOUR NEW CAREER STARTS HERE</h2>
                                <p class="lead ">IIT offers the following:</p>
                                <hr class="my-4">
                                <ul>
                                    <li>Senior High School * FREE</li>
                                    <li>Junior High School * Grade 7</li>
                                    <li>TESDA Scholarship * Contact Center Services NCII</li>
                                    <li>Certificate Trainings in:

                                        <ul>
                                            <li>Advance Computer Hardware and Software Configuration</li>
                                            <li>Visual Graphic Design</li>
                                            <li>Visual Graphic Animation</li>
                                            <li>Web Designing</li>
                                            <li>Database Programming and Management</li>
                                            <li>Basic Computer Programming</li>
                                            <li>Technical Drafting using CAD</li>
                                        </ul>
                                    </li>
                                </ul>
                                 <p>We at IIT understand the importance of creating a culture of innovation in classrooms thus, we don’t just prepare our senior high school students to be "college ready". <br /> They, together with post-secondary students are given a grounding on creativity, inventiveness to be "innovation ready".  <br />Our instruction is geared toward producing graduates who <strong>think critically and creatively, communicate effectively and collaborate.</strong> <br /> Students are required to work cooperatively with classmates, with each other towards a shared purpose.</p>
                                <a class="btn btn-primary btn-lg" href="/spa/shs-admission" role="button">Enroll Now</a>
                                <br />
                                <br />
                            </div>
                        </div>
                    </div>
            </div>

        </section>
        <section class="jumbotron h-j1 jumbotron-fluid" >
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card mb-4 shadow-sm">
                            <img src="{{ asset('images/computer-laboratory.jpg') }}" class="img-thumbnail""/>
                            <div class="card-body">
                            <p class="card-text">Fully Air-conditioned Classroom and 1:1 in Computer Laboratory.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                    <div class="card mb-4 shadow-sm">
                        <img src="{{ asset('images/students.jpg') }}" class="img-thumbnail""/>
                        <div class="card-body">
                        <p class="card-text">Our students is actively participating in Sport Activities to promote health and wellness.</p>
                        </div>
                    </div>
                    </div>
                    <div class="col-md-4">
                    <div class="card mb-4 shadow-sm">
                        <img src="{{ asset('images/classroom.jpg') }}" class="img-thumbnail""/>
                        <div class="card-body">
                        <p class="card-text">Our classroom is conducive for learning with it's cold vibrants.</p>
                        </div>
                    </div>
                    </div>
                </div>
            </div>

        </section>
@endsection

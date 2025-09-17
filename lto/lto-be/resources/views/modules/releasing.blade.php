@extends('layouts.student-info')
@section('title', 'Student Information System')
@push('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.css" rel="stylesheet" type="text/css" />
    <style>
        #datepicker > span:hover{cursor: pointer;}
    </style>
@endpush
@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.js"></script>
    <script>
    $(function () {
        $("#datepicker").datepicker({
                autoclose: true,
                todayHighlight: true
        }).datepicker('update', new Date());

        $("#yearLevel").change(function(){
            $.ajax({
              type: 'GET',
                url:`/api/year-level-courses/${this.value}`,
                cache: false,
                processData: false,
                contentType: false,
                success: function(data, textStatus, jqXHR) {
                   $('#course').append("<option value>Please Select Year Level</option>");
                     $.each(data.courses, function(i, d) {
                    // You will need to alter the below to get the right values from your json object.  Guessing that d.id / d.modelName are columns in your carModels data
                    $('#course').append('<option value="' + d.id + '">' + d.name + '</option>');
                });
                }
            });
        });
         $("#course").change(function(){
            $.ajax({
              type: 'GET',
                url:`/api/course-subject-type/${this.value}`,
                cache: false,
                processData: false,
                contentType: false,
                success: function(data, textStatus, jqXHR) {
                      $('#subjectType').append("<option value>Please Select Year Level</option>");
                     $.each(data.subjectTypes, function(i, d) {
                    // You will need to alter the below to get the right values from your json object.  Guessing that d.id / d.modelName are columns in your carModels data
                    $('#subjectType').append('<option value="' + d.id + '">' + d.name + '</option>');
                });
                }
            });
        });
        $("#subjectType").change(function(){
            $.ajax({
              type: 'GET',
                url:`/api/sis/subjects/${this.value}`,
                cache: false,
                processData: false,
                contentType: false,
                success: function(data, textStatus, jqXHR) {
                      $('#subjects').append("<option value>Please Select Year Level</option>");
                     $.each(data.subjects, function(i, d) {
                    // You will need to alter the below to get the right values from your json object.  Guessing that d.id / d.modelName are columns in your carModels data
                    $('#subjects').append('<option value="' + d.id + '">' + d.name + '</option>');
                });
                }
            });
        });
        var vuex  = localStorage.getItem('vuex');
        var parse = JSON.parse(vuex)
        if(parse == null){
          $('#submit-module').hide();
        }else{
          if (parse.users.user.role_ids.includes(3) === true || parse.users.user.role_ids.length == 0){
          $('#submit-module').hide();
          
          }else{
            $('#for-student').hide();
          }
        }
        
    });
    </script>
@endpush
@section('content')
<a href="releasing-modules" id="for-student">Released Modules</a>
<div id="submit-module">
<h4>Releasing Module (<a href="releasing-modules">Released Modules</a>)</h4>
@if (Session::has('success'))
<div class="alert alert-success alert-dismissible  show" role="alert" >
  <strong>Success</strong> Module submitted successfully.
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
@endif
<form action="releasing/store" method="POST">
 @csrf
<div class="form-group">
</div>
<div class="form-group">
    <label for="yearLevel">Year Level:</label>
    <select class="form-control" id="yearLevel" required name="year_level_id">
    <option value="">Select Year Level</option>
      @foreach($yearLevel as $l)
        <option value="{{ $l->id }}">{{ $l->name }}</option>
      @endforeach
    </select>
  </div>

<div class="form-group">
  <label for="course">Course:</label>
    <select class="form-control" id="course" required name="course_id">
    </select>
</div>

<div class="form-group">
  <label for="subjectType">Subject Type:</label>
    <select class="form-control" id="subjectType" required name="subject_type_id">
    </select>
</div>

<div class="form-group">
  <label for="subjects">Subjects:</label>
    <select class="form-control" id="subjects" required name="subject_id">
    </select>
</div>

<div class="form-group">
    <label for="teacher">Teacher:</label>
    <select class="form-control" id="teacher" required name="teacher_id">
    <option value="">Select Teacher</option>
      @foreach($teachers as $teacher)
        <option value="{{ $teacher->id }}">{{ $teacher->lastname . ', ' . $teacher->firstname }}</option>
      @endforeach
    </select>
  </div>

<div class="form-group">
    <label for="receivedBy">Release By:</label>
    <select class="form-control" id="receivedBy" required name="release_by_id">
    <option value="">Received By: </option>
      @foreach($releaseBy as $release)
        <option value="{{ $release->id }}">{{ $release->lastname . ', ' . $release->firstname }}</option>
      @endforeach
    </select>
  </div>

<br />
<br />
<input  type="submit" class="btn btn-success" label="Submit">
</form>
</div>

@endsection

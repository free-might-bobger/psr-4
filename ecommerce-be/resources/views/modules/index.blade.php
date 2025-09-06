@extends('layouts.student-info')
@section('title', 'Student Information System')
@push('css')

@endpush
@push('scripts')
<script>
$(function() {
  var vuex  = localStorage.getItem('vuex');
        var parse = JSON.parse(vuex)
        if(parse == null){
          $('#action').hide();
            $('.access-right').each(function(){
              $(this).hide();
            });
        }else{
          if (parse.users.user.role_ids.includes(3) === true || parse.users.user.role_ids.length == 0){
            $('#action').hide();
            $('.access-right').each(function(){
              $(this).hide();
            });
          
          }
        }
});
</script>
@endpush
@section('content')
<h4>Modules Submitted</h4>
@if (Session::has('success'))
<div class="alert alert-success alert-dismissible  show" role="alert" >
  <strong>Success</strong> Module deleted successfully.
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
@endif
<table class="table">
  <thead>
    <tr>
      <th scope="col">Year Level</th>
      <th scope="col">Course </th>
      <th scope="col">Subject Type </th>
      <th scope="col">Subject</th>
      <th scope="col">Teacher</th>
      <th scope="col">Received By</th>
      <th scope="col">Date</th>
      <th scope="col" id="action">Action</th>
    </tr>
  </thead>
  <tbody>
  @foreach($moduleSubmits as $module)
    <tr>
      <th scope="row">{{ $module->yearLevel->name}}</th>
      <td>{{ $module->course->name}}</td>
      <td>{{ $module->subjectType->name}}</td>
      <td>{{ $module->subject->name }} </td>
      <td>{{ $module->teacher->name}}</td>
      <td>{{ $module->receivedBy->name}}</td>
      <td>{{ $module->created_at }}</td>
      <td class="access-right"><a href="{{ $module->id }}">Delete</a></td>
    </tr>
  @endforeach
  </tbody>
</table>
@endsection

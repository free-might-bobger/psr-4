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
<h5>Report Cards</h5>
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
      <th scope="col">School Year</th>
      <th scope="col" id="action">Action</th>
    </tr>
  </thead>
  <tbody>
    @foreach($schoolYear as $sy)
        @if($sy['sy'] == $ce->schoolYear['sy'])
          <tr >
              <td>{{ $sy['sy'] }} {{ $ce->schoolYear['sy'] }}</td>
              <td class="access-right"><a href="/api/print-grades/{{$ce->optimus_id}}?schoolYear={{ $sy['sy'] }}">view</a></td>
          </tr>
        @endif
    @endforeach
  </tbody>
</table>
@endsection

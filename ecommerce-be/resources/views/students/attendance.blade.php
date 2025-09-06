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
      <th scope="col">Login</th>
      <th scope="col">Logout</th>
    </tr>
  </thead>
  <tbody>
    @foreach($studentAttendance as $attendance)
          <tr >
              <td>{{$attendance->created_at }}</td>

              <td>
                @if($attendance->is_out == 1)
                       {{ $attendance->updated_at }}
                @endif

              </td>
          </tr>
    @endforeach
  </tbody>
</table>
@endsection

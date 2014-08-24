@extends("domain.layout")
@section("content")
  	<h2>Hello {{ Sentry::getUser()->first_name }}</h2>
  	<p>Welcome to your sparse profile page.</p>
	<h3>List users</h3>
  	<table>
  		<thead>
  			<tr>
  				<th>Email</th>
  				<th>Name & lastname</th>
  				<th>Active</th>
  				<th>Activated at</th>
  				<th>Last login</th>
  				<th>Created at</th>
  			</tr>
  		</thead>
  		<tbody>
  		@foreach ($users as $user)
	  	<tr>
	         <td>{{$user->email}}</td>
	         <td>{{$user->first_name}} {{$user->last_name}}</td>
	         <td>{{$user->activated == 1 ? 'Yes' : 'No' }}</td>
	         <td>{{$user->activated_at}}</td>
	         <td>{{$user->last_login}}</td>
	         <td>{{$user->created_at}}</td>
	    </tr>
		@endforeach
		</tbody>
	</table>
@stop
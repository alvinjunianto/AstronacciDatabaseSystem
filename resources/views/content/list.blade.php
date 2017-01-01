@extends('layouts.logged')
@section('content')
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Setting</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
			<link href="{{ URL::asset('css/styling.css') }}" rel="stylesheet">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-child fa-fw"></i> Users
                </div>
                <div class="panel-body">
				<form action="{{ route('admin.assign') }}" method="post">
					<table class="responstable">
						
						<tr>            
						<th>Username</th>
						<th>Fullname</th>
						<th>Role</th>
						<th>A Shop</th>
						<th>Delete?</th>
						</tr>
						
						<?php $idx = 0; ?>
						
						@foreach ($users as $user)
						<tr>			
							<td><input id="ischanged{{ $idx }}" type="checkbox" style="display:none" name="ischanged{{ $idx }}"><b>{{ $user->username }}<input type="hidden" name="username{{ $idx }}" value="{{ $user->username }}"> </b></td>
							<td style="color:black"><b>{{ $user->fullname }}</b></td>
							<td><select id="roles{{ $idx }}" onchange="checkChange({{ $idx }})" name="roles{{ $idx }}">
								<option value="0" {{ $user->hasRole($user->username, '0') ? 'selected' : ''}} >Superadmin</option>
								<option value="1" {{ $user->hasRole($user->username, '1') ? 'selected' : ''}} >A-Club admin</option>
								<option value="2" {{ $user->hasRole($user->username, '2') ? 'selected' : ''}} >MRG admin</option>
								<option value="3" {{ $user->hasRole($user->username, '3') ? 'selected' : ''}} >CAT admin</option>
								<option value="4" {{ $user->hasRole($user->username, '4') ? 'selected' : ''}} >UOB admin</option>
								<option value="5" {{ $user->hasRole($user->username, '5') ? 'selected' : ''}} >Sales</option>
							</select></td>
							<td><input id="ashop{{ $idx }}" onchange="checkChange({{ $idx }})" type="checkbox" {{ $user->hasAShop($user->username) ? 'checked' : ''}} name="ashop{{ $idx }}"></td>
							<td><button type="submit" name="delbut" value="{{ $idx }}">Delete</button></td>
								{{ csrf_field() }}
							<br>				
						</tr>
						<?php $idx = $idx + 1; ?>
						@endforeach
					</table>
                    <br><br>
					<input type="hidden" name="numusers" value="{{ $idx }}">
					<button type="submit" name="assbut">Assign Roles</button>
                    <a href="#" class="button turquoise" style="text-align: center"><span>✎</span>Edit</a>
                    <a href="#" class="button turquoise" style="text-align: center"><span>✗</span>Delete</a>
                    <a href="/adduser" class="button turquoise" style="text-align: center"><span>+</span>New User</a>
					</form>
                </div>
                <!-- /.panel-body -->
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-gears fa-fw"></i> Other Settings
                </div>
                <div class="panel-body">
                    <div>
						<form role="form" method="POST" action="{{ url('/register') }}">
							{{ csrf_field() }}
							
							<div style="height:60px" class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
								<label for="name" class="col-md-4 control-label">Username</label>
								<input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>

								@if ($errors->has('name'))
									<span class="help-block">
										<strong>{{ $errors->first('name') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
								<label for="email" class="col-md-4 control-label">E-Mail Address</label>
								<input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

								@if ($errors->has('email'))
									<span class="help-block">
										<strong>{{ $errors->first('email') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
								<label for="password" class="col-md-4 control-label">Password</label>
								<input id="password" type="password" class="form-control" name="password" required>

								@if ($errors->has('password'))
									<span class="help-block">
										<strong>{{ $errors->first('password') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group">
								<label for="password-confirm" class="col-md-4 control-label">Confirm Password</label>
								<input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
							</div>
							<div class="form-group">
								<label for="ashop" class="col-md-4 control-label">A Shop Auth</label>
								<div class="col-md-6">
									<input name="ashop" value="0" type="hidden">
									<input id="ashop" type="checkbox" name="ashop" ><br><br>
								</div>
							</div>
							<div class="form-group">
								<label for="role" class="col-md-4 control-label">Role</label>							
								<input id="role" type="text" class="form-control" value="5" name="role" required>
							</div>
							<div class="form-group">
								<label for="fullname" class="col-md-4 control-label">Fullname</label>					
								<input id="fullname" type="text" class="form-control" name="fullname" required>
							</div>
							<div class="form-group">
								<div class="col-md-6 col-md-offset-4">
									<button type="submit" class="btn btn-primary">
										Register
									</button>
								</div>
							</div>
						</form>					
					</div>
                </div>
                <!-- /.panel-body -->
            </div>

			<script>
				function checkChange(idx) {
					var ashopcb = document.getElementById("ashop"+idx);
					var rolesel = document.getElementById("roles"+idx);
					if ((ashopcb.checked != ashopcb.defaultChecked)||(!rolesel.options[rolesel.selectedIndex].defaultSelected)) {
						document.getElementById("ischanged"+idx).checked = true;				
					}
					else {
						document.getElementById("ischanged"+idx).checked = false;
					}
				}
			</script>
			
            
	
@endsection
</html>

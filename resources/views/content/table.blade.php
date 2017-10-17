	<div>
		<div class="row">
            <br><br>
            <!-- /.col-lg-12 -->
        </div>
    </div>	

	@if ($route != 'assign')
		<div class="panel-group" id="accordion1">
			<div class="panel">
				<!-- @if($route == 'product')
					@if(Auth::user()->hasAnyRole(['0']))
						<a id="addclib" onclick="addcli()" class="btn btn-primary">Add New Product</a>
						<br>
						<br>
					@endif
				@elseif ($route == 'trans')
					<a id="addclib" onclick="addcli()" class="btn btn-primary">Add New Transaction</a>
					<br>
					<br>
				@else
					<a id="addclib" onclick="addcli()" class="btn btn-primary">Add New Client</a> -->
					<a id="importb" onclick="importex()" class="btn btn-primary">Import Excel File</a> 
					<!-- <br>
				@endif -->
				<br>
			</div>
		@endif
		<div id="import" style="display:none">
		@if(($route != 'product') and ($route != 'trans') and ($route != 'assign'))	
		<div class="panel panel-default" style="padding:15px">
			<div class="panel-body">
				<form method="post" action="{{route($route . '.import')}}" enctype="multipart/form-data">
					<input type = "hidden" name = "_token" value = "<?php echo csrf_token() ?>">
					<input type="file" name="import_file" />
					<br>
					<button class="btn btn-primary">Import .xls File</button>
				</form>
			</div>
		</div>
	@endif
		</div>
    </div>
	
	
	<div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
				List of {{ $route }} client
                </div>
				<!-- TODO FORM -->
				@if($route == 'green')
					<form action="{{ route('green.assign') }}" method="post">
				@elseif($route == 'RedClub')
					<form action="{{ route('RedClub.assign') }}" method="post">
				@else
					<form action="{{ route('grow.assign') }}" method="post">
				@endif
				
                <!-- /.panel-heading -->
                <div class="panel-body">
					<div style="overflow-x:scroll">
                    <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables" style="font-size:80%;">
                        <thead>
							<tr>
							@if(($route == 'green')||($route == 'RedClub')||($route == 'grow'))
								<th> Select </th>
							@endif
							<!-- Mendapatkan judul setiap kolom pada tabel dari variabel heads -->
							@foreach ($heads as $head)
								<th> {{$head}} </th>
							@endforeach
								
							</tr>
                        </thead>
						<tbody>
						<?php $idx = 0; ?>
						<!-- Menampilkan seluruh client untuk PC terkait, dari list pada variabel clients -->
						@foreach ($clients as $client)																				
							<tr class="gradeA">
								@if(($route == 'green')||($route == 'RedClub')||($route == 'grow'))
								<td style="text-align:center;">
									<input id="" onchange="" type="checkbox" style="" name="assigned{{ $idx }}">
									@if($route == 'green') 
									<input type="hidden" name="id{{ $idx }}" value={{ $client->green_id }}>
									@elseif($route == 'RedClub') 
									<input type="hidden" name="id{{ $idx }}" value={{ $client->username }}>
									@elseif($route == 'grow') 
									<input type="hidden" name="id{{ $idx }}" value={{ $client->grow_id }}> 
									@endif
								</td>
								@endif
							@foreach ($atts as $att)
                                @if ($route == 'AClub') <!-- Client Green diidentifikasi berdasarkan green_id (untuk dilihat detailnya) -->
                                    <td> <a target="_blank" href="{{route($route . '.detail', ['id' => $client->master_id])}}" style="text-decoration:none; color:black;">{{$client->$att}} </a></td>
                                @elseif ($route == 'RedClub') <!-- Client RedClub diidentifikasi berdasarkan username (untuk dilihat detailnya) -->
                                    <td> <a target="_blank" href="{{route($route . '.detail', ['id' => $client->username])}}" style="text-decoration:none; color:black;">{{$client->$att}} </a></td>
                                @elseif ($route == 'MRG')
                                	<td> <a target="_blank" href="{{route($route . '.detail', ['id' => $client->master_id])}}" style="text-decoration:none; color:black;">{{$client->$att}} </a></td>
                                @elseif ($route == 'UOB')
                                    <td> <a target="_blank" href="{{route($route . '.detail', ['id' => $client->client_id])}}" style="text-decoration:none; color:black;">{{$client->$att}} </a></td>
                                @elseif (($route != 'product') and ($route != 'trans') and ($route != 'assign')) <!-- Client PC diidentifikasi berdasarkan all_pc_id -->
								    <td> <a target="_blank" href="{{route($route . '.detail', ['id' => $client->user_id])}}" style="text-decoration:none; color:black;">{{$client->$att}} </a></td>
								@else
									<td>{{$client->$att}}</td>
                                @endif
							@endforeach
							</tr>
							
							<?php $idx = $idx + 1; ?>
							
						@endforeach
						</tbody>
						<input type="hidden" name="numusers" value="{{ $idx }}">
					</table>
					
					{{ $clients->links() }}
					
                    <!-- /.table-responsive -->
					</div>
					{{ csrf_field() }}
					
					@if(($route == 'green')||($route == 'RedClub')||($route == 'grow'))
					<input type="hidden" name="username" value={{ Auth::user()->username }}>
					<div style="float:right">
						&nbsp &nbsp Prospect to:
						<select id="" onchange="" name="prospect">
							<option {{ Auth::user()->hasRole(Auth::user()->username, '1') ? 'selected' : ''}}>A-Club</option>
							<option {{ Auth::user()->hasRole(Auth::user()->username, '2') ? 'selected' : ''}}>MRG</option>
							<option {{ Auth::user()->hasRole(Auth::user()->username, '3') ? 'selected' : ''}}>CAT</option>
							<option {{ Auth::user()->hasRole(Auth::user()->username, '4') ? 'selected' : ''}}>UOB</option>
						</select>
						&nbsp &nbsp Assign to:
						<select name="assign">
						@foreach($sales as $sale)
							<option>{{$sale->sales_username}}</option>
						@endforeach
						</select>
						<button class="button turquoise" style="border: 0; margin:20px; margin-bottom:10px" type="submit" name="assbut"><span>✎</span>Save</button>
					</div>
					@endif
				
                </div>
                <!-- /.panel-body -->
							
				</form>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>

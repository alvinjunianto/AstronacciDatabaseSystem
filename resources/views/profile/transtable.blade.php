
<body>	   

    @if(($route == "CAT") || ($route == "MRG") || ($route == "UOB") || ($route == "green") || ($route == "AShop"))    
    <div class="panel panel-default" style="margin:15px">        
            @if (($route == "CAT") || ($route == "UOB"))
                <?php $had_trans = false; ?>
                @foreach ($insreg as $atr)
                    <?php $atr2 = strtolower(str_replace(' ', '_',$atr)); ?>
                    @if ($client->$atr2 != NULL)
                        <?php $had_trans = true; ?>
                    @endif
                @endforeach
            @else
            <table width="100%" class="table table-striped table-bordered table-hover" id="trans">
                <thead>
                    <tr>
                        @foreach ($headsreg as $headreg)
                        <th> {{$headreg}} </th>
                        @endforeach
                        
                    </tr>
                </thead>
                <tbody>
                    @foreach ($clientsreg as $clientreg)
                    
                    <tr class="gradeA">
                        <?php $count_temp = 1 ; ?>
                        @foreach ($attsreg as $attreg)
                       @if ($route == 'AClub')
                            <td> <a target="_blank" href="{{route('AClub.member',['id' => $client->master_id, 'package' => $clientreg->user_id])}}">{{$clientreg->$attreg}} </a>
                                @if ($count_temp == 1)
                                    <div class="btn-hvr-container"><button class="btn btn-primary hvr-btn">edit</button><button class="btn btn-primary hvr-btn">delete</button></div></td>
                                    <?php $count_temp++ ; ?>
                                @else
                                    </td>
                                @endif
                        @elseif ($route == 'green') 
                            <td> <a target="_blank" href="{{route('green.trans',['id' => $client->green_id, 'progress' => $clientreg->progress_id])}}">{{$clientreg->$attreg}} </a>
                                @if ($count_temp == 1)
                                    <div class="btn-hvr-container"><button class="btn btn-primary hvr-btn">edit</button><button class="btn btn-primary hvr-btn">delete</button></div></td>
                                    <?php $count_temp++ ; ?>
                                @else
                                    </td>
                                @endif
                        @elseif ($route == 'MRG')
                            <td> <a target="_blank" href="{{route('MRG.account',['id' => $client->master_id, 'account' => $clientreg->accounts_number])}}">{{$clientreg->$attreg}} </a>
                                @if ($count_temp == 1)
                                    <div class="btn-hvr-container"><button class="btn btn-primary hvr-btn">edit</button><button class="btn btn-primary hvr-btn">delete</button></div></td>
                                    <?php $count_temp++ ; ?>
                                @else
                                    </td>
                                @endif
                        @else
                            <td> <a target="_blank" href="{{route('AShop.trans',['id' => $client->master_id, 'transaction' => $clientreg->transaction_id])}}">{{$clientreg->$attreg}} </a>
                                @if ($count_temp == 1)
                                    <div class="btn-hvr-container"><button class="btn btn-primary hvr-btn">edit</button><button class="btn btn-primary hvr-btn">delete</button></div></td>
                                    <?php $count_temp++ ; ?>
                                @else
                                    </td>
                                @endif
                        @endif

                        @endforeach
                        
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $clientsreg -> links() }}
            @endif
    @elseif ($route == 'AClub')    
    <div class="panel panel-default" style="margin:15px">                
            <table width="100%" class="table table-striped table-bordered table-hover" id="trans">
                <thead>
                    <tr>
                        @foreach ($headsreg as $headreg)
                        <th> {{$headreg}} </th>
                        @endforeach
                        
                    </tr>
                </thead>
                <tbody>
                    @foreach ($clientsreg as $clientreg)
                    
                    <tr class="gradeA">
                        
                        <?php $count_temp = 1 ; ?>
                        @foreach ($attsreg as $attreg)  
                            @if ($route != 'AShop')
                                <td> <a target="_blank" href="{{route('AClub.member',['id' => $client->master_id, 'package' => $clientreg->user_id])}}">{{$clientreg->$attreg}} </a>
                                @if ($count_temp == 1)
                                    <div class="btn-hvr-container"><button class="btn btn-primary hvr-btn">edit</button><button class="btn btn-primary hvr-btn">delete</button></div></td>
                                    <?php $count_temp++ ; ?>
                                @else
                                    </td>
                                @endif
                            @else
                                <td> <a target="_blank" href="{{route('AShop.trans',['id' => $client->master_id, 'transaction' => $clientreg->transaction_id])}}">{{$clientreg->$attreg}} </a>
                                @if ($count_temp == 1)
                                    <div class="btn-hvr-container"><button class="btn btn-primary hvr-btn">edit</button><button class="btn btn-primary hvr-btn">delete</button></div></td>
                                    <?php $count_temp++ ; ?>
                                @else
                                    </td>
                                @endif
                            @endif
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>    
            {{ $clientsreg -> links() }}
    @endif

	<br><br>

    @if(count($errors) > 0)
        @foreach($errors->all() as $error)
            <h4>{{$error}}</h4>
        @endforeach
    @endif
	
</div>
</body>
</html>

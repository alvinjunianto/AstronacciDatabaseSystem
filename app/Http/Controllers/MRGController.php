<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Excel;
use App\Mrg;
use App\MrgAccount;
use App\MasterClient;
use DB;

class MRGController extends Controller
{

    private function nullify($string)
    {
        $newstring = trim($string);
        if ($newstring === ''){
           return null;
        }
        return $newstring;
    }

    public function getData()
    {
        $mrgs = MRG::all();

        foreach ($mrgs as $mrg) {
            $master = $mrg->master;
            $mrg->master_id = $master->master_id;
            $mrg->name = $master->name;
            $mrg->telephone_number = $master->telephone_number;
            $mrg->email = $master->email;
            $mrg->birthdate = $master->birthdate;
            $mrg->address = $master->address;
            $mrg->city = $master->city;
            $mrg->province = $master->province;
            $mrg->gender = $master->gender;
            $mrg->line_id = $master->line_id;
            $mrg->whatsapp = $master->whatsapp;
            $mrg->facebook = $master->facebook;

            //data from mrg transaction
            $last_transaction = $mrg->accounts()->orderBy('created_at','desc')->first();
            // dd($last_transaction->sales_name);
            $mrg->sales_name = $last_transaction->sales_name;
            $mrg->accounts_number = $last_transaction->accounts_number;
            $mrg->account_type = $last_transaction->account_type;
        }

        return $mrgs;
    }

    public function getTable(Request $request) {
        // $keyword = $request['q'];

        // $aclub_info = AclubInformation::where('sumber_data', 'like', "%{$keyword}%")
        //         ->orWhere('keterangan', 'like', "%{$keyword}%")
        //         ->paginate(15);
        $page = 0;
        $page = $request['page']-1;
        $record_amount = 15;

        $mrgs = $this->getData();
        $record_count = count($mrgs);
        $mrgs = $mrgs->forPage(1, $record_amount);
        // $aclub_members = collect(array_slice($aclub_members, $page*$record_amount, $record_amount));
        // $aclub_members = $aclub_members->skip($record_amount*$page)->take($record_amount);

        // dd($aclub_members);
        $page_count = ceil($record_count/$record_amount);

        $headsMaster = [
                    "User ID",
                    "Nama",
                    "Email",
                    "Telepon",
                    "Tanggal Lahir"
                ];

        $attsMaster = [
                        "master_id",
                        "name",
                        "email",
                        "telephone_number",
                        "birthdate"
                    ];

        //Judul kolom yang ditampilkan pada tabel
        $heads = [
                "Alamat" => "address",
                "Kota" => "city",
                "Gender" => "gender",
                "Line ID" => "line_id",
                "WhatsApp" => "whatsapp",
                "Sumber" => "sumber_data",
                "Sales" => "sales_name",
                "Tanggal Join" => "join_date",
                "Account" => "accounts_number",
                "Type" => "account_type",
                ];
        

        //Nama attribute pada sql
        $atts = [
                "address",
                "city",
                "gender",
                "line_id",
                "whatsapp",
                "sumber_data",
                "sales_name",
                "join_date",
                "accounts_number",
                "account_type"
                ];

        //Filter
        $master_clients = MasterClient::all();
        $array_month = array();
        foreach ($master_clients as $master_client) {
            array_push($array_month, date('m', strtotime($master_client->birthdate)));
        }
        $filter_birthdates = array_unique($array_month);
        sort($filter_birthdates);
        foreach ($filter_birthdates as $key => $filter_birthdate) {
            // dd(date('F', mktime(0, 0, 0, $filter_birthdate, 10)));
            $filter_birthdates[$key] = date('F', mktime(0, 0, 0, $filter_birthdate, 10));
        }

        // $this->getFilteredAndSortedTable('test');

        $joined = DB::table('master_clients')
                    ->join('mrgs', 'mrgs.master_id', '=', 'master_clients.master_id');

        $filter_cities = $joined->select('city')->distinct()->get();
        $filter_gender = $joined->select('gender')->distinct()->get();
        $filter_sumber = DB::table('mrgs')->select('sumber_data')->distinct()->get();
        $filter_sales = DB::table('mrg_accounts')->select('sales_name')->distinct()->get();
        // $filter_accounts = DB::table('mrg_accounts')->select('accounts_number')->distinct()->get();
        $filter_type = DB::table('mrg_accounts')->select('account_type')->distinct()->get();
        $filter_date = ['0'=>['0'=>'January'], 
        '1'=>['0'=>'February'], 
        '2'=>['0'=>'March'], 
        '3'=>['0'=>'April'], 
        '4'=>['0'=>'May'], 
        '5'=>['0'=>'June'], 
        '6'=>['0'=>'July'],
        '7'=>['0'=>'August'],
        '8'=>['0'=>'September'],
        '9'=>['0'=>'October'],
        '10'=>['0'=>'November'],
        '11'=>['0'=>'December']];

        $filterable = [
            "Kota" => $filter_cities,
            "Gender" => $filter_gender,
            "Sumber" => $filter_sumber,
            "Sales" => $filter_sales,
            "Tanggal Join" => $filter_date,
            "Type" => $filter_type
            ];

        //sort
        $sortables = [
            "Kota" => "city",
            "Gender" => "gender",
            "Sumber" => "sumber_data",
            "Sales" => "sales_name",
            "Tanggal Join" => "join_date",
            "Account" => "accounts_number",
            "Type" => "account_type"];

        //Return view table dengan parameter
        return view('vpc/mrgview',
                    [
                        'route' => 'MRG',
                        'clients' => $mrgs,
                        'heads'=>$heads, 'atts'=>$atts,
                        'headsMaster' => $headsMaster,
                        'attsMaster' => $attsMaster,
                        'filter_birthdates' => $filter_birthdates,
                        'filter_cities' => $filter_cities,
                        'filter_gender' => $filter_gender,
                        'filter_sumber' => $filter_sumber,
                        'filter_sales' => $filter_sales,
                        'filter_type' => $filter_type,
                        'filter_date' => $filter_date,
                        'filterable' => $filterable,
                        'sortables' => $sortables,
                        'count' => $page_count
                    ]);
    }

    // RETURN : LIST (COLLECTION) OF FILTERED AND SORTED TABLE LIST

    public function getFilteredAndSortedTable(Request $request) {
        // test
        // $example_filter = array('gender'=>['M'], 'birthdate'=>[4,5,6]);
        // $example_sort = array('email'=>false, 'name'=>true);

        // $json_filter = json_encode($example_filter);
        // $json_sort = json_encode($example_sort);
        // test

         $attsMaster = [
                        "master_id",
                        "name",
                        "email",
                        "telephone_number",
                        "birthdate"
                    ];

        //Nama attribute pada sql
        $atts = [
                "address",
                "city",
                "gender",
                "line_id",
                "whatsapp",
                "sumber_data",
                "sales_name",
                "join_date",
                "accounts_number",
                "account_type"
                ];

        $json_filter = $request['filters'];
        $json_sort = $request['sorts'];
        $page = 0;
        $page = $request['page']-1;
        $record_amount = 15;


        // add 'select' of query
        $query = "";
        $query = $query."SELECT * ";
        $query = $query."FROM  ";
        $query = $query."master_clients  ";
        $query = $query."INNER JOIN mrgs ON master_clients.master_id = mrgs.master_id  ";
        $query = $query."INNER JOIN (SELECT  accounts_number, T1.master_id, account_type,  ";
        $query = $query."            sales_name, T1.created_at, updated_at, created_by, updated_by ";
        $query = $query."            FROM  ";
        $query = $query."                ( SELECT master_id, max(created_at) as created_at  ";
        $query = $query."                    FROM mrg_accounts ";
        $query = $query."                    GROUP BY master_id) as T1  ";
        $query = $query."            INNER JOIN  ";
        $query = $query."                ( SELECT * ";
        $query = $query."                   FROM mrg_accounts) as T2  ";
        $query = $query."                    ON T1.master_id = T2.master_id  ";
        $query = $query."                    AND T1.created_at = T2.created_at) as last_transaction  ";
        $query = $query."ON master_clients.master_id = last_transaction.master_id ";

        // add subquery of filter
        $query = $this->addFilterSubquery($query, $json_filter);
        // add subquery of sort
        $query = $this->addSortSubquery($query, $json_sort);
        // add semicolon
        $query = $query.";";

        // retrieve result
        $list_old = DB::select($query);

        $record_count = count($list_old);
        $page_count = ceil($record_count/$record_amount);
        
        $list = collect(array_slice($list_old, $page*$record_amount, $record_amount));

        return view('vpc/mrgtable',
                    [
                        'route' => 'MRG',
                        'clients' => $list,
                        'atts' => $atts,
                        'attsMaster' => $attsMaster,
                        'count' => $page_count
                    ]);
        // return $list;
    }
 
    // RETURN : STRING QUERY FOR FILTER IN SQL 
    // NOTE : WITHOUT SEMICOLON
    public function addFilterSubquery($query, $json_filter) {
        $filter = json_decode($json_filter, true);

        if (empty($filter)) {
            return $query;
        }

        // add 'where' of query
        $query = $query.' WHERE ';        
        $is_first = true;
        foreach ($filter as $key_filter => $values_filter) {
            if (!$is_first) {
                $query = $query." and ";
            }
            $idx_filter = 0;
            $query = $query.'(';

            if (in_array($key_filter, ['birthdate','payment_date'])) {
                $idx_value = 0;
                foreach ($values_filter as $value_filter) {
                    $query = $query."MONTH(".$key_filter.")"." = '".$value_filter."'";
                    $idx_value += 1;
                    if ($idx_value != count($values_filter)) {
                        $query = $query." or ";
                    }   
                 }
            } else {
                $idx_value = 0;
                foreach ($values_filter as $value_filter) {
                    $query = $query.$key_filter." = '".$value_filter."'";
                    $idx_value += 1;
                    if ($idx_value != count($values_filter)) {
                        $query = $query." or ";
                    }
                 }
            }
            $query = $query.')';
            $is_first = false;
        }   

        // get result
        return $query;
    }

    public function addSortSubquery($query, $json_sort) {
        $sort = json_decode($json_sort, true);

        if (empty($sort)) {
            return $query;
        }
        
        $subquery = " ORDER BY ";
        $idx_sort = 0;
        foreach ($sort as $key_sort => $value_sort) {
            if ($value_sort == true) {
                $subquery = $subquery.$key_sort." ASC";            
            } else {
                $subquery = $subquery.$key_sort." DESC";                            
            }
            $idx_sort += 1;
            if ($idx_sort != count($sort)) {
                $subquery = $subquery.", ";
            }
        }
        $query = $query.$subquery;
        return $query;
    }

    public function clientDetail($id, Request $request) {
        $mrg = MRG::where('master_id', $id)->first();

        $ins= ["Sumber Data (MRG)" => "sumber_data",
                "Join Date (MRG)" => "join_date",
                "Sales" => "sales_name"];

        $heads = $ins;

        // form transaction
        $insreg = ["Account Number", "Account Type", "Sales Name"];

        $keyword = $request['q'];

        $clientsreg = $mrg->accounts()
                    ->where('accounts_number', 'like', "%{$keyword}%")
                    ->orWhere('account_type', 'like', "%{$keyword}%")
                    ->orWhere('sales_name', 'like', "%{$keyword}%")
                    ->paginate(15);

        //kolom account
        $headsreg = ["Account Number", "Account Type", "Sales Name"];

        //attribute sql account
        $attsreg = ["accounts_number", "account_type", "sales_name"];

        return view('profile/transtable', ['route'=>'MRG', 'client'=>$mrg, 'heads'=>$heads, 'ins'=>$ins, 'insreg'=>$insreg, 'clientsreg'=>$clientsreg, 'headsreg'=>$headsreg, 'attsreg'=>$attsreg]);
    }

     public function addTrans(Request $request) {
        $this->validate($request, [
                'master_id' => 'required',
                "account_number" => 'required|unique:mrg_accounts|string:20', 
                "account_type" => 'string:20', 
                "sales_name" => ''
            ]);

        $mrg_account = new \App\MrgAccount();

        $err = [];

        $mrg_account->master_id = $request->user_id;
        $mrg_account->accounts_number = $request->accounts_number;
        $mrg_account->account_type = $request->account_type;
        $mrg_account->sales_name = $request->sales_name;

        $mrg_account->save();

        return redirect()->back()->withErrors($err);
    }

    public function deleteClient($id) {
        //Menghapus client dengan ID tertentu
        try {
            $mrg = Mrg::find($id);
            $mrg->delete();
        } catch(\Illuminate\Database\QueryException $ex){
            $err[] = $ex->getMessage();
        }
        return back();
    }

    public function editClient(Request $request) {
        //Validasi input
        $this->validate($request, [
                'user_id' => 'required|unique:mrgs',
                'sumber_data' => '',
                'join_date' => 'date'
            ]);
        //Inisialisasi array error
        $err = [];
        try {
            $mrg = Mrg::where('master_id',$request->user_id)->first();

            $err =[];

            $mrg->sumber_data = $request->sumber_data;
            $mrg->join_date = $request->join_date;

            $mrg->update();
        } catch(\Illuminate\Database\QueryException $ex){
            $err[] = $ex->getMessage();
        }
        return redirect()->back()->withErrors($err);
    }

    public function clientDetailAccount($id, $account) {

        $mrg_account = MrgAccount::where('accounts_number', $account)->first();

        $heads = ["Master ID" => "master_id",
                    "Nomor Account" => "accounts_number",
                    "Type Account" => "account_type",
                    "Sales" => "sales_name"];

        $ins = ["Type Account" => "account_type",
                "Sales" => "sales_name"];

        return view('profile/mrgaccount', ['route'=>'MRG', 'client'=>$mrg_account, 'ins'=>$ins, 'heads'=>$heads]);
    }

     public function editTrans(Request $request) {
        //Validasi input
        $this->validate($request, [
                "account_number" => 'required|unique:mrg_accounts|string:20', 
                "account_type" => 'string:20', 
                "sales_name" => ''
            ]);
        $mrg_account = MrgAccount::where('accounts_number',$request->user_id)->first();
        //Inisialisasi array error
        $err = [];

        try {
            $mrg_account->account_type = $request->account_type;
            $mrg_account->sales_name = $request->sales_name;

            $mrg_account->update();
        } catch(\Illuminate\Database\QueryException $ex){
            $err[] = $ex->getMessage();
        }

        if(!empty($err)) {
            return redirect()->back()->withErrors($err);
        } else {
            return redirect()->route('detail', ['id' => $mrg_account->master_id]);
        }
        
    }

    public function deleteTrans($id) {
        try {
            $mrg_account = MrgAccount::find($id);
            $mrg_account->delete();
        } catch(\Illuminate\Database\QueryException $ex){
            $err[] = $ex->getMessage();
        }
        return back();
    }

    public function importExcel() {
        $err = []; //Inisialisasi array error
        if(Input::hasFile('import_file')){ //Mengecek apakah file diberikan
            $path = Input::file('import_file')->getRealPath(); //Mendapatkan path
            $data = Excel::load($path, function($reader) { //Load excel
            })->get();
            if(!empty($data) && $data->count()){
                $i = 1;

                //Cek apakah ada error
                foreach ($data as $key => $value) {
                    $i++;
                    if (($value->master_id) === null) {
                        $msg = "Master ID empty on line ".$i;
                        $err[] = $msg;
                    }
                    if (($value->sumber_data) === null) {
                        $msg = "Sumber Data empty on line ".$i;
                        $err[] = $msg;
                    }
                    if (($value->tanggal_join) === null) {
                        $msg = "Tanggal Join empty on line ".$i;
                        $err[] = $msg;
                    }
                } //end validasi

                //Jika tidak ada error, import dengan cara insert satu per satu
                if (empty($err)) {
                    foreach ($data as $key => $value) {
                        try {
                            $mrg = new \App\Mrg;

                            $mrg->master_id = $value->master_id;
                            $mrg->sumber_data = $value->sumber_data;
                            $mrg->join_date = $value->tanggal_join;

                            $mrg->save();
                        } catch(\Illuminate\Database\QueryException $ex){
                          echo ($ex->getMessage());
                          $err[] = $ex->getMessage();
                        }
                    }
                    if (empty($err)) { //message jika tidak ada error saat import
                        $msg = "Excel successfully imported";
                        $err[] = $msg;
                    }
                }
            }
        } else {
            $msg = "No file supplied";
            $err[] = $msg;
        }

        return redirect()->back()->withErrors([$err]);
    }

    public function exportExcel() {
        $data = Mrg::all();
        $array = [];
        $heads = ["Master ID" => "master_id", "Sumber Data" => "sumber_data", "Join Date" => "join_date"];
        foreach ($data as $dat) {
            $arr = [];
            foreach ($heads as $key => $value) {
                //echo $key . " " . $value . "<br>";
                $arr[$key] = $dat->$value;
            }
            $array[] = $arr;
        }
        //print_r($array);
        //$array = ['a' => 'b'];
        return Excel::create('ExportedMRG', function($excel) use ($array) {
            $excel->sheet('Sheet1', function($sheet) use ($array)
            {
                $sheet->fromArray($array);
            });
        })->export('xls');
    }

    public function updateTrans($account) {
        $mrg_account = MrgAccount::where('accounts_number', $account)->first();

        $ins = ["Type Account" => "account_type",
                "Sales" => "sales_name"];

        return view('content/mrgeditform', ['route'=>'MRG', 'client'=>$mrg_account, 'ins'=>$ins]);
    }
}

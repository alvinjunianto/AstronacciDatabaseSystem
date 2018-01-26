<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Excel;
use DB;
use App\Uob;
use App\MasterClient;

class UOBController extends Controller
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
        $uobs = Uob::all();

        foreach ($uobs as $uob) {
            $master = $uob->master;
            $uob->master_id = $master->master_id;
            $uob->name = $master->name;
            $uob->telephone_number = $master->telephone_number;
            $uob->email = $master->email;
            $uob->birthdate = $master->birthdate;
            $uob->address = $master->address;
            $uob->city = $master->city;
            $uob->province = $master->province;
            $uob->gender = $master->gender;
            $uob->line_id = $master->line_id;
            $uob->bbm = $master->bbm;
            $uob->whatsapp = $master->whatsapp;
            $uob->facebook = $master->facebook;
        }

        return $uobs;
    }

    public function getTable(Request $request) {
        $page = 0;
        $page = $request['page']-1;
        $record_amount = 3;

        $uobs = $this->getData();
        $record_count = count($uobs);
        $uobs = $uobs->forPage(1, $record_amount);

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
                "Kode Client" => "client_id",
                "Status" => "status",
                "Nomor RDI" => "nomor_rdi",
                "Tanggal RDI" => "tanggal_rdi_done",
                "Tanggal Top Up" => "tanggal_top_up",
                "Tanggal Trading" => "tanggal_trading",
                "Bank" => "bank_pribadi",
                "Nomor Rekening" => "nomor_rekening_pribadi",
                "RDI Bank" => "rdi_bank"
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
                "client_id",
                "status",
                "nomor_rdi",
                "tanggal_rdi_done",
                "tanggal_top_up",
                "tanggal_trading",
                "bank_pribadi",
                "nomor_rekening_pribadi",
                "rdi_bank"
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
            $filter_birthdates[$key] = date('F', mktime(0, 0, 0, $filter_birthdate, 10));
        }

        $joined = DB::table('master_clients')
                    ->join('uobs', 'uobs.master_id', '=', 'master_clients.master_id');

        $filter_cities = $joined->select('city')->distinct()->get();
        $filter_gender = $joined->select('gender')->distinct()->get();
        $filter_sumber = DB::table('uobs')->select('sumber_data')->distinct()->get();
        $filter_sales = DB::table('uobs')->select('sales_name')->distinct()->get();
        $filter_status = DB::table('uobs')->select('status')->distinct()->get();
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
            "Status" => $filter_status,
            "Tanggal RDI" => $filter_date,
            "Tanggal Top Up" => $filter_date,
            "Tanggal Trading" => $filter_date
            ];

        //sort
        $sortables = [
            "Tanggal Lahir" => "birthdate",
            "Kota" => "city",
            "Gender" => "gender",
            "Sumber" => "sumber_data",
            "Sales" => "sales_name",
            "Status" => "status",
            "Tanggal RDI" => "tanggal_rdi_done",
            "Tanggal Top Up" => "tanggal_top_up",
            "Tanggal Trading" => "tanggal_trading"
            ];

        //Return view table dengan parameter
        return view('vpc/uobview',
                    [
                        'route' => 'UOB',
                        'clients' => $uobs,
                        'heads'=>$heads, 'atts'=>$atts,
                        'headsMaster' => $headsMaster,
                        'attsMaster' => $attsMaster,
                        'filter_birthdates' => $filter_birthdates,
                        'filter_cities' => $filter_cities,
                        'filter_gender' => $filter_gender,
                        'filter_sumber' => $filter_sumber,
                        'filter_sales' => $filter_sales,
                        'filter_status' => $filter_status,
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
                "client_id",
                "status",
                "nomor_rdi",
                "tanggal_rdi_done",
                "tanggal_top_up",
                "tanggal_trading",
                "bank_pribadi",
                "nomor_rekening_pribadi",
                "rdi_bank"
                ];

        $json_filter = $request['filters'];
        $json_sort = $request['sorts'];
        $page = 0;
        $page = $request['page']-1;
        $record_amount = 3;


        // add 'select' of query
        $query = "";
        $query = $query."SELECT * ";
        $query = $query."FROM master_clients ";
        $query = $query."INNER JOIN uobs ";
        $query = $query."ON uobs.master_id = master_clients.master_id ";

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

        return view('vpc/uobtable', [
                        'route' => 'UOB',
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

    public function clientDetail($id) {
        $uob = Uob::where('client_id', $id)->first();

        //judul + sql
        $ins= [
                "Kode Client" => "client_id",
                "Master ID" => "master_id",
                "Sales" => "sales_name",
                "Sumber Data" => "sumber_data",
                "Tanggal Join" => "join_date",
                "Nomor KTP" => "nomor_ktp",
                "Expired KTP" => "tanggal_expired_ktp",
                "Nomor NPWP" => "nomor_npwp",
                "Alamat Surat Menyurat" => "alamat_surat",
                "Saudara Tidak Serumah" => "saudara_tidak_serumah",
                "Nama Ibu Kandung" => "nama_ibu_kandung",
            ];

        $heads = $ins;

        //form transaction
        $insreg = ["Bank Pribadi" => "bank_pribadi",
                        "Nomor Rekening Pribadi" => "nomor_rekening_pribadi",
                        "Tanggal RDI Done" => 'tanggal_rdi_done',
                        "RDI Bank" => "rdi_bank",
                        "Nomor RDI" => 'nomor_rdi',
                        "Tanggal Top Up" => 'tanggal_top_up',
                        "Nominal Top Up" => 'nominal_top_up',
                        "Tanggal Trading" => 'tanggal_trading',
                        "Status" => 'status',
                        "Trading Via" => 'trading_via',
                        "Keterangan" => 'keterangan'];

        //judul + sql transaction
        $headsreg = [  "Bank Pribadi" => "bank_pribadi",
                        "Nomor Rekening Pribadi" => "nomor_rekening_pribadi",
                        "Tanggal RDI Done" => 'tanggal_rdi_done',
                        "RDI Bank" => "rdi_bank",
                        "Nomor RDI" => 'nomor_rdi',
                        "Tanggal Top Up" => 'tanggal_top_up',
                        "Nominal Top Up" => 'nominal_top_up',
                        "Tanggal Trading" => 'tanggal_trading',
                        "Status" => 'status',
                        "Trading Via" => 'trading_via',
                        "Keterangan" => 'keterangan',
                    ];

        return view('profile/profile', ['route'=>'UOB', 'client'=>$uob, 'heads' => $heads, 'ins'=>$ins, 'insreg'=>$insreg, 'headsreg'=>$headsreg]);
    }

    public function addTrans(Request $request) {
        $this->validate($request, [
                'bank_pribadi' => '',
                'nomor_rekening_pribadi' => 'string:50',
                'tanggal_rdi_done' => 'date',
                'rdi_bank' => 'string:20',
                'nomor_rdi' => '',
                'tanggal_top_up' => 'date',
                'nominal_top_up' => 'integer',
                'tanggal_trading' => 'date',
                'status' => '',
                'trading_via' => '',
                'keterangan' => ''
            ]);

        $uob = Uob::where('client_id',$request->user_id)->first();

        $err =[];

        $uob->bank_pribadi = $request->bank_pribadi;
        $uob->nomor_rekening_pribadi = $request->nomor_rekening_pribadi;
        $uob->tanggal_rdi_done = $request->tanggal_rdi_done;
        $uob->rdi_bank = $request->rdi_bank;
        $uob->nomor_rdi = $request->nomor_rdi;
        $uob->tanggal_top_up = $request->tanggal_top_up;
        $uob->nominal_top_up = $request->nominal_top_up;
        $uob->tanggal_trading = $request->tanggal_trading;
        $uob->status = $request->status;
        $uob->trading_via = $request->trading_via;
        $uob->keterangan = $request->keterangan;

        $uob->update();

        return redirect()->back()->withErrors($err);
    }

    public function editClient(Request $request) {
        //Validasi input
        $this->validate($request, [
                'master_id' => 'required',
                'kode_client' => 'required|unique:uobs',
                'sales_uob' => '',
                'sumber_data_uob' => '',
                'tanggal_join_uob' => 'date',
                'nomer_ktp' => 'string:20',
                'expired_ktp' => 'date',
                'nomer_npwp' => 'string:40',
                'alamat_surat' => '',
                'saudara_tidak_serumah' => '',
                'ibu_kandung' => '',
            ]);

        //Inisialisasi array error
        $err = [];
        try {
            $uob = UOB::where('client_id',$request->user_id)->first();

            $err =[];

            $uob->client_id = $request->client_id;
            $uob->master_id = $request->master_id;
            $uob->sales_name = $request->sales_name;
            $uob->sumber_data = $request->sumber_data;
            $uob->join_date = $request->join_date;
            $uob->nomor_ktp = $request->nomor_ktp;
            $uob->tanggal_expired_ktp = $request->tanggal_expired_ktp;
            $uob->nomor_npwp = $request->nomor_npwp;
            $uob->alamat_surat = $request->alamat_surat;
            $uob->saudara_tidak_serumah = $request->saudara_tidak_serumah;
            $uob->nama_ibu_kandung = $request->nama_ibu_kandung;

            $uob->update();
        } catch(\Illuminate\Database\QueryException $ex){
            $err[] = $ex->getMessage();
        }
        return redirect()->back()->withErrors($err);
    }

    public function deleteClient($id) {
        //Menghapus client dengan ID tertentu
        try {
            $cat = Uob::find($id);
            $cat->delete();
        } catch(\Illuminate\Database\QueryException $ex){
            $err[] = $ex->getMessage();
        }
        return redirect("home");
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
                    if (($value->kode_client) === null) {
                        $msg = "Kode Client empty on line ".$i;
                        $err[] = $msg;
                    }
                    if (($value->master_id) === null) {
                        $msg = "Master ID empty on line ".$i;
                        $err[] = $msg;
                    }
                    if (($value->sales) === null) {
                        $msg = "Sales empty on line ".$i;
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
                    if (($value->nomer_ktp) === null) {
                        $msg = "Nomer KTP empty on line ".$i;
                        $err[] = $msg;
                    }
                    if (($value->expired_ktp) === null) {
                        $msg = "Expired KTP empty on line ".$i;
                        $err[] = $msg;
                    }
                    if (($value->nomer_npwp) === null) {
                        $msg = "Nomer NPWP empty on line ".$i;
                        $err[] = $msg;
                    }
                    if (($value->alamat_surat) === null) {
                        $msg = "Alamat Surat empty on line ".$i;
                        $err[] = $msg;
                    }
                    if (($value->saudara_tidak_serumah) === null) {
                        $msg = "Saudara Tidak Serumah empty on line ".$i;
                        $err[] = $msg;
                    }
                    if (($value->ibu_kandung) === null) {
                        $msg = "Ibu Kandung empty on line ".$i;
                        $err[] = $msg;
                    }

                } //end validasi

                //Jika tidak ada error, import dengan cara insert satu per satu
                if (empty($err)) {
                    foreach ($data as $key => $value) {
                        try {
                            $uob = new \App\Uob;

                            $uob->client_id = $value->kode_client;
                            $uob->master_id = $value->master_id;
                            $uob->sales_name = $value->sales;
                            $uob->sumber_data = $value->sumber_data;
                            $uob->join_date = $value->tanggal_join;
                            $uob->nomor_ktp = $value->nomer_ktp;
                            $uob->tanggal_expired_ktp = $value->expired_ktp;
                            $uob->nomor_npwp = $value->nomer_npwp;
                            $uob->alamat_surat = $value->alamat_surat;
                            $uob->saudara_tidak_serumah = $value->saudara_tidak_serumah;
                            $uob->nama_ibu_kandung = $value->ibu_kandung;

                            $uob->save();
                        } catch(\Illuminate\Database\QueryException $ex){
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
        $data = UOB::all();
        $array = [];
        $heads = [
          "Kode Client" => "client_id",
          "Master ID" => "master_id",
          "Sales" => "sales_name",
          "Sumber Data" => "sumber_data",
          "Tanggal Join" => "join_date",
          "Nomor KTP" => "nomor_ktp",
          "Expired KTP" => "tanggal_expired_ktp",
          "Nomor NPWP" => "nomor_npwp",
          "Alamat Surat Menyurat" => "alamat_surat",
          "Saudara Tidak Serumah" => "saudara_tidak_serumah",
          "Nama Ibu Kandung" => "nama_ibu_kandung",
          "Bank Pribadi" => "bank_pribadi",
          "Nomor Rekening Pribadi" => "nomor_rekening_pribadi",
          "Tanggal RDI Done" => 'tanggal_rdi_done',
          "RDI Bank" => "rdi_bank",
          "Nomor RDI" => 'nomor_rdi',
          "Tanggal Top Up" => 'tanggal_top_up',
          "Nominal Top Up" => 'nominal_top_up',
          "Tanggal Trading" => 'tanggal_trading',
          "Status" => 'status',
          "Trading Via" => 'trading_via',
          "Keterangan" => 'keterangan'
        ];
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
        return Excel::create('ExportedUOB', function($excel) use ($array) {
            $excel->sheet('Sheet1', function($sheet) use ($array)
            {
                $sheet->fromArray($array);
            });
        })->export('xls');
    }
}

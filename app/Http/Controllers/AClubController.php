<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Excel;
use DB;
use App\AclubInformation;
use App\AclubMember;
use App\AclubTransaction;
use App\MasterClient;

class AClubController extends Controller
{
    
    private function nullify($string)
    {
        $newstring = trim($string);
        if ($newstring === ''){
           return null;
        }
        return $newstring;
    }

    public function getTable() {
        //Select seluruh tabel
        $aclub_info = AclubInformation::paginate(15);

        // $this->getAClubMember(100003);

        // $this->getAClubTransaction(124);

        foreach ($aclub_info as $aclub_master) {
            $master = $aclub_master->master;
            $aclub_master->redclub_user_id = $master->redclub_user_id;
            $aclub_master->redclub_password = $master->redclub_password;
            $aclub_master->name = $master->name;
            $aclub_master->telephone_number = $master->telephone_number;
            $aclub_master->email = $master->email;
            $aclub_master->birthdate = $master->birthdate;
            $aclub_master->address = $master->address;
            $aclub_master->city = $master->city;
            $aclub_master->province = $master->province;
            $aclub_master->gender = $master->gender;
            $aclub_master->line_id = $master->line_id;
            $aclub_master->bbm = $master->bbm;
            $aclub_master->whatsapp = $master->whatsapp;
            $aclub_master->facebook = $master->facebook;
        }

        //Judul kolom yang ditampilkan pada tabel
        $heads = ["Master ID",
                "RedClub User ID",
                "RedClub Password",
                "Nama",
                "Nomor Telepon",
                "Email",
                "Tanggal Lahir",
                "Alamat",
                "Kota",
                "Provinsi",
                "Gender",
                "Line ID",
                "BBM",
                "WhatsApp",
                "Facebook",
                "Sumber Data (A-Club)",
                "Keterangan (A-Club)"];


        //Nama attribute pada sql
        $atts = ["master_id",
                "redclub_user_id",
                "redclub_password",
                "name",
                "telephone_number",
                "email",
                "birthdate",
                "address",
                "city",
                "province",
                "gender",
                "line_id",
                "bbm",
                "whatsapp",
                "facebook",
                "sumber_data",
                "keterangan"];

        //Return view table dengan parameter
        return view('content/table', ['route' => 'AClub', 'clients' => $aclub_info, 'heads'=>$heads, 'atts'=>$atts]);
    }

    public function getAClubMember($id) {
        // detail master dengan master_id = $id
        $aclub_information = AclubInformation::where('master_id', $id)->first();

        // aclub_master adalah aclub_master nya
        $aclub_master = $aclub_information->master;

        // aclub_members adalah list member dari master_id = $id
        $aclub_members = $aclub_master->aclubMembers;

        $heads = ["Master ID",
                "User ID",
                "Sales Name",
                "Group"];

        $atts = ["master_id",
                "user_id",
                "sales_name",
                "group"];

        $insreg = ["User ID",
                    "Payment Date", 
                    "Sales",
                    "Kode",
                    "Nominal",
                    "Start Date",
                    "Keterangan"];

        // yang ditampilin di page member cuman aclub_information dan aclub_members aja
        dd($aclub_members);

    }

    public function getAClubTransaction($user_id) {
        // aclub dengan user_id = $user_id
        $aclub_member = AclubMember::where('user_id', $user_id)->first();

        $aclub_transaction = $aclub_member->aclubTransactions;

        $heads = ["Transaction ID",
                    "User ID",
                    "Payment Date",
                    "Kode",
                    "Nominal",
                    "Start date",
                    "Expired date",
                    "Masa tenggang",
                    "Yellow Zone",
                    "Red Zone"];

        $atts = ["transaction_id",
                    "user_id",
                    "payment_date",
                    "kode",
                    "status",
                    "nominal",
                    "start_date",
                    "expired_date",
                    "masa_tenggang",
                    "yellow_zone",
                    "red_zone"];

        $insreg = ["Payment date", 
                    "Kode", 
                    "Nominal",
                    "Start Date",
                    "Keterangan"];

        // yang ditampilin di page member cuman aclub_information dan aclub_members aja
        dd($aclub_transaction);
    }



    public function clientDetail($id) {
        // detail master dengan master_id = $id
        $aclub_information = AclubInformation::where('master_id', $id)->first();

        // aclub_master adalah aclub_master nya
        $aclub_master = $aclub_information->master;

        $ins = ["Master_id" => "master_id", 
                "Sumber Data" => "sumber_data", 
                "Keterangan" => "keterangan"];

        $heads = $ins;

        // aclub_members adalah list member dari master_id = $id
        $aclub_members = $aclub_master->aclubMembers()->paginate(1);

        $headsreg = ["User ID",
                    "Payment Date", 
                    "Sales",
                    "Kode",
                    "Nominal",
                    "Start Date",
                    "Keterangan"];

        $insreg = ["User ID",
                    "Payment Date",
                    "Sales",
                    "Kode",
                    "Nominal",
                    "Start Date",
                    "Keterangan"];

        $attsreg = ["user_id", "payment_date", "sales_name", "kode", "nominal", "start_date", "keterangan"];



        // yang ditampilin di page member cuman aclub_information dan aclub_members aja

        return view('profile/profile', ['route'=>'AClub', 'client'=>$aclub_information, 'clientsreg'=>$aclub_members, 'heads'=>$heads, 'ins'=>$ins, 'insreg'=>$insreg, 'headsreg'=>$headsreg, 'attsreg'=>$attsreg]);
    }

    public function editClient(Request $request) {
        //Validasi input
        $this->validate($request, [
                'all_pc_id' => 'required',
                'user_id' => 'required',
                'fullname' => 'required',
                'email' => 'email',
                'no_hp' => 'required',
                'address' => 'required',
            ]);
        //Inisialisasi array error
        DB::beginTransaction();
        $err = [];
        try {
            //Untuk parameter yang tidak boleh null, digunakan nullify untuk menjadikan input empty string menjadi null
            //Edit atribut master client
            DB::select("call edit_master_client(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", [$request->all_pc_id, $request->fullname, $this->nullify($request->email), $request->no_hp, $this->nullify($request->birthdate), $this->nullify($request->line_id), $this->nullify($request->bb_pin), $this->nullify($request->twitter), $request->address, $this->nullify($request->city), $this->nullify($request->marital_status), $this->nullify($request->jenis_kelamin), $this->nullify($request->no_telp), $this->nullify($request->provinsi), $this->nullify($request->facebook)]);
            //Edit atribut AClub
            DB::select("call edit_aclub(?,?,?,?,?,?,?,?,?,?,?,?,?)", [$request->all_pc_id, $request->user_id, $this->nullify($request->interest_and_hobby), $this->nullify($request->trading_experience_year), $this->nullify($request->your_stock_future_broker), $this->nullify($request->annual_income), $this->nullify($request->security_question), $this->nullify($request->security_answer), $this->nullify($request->status), $this->nullify($request->keterangan), $this->nullify($request->website), $this->nullify($request->state), $this->nullify($request->occupation)]);
        } catch(\Illuminate\Database\QueryException $ex){ 
            DB::rollback();
            $err[] = $ex->getMessage();
        }
        DB::commit();
        return redirect()->back()->withErrors($err);
    }

    // public function addClient(Request $request) {
    //     //Validasi input
    //     $this->validate($request, [
    //             'user_id' => 'required',
    //             'nama' => 'required',
    //             'email' => 'email',
    //             'no_hp' => 'required',
    //             'alamat' => 'required',
    //         ]);

    //     //Inisialisasi array error
    //     DB::beginTransaction();
    //     $err = [];
    //     try {
    //         //Input data ke SQL
    //          DB::select("call inputaclub_member(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", [$request->user_id, $request->nama, $request->no_hp, $this->nullify($request->no_telepon), $request->alamat, $this->nullify($request->kota), $this->nullify($request->provinsi), $request->email, $this->nullify($request->tanggal_lahir), $this->nullify($request->line_id), $this->nullify($request->pin_bb), $this->nullify($request->facebook), $this->nullify($request->twitter), $this->nullify($request->jenis_kelamin), $this->nullify($request->occupation), $this->nullify($request->website), $this->nullify($request->state), $this->nullify($request->interest_and_hobby), $this->nullify($request->trading_experience_year), $this->nullify($request->your_stock_and_future_broker), $this->nullify($request->annual_income), $this->nullify($request->status), $this->nullify($request->keterangan),$this->nullify($request->security_question), $this->nullify($request->security_answer)]);
    //     } catch(\Illuminate\Database\QueryException $ex){ 
    //         DB::rollback();
    //         $err[] = $ex->getMessage();
    //     }
    //     DB::commit();
    //     return redirect()->back()->withErrors($err);

    // }

    public function deleteClient($id) {
        //Menghapus client dengan ID tertentu
        try {
            DB::select("call delete_aclub(?)", [$id]);
        } catch(\Illuminate\Database\QueryException $ex){ 
            $err[] = $ex->getMessage();
        }
        return redirect("home");
    }

    public function addTrans(Request $request) {
        $aclub_trans = new \App\AclubTransaction();
        $aclub_trans->user_id = $request->user_id;
        $aclub_trans->payment_date = $request->payment_date;
        $aclub_trans->kode = $request->kode;
        $aclub_trans->status = $request->status;
        $aclub_trans->nominal = $request->nominal;
        $aclub_trans->start_date = $request->start_date;
        $aclub_trans->expired_date = $request->expired_date;
        $aclub_trans->masa_tenggang = $request->masa_tenggang;
        $aclub_trans->yellow_zone = $request->yellow_zone;
        $aclub_trans->red_zone = $request->red_zone;
        $aclub_trans->sales_name = $request->sales_name;

        $aclub_trans->save();
        return redirect()->back()->withErrors($err);
    }

    public function deleteTrans($id){
        echo ($id);
        $err = [];
        try{
            DB::select("call delete_aclub_registration_alt(?)", [$id]);
        } catch(\Illuminate\Database\QueryException $ex){ 
            $err[] = $ex->getMessage();
        }
        
        return redirect()->back()->withErrors($err);
    }

    public function importExcel() {
        //Inisialisasi array error
        $err = [];
        if(Input::hasFile('import_file')){ //Mengecek apakah file diberikan
            $path = Input::file('import_file')->getRealPath(); //Mendapatkan path
            $data = Excel::load($path, function($reader) { //Load excel
            })->get();
            if(!empty($data) && $data->count()){
                $i = 1;
                //Cek apakah ada error
                foreach ($data as $key => $value) {
                    $i++;
                    if (($value->user_id) === null) {
                        $msg = "User ID empty on line ".$i;
                        $err[] = $msg;
                    }
                    if (($value->nama) === null) {
                        $msg = "Nama empty on line ".$i;
                        $err[] = $msg;
                    }
                    if (($value->email) === null) {
                        $msg = "Email empty on line ".$i;
                        $err[] = $msg;
                    }
                    if (($value->no_hp) === null) {
                        $msg = "No HP empty on line ".$i;
                        $err[] = $msg;
                    }
                    if (($value->alamat) === null) {
                        $msg = "Alamat empty on line ".$i;
                        $err[] = $msg;
                    }
                } //end validasi

                //Jika tidak ada error, import dengan cara insert satu per satu
                if (empty($err)) {
                    foreach ($data as $key => $value) {
                        echo $value->account . ' ' . $value->nama . ' ' . $value->tanggal_join . ' ' . $value->alamat . ' ' . $value->kota . ' ' . $value->telepon . ' ' . $value->email . ' ' . $value->type . ' ' . $value->sales . ' ' . "<br/>";
                        try { 
                            DB::select("call inputaclub_member(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", [$value->user_id, $value->nama, $value->no_hp, $value->no_telepon, $value->alamat, $value->kota, $value->provinsi, $value->email, $this->nullify($value->tanggal_lahir), $value->line_id, $value->pin_bb, $value->facebook, $value->twitter, $value->jenis_kelamin, $value->occupation, $value->website, $value->state, $value->interest_and_hobby, $this->nullify($value->trading_experience_year), $value->your_stock_and_future_broker, $this->nullify($value->annual_income), $value->status, $value->keterangan, $value->security_question, $value->security_answer]);
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
}
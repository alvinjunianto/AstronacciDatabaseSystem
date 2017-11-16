<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Excel;
use App\GreenProspectClient;
use App\GreenProspectProgress;
use DB;

class GreenController extends Controller
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
        $clients = GreenProspectClient::paginate(15);

        $heads = ["Green ID",
                    "Name",
                    "Date",
                    "Phone",
                    "Email",
                    "Interest",
                    "Pemberi",
                    "Sumber Data",
                    "Keterangan Perintah"];


        $atts = ["green_id",
                    "name",
                    "date",
                    "phone",
                    "email",
                    "interest",
                    "pemberi",
                    "sumber_data",
                    "keterangan_perintah"];

        return view('content/table', ['route' => 'green', 'clients' => $clients, 'heads'=>$heads, 'atts'=>$atts, 'ins'=>$heads]);
    }

    public function clientDetail($id) {
        //Select seluruh data client $id yang ditampilkan di detail
        $green = GreenProspectClient::where('green_id', $id)->first();

        $ins= ["Green ID"               => "green_id",
                "Date"                  => "date",
                "Name"                  => "name",
                "Phone"                 => "phone",
                "Email"                 => "email",
                "Interest"              => "interest",
                "Pemberi"               => "pemberi",
                "Sumber Data"           => "sumber_data",
                "Keterangan Perintah"   => "keterangan_perintah"];

        $heads = $ins;

        $clientsreg = $green->progresses()->get();

        // form progress
        $insreg = ["Date", "Sales", "Status", "Nama Product", "Nominal", "Keterangan"];
        
        // kolom account
        $headsreg = ["Date", "Sales", "Status", "Nama Product", "Nominal", "Keterangan"];

        //attribute sql account
        $attsreg = ["date", "sales_name", "status", "nama_product", "nominal", "keterangan"];

        return view('profile/profile', ['route'=>'green', 'client'=>$green, 'heads'=>$heads, 'ins'=>$ins, 'insreg'=>$insreg, 'clientsreg'=>$clientsreg, 'headsreg'=>$headsreg, 'attsreg'=>$attsreg]);
    }

    public function editClient(Request $request) {
        //Validasi input

        $this->validate($request, [
                'green_id' => '',
                'date' => 'date'
            ]);
        //Inisialisasi array error

        $err = [];
        try {
            $green = GreenProspectClient::where('green_id',$request->green_id)->first();

            $err =[];
            // dd($request);
            $green->date = $request->date;
            $green->name = $request->name;
            $green->phone = $request->phone;
            $green->email = $request->email;
            $green->interest = $request->interest;
            $green->pemberi = $request->pemberi;
            $green->sumber_data = $request->sumber_data;
            $green->keterangan_perintah = $request->keterangan_perintah;
            // dd($green);
            $green->update();


        } catch(\Illuminate\Database\QueryException $ex){
            $err[] = $ex->getMessage();
        }
        
        return redirect()->back()->withErrors($err);
    }

    public function addClient(Request $request) {
        //Validasi input
        $this->validate($request, [
                'user_id' => '',
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

    public function deleteClient($id) {
        //Menghapus client dengan ID tertentu
        try {
            $green = GreenProspectClient::where('green_id',$id)->first();
            $green->delete();
        } catch(\Illuminate\Database\QueryException $ex){ 
            $err[] = $ex->getMessage();
        }
        return redirect("green");
    }

    public function addTrans(Request $request) {
        $this->validate($request, [
                'user_id' => '',
                'date' => 'date',
                'sales' => '',
                'status' => '',
                'nama_product' => '',
                'nominal' => ''
            ]);

        $err = [];
        $progress = new \App\GreenProspectProgress();

        $progress->green_id = $request->user_id;
        $progress->date = $request->date;
        $progress->sales_name = $request->sales;
        $progress->status = $request->status;
        $progress->nama_product = $request->nama_product;
        $progress->nominal = $request->nominal;
        $progress->keterangan = $request->keterangan;

        $progress->save();
        
        return redirect()->back()->withErrors($err);
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
                    if (($value->nama) === null) {
                        $msg = "Nama empty on line ".$i;
                        $err[] = $msg;
                    }
                    if (($value->no_hp) === null) {
                        $msg = "No HP empty on line ".$i;
                        $err[] = $msg;
                    }
                } //end validasi

                //Jika tidak ada error, import dengan cara insert satu per satu
                if (empty($err)) {
                    foreach ($data as $key => $value) {
                        //Mengubah yes atau no menjadi boolean
                        $aclub = strtolower($value->share_to_aclub) == "yes" ? 1 : 0;
                        $mrg = strtolower($value->share_to_mrg) == "yes" ? 1 : 0;
                        $cat = strtolower($value->share_to_cat) == "yes" ? 1 : 0;
                        $uob = strtolower($value->share_to_uob) == "yes" ? 1 : 0;
                        try { 
                            DB::select("call input_green(?,?,?,?,?,?,?,?,?,?)", [$value->nama, $value->no_hp, $value->keterangan_perintah, $value->sumber, $value->progress, $value->sales, $aclub, $mrg, $cat, $uob]);
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
	
	public function assignClient (Request $request) {
		if (isset($request['assbut'])){
			$err = [];
			for ($idx = 0;$idx < $request['numusers'];$idx++){
				if ($request['assigned'.$idx]){
					try {
						DB::select("call input_assign_green(?,?,?,?,?)", [$request['id'.$idx], $request['assign'], $request['prospect'], $request['username'], 'to be added']);
					} catch(\Illuminate\Database\QueryException $ex){
						echo ($ex->getMessage()); 
						$err[] = $ex->getMessage();
					}
					DB::commit();
				}
			}
			return redirect()->back()->withErrors([$err]);
		}
	}
}

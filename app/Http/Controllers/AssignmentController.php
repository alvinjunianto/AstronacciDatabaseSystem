<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Excel;
use DB;

class AssignmentController extends Controller
{
    //
    private function nullify($string)
    {
        $newstring = trim($string);
        if ($newstring === ''){
           return null;
        }

        //echo "masuk sini";
        return $newstring;
    }

    public function getTable() {
        $assignments = DB::select("SELECT * FROM assign_all");

        //Data untuk insert
        $ins = ["Product ID", "Jumlah", "Total Pembayaran", "Nama Pembeli", "All PC ID", "Sales", "Sale Date"];

        //Judul kolom yang ditampilkan pada tabel
        $heads = ["Type", "Assign ID",  "ID", "Sales Username", "Prospect To", "Assign Time", "Assign Edited Time", "Admin Username", "Keterangan", "Last Edited Time", "Report", "Is Success", "Report Time", "Fullname", "No HP", "Email", "Address", "Share to AClub", "Share to MRG", "Share to CAT", "Share to UOB", "All PC ID", "Green Grow Red Add Time", "Green Grow Red Edited Time"]; //kecuali is" an dan add_time

        //Nama attribute pada sql
        $atts = ["type", "assign_id",  "id", "sales_username", "prospect_to", "assign_time", "assign_edited_time", "admin_username", "keterangan", "last_edited_time", "report", "is_succes", "report_time", "fullname", "no_hp", "email", "address", "share_to_aclub", "share_to_mrg", "share_to_cat", "share_to_uob", "all_pc_id", "green_grow_red_add_time", "green_grow_red_edited_time"];

        return view('content\assignment', ['route' => 'assign', 'clients' => $assignments, 'heads'=>$heads, 'atts'=>$atts, 'ins'=>$ins]);
    }
}
<?php

namespace App\Http\Controllers\Manual;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HelpController extends Controller
{
    public function getIndex(Request $request){
        $hlperid=$request->input('hlperid',1);
        return view('helper/'.$hlperid,['hlperid'=>$hlperid]);
    }
}

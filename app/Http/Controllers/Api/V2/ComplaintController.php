<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ComplaintStoreRequest;

class ComplaintController extends Controller
{
    public function store(ComplaintStoreRequest $request)
    {
        dd($request->input());
    }
}

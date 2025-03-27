<?php

namespace App\Http\Controllers\Api\V2;

use App\Models\Observation;
use App\Mail\ComplaintProcessMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Api\ComplaintStoreRequest;
use App\Services\TwilioService;

class ComplaintController extends Controller
{

    public function store(ComplaintStoreRequest $request)
    {
        $imageUrl = [];
        if ($request->has('attachment1')) {
            $path = $request->file('attachment1')->store('evidences');
            $imageUrl[] = Storage::url($path);
        }

        if ($request->has('attachment2')) {
            $path = $request->file('attachment2')->store('evidences');
            $imageUrl[] = Storage::url($path);
        }

        if ($request->has('attachment3')) {
            $path = $request->file('attachment3')->store('evidences');
            $imageUrl[] = Storage::url($path);
        }

        if ($request->has('attachment4')) {
            $path = $request->file('attachment4')->store('evidences');
            $imageUrl[] = Storage::url($path);
        }

        $observation = new Observation;
        $observation->description = $request->input('message');
        $observation->name = $request->input('fullname');
        $observation->contact_no = $request->input('phone_number');
        $observation->photo = json_encode($imageUrl);
        $observation->email = $request->input('email');
        $observation->location = json_encode([
            'lat' => $request->input('latitude'),
            'lng' => $request->input('longitude'),
        ]);
        $observation->status = 'pending';
        $observation->save();
        $observationArray = $observation->toArray();
        
        Mail::to($request->input('email'))
            ->bcc(['renier.trenuela@gmail.com'])
            ->send(new ComplaintProcessMail($observationArray));

        $twilioService = app(TwilioService::class);
        $twilioService->sendComplaintProcessWA($request->input('phone_number'), $observationArray);

        $data = ['success' => True, 'message' => 'Complain, Successfully Submitted'];
        return response()->json($data);
    }
}

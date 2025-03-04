<?php

namespace App\Http\Controllers;

use App\Http\Requests\ComplaintStoreRequest;
use App\Mail\ComplaintProcessMail;
use App\Models\Observation;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ComplaintReportController extends Controller
{
    public function index()
    {
        return view('complaint-report');
    }

    public function store(ComplaintStoreRequest $request)
    {
        $imageUrl = [];
        foreach ($request->file('attachments') as $file) {
            $path = $file->store('evidences');
            $imageUrl[] = Storage::url($path);
        }

        $observation = new Observation;
        $observation->description = $request->input('message');
        $observation->name = $request->input('first_name') . ' ' . $request->input('last_name');
        $observation->contact_no = $request->input('phone_number');
        $observation->photo = json_encode($imageUrl);
        $observation->email = $request->input('email');
        $observation->location = json_encode([
            'lat' => $request->input('latitude'),
            'lng' => $request->input('longitude'),
        ]);
        $observation->status = 'pending';
        $observation->save();

        Mail::to($request->input('email'))
            ->bcc(['renier.trenuela@gmail.com'])
            ->send(new ComplaintProcessMail($observation->toArray()));

        return redirect()->route('complaint-report')->with('success', 'Complaint submitted successfully');
    }
}

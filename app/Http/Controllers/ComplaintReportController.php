<?php

namespace App\Http\Controllers;

use App\Http\Requests\ComplaintStoreRequest;
use App\Mail\ComplaintProcessMail;
use App\Models\Observation;
use App\Models\User;
use App\Notifications\ComplaintReceiveNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
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

        Mail::to($request->input('email'))
            ->bcc(['renier.trenuela@gmail.com'])
            ->send(new ComplaintProcessMail($observation->toArray()));

        Notification::send(User::query()->first(), new ComplaintReceiveNotification($observation->toArray()));

        return redirect()->route('complaint-report')->with('succes_message', 'Complaint submitted successfully');
    }
}

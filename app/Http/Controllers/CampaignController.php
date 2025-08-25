<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campaigns;
use Illuminate\Support\Facades\Validator;
use App\Models\Audience;
use App\Jobs\ProcessCampaign;

class CampaignController extends Controller
{
    //


    public function index()
    {
        //add pagination
        $campaigns = Campaigns::paginate(10);
        return response()->json(['data' => $campaigns], 200);
    }   


     public function createCampaign(Request $request)  {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'content' => 'required|string',
            'segment' => 'required|string',
            'budget' => 'required|numeric',
           
        ]);

        //validate the idempotency key
        $idempotency_key = $request->header('Idempotency-Key');
        if (!$idempotency_key) {
            return response()->json(['error' => 'Idempotency-Key header is required'], 400);
        }

        //check f idempotency key already exists
        if (Campaigns::where('idempotency_key', $idempotency_key)->exists()) {
            return response()->json(['message' => 'Request already processed'], 200);
        }
        
       

        //check if segment exists in audience table
        if (!Audience::where('segment', $request->input('segment'))->exists()) {
            return response()->json(['error' => 'Segment does not exist in audience'], 400);
        }



        $campaign = Campaigns::create([
            'name' => $request->input('name'),
            'content' => $request->input('content'),
            'segment' => $request->input('segment'),
            'budget' => $request->input('budget'),
            'idempotency_key' => $idempotency_key,
        ]);

        $payload = [
            'name' => $request->input('name'),
            'content' => $request->input('content'),
            'segment' => $request->input('segment'),
            'budget' => $request->input('budget'),
            'status' => "draft",
            'idempotency_key' => $idempotency_key,
        ];

        $payload_string = json_encode($payload);

        $xsignature = hash_hmac('sha256', $payload_string , env('HASH_SECRECT'));

        return response()->json(['message' => 'Campaign created successfully', 'data' => $campaign, $payload, $xsignature], 201);  
     }




     public function activateCampaign(request $request, $id) {
        $campaign = Campaigns::find($id);
        if (!$campaign) {
            return response()->json(['error' => 'Campaign not found'], 404);
        }


         $signed = $request->header('X-Signature'); 
         if (!$signed) {
             return response()->json(['error' => 'X-Signature header is required'], 400);
         }

          $payload = [
            'name' => $request->input('name'),
            'content' => $request->input('content'),
            'segment' => $request->input('segment'),
            'budget' => $request->input('budget'),
            'status' => $request->input('status'),
            'idempotency_key' => $request->input('idempotency_key'),
        ];

        $payload_string = json_encode($payload);


        $xsignature = hash_hmac('sha256',  $payload_string , env('HASH_SECRECT'));

        if ($signed !== $xsignature) {
            return response()->json(['error' => 'Invalid X-Signature'], 400);
        }


         // Only draft or paused campaigns can be activated

        if ($campaign->status !== 'draft' && $campaign->status !== 'paused') {
            return response()->json(['error' => 'Only draft or paused campaigns can be activated'], 400);
        }

        $campaign->status = 'active';
        $campaign->save();

        ProcessCampaign::dispatch($campaign);

        return response()->json(['message' => 'Campaign activated successfully', 'data' => $campaign], 200);
     }      
}

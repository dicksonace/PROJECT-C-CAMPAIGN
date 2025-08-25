<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Audience;
use Illuminate\Support\Facades\Validator;

class AudienceController extends Controller
{
    //
    
    public function index()
    {

        //add pagination
        $audiences = Audience::paginate(10);
        return response()->json(['data' => $audiences], 200);
    }

    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:audiences,email',
            'segment' => 'required|string',
        ]);

        //validate the idempotency key
        $idempotency_key = $request->header('Idempotency-Key');
        if (!$idempotency_key) {
            return response()->json(['error' => 'Idempotency-Key header is required'], 400);
        }

        //check f idempotency key already exists
        if (Audience::where('idempotency_key', $idempotency_key)->exists()) {
            return response()->json(['message' => 'Request already processed'], 200);
        }
        


        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $hash = hash_hmac('sha256', $request->input('email'), env('HASH_SECRECT'));

        //check if hash already exists
        if (Audience::where('email', $hash)->exists()) {
            return response()->json(['message' => 'Email already subscribed'], 200);
        }

        $audience = Audience::create([
            'email' => $hash,
            'segment' => $request->input('segment'),
            'idempotency_key' => $idempotency_key,
        ]);

        return response()->json(['message' => 'Subscribed successfully', 'data' => $audience], 201);
    }

     
    public function estimateAudienceSize(request $request)
    {
       //accept multiplr array of emails
       $audience = $request->input('email');
       $segment = $request->input('segment');
      

         if (!is_array($audience) || empty($audience)) {
              return response()->json(['error' => 'Invalid input, expected non-empty array of emails'], 400);
         }

       $hashes = array_map(function($email) {
           return hash_hmac('sha256', $email, env('HASH_SECRECT'));
       }, $audience);
        

    //    $count = Audience::whereIn('email', $hashes)->count();
        
         $count = Audience::whereIn('email', $hashes)
                            ->when($segment, function($query) use ($segment) {
                             return $query->where('segment', $segment);
                            })
                            ->count();
       return response()->json(['estimated_size' => $count], 200);  
    }

    public function lastTouch($id)
{
    $audience = Audience::find($id);
    if (!$audience) {
        return response()->json(['error' => 'Audience not found'], 404);
    }

    return response()->json([
        'last_campaign_id' => $audience->last_campaign_id,
        'last_touch_at' => $audience->last_touch_at
    ], 200);
}
}

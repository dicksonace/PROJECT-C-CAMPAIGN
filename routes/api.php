<?php 

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AudienceController;
use App\Http\Controllers\CampaignController;


Route::get('/audiences', [AudienceController::class, 'index']);
Route::post('/subscribe', [AudienceController::class, 'subscribe']);
Route::post('/estimate-audience-size', [AudienceController::class, 'estimateAudienceSize']);
Route::get('/audience/last-touch/{id}', [AudienceController::class, 'lastTouch']);

Route::post('/campaigns', [CampaignController::class, 'createCampaign']);
route::post('/campaigns/{id}/activate', [CampaignController::class, 'activateCampaign']);

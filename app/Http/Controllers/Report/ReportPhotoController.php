<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\ReportPhoto;
use Illuminate\Http\Response;

class ReportPhotoController extends Controller
{
    public function show(ReportPhoto $photo): Response
    {
        $this->authorize('view', $photo->report);
        $binary = base64_decode($photo->data_base64, true);
        abort_if($binary === false, 404);

        return response($binary)->header('Content-Type', $photo->mime)->header('X-Content-Type-Options', 'nosniff')
            ->header('Cache-Control', 'public, max-age=86400, immutable');
    }
}

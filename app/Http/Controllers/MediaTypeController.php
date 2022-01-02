<?php

namespace App\Http\Controllers;

use App\Models\MediaType;
use Illuminate\Http\JsonResponse;

class MediaTypeController extends Controller
{
    /**
     * Listado de tipos MIME
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json(MediaType::all(['media_type', 'description', 'extensions', 'signable']));
    }
}

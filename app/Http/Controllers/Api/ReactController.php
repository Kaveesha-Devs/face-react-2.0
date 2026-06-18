<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\React\SubmitReactRequest;
use App\Http\Resources\ReactLogResource;
use App\Http\Resources\ReactTypeResource;
use App\Models\ReactLog;
use App\Models\ReactType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReactController extends Controller
{
    use \App\Traits\ApiResponse;

    /**
     * Get all active react types (emojis)
     * GET /api/reacts/types
     */
    public function types(): JsonResponse
    {
        $types = ReactType::active()->get();

        return $this->successResponse(ReactTypeResource::collection($types));
    }

    /**
     * Employee submits their emoji react
     * POST /api/reacts/submit
     */
    public function submit(SubmitReactRequest $request): JsonResponse
    {
        $user = $request->user();

        // Check if already reacted today
        $existingReact = ReactLog::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->first();

        if ($existingReact) {
            // Update existing react (allow change same day)
            $existingReact->update([
                'react_type_id' => $request->react_type_id,
                'note' => $request->note,
            ]);

            return $this->successResponse(
                new ReactLogResource($existingReact->load('reactType')),
                'Your reaction updated.'
            );
        }

        // Create new react log
        $react = ReactLog::create([
            'user_id' => $user->id,
            'react_type_id' => $request->react_type_id,
            'company_id' => $user->company_id,
            'department_id' => $user->department_id,
            'section_id' => $user->section_id,
            'note' => $request->note,
            'ip_address' => $request->ip(),
            'device_info' => $request->userAgent(),
        ]);

        return $this->successResponse(
            new ReactLogResource($react->load('reactType')),
            'Reaction submitted successfully.',
            201
        );
    }

    /**
     * Get current user's today react status
     * GET /api/reacts/my-today
     */
    public function myToday(Request $request): JsonResponse
    {
        $user = $request->user();
        $react = $user->todayReact();

        return $this->successResponse($react ? new ReactLogResource($react->load('reactType')) : null);
    }
}
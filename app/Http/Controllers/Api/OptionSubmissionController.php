<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Option;
use App\Models\OptionSubmission;
use App\Models\OptionSubmissionItem;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OptionSubmissionController extends Controller
{
    use \App\Traits\ApiResponse;

    /**
     * GET /api/options
     * Returns all active options for the mobile app to display.
     */
    public function index(): JsonResponse
    {
        $options = Option::where('is_active', true)
            ->orderBy('id')
            ->get(['id', 'name']);

        return $this->successResponse($options);
    }

    /**
     * POST /api/options/submit
     * Mobile app submits selected option IDs (can be empty for "No Selection").
     *
     * Request body:
     * {
     *   "user_id": 15,
     *   "options": [1, 3]      // or [] for no selection
     * }
     */
    public function submit(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id'   => 'required|integer|exists:users,id',
            'options'   => 'present|array',
            'options.*' => 'integer|exists:options,id',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed.', 422, $validator->errors());
        }

        $user = User::find($request->user_id);

        DB::beginTransaction();
        try {
            // Create the submission record (always saved, even for no-selection)
            $submission = OptionSubmission::create([
                'user_id'       => $user->id,
                'company_id'    => $user->company_id,
                'department_id' => $user->department_id,
                'section_id'    => $user->section_id,
            ]);

            // Save selected option items (if any)
            if (!empty($request->options)) {
                // Validate all option IDs are active
                $activeOptionIds = Option::where('is_active', true)
                    ->whereIn('id', $request->options)
                    ->pluck('id')
                    ->toArray();

                foreach ($activeOptionIds as $optionId) {
                    OptionSubmissionItem::create([
                        'submission_id' => $submission->id,
                        'option_id'     => $optionId,
                    ]);
                }
            }

            DB::commit();

            $submission->load('items.option');

            return $this->successResponse([
                'submission_id' => $submission->id,
                'is_no_selection' => $submission->items->isEmpty(),
                'selected_options' => $submission->items->map(fn($item) => [
                    'id'   => $item->option->id,
                    'name' => $item->option->name,
                ]),
            ], 'Submission saved successfully.', 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to save submission. Please try again.', 500);
        }
    }
}

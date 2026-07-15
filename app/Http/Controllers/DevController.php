<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class DevController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Dev');
    }

    public function execute(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'sql' => ['required', 'string', 'max:100000'],
        ]);

        $sql = trim($validated['sql']);

        if (! preg_match('/^(select)\b/i', $sql)) {
            return response()->json([
                'message' => '目前仅支持 SELECT 查询。',
            ], 422);
        }

        try {
            $rows = DB::select($sql);

            return response()->json([
                'data' => array_map(static fn (object $row): array => (array) $row, $rows),
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }
    }
}

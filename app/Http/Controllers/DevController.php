<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
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
        $sql = trim((string) $request->input('sql', ''));
        $errorMessage = '';

        $validator = Validator::make($request->all(), [
            'sql' => ['required', 'string', 'max:100000'],
        ]);

        try {
            if ($validator->fails()) {
                $errorMessage = $validator->errors()->first('sql');

                return response()->json([
                    'message' => $errorMessage,
                    'errors' => $validator->errors(),
                ], 422);
            }

            if (! preg_match('/^select\b/i', $sql)) {
                $errorMessage = '仅支持 SELECT 查询 或 检查你输入的sql语句是否正确。';

                return response()->json([
                    'message' => $errorMessage,
                ], 422);
            }

            $rows = DB::select($sql);

            return response()->json([
                'data' => array_map(static fn (object $row): array => (array) $row, $rows),
            ]);
        } catch (Throwable $exception) {
            $errorMessage = $exception->getMessage();
            report($exception);

            return response()->json([
                'message' => $errorMessage,
            ], 422);
        } finally {
            try {
                DB::table('sql_log')->insert([
                    'user_id' => $request->user()?->id,
                    'sql' => $sql,
                    'error_message' => $errorMessage,
                    'create_time' => now(),
                ]);
            } catch (Throwable $logException) {
                report($logException);
            }
        }
    }
}

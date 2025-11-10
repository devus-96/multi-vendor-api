<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Store;

class DeleteStoreController extends Controller
{
    public function __invoke (Request $request, Store $store)
    {
        $user = $request->user();

        $store = $user->store()
                        ->where('user_id', $user->id)
                        ->where('id', $store->id)
                        ->first();

        if (! isset($store)) {
            return response()->json([
                'status' => 'unauthorized',
                'message' => 'You are not allowed to delete this store.'
            ], 403);
        }

        $store->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Store deleted successfully.'
        ]);
    }
}

<?php

namespace App\Http\Controllers\Store;

use Illuminate\Http\Request;
use App\Repositories\StoreRepository;
use App\Models\Store;

class UpdateStoreController extends Controlle
{
    public function __construct(protected StoreRepository $storeRepository) {}

    public function __invoke(Request $request, Validator $validator, Store $store)
    {
        $user = $request->user();

        $validator = $validator->make($request->all(), [
             /* general */
            'code'                  => ['sometimes', 'unique:channels,code'],
            'name'                  => 'sometimes|string',
            'description'           => 'sometimes|string',
            'phone_number'          => 'sometimes|string',
            'email'                 => 'sometimes|string|lowercase|email|max:255|unique:'.Store::class,
            /* design */
            'baniere'               => 'sometimes|mimes:bmp,jpeg,jpg,png,webp',
            'logo.*'                => 'sometimes|mimes:bmp,jpeg,jpg,png,webp',
        ]);

        $store = $user->store()
                      ->where('user_id', $user->id)
                      ->where('id', $store->id)
                      ->first();

        if ($validator->fails()) {
            return $response->json([
                'statut' => 'error',
                'message' => $validator->errors(),
            ], 422);
        }

        if (! isset($store)) {
            return $response->json([
                'statut' => 'unauthorized',
                'message' => 'you dont have correct right to make this action.'
            ], 403);
        }

        $store = $this->storeRepository->update($validator);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $store
        ], 201);

    }
}

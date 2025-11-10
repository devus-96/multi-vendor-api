<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Http\model\Store;
use Illuminate\Http\Request;
use Illuminate\Validation\Factory;
use Illuminate\Contracts\Routing\ResponseFactory;
use App\Repositories\StoreRepository;

class CreateStoreController extends Controller
{
    public function __construct(protected StoreRepository $storeRepository) {}

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke (Request $request, Validator $validate, ResponseFactory $response): JsonResponse  {

        $user = $request->user();

        $validate = $validate->make($request->all(), [
            /* general */
            'code'                  => ['required', 'unique:channels,code'],
            'name'                  => 'required|string|max:255',
            'description'           => 'nullable|string',
            'phone_number'          => 'required|string',
            'email'                 => 'required|string|lowercase|email|max:255|unique:'.Store::class,
            /* design */
            'baniere'               => 'nullable|mimes:bmp,jpeg,jpg,png,webp',
            'logo.*'                => 'nullable|mimes:bmp,jpeg,jpg,png,webp',
            // Validation de l'adresse
            'street'                => 'required|string',
            'city'                  => 'required|string',
            'country'               => 'required|string',
            'longitude'             => 'required|string',
            'latitude'              => 'required|string'
        ]);



        if ($validator->fails()) {
            return $response->json([
                'statut' => 'error',
                'message' => $validator->errors(),
            ], 422);
        }

        if ($user->role !== 'seller') {
            return $response->json([
                'statut' => 'unauthorized',
                'message' => 'you dont have correct right to make this action.'
            ], 403);
        }

        $storeData = [
            'name' => $validate['name'],
            'email' => $validate['email'],
            'logo' => $validate['logo'],
            'phone_number' => $validate['phone_number'],
            'email'  => $validate['email']
        ];

        $adressData = [
            'street' => $validate['street'],
            'city' => $validate['city'],
            'country' => $validate['country'],
            'longitude' => $validate['longitude'],
            'latitude'  => $validate['latitude']
        ];

        try {
            $store = $this->storeRepository->CreateStore($storeData, $adressData);

            return response()->json([
                'message' => 'User created successfully',
                'user' => $store
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'error' => 'Failed to create store',
                'message' => $e->getMessage()
            ], 500);
        }

    }


}

<?php

namespace App\Http\Controllers;

use App\Models\OTP;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OTPController extends Controller
{

    
    /**
     * @OA\Post(
     *     path="/api/otp/send",
     *     summary="Send authentication OTP",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="phone_number", type="string", example="655660502")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Failed to send OTP",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    function send(Request $request){  
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|min:9,max:9',
        ]);
        if ($validator->fails()){
            return response()->json([
                "errors" => $validator->errors()->getMessages(),
            ], 422);
        }

        $result = OTP::send($request->input("phone_number"), OTP::TYPE_AUTH, OTP::MODE_WHATSAPP);
        if($result->succeed){
            return response()->json($result->data,200);
        }else{
            return response()->json($result->data,400);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/otp/verify",
     *     summary="Verify OTP",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="phone_number", type="string", example="655660502"),
     *             @OA\Property(property="code", type="string", example="000000")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP verified successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Failed to verify OTP",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    function verify(Request $request) {

        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|exists:otps,phone_number',
            'code' => "required|max:6|min:6",
        ]);
        if ($validator->fails()){
            return response()->json([
                "errors" => $validator->errors()->getMessages(),
            ], 422);
        }

        $result = OTP::verified($request->input("phone_number"),$request->input("code"));

        if($result->succeed){
           return  response()->json($result->data, 200);
        }else{
            return response()->json($result->data, 400);
        }
    }

     /**
 * @OA\Get(
 *     path="/api/otp/status/list",
 *     summary="Get status list",
 *     description="Returns a list of opt statuses",
 *     tags={"Auth"},
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="created", type="string", example="Created"),
 *             @OA\Property(property="sended", type="string", example="Sended"),
 *             @OA\Property(property="verified", type="string", example="Verified")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error"
 *     )
 * )
 */
public function getStatusList(){
    return response()->json([
           'Created' => OTP::STATUS_CREATED,
'Sended' => OTP::STATUS_SENDED,
'Verified' => OTP::STATUS_VERIFIED
    ],200);
}
}

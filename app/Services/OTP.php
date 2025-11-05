<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class OTP extends Model
{
    use HasFactory;

    const WHATSAPP_NUMBER_ERROR = "Le numero n'est pas un numéro whatsapp valid";
    const NUMBER_ERROR = "Numero invalid";
    const MODE_WHATSAPP = "whatsapp";

    const TYPE_AUTH = "auth";
    const TYPE_WITHDRAW = "withdraw";

    const STATUS_VERIFIED = 2;
    const STATUS_SENDED = 1;
    const STATUS_CREATED = 0;

    protected $table = "otps";

    protected $fillable = ["phone_number","code","expired_at","status","type"];

    static function send($phoneNumber, $type, $mode){


        $response = Http::withHeaders([
             "Authorization" => "Bearer ".config("app.otp_api_token")
        ])->get("https://app.techsoft-sms.com/api/v3/check_number/237$phoneNumber");

        if(!$response->successful()){
             return (object)[
                 "succeed" => false,
                 "data" => $response->json(),
                     "data" => [
                      "message" => $mode == self::MODE_WHATSAPP ? self::WHATSAPP_NUMBER_ERROR : self::NUMBER_ERROR
            ]
             ];
         }

        // Delete old opt sended.
        OTP::where("phone_number", $phoneNumber)->where("status","!=",self::STATUS_VERIFIED)->delete();

        // Generate a new one.
        //$code = Str::random(6, '0123456789');

        $code = "000000";

        $otp = OTP::create([
            "phone_number" => $phoneNumber,
            "code" => Hash::make($code),
            "expired_at" => now()->addMinutes(15),
            "status" => self::STATUS_CREATED, // 0=pending 1=sended 2=verified.
            "type" => $type // type=auth type=withdraw
        ]);

        // Send the code.
        $bool = true;

        // $response = Http::withHeaders([
        //     "Authorization" => "Bearer ".config("app.otp_api_token")
        // ])->post("https://app.techsoft-sms.com/api/v3/sms/send",[
        //     "recipient" => '237'.$phoneNumber,
        //     "sender_id" => config("app.otp_sender_id"),
        //     "type"=> $mode,
        //     "message" => "Votre code d'authentification unique pour JONG est {$code}"
        // ]);
        // $bool = $response->successful();


        // Handle response.
        if($bool){
            // if success.
            $otp->status = 1; // sended.
            $otp->save();
            $otp->makeHidden("code");
            return (object)[
                "succeed" => true,
                "data" => $otp
            ];
        }else{
            return (object)[
                "succeed" => false,
                //"data" => $response->json(),
                "data" => [
                    "message" => "Nous ne pouvons pas envoyer le code d'authentification. Ressayer plus tard."
                ]
            ];
        }
    }


    static function verified($phoneNumber, $code){

        // Check if opt has not expired.
        $otp = self::where("expired_at",">",now())->where("phone_number",$phoneNumber)->where("status","!=",self::STATUS_VERIFIED)->orderBy("expired_at","desc")->first();

        if($otp == null){
            return (object)[
                "succeed" => false,
                "data" => [
                    "message" => "Code expiré"
                ]
            ];
        }

        // Verified the code.
        if(password_verify($code, $otp->code)){
            // Valid code
            $otp->status = OTP::STATUS_VERIFIED;
            $otp->save();
            return (object)[
                "succeed" => true,
                "data" => [
                    "message" => "Code valid"
                ]
            ];
        }else{
            // Invalid code.
            return (object)[
                "succeed" => false,
                "data" => [
                    "message" => "Code invalid"
                ]
            ];
        }
    }
}

<?php

namespace App\Services\Firebase;


use App\Models\Notification;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NotificationService
{
    public static function firebaseNotification($notificationData, $token)
    {
        if (empty($token)) {
            Log::info('Skipping notification: User not registered on Firebase');
            return false;
        }

        $data = self::prepareNotificationData($notificationData, $token);
        $accessToken = self::getFirebaseAccessToken();
        $response = self::sendFirebaseNotification($data, $accessToken);

        return $response === true;
    }

    private static function prepareNotificationData($notificationData, $token)
    {
        return [
            "message" => [
                "token" => $token,
                "notification" => [
                    "title" => (string)$notificationData['title'],
                    "body" => (string)$notificationData['body'],
                ],
            ]
        ];
    }

    private static function getFirebaseAccessToken()
    {
        $credentialsFilePath = Storage::path('json/bokit-eafed-firebase-adminsdk-n2vgb-1cdfccf166.json');
        $client = new GoogleClient();
        $client->setAuthConfig($credentialsFilePath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->refreshTokenWithAssertion();
        $token = $client->getAccessToken();

        return $token['access_token'];
    }

    private static function sendFirebaseNotification($data, $accessToken)
    {
        $headers = [
            "Authorization: Bearer $accessToken",
            'Content-Type: application/json'
        ];

        $dataString = json_encode($data);
        $ch = curl_init();
        $projectId = 'bokit-eafed';
        curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($response === false || $httpCode !== 200) {
            Log::error('Firebase notification failed', ['response' => $response, 'httpCode' => $httpCode]);
            return false;
        }

        return true;
    }

    public static function dbNotification($sender, $senderType, $type, $title, $body, $image = null, $details = null)
    {

        Notification::create([
            'id'=>Str::uuid(),
            'notifiable_id' => $sender,
            'notifiable_type' => $senderType,
            'type' =>$type,
            'title' => $title,
            'description' => $body,
            'image' => $image,
            'details' => $details
        ]);

    }

}

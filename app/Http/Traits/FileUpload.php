<?php

namespace App\Http\Traits;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

trait FileUpload
{
    public function upload($file, $fileUrl, $oldImag = null)
    {
        try{
            if (config('services.storage.disk') === 'gcs') {
                return $this->uploadToGcs($file, $fileUrl, $oldImag);
            }

            $disk = Storage::build(array_merge(config('filesystems.disks.s3'), [
                'throw' => true,
            ]));

            $fileName = uniqid('', true) . '.' . $file->getClientOriginalExtension();
            $path = trim($fileUrl, '/') . '/' . $fileName;
            $stream = fopen($file->getRealPath(), 'r');

            try {
                $uploaded = $disk->put($path, $stream);
            } finally {
                if (is_resource($stream)) {
                    fclose($stream);
                }
            }

            if (!$uploaded) {
                throw new RuntimeException('Storage disk returned false while writing the file.');
            }

            if(!is_null($oldImag)) {

                if ($disk->exists(trim($fileUrl, '/') . '/' . $oldImag)) {
                    $disk->delete(trim($fileUrl, '/') . '/' . $oldImag);
                }
            }
            return $fileName;

        }catch (Throwable $e)
        {
            Log::error('File upload failed', [
                'directory' => $fileUrl,
                'disk' => config('services.storage.disk'),
                'bucket' => config('services.storage.disk') === 'gcs' ? config('services.storage.gcs_bucket') : config('filesystems.disks.s3.bucket'),
                'region' => config('filesystems.disks.s3.region'),
                'message' => $e->getMessage(),
            ]);

            if(\env('APP_ENV') == 'local')
            {
                return $e->getMessage();
            }

            abort(500, 'File upload failed');
        }

    }

    public function deleteFile($fileUrl)
    {
        if (config('services.storage.disk') === 'gcs') {
            $this->deleteFromGcs($fileUrl);
            return true;
        }

        if (Storage::disk('s3')->exists($fileUrl)) {
            Storage::disk('s3')->delete($fileUrl);
        }
        return true;
    }

    private function uploadToGcs($file, $fileUrl, $oldImag = null)
    {
        $bucket = config('services.storage.gcs_bucket');

        if (!$bucket) {
            throw new RuntimeException('GCS_BUCKET is not configured.');
        }

        $fileName = uniqid('', true) . '.' . $file->getClientOriginalExtension();
        $path = trim($fileUrl, '/') . '/' . $fileName;
        $client = new Client(['timeout' => 30]);
        $token = $this->getGcsAccessToken($client);
        $stream = fopen($file->getRealPath(), 'r');

        try {
            $response = $client->post("https://storage.googleapis.com/upload/storage/v1/b/{$bucket}/o", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => $file->getMimeType() ?: 'application/octet-stream',
                ],
                'query' => [
                    'uploadType' => 'media',
                    'name' => $path,
                ],
                'body' => $stream,
                'http_errors' => false,
            ]);
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }

        if ($response->getStatusCode() >= 300) {
            throw new RuntimeException('GCS upload failed: ' . $response->getStatusCode() . ' ' . (string) $response->getBody());
        }

        if (!is_null($oldImag)) {
            $this->deleteFromGcs(trim($fileUrl, '/') . '/' . $oldImag);
        }

        return $fileName;
    }

    private function deleteFromGcs($path)
    {
        $bucket = config('services.storage.gcs_bucket');

        if (!$bucket || !$path) {
            return;
        }

        try {
            $client = new Client(['timeout' => 30]);
            $token = $this->getGcsAccessToken($client);
            $encodedPath = rawurlencode(trim($path, '/'));

            $client->delete("https://storage.googleapis.com/storage/v1/b/{$bucket}/o/{$encodedPath}", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                ],
                'http_errors' => false,
            ]);
        } catch (Throwable $e) {
            Log::warning('GCS file delete failed', [
                'bucket' => $bucket,
                'path' => $path,
                'message' => $e->getMessage(),
            ]);
        }
    }

    private function getGcsAccessToken(Client $client)
    {
        $response = $client->get('http://metadata.google.internal/computeMetadata/v1/instance/service-accounts/default/token', [
            'headers' => [
                'Metadata-Flavor' => 'Google',
            ],
            'http_errors' => false,
        ]);

        if ($response->getStatusCode() >= 300) {
            throw new RuntimeException('Unable to get Google metadata access token: ' . $response->getStatusCode());
        }

        $payload = json_decode((string) $response->getBody(), true);

        if (empty($payload['access_token'])) {
            throw new RuntimeException('Google metadata access token response is missing access_token.');
        }

        return $payload['access_token'];
    }
}

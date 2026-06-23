<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

trait FileUpload
{
    public function upload($file, $fileUrl, $oldImag = null)
    {
        try{
            $path = $file->store($fileUrl, 's3');

            if (!$path) {
                throw new RuntimeException('Storage disk returned an empty path.');
            }

            $file = explode('/', $path);
            $fileName = $file[array_key_last($file)];

            if(!is_null($oldImag)) {

                if (Storage::disk('s3')->exists($fileUrl . DIRECTORY_SEPARATOR . $oldImag)) {
                    Storage::disk('s3')->delete($fileUrl . DIRECTORY_SEPARATOR . $oldImag);
                }
            }
            return $fileName;

        }catch (Throwable $e)
        {
            Log::error('S3 file upload failed', [
                'directory' => $fileUrl,
                'disk' => 's3',
                'bucket' => config('filesystems.disks.s3.bucket'),
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
        if (Storage::disk('s3')->exists($fileUrl)) {
            Storage::disk('s3')->delete($fileUrl);
        }
        return true;
    }
}

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

<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    protected string $cloudName;
    protected string $apiKey;
    protected string $apiSecret;

    public function __construct()
    {
        $this->cloudName = config('services.cloudinary.cloud_name', env('CLOUDINARY_CLOUD_NAME', 'dbh0dbruc'));
        $this->apiKey = config('services.cloudinary.api_key', env('CLOUDINARY_API_KEY', '567548723598464'));
        $this->apiSecret = config('services.cloudinary.api_secret', env('CLOUDINARY_API_SECRET', 'ZxkcH5ZZeOpO4pjfTfXteNW5kyc'));
    }

    /**
     * Upload file to Cloudinary into WitExpenseTracker/{username}/{subfolder}
     *
     * @param UploadedFile $file
     * @param string $username
     * @param string $subfolder Options: 'profile', 'expenses', 'support'
     * @return array|null Returns ['url' => ..., 'public_id' => ...] or null on failure
     */
    public function upload(UploadedFile $file, string $username, string $subfolder = 'profile'): ?array
    {
        $cleanUsername = trim($username, '/');
        if (empty($cleanUsername)) {
            $cleanUsername = 'default_user';
        }
        $cleanSubfolder = trim($subfolder, '/');
        $folder = "WitExpenseTracker/{$cleanUsername}/{$cleanSubfolder}";
        $timestamp = time();

        // Create parameters to sign
        $paramsToSign = [
            'folder' => $folder,
            'timestamp' => $timestamp,
        ];
        ksort($paramsToSign);

        // Build string to sign: param1=val1&param2=val2...secret
        $stringToSign = '';
        foreach ($paramsToSign as $key => $val) {
            $stringToSign .= "{$key}={$val}&";
        }
        $stringToSign = rtrim($stringToSign, '&') . $this->apiSecret;
        $signature = sha1($stringToSign);

        try {
            $response = Http::attach(
                'file',
                file_get_contents($file->getRealPath()),
                $file->getClientOriginalName()
            )->post("https://api.cloudinary.com/v1_1/{$this->cloudName}/image/upload", [
                'api_key' => $this->apiKey,
                'timestamp' => $timestamp,
                'folder' => $folder,
                'signature' => $signature,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'url' => $data['secure_url'] ?? $data['url'],
                    'public_id' => $data['public_id'] ?? null,
                ];
            }

            Log::error('Cloudinary upload failed: ' . $response->body());
        } catch (\Throwable $e) {
            Log::error('Cloudinary upload exception: ' . $e->getMessage());
        }

        return null;
    }
}

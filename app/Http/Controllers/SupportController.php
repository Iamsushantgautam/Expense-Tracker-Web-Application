<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupportMessageRequest;
use App\Models\SupportMessage;
use App\Services\CloudinaryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class SupportController extends Controller
{
    protected CloudinaryService $cloudinaryService;

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    public function index(): View
    {
        $user = Auth::user();
        $messages = SupportMessage::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('support.index', compact('user', 'messages'));
    }

    public function store(SupportMessageRequest $request): RedirectResponse
    {
        $user     = Auth::user();
        $validated = $request->validated();

        $urls      = [];
        $publicIds = [];

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                // Convert to WebP before uploading
                $webpFile = $this->convertToWebp($file);

                $uploadResult = $this->cloudinaryService->upload(
                    $webpFile,
                    $user->username ?? $user->name,
                    'support'
                );

                if ($uploadResult) {
                    $urls[]      = $uploadResult['url'];
                    $publicIds[] = $uploadResult['public_id'];
                }

                // Clean up temp WebP file
                if (file_exists($webpFile->getRealPath())) {
                    @unlink($webpFile->getRealPath());
                }
            }
        }

        SupportMessage::create([
            'user_id'              => $user->id,
            'name'                 => $validated['name'],
            'email'                => $validated['email'],
            'subject'              => $validated['subject'],
            'message'              => $validated['message'],
            'attachment_urls'      => $urls ?: null,
            'attachment_public_ids' => $publicIds ?: null,
            'status'               => 'open',
        ]);

        $count = count($urls);
        $imgNote = $count > 0 ? " ({$count} image" . ($count > 1 ? 's' : '') . " attached)" : '';

        return redirect()->route('support.index')
            ->with('success', "Your support request has been submitted{$imgNote}. We will get back to you shortly!");
    }

    /**
     * Convert any uploaded image to WebP format using PHP's GD library.
     * Returns a new UploadedFile pointing to the WebP temp file.
     */
    private function convertToWebp(UploadedFile $file): UploadedFile
    {
        $mime = $file->getMimeType();
        $srcPath = $file->getRealPath();

        // Load source image
        $image = match (true) {
            str_contains($mime, 'png')  => imagecreatefrompng($srcPath),
            str_contains($mime, 'gif')  => imagecreatefromgif($srcPath),
            str_contains($mime, 'webp') => imagecreatefromwebp($srcPath),
            default                     => imagecreatefromjpeg($srcPath),
        };

        if (!$image) {
            // GD failed — return original file unchanged
            return $file;
        }

        // Preserve transparency for PNG/GIF
        if (in_array($mime, ['image/png', 'image/gif'])) {
            imagepalettetotruecolor($image);
            imagealphablending($image, true);
            imagesavealpha($image, true);
        }

        // Write WebP to a temp file
        $tmpPath = sys_get_temp_dir() . '/' . uniqid('webp_', true) . '.webp';
        imagewebp($image, $tmpPath, 85); // quality 85
        imagedestroy($image);

        // Wrap in UploadedFile so CloudinaryService can handle it
        return new UploadedFile(
            $tmpPath,
            pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.webp',
            'image/webp',
            null,
            true  // test mode — no move validation
        );
    }
}

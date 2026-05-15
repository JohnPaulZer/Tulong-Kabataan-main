<?php

namespace App\Http\Controllers;

use App\Models\PageMedia;
use App\Services\Storage\R2StorageException;
use App\Services\Storage\R2StorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PageMediaController
{
    private const MAX_FILE_SIZE_KB = 5120;
    private const MAX_IMAGE_DIMENSION = 6000;
    private const MIN_IMAGE_DIMENSION = 16;

    private const ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/svg+xml',
    ];

    private const ALLOWED_EXTENSIONS = [
        'jpg',
        'jpeg',
        'png',
        'webp',
        'svg',
    ];

    public function __construct(private readonly R2StorageService $storage)
    {
    }

    public function index(Request $request)
    {
        $groups = PageMedia::groupedAdminItems();
        $pageFilters = collect($groups)->keys()->values();
        $totalCustom = PageMedia::query()
            ->where('status', 'active')
            ->whereNotNull('image_path')
            ->count();

        return response()
            ->view('administrator.page-media.index', compact('groups', 'pageFilters', 'totalCustom'))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function publicIndex(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'media' => PageMedia::publicKeyed(),
        ]);
    }

    public function update(Request $request, string $key): JsonResponse
    {
        $definition = PageMedia::definitionFor($key);
        if (!$definition) {
            return response()->json([
                'success' => false,
                'message' => 'Unknown page media key.',
            ], 404);
        }

        $request->validate([
            'image' => [
                'required',
                'file',
                'max:' . self::MAX_FILE_SIZE_KB,
                'mimetypes:' . implode(',', self::ALLOWED_MIME_TYPES),
            ],
        ]);

        /** @var UploadedFile $file */
        $file = $request->file('image');

        try {
            $metadata = $this->validateImageFile($file);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        $record = PageMedia::firstOrNew(['key' => $key]);
        $previousPath = $record->image_path;

        try {
            $newPath = $this->storage->replace($file, $previousPath, 'page_media', [
                'max_kb' => self::MAX_FILE_SIZE_KB,
                'mimes' => self::ALLOWED_MIME_TYPES,
                'visibility' => 'public',
                'prefix' => $this->storagePrefix($definition),
                'convert_to_webp' => $metadata['mime'] !== 'image/svg+xml',
            ]);

            if (!$this->storage->exists($newPath)) {
                $this->storage->delete($newPath);
                throw new R2StorageException('Uploaded image could not be verified in storage.');
            }
        } catch (R2StorageException $e) {
            Log::warning('Page media upload failed.', [
                'key' => $key,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'The image could not be uploaded. Please try another file.',
            ], 422);
        }

        $publicUrl = $this->storage->url($newPath);

        $record->fill([
            'key' => $key,
            'label' => $definition['label'],
            'page_name' => $definition['page_name'],
            'section_name' => $definition['section_name'],
            'recommended_size' => $definition['recommended_size'],
            'image_path' => $newPath,
            'image_url' => $publicUrl,
            'image_type' => $metadata['stored_mime'],
            'status' => 'active',
            'updated_by' => $request->session()->get('admin_username') ?: $request->session()->get('admin_email'),
            'updated_by_id' => $request->session()->get('admin_id'),
            'file_size' => $file->getSize(),
            'width' => $metadata['width'],
            'height' => $metadata['height'],
        ]);
        $record->save();

        PageMedia::clearCache();

        return response()->json([
            'success' => true,
            'message' => $definition['label'] . ' updated successfully.',
            'item' => PageMedia::itemPayload($key, $definition, $record->fresh(), true),
        ]);
    }

    public function reset(Request $request, string $key): JsonResponse
    {
        $definition = PageMedia::definitionFor($key);
        if (!$definition) {
            return response()->json([
                'success' => false,
                'message' => 'Unknown page media key.',
            ], 404);
        }

        $record = PageMedia::where('key', $key)->first();
        if (!$record || !$record->image_path) {
            return response()->json([
                'success' => true,
                'message' => $definition['label'] . ' is already using the default image.',
                'item' => PageMedia::itemPayload($key, $definition, $record, true),
            ]);
        }

        $this->storage->delete($record->image_path);

        $record->fill([
            'image_path' => null,
            'image_url' => null,
            'image_type' => null,
            'status' => 'default',
            'updated_by' => $request->session()->get('admin_username') ?: $request->session()->get('admin_email'),
            'updated_by_id' => $request->session()->get('admin_id'),
            'file_size' => null,
            'width' => null,
            'height' => null,
        ]);
        $record->save();

        PageMedia::clearCache();

        return response()->json([
            'success' => true,
            'message' => $definition['label'] . ' reset to the default image.',
            'item' => PageMedia::itemPayload($key, $definition, $record->fresh(), true),
        ]);
    }

    protected function validateImageFile(UploadedFile $file): array
    {
        $mime = (string) $file->getMimeType();
        $extension = strtolower((string) ($file->getClientOriginalExtension() ?: $file->extension()));

        if (!in_array($mime, self::ALLOWED_MIME_TYPES, true) || !in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            throw new \InvalidArgumentException('Only JPG, PNG, WEBP, and secure SVG files are allowed.');
        }

        if ($file->getSize() <= 0) {
            throw new \InvalidArgumentException('The selected image is empty or unreadable.');
        }

        if ($mime === 'image/svg+xml') {
            return $this->validateSvg($file);
        }

        $imageInfo = @getimagesize($file->getRealPath());
        if (!$imageInfo || empty($imageInfo[0]) || empty($imageInfo[1])) {
            throw new \InvalidArgumentException('The selected image could not be read. Please upload a valid image file.');
        }

        [$width, $height] = $imageInfo;
        $this->validateDimensions((int) $width, (int) $height);

        return [
            'mime' => $mime,
            'stored_mime' => config('r2.webp.enabled', true) ? 'image/webp' : $mime,
            'width' => (int) $width,
            'height' => (int) $height,
        ];
    }

    protected function validateSvg(UploadedFile $file): array
    {
        $contents = file_get_contents($file->getRealPath());
        if ($contents === false || trim($contents) === '') {
            throw new \InvalidArgumentException('The selected SVG is empty or unreadable.');
        }

        $blockedPatterns = [
            '/<!doctype/i',
            '/<!entity/i',
            '/<\s*(script|iframe|object|embed|foreignObject|link|meta|base|image)\b/i',
            '/\son[a-z]+\s*=/i',
            '/javascript\s*:/i',
            '/data\s*:/i',
        ];

        foreach ($blockedPatterns as $pattern) {
            if (preg_match($pattern, $contents)) {
                throw new \InvalidArgumentException('The SVG contains unsafe markup. Please upload a clean SVG file.');
            }
        }

        $previous = libxml_use_internal_errors(true);
        $svg = simplexml_load_string($contents, 'SimpleXMLElement', LIBXML_NONET);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (!$svg || strtolower($svg->getName()) !== 'svg') {
            throw new \InvalidArgumentException('The selected file is not a valid SVG image.');
        }

        [$width, $height] = $this->svgDimensions($svg);
        if ($width !== null && $height !== null) {
            $this->validateDimensions($width, $height);
        }

        return [
            'mime' => 'image/svg+xml',
            'stored_mime' => 'image/svg+xml',
            'width' => $width,
            'height' => $height,
        ];
    }

    protected function validateDimensions(int $width, int $height): void
    {
        if ($width < self::MIN_IMAGE_DIMENSION || $height < self::MIN_IMAGE_DIMENSION) {
            throw new \InvalidArgumentException('The image dimensions are too small.');
        }

        if ($width > self::MAX_IMAGE_DIMENSION || $height > self::MAX_IMAGE_DIMENSION) {
            throw new \InvalidArgumentException('The image dimensions are too large. Please keep each side under 6000px.');
        }
    }

    protected function svgDimensions(\SimpleXMLElement $svg): array
    {
        $width = $this->numericSvgLength((string) ($svg['width'] ?? ''));
        $height = $this->numericSvgLength((string) ($svg['height'] ?? ''));

        if ((!$width || !$height) && isset($svg['viewBox'])) {
            $parts = preg_split('/[\s,]+/', trim((string) $svg['viewBox']));
            if (is_array($parts) && count($parts) === 4) {
                $width = $width ?: (int) round((float) $parts[2]);
                $height = $height ?: (int) round((float) $parts[3]);
            }
        }

        return [$width ?: null, $height ?: null];
    }

    protected function numericSvgLength(string $value): ?int
    {
        if ($value === '') {
            return null;
        }

        if (!preg_match('/^\s*(\d+(?:\.\d+)?)/', $value, $matches)) {
            return null;
        }

        return (int) round((float) $matches[1]);
    }

    protected function storagePrefix(array $definition): string
    {
        return Str::slug($definition['page_name']) . '/' . Str::slug($definition['section_name']);
    }
}

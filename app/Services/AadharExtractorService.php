<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser as PdfParser;
use thiagoalessio\TesseractOCR\TesseractOCR;

class AadharExtractorService
{
    public function extractFromUploadedFile(UploadedFile $file): ?string
    {
        $temporaryPath = $file->store('temp_aadhar');
        $absolutePath = Storage::path($temporaryPath);

        try {
            $extension = strtolower($file->getClientOriginalExtension());

            if ($extension === 'pdf') {
                $text = $this->extractTextFromPdf($absolutePath);
            } else {
                $text = $this->extractTextFromImage($absolutePath);
            }
        } finally {
            Storage::delete($temporaryPath);
        }

        if (!$text) {
            return null;
        }

        preg_match('/\b(\d{4}\s?\d{4}\s?\d{4})\b/', $text, $matches);

        return isset($matches[1])
            ? preg_replace('/\s+/', '', $matches[1])
            : null;
    }

    protected function extractTextFromPdf(string $path): ?string
    {
        $parser = new PdfParser();
        $pdf = $parser->parseFile($path);

        return $pdf?->getText();
    }

    protected function extractTextFromImage(string $path): ?string
    {
        if (!class_exists(TesseractOCR::class)) {
            return null;
        }

        return (new TesseractOCR($path))->run();
    }
}


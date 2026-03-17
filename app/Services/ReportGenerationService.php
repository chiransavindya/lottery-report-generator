<?php

namespace App\Services;

use App\Models\Draw;
use App\Models\Report;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use setasign\Fpdi\Fpdi;

class ReportGenerationService
{
    /**
     * Generate PDF report for all languages.
     * Auto-publishes upon successful generation.
     */
    public function generateAllLanguages(Draw $draw, int $userId): Report
    {
        $report = Report::create([
            'draw_id' => $draw->id,
            'generated_by' => $userId,
            'status' => 'published', // Auto-publish
            'published_at' => now(),
        ]);

        $successCount = 0;
        $errors = [];

        foreach (config('reports.languages') as $language) {
            try {
                $path = $this->generateReport($draw, $language);
                $report->update(["pdf_path_{$language}" => $path]);
                $successCount++;
            } catch (\Exception $e) {
                $errors[$language] = $e->getMessage();
                \Log::error("Failed to generate {$language} PDF for draw {$draw->id}", [
                    'draw_id' => $draw->id,
                    'lottery_type' => $draw->lotteryType->code,
                    'language' => $language,
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
            }
        }

        // Log summary
        if ($successCount === 0) {
            \Log::error("All PDF generations failed for draw {$draw->id}", ['errors' => $errors]);
            throw new \Exception("Failed to generate any PDFs: " . json_encode($errors));
        } elseif (count($errors) > 0) {
            \Log::warning("Partial PDF generation success for draw {$draw->id}", [
                'success_count' => $successCount,
                'errors' => $errors
            ]);
        }

        return $report;
    }

    /**
     * Generate PDF for a specific language.
     */
    public function generateReport(Draw $draw, string $language): string
    {
        // Prepare data
        $data = $this->getReportData($draw, $language);

        // Get lottery-specific template name
        $lotteryCode = strtolower($draw->lotteryType->code);
        $templateName = "reports.{$lotteryCode}_{$language}";

        // Fallback to generic template if lottery-specific doesn't exist
        if (!View::exists($templateName)) {
            $templateName = "reports.template_{$language}";
        }

        // Render HTML from Blade template
        $html = View::make($templateName, $data)->render();

        // Generate PDF
        $pdf = $this->createPdf($html);

        // Save to storage
        $filename = $this->getStoragePath($draw, $language);
        $this->savePdf($pdf, $filename);

        return $filename;
    }

    /**
     * Prepare data for PDF template.
     */
    protected function getReportData(Draw $draw, string $language): array
    {
        $labels = config("reports.labels.{$language}");
        $lotteryCode = $draw->lotteryType->code;
        $logoFile = config("reports.logos.{$lotteryCode}", 'logo.png');

        return [
            'draw' => $draw,
            'lottery' => $draw->lotteryType,
            'labels' => $labels,
            'logo' => public_path("images/{$logoFile}"),
            'language' => $language,
            'generated_at' => now(),
        ];
    }

    /**
     * Create DomPDF instance with configuration.
     */
    protected function createPdf(string $html): Dompdf
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', config('reports.pdf_options.enable_html5_parser'));
        $options->set('isRemoteEnabled', config('reports.pdf_options.enable_remote'));
        $options->set('defaultFont', config('reports.pdf_options.default_font'));
        $options->set('chroot', public_path());

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper(
            config('reports.pdf_options.paper'),
            config('reports.pdf_options.orientation')
        );
        $dompdf->render();

        return $dompdf;
    }

    /**
     * Save PDF to storage.
     */
    protected function savePdf(Dompdf $pdf, string $filename): void
    {
        $directory = dirname($filename);

        if (!Storage::exists($directory)) {
            try {
                Storage::makeDirectory($directory);
            } catch (\Exception $e) {
                throw new \Exception("Failed to create directory {$directory}: " . $e->getMessage());
            }
        }

        try {
            $pdfContent = $pdf->output();

            if (empty($pdfContent)) {
                throw new \Exception("PDF content is empty");
            }

            Log::info("PDF content size: " . strlen($pdfContent) . " bytes");

            // Try direct file_put_contents as fallback
            $fullPath = Storage::path($filename);
            $dirPath = dirname($fullPath);

            // Ensure directory exists
            if (!file_exists($dirPath)) {
                mkdir($dirPath, 0755, true);
            }

            // Write file directly
            $bytesWritten = file_put_contents($fullPath, $pdfContent);

            if ($bytesWritten === false) {
                throw new \Exception("file_put_contents failed. Path: {$fullPath}");
            }

            Log::info("PDF saved successfully: {$bytesWritten} bytes written to {$fullPath}");

        } catch (\Exception $e) {
            throw new \Exception("Failed to save PDF to storage: {$filename} - " . $e->getMessage());
        }
    }

    /**
     * Build storage path for PDF file.
     */
    protected function getStoragePath(Draw $draw, string $language): string
    {
        $date = $draw->draw_date->format('Y/m');
        $lotteryCode = $draw->lotteryType->code;
        $drawNumber = $draw->draw_number;

        return "reports/{$date}/{$lotteryCode}/{$drawNumber}/report_{$language}.pdf";
    }

    /**
     * Publish a report (change status from draft to published).
     */
    public function publishReport(Report $report): void
    {
        $report->update([
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    /**
     * Generate consolidated daily reports (all 8 lotteries combined).
     * Creates 3 PDFs: English, Sinhala, Tamil.
     * v2.0 New Feature
     *
     * @param string $date Draw date (YYYY-MM-DD)
     * @param int $userId User generating the report
     * @return Report
     */
    public function generateConsolidatedDailyReport(string $date, int $userId): Report
    {
        // Get all draws for the specified date
        $draws = Draw::whereDate('draw_date', $date)
            ->with('lotteryType')
            ->orderBy('lottery_type_id')
            ->get();

        if ($draws->count() < 8) {
            throw new \Exception("Incomplete data for date {$date}. Found {$draws->count()}/8 lotteries.");
        }

        // Create report record (we'll store draw_id as the first draw for reference)
        $report = Report::create([
            'draw_id' => $draws->first()->id,
            'generated_by' => $userId,
            'status' => 'draft',
        ]);

        $successCount = 0;
        $errors = [];

        foreach (config('reports.languages') as $language) {
            try {
                $path = $this->generateConsolidatedReport($draws, $language, $date);
                $report->update(["pdf_path_{$language}" => $path]);
                $successCount++;
            } catch (\Exception $e) {
                $errors[$language] = $e->getMessage();
                \Log::error("Failed to generate consolidated {$language} PDF for {$date}", [
                    'date' => $date,
                    'language' => $language,
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        if ($successCount === 0) {
            throw new \Exception("Failed to generate any consolidated PDFs: " . json_encode($errors));
        }

        // Auto-publish consolidated reports (no draft state needed)
        $report->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        return $report;
    }

    /**
     * Generate consolidated PDF for all lotteries on a specific date.
     *
     * @param \Illuminate\Database\Eloquent\Collection $draws
     * @param string $language
     * @param string $date
     * @return string Storage path
     */
    public function generateConsolidatedReport($draws, string $language, string $date): string
    {
        // Language-specific configurations
        $languageConfig = [
            'en' => [
                'title' => 'Daily Report Results',
                'headerColor' => '#3b82f6',
                'labels' => [
                    'Winning Numbers' => 'WINNING NUMBERS',
                    'English Letter' => 'ENGLISH LETTER',
                    'Super Number' => 'SUPER NUMBER',
                    'Zodiac' => 'ZODIAC (LAGNA)',
                    'Next Jackpot' => 'NEXT SUPER JACKPOT',
                    'Total Value' => 'TOTAL VALUE OF PRIZES',
                    'Total Prize Value' => 'TOTAL PRIZE VALUE',
                    'Winners' => 'TOTAL NO. OF Rs.200,000 WINNERS',
                    'Prize' => 'Prize',
                    'Special No' => 'Special No.'
                ],
            ],
            'si' => [
                'title' => 'දෛනික වාර්තාව',
                'headerColor' => '#10b981',
                'labels' => [
                    'Winning Numbers' => 'ජයග්‍රාහී අංක',
                    'English Letter' => 'ඉංග්‍රීසි අකුර',
                    'Super Number' => 'සුපිරි අංකය',
                    'Zodiac' => 'ලග්නය',
                    'Next Jackpot' => 'මීළඟ සුපිරි ජැක්පොට්',
                    'Total Value' => 'ත්‍යාගවල මුළු වටිනාකම',
                    'Total Prize Value' => 'ත්‍යාගවල මුළු වටිනාකම',
                    'Winners' => 'රු.200,000 දිනුම්ලාභීන් ගණන',
                    'Prize' => 'ත්‍යාගය',
                    'Special No' => 'විශේෂ අංකය'
                ],
            ],
            'ta' => [
                'title' => 'தினசரி அறிக்கை',
                'headerColor' => '#f59e0b',
                'labels' => [
                    'Winning Numbers' => 'வெற்றி எண்கள்',
                    'English Letter' => 'ஆங்கில எழுத்து',
                    'Super Number' => 'சூப்பர் எண்',
                    'Zodiac' => 'இராசி',
                    'Next Jackpot' => 'அடுத்த சூப்பர் ஜாக்பாட்',
                    'Total Value' => 'பரிசுகளின் மொத்த மதிப்பு',
                    'Total Prize Value' => 'வெல்லப்பட்ட மொத்த பரிசுத்தொகை',
                    'Winners' => 'ரூ.200,000 வெற்றியாளர்களின் எண்ணிக்கை',
                    'Prize' => 'பரிசு',
                    'Special No' => 'சிறப்பு எண்'
                ],
            ],
        ];

        // Prepare data for the PDF template
        $data = [
            'draws' => $draws,
            'date' => $date,
            'labels' => $languageConfig[$language]['labels'] ?? [],
            'language' => $language,
            'languageTitle' => $languageConfig[$language]['title'] ?? 'Daily Report',
            'headerColor' => $languageConfig[$language]['headerColor'] ?? '#3b82f6',
            'generated_at' => now(),
        ];

        // Use the new unified PDF template
        $templateName = "reports.show_pdf";

        // Render HTML
        $html = View::make($templateName, $data)->render();

        // Generate PDF
        $pdf = $this->createPdf($html);

        // Save to storage
        $filename = $this->getConsolidatedStoragePath($date, $language);
        $this->savePdf($pdf, $filename);

        return $filename;
    }

    /**
     * Build storage path for consolidated daily report.
     *
     * @param string $date
     * @param string $language
     * @return string
     */
    protected function getConsolidatedStoragePath(string $date, string $language): string
    {
        $dateObj = new \DateTime($date);
        $year = $dateObj->format('Y');
        $month = $dateObj->format('m');
        $day = $dateObj->format('Y-m-d');

        return "reports/{$year}/{$month}/consolidated/{$day}/consolidated_{$language}.pdf";
    }

    /**
     * Create ZIP file containing all 3 language PDFs for a report.
     * v2.0 New Feature
     *
     * @param Report $report
     * @return string Path to ZIP file
     */
    public function createZipDownload(Report $report): string
    {
        $zip = new \ZipArchive();
        $date = $report->draw->draw_date->format('Y-m-d');
        $zipFilename = storage_path("app/temp/reports_{$date}_" . time() . ".zip");

        // Ensure temp directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        if ($zip->open($zipFilename, \ZipArchive::CREATE) !== true) {
            throw new \Exception("Could not create ZIP file");
        }

        // Add each language PDF to ZIP
        foreach (config('reports.languages') as $language) {
            $pdfPath = $report->{"pdf_path_{$language}"};

            if ($pdfPath && Storage::exists($pdfPath)) {
                $pdfContent = Storage::get($pdfPath);
                $pdfFilename = "report_{$date}_{$language}.pdf";
                $zip->addFromString($pdfFilename, $pdfContent);
            }
        }

        $zip->close();

        return $zipFilename;
    }

    /**
     * Create a merged PDF containing all 3 languages.
     * Generates fresh PDFs for English, Sinhala, and Tamil, then merges them.
     * Order: Page 1 = English, Page 2 = Sinhala, Page 3 = Tamil
     * v2.0 New Feature
     *
     * @param Report $report
     * @return string Path to temporary PDF file
     */
    public function createMergedPdfDownload(Report $report): string
    {
        $date = $report->draw->draw_date->format('Y-m-d');

        // Get all draws for this date
        $draws = Draw::whereDate('draw_date', $date)
            ->with('lotteryType')
            ->orderBy('lottery_type_id')
            ->get();

        $tempPdfPaths = [];

        // Generate fresh PDF for each language
        foreach (['en', 'si', 'ta'] as $lang) {
            try {
                // Prepare data for the PDF template (same as consolidated report)
                $data = [
                    'report' => $report,
                    'draws' => $draws,
                    'date' => $date,
                    'language' => $lang,
                    'generated_at' => now(),
                ];

                // Use the unified PDF template
                $templateName = "reports.show_pdf";

                // Render HTML
                $html = View::make($templateName, $data)->render();

                // Generate PDF
                $pdf = $this->createPdf($html);

                // Save to temp file
                $tempFilename = "temp_{$lang}_{$date}_" . time() . ".pdf";
                $tempPath = storage_path("app/temp/{$tempFilename}");

                // Ensure temp directory exists
                if (!file_exists(dirname($tempPath))) {
                    mkdir(dirname($tempPath), 0755, true);
                }

                file_put_contents($tempPath, $pdf->output());
                $tempPdfPaths[$lang] = $tempPath;

            } catch (\Exception $e) {
                // Clean up any temp files created so far
                foreach ($tempPdfPaths as $tempFile) {
                    if (file_exists($tempFile)) {
                        unlink($tempFile);
                    }
                }
                throw new \Exception("Failed to generate {$lang} PDF: " . $e->getMessage());
            }
        }

        // Create merged PDF using FPDI
        $pdf = new Fpdi();

        // Add each PDF to the merged document in order: en, si, ta
        foreach ($tempPdfPaths as $lang => $pdfPath) {
            $pageCount = $pdf->setSourceFile($pdfPath);

            // Import all pages from this PDF
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplatesize($templateId);

                // Add a page with the same orientation and size as the source
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId);
            }
        }

        // Save merged file
        $filename = "{$date}_Newspaper_Report-ALL.pdf";
        $mergedPath = storage_path("app/temp/{$filename}");

        $pdf->Output($mergedPath, 'F');

        // Clean up temporary individual PDFs
        foreach ($tempPdfPaths as $tempFile) {
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }

        return $mergedPath;
    }

    /**
     * Clean up old temporary ZIP files.
     *
     * @return void
     */
    public function cleanupTempFiles(): void
    {
        $tempDir = storage_path('app/temp');

        if (!is_dir($tempDir)) {
            return;
        }

        $files = glob($tempDir . '/reports_*.zip');
        $now = time();

        foreach ($files as $file) {
            if (is_file($file)) {
                // Delete files older than 1 hour
                if ($now - filemtime($file) >= 3600) {
                    unlink($file);
                }
            }
        }
    }
}

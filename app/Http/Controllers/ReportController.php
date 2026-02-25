<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateReportRequest;
use App\Models\Draw;
use App\Models\Report;
use App\Services\ReportGenerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportGenerationService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Display listing grouped by draw date with upload batches.
     */
    public function index(Request $request)
    {
        // Get completed upload batches grouped by draw date
        $uploadBatches = \App\Models\UploadBatch::where('status', 'completed')
            ->whereNotNull('draw_date')
            ->with(['files'])
            ->latest('draw_date')
            ->get()
            ->groupBy('draw_date');

        // Also get existing reports for display
        $existingReports = Report::with(['draw.lotteryType', 'generator'])
            ->join('draws', 'reports.draw_id', '=', 'draws.id')
            ->select('reports.*')
            ->get()
            ->groupBy(function ($report) {
            return $report->draw->draw_date->format('Y-m-d');
        });

        // Define lottery display order
        $lotteryOrder = ['KP', 'LW', 'AK', 'SF', 'SB', 'SR', 'JS', 'DS'];

        return view('reports.index', compact('uploadBatches', 'existingReports', 'lotteryOrder'));
    }

    /**
     * Show form to select draw for report generation.
     */
    public function create()
    {
        // Get complete upload batches (that have draw_date set and is_complete = 1)
        $uploadBatches = \App\Models\UploadBatch::where('is_complete', true)
            ->whereNotNull('draw_date')
            ->where('status', 'completed')
            ->latest('draw_date')
            ->with('files')
            ->get();

        return view('reports.create', compact('uploadBatches'));
    }

    /**
     * Generate report for selected draw.
     */
    public function store(GenerateReportRequest $request)
    {
        $draw = Draw::findOrFail($request->draw_id);

        try {
            // Generate reports for all languages
            $report = $this->reportService->generateAllLanguages($draw, auth()->id());

            return redirect()
                ->route('reports.show', $report)
                ->with('success', 'Report generated successfully! You can now preview and download.');
        }
        catch (\Exception $e) {
            \Log::error('PDF generation failed', [
                'draw_id' => $request->draw_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withErrors(['error' => 'Failed to generate report: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Show report details and previews.
     */
    public function show(Report $report, string $language = 'en')
    {
        // Check authorization
        if (auth()->user()->isOperator() && $report->generated_by !== auth()->id()) {
            abort(403, 'You are not authorized to view this report.');
        }

        // Validate language
        if (!in_array($language, config('reports.languages'))) {
            $language = 'en'; // Default to English
        }

        // Get all draws for this date
        $drawDate = $report->draw->draw_date->format('Y-m-d');
        $draws = Draw::whereDate('draw_date', $drawDate)
            ->with('lotteryType')
            ->orderBy('lottery_type_id')
            ->get();

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
                    'Winners' => 'TOTAL NO. OF Rs.200,000 WINNERS',
                    'Prize' => 'Prize',
                    'Special No' => 'Special No.',
                    'Draw Number' => 'Draw Number',
                    'Colour' => 'Colour'
                ],
            ],
            'si' => [
                'title' => 'දෛනික වාර්තාව',
                'headerColor' => '#10b981',
                'labels' => [
                    'Winning Numbers' => 'ජයග්‍රාහී අංක',
                    'English Letter' => 'ඉංග්‍රීසි අක්ෂරය',
                    'Super Number' => 'සුපිරි අංකය',
                    'Zodiac' => 'ලග්නය',
                    'Next Jackpot' => 'මීළඟ සුපිරි ජයමල්ල',
                    'Total Value' => 'දිනා ඇති මුළු මුදල',
                    'Total Prize Value' => 'ත්‍යාගවල මුළු වටිනාකම',
                    'Winners' => 'අද බිහි වු දෙලක්ෂපතියන් ගණන',
                    'Prize' => 'ත්‍යාගය',
                    'Amount' => 'මුදල',
                    'Special No' => 'විශේෂ අංකය',
                    'Draw Number' => 'දිනුම් වාරය',
                    'Colour' => 'වර්ණය',
                    // Colors
                    'Green' => 'කොළ',
                    'Yellow' => 'කහ',
                    'Red' => 'රතු',
                    'Blue' => 'නිල්',
                    'Orange' => 'තැඹිලි',
                    'Purple' => 'දම්',
                    'Pink' => 'රෝස',
                    'Light Blue' => 'ලා නිල්',
                    'Light blue' => 'ලා නිල්',
                    'Light Pink' => 'ලා රෝස',
                    'Light pink' => 'ලා රෝස',
                    'Light Green' => 'ලා කොළ',
                    'Light green' => 'ලා කොළ',
                    'Dark Green' => 'තද කොළ',
                    'Dark green' => 'තද කොළ',
                    'Dark Blue' => 'තද නිල්',
                    'Dark blue' => 'තද නිල්',
                    'Brown' => 'දුඹුරු',
                    'Light Brown' => 'ලා දුඹුරු',
                    'Light brown' => 'ලා දුඹුරු',
                    'Dark Brown' => 'තද දුඹුරු',
                    'Dark brown' => 'තද දුඹුරු',
                    // Zodiac Signs
                    'Aries' => 'මේෂ',
                    'Taurus' => 'වෘෂභ',
                    'Gemini' => 'මිථුන',
                    'Cancer' => 'කටක',
                    'Leo' => 'සිංහ',
                    'Virgo' => 'කන්‍යා',
                    'Libra' => 'තුලා',
                    'Scorpio' => 'වෘශ්චික',
                    'Sagittarius' => 'ධනු',
                    'Capricorn' => 'මකර',
                    'Aquarius' => 'කුම්භ',
                    'Pisces' => 'මීන'
                ],
            ],
            'ta' => [
                'title' => 'தினசரி அறிக்கை',
                'headerColor' => '#f59e0b',
                'labels' => [
                    'Winning Numbers' => 'வெற்றி இலக்கங்கள்',
                    'English Letter' => 'ஆங்கில எழுத்து',
                    'Super Number' => 'சுப்பர் இலக்கம்',
                    'Zodiac' => 'இராசி',
                    'Next Jackpot' => 'அடுத்த சுப்பர் பரிசுப்பொதி',
                    'Total Value' => 'பரிசுகளின் மொத்த பெறுமதி',
                    'Winners' => 'ரூ. 200,000 வெற்றியாளர்களின் மொத்த எண்ணிக்கை',
                    'Prize' => 'பரிசு',
                    'Special No' => 'விசேட இலக்கம்',
                    'Draw Number' => 'சீட்டிழுப்பு இலக்கம்',
                    'Colour' => 'நிறம்',
                    // Colors
                    'Green' => 'பச்சை',
                    'Yellow' => 'மஞ்சள்',
                    'Red' => 'சிவப்பு',
                    'Blue' => 'நீலம்',
                    'Orange' => 'செம்மஞ்சள்',
                    'Purple' => 'ஊதா',
                    'Pink' => 'இளஞ்சிவப்பு',
                    'Light Blue' => 'இள நீலம்',
                    'Light blue' => 'இள நீலம்',
                    // Zodiac
                    'Aries' => 'மேடம்',
                    'Taurus' => 'இடபம்',
                    'Gemini' => 'மிதுனம்',
                    'Cancer' => 'கடகம்',
                    'Leo' => 'சிம்மம்',
                    'Virgo' => 'கன்னி',
                    'Libra' => 'துலாம்',
                    'Scorpio' => 'விருச்சிகம்',
                    'Sagittarius' => 'தனுசு',
                    'Capricorn' => 'மகரம்',
                    'Aquarius' => 'கும்பம்',
                    'Pisces' => 'மீனம்'
                ],
            ],
        ];

        // Prepare data
        $data = [
            'draws' => $draws,
            'date' => $drawDate,
            'labels' => $languageConfig[$language]['labels'] ?? [],
            'language' => $language,
            'languageTitle' => $languageConfig[$language]['title'] ?? 'Daily Report',
            'headerColor' => $languageConfig[$language]['headerColor'] ?? '#3b82f6',
            'generated_at' => now(),
            'report' => $report, // Pass report for download buttons
        ];

        // Return the PDF template view directly (no PDF conversion)
        return view('reports.show_pdf', $data);
    }

    /**
     * Preview PDF template in browser (live preview).
     */
    public function previewPdf(Report $report, string $language)
    {
        // Validate language
        if (!in_array($language, config('reports.languages'))) {
            abort(404, 'Invalid language');
        }

        // Get all draws for this date
        $drawDate = $report->draw->draw_date->format('Y-m-d');
        $draws = Draw::whereDate('draw_date', $drawDate)
            ->with('lotteryType')
            ->orderBy('lottery_type_id')
            ->get();

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
                    'Winners' => 'TOTAL NO. OF Rs.200,000 WINNERS',
                    'Prize' => 'Prize',
                    'Special No' => 'Special No.',
                    'Draw Number' => 'Draw Number',
                    'Colour' => 'Colour'
                ],
            ],
            'si' => [
                'title' => 'දෛනික වාර්තාව',
                'headerColor' => '#10b981',
                'labels' => [
                    'Winning Numbers' => 'ජයග්‍රාහී අංක',
                    'English Letter' => 'ඉංග්‍රීසි අක්ෂරය',
                    'Super Number' => 'සුපිරි අංකය',
                    'Zodiac' => 'ලග්නය',
                    'Next Jackpot' => 'මීළඟ සුපිරි ජයමල්ල',
                    'Total Value' => 'දිනා ඇති මුළු මුදල',
                    'Total Prize Value' => 'ත්‍යාගවල මුළු වටිනාකම',
                    'Winners' => 'අද බිහි වු දෙලක්ෂපතියන් ගණන',
                    'Prize' => 'ත්‍යාගය',
                    'Amount' => 'මුදල',
                    'Special No' => 'විශේෂ අංකය',
                    'Draw Number' => 'දිනුම් වාරය',
                    'Colour' => 'වර්ණය',
                    // Colors
                    'Green' => 'කොළ',
                    'Yellow' => 'කහ',
                    'Red' => 'රතු',
                    'Blue' => 'නිල්',
                    'Orange' => 'තැඹිලි',
                    'Purple' => 'දම්',
                    'Pink' => 'රෝස',
                    'Light Blue' => 'ලා නිල්',
                    'Light blue' => 'ලා නිල්',
                    'Light Pink' => 'ලා රෝස',
                    'Light pink' => 'ලා රෝස',
                    'Light Green' => 'ලා කොළ',
                    'Light green' => 'ලා කොළ',
                    'Dark Green' => 'තද කොළ',
                    'Dark green' => 'තද කොළ',
                    'Dark Blue' => 'තද නිල්',
                    'Dark blue' => 'තද නිල්',
                    'Brown' => 'දුඹුරු',
                    'Light Brown' => 'ලා දුඹුරු',
                    'Light brown' => 'ලා දුඹුරු',
                    'Dark Brown' => 'තද දුඹුරු',
                    'Dark brown' => 'තද දුඹුරු',
                    // Zodiac Signs
                    'Aries' => 'මේෂ',
                    'Taurus' => 'වෘෂභ',
                    'Gemini' => 'මිථුන',
                    'Cancer' => 'කටක',
                    'Leo' => 'සිංහ',
                    'Virgo' => 'කන්‍යා',
                    'Libra' => 'තුලා',
                    'Scorpio' => 'වෘශ්චික',
                    'Sagittarius' => 'ධනු',
                    'Capricorn' => 'මකර',
                    'Aquarius' => 'කුම්භ',
                    'Pisces' => 'මීන'
                ],
            ],
            'ta' => [
                'title' => 'தினசரி அறிக்கை',
                'headerColor' => '#f59e0b',
                'labels' => [
                    'Winning Numbers' => 'வெற்றி இலக்கங்கள்',
                    'English Letter' => 'ஆங்கில எழுத்து',
                    'Super Number' => 'சுப்பர் இலக்கம்',
                    'Zodiac' => 'இராசி',
                    'Next Jackpot' => 'அடுத்த சுப்பர் பரிசுப்பொதி',
                    'Total Value' => 'பரிசுகளின் மொத்த பெறுமதி',
                    'Winners' => 'ரூ. 200,000 வெற்றியாளர்களின் மொத்த எண்ணிக்கை',
                    'Prize' => 'பரிசு',
                    'Special No' => 'விசேட இலக்கம்',
                    'Draw Number' => 'சீட்டிழுப்பு இலக்கம்',
                    'Colour' => 'நிறம்',
                    // Colors
                    'Green' => 'பச்சை',
                    'Yellow' => 'மஞ்சள்',
                    'Red' => 'சிவப்பு',
                    'Blue' => 'நீலம்',
                    'Orange' => 'செம்மஞ்சள்',
                    'Purple' => 'ஊதா',
                    'Pink' => 'இளஞ்சிவப்பு',
                    'Light Blue' => 'இள நீலம்',
                    'Light blue' => 'இள நீலம்',
                    // Zodiac
                    'Aries' => 'மேடம்',
                    'Taurus' => 'இடபம்',
                    'Gemini' => 'மிதுனம்',
                    'Cancer' => 'கடகம்',
                    'Leo' => 'சிம்மம்',
                    'Virgo' => 'கன்னி',
                    'Libra' => 'துலாம்',
                    'Scorpio' => 'விருச்சிகம்',
                    'Sagittarius' => 'தனுசு',
                    'Capricorn' => 'மகரம்',
                    'Aquarius' => 'கும்பம்',
                    'Pisces' => 'மீனம்'
                ],
            ],
        ];

        // Prepare data
        $data = [
            'draws' => $draws,
            'date' => $drawDate,
            'labels' => $languageConfig[$language]['labels'] ?? [],
            'language' => $language,
            'languageTitle' => $languageConfig[$language]['title'] ?? 'Daily Report',
            'headerColor' => $languageConfig[$language]['headerColor'] ?? '#3b82f6',
            'generated_at' => now(),
        ];

        // Return the PDF template view directly (no PDF conversion)
        return view('reports.show_pdf', $data);
    }

    /**
     * Download PDF for specific language.
     * If PDF doesn't exist (e.g., deleted by cleanup), regenerate it on-demand.
     */
    public function download(Report $report, string $language)
    {
        // Validate language
        if (!in_array($language, config('reports.languages'))) {
            abort(404, 'Invalid language');
        }

        // Check authorization
        if (auth()->user()->isOperator() && $report->generated_by !== auth()->id()) {
            abort(403, 'You are not authorized to download this report.');
        }

        // Get file path
        $path = $report->{ "pdf_path_{$language}"};

        // REGENERATE ON-DEMAND if file doesn't exist
        if (!$path || !Storage::exists($path)) {
            \Log::info("PDF not found, regenerating on-demand", [
                'report_id' => $report->id,
                'language' => $language,
                'old_path' => $path
            ]);

            try {
                // Get draw date and all draws for consolidated report
                $drawDate = $report->draw->draw_date->format('Y-m-d');
                $draws = Draw::whereDate('draw_date', $drawDate)
                    ->with('lotteryType')
                    ->orderBy('lottery_type_id')
                    ->get();

                // Regenerate the PDF
                $newPath = $this->reportService->generateConsolidatedReport($draws, $language, $drawDate);

                // Update report record with new path
                $report->update(["pdf_path_{$language}" => $newPath]);

                $path = $newPath;

                \Log::info("PDF regenerated successfully", [
                    'report_id' => $report->id,
                    'language' => $language,
                    'new_path' => $newPath
                ]);
            }
            catch (\Exception $e) {
                \Log::error("Failed to regenerate PDF on-demand", [
                    'report_id' => $report->id,
                    'language' => $language,
                    'error' => $e->getMessage()
                ]);
                abort(500, 'Failed to generate report: ' . $e->getMessage());
            }
        }

        $filename = sprintf(
            '%s_Report_%s.pdf',
            $report->draw->draw_date->format('Y-m-d'),
            strtoupper($language)
        );

        return response()->file(Storage::path($path), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Publish a draft report (admin only).
     */
    public function publish(Report $report)
    {
        if ($report->status !== 'draft') {
            return back()->with('error', 'Only draft reports can be published.');
        }

        $this->reportService->publishReport($report);

        return back()->with('success', 'Report published successfully!');
    }

    /**
     * Delete a report (admin only).
     */
    public function destroy(Report $report)
    {
        // Delete PDF files from storage
        foreach (config('reports.languages') as $language) {
            $path = $report->{ "pdf_path_{$language}"};
            if ($path && Storage::exists($path)) {
                Storage::delete($path);
            }
        }

        // Delete database record
        $report->delete();

        return redirect()
            ->route('reports.index')
            ->with('success', 'Report deleted successfully.');
    }

    /**
     * Download all language PDFs as ZIP file.
     * v2.0 New Feature
     */
    public function downloadZip(Report $report)
    {
        // Check authorization
        if (auth()->user()->isOperator() && $report->generated_by !== auth()->id()) {
            abort(403, 'You are not authorized to download this report.');
        }

        try {
            // Create ZIP file
            $zipPath = $this->reportService->createZipDownload($report);

            $date = $report->draw->draw_date->format('Y-m-d');
            $filename = "LRMS_Reports_{$date}.zip";

            return response()->download($zipPath, $filename)->deleteFileAfterSend(true);
        }
        catch (\Exception $e) {
            \Log::error('Failed to create ZIP download', [
                'report_id' => $report->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Failed to create ZIP file: ' . $e->getMessage()]);
        }
    }

    /**
     * Download all languages in a single merged PDF.
     * v2.0 New Feature
     */
    public function downloadMerged(Report $report)
    {
        // Check authorization
        if (auth()->user()->isOperator() && $report->generated_by !== auth()->id()) {
            abort(403, 'You are not authorized to download this report.');
        }

        try {
            // Create Merged PDF
            $pdfPath = $this->reportService->createMergedPdfDownload($report);

            $date = $report->draw->draw_date->format('Y-m-d');
            $filename = "{$date}_Newspaper_Report-ALL.pdf";

            return response()->download($pdfPath, $filename)->deleteFileAfterSend(true);
        }
        catch (\Exception $e) {
            \Log::error('Failed to create Merged PDF download', [
                'report_id' => $report->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Failed to create Merged PDF: ' . $e->getMessage()]);
        }
    }

    /**
     * Generate consolidated daily report (all 8 lotteries).
     * v2.0 New Workflow
     */
    public function generateConsolidated(Request $request)
    {
        $request->validate([
            'draw_date' => 'required|date',
        ]);

        $date = $request->draw_date;

        try {
            // Check if all 8 lotteries are present for this date
            $drawCount = Draw::whereDate('draw_date', $date)->count();

            if ($drawCount < 8) {
                return back()->withErrors([
                    'error' => "Incomplete data for {$date}. Only {$drawCount}/8 lotteries found. Cannot generate consolidated report.",
                ]);
            }

            // Generate consolidated report
            $report = $this->reportService->generateConsolidatedDailyReport($date, auth()->id());

            return redirect()
                ->route('reports.show', $report)
                ->with('success', "Consolidated daily report for {$date} generated successfully!");
        }
        catch (\Exception $e) {
            \Log::error('Consolidated report generation failed', [
                'date' => $date,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Failed to generate consolidated report: ' . $e->getMessage()]);
        }
    }
}

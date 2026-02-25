/**
 * Report PDF Generator - Accordion UI Version
 * Handles PDF generation for lottery reports using html2canvas + jsPDF
 * Updated for accordion/collapsible interface (no tab switching needed)
 */

import PDFGenerator from './pdf-generator';

/**
 * Initialize report PDF download functionality
 */
function initReportPDFDownloads() {
    // Get report metadata
    const reportContainer = document.querySelector('[data-report-date]');
    if (!reportContainer) return;

    const reportDate = reportContainer.dataset.reportDate;

    // Download single language PDF
    window.downloadReportPDF = async function(language) {
        // Map language codes to suffixes
        const languageSuffix = {
            'en': 'ENG',
            'si': 'SIN',
            'ta': 'TAM'
        };

        const suffix = languageSuffix[language] || 'ENG';
        const elementId = `report-content-${language}`;
        const filename = `${reportDate}_Newspaper_Report-${suffix}.pdf`;

        return await PDFGenerator.generatePDF(elementId, filename);
    };

    // Download all languages merged into one PDF
    window.downloadMergedReportPDF = async function() {
        const elements = [
            { id: 'report-content-en', language: 'en' },
            { id: 'report-content-si', language: 'si' },
            { id: 'report-content-ta', language: 'ta' }
        ];

        const filename = `${reportDate}_Newspaper_Report-ALL.pdf`;

        return await PDFGenerator.generateMultiPagePDF(elements, filename);
    };

    // Attach event listeners to download buttons
    document.querySelectorAll('[data-pdf-action]').forEach(btn => {
        btn.addEventListener('click', async function(e) {
            e.preventDefault();
            e.stopPropagation(); // Prevent accordion toggle

            const action = this.dataset.pdfAction;
            const language = this.dataset.pdfLanguage;

            if (action === 'download-language' && language) {
                // Download the specific language requested
                await window.downloadReportPDF(language);
            } else if (action === 'download-merged') {
                // Download all languages merged
                await window.downloadMergedReportPDF();
            }
        });
    });
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initReportPDFDownloads);
} else {
    initReportPDFDownloads();
}

export { initReportPDFDownloads };

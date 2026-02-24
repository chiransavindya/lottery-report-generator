/**
 * PDF Generator Module
 * Uses html2canvas and jsPDF for client-side PDF generation
 */

import html2canvas from 'html2canvas';
import { jsPDF } from 'jspdf';

class PDFGenerator {
    constructor() {
        this.isGenerating = false;
    }

    /**
     * Generate PDF from HTML element
     * @param {string} elementId - ID of the HTML element to capture
     * @param {string} filename - Name of the PDF file
     * @param {object} options - Additional options
     */
    async generatePDF(elementId, filename, options = {}) {
        if (this.isGenerating) {
            console.warn('PDF generation already in progress');
            return;
        }

        this.isGenerating = true;
        const element = document.getElementById(elementId);

        if (!element) {
            console.error(`Element with ID "${elementId}" not found`);
            this.isGenerating = false;
            return;
        }

        try {
            // Show loading indicator if available
            this.showLoading();

            // Step 1: Capture HTML as high-resolution image
            const canvas = await html2canvas(element, {
                scale: 4, // 4x resolution for crisp output
                useCORS: true, // Handle cross-origin images
                allowTaint: true,
                backgroundColor: '#ffffff',
                logging: false,
                ...options.canvasOptions
            });

            // Step 2: Convert canvas to image data
            const imgData = canvas.toDataURL('image/png', 1.0);

            // Step 3: Calculate PDF dimensions
            const imgWidth = canvas.width / 4; // Scale back to original size
            const imgHeight = canvas.height / 4;

            // Convert pixels to mm (assuming 96 DPI)
            const pdfWidth = imgWidth * 0.264583; // px to mm conversion
            const pdfHeight = imgHeight * 0.264583;

            // Step 4: Create PDF with dynamic sizing
            const pdf = new jsPDF({
                orientation: pdfWidth > pdfHeight ? 'landscape' : 'portrait',
                unit: 'mm',
                format: [pdfWidth, pdfHeight],
                compress: true
            });

            // Add image to PDF
            pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight, undefined, 'FAST');

            // Step 5: Download the PDF
            pdf.save(filename);

            this.hideLoading();
            this.isGenerating = false;

            return true;
        } catch (error) {
            console.error('PDF generation failed:', error);
            this.hideLoading();
            this.isGenerating = false;
            alert('Failed to generate PDF. Please try again.');
            return false;
        }
    }

    /**
     * Generate multi-page PDF with multiple elements (for "All Reports")
     * @param {Array} elements - Array of {id, language} objects
     * @param {string} filename - Name of the PDF file
     */
    async generateMultiPagePDF(elements, filename) {
        if (this.isGenerating) {
            console.warn('PDF generation already in progress');
            return;
        }

        this.isGenerating = true;

        try {
            this.showLoading();

            let pdf = null;

            for (let i = 0; i < elements.length; i++) {
                const { id, language } = elements[i];
                const element = document.getElementById(id);

                if (!element) {
                    console.warn(`Element with ID "${id}" not found, skipping`);
                    continue;
                }

                // Temporarily make the element visible if it's hidden
                const parentPane = element.closest('.tab-pane');
                const wasHidden = parentPane && parentPane.style.display === 'none';

                if (wasHidden) {
                    parentPane.style.display = 'block';
                }

                // Small delay to ensure rendering is complete
                await new Promise(resolve => setTimeout(resolve, 100));

                // Capture the element
                const canvas = await html2canvas(element, {
                    scale: 4,
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: '#ffffff',
                    logging: false
                });

                // Restore original display state
                if (wasHidden) {
                    parentPane.style.display = 'none';
                }

                const imgData = canvas.toDataURL('image/png', 1.0);
                const imgWidth = canvas.width / 4;
                const imgHeight = canvas.height / 4;
                const pdfWidth = imgWidth * 0.264583;
                const pdfHeight = imgHeight * 0.264583;

                if (i === 0) {
                    // Create PDF with first page
                    pdf = new jsPDF({
                        orientation: pdfWidth > pdfHeight ? 'landscape' : 'portrait',
                        unit: 'mm',
                        format: [pdfWidth, pdfHeight],
                        compress: true
                    });
                } else {
                    // Add new page for subsequent elements
                    pdf.addPage([pdfWidth, pdfHeight], pdfWidth > pdfHeight ? 'landscape' : 'portrait');
                }

                pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight, undefined, 'FAST');
            }

            if (pdf) {
                pdf.save(filename);
            }

            this.hideLoading();
            this.isGenerating = false;

            return true;
        } catch (error) {
            console.error('Multi-page PDF generation failed:', error);
            this.hideLoading();
            this.isGenerating = false;
            alert('Failed to generate PDF. Please try again.');
            return false;
        }
    }

    /**
     * Show loading indicator
     */
    showLoading() {
        // Disable all download buttons
        document.querySelectorAll('.pdf-download-btn').forEach(btn => {
            btn.disabled = true;
            btn.classList.add('generating');
            const originalText = btn.innerHTML;
            btn.dataset.originalText = originalText;
            btn.innerHTML = '<span class="spinner"></span> Generating...';
        });
    }

    /**
     * Hide loading indicator
     */
    hideLoading() {
        // Re-enable all download buttons
        document.querySelectorAll('.pdf-download-btn').forEach(btn => {
            btn.disabled = false;
            btn.classList.remove('generating');
            if (btn.dataset.originalText) {
                btn.innerHTML = btn.dataset.originalText;
            }
        });
    }
}

// Export singleton instance
export default new PDFGenerator();

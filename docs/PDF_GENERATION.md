# PDF Generation System Documentation

## Overview

The LRMS v2 PDF generation system uses a client-side approach for high-quality newspaper report downloads. This implementation leverages `html2canvas` and `jsPDF` to create pixel-perfect PDFs directly in the browser.

## Architecture

### Three-Step Process

#### Step 1: Image Capture (html2canvas)
Instead of converting HTML/CSS code to text, the system takes a "high-definition screenshot" of the specific HTML report section.

**Key Features:**
- **4x Scaling**: Uses `scale: 4`, meaning it renders the image at 4 times the screen resolution
- **Result**: Crisp, clear text that remains sharp when printed or zoomed
- **CORS Handling**: Configured with `useCORS: true` to include external images and assets
- **Background**: Sets `backgroundColor: '#ffffff'` for consistent white backgrounds

```javascript
const canvas = await html2canvas(element, {
    scale: 4,              // 4x resolution for crisp output
    useCORS: true,         // Handle cross-origin images
    allowTaint: true,
    backgroundColor: '#ffffff',
    logging: false
});
```

#### Step 2: PDF Construction (jsPDF)
The captured image (in PNG format) is handed over to the PDF engine.

**Features:**
- **Dynamic Sizing**: PDF page dimensions automatically match HTML content size
- **Format Optimization**: Automatically detects orientation (portrait/landscape)
- **Compression**: Uses `compress: true` for optimized file sizes
- **High Quality**: PNG format at 1.0 quality ensures no loss

```javascript
const pdf = new jsPDF({
    orientation: pdfWidth > pdfHeight ? 'landscape' : 'portrait',
    unit: 'mm',
    format: [pdfWidth, pdfHeight],
    compress: true
});

pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight, undefined, 'FAST');
```

#### Step 3: State Management & Download
**UX Safety Features:**
- Sets `isGenerating` state to prevent multiple simultaneous generations
- Disables download buttons during processing
- Shows loading spinner to indicate progress
- Automatic file naming: `{DrawDate}_Newspaper_Report.pdf`

## File Structure

```
resources/
├── js/
│   ├── app.js                 # Main entry point
│   ├── pdf-generator.js       # Core PDF generation logic
│   └── report-pdf.js          # Report-specific implementation
└── views/
    └── reports/
        └── show_pdf.blade.php # Report display with download buttons

public/
└── css/
    └── pdf-buttons.css        # PDF button styling
```

## Components

### 1. PDFGenerator Class (`pdf-generator.js`)
Core reusable PDF generation engine.

**Methods:**
- `generatePDF(elementId, filename, options)` - Generate single-page PDF
- `generateMultiPagePDF(elements, filename)` - Generate multi-page PDF
- `showLoading()` - Display loading state
- `hideLoading()` - Remove loading state

### 2. Report PDF Module (`report-pdf.js`)
Report-specific implementation that uses PDFGenerator.

**Features:**
- Automatic report metadata detection
- Language-specific file naming
- Event listener management
- User feedback and error handling

### 3. UI Components (`show_pdf.blade.php`)
- Language selection tabs
- Download sidebar with buttons
- Report content area with unique IDs
- Data attributes for JavaScript access

## Download Options

### Individual Language PDFs
**Method**: Client-side (html2canvas + jsPDF)
- Click language-specific button
- Instant generation from current view
- File name: `{Date}_Newspaper_Report.pdf`
- Resolution: 4x for print quality

### All Languages (ZIP)
**Method**: Server-side backend generation
- Uses existing backend ZIP functionality
- Contains all three language PDFs
- Ensures consistency across downloads

## Technical Details

### Resolution Calculation
```javascript
// 4x capture for high-res
const canvas = await html2canvas(element, { scale: 4 });

// Scale back to original dimensions for PDF
const imgWidth = canvas.width / 4;
const imgHeight = canvas.height / 4;

// Convert pixels to millimeters (96 DPI standard)
const pdfWidth = imgWidth * 0.264583;
const pdfHeight = imgHeight * 0.264583;
```

### Button State Management
```javascript
// During generation
button.disabled = true;
button.classList.add('generating');
button.innerHTML = '<span class="spinner"></span> Generating...';

// After completion
button.disabled = false;
button.classList.remove('generating');
button.innerHTML = originalText;
```

## Browser Compatibility

- **Chrome/Edge**: Full support ✅
- **Firefox**: Full support ✅
- **Safari**: Full support (CORS may need attention) ⚠️
- **Mobile**: Requires testing 📱

## Performance Considerations

1. **Memory Usage**: 4x scaling increases memory requirements
2. **Generation Time**: ~2-5 seconds depending on content complexity
3. **File Size**: ~200-500KB per page (PNG compression)

## Advantages

✅ **High Quality**: 4x resolution ensures crisp text  
✅ **No Backend Load**: Processing happens client-side  
✅ **Instant Preview**: Direct capture of what user sees  
✅ **Cross-Browser**: Works consistently across modern browsers  
✅ **CSS Fidelity**: Exact replication of styled content  

## Limitations

⚠️ **Single Language Per Generation**: Each button downloads current view only  
⚠️ **Memory Intensive**: Large reports may cause slowdowns on low-end devices  
⚠️ **Font Dependency**: Requires fonts to be loaded before capture  

## Usage Example

```javascript
// Initialize (automatically done in report-pdf.js)
import PDFGenerator from './pdf-generator';

// Generate PDF
await PDFGenerator.generatePDF('report-content-en', '2026-02-06_Report.pdf');
```

## Future Enhancements

1. **Multi-language Single PDF**: Load all languages on page (hidden) for true multi-page PDFs
2. **Progress Indicators**: Show percentage during large file generation
3. **Quality Settings**: Allow users to choose resolution (2x, 3x, 4x)
4. **Batch Downloads**: Queue multiple downloads
5. **Background Processing**: Use Web Workers for large files

## Troubleshooting

### Issue: Blurry Text
**Solution**: Ensure `scale: 4` is set in html2canvas options

### Issue: Missing Images
**Solution**: Check CORS configuration and `useCORS: true` setting

### Issue: Button Not Responding
**Solution**: Check browser console for JavaScript errors, verify Vite assets are built

### Issue: Long Generation Time
**Solution**: Reduce content complexity or scale factor

## Dependencies

```json
{
  "html2canvas": "^1.x",
  "jspdf": "^2.x"
}
```

## Build Commands

```bash
# Development
npm run dev

# Production build
npm run build
```

---

**Last Updated**: February 6, 2026  
**Version**: 2.0  
**Authors**: Development Team

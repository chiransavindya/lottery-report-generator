# Accordion UI Implementation

## Overview
The report view has been redesigned from a tab-based interface to an accordion/collapsible interface for better UX and more reliable PDF generation.

## Key Changes

### 1. **From Tabs to Accordion**
- **Before**: Language tabs (English | සිංහල | தமிழ்) with one visible report at a time
- **After**: Collapsible sections with all three language reports rendered on the same page

### 2. **Benefits**
- ✅ All reports exist in DOM from page load → Reliable PDF generation for any language
- ✅ Cleaner UI → Users expand only the language they need
- ✅ Download buttons integrated into section headers → Easier access
- ✅ No more tab switching → Smoother user experience
- ✅ Scroll to expanded section → Better navigation

## User Interface

### Accordion Sections
Each language has its own accordion section with:

#### Header (Clickable)
- **Language Icon**: 🇬🇧 (English), 🇱🇰 සිං (Sinhala), 🇱🇰 த (Tamil)
- **Language Name**: Displayed in native script
- **Toggle Arrow**: ▶ (collapsed) / ▼ (expanded)
- **Download Button**: 📥 Download PDF (does NOT toggle accordion when clicked)

#### Content (Collapsible)
- A4-sized report container with full newspaper report
- Smooth expand/collapse animation
- English expanded by default, others collapsed

### Visual Features
- **Color-coded headers**: Each language has distinct color
  - English: Blue (#3B82F6)
  - Sinhala: Green (#10B981)
  - Tamil: Orange (#F59E0B)
- **Hover effects**: Headers brighten on hover
- **Smooth animations**: 0.3s transitions for expand/collapse
- **Responsive design**: Mobile-friendly with adjusted layouts

## PDF Download Workflow

### How It Works
1. User clicks on accordion header to expand desired language
2. Report section smoothly expands and scrolls into view
3. User reviews the report content
4. User clicks "📥 Download PDF" button in the header
5. html2canvas captures the report at 4x resolution
6. jsPDF generates PDF with filename: `{DrawDate}_Newspaper_Report-{LANG}.pdf`
7. Browser downloads the PDF

### Technical Implementation
- **All three reports rendered**: No DOM manipulation needed during download
- **Event propagation prevented**: Download button click doesn't toggle accordion
- **Same PDF quality**: 4x scaling maintained from previous implementation

## Files Modified

### New Files
- `public/css/report-accordion.css` - Complete accordion styling with animations

### Updated Files
- `resources/views/reports/show_pdf.blade.php`:
  - Removed language tabs navigation
  - Added accordion structure with foreach loop
  - Integrated download buttons into headers
  - Added inline `toggleAccordion()` function
  
- `resources/js/report-pdf.js`:
  - Removed tab switching logic
  - Simplified to PDF download only
  - Added event.stopPropagation() for download buttons

## Testing Checklist

### Accordion Functionality
- [ ] Click English header → Section expands/collapses
- [ ] Click Sinhala header → Section expands/collapses
- [ ] Click Tamil header → Section expands/collapses
- [ ] Toggle arrow rotates (▶ ↔ ▼)
- [ ] Only one section can be expanded at a time? (No - all can be open)

### PDF Downloads
- [ ] English download generates `{Date}_Newspaper_Report-ENG.pdf`
- [ ] Sinhala download generates `{Date}_Newspaper_Report-SIN.pdf`
- [ ] Tamil download generates `{Date}_Newspaper_Report-TAM.pdf`
- [ ] Download button doesn't toggle accordion when clicked
- [ ] PDF quality is high (4x resolution)
- [ ] All content fits properly in PDF

### Visual/UX
- [ ] Accordion headers have correct colors
- [ ] Hover effects work on headers
- [ ] Smooth animations on expand/collapse
- [ ] Page scrolls to expanded section
- [ ] Responsive on mobile devices
- [ ] Download buttons styled correctly

## Troubleshooting

### Issue: Accordion doesn't expand
**Solution**: Check browser console for JavaScript errors. Ensure `toggleAccordion()` function is loaded.

### Issue: Download button toggles accordion
**Solution**: Verify `event.stopPropagation()` is present in click handler.

### Issue: Wrong language downloaded
**Solution**: Check `data-pdf-language` attribute on download buttons matches accordion language.

### Issue: PDF generation fails
**Solution**: Ensure all three `report-content-{lang}` elements exist in DOM. Check console for html2canvas errors.

## Future Enhancements (Optional)

1. **Auto-collapse others**: When one section expands, auto-collapse others
2. **Keyboard navigation**: Arrow keys to navigate between sections
3. **Permalinks**: URL fragments to deep-link to specific language (#en, #si, #ta)
4. **Download all**: Button to generate multi-language PDF with all three reports
5. **Print support**: CSS print styles for direct printing of expanded section

## Code Reference

### Accordion Toggle Function
```javascript
function toggleAccordion(lang) {
    const content = document.getElementById('content-' + lang);
    const toggle = document.getElementById('toggle-' + lang);
    const header = content.previousElementSibling;
    
    if (content.style.display === 'none' || content.style.display === '') {
        content.style.display = 'block';
        toggle.textContent = '▼';
        
        // Smooth scroll to section
        setTimeout(() => {
            header.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 100);
    } else {
        content.style.display = 'none';
        toggle.textContent = '▶';
    }
}
```

### Download Event Handler
```javascript
document.querySelectorAll('[data-pdf-action]').forEach(btn => {
    btn.addEventListener('click', async function(e) {
        e.preventDefault();
        e.stopPropagation(); // Prevent accordion toggle
        
        const language = this.dataset.pdfLanguage;
        await window.downloadReportPDF(language);
    });
});
```

---

**Implementation Date**: 2026-02-06  
**Version**: 1.0  
**Status**: ✅ Complete and Ready for Testing

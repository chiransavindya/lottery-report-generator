# Report Page CSS Analysis & PDF Dimensions
## Quick Reference: PDF Dimensions


### Exact Dimensions by Language

| Language | Width (Fixed) | Height (Dynamic) | Typical Range |
|----------|---------------|------------------|---------------|
| 🇬🇧 **English** | **10.8cm (108mm)** | Calculated from content | **170-200mm** |
| 🇱🇰 **Sinhala** | **9.3cm (93mm)** | Calculated from content | **180-220mm** |
| 🇮🇳 **Tamil** | **10.8cm (108mm)** | Calculated from content | **190-230mm** |

### Key Points

- ✅ **Width is FIXED per language** (defined in CSS)
- ✅ **Height is DYNAMIC** (calculated from actual HTML content at render time)
- ✅ **Sinhala is narrower** (9.3cm vs 10.8cm) to accommodate script requirements
- ✅ **NOT standard A4** (210×297mm) - each page has custom dimensions
- ✅ **PDF generation happens on frontend** using jsPDF + html2canvas
- ✅ **4x scale factor** for high-quality output

---

## Overview
The Report page generates PDF documents for three languages: English, Sinhala, and Tamil. The CSS defines layout, styling, and print specifications for the report generation.

---

## 1. Main CSS File: `report.css`

### Core Container Styles

#### `.report-container`
- **Purpose**: Main wrapper for entire report
- **Display**: Flexbox (column direction)
- **Properties**:
  - `display: flex`
  - `flex-direction: column`
  - `align-items: center`
  - `justify-content: center`
  - `padding: 10px`
  - `background: white`

#### `.report-section`
- **Purpose**: Individual report section (content area for each language)
- **Max Width**: 11cm (A4 width reference)
- **Min Height**: 17cm (A4 proportions reference)
- **Properties**:
  - `display: flex`
  - `flex-direction: column`
  - `justify-content: space-between`
  - `background: white`
  - `padding: 2px`
  - `page-break-after: always` (Print style)
  - `margin-top: 0.5cm`

#### `.component`
- **Purpose**: Individual component wrapper within a section
- **Properties**:
  - `display: flex`
  - `flex-direction: column`
  - `align-items: center`
  - `justify-content: center`
  - `width: 100%`
  - `background: white`
  - `margin-bottom: 0.05cm`

**Component Margin Adjustments**:
- First component: `margin-bottom: 0`
- Last component: `margin-top: -0.05cm`

## 3. Print Media Styles

### Core Print Rules
```css
@media print {
  body {
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
    margin: 0;
    padding: 0;
  }
}
```

**Purpose**: Preserve colors and formatting during PDF generation

### Report Container Print Styles
```css
.report-container {
  padding: 0;
  margin: 0;
  display: block !important;
}
```

### Report Section Print Styles
```css
.report-section {
  break-inside: avoid;
  page-break-inside: avoid;
  page-break-after: always;
  margin: 0;
  padding: 0.5cm;
  min-height: auto;
  max-width: none;
  width: 100%;
  background: white !important;
}

.report-section:last-child {
  page-break-after: avoid;
}
```

### Hidden Elements During Print
- `.report-button-container`
- `.individual-download-buttons`
- `.print-buttons`
- `.combined-download-buttons`
- `.status-generating`

All set to `display: none !important;`

### Universal Print Color Preservation
```css
* {
  -webkit-print-color-adjust: exact !important;
  print-color-adjust: exact !important;
}
```

### Image Print Handling
```css
img {
  max-width: 100% !important;
  height: auto !important;
}
```

---

## 4. Screen-Only Styles

```css
@media screen {
  .print-only {
    display: none;
  }
}
```

---

## 5. PDF Generation & Sizing

### How PDF Sizes are Calculated

The Report page uses **dynamic sizing** based on captured content:

#### HTML to Canvas Conversion
1. **Capture**: `html2canvas()` converts HTML element to PNG
   - Scale: 4x for high quality
   - Options: `useCORS: true`, `backgroundColor: white`

2. **Canvas Dimensions**:
   - Width: `canvas.width` (in pixels)
   - Height: `canvas.height` (in pixels)

3. **Conversion to MM** (at 96 DPI):
   ```javascript
   mmWidth = (pxWidth * 0.264583) / scale
   mmHeight = (pxHeight * 0.264583) / scale
   ```
   - `0.264583` = conversion factor (96 DPI to mm)
   - Divided by `scale` (4) to undo the 4x scaling

#### PDF Creation
```javascript
const pdf = new jsPDF({
  orientation: "portrait",
  unit: "mm",
  format: [mmWidth, mmHeight],  // Dynamic page size!
  compress: true,
});
```

**Key Point**: Page size is **NOT fixed to A4** - it's dynamically sized based on captured content

---

## 6. PDF Output Types

### Individual Language PDFs
**Filename Format**: `Newspaper-Results-{language}-{date}.pdf`

**Example Files**:
- `Newspaper-Results-english-2026-02-12.pdf`
- `Newspaper-Results-sinhala-2026-02-12.pdf`
- `Newspaper-Results-tamil-2026-02-12.pdf`

**Generation Method**:
- One language section captured via `html2canvas`
- Scale: 4x
- Compression: enabled
- Single page PDF
- Page dimensions: Language-specific width × dynamic height

**Page Sizes**:
- English: 108mm width × 170-200mm height
- Sinhala: 93mm width × 180-220mm height
- Tamil: 108mm width × 190-230mm height

### Combined All-Languages PDF
**Filename Format**: `Newspaper-Results-{date}.pdf`

**Example File**:
- `Newspaper-Results-2026-02-12.pdf`

**Generation Method**:
- All 3 languages captured sequentially (English → Sinhala → Tamil)
- Each language on a separate page with **different dimensions**
- Scale: 4x per page
- Compression: enabled
- 3-page PDF total

**Multi-Page Structure**:
- Page 1: English (108mm × ~190mm)
- Page 2: Sinhala (93mm × ~200mm)
- Page 3: Tamil (108mm × ~210mm)

**Important**: Each page has its own dimensions - this is NOT a standard A4 document!

## 7. Actual PDF Dimensions

### Language-Specific Widths

Each language has a **different fixed width** defined in the component CSS files:

| Language | Width | Width (mm) | Height | Notes |
|----------|-------|------------|--------|-------|
| **English** | **10.8cm** | **108mm** | **Dynamic** | Standard width for English components |
| **Sinhala** | **9.3cm** | **93mm** | **Dynamic** | Narrower to accommodate Sinhala script |
| **Tamil** | **10.8cm** | **108mm** | **Dynamic** | Same width as English |

### Visual Dimension Comparison

```
┌─────────────────────────────────────┐
│  ENGLISH (10.8cm / 108mm width)     │
│  ┌─────────────────────────────┐   │
│  │ Header                      │   │
│  │ Kapruka                     │   │ ~170-200mm
│  │ Lagna Wasana                │   │ (dynamic)
│  │ ...                         │   │
│  │ Footer                      │   │
│  └─────────────────────────────┘   │
└─────────────────────────────────────┘

┌───────────────────────────────┐
│  SINHALA (9.3cm / 93mm width) │
│  ┌─────────────────────────┐  │
│  │ Header (Sinhala)        │  │
│  │ Kapruka                 │  │ ~180-220mm
│  │ Lagna Wasana            │  │ (dynamic)
│  │ ...                     │  │
│  │ Footer                  │  │
│  └─────────────────────────┘  │
└───────────────────────────────┘

┌─────────────────────────────────────┐
│  TAMIL (10.8cm / 108mm width)       │
│  ┌─────────────────────────────┐   │
│  │ Header (Tamil)              │   │
│  │ Kapruka                     │   │ ~190-230mm
│  │ Lagna Wasana                │   │ (dynamic)
│  │ ...                         │   │
│  │ Footer                      │   │
│  └─────────────────────────────┘   │
└─────────────────────────────────────┘
```

### Width Implementation in CSS

#### English Components:
```css
/* header.css, kapruka.css, lagna.css, etc. */
.image-container, .kapruka-ticket-container, .lagna-ticket-container {
  width: 10.8cm;
  height: auto;
}

.header-logo {
  width: 10.8cm;
  height: 100%;
}
```

#### Sinhala Components:
```css
/* headersin.css, kapruka.css, lagna.css, etc. */
.image-container1, .kapruka-ticket-containersn, .lagna-ticket-containersn {
  width: 9.3cm;
  height: auto;
}

.headersin-logo {
  width: 9.3cm;
  height: 100%;
}
```

#### Tamil Components:
```css
/* headerTamil.css, kapruka.css (Tamil variant) */
.image-container2, .shanida-ticket-container-tm {
  width: 10.8cm;
  height: auto;
}

.headertamil-logo {
  width: 10.8cm;
  height: 100%;
}
```

### Dynamic Height Calculation

Heights vary based on:
1. **Text content length** (Sinhala/Tamil scripts render differently than English)
2. **Number of lottery results** in each component
3. **Font sizes and line-height** of individual components
4. **Component spacing** (0.05cm margin-bottom per component)

### Typical PDF Dimensions (After Generation)

Based on the component widths and dynamic content:

| Language | Width (mm) | Approx. Height (mm) | Typical Page Size |
|----------|------------|---------------------|-------------------|
| **English** | **108mm** | **170-200mm** | Custom (10.8cm × 17-20cm) |
| **Sinhala** | **93mm** | **180-220mm** | Custom (9.3cm × 18-22cm) |
| **Tamil** | **108mm** | **190-230mm** | Custom (10.8cm × 19-23cm) |

**Note**: 
- Width: Fixed per language (10.8cm for English/Tamil, 9.3cm for Sinhala)
- Height: Dynamically calculated from actual rendered HTML content
- PDF generation uses `format: [mmWidth, mmHeight]` - NOT standard A4
- 1cm = 10mm conversion

### Print Priority
Print styles use `!important` to ensure:
- Color preservation: `-webkit-print-color-adjust: exact !important`
- Layout control: `page-break-*` properties
- Element visibility: `display: none !important` for UI elements

---

## 12. Summary Table

| Category | Details |
|----------|---------|
| **English Width** | **10.8cm (108mm)** |
| **Sinhala Width** | **9.3cm (93mm)** |
| **Tamil Width** | **10.8cm (108mm)** |
| **Reference Height** | 17cm minimum (A4 proportions) |
| **Actual Height** | Dynamically calculated from content |
| **Component Spacing** | 0.05cm margin-bottom |
| **HTML2Canvas Scale** | 4x (for high quality) |
| **Size Conversion** | (px × 0.264583) / scale |
| **PDF Orientation** | Portrait |
| **PDF Unit** | Millimeters |
| **PDF Format** | Dynamic `[mmWidth, mmHeight]` |
| **Compression** | Enabled |
| **Page Count** | 1 per language, 3 total in combined |
| **Frontend Library** | jsPDF + html2canvas |
| **Backend Framework** | Laravel 10.x + Inertia.js |onst element = elementRef.current;
  element.style.opacity = "1";
  element.style.display = "block";

  await new Promise((resolve) => setTimeout(resolve, 100));

  // Capture HTML element as canvas with 4x scale for high quality
  const canvas = await html2canvas(element, {
    scale: scale,                    // 4x for high quality
    useCORS: true,
    logging: false,
    willReadFrequently: true,
    backgroundColor: "white",
    scrollX: 0,
    scrollY: 0,
  });

  const imgData = canvas.toDataURL("image/png");
  const pxWidth = canvas.width;      // Canvas width in pixels
  const pxHeight = canvas.height;    // Canvas height in pixels

  // Convert px -> mm at 96dpi, then undo the html2canvas scale
  const mmWidth = (pxWidth * 0.264583) / scale;
  const mmHeight = (pxHeight * 0.264583) / scale;

  return {
    imgData,
    mmWidth,
    mmHeight,
  };
};
```


### Report Component Structure

```javascript
// Report sections with refs for PDF capture
return (
  <>
    <Head title="Report" />
    <div className="report-container">
      {/* English Report - 10.8cm width */}
      <div ref={englishReportRef} className="section-padding report-section">
        {renderSection([
          HeaderEnglish,
          KaprukaEnglish,
          LagnaWasanaEnglish,
          AdakotipathiEnglish,
          ShanidaEnglish,
          SuperballEnglish,
          isSasiriDay && SasiriEnglish,
          isJayasampathaDay && JayasampathaEnglish,
          SupiridanaEnglish,
          FooterEnglish,
        ])}
      </div>

      {/* Sinhala Report - 9.3cm width */}
      <div ref={sinhalaReportRef} className="section-padding report-section">
        {renderSection([
          HeaderSinhala,
          KaprukaSinhala,
          LagnaWasanaSinhala,
          AdakotipathiSinhala,
          ShanidaSinhala,
          SuperballSinhala,
          isSasiriDay && SasiriSinhala,
          isJayasampathaDay && JayasampathaSinhala,
          SupiridanaSinhala,
          FooterSinhala,
        ])}
      </div>

      {/* Tamil Report - 10.8cm width */}
      <div ref={tamilReportRef} className="section-padding report-section">
        {renderSection([
          HeaderTamil,
          KaprukaTamil,
          LagnaWasanaTamil,
          AdakotipathiTamil,
          ShanidaTamil,
          SuperballTamil,
          isSasiriDay && SasiriTamil,
          isJayasampathaDay && JayasampathaTamil,
          SupiridanaTamil,
          FooterTamil,
        ])}
      </div>

## 10. Component Dimension Reference

### Width Variations by Language

Each lottery component has language-specific CSS classes with different widths:

| Component | English Width | Sinhala Width | Tamil Width |
|-----------|---------------|---------------|-------------|
| Header | 10.8cm | 9.3cm | 10.8cm |
| Kapruka | 10.8cm | 9.3cm | 10.8cm |
| Lagna Wasana | 10.8cm | 9.3cm | 10.8cm |
| Adakotipathi | 10.8cm | 9.3cm | 10.8cm |
| Shanida | 10.8cm | 9.3cm | 10.8cm |
| Superball | 10.8cm | 9.3cm | 10.8cm |
| Sasiri | 10.8cm | 9.3cm | 10.8cm |
| Jayasampatha | 10.8cm | 9.3cm | 10.8cm |
| Supiridana | 10.8cm | 9.3cm | 10.8cm |
| Footer | 10.8cm | 9.3cm | 10.8cm |

### Standard Component Height

Most components have:
- **max-height**: 2.4cm
- **height**: auto (adjusts to content)

## 11. Summary Table

| Category | Details |
|----------|---------|
| **Container Width** | 11cm (A4 reference) |
| **Reference Height** | 17cm (A4 proportions) |
| **Component Spacing** | 0.05cm margin-bottom |
| **Size Conversion** | px to mm at 96 DPI |
| **PDF Orientation** | Portrait |
| **PDF Unit** | Millimeters |
| *13. Dimension Calculation Formula

### Width Calculation
```
English/Tamil Width: 10.8cm = 108mm
Sinhala Width:       9.3cm  = 93mm
```

### Height Calculation
```javascript
// Step 1: HTML2Canvas captures at 4x scale
canvas.width  = actualWidth × 4
canvas.height = actualHeight × 4

// Step 2: Convert to millimeters
mmWidth  = (canvas.width  × 0.264583) / 4
mmHeight = (canvas.height × 0.264583) / 4

// Where 0.264583 is the conversion factor for 96 DPI to mm
// (25.4mm per inch / 96 pixels per inch = 0.264583)
```

### Example Calculation
```
If Sinhala content renders to 352px wide × 800px tall at 1x scale:

At 4x scale:
- Canvas width  = 352 × 4 = 1408px
- Canvas height = 800 × 4 = 3200px

Convert to mm:
- mmWidth  = (1408 × 0.264583) / 4 = 93.04mm ≈ 9.3cm ✓
- mmHeight = (3200 × 0.264583) / 4 = 211.67mm ≈ 21.2cm
```


## 15. Notes for Developers

### Critical Implementation Details

1. **Language-specific widths are fixed**:
   - English/Tamil: Always 10.8cm (108mm)
   - Sinhala: Always 9.3cm (93mm)
   - This is NOT negotiable - it's set in component CSS

2. **PDF sizing is content-driven**: 
   - Heights are calculated from actual rendered HTML, not fixed values
   - Each language may have different heights in the same PDF

3. **High quality rendering**: 
   - 4x scale captures fine details
   - Compress flag reduces file size
   - Balance between quality and file size

4. **Dynamic language-specific spacing**: 
   - Sinhala script requires different dimensions
   - Tamil and English can share dimensions but content length differs

5. **Print styles are critical**: 
   - `print-color-adjust: exact` essential for color lottery results preservation
   - Ensures colors don't get converted to grayscale

6. **PDF generation is frontend-only**:
   - No server-side PDF generation
   - Backend only serves data via API
   - All PDF work happens in browser using jsPDF + html2canvas

7. **Each page has its own dimensions**:
   - Combined PDF uses `addPage([width, height])` for each language
   - NOT a standard A4 multi-page document

8. **Conversion factor**:
   - 0.264583 mm/px at 96 DPI
   - This is the standard web DPI conversion
   - Must divide by scale (4) after conversion

### Common Pitfalls

❌ **Don't**: Force all pages to A4 (210mm × 297mm)  
✅ **Do**: Use dynamic dimensions from captured content

❌ **Don't**: Use same width for all languages  
✅ **Do**: Respect language-specific widths (10.8cm / 9.3cm)

❌ **Don't**: Generate PDFs on backend  
✅ **Do**: Generate PDFs on frontend with current architecture

❌ **Don't**: Use low scale (1x or 2x) for quality  
✅ **Do**: Use 4x scale for production-quality PDFs
1. **PDF sizing is content-driven**: Dimensions are calculated from actual rendered HTML, not fixed values
2. **High quality rendering**: 4x scale captures fine details; compress flag reduces file size
3. **Dynamic language-specific spacing**: Sinhala and Tamil may require different heights due to character rendering
4. **Print styles critical**: `print-color-adjust: exact` essential for color lottery results preservation
5. **No fixed page breaks**: Content flows naturally; `page-break-after: always` only on sections
6. **Responsive buttons**: Grid uses flexbox principles for responsive layout

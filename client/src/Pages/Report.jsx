import { Head } from "@inertiajs/react";
import axios from "axios";
import html2canvas from "html2canvas";
import { jsPDF } from "jspdf";
import { useEffect, useRef, useState } from "react";
import "../css/report.css";

// English Components
import AdakotipathiEnglish from "../Components/adakotipathiEnglish";
import FooterEnglish from "../Components/footerEnglish";
import HeaderEnglish from "../Components/headerEnglish";
import JayasampathaEnglish from "../Components/jayasampathaEnglish";
// import JayodaEnglish from "../Components/jayodaEnglish";
import KaprukaEnglish from "../Components/kaprukaEnglish";
import LagnaWasanaEnglish from "../Components/lagnawasanaEnglish";
import SasiriEnglish from "../Components/sasiriEnglish";
import ShanidaEnglish from "../Components/shanidaEnglish";
import SuperballEnglish from "../Components/superballEnglish";
import SupiridanaEnglish from "../Components/supiridanaEnglish";

// Sinhala Components
import AdakotipathiSinhala from "../Components/adakotipathiSinhala";
import FooterSinhala from "../Components/footerSinhala";
import HeaderSinhala from "../Components/headerSinhala";
import JayasampathaSinhala from "../Components/jayasampathaSinhala";
// import JayodaSinhala from "../Components/jayodaSinhala";
import KaprukaSinhala from "../Components/kaprukaSinhala";
import LagnaWasanaSinhala from "../Components/lagnawasanawaSinhala";
import SasiriSinhala from "../Components/sasiriSinhala";
import ShanidaSinhala from "../Components/shanidaSinhala";
import SuperballSinhala from "../Components/superballSinhala";
import SupiridanaSinhala from "../Components/supiridanaSinhala";

// Tamil Components
import AdakotipathiTamil from "../Components/adakotipathiTamil";
import FooterTamil from "../Components/footerTamil";
import HeaderTamil from "../Components/headerTamil";
import JayasampathaTamil from "../Components/jayasampathaTamil";
// import JayodaTamil from "../Components/jayodaTamil";
import KaprukaTamil from "../Components/kaprukaTamil";
import LagnaWasanaTamil from "../Components/lagnawasanawaTamil";
import SasiriTamil from "../Components/sasiriTamil";
import ShanidaTamil from "../Components/shanidaTamil";
import SuperballTamil from "../Components/superballTamil";
import SupiridanaTamil from "../Components/supiridanaTamil";

const Report = () => {
  const englishReportRef = useRef(null);
  const sinhalaReportRef = useRef(null);
  const tamilReportRef = useRef(null);
  const [isGenerating, setIsGenerating] = useState(false);
  const [generatingLanguage, setGeneratingLanguage] = useState('');
  const [reportDateIso, setReportDateIso] = useState('');

  useEffect(() => {
    const fetchReportDateFromXml = async () => {
      try {
        // Use the same source as the headers: latest Kapruka record date (from XML)
        const response = await axios.get("/api/lottery", { params: { name: "Kapruka" } });
        if (response.data && response.data.date) {
          const date = new Date(response.data.date);
          const year = date.getFullYear();
          const month = String(date.getMonth() + 1).padStart(2, "0");
          const day = String(date.getDate()).padStart(2, "0");
          setReportDateIso(`${year}-${month}-${day}`);
          return;
        }
      } catch (error) {
        console.error("Error fetching report date:", error);
      }

      // Fallback to today's date if API fails
      const fallback = new Date();
      const year = fallback.getFullYear();
      const month = String(fallback.getMonth() + 1).padStart(2, "0");
      const day = String(fallback.getDate()).padStart(2, "0");
      setReportDateIso(`${year}-${month}-${day}`);
    };

    fetchReportDateFromXml();
  }, []);

  const captureElementAsPng = async (elementRef, scale = 4) => {
    if (!elementRef.current) {
      throw new Error('Missing element ref for PDF capture');
    }

    const element = elementRef.current;
    element.style.opacity = "1";
    element.style.display = "block";

    await new Promise((resolve) => setTimeout(resolve, 100));

    const canvas = await html2canvas(element, {
      scale: scale,
      useCORS: true,
      logging: false,
      willReadFrequently: true,
      backgroundColor: "white",
      scrollX: 0,
      scrollY: 0,
    });

    const imgData = canvas.toDataURL("image/png");
    const pxWidth = canvas.width;
    const pxHeight = canvas.height;

    // Convert px -> mm at 96dpi, then undo the html2canvas scale.
    const mmWidth = (pxWidth * 0.264583) / scale;
    const mmHeight = (pxHeight * 0.264583) / scale;

    return {
      imgData,
      mmWidth,
      mmHeight,
    };
  };

  const today = new Date().getDay();
  // const isMondayOrWednesday = today === 1 || today === 3; // Jayoda temporarily removed
  // const isSasiriDay = [0, 2, 4, 5, 6].includes(today); // Sasiri now shows all 7 days
  const isSasiriDay = true; // Sasiri shows all days of the week
  const isJayasampathaDay = true;

  // High quality single language PDF generation
  const generateHighQualitySingleLanguagePDF = async (elementRef, languageName) => {
    if (!elementRef.current || isGenerating) return;

    setIsGenerating(true);
    setGeneratingLanguage(`${languageName} HQ`);

    try {
      // Higher scale for better quality
      const scale = 4;
      const { imgData, mmWidth, mmHeight } = await captureElementAsPng(elementRef, scale);

      // IMPORTANT: Do NOT force A4. Use the captured content size as the PDF page size.
      const pdf = new jsPDF({
        orientation: "portrait",
        unit: "mm",
        format: [mmWidth, mmHeight],
        compress: true,
      });

      pdf.addImage(imgData, "PNG", 0, 0, mmWidth, mmHeight);

      const fileDate = reportDateIso || new Date().toISOString().split("T")[0];
      pdf.save(`Newspaper-Results-HQ-${languageName.toLowerCase()}-${fileDate}.pdf`);
    } catch (error) {
      console.error(`Error generating ${languageName} HQ PDF:`, error);
      alert(`Error generating ${languageName} HQ PDF. Please try again.`);
    } finally {
      setIsGenerating(false);
      setGeneratingLanguage('');
    }
  };

  // PDF generation
  const generateHighQualityPDF = async () => {
    if (isGenerating) return;

    setIsGenerating(true);
    setGeneratingLanguage('High Quality');

    try {
      const scale = 4; // Higher quality

      const english = await captureElementAsPng(englishReportRef, scale);
      const pdf = new jsPDF({
        orientation: "portrait",
        unit: "mm",
        format: [english.mmWidth, english.mmHeight],
        compress: true,
      });
      pdf.addImage(english.imgData, "PNG", 0, 0, english.mmWidth, english.mmHeight);

      const sinhala = await captureElementAsPng(sinhalaReportRef, scale);
      pdf.addPage([sinhala.mmWidth, sinhala.mmHeight]);
      pdf.addImage(sinhala.imgData, "PNG", 0, 0, sinhala.mmWidth, sinhala.mmHeight);

      const tamil = await captureElementAsPng(tamilReportRef, scale);
      pdf.addPage([tamil.mmWidth, tamil.mmHeight]);
      pdf.addImage(tamil.imgData, "PNG", 0, 0, tamil.mmWidth, tamil.mmHeight);

      const fileDate = reportDateIso || new Date().toISOString().split("T")[0];
      pdf.save(`Newspaper-Results-hq-${fileDate}.pdf`);
    } catch (error) {
      console.error('Error generating high quality PDF:', error);
      alert('Error generating PDF. Please try again.');
    } finally {
      setIsGenerating(false);
      setGeneratingLanguage('');
    }
  };

  const renderSection = (components) => {
    const validComponents = components.filter(Boolean);
    return (
      <div className="report-section">
        {validComponents.map((Component, index) => (
          <div
            key={index}
            className={`component ${index === 0 ? 'first-component' : ''} ${index === validComponents.length - 1 ? 'last-component' : ''
              }`}
          >
            <Component />
          </div>
        ))}
      </div>
    );
  };

  return (
    <>
      <Head title="Report" />
      <div className="report-container">
        {/* English Report */}
        <div ref={englishReportRef} className="section-padding report-section">
        {renderSection([
          HeaderEnglish,
          KaprukaEnglish,
          LagnaWasanaEnglish,
          AdakotipathiEnglish,
          ShanidaEnglish,
          SuperballEnglish,
          isSasiriDay && SasiriEnglish,
          // isMondayOrWednesday && JayodaEnglish, // Jayoda temporarily removed
          isJayasampathaDay && JayasampathaEnglish,
          SupiridanaEnglish,
          FooterEnglish,
        ])}
      </div>

      <br />

      {/* Sinhala Report */}
      <div ref={sinhalaReportRef} className="section-padding report-section">
        {renderSection([
          HeaderSinhala,
          KaprukaSinhala,
          LagnaWasanaSinhala,
          AdakotipathiSinhala,
          ShanidaSinhala,
          SuperballSinhala,
          isSasiriDay && SasiriSinhala,
          // isMondayOrWednesday && JayodaSinhala,
          isJayasampathaDay && JayasampathaSinhala,
          SupiridanaSinhala,
          FooterSinhala,
        ])}
      </div>

      <br />

      {/* Tamil Report */}
      <div ref={tamilReportRef} className="section-padding report-section">
        {renderSection([
          HeaderTamil,
          KaprukaTamil,
          LagnaWasanaTamil,
          AdakotipathiTamil,
          ShanidaTamil,
          SuperballTamil,
          isSasiriDay && SasiriTamil,
          // isMondayOrWednesday && JayodaTamil, 
          isJayasampathaDay && JayasampathaTamil,
          SupiridanaTamil,
          FooterTamil,
        ])}
      </div>

      {/* Download Control Panel */}
      <div className="report-button-container">
        <div className="download-control-panel">
          {/* Reports Generation Section */}
          <div className="report-section-container">
            <h3 className="section-title high-quality">Download Reports</h3>

            {/* High Quality Language Buttons */}
            <div className="language-buttons-grid">
              <button
                onClick={() => generateHighQualitySingleLanguagePDF(englishReportRef, 'English')}
                disabled={isGenerating}
                className={`language-button hq english ${isGenerating && generatingLanguage === 'English HQ' ? 'generating' : ''}`}
                style={{
                  cursor: isGenerating ? 'not-allowed' : 'pointer',
                  opacity: isGenerating && generatingLanguage !== 'English HQ' ? 0.6 : 1
                }}
              >
                {isGenerating && generatingLanguage === 'English HQ' ? '⏳ Generating...' : 'English'}
              </button>
              <button
                onClick={() => generateHighQualitySingleLanguagePDF(sinhalaReportRef, 'Sinhala')}
                disabled={isGenerating}
                className={`language-button hq sinhala ${isGenerating && generatingLanguage === 'Sinhala HQ' ? 'generating' : ''}`}
                style={{
                  cursor: isGenerating ? 'not-allowed' : 'pointer',
                  opacity: isGenerating && generatingLanguage !== 'Sinhala HQ' ? 0.6 : 1
                }}
              >
                {isGenerating && generatingLanguage === 'Sinhala HQ' ? '⏳ Generating...' : 'Sinhala'}
              </button>
              <button
                onClick={() => generateHighQualitySingleLanguagePDF(tamilReportRef, 'Tamil')}
                disabled={isGenerating}
                className={`language-button hq tamil ${isGenerating && generatingLanguage === 'Tamil HQ' ? 'generating' : ''}`}
                style={{
                  cursor: isGenerating ? 'not-allowed' : 'pointer',
                  opacity: isGenerating && generatingLanguage !== 'Tamil HQ' ? 0.6 : 1
                }}
              >
                {isGenerating && generatingLanguage === 'Tamil HQ' ? '⏳ Generating...' : 'Tamil'}
              </button>
            </div>

            {/* All Reports Button */}
            <button
              onClick={generateHighQualityPDF}
              disabled={isGenerating}
              className={`all-reports-button high-quality ${isGenerating && generatingLanguage === 'High Quality' ? 'generating' : ''}`}
              style={{
                cursor: isGenerating ? 'not-allowed' : 'pointer',
                opacity: isGenerating && generatingLanguage !== 'High Quality' ? 0.6 : 1
              }}
            >
              {isGenerating && generatingLanguage === 'High Quality'
                ? '⏳ Generating All Reports...'
                : 'All Reports'}
            </button>
          </div>
        </div>
      </div>

      {/* Status Display */}
      {isGenerating && (
        <div className="status-panel">
          <div className="status-display">
            <div className="status-title">
              🔄 Generating {generatingLanguage} PDF...
            </div>
            <div className="status-subtitle">
              Please wait, this will only take a moment...
            </div>
          </div>
        </div>
      )}
      </div>
    </>
  );
};

export default Report;

import html2canvas from "html2canvas";
import { jsPDF } from "jspdf";
import { useRef, useState } from "react";
import "../../css/report.css";

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
      const pdf = new jsPDF({
        orientation: "portrait",
        unit: "mm",
        format: "a4",
        compress: true,
      });

      const element = elementRef.current;
      element.style.opacity = "1";
      element.style.display = "block";

      await new Promise((resolve) => setTimeout(resolve, 100));

      // Higher scale for better quality
      const scale = 4;
      
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
      const mmWidth = (pxWidth * 0.264583) / scale;
      const mmHeight = (pxHeight * 0.264583) / scale;

      pdf.addImage(imgData, "PNG", 10, 10, mmWidth, mmHeight);

      const today = new Date().toISOString().split("T")[0];
      pdf.save(`Newspaper-Results-HQ-${languageName.toLowerCase()}-${today}.pdf`);
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
      const pdf = new jsPDF({
        orientation: "portrait",
        unit: "mm",
        format: "a4",
        compress: true,
      });

      const highQualityCapture = async (elementRef, addPage) => {
        if (!elementRef.current) return;

        const element = elementRef.current;
        element.style.opacity = "1";
        element.style.display = "block";

        await new Promise((resolve) => setTimeout(resolve, 100));

        const scale = 4; // Higher quality

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
        const mmWidth = (pxWidth * 0.264583) / scale;
        const mmHeight = (pxHeight * 0.264583) / scale;

        pdf.addImage(imgData, "PNG", 10, 10, mmWidth, mmHeight);

        if (addPage) {
          pdf.addPage();
        }
      };

      await highQualityCapture(englishReportRef, true);
      await highQualityCapture(sinhalaReportRef, true);
      await highQualityCapture(tamilReportRef, false);

      const today = new Date().toISOString().split("T")[0];
      pdf.save(`Newspaper-Results-hq-${today}.pdf`);
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
            className={`component ${index === 0 ? 'first-component' : ''} ${
              index === validComponents.length - 1 ? 'last-component' : ''
            }`}
          >
            <Component />
          </div>
        ))}
      </div>
    );
  };

  return (
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
          // isMondayOrWednesday && JayodaSinhala, // Jayoda temporarily removed
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
          // isMondayOrWednesday && JayodaTamil, // Jayoda temporarily removed
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
  );
};

export default Report;

import axios from "axios";
import { useEffect, useState } from "react";
import "../css/adakotipathi.css";

const AdakotipathiTamil = ({ name = "Ada kotipathi" }) => {
  const [lottery, setLottery] = useState({
    number: null,
    color: null,
    ball1: null,
    ball2: null,
    ball3: null,
    ball4: null,
    ball5: null,
    next_super: null,
    // special4: null, // Temporarily commented out
  });

  useEffect(() => {
    const fetchLottery = async () => {
      try {
        const response = await axios.get(`/api/lottery`, { params: { name } });
        setLottery(response.data);
      } catch (error) {
        console.error("Error fetching lottery data:", error);
      }
    };

    fetchLottery();
  }, [name]);


  const balls = [lottery.ball1, lottery.ball2, lottery.ball3, lottery.ball4, lottery.ball5].filter(
    (ball) => ball !== null
  );

  const translateColor = (color) => {
    const colorLower = color?.trim()?.toLowerCase() || "";

    if (colorLower === "green") return "பச்சை";
    if (colorLower === "light green") return "வெளிர் பச்சை";
    if (colorLower === "dark green") return "அடர் பச்சை";
    if (colorLower === "red") return "சிகப்பு";
    if (colorLower === "blue") return "நீலம்";
    if (colorLower === "light blue") return "இள நீலம்";
    if (colorLower === "dark blue") return "அடர் நீலம்";
    if (colorLower === "orange") return "செம்மஞ்சள்";
    if (colorLower === "pink") return "இளஞ்சிகப்பு";
    if (colorLower === "light pink") return "வெளிர் இளஞ்சிகப்பு";
    if (colorLower === "purple") return "ஊதா";
    if (colorLower === "yellow") return "மஞ்சள்";
    if (colorLower === "brown") return "பழுப்பு";
    if (colorLower === "light brown") return "வெளிர் பழுப்பு";
    if (colorLower === "dark brown") return "அடர் பழுப்பு";

    return color;
  };

  const formatCurrency = (amount) => {
    return "ரூ. " + Number(amount).toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  };


  return (
    <div className="adakotipathi-ticket-container">
      <div className="adakotipathi-ticket-card">
        <div className="adakotipathi-ticket-header">
          <div className="adakotipathi-ticket-logo-container">
            <img
              src="/images/logo/adakotipathi.png"
              alt={name}
              className="adakotipathi-ticket-logo"
            />
          </div>
          <div className="adakotipathi-ticket-draw-number-container-tm">

            <div className="adakotipathi-ticket-draw-number-text-tm">
              வெற்றி வாரம்
            </div>
            <div className="adakotipathi-ticket-draw-number-text1-tm">
              {lottery.number || "Loading..."}
            </div>


            <div className="adakotipathi-ticket-colour-text-tm">
              வர்ணம்
            </div>
            <div className="adakotipathi-ticket-colour-text1-tm">
              {translateColor(lottery.color) || "Loading..."}
            </div>

            <div className="adakotipathi-ticket-winning-numbers">
              <div className="adakotipathi-ticket-winning-numbers-title-tm">
                <span>----- வெற்றி எண்கள் -----</span>
                <span>ஆங்கில எழுத்து</span>
              </div>
              <div className="adakotipathi-ticket-winning-numbers-container-tm">
                {balls.length > 0
                  ? balls.map((ball, index) => (

                    <div key={index} className="adakotipathi-ticket-winning-number">
                      <div className="adakotipathi-ticket-winning-number-text">
                        {ball}
                      </div>
                    </div>

                  ))
                  : "Loading..."}
              </div>
            </div>
            <div className="adakotipathi-ticket-special">
              <div className="adakotipathi-ticket-bottomtm">
                அடுத்த சுப்பர் ஐக்பொட் : <div className="adakotipathi-ticket-bottomtm-txt">
                  {formatCurrency(lottery.next_super) || "Loading..."}
                </div>
              </div>
              {/* Special Numbers Section - Temporarily commented out */}
              {/*
                {lottery.special4 && (
                <div className="adakotipathi-ticket-special-prize-container-tm">
                  <img
                    src="/images/sc.png"
                    alt="Special Prize"
                    className="adakotipathi-ticket-special-prize-icon"
                  />

                  <table className="special-prize-table-tm">
                    <thead>
                      <tr>
                        <th>விலை</th>
                        <th>விசேட இலக்கம்</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>ரூ. 100,000/-</td>
                        <td className="lagna-special-txt">{lottery.special4}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              )}
              */}

            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default AdakotipathiTamil;

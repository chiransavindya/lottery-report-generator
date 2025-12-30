import axios from "axios";
import { useEffect, useState } from "react";
import "../css/supiridana.css";

const SupiridanaTamil = ({ name = "Supiri Dhana Sampatha" }) => {
  const [lottery, setLottery] = useState({
    number: null,
    color: null,
    ball1: null,
    ball2: null,
    ball3: null,
    ball4: null,
    ball5: null,
    ball6: null,
    ball7: null,
    next_super: null,
    // special1: null, // Temporarily removed
    // special2: null, // Temporarily removed
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


  const translateColor = (color) => {
    const colorLower = color?.trim()?.toLowerCase() || "";

    if (colorLower === "green") return "பச்சை";
    if (colorLower === "light green") return "வெளிர் பச்சை";
    if (colorLower === "dark green") return "அடர் பச்சை";
    if (colorLower === "red") return "சிகப்பு";
    if (colorLower === "blue") return "நீலம்";
    if (colorLower === "light blue") return "இளநீலம்";
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


  const balls = [lottery.ball1, lottery.ball2, lottery.ball3, lottery.ball4, lottery.ball5, lottery.ball6, lottery.ball7].filter(
    (ball) => ball !== null
  );

  const formatCurrency = (amount) => {
    return "ரூ. " + Number(amount).toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  };

  return (
    <div className="supiridana-ticket-container">
      <div className="supiridana-ticket-card">
        <div className="supiridana-ticket-header">
          <div className="supiridana-ticket-logo-container">
            <img
              src="/images/logo/supiridana.png"
              alt={name}
              className="supiridana-ticket-logo"
            />
          </div>
          <div className="supiridana-ticket-draw-number-containertm">

            <div className="supiridana-ticket-draw-number-text">
              வெற்றி வாரம்
            </div>
            <div className="supiridana-ticket-draw-number-text1-tm">
              {lottery.number || "Loading..."}
            </div>


            <div className="supiridana-ticket-colour-text">
              வர்ணம்
            </div>
            <div className="supiridana-ticket-colour-text1">
              {translateColor(lottery.color) || "Loading..."}
            </div>


            <div className="supiridana-ticket-winning-numbers">
              <div className="supiridana-ticket-winning-numbers-titletm">
                <span>ஆங்கில எழுத்து</span>
                <span>-----வெற்றி எண்கள்-----</span>
              </div>

              {/* Main container that holds two sections in one row */}
              <div className="supiridana-ticket-all-numbers-row-tm">

                {/* English Letter Container */}
                <div className="supiridana-ticket-english-letter-container-tm">
                  {balls.length > 0 && balls[0] && (
                    <div className="supiridana-ticket-winning-number-tm">
                      <div className="supiridana-ticket-winning-number-text">
                        {balls[0]}
                      </div>
                    </div>
                  )}
                </div>

                {/* Winning Numbers Container (includes former super number) */}
                <div className="supiridana-ticket-winning-numbers-container-tm">
                  {balls.length > 1
                    ? balls.slice(1).map((ball, index) => (
                      <div key={index} className="supiridana-ticket-winning-number-tm">
                        <div className="supiridana-ticket-winning-number-text">
                          {ball}
                        </div>
                      </div>
                    ))
                    : "Loading..."}
                </div>

              </div>
            </div>
            <div className="supiridana-ticket-special">
              <div className="supiridana-ticket-bottomtm">
                அடுத்த சுப்பர் ஐக்பொட் :
                <div className="supiridana-ticket-bottomtm-txt">
                  {formatCurrency(lottery.next_super) || "Loading..."}
                </div>
              </div>
              {/* Special Numbers Section - Temporarily removed */}
              {/**
            {(lottery.special1 || lottery.special2) && (
  <div className="supiridana-ticket-special-prize-container-tm">
    <img
      src="/images/sc.png"
      alt="Special Prize"
      className="supiridana-ticket-special-prize-icon"
    />

    <table className="special-prize-table-tm">
      <thead>
        <tr>
          <th>பரிசு</th>
          <th>விசேட இலக்கம்</th>
        </tr>
      </thead>
      <tbody>
        {lottery.special1 && (
          <tr>
            <td>ரூ 50,000/-</td>
            <td className="lagna-special-txt">{lottery.special1}</td>
          </tr>
        )}
        {lottery.special2 && (
          <tr>
            <td>{lottery.special2_label || 'ரூ 40/-'}</td>
            <td className="lagna-special-txt">{lottery.special2}</td>
          </tr>
        )}
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

export default SupiridanaTamil;

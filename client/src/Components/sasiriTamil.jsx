import axios from "axios";
import { useEffect, useState } from "react";
import "../css/sasiri.css";

const SasiriTamil = ({ name = "Sasiri" }) => {
  const [lottery, setLottery] = useState({
    number: null,
    color: null,
    ball1: null,
    ball2: null,
    ball3: null,
    total: null,
    count: null,
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

  // Combine individual balls into an array
  const balls = [lottery.ball1, lottery.ball2, lottery.ball3].filter(
    (ball) => ball !== null
  );

  const translateColor = (color) => {
    const colorLower = color?.toLowerCase().trim() || "";

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

  const formatCurrency = (amount) => {
    return "ரூ. " + Number(amount).toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  };

  const formatCount = (count) => {
    // Pad with leading zero if single digit (e.g., 9 becomes 09)
    return count !== null ? String(count).padStart(2, '0') : "00";
  };


  return (
    <div className="sasiri-ticket-container">
      <div className="sasiri-ticket-card">
        <div className="sasiri-ticket-header">
          <div className="sasiri-ticket-logo-container">
            <img
              src="/images/logo/sasiritamil.png"
              alt={name}
              className="sasiri-ticket-logo"
            />
          </div>
          <div className="sasiri-ticket-draw-number-containertm">

            <div className="sasiri-ticket-draw-number-text-tm">
              வெற்றி வாரம்
            </div>
            <div className="sasiri-ticket-draw-number-text1tm">
              {lottery.number || "Loading..."}
            </div>


            <div className="sasiri-ticket-colour-text-tm">
              வர்ணம்
            </div>
            <div className="sasiri-ticket-colour-text1-tm">
              {translateColor(lottery.color) || "Loading..."}
            </div>

            <div className="sasiri-ticket-winning-numbers">
              <div className="sasiri-ticket-winning-numbers-titletm">
                ---- வெற்றி எண்கள் ----
              </div>
              <div className="sasiri-ticket-winning-numbers-containertm">
                {balls.length > 0
                  ? balls.map((ball, index) => (

                    <div key={index} className="sasiri-ticket-winning-number">
                      <div className="sasiri-ticket-winning-number-text">
                        {ball}
                      </div>
                    </div>

                  ))
                  : "Loading..."}
                <div className="sasiri-ticket-winner-containertm">
                  இன்று இரண்டு இலட்சத்திற்கு அதிபதியானோரின்  <br /> எண்ணிக்கை
                </div>
                <div className="sasiri-ticket-winner-containertm1">
                  {formatCount(lottery.count)}
                </div>
              </div>
            </div>
            <div className="sasiri-ticket-special">
              <div className="sasiri-ticket-bottomtm">

                வெல்லப்பட்ட மொத்த பரிசுத் தொகை :
                <div className="sasiri-ticket-bottomtm-txt">
                  {formatCurrency(lottery.total) || "Loading..."}
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default SasiriTamil;

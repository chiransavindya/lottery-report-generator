import axios from "axios";
import { useEffect, useState } from "react";
import "../css/shanida.css";

const ShanidaTamil = ({ name = "Shanida" }) => {
  const [lottery, setLottery] = useState({
    number: null,
    color: null,
    ball1: null,
    ball2: null,
    ball3: null,
    ball4: null,
    ball5: null,
    next_super: null,
    special1: null,
    special2: null,
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

  const translateLabel = (label) => {
    if (label) {
      return label.replace(/Rs\./g, 'ரூ');
    }
    return label;
  };


  // Combine individual balls into an array
  const balls = [lottery.ball1, lottery.ball2, lottery.ball3, lottery.ball4, lottery.ball5].filter(
    (ball) => ball !== null
  );

  const formatCurrency = (amount) => {
    return "ரூ. " + Number(amount).toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  };

  return (
    <div className="shanida-ticket-container-tm">
      <div className="shanida-ticket-card">
        <div className="shanida-ticket-header">
          <div className="shanida-ticket-logo-container">
            <img
              src="/images/logo/ShanidaTamil.png"
              alt={name}
              className="shanida-ticket-logotm"
            />
          </div>
          <div className="shanida-ticket-draw-number-containertm">

            <div className="shanida-ticket-draw-number-text-tm">
              வெற்றி வாரம்
            </div>
            <div className="shanida-ticket-draw-number-text1-tm">
              {lottery.number || "Loading..."}
            </div>


            <div className="shanida-ticket-colour-text-tm">
              வர்ணம்
            </div>
            <div className="shanida-ticket-colour-text1-tm">
              {translateColor(lottery.color) || "Loading..."}
            </div>

            <div className="shanida-ticket-winning-numbers-tm">
              <div className="shanida-ticket-winning-numbers-titletm">
                <span>வெற்றி எண்கள்</span>
                <span>ஆங்கில எழுத்து</span>
              </div>
              <div className="shanida-ticket-winning-numbers-title1">
                சுப்பர் இலக்கம்
              </div>

              <div className="shanida-ticket-winning-numbers-container-tm">
                {/* Winning Numbers (all except last) */}
                <div className="shanida-ticket-winning-numbers-group-tm">
                  {balls.length > 1
                    ? balls.slice(0, -1).map((ball, index) => (
                      <div key={index} className="shanida-ticket-winning-number-tml">
                        <div className="shanida-ticket-winning-number-text-tm">
                          {ball}
                        </div>
                      </div>
                    ))
                    : "Loading..."}
                </div>

                {/* English Letter (last number) with gap */}
                <div className="shanida-ticket-english-letter-group-tm">
                  {balls.length > 0 && balls[balls.length - 1] && (
                    <div className="shanida-ticket-winning-number-tml shanida-ticket-english-letter-tm">
                      <div className="shanida-ticket-winning-number-text-tm">
                        {balls[balls.length - 1]}
                      </div>
                    </div>
                  )}
                </div>
              </div>
            </div>
            <div className="shanida-ticket-special-tm">
              <div className="shanida-ticket-bottomtm">
                அடுத்த சுப்பர் ஐக்பொட் :
                <div className="shanida-ticket-bottomtm-txt">
                  {formatCurrency(lottery.next_super) || "Loading..."}
                </div>
              </div>
              {/* Special Numbers Section */}
              {(lottery.special1 || lottery.special2 || lottery.special3) && (
                <div className="shanida-ticket-special-prize-container-tm">
                  <img
                    src="/images/sc.png"
                    alt="Special Prize"
                    className="shanida-ticket-special-prize-icon-tm"
                  />

                  <table className="shanida-special-numbers-tm">
                    <thead>
                      <tr>
                        <th>பிரிவு</th>
                        <th>விசேட இலக்கம்</th>
                      </tr>
                    </thead>
                    <tbody>
                      {lottery.special1 && (
                        <tr>
                          <td>{translateLabel(lottery.special1_label) || 'ரூ 50,000/-'}</td>
                          <td className="special-number">{lottery.special1}</td>
                        </tr>
                      )}
                      {lottery.special3 && (
                        <tr>
                          <td>{translateLabel(lottery.special3_label) || 'ரூ 200/-'}</td>
                          <td className="special-number">{lottery.special3}</td>
                        </tr>
                      )}
                      {lottery.special2 && (
                        <tr>
                          <td>{translateLabel(lottery.special2_label) || 'ரூ 40/-'}</td>
                          <td className="special-number">{lottery.special2}</td>
                        </tr>
                      )}
                    </tbody>
                  </table>
                </div>
              )}

            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default ShanidaTamil;

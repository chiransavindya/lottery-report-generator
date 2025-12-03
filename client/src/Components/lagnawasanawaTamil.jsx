import axios from "axios";
import { useEffect, useState } from "react";
import "../css/lagna.css";

const LagnaWasanaTamil = ({ name = "Lagna Wasanawa" }) => {
  // State for lottery data
  const [lottery, setLottery] = useState({
    number: null,
    color: null,
    ball1: null,
    ball2: null,
    ball3: null,
    ball4: null,
    ball5: null,
    next_super: null,
    // special1: null, // Temporarily removed
    // special2: null, // Temporarily removed
  });

  // Fetch lottery data on component mount
  useEffect(() => {
    const fetchLottery = async () => {
      try {
        const response = await axios.get(`/api/lottery`, { params: { name } });
        setLottery(response.data); // Update state with fetched data
      } catch (error) {
        console.error("Error fetching lottery data:", error);
      }
    };

    fetchLottery();
  }, [name]);

  // Combine individual balls into an array
  const balls = [
    lottery.ball1,
    lottery.ball2,
    lottery.ball3,
    lottery.ball4,
    lottery.ball5,
  ].filter((ball) => ball !== null);

  // Function to translate color to Tamil
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


  // Function to translate ball names to Tamil
  const translateBallName = (ball) => {
    const translations = {
      CAPRICORN: "மகரம்",
      AQUARIUS: "கும்பம்",
      PISCES: "மீனம்",
      ARIES: "மேஷம்",
      TAURUS: "ரிஷபம்",
      GEMINI: "மிதுனம்",
      CANCER: "கடகம்",
      LEO: "சிம்மம்",
      VIRGO: "கன்னி",
      LIBRA: "துலாம்",
      SCORPIO: "விருச்சிகம்",
      SAGITTARIUS: "தனுசு",
    };
    return translations[ball] || ball;
  };

  const ballImageMap = {
    CAPRICORN: "/images/lagnaimages/capricorn.png",
    AQUARIUS: "/images/lagnaimages/aquarius.png",
    PISCES: "/images/lagnaimages/pisces.png",
    ARIES: "/images/lagnaimages/aries.png",
    TAURUS: "/images/lagnaimages/taurus.png",
    GEMINI: "/images/lagnaimages/gemini.png",
    CANCER: "/images/lagnaimages/cancer.png",
    LEO: "/images/lagnaimages/leo.png",
    VIRGO: "/images/lagnaimages/virgo.png",
    LIBRA: "/images/lagnaimages/libra.png",
    SCORPIO: "/images/lagnaimages/scorpio.png",
    SAGITTARIUS: "/images/lagnaimages/sagittarius.png",
  };

  const formatCurrency = (amount) => {
    return "ரூ." + Number(amount).toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  };

  return (
    <div className="lagna-ticket-container">
      <div className="lagna-ticket-card">
        <div className="lagna-ticket-header">
          <div className="lagna-ticket-logo-container">
            <img
              src="/images/logo/lagnatamil.png"
              alt={name}
              className="lagna-ticket-logo"
            />
          </div>
          <div className="lagna-ticket-draw-number-containertm">
            <div className="lagna-ticket-draw-number-text-tm">வெற்றி வாரம்</div>
            <div className="lagna-ticket-draw-number-text1-tm">
              {lottery.number || "Loading..."}
            </div>

            <div className="lagna-ticket-colour-text-tm">நிறம்</div>
            <div className="lagna-ticket-colour-text1-tm">
              {translateColor(lottery.color) || "Loading..."}
            </div>

            <div className="lagna-ticket-winning-numbers">
              <div className="lagna-ticket-winning-numbers-titlet">
                --- வெற்றி எண்கள் ---
              </div>
              <div className="lagna-ticket-winning-numbers-containertm">
                {balls.length > 0
                  ? balls.map((ball, index) => (
                    <div key={index} className="lagna-ticket-winning-number">
                      {index === 4 && ballImageMap[ball] ? (
                        <>
                          <img
                            src={ballImageMap[ball]}
                            alt={`Ball ${ball}`}
                            className="lagna-ticket-ball-imaget"
                          />
                          <div className="lagna-ticket-ball-namet">
                            {translateBallName(ball)}
                          </div>
                        </>
                      ) : (
                        <div className="lagna-ticket-winning-number-text">
                          {translateBallName(ball)}
                        </div>
                      )}
                    </div>
                  ))
                  : "Loading..."}
              </div>
            </div>

            <div className="lagna-ticket-special-tm">
              <div className="lagna-ticket-bottomtm">
                அடுத்த சுப்பர் ஐக்பொட்  : <div className="lagna-ticket-bottomtm-txt">
                  {formatCurrency(lottery.next_super) || "Loading..."}
                </div>
              </div>
              {/** Special numbers section - Temporarily removed */}
              {/**
              {(lottery.special1 || lottery.special2) && (
  <div className="lagna-ticket-special-prize-container-tm">
    <img
      src="/images/sc.png"
      alt="Special Prize"
      className="lagna-ticket-special-prize-icon"
    />

    <table className="lottery-table-tm">
      <thead>
        <tr>
          <th>Prize</th>
          <th>Special No.</th>
        </tr>
      </thead>
      <tbody>
        {lottery.special1 && (
          <tr>
            <td>{lottery.special1_label || 'ரூ. 50,000/-'}</td>
            <td className="lagna-special-txt">{lottery.special1}</td>
          </tr>
        )}
        {lottery.special2 && (
          <tr>
            <td>{lottery.special2_label || 'ரூ. 40/-'}</td>
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

export default LagnaWasanaTamil;

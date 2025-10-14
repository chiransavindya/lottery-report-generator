import axios from "axios";
import { useEffect, useState } from "react";
import "../../css/lagna.css";

const LagnaWasanaSinhala = ({ name = "Lagna Wasanawa" }) => {
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
  const balls = [lottery.ball1, lottery.ball2, lottery.ball3, lottery.ball4, lottery.ball5].filter(
    (ball) => ball !== null
  );

  // Function to translate color to Sinhala
  const translateColor = (color) => {
    if (color === "Green" || color === "green") {
      return "කොළ";
    }
    else if (color === "Red" || color === "red") {
        return "රතු";
    }
    else if (color === "Blue" || color === "blue") {
      return "නිල්";
    }
    else if (color === "Orange" || color === "orange") {
      return "තැඹිලි";
    }
    else if (color === "Pink" || color === "pink") {
      return "රෝස";
    }
    else if (color === "Purple" || color === "purple") {
      return "දම්";
    }
    else if (color === "Yellow" || color === "yellow") {
      return "කහ";
    }
    else if (color === "Brown" || color === "Brown") {
      return "දුඹුරු";
    }
    else if (color === "Light Blue" || color === "light blue" || color === "Light blue") {
      return "ලා නිල්";
    }
    else if (color === "Light Pink" || color === "light pink" || color === "Light pink") {
      return "ලා රෝස";
    }
    return color;
  };

  // Function to translate ball names to Sinhala
  const translateBallName = (ball) => {
    const translations = {
      CAPRICORN: "මකර",
      AQUARIUS: "කුම්භ",
      PISCES: "මීන",
      ARIES: "මේශ",
      TAURUS: "වෘෂභ",
      GEMINI: "මිථුන",
      CANCER: "කටක",
      LEO: "සින්හ",
      VIRGO: "කන්‍යා",
      LIBRA: "තුලා",
      SCORPIO: "වෘශ්චික",
      SAGITTARIUS: "ධනු",
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
    return "රු. " + Number(amount).toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  };

  return (
    <div className="lagna-ticket-containersn">
      <div className="lagna-ticket-card">
        <div className="lagna-ticket-header">
          <div className="lagna-ticket-logo-container">
            <img
              src="/images/logo/lagnasinhala.png"
              alt={name}
              className="lagna-ticket-logosn"
            />
          </div>
          <div className="lagna-ticket-draw-number-containersn">
            <div className="lagna-ticket-draw-number-text">දිනුම් වාරය</div>
            <div className="lagna-ticket-draw-number-text1-sn">
              {lottery.number || "Loading..."}
            </div>
            <div className="lagna-ticket-colour-text">වර්ණය</div>
            <div className="lagna-ticket-colour-text1">
              {translateColor(lottery.color) || "Loading..."}
            </div>
            <div className="lagna-ticket-winning-numbers">
              <div className="lagna-ticket-winning-numbers-titles">
                ---- ජයග්‍රාහී අංක ----
              </div>
              <div className="lagna-ticket-winning-numbers-containersn">
                {balls.length > 0
                  ? balls.map((ball, index) => (
                      <div key={index} className="lagna-ticket-winning-number">
                        {index === 4 && ballImageMap[ball] ? (
                          <>
                            <img
                              src={ballImageMap[ball]}
                              alt={`Ball ${ball}`}
                              className="lagna-ticket-ball-images"
                            />
                            <div className="lagna-ticket-ball-names">
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
            <div className="lagna-ticket-special-sn">
              <div className="lagna-ticket-bottoms">
                මීළඟ සුපිරි ජයමල්ල : <br/>{formatCurrency(lottery.next_super) || "Loading..."}
              </div>
              {/* Special Numbers Section - Temporarily removed */}
              {/**
              {(lottery.special1 || lottery.special2) && (
  <div className="lagna-ticket-special-prize-containersn">
    <img
      src="/images/sc.png"
      alt="Special Prize"
      className="lagna-ticket-special-prize-icon"
    />

    <table className="lottery-table-sn">
      <thead>
        <tr>
          <th>Prize</th>
          <th>Special No.</th>
        </tr>
      </thead>
      <tbody>
        {lottery.special1 && (
          <tr>
            <td>{lottery.special1_label || 'රු. 50,000/-'}</td>
            <td className="lagna-special-txt">{lottery.special1}</td>
          </tr>
        )}
        {lottery.special2 && (
          <tr>
            <td>{lottery.special2_label || 'රු. 40/-'}</td>
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

export default LagnaWasanaSinhala;

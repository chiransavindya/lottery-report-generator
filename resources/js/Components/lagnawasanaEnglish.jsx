import axios from "axios";
import { useEffect, useState } from "react";
import "../../css/lagna.css";

const LagnaWasanaEnglish = ({ name = "Lagna Wasanawa" }) => {

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
    // special3: null, // Temporarily removed
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
    return "Rs." + Number(amount).toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};

  return (
    <div className="lagna-ticket-container">
      <div className="lagna-ticket-card">
        <div className="lagna-ticket-header">
          <div className="lagna-ticket-logo-container">
            <img
              src="/images/logo/lagnaenglish.png"
              alt={name}
              className="lagna-ticket-logo"
            />
          </div>
          <div className="lagna-ticket-draw-number-container">
           
              <div className="lagna-ticket-draw-number-text">
                Draw Number
                </div>
                <div className="lagna-ticket-draw-number-text1">
                  {lottery.number || "Loading..."}
                </div>
           
                <div className="lagna-ticket-colour-text">
                  Colour 
                </div>
                <div className="lagna-ticket-colour-text1">
                  {lottery.color || "Loading..."}
                </div>
            
           
            <div className="lagna-ticket-winning-numbers">
              <div className="lagna-ticket-winning-numbers-title">
                ---- Winning Numbers ----
              </div>
              <div className="lagna-ticket-winning-numbers-container">
              {balls.length > 0
                ? balls.map((ball, index) => (
                    <div key={index} className="lagna-ticket-winning-number">
                      {index === 4 && ballImageMap[ball] ? (
                        <>
                          <img
                            src={ballImageMap[ball]}
                            alt={`Ball ${ball}`}
                            className="lagna-ticket-ball-image"
                          />
                          <div className="lagna-ticket-ball-name">{ball}</div>
                        </>
                      ) : (
                        <div className="lagna-ticket-winning-number-text">
                          {ball}
                        </div>
                      )}
                    </div>
                  ))
                : "Loading..."}
            </div>
            </div>
            <div className="lagna-ticket-special">
            <div className="lagna-ticket-bottom">
              Next Super Jackpot :<br/> {formatCurrency(lottery.next_super) || "Loading..."}
            </div>
            {/* Special Numbers Section - Temporarily removed */}
            {/**
            {lottery.show_special_section && (
              <div className="lagna-ticket-special-prize-container">
                <img
                  src="/images/sc.png"
                  alt="Special Prize"
                  className="lagna-ticket-special-prize-icon"
                />
                
                <table className="lottery-table">
  <thead>
  <tr>
    <th>Prize</th>
    <th>Special No.</th>
  </tr>
  </thead>
  <tbody>
    {lottery.special1 && (
      <tr>
        <td>{lottery.special1_label || 'Rs. 50,000/-'}</td>
        <td>{lottery.special1}</td>
      </tr>
    )}
    {lottery.special2 && (
      <tr>
        <td>{lottery.special2_label || 'Rs. 40/-'}</td>
        <td>{lottery.special2}</td>
      </tr>
    )}
    {lottery.special3 && (
      <tr>
        <td>{lottery.special3_label || 'Rs. 200/-'}</td>
        <td>{lottery.special3}</td>
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

export default LagnaWasanaEnglish;

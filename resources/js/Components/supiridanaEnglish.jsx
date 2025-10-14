import axios from "axios";
import { useEffect, useState } from "react";
import "../../css/supiridana.css";

const SupiridanaEnglish = ({ name = "Supiri Dhana Sampatha" }) => {
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


  const balls = [lottery.ball1, lottery.ball2, lottery.ball3, lottery.ball4, lottery.ball5, lottery.ball6, lottery.ball7].filter(
    (ball) => ball !== null
  );

  const formatCurrency = (amount) => {
    return "Rs." + Number(amount).toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  };

  // Function to sort special prizes by value (temporarily disabled)
  /*
  const getSortedSpecialPrizes = () => {
    const specialPrizes = [];
    if (lottery.special1 && lottery.special1_label) {
      specialPrizes.push({ label: lottery.special1_label, number: lottery.special1, value: extractPrizeValue(lottery.special1_label) });
    }
    if (lottery.special2 && lottery.special2_label) {
      specialPrizes.push({ label: lottery.special2_label, number: lottery.special2, value: extractPrizeValue(lottery.special2_label) });
    }
    if (lottery.special3 && lottery.special3_label) {
      specialPrizes.push({ label: lottery.special3_label, number: lottery.special3, value: extractPrizeValue(lottery.special3_label) });
    }
    if (lottery.special4 && lottery.special4_label) {
      specialPrizes.push({ label: lottery.special4_label, number: lottery.special4, value: extractPrizeValue(lottery.special4_label) });
    }
    return specialPrizes.sort((a, b) => a.value - b.value);
  };
  */

  // Function to extract numeric value from prize label (temporarily disabled)
  /*
  const extractPrizeValue = (label) => {
    if (!label) return 0;
    const match = label.match(/Rs\.\s*([\d,]+)/);
    if (match) {
      return parseInt(match[1].replace(/,/g, ''));
    }
    return 0;
  };
  */

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
          <div className="supiridana-ticket-draw-number-container">
              <div className="supiridana-ticket-draw-number-text">
                Draw Number
              </div>
              <div className="supiridana-ticket-draw-number-text1">
                {lottery.number || "Loading..."}
              </div>
            
            
              <div className="supiridana-ticket-colour-text">
                Colour
              </div>
              <div className="supiridana-ticket-colour-text1">
                {lottery.color || "Loading..."}
              </div>
            
            :
              <div className="supiridana-ticket-winning-numbers">
                <div className="supiridana-ticket-winning-numbers-title">
                  <span>English Letter</span>
                  <span>-----Winning Numbers-----</span>
                </div>
                
                {/* Main container that holds all three sections in one row */}
                <div className="supiridana-ticket-all-numbers-row">
                  
                  {/* English Letter Container */}
                  <div className="supiridana-ticket-english-letter-container">
                    {balls.length > 0 && balls[0] && (
                      <div className="supiridana-ticket-winning-number">
                        <div className="supiridana-ticket-winning-number-text">
                          {balls[0]}
                        </div>
                      </div>
                    )}
                  </div>

                  {/* Winning Numbers Container */}
                  <div className="supiridana-ticket-winning-numbers-container">
                    {balls.length > 1
                      ? balls.slice(1).map((ball, index) => (
                          <div key={index} className="supiridana-ticket-winning-number">
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
            <div className="supiridana-ticket-bottom">
              Next Super Jackpot : {formatCurrency(lottery.next_super) || "Loading..."}
            </div>
            {/* Special Numbers Section - Temporarily removed */}
            {/**
            {getSortedSpecialPrizes().length > 0 && (
  <div className="supiridana-ticket-special-prize-container">
    <img
      src="/images/sc.png"
      alt="Special Prize"
      className="supiridana-ticket-special-prize-icon"
    />

    <table className="special-prize-table">
      <thead>
        <tr>
          <th>Prize</th>
          <th>Special No.</th>
        </tr>
      </thead>
      <tbody>
        {getSortedSpecialPrizes().map((prize, index) => (
          <tr key={index}>
            <td>{prize.label}</td>
            <td className="lagna-special-txt">{prize.number}</td>
          </tr>
        ))}
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

export default SupiridanaEnglish;

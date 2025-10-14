import axios from "axios";
import { useEffect, useState } from "react";
import "../../css/adakotipathi.css";

const AdakotipathiEnglish = ({ name = "Ada kotipathi" }) => {
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

  const formatCurrency = (amount) => {
    return "Rs. " + Number(amount).toLocaleString("en-US", { minimumFractionDigits: 1, maximumFractionDigits: 2 });
  };

  // Function to sort special prizes by value (lowest to highest)
  // Temporarily commented out
  /*
  const getSortedSpecialPrizes = () => {
    const specialPrizes = [];
    
    // Collect all special prizes that exist
    if (lottery.special1 && lottery.special1_label) {
      specialPrizes.push({
        label: lottery.special1_label,
        number: lottery.special1,
        value: extractPrizeValue(lottery.special1_label)
      });
    }
    if (lottery.special2 && lottery.special2_label) {
      specialPrizes.push({
        label: lottery.special2_label,
        number: lottery.special2,
        value: extractPrizeValue(lottery.special2_label)
      });
    }
    if (lottery.special3 && lottery.special3_label) {
      specialPrizes.push({
        label: lottery.special3_label,
        number: lottery.special3,
        value: extractPrizeValue(lottery.special3_label)
      });
    }
    if (lottery.special4 && lottery.special4_label) {
      specialPrizes.push({
        label: lottery.special4_label,
        number: lottery.special4,
        value: extractPrizeValue(lottery.special4_label)
      });
    }

    // Sort by prize value (lowest to highest - ascending order)
    return specialPrizes.sort((a, b) => a.value - b.value);
  };
  */

  // Function to extract numeric value from prize label (e.g., "Rs. 50,000/-" -> 50000)
  // Temporarily commented out
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
          <div className="adakotipathi-ticket-draw-number-container">
            
              <div className="adakotipathi-ticket-draw-number-text">
                Draw Number
              </div>
              <div className="adakotipathi-ticket-draw-number-text1">
                {lottery.number || "Loading..."}
              </div>
            
            
              <div className="adakotipathi-ticket-colour-text">
                Colour
              </div>
              <div className="adakotipathi-ticket-colour-text1">
                {lottery.color || "Loading..."}
              </div>
            
            <div className="adakotipathi-ticket-winning-numbers">
              <div className="adakotipathi-ticket-winning-numbers-title">
                <span className="winning-numbers-text">----Winning Numbers----</span>
                <span className="english-letter-text">English letter</span>
              </div>
              <div className="adakotipathi-ticket-winning-numbers-container">
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
            <div className="adakotipathi-ticket-bottom">
              Next Super Jackpot : <br/>{formatCurrency(lottery.next_super) || "Loading..."}
            </div>
            
            {/* Special Numbers Section - Temporarily commented out */}
            {/*
            {getSortedSpecialPrizes().length > 0 && (
            <div className="adakotipathi-ticket-special-prize-container">
              <img
                src="/images/sc.png"
                alt="Special Prize"
                className="adakotipathi-ticket-special-prize-icon"
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

export default AdakotipathiEnglish;

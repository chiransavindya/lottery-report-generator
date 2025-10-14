import axios from "axios";
import { useEffect, useState } from "react";
import "../../css/shanida.css";

const ShanidaSinhala = ({ name = "Shanida" }) => {
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
    show_special_section: false,
    special1_label: "රු. 50,000/-",
    special2_label: "රු. 40/-",
    special3_label: "රු. 200/-",
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
    else if (color === "Light Blue" || color === "light blue" || color === "Light blue") {
      return "ලා නිල්";
    }
    else if (color === "Light Pink" || color === "light pink" || color === "Light pink") {
      return "ලා රෝස";
    }
    return color;
  };

  const formatCurrency = (amount) => {
    return "රු. " + Number(amount).toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  };

  // Function to sort special prizes by value (lowest to highest)
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

  // Function to extract numeric value from prize label (e.g., "රු. 50,000/-" -> 50000)
  const extractPrizeValue = (label) => {
    if (!label) return 0;
    const match = label.match(/රු\.\s*([\d,]+)|Rs\.\s*([\d,]+)/);
    if (match) {
      const numStr = match[1] || match[2];
      return parseInt(numStr.replace(/,/g, ''));
    }
    return 0;
  };

  return (
    <div className="shanida-ticket-containersn">
      <div className="shanida-ticket-card">
        <div className="shanida-ticket-header">
          <div className="shanida-ticket-logo-container">
            <img
              src="/images/logo/shanida.png"
              alt={name}
              className="shanida-ticket-logosn"
            />
          </div>
          <div className="shanida-ticket-draw-number-containersn">
            
              <div className="shanida-ticket-draw-number-text-sn">
                දිනුම් වාරය
              </div>
              <div className="shanida-ticket-draw-number-text1-sn">
                {lottery.number || "Loading..."}
              </div>
            
            
              <div className="shanida-ticket-colour-text-sn">
                වර්ණය
              </div>
              <div className="shanida-ticket-colour-text1-sn">
                {translateColor(lottery.color) || "Loading..."}
              </div>
            
            <div className="shanida-ticket-winning-numbers-sn">
                <div className="shanida-ticket-winning-numbers-title-sn">
                  <span className="winning-numbers-text-sn">------- ජයග්‍රාහී අංක -------</span>
                  <span className="english-letter-text-sn">ඉංග්‍රීසි අක්ෂරය</span>
                </div>
                
                {/* Main container that holds both sections in one row */}
                <div className="shanida-ticket-all-numbers-row-sn">
                  
                  {/* Winning Numbers Container (all except last) */}
                  <div className="shanida-ticket-winning-numbers-container-sn">
                    {balls.length > 1
                      ? balls.slice(0, -1).map((ball, index) => (
                          <div key={index} className="shanida-ticket-winning-number-sn">
                            <div className="shanida-ticket-winning-number-text-sn">
                              {ball}
                            </div>
                          </div>
                        ))
                      : "Loading..."}
                  </div>
                  
                  {/* English Letter Container (last number) */}
                  <div className="shanida-ticket-english-letter-container-sn">
                    {balls.length > 0 && balls[balls.length - 1] && (
                      <div className="shanida-ticket-english-letter-sn">
                        <div className="shanida-ticket-english-letter-text-sn">
                          {balls[balls.length - 1]}
                        </div>
                      </div>
                    )}
                  </div>
                  
                </div>
              </div>
            <div className="shanida-ticket-special">
            <div className="shanida-ticket-bottomsn">
              මීළඟ සුපිරි ජයමල්ල : {formatCurrency(lottery.next_super) || "Loading..."}
            </div>
            {/* Special Numbers Section */}
            {getSortedSpecialPrizes().length > 0 && (
  <div className="shanida-ticket-special-prize-container-sn">
    <img
      src="/images/sc.png"
      alt="Special Prize"
      className="shanida-ticket-special-prize-icon-sn"
    />

    <table className="special-prize-table-sn">
      <thead>
        <tr>
          <th>මුදල</th>
          <th>විශේෂ අංකය</th>
        </tr>
      </thead>
      <tbody>
        {getSortedSpecialPrizes().map((prize, index) => (
          <tr key={index}>
            <td>{prize.label}</td>
            <td className="special-number">{prize.number}</td>
          </tr>
        ))}
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

export default ShanidaSinhala;

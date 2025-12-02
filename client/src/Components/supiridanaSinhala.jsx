import axios from "axios";
import { useEffect, useState } from "react";
import "../css/supiridana.css";

const SupiridanaSinhala = ({ name = "Supiri Dhana Sampatha" }) => {
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
    const colorLower = color?.toLowerCase().trim() || "";
    
    if (colorLower === "green") return "කොළ";
    if (colorLower === "light green") return "ලා කොළ";
    if (colorLower === "dark green") return "තද කොළ";
    if (colorLower === "red") return "රතු";
    if (colorLower === "blue") return "නිල්";
    if (colorLower === "light blue") return "ලා නිල්";
    if (colorLower === "dark blue") return "තද නිල්";
    if (colorLower === "orange") return "තැඹිලි";
    if (colorLower === "pink") return "රෝස";
    if (colorLower === "light pink") return "ලා රෝස";
    if (colorLower === "purple") return "දම්";
    if (colorLower === "yellow") return "කහ";
    if (colorLower === "brown") return "දුඹුරු";
    if (colorLower === "light brown") return "ලා දුඹුරු";
    if (colorLower === "dark brown") return "තද දුඹුරු";
    
    return color;
  };

  const balls = [lottery.ball1, lottery.ball2, lottery.ball3, lottery.ball4, lottery.ball5, lottery.ball6, lottery.ball7].filter(
    (ball) => ball !== null
  );

  const formatCurrency = (amount) => {
    return "රු. " + Number(amount).toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  };

  return (
    <div className="supiridana-ticket-containersn">
      <div className="supiridana-ticket-card">
        <div className="supiridana-ticket-header">
          <div className="supiridana-ticket-logo-container">
            <img
              src="/images/logo/supiridana.png"
              alt={name}
              className="supiridana-ticket-logosn"
            />
          </div>
          <div className="supiridana-ticket-draw-number-containersn">
            
              <div className="supiridana-ticket-draw-number-text">
                  දිනුම් වාරය
              </div>
              <div className="supiridana-ticket-draw-number-text1">
                {lottery.number || "Loading..."}
              </div>
            
           
              <div className="supiridana-ticket-colour-text">
                වර්ණය
              </div>
              <div className="supiridana-ticket-colour-text1">
                {translateColor(lottery.color) || "Loading..."}
              </div>
           
              <div className="supiridana-ticket-winning-numbers">
                <div className="supiridana-ticket-winning-numbers-titles">
                  <span>ඉංග්‍රීසි අක්ෂරය</span>
                  <span>ජයග්‍රාහී අංක</span>
                </div>
                
                {/* Main container that holds all three sections in one row */}
                <div className="supiridana-ticket-all-numbers-row-sn">
                  
                  {/* English Letter Container */}
                  <div className="supiridana-ticket-english-letter-container-sn">
                    {balls.length > 0 && balls[0] && (
                      <div className="supiridana-ticket-winning-number-sn">
                        <div className="supiridana-ticket-winning-number-text">
                          {balls[0]}
                        </div>
                      </div>
                    )}
                  </div>

                  {/* Winning Numbers Container */}
                  <div className="supiridana-ticket-winning-numbers-container-sn">
                    {balls.length > 1
                      ? balls.slice(1).map((ball, index) => (
                          <div key={index} className="supiridana-ticket-winning-number-sn">
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
              <div className="supiridana-ticket-bottom-sn">
                මීළඟ සුපිරි ජයමල්ල : {formatCurrency(lottery.next_super) || "Loading..."}
              </div>
              {/* Special Numbers Section - Temporarily removed */}
              {/**
              {lottery.show_special_section && (
  <div className="supiridana-ticket-special-prize-container-sn">
    <img
      src="/images/sc.png"
      alt="Special Prize"
      className="supiridana-ticket-special-prize-icon"
    />

    <table className="special-prize-table-sn">
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
            <td className="special-number">{lottery.special1}</td>
          </tr>
        )}
        {lottery.special2 && (
          <tr>
            <td>{lottery.special2_label || 'රු. 40/-'}</td>
            <td className="special-number">{lottery.special2}</td>
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

export default SupiridanaSinhala;

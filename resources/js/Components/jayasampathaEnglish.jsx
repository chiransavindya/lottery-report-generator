import axios from "axios";
import { useEffect, useState } from "react";
import "../../css/jayasampatha.css"; // Using jayoda css for now, can be changed

const JayasampathaEnglish = ({ name = "Jaya Sampatha" }) => {
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

  useEffect(() => {
    const fetchLottery = async () => {
      try {
        console.log("Fetching lottery data for:", name);
        const response = await axios.get(`/api/lottery`, { params: { name } });
        console.log("API response:", response.data);
        setLottery(response.data);
      } catch (error) {
        console.error("Error fetching lottery data:", error);
        console.error("Error details:", error.response?.data);
      }
    };

    fetchLottery();
  }, [name]);


  const balls = [lottery.ball1, lottery.ball2, lottery.ball3, lottery.ball4, lottery.ball5].filter(
    (ball) => ball !== null
  );

  // Debug logging
  console.log("Jayasampatha lottery data:", lottery);
  console.log("Filtered balls:", balls);

  const formatCurrency = (amount) => {
    return "Rs. " + Number(amount).toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  };

  return (
    <div className="jayasampatha-ticket-container">
      <div className="jayasampatha-ticket-card">
        <div className="jayasampatha-ticket-header">
          <div className="jayasampatha-ticket-logo-container">
            <img
              src="/images/logo/jayasampatha.png"
              alt={name}
              className="jayasampatha-ticket-logo"
            />
          </div>
          <div className="jayasampatha-ticket-draw-number-container">
            
              <div className="jayasampatha-ticket-draw-number-text">
                Draw Number
              </div> 
              <div className="jayasampatha-ticket-draw-number-text1">
                {lottery.number || "Loading..."}
              </div>

          
              <div className="jayasampatha-ticket-colour-text">
                Colour
              </div>
              <div className="jayasampatha-ticket-colour-text1">
                {lottery.color || "Loading..."}
              </div>
            
            <div className="jayasampatha-ticket-winning-numbers">
              <div className="jayasampatha-ticket-winning-numbers-title">
                <span className="winning-numbers-text">English Letter</span>
                <span className="english-letter-text">---- Winning Numbers ----</span>
              </div>

              <div className="jayasampatha-letter-and-numbers">
                <div className="jayasampatha-letter-en">
                  <div className="jayasampatha-letter-badge">
                    <div className="jayasampatha-letter-text">{lottery.ball5 || "X"}</div>
                  </div>
                </div>

                <div className="jayasampatha-ticket-winning-numbers-container">
                  {balls.length > 0
                    ? balls.slice(0, 4).map((ball, index) => (
                        <div key={index} className="jayasampatha-ticket-winning-number">
                          <div className="jayasampatha-ticket-winning-number-text">
                            {ball}
                          </div>
                        </div>
                      ))
                    : "Loading..."}
                </div>
              </div>
            </div>
            <div className="jayasampatha-ticket-special">
            <div className="jayasampatha-ticket-bottom">
              Total Value of Prizes : {formatCurrency(lottery.total) || "Loading..."}
            </div>
            {/* Special Numbers Section - Temporarily removed */}
            {/**
            {lottery.show_special_section && (
              <div className="jayasampatha-ticket-special-prize-container">
                <img
                  src="/images/sc.png"
                  alt="Special Prize"
                  className="jayasampatha-ticket-special-prize-icon"
                />
                <div className="special-numbers">
                  {lottery.special1 && (
                    <>
                      Special number for {lottery.special1_label || 'Rs. 50,000/-'}: {lottery.special1}
                      <br />
                    </>
                  )}
                  {lottery.special2 && (
                    <div className="lagna-special-txt">{lottery.special2_label || 'Rs. 40/-'}: {lottery.special2}</div>
                  )}
                </div>
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

export default JayasampathaEnglish;

import React, { useState, useEffect } from "react";
import axios from "axios";
import "../../css/sasiri.css";

const SasiriEnglish = ({ name = "Sasiri" }) => {
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

  const formatCurrency = (amount) => {
    return "Rs. " + Number(amount).toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  };

  return (
    <div className="sasiri-ticket-container">
      <div className="sasiri-ticket-card">
        <div className="sasiri-ticket-header">
          <div className="sasiri-ticket-logo-container">
            <img
              src="/images/logo/sasirienglish.png"
              alt={name}
              className="sasiri-ticket-logo"
            />
          </div>
          <div className="sasiri-ticket-draw-number-container">
            
            <div className="sasiri-ticket-draw-number-text">
                Draw Number
                </div>
                <div className="sasiri-ticket-draw-number-text1">
                  {lottery.number || "Loading..."}
                </div>

                <div className="sasiri-ticket-colour-text">
                  Colour 
                </div>
                <div className="sasiri-ticket-colour-text1">
                  {lottery.color || "Loading..."}
                </div>
            

            <div className="sasiri-ticket-winning-numbers">
              <div className="sasiri-ticket-winning-numbers-title">
                ----- Winning Numbers -----
              </div>
              <div className="sasiri-ticket-winning-numbers-container">
                {balls.length > 0
                  ? balls.map((ball, index) => (
                      
                        <div key={index} className="sasiri-ticket-winning-number">
                          <div className="sasiri-ticket-winning-number-text">
                            {ball}
                          </div>
                        </div>
                    ))
                  : "Loading..."}
                  <div className="sasiri-ticket-winner-containeren">
                        Total no.of <br/> Rs. 200,000 winners
                  </div>
                  <div className="sasiri-ticket-winner-container1en">
                    {lottery.count || "Loading..."}
                  </div>
                
              </div>
            </div>
            <div className="sasiri-ticket-special">
              <div className="sasiri-ticket-bottom">
                
                  Total Value of Prize : {formatCurrency(lottery.total) || "Loading..."}
               
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default SasiriEnglish;

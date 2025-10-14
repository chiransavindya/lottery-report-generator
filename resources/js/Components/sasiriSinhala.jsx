import React, { useState, useEffect } from "react";
import axios from "axios";
import "../../css/sasiri.css";

const SasiriSinhala = ({ name = "Sasiri" }) => {
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
    else if (color === "Orange" || color === "orange" || color === "orange ") {
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

  return (
    <div className="sasiri-ticket-containersn">
      <div className="sasiri-ticket-card">
        <div className="sasiri-ticket-header">
          <div className="sasiri-ticket-logo-container">
            <img
              src="/images/logo/sasirisinhala.png"
              alt={name}
              className="sasiri-ticket-logosn"
            />
          </div>
          <div className="sasiri-ticket-draw-number-containersn">
            
              <div className="sasiri-ticket-draw-number-text">
                දිනුම් වාරය
              </div>
              <div className="sasiri-ticket-draw-number-text1-sn">
                {lottery.number || "Loading..."}
              </div>
            
            
              <div className="sasiri-ticket-colour-text">
                වර්ණය
              </div>
              <div className="sasiri-ticket-colour-text1">
                {translateColor(lottery.color) || "Loading..."}
              </div>
           
            <div className="sasiri-ticket-winning-numbers">
              <div className="sasiri-ticket-winning-numbers-titles">
                -------- ජයග්‍රාහී  අංක --------
              </div>
              <div className="sasiri-ticket-winning-numbers-containersn">
                {balls.length > 0
                  ? balls.map((ball, index) => (
                     
                        <div key={index} className="sasiri-ticket-winning-number">
                          <div className="sasiri-ticket-winning-number-text">
                            {ball}
                          </div>
                        </div>
                     
                    ))
                  : "Loading..."}
                  <div className="sasiri-ticket-winner-container-text">  
                      අද බිහි වූ දෙලක්ෂපතියන් ගණන
                  </div>
                  
                  <div className="sasiri-ticket-winner-container1">
                    {lottery.count || "Loading..."}  
                  </div>
              </div>
            </div>
            <div className="sasiri-ticket-special">
              <div className="sasiri-ticket-bottomsn">
                  දිනා ඇති මුළු මුදල : {formatCurrency(lottery.total) || "Loading..."}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default SasiriSinhala;

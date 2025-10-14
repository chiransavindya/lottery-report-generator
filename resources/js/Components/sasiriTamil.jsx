import React, { useState, useEffect } from "react";
import axios from "axios";
import "../../css/sasiri.css";

const SasiriTamil = ({ name = "Sasiri" }) => {
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
        return "பச்சை";
    }
    else if (color === "Red" || color === "red") {
        return "சிகப்பு";
    }
    else if (color === "Blue" || color === "blue") {
        return "நீலம்";
    }
    else if (color === "Orange" || color === "orange" || color === "orange ") {
        return "செம்மஞ்சள்";
    }
    else if (color === "Pink" || color === "pink") {
        return "இளஞ்சிகப்பு";
    }
    else if (color === "Purple" || color === "purple") {
        return "ஊதா";
    }
    else if (color === "Yellow" || color === "yellow") {
      return "மஞ்சள்";
    }
    else if (color === "Brown" || color === "brown") {
      return "பழுப்பு";
    }
    else if (color === "Light Blue" || color === "light blue" || color === "Light blue") {
        return "இளநீலம்";
    }
    else if (color === "Light Pink" || color === "light pink" || color === "Light pink") {
        return "இளஞ்சிகப்பு";
    }
    return color;
};

const formatCurrency = (amount) => {
  return "ரூ. " + Number(amount).toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};


  return (
    <div className="sasiri-ticket-container">
      <div className="sasiri-ticket-card">
        <div className="sasiri-ticket-header">
          <div className="sasiri-ticket-logo-container">
            <img
              src="/images/logo/sasiritamil.png"
              alt={name}
              className="sasiri-ticket-logo"
            />
          </div>
          <div className="sasiri-ticket-draw-number-containertm">
            
              <div className="sasiri-ticket-draw-number-text">
                வெற்றி வாரம்
              </div>
              <div className="sasiri-ticket-draw-number-text1tm">
                {lottery.number || "Loading..."}
              </div>
           
        
              <div className="sasiri-ticket-colour-text">
                வர்ணம்
              </div>
              <div className="sasiri-ticket-colour-text1">
                {translateColor(lottery.color) || "Loading..."}
              </div>
          
            <div className="sasiri-ticket-winning-numbers">
              <div className="sasiri-ticket-winning-numbers-titletm">
              ---- வெற்றி எண்கள் ----
              </div>
              <div className="sasiri-ticket-winning-numbers-containertm">
                {balls.length > 0
                  ? balls.map((ball, index) => (
                      
                        <div key={index} className="sasiri-ticket-winning-number">
                          <div className="sasiri-ticket-winning-number-text">
                            {ball}
                          </div>
                        </div>
                      
                    ))
                  : "Loading..."}
                  <div className="sasiri-ticket-winner-containertm">  
                  இன்று இரண்டு இலட்சத்திற்கு அதிபதியானோரின்  <br /> எண்ணிக்கை
                  </div>
                  <div className="sasiri-ticket-winner-containertm1">
                    {lottery.count || "Loading..."}  
                  </div>
              </div>
            </div>
            <div className="sasiri-ticket-special">
              <div className="sasiri-ticket-bottomtm">
               
                  வெல்லப்பட்ட மொத்த பரிசுத் தொகை : 
                  <div className="sasiri-ticket-bottomtm-txt">
                    {formatCurrency(lottery.total) || "Loading..."}
                  </div>
              
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default SasiriTamil;

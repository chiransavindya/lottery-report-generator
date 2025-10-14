import axios from "axios";
import { useEffect, useState } from "react";
import "../../css/superball.css";

const SuperballTamil = ({ name = "Superball" }) => {
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
        return "பச்சை";
    }
    else if (color === "Red" || color === "red") {
        return "சிகப்பு";
    }
    else if (color === "Blue" || color === "blue") {
        return "நீலம்";
    }
    else if (color === "Orange" || color === "orange") {
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
    else if (color === "Light Blue" || color === "light blue" || color === "Light blue") {
        return "இளநீலம்";
    }
    else if (color === "Light Pink" || color === "light pink" || color === "Light pink") {
        return "இளஞ்சிகப்பு";
    }
    return color;
};

  const translateLabel = (label) => {
    if (label) {
      return label.replace(/Rs\./g, 'ரூ');
    }
    return label;
  };

const formatCurrency = (amount) => {
  return "ரூ. " + Number(amount).toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};


  return (
    <div className="superball-ticket-container">
      <div className="superball-ticket-card">
        <div className="superball-ticket-header">
          <div className="superball-ticket-logo-container">
            <img
              src="/images/logo/superball.png"
              alt={name}
              className="superball-ticket-logo"
            />
          </div>
          <div className="superball-ticket-draw-number-containertm">
            
              <div className="superball-ticket-draw-number-text">
                வெற்றி வாரம்
              </div>
              <div className="superball-ticket-draw-number-text1-tm">
                {lottery.number || "Loading..."}
              </div>
           
          
              <div className="superball-ticket-colour-text">
                வர்ணம்
              </div>
              <div className="superball-ticket-colour-text1">
                {translateColor(lottery.color) || "Loading..."}
              </div>
          

              <div className="superball-ticket-winning-numbers">
                <div className="superball-ticket-winning-numbers-titlet">
                  <span>---- வெற்றி எண்கள் ----</span>
                  <span>ஆங்கில எழுத்து</span>
                </div>
                
                <div className="superball-ticket-winning-numbers-container-tm">
                  {/* Winning Numbers (all except last) */}
                  <div className="superball-ticket-winning-numbers-group-tm">
                    {balls.length > 1
                      ? balls.slice(0, -1).map((ball, index) => (
                          <div key={index} className="superball-ticket-winning-number-tm">
                            <div className="superball-ticket-winning-number-text">
                              {ball}
                            </div>
                          </div>
                        ))
                      : "Loading..."}
                  </div>
                  
                  {/* English Letter (last number) with gap */}
                  <div className="superball-ticket-english-letter-group-tm">
                    {balls.length > 0 && balls[balls.length - 1] && (
                      <div className="superball-ticket-winning-number-tm superball-ticket-english-letter-tm">
                        <div className="superball-ticket-winning-number-text">
                          {balls[balls.length - 1]}
                        </div>
                      </div>
                    )}
                  </div>
                </div>
              </div>
            <div className="superball-ticket-special">
            <div className="superball-ticket-bottomtm">
              அடுத்த சுப்பர் ஐக்பொட் : 
              <div className="superball-ticket-bottomtm-txt">
                {formatCurrency(lottery.next_super) || "Loading..."}
              </div>
            </div>
            {/* Special Numbers Section */}
            {(lottery.special1 || lottery.special2 || lottery.special3) && (
  <div className="superball-ticket-special-prize-container-tm">
    <img
      src="/images/sc.png"
      alt="Special Prize"
      className="superball-ticket-special-prize-icon-tm"
    />

    <table className="superball-special-numbers-tm">
      <thead>
        <tr>
          <th>பரிசு</th>
          <th>விசேட இலக்கம்</th>
        </tr>
      </thead>
      <tbody>
        {lottery.special1 && (
          <tr>
            <td>{translateLabel(lottery.special1_label) || 'ரூ 50,000/-'}</td>
            <td className="special-number">{lottery.special1}</td>
          </tr>
        )}
        {lottery.special3 && (
          <tr>
            <td>{translateLabel(lottery.special3_label) || 'ரூ 200/-'}</td>
            <td className="special-number">{lottery.special3}</td>
          </tr>
        )}
        {lottery.special2 && (
          <tr>
            <td>{translateLabel(lottery.special2_label) || 'ரூ 40/-'}</td>
            <td className="special-number">{lottery.special2}</td>
          </tr>
        )}
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

export default SuperballTamil;

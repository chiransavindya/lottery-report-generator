import axios from "axios";
import { useEffect, useState } from "react";
import "../../css/jayasampatha.css";

const JayasampathaTamil = ({ name = "Jaya Sampatha" }) => {
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
    if (color === "Green" || color === "green") return "பச்சை";
    if (color === "Red" || color === "red") return "சிகப்பு";
    if (color === "Blue" || color === "blue") return "நீலம்";
    if (color === "Orange" || color === "orange") return "செம்மஞ்சள்";
    if (color === "Pink" || color === "pink") return "இளஞ்சிகப்பு";
    if (color === "Purple" || color === "purple") return "ஊதா";
    if (color === "Yellow" || color === "yellow") return "மஞ்சள்";
    if (color === "Brown" || color === "Brown") return "பழுப்பு";
    if (color === "Light Blue" || color === "light blue" || color === "Light blue") return "இளநீலம்";
    if (color === "Light Pink" || color === "light pink" || color === "Light pink") return "இளஞ்சிகப்பு";
    return color;
  };

  const formatCurrency = (amount) => {
    return "ரூ. " + Number(amount).toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  };

  return (
    <div className="jayasampatha-ticket-container-tm">
      <div className="jayasampatha-ticket-card">
        <div className="jayasampatha-ticket-header">
          <div className="jayasampatha-ticket-logo-container">
            <img
              src="/images/logo/jayasampatha.png"
              alt={name}
              className="jayasampatha-ticket-logo"
            />
          </div>
          <div className="jayasampatha-ticket-draw-number-container-tm">
            
              <div className="jayasampatha-ticket-draw-number-text">
                வெற்றி வாரம்
              </div>
              <div className="jayasampatha-ticket-draw-number-text1-tm">
                {lottery.number || "Loading..."}
              </div>
           
          
              <div className="jayasampatha-ticket-colour-text">
                வர்ணம்
              </div>
              <div className="jayasampatha-ticket-colour-text1">
                {translateColor(lottery.color) || "Loading..."}
              </div>
          
            <div className="jayasampatha-ticket-winning-numbers">
              <div className="jayasampatha-ticket-winning-numbers-title-tm">
                ------ வெற்றி எண்கள் ------
              </div>

              <div className="jayasampatha-letter-and-numbers">
                <div className="jayasampatha-letter">
                  <div className="jayasampatha-letter-label">ஆங்கில எழுத்து</div>
                  <div className="jayasampatha-letter-badge">
                    <div className="jayasampatha-letter-text">{lottery.ball5 || "X"}</div>
                  </div>
                </div>

                <div className="jayasampatha-ticket-winning-numbers-container-tm">
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
            <div className="jayasampatha-ticket-bottom-tm">
            பரிசுகளின் மொத்த மதிப்பு : {formatCurrency(lottery.total) || "Loading..."}
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
                <div className="special-numbers-tm">
                  {lottery.special1 && (
                    <>
                      {lottery.special1_label || 'ரு. 50,000/-'} க்கான விசேட இலக்கம்: {lottery.special1}
                      <br />
                    </>
                  )}
                    <div className="lagna-special-txt">
                      {lottery.special2 && <>{lottery.special2_label || 'ரு. 40/-'} : {lottery.special2}</>}
                    </div>
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

export default JayasampathaTamil;

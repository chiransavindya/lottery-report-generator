import axios from "axios";
import { useEffect, useState } from "react";
import "../css/jayoda.css";

const JayodaTamil = ({ name = "Jayoda" }) => {
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
    const colorLower = color?.trim()?.toLowerCase() || "";

    if (colorLower === "green") return "பச்சை";
    if (colorLower === "light green") return "வெளிர் பச்சை";
    if (colorLower === "dark green") return "அடர் பச்சை";
    if (colorLower === "red") return "சிகப்பு";
    if (colorLower === "blue") return "நீலம்";
    if (colorLower === "light blue") return "இளநீலம்";
    if (colorLower === "dark blue") return "அடர் நீலம்";
    if (colorLower === "orange") return "செம்மஞ்சள்";
    if (colorLower === "pink") return "இளஞ்சிகப்பு";
    if (colorLower === "light pink") return "வெளிர் இளஞ்சிகப்பு";
    if (colorLower === "purple") return "ஊதா";
    if (colorLower === "yellow") return "மஞ்சள்";
    if (colorLower === "brown") return "பழுப்பு";
    if (colorLower === "light brown") return "வெளிர் பழுப்பு";
    if (colorLower === "dark brown") return "அடர் பழுப்பு";

    return color;
  };

  const formatCurrency = (amount) => {
    return "ரூ. " + Number(amount).toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  };


  return (
    <div className="jayoda-ticket-container-tm">
      <div className="jayoda-ticket-card">
        <div className="jayoda-ticket-header">
          <div className="jayoda-ticket-logo-container">
            <img
              src="/images/logo/jayoda.png"
              alt={name}
              className="jayoda-ticket-logo"
            />
          </div>
          <div className="jayoda-ticket-draw-number-container-tm">

            <div className="jayoda-ticket-draw-number-text">
              வெற்றி வாரம்
            </div>
            <div className="jayoda-ticket-draw-number-text1-tm">
              {lottery.number || "Loading..."}
            </div>


            <div className="jayoda-ticket-colour-text">
              வர்ணம்
            </div>
            <div className="jayoda-ticket-colour-text1">
              {translateColor(lottery.color) || "Loading..."}
            </div>

            <div className="jayoda-ticket-winning-numbers">
              <div className="jayoda-ticket-winning-numbers-titlet">
                <span>--- வெற்றி எண்கள் ---</span>
                <span>ஆங்கில எழுத்து</span>
              </div>
              <div className="jayoda-ticket-winning-numbers-container">
                {balls.length > 0
                  ? balls.map((ball, index) => (

                    <div key={index} className="jayoda-ticket-winning-number">
                      <div className="jayoda-ticket-winning-number-text">
                        {ball}
                      </div>
                    </div>

                  ))
                  : "Loading..."}
              </div>
            </div>
            <div className="jayoda-ticket-special">
              <div className="jayoda-ticket-bottomtm">
                அடுத்த சுப்பர் ஐக்பொட் :
                <div className="jayoda-ticket-bottomtm-txt">
                  {formatCurrency(lottery.next_super) || "Loading..."}
                </div>
              </div>
              {/* Special Numbers Section */}
              {lottery.show_special_section && (
                <div className="jayoda-ticket-special-prize-container">
                  <img
                    src="/images/sc.png"
                    alt="Special Prize"
                    className="jayoda-ticket-special-prize-icon"
                  />
                  <div className="special-numbers">
                    {lottery.special1 && (
                      <>
                        {lottery.special1_label || 'ரூ. 50,000/-'} பணம் பரிசுகான <br /> வீசேட இலக்கங்கள் :{" "}
                        {lottery.special1 || "Loading..."}
                        <br />
                      </>
                    )}
                    {lottery.special2 && <>{lottery.special2_label || 'ரூ. 40/-'} : {lottery.special2}</>}
                  </div>
                </div>
              )}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default JayodaTamil;

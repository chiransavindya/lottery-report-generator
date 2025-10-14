import axios from "axios";
import { useEffect, useState } from "react";
import "../../css/jayasampatha.css";

const JayasampathaSinhala = ({ name = "Jaya Sampatha" }) => {
const [lottery, setLottery] = useState({
    number: null,
    color: null,
    ball1: null,
    ball2: null,
    ball3: null,
    ball4: null,
    ball5: null,
    next_super: null,
    // special1: null, // Temporarily commented out
   // special2: null, // Temporarily commented out
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
    if (color === "Green" || color === "green") return "කොළ";
    if (color === "Red" || color === "red") return "රතු";
    if (color === "Blue" || color === "blue") return "නිල්";
    if (color === "Orange" || color === "orange") return "තැඹිලි";
    if (color === "Pink" || color === "pink") return "රෝස";
    if (color === "Purple" || color === "purple") return "දම්";
    if (color === "Yellow" || color === "yellow") return "කහ";
    if (color === "Light Blue" || color === "light blue" || color === "Light blue") return "ලා නිල්";
    if (color === "Light Pink" || color === "light pink" || color === "Light pink") return "ලා රෝස";
    return color;
};

  const formatCurrency = (amount) => {
    return "රු. " + Number(amount).toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  };

  return (
    <div className="jayasampatha-ticket-containersn">
      <div className="jayasampatha-ticket-card">
        <div className="jayasampatha-ticket-header">
          <div className="jayasampatha-ticket-logo-container">
            <img
              src="/images/logo/jayasampatha.png"
              alt={name}
              className="jayasampatha-ticket-logosn"
            />
          </div>
          <div className="jayasampatha-ticket-draw-number-container-sn">
            
              <div className="jayasampatha-ticket-draw-number-text">
                දිනුම් වාරය
              </div>
              <div className="jayasampatha-ticket-draw-number-text1-sn">
                {lottery.number || "Loading..."}
              </div>
            
              <div className="jayasampatha-ticket-colour-text">
                වර්ණය
              </div>
              <div className="jayasampatha-ticket-colour-text1">
                {translateColor(lottery.color) || "Loading..."}
              </div>
            
            <div className="jayasampatha-ticket-winning-numbers">
              <div className="jayasampatha-ticket-winning-numbers-titles">
                ------- ජයග්‍රාහී අංක -------
              </div>

              <div className="jayasampatha-letter-and-numbers">
                <div className="jayasampatha-letter">
                  <div className="jayasampatha-letter-label">ඉංග්‍රීසි අකුර</div>
                  <div className="jayasampatha-letter-badge">
                    <div className="jayasampatha-letter-text">{lottery.ball5 || "X"}</div>
                  </div>
                </div>

                <div className="jayasampatha-ticket-winning-numbers-containersn">
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
            <div className="jayasampatha-ticket-bottom-sn">
            ත්‍යාගවල මුළු වටිනාකම : {formatCurrency(lottery.total) || "Loading..."}
            </div>

            {/* Special Numbers Section - Temporarily commented out */}
            {/*
            {lottery.show_special_section && (
              <div className="jayasampatha-ticket-special-prize-container">
                <img
                  src="/images/sc.png"
                  alt="Special Prize"
                  className="jayasampatha-ticket-special-prize-icon"
                />
                <div className="special-numbers-sn">
                  {lottery.special1 && (
                    <>
                      විශේෂ අංකය {lottery.special1_label || 'රු. 50,000/-'}: {lottery.special1}
                      <br />
                    </>
                  )}
                    <div className="lagna-special-txt">
                      {lottery.special2 && <>{lottery.special2_label || 'රු. 40/-'}: {lottery.special2}</>}
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

export default JayasampathaSinhala;

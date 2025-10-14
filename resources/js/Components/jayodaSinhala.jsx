import axios from "axios";
import { useEffect, useState } from "react";
import "../../css/jayoda.css";

const JayodaSinhala = ({ name = "Jayoda" }) => {
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


  return (
    <div className="jayoda-ticket-containersn">
      <div className="jayoda-ticket-card">
        <div className="jayoda-ticket-header">
          <div className="jayoda-ticket-logo-container">
            <img
              src="/images/logo/jayoda.png"
              alt={name}
              className="jayoda-ticket-logosn"
            />
          </div>
          <div className="jayoda-ticket-draw-number-containersn">
            
              <div className="jayoda-ticket-draw-number-text">
                දිනුම් වාරය
              </div>
              <div className="jayoda-ticket-draw-number-text1-sn">
                {lottery.number || "Loading..."}
              </div>
           
            
              <div className="jayoda-ticket-colour-text">
                වර්ණය
              </div>
              <div className="jayoda-ticket-colour-text1">
                {translateColor(lottery.color) || "Loading..."}
              </div>
            
            <div className="jayoda-ticket-winning-numbers">
              <div className="jayoda-ticket-winning-numbers-titles">
                <span>------- ජයග්‍රාහී අංක -------</span>
                <span>ඉංග්‍රීසි අක්ෂරය</span>
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
            <div className="jayoda-ticket-bottom-sn">
                මීළඟ සුපිරි ජයමල්ල : {formatCurrency(lottery.next_super) || "Loading..."}
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
                        {lottery.special1_label || 'රු. 50,000/-'} පනම් ප්‍රිස්කුවන් සඳහා <br/> විශේෂ අංකය :{" "}
                        {lottery.special1 || "Loading..."}
                        <br />
                      </>
                    )}
                    {lottery.special2 && <>{lottery.special2_label || 'රු. 40/-'} : {lottery.special2}</>}
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

export default JayodaSinhala;

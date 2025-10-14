import axios from "axios";
import { useEffect, useState } from "react";
import "../../css/kapruka.css";

const KaprukaSinhala = ({ name = "Kapruka" }) => {
  const [lottery, setLottery] = useState({
    number: null,
    color: null,
    ball1: null,
    ball2: null,
    ball3: null,
    ball4: null,
    ball5: null,
    ball6: null,
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

  // Combine individual balls into an array
  const balls = [lottery.ball5, lottery.ball6, lottery.ball1,lottery.ball2,lottery.ball3,lottery.ball4].filter(
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
    <div className="kapruka-ticket-containersn">
      <div className="kapruka-ticket-card">
        <div className="kapruka-ticket-header">
          <div className="kapruka-ticket-logo-container">
            <img
              src="/images/logo/kapruka.png"
              alt={name}
              className="kapruka-ticket-logosn"
            />
          </div>
          <div className="kapruka-ticket-draw-number-containersn">
            
              <div className="kapruka-ticket-draw-number-text">
                දිනුම් වාරය
              </div>
              <div className="kapruka-ticket-draw-number-text1-sn">
                {lottery.number || "Loading..."}
              </div>
            
            
              <div className="kapruka-ticket-colour-text">
                වර්ණය
              </div>
              <div className="kapruka-ticket-colour-text1">
                {translateColor(lottery.color) || "Loading..."}
              </div>
        
              <div className="kapruka-ticket-winning-numbers-s">
              <div className="kapruka-ticket-winning-numbers-titles">
                ඉංග්‍රීසි අක්ෂරය
              </div>
              <div className="kapruka-ticket-winning-numbers-title1s">
               සුපිරි අංකය
              </div>
              <div className="kapruka-ticket-winning-numbers-title2s">
                ජයග්‍රාහී අංක
              </div>
              </div>
              <div className="kapruka-ticket-winning-numbers-containersn">
                {balls.length > 0
                  ? balls.map((ball, index) => (
                      
                        <div key={index} className="kapruka-ticket-winning-number">
                          <div className="kapruka-ticket-winning-number-text-sn">
                            {ball}
                          </div>
                        </div>
                     
                    ))
                  : "Loading..."}
              </div>
           
              <div className="kapruka-ticket-special">
            <div className="kapruka-ticket-bottoms">
                මීළඟ සුපිරි ජයමල්ල : {formatCurrency(lottery.next_super) || "Loading..."}
            </div>
            {/* Special Numbers Section - Temporarily removed */}
            {/**
            {lottery.show_special_section && (
              <div className="kapruka-ticket-special-prize-container">
                <img
                  src="/images/sc.png"
                  alt="Special Prize"
                  className="kapruka-ticket-special-prize-icon"
                />
                <div className="lagna-special-numbers">
                  {lottery.special1 && (
                    <>
                      <div className="lagna-special-numbers-text-pp"> 
                      විශේෂ අංකය <br /> {lottery.special1_label || 'රු. 50,000/-'} සඳහා
                      </div>
                      <div className="lagna-special-txt"> {lottery.special1} </div>
                    </>
                  )}
                  </div>
                  
                  <div className="lagna-special-numbers">
                      <div className="lagna-special-numbers-text-pp"> 
                      විශේෂ අංකය <br /> {lottery.special2_label || 'රු. 40/-'} සඳහා
                      </div>
                      <div className="lagna-special-txt"> {lottery.special2 && <>{lottery.special2}</>} </div>
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

export default KaprukaSinhala;

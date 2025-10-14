import axios from "axios";
import { useEffect, useState } from "react";
import "../../css/superball.css";

const SuperballEnglish = ({ name = "Superball" }) => {
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
    special3: null,
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

  const formatCurrency = (amount) => {
    return "Rs. " + Number(amount).toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
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
          <div className="superball-ticket-draw-number-container">
            
              <div className="superball-ticket-draw-number-text">
                Draw Number
              </div> 
              <div className="superball-ticket-draw-number-text1">
                {lottery.number || "Loading..."}
              </div>

          
              <div className="superball-ticket-colour-text">
                Colour
              </div>
              <div className="superball-ticket-colour-text1">
                {lottery.color || "Loading..."}
              </div>
            
            <div className="superball-ticket-winning-numbers">
              <div className="superball-ticket-winning-numbers-title">
                <span>---- Winning Numbers ----</span>
                <span>English letter</span>
              </div>
              <div className="superball-ticket-winning-numbers-container">
                {balls.length > 0
                  ? balls.map((ball, index) => (
                      
                        <div key={index} className="superball-ticket-winning-number">
                          <div className="superball-ticket-winning-number-text">
                            {ball}
                          </div>
                        </div>
                     
                    ))
                  : "Loading..."}
              </div>
            </div>
            <div className="superball-ticket-special">
                <div className="superball-ticket-bottom">
                  Next Super Jackpot :<br/> {formatCurrency(lottery.next_super) || "Loading..."}
                </div>
                {lottery.show_special_section && (
                  <div className="superball-ticket-special-prize-container">
                    <div className="superball-icon-wrapper">
                      <img
                        src="/images/sc.png"
                        alt="Special Prize"
                        className="superball-ticket-special-prize-icon"
                      />
                    </div>

                    <table className="superball-special-numbers">
                    <thead>
                      <tr>
                        <th>Prize</th>
                        <th>Special No.</th>
                      </tr>
                    </thead>
                    <tbody>
                      {lottery.special1 && (
                        <tr>
                          <td>{lottery.special1_label || 'Rs. 50,000/-'}</td>
                          <td><b>{lottery.special1}</b></td>
                        </tr>
                      )}
                      {lottery.special3 && (
                        <tr>
                          <td>{lottery.special3_label || 'Rs. 200/-'}</td>
                          <td>{lottery.special3}</td>
                        </tr>
                      )}
                      {lottery.special2 && (
                        <tr>
                          <td>{lottery.special2_label || 'Rs. 40/-'}</td>
                          <td>{lottery.special2}</td>
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

export default SuperballEnglish;

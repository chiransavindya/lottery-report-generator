import axios from "axios";
import { useEffect, useState } from "react";
import "../../css/superball.css";

const SuperballSinhala = ({ name = "Superball" }) => {
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
    <div className="superball-ticket-containersn">
      <div className="superball-ticket-card">
        <div className="superball-ticket-header">
          <div className="superball-ticket-logo-container">
            <img
              src="/images/logo/superball.png"
              alt={name}
              className="superball-ticket-logosn"
            />
          </div>
          <div className="superball-ticket-draw-number-containersn">
            
              <div className="superball-ticket-draw-number-textsn">
                දිනුම් වාරය
              </div>
              <div className="superball-ticket-draw-number-text1-sn">
                {lottery.number || "Loading..."}
              </div>
           
            
              <div className="superball-ticket-colour-textsn">
                වර්ණය
              </div>
              <div className="superball-ticket-colour-text1">
                {translateColor(lottery.color) || "Loading..."}
              </div>
            
            <div className="superball-ticket-winning-numbers">
              <div className="superball-ticket-winning-numbers-titlesn">
                <span>----- ජයග්‍රාහී අංක -----</span>
                <span>ඉංග්‍රීසි අක්ෂරය</span>
              </div>
              <div className="superball-ticket-winning-numbers-container-sn">
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
              <div className="superball-ticket-bottom-sn">
                මීළඟ සුපිරි ජයමල්ල :<br />
                 {formatCurrency(lottery.next_super) || "Loading..."}
              </div>
              {/* Special Numbers Section */}
              {lottery.show_special_section && (
  <div className="superball-ticket-special-prize-container-sn">
    <img
      src="/images/sc.png"
      alt="Special Prize"
      className="superball-ticket-special-prize-icon-sn"
    />

    <table className="special-prize-table">
      <thead>
        <tr>
          <th>මුදල</th>
          <th>විශේෂ අංකය</th>
        </tr>
      </thead>
      <tbody>
        {lottery.special1 && (
          <tr>
            <td>{lottery.special1_label || 'රු. 50,000/-'}</td>
            <td className="special-number">{lottery.special1}</td>
          </tr>
        )}
        {lottery.special2 && (
          <tr>
            <td>{lottery.special2_label || 'රු. 40/-'}</td>
            <td className="special-number">{lottery.special2}</td>
          </tr>
        )}
        {lottery.special3 && (
          <tr>
            <td>{lottery.special3_label || 'රු. 200/-'}</td>
            <td className="special-number">{lottery.special3}</td>
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

export default SuperballSinhala;

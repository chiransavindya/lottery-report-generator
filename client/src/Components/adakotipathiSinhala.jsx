import axios from "axios";
import { useEffect, useState } from "react";
import "../css/adakotipathi.css";

const AdakotipathiSinhala = ({ name = "Ada kotipathi" }) => {
  const [lottery, setLottery] = useState({
    number: null,
    color: null,
    ball1: null,
    ball2: null,
    ball3: null,
    ball4: null,
    ball5: null,
    next_super: null,
    // special4: null, // Temporarily commented out
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

    if (colorLower === "green") return "කොළ";
    if (colorLower === "light green") return "ලා කොළ";
    if (colorLower === "dark green") return "තද කොළ";
    if (colorLower === "red") return "රතු";
    if (colorLower === "blue") return "නිල්";
    if (colorLower === "light blue") return "ලා නිල්";
    if (colorLower === "dark blue") return "තද නිල්";
    if (colorLower === "orange") return "තැඹිලි";
    if (colorLower === "pink") return "රෝස";
    if (colorLower === "light pink") return "ලා රෝස";
    if (colorLower === "purple") return "දම්";
    if (colorLower === "yellow") return "කහ";
    if (colorLower === "brown") return "දුඹුරු";
    if (colorLower === "light brown") return "ලා දුඹුරු";
    if (colorLower === "dark brown") return "තද දුඹුරු";

    return color;
  };

  const formatCurrency = (amount) => {
    return "රු. " + Number(amount).toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  };

  return (
    <div className="adakotipathi-ticket-containersn">
      <div className="adakotipathi-ticket-card">
        <div className="adakotipathi-ticket-header">
          <div className="adakotipathi-ticket-logo-container">
            <img
              src="/images/logo/adakotipathi.png"
              alt={name}
              className="adakotipathi-ticket-logo-sn"
            />
          </div>
          <div className="adakotipathi-ticket-draw-number-container-sn">

            <div className="adakotipathi-ticket-draw-number-text-sn">
              දිනුම් වාරය
            </div>
            <div className="adakotipathi-ticket-draw-number-text1-sn">
              {lottery.number || "Loading..."}
            </div>

            <div className="adakotipathi-ticket-colour-text-sn">
              වර්ණය
            </div>
            <div className="adakotipathi-ticket-colour-text1-sn">
              {translateColor(lottery.color) || "Loading..."}
            </div>

            <div className="adakotipathi-ticket-winning-numbers">
              <div className="adakotipathi-ticket-winning-numbers-titles">
                <span>----- ජයග්‍රාහී අංක -----</span>
                <span>ඉංග්‍රීසි අක්ෂරය</span>
              </div>
              <div className="adakotipathi-ticket-winning-numbers-containersn">
                {balls.length > 0
                  ? balls.map((ball, index) => (

                    <div key={index} className="adakotipathi-ticket-winning-number">
                      <div className="adakotipathi-ticket-winning-number-text">
                        {ball}
                      </div>
                    </div>

                  ))
                  : "Loading..."}
              </div>
            </div>
            <div className="adakotipathi-ticket-special">
              <div className="adakotipathi-ticket-bottomsn">
                මීළඟ සුපිරි ජයමල්ල : {formatCurrency(lottery.next_super) || "Loading..."}
              </div>
              {/* Special Numbers Section - Temporarily commented out */}
              {/*
                {lottery.special4 && (
                <div className="adakotipathi-ticket-special-prize-container-sn">
                  <img
                    src="/images/sc.png"
                    alt="Special Prize"
                    className="adakotipathi-ticket-special-prize-icon"
                  />

                  <table className="special-prize-table-sn">
                    <thead>
                      <tr>
                        <th>මුදල</th>
                        <th>විශේෂ අංකය</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>රු. 100,000/-</td>
                        <td className="special-number">{lottery.special4}</td>
                      </tr>
                    </tbody>
                  </table>
			 
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

export default AdakotipathiSinhala;

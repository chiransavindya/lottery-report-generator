import axios from "axios";
import { useEffect, useState } from "react";
import "../css/sasiri.css";

const SasiriSinhala = ({ name = "Sasiri" }) => {
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
    const colorLower = color?.toLowerCase().trim() || "";

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

  const formatCount = (count) => {
    // Pad with leading zero if single digit (e.g., 9 becomes 09)
    return count !== null ? String(count).padStart(2, '0') : "00";
  };

  return (
    <div className="sasiri-ticket-containersn">
      <div className="sasiri-ticket-card">
        <div className="sasiri-ticket-header">
          <div className="sasiri-ticket-logo-container">
            <img
              src="/images/logo/sasirisinhala.png"
              alt={name}
              className="sasiri-ticket-logosn"
            />
          </div>
          <div className="sasiri-ticket-draw-number-containersn">

            <div className="sasiri-ticket-draw-number-text-sn">
              දිනුම් වාරය
            </div>
            <div className="sasiri-ticket-draw-number-text1-sn">
              {lottery.number || "Loading..."}
            </div>


            <div className="sasiri-ticket-colour-text-sn">
              වර්ණය
            </div>
            <div className="sasiri-ticket-colour-text1-sn">
              {translateColor(lottery.color) || "Loading..."}
            </div>

            <div className="sasiri-ticket-winning-numbers">
              <div className="sasiri-ticket-winning-numbers-titles">
                -------- ජයග්‍රාහී  අංක --------
              </div>
              <div className="sasiri-ticket-winning-numbers-containersn">
                {balls.length > 0
                  ? balls.map((ball, index) => (

                    <div key={index} className="sasiri-ticket-winning-number">
                      <div className="sasiri-ticket-winning-number-text">
                        {ball}
                      </div>
                    </div>

                  ))
                  : "Loading..."}
                <div className="sasiri-ticket-winner-container-text">
                  අද බිහි වූ දෙලක්ෂපතියන් ගණන
                </div>

                <div className="sasiri-ticket-winner-container1">
                  {formatCount(lottery.count)}
                </div>
              </div>
            </div>
            <div className="sasiri-ticket-special">
              <div className="sasiri-ticket-bottomsn">
                දිනා ඇති මුළු මුදල : {formatCurrency(lottery.total) || "Loading..."}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default SasiriSinhala;

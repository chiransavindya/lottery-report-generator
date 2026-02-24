import React, { useEffect, useState } from "react";
import axios from "axios";
import "../css/headerTamil.css";

const HeaderTamil = () => {
  const [currentDate, setCurrentDate] = useState("");

  useEffect(() => {
    const translateWeekdayToTamil = (weekday) => {
      const weekdaysTamil = {
        Sunday: "ஞாயிற்றுக்கிழமை",
        Monday: "திங்கட்கிழமை",
        Tuesday: "செவ்வாய்க்கிழமை",
        Wednesday: "புதன்கிழமை",
        Thursday: "வியாழக்கிழமை",
        Friday: "வெள்ளிக்கிழமை",
        Saturday: "சனிக்கிழமை",
      };
      return weekdaysTamil[weekday] || weekday;
    };

    const fetchLotteryDate = async () => {
      try {
        // Fetch the first available lottery to get the date from XML
        const response = await axios.get("/api/lottery", { params: { name: "Kapruka" } });
        if (response.data && response.data.date) {
          const date = new Date(response.data.date);
          const options = { weekday: "long" };
          const year = date.getFullYear();
          const month = String(date.getMonth() + 1).padStart(2, "0");
          const day = String(date.getDate()).padStart(2, "0");
          const weekday = date.toLocaleDateString("en-US", options);
          const weekdayTamil = translateWeekdayToTamil(weekday);
          setCurrentDate(`${year}.${month}.${day} ${weekdayTamil}`);
        }
      } catch (error) {
        console.error("Error fetching lottery date:", error);
        // Fallback to current date if API fails
        const date = new Date();
        const options = { weekday: "long" };
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, "0");
        const day = String(date.getDate()).padStart(2, "0");
        const weekday = date.toLocaleDateString("en-US", options);
        const weekdayTamil = translateWeekdayToTamil(weekday);
        setCurrentDate(`${year}.${month}.${day} ${weekdayTamil}`);
      }
    };

    fetchLotteryDate();
  }, []);

  return (
    <div className="headertamil-container">
      <div className="image-container2">
        <img
          src="./images/logo/headertm.png"
          alt="Company Logo"
          className="headertamil-logo"
        />
        <div className="date-container2">
          <div className="date-text2">
            {currentDate || "Loading..."}
          </div>
        </div>
      </div>
    </div>
  );
};

export default HeaderTamil;

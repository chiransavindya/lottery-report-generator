import React, { useEffect, useState } from "react";
import "../../css/headerTamil.css";

const HeaderTamil = () => {
  const [currentDate, setCurrentDate] = useState("");

  useEffect(() => {
    const translateWeekdayToTamil = (weekday) => {
      const weekdaysTamil = {
        Sunday: "ஞாயிறு",
        Monday: "திங்கள்கிழமை",
        Tuesday: "செவ்வாய்க்கிழமை",
        Wednesday: "புதன்கிழமை",
        Thursday: "வியாழக்கிழமை",
        Friday: "வெள்ளிக்கிழமை",
        Saturday: "சனிக்கிழமை",
      };
      return weekdaysTamil[weekday] || weekday;
    };

    const formatDate = () => {
      const date = new Date();
      const options = { weekday: "long" };
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const day = String(date.getDate()).padStart(2, "0");
      const weekday = date.toLocaleDateString("en-US", options);
      const weekdayTamil = translateWeekdayToTamil(weekday);

      return `${year}.${month}.${day} ${weekdayTamil}`;
    };

    setCurrentDate(formatDate());
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

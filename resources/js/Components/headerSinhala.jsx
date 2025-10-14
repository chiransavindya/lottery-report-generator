import React, { useEffect, useState } from "react";
import "../../css/headersin.css";

const HeaderSinhala = () => {
  const [currentDate, setCurrentDate] = useState("");

  useEffect(() => {
    const translateWeekdayToSinhala = (weekday) => {
      const weekdaysSinhala = {
        Sunday: "ඉරිදා",
        Monday: "සඳුදා",
        Tuesday: "අඟහරුවාදා",
        Wednesday: "බදාදා",
        Thursday: "බ්‍රහස්පතින්දා",
        Friday: "සිකුරාදා",
        Saturday: "සෙනසුරාදා",
      };
      return weekdaysSinhala[weekday] || weekday;
    };

    const formatDate = () => {
      const date = new Date();
      const options = { weekday: "long" };
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const day = String(date.getDate()).padStart(2, "0");
      const weekday = date.toLocaleDateString("en-US", options);
      const weekdaySinhala = translateWeekdayToSinhala(weekday);

      return `${year}.${month}.${day} ${weekdaySinhala}`;
    };

    setCurrentDate(formatDate());
  }, []);

  return (
    <div className="headersin-container">
      <div className="image-container1">
        <img
          src="./images/logo/headersn.png"
          alt="Company Logo"
          className="headersin-logo"
        />
        
        <div className="date-container1">
          <div className="date-text1">
            {currentDate || "Loading..."}
          </div>
        </div>
      </div>
    </div>
  );
};

export default HeaderSinhala;

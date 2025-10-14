import React, { useEffect, useState } from "react";
import "../../css/header.css";

const HeaderEnglish = () => {
  const [currentDate, setCurrentDate] = useState("");

  useEffect(() => {
    const formatDate = () => {
      const date = new Date();
      const options = { weekday: "long" };
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const day = String(date.getDate()).padStart(2, "0");
      const weekday = date.toLocaleDateString("en-US", options);

      return `${year}.${month}.${day} ${weekday}`;
    };

    setCurrentDate(formatDate());
  }, []);

  return (
    <>
  
      <div className="image-container">
        <img
          src="./images/logo/headeren.png"
          alt="Company Logo"
          className="header-logo"
        />

          <div className="header-date-text">
            {currentDate || "Loading..."}
          </div>
          
        </div>
    </>
  );
};

export default HeaderEnglish;

import React, { useEffect, useState } from "react";
import axios from "axios";
import "../css/header.css";

const HeaderEnglish = () => {
  const [currentDate, setCurrentDate] = useState("");

  useEffect(() => {
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
          setCurrentDate(`${year}.${month}.${day} ${weekday}`);
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
        setCurrentDate(`${year}.${month}.${day} ${weekday}`);
      }
    };

    fetchLotteryDate();
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

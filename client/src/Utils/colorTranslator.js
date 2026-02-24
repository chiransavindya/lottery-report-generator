const translateColor = (color) => {
    const colorMap = {
      green: "කොළ",
      red: "රතු",
      blue: "නිල්",
      orange: "තැඹිලි",
      pink: "රෝස",
      purple: "දම්",
      "light blue": "ලා නිල්",
      "light pink": "ලා රෝස",
    };
  
    return colorMap[color.toLowerCase()] || color;
  };
  
  export default translateColor;
  
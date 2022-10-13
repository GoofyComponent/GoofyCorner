/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./assets/**/*.js", "./templates/**/*.html.twig"],
  theme: {
    extend: {
      fontFamily: {
        "open-sans": ["Open Sans", "sans-serif"],
      },
      textColor: {
        primary: "#D96236",
        secondary: "#F2A488",
        "lightgray-gc": "#F2F2F2",
      },
      colors: {
        primary: "#D96236",
        secondary: "#F2A488",
        "lightgray-gc": "#F2F2F2",
      },
    },
    plugins: [],
  },
};

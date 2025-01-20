import type {Config} from "tailwindcss";

import {colors} from "./theme/colors/colors";

const config: Config = {
    content: [
        "./src/components/**/*.{js,ts,jsx,tsx,mdx}",
        "./src/app/**/*.{js,ts,jsx,tsx,mdx}",
    ],
    darkMode: ["class"],
    theme: {
        extend: {
            keyframes: {
                rotate180: {
                    "0%": {transform: "rotate(0deg)"},
                    "100%": {transform: "rotate(180deg)"},
                },
                appear: {
                    "0%": {opacity: "0"},
                    "100%": {opacity: "1"},
                },
                shimmer: {
                    "100%": {
                        transform: "translateX(100%)",
                    },
                },
                livePulse: {
                    "0%": {
                        transform: "scale(0.95)",
                        boxShadow: "0 0 0 0 rgba(22, 155, 91, 0.7)",
                    },
                    "70%": {
                        transform: "scale(1)",
                        boxShadow: "0 0 0 5px rgba(22, 155, 91, 0)",
                    },
                    "100%": {
                        transform: "scale(0.95)",
                        boxShadow: "0 0 0 0 rgba(22, 155, 91, 0)",
                    },
                },
            },
            animation: {
                rotate180: "rotate180 0.3s linear",
                appear: "appear 1.7s linear",
                "appear-long": "appear 3s linear",
                livePulse: "livePulse 1.5s linear infinite",
            },
            backgroundImage: {
                "gradient-radial": "radial-gradient(var(--tw-gradient-stops))",
                "gradient-conic":
                    "conic-gradient(from 180deg at 50% 50%, var(--tw-gradient-stops))",
                "conic-gradient-email-light":
                    "conic-gradient(transparent 270deg, #8851FF, transparent)",
                "conic-gradient-email-dark":
                    "conic-gradient(transparent 270deg, rgba(255, 255, 255, 1), transparent)",
            },
            fontFamily: {
                redaction35: ["var(--font-redaction35)"],
                abcDiatype: ["var(--font-abc-diatype)"],
            },
            spacing: {
                "6.5": "26px",
                "15": "60px",
            },
            screens: {
                mobile: {max: "1024px"},
                "mobile-small": {max: "375px"},
                "mobile-medium": {max: "460px"},
                tablet: {min: "500px", max: "1024px"},
                ["small-desktop"]: {min: "1024px", max: "1440px"},
            },
            colors,
        },
        fontSize: {
            xs: "8px",
            sm: "10px",
            base: "12px",
            lg: "14px",
            xl: "16px",
            "2xl": "18px",
            "3xl": "20px",
            "4xl": "24px",
            "5xl": "28px",
            "6xl": "32px",
            "7xl": "36px",
            "8xl": "40px",
            "9xl": "48px",
        },
    },
    plugins: [
        require("./theme/plugins/custom-classes"),
        require("./theme/plugins/custom-headers"),
    ],
};
export default config;

import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";
import animate from "tailwindcss-animate";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
            },
            animation: {
                "accordion-down": "accordion-down 0.2s ease-out",
                "accordion-up": "accordion-up 0.2s ease-out",
                "fade-in": "fade-in 0.3s ease-in-out",
                "fade-out": "fade-out 0.3s ease-in-out",
                "slide-in-right": "slide-in-right 0.3s ease-out",
                "slide-out-right": "slide-out-right 0.3s ease-out",
                "slide-in-left": "slide-in-left 0.3s ease-out",
                "slide-out-left": "slide-out-left 0.3s ease-out",
                "slide-up": "slide-up 0.3s ease-out",
                "slide-down": "slide-down 0.3s ease-out",
                "bounce-in": "bounce-in 0.6s ease",
            },
            keyframes: {
                "accordion-down": {
                    from: { height: 0 },
                    to: { height: "var(--radix-accordion-content-height)" },
                },
                "accordion-up": {
                    from: { height: "var(--radix-accordion-content-height)" },
                    to: { height: 0 },
                },
                "fade-in": {
                    from: { opacity: 0 },
                    to: { opacity: 1 },
                },
                "fade-out": {
                    from: { opacity: 1 },
                    to: { opacity: 0 },
                },
                "slide-in-right": {
                    from: { transform: "translateX(100%)" },
                    to: { transform: "translateX(0)" },
                },
                "slide-out-right": {
                    from: { transform: "translateX(0)" },
                    to: { transform: "translateX(100%)" },
                },
                "slide-in-left": {
                    from: { transform: "translateX(-100%)" },
                    to: { transform: "translateX(0)" },
                },
                "slide-out-left": {
                    from: { transform: "translateX(0)" },
                    to: { transform: "translateX(-100%)" },
                },
                "slide-up": {
                    from: { transform: "translateY(10px)", opacity: 0 },
                    to: { transform: "translateY(0)", opacity: 1 },
                },
                "slide-down": {
                    from: { transform: "translateY(-10px)", opacity: 0 },
                    to: { transform: "translateY(0)", opacity: 1 },
                },
                "bounce-in": {
                    "0%": { transform: "scale(0.8)", opacity: 0 },
                    "70%": { transform: "scale(1.05)", opacity: 1 },
                    "100%": { transform: "scale(1)", opacity: 1 },
                },
            },
        },
    },

    plugins: [forms, animate],
};

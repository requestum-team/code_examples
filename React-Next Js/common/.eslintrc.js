/* eslint-disable @typescript-eslint/no-var-requires */
const APP_ALIASES = require("./app-structure.config.ts");

const importsAliasesRegexValue = APP_ALIASES.reduce(
    (acc, curr, index, array) => {
        acc += curr + (index === array.length - 1 ? "" : "|");

        return acc;
    },
    ""
);

/**
 * @type {import("eslint").Linter.Config}
 */
module.exports = {
    extends: [
        "plugin:@typescript-eslint/recommended",
        "next/core-web-vitals",
        "next",
        "plugin:prettier/recommended",
        "plugin:storybook/recommended",
    ],
    parser: "@typescript-eslint/parser",
    plugins: ["@typescript-eslint", "simple-import-sort"],
    ignorePatterns: ["**/*/generated/*"],
    overrides: [
        {
            files: ["*.ts", "*.tsx"],
            rules: {
                "prettier/prettier": [
                    "error",
                    {
                        endOfLine: "auto",
                        bracketLine: true,
                    },
                ],
                "react/display-name": "off",
                "no-undef": "off",
                "no-shadow": "off",
                "react/react-in-jsx-scope": "off",
                "@typescript-eslint/no-shadow": ["error"],
                "@typescript-eslint/no-empty-interface": "off",
                quotes: ["error", "double"],
                "simple-import-sort/imports": [
                    "error",
                    {
                        groups: [["^@?\\w"], [`^(${importsAliasesRegexValue})(/.*|$)`]],
                    },
                ],
                "simple-import-sort/exports": "error",
                "react/jsx-curly-brace-presence": ["error", {props: "never"}],
            },
        },
    ],
};

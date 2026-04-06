import js from "@eslint/js";

export default [
  js.configs.recommended,
  {
    languageOptions: {
      ecmaVersion: 2021,
      sourceType: "script",
      globals: {
        window: "readonly",
        document: "readonly",
        console: "readonly",
        fetch: "readonly",
        jQuery: "readonly",
        $: "readonly",
        wp: "readonly",
        ajaxurl: "readonly"
      }
    },
    rules: {
      "no-unused-vars": "warn",
      "no-console": "warn",
      "eqeqeq": "error",
      "semi": ["error", "always"],
      "quotes": ["warn", "single"],
      "no-var": "warn",
      "prefer-const": "warn"
    }
  }
];
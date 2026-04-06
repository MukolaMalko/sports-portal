# Linting у проекті Sports Portal

## Обраний лінтер та причини вибору

Для проекту обрано **ESLint v10** — найпопулярніший статичний аналізатор JavaScript коду.

### Чому ESLint?

| Критерій | ESLint |
|---|---|
| Підтримка ES2021+ | ✅ |
| Гнучка конфігурація | ✅ |
| Авто-виправлення (`--fix`) | ✅ |
| Інтеграція з VS Code | ✅ |
| Підтримка WordPress globals | ✅ |
| Активна підтримка | ✅ |

Оскільки проект використовує JavaScript для взаємодії з WordPress REST API у браузері, ESLint є оптимальним вибором — він підтримує браузерне середовище, jQuery та специфічні глобальні змінні WordPress (`wp`, `ajaxurl`).

---

## Встановлення

### Вимоги
- Node.js v18+
- npm

### Кроки встановлення

```bash
# Ініціалізація npm проекту
npm init -y

# Встановлення ESLint та конфігураційного пакету
npm install --save-dev eslint @eslint/js
```

---

## Конфігурація

Файл конфігурації: `eslint.config.js`

```javascript
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
```

---

## Базові правила та їх пояснення

| Правило | Рівень | Пояснення |
|---|---|---|
| `no-unused-vars` | warning | Попереджає про змінні, які оголошені але не використовуються — засмічують код |
| `no-console` | warning | Попереджає про `console.log()` — їх не варто залишати у продакшн коді |
| `eqeqeq` | error | Забороняє `==` та `!=`, вимагає `===` та `!==` — запобігає неочевидним перетворенням типів |
| `semi` | error | Вимагає крапку з комою в кінці виразів — єдиний стиль коду |
| `quotes` | warning | Рекомендує одинарні лапки для рядків — єдиний стиль в проекті |
| `no-var` | warning | Рекомендує `let`/`const` замість застарілого `var` |
| `prefer-const` | warning | Рекомендує `const` для змінних, які не змінюються після оголошення |

---

## Запуск лінтера

### Перевірка коду
```bash
npm run lint
```

### Автоматичне виправлення
```bash
npm run lint:fix
```

### Скрипти у `package.json`

```json
"scripts": {
  "lint": "eslint js/app.js",
  "lint:fix": "eslint js/app.js --fix"
}
```

---

## Результати лінтингу

### До виправлення

Запуск лінтера виявив **31 проблему** (19 errors, 12 warnings):

| Тип помилки | Кількість | Правило |
|---|---|---|
| Відсутня крапка з комою | 15 | `semi` |
| Використання `var` | 8 | `no-var` |
| Невикористані змінні | 3 | `no-unused-vars` |
| Зайві `console.log()` | 2 | `no-console` |
| Подвійне `==` замість `===` | 1 | `eqeqeq` |
| Повторне оголошення змінної | 1 | `no-redeclare` |
| Невизначені глобальні змінні | 1 | `no-undef` |

### Після виправлення

```
✖ 0 problems (0 errors, 0 warnings)
```

**Виправлено 100% помилок.**

### Що було виправлено

1. **`var` → `const`/`let`** — всі оголошення змінних замінено на сучасний синтаксис
2. **`==` → `===`** — оператор порівняння замінено на строгий
3. **Видалено невикористані змінні** — `unused`, `data` та дублікат `apiUrl`
4. **Додано крапки з комою** — автоматично через `--fix`
5. **Додано `fetch` до globals** — браузерний API зареєстровано в конфігурації ESLint
6. **Видалено `console.log()`** — замінено на корисний код


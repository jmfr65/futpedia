# Coding Standards

## PHP

*   **Style:** PSR-12 (Extended Coding Style).
    *   4 spaces for indentation, no tabs.
    *   Opening braces for classes and methods on the next line.
    *   Opening braces for control structures on the same line.
*   **Naming Conventions:**
    *   Variables: `snake_case` (e.g., `$user_name`).
    *   Functions: `snake_case` (e.g., `get_user_data()`).
    *   Classes: `PascalCase` (e.g., `UserManager`).
    *   Class Methods: `camelCase` (e.g., `getUserData()`).
    *   Constants: `UPPER_SNAKE_CASE` (e.g., `MAX_USERS`).
*   **Comments:** PHPDoc for classes, methods, and functions. Inline comments for complex logic.
*   **PHP Tags:** Use `<?php ... ?>` and `<?= ... ?>` for short echo. Avoid `<? ... ?>`.
*   **Files:** One file per main definition (e.g., a class or a set of related functions).

## JavaScript (JS)

*   **Style:** Consistent indentation (e.g., 2 or 4 spaces).
*   **Naming Conventions:**
    *   Variables and functions: `camelCase` (e.g., `userName`, `getUserData()`).
    *   Classes: `PascalCase`.
    *   Constants: `UPPER_SNAKE_CASE`.
*   **Semicolons:** Always use at the end of statements.
*   **Strict Mode:** Use `'use strict';` at the beginning of scripts.

## CSS

*   **Naming Conventions:** `kebab-case` for classes and IDs (e.g., `.main-menu`, `#submit-button`).
*   **Formatting:** Each declaration on a new line, indented.
*   **Comments:** For important sections.

## SQL

*   **Keywords:** Uppercase (e.g., `SELECT`, `FROM`, `WHERE`, `UPDATE`, `INSERT INTO`).
*   **Table and Column Names:** `snake_case` (e.g., `users`, `user_name`, `registration_date`).
*   **Indentation:** For complex queries, indent clauses to improve readability.

prueba
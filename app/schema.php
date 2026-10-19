<?php
declare(strict_types=1);

/** Columns added after the first release: [table, column, definition]. Applied by install.php if missing. */
function schema_migrations(): array
{
    return [
        ['services', 'image', 'VARCHAR(255) NULL'],
        ['services', 'slug', 'VARCHAR(190) NULL'],
        ['services', 'features', 'TEXT NULL'],
        ['services', 'content', 'TEXT NULL'],
        ['projects', 'slug', 'VARCHAR(190) NULL'],
        ['projects', 'client', 'VARCHAR(190) NULL'],
        ['projects', 'tech_stack', 'TEXT NULL'],
        ['projects', 'content', 'TEXT NULL'],
    ];
}

function column_exists(string $table, string $column): bool
{
    if (db_driver() === 'mysql') {
        return (bool) row("SHOW COLUMNS FROM $table LIKE ?", [$column]);
    }
    foreach (rows("PRAGMA table_info($table)") as $col) {
        if ($col['name'] === $column) {
            return true;
        }
    }
    return false;
}

/** CREATE TABLE statements that work on both SQLite and MySQL. */
function schema_statements(string $driver): array
{
    $my  = $driver === 'mysql';
    $id  = $my ? 'id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY' : 'id INTEGER PRIMARY KEY AUTOINCREMENT';
    $end = $my ? ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci' : ')';
    $ord = 'sort_order INT NOT NULL DEFAULT 0, is_active INT NOT NULL DEFAULT 1';

    return [
        "CREATE TABLE IF NOT EXISTS admins ($id,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(190) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            last_login DATETIME NULL,
            created_at DATETIME NOT NULL$end",

        "CREATE TABLE IF NOT EXISTS settings (
            setting_key VARCHAR(100) NOT NULL PRIMARY KEY,
            setting_value TEXT NULL$end",

        "CREATE TABLE IF NOT EXISTS technologies ($id,
            icon VARCHAR(100) NULL,
            name VARCHAR(100) NOT NULL,
            $ord$end",

        "CREATE TABLE IF NOT EXISTS services ($id,
            icon VARCHAR(100) NULL,
            title VARCHAR(190) NOT NULL,
            description TEXT NULL,
            link_text VARCHAR(100) NULL,
            link_url VARCHAR(255) NULL,
            image VARCHAR(255) NULL,
            slug VARCHAR(190) NULL,
            features TEXT NULL,
            content TEXT NULL,
            $ord$end",

        "CREATE TABLE IF NOT EXISTS stats ($id,
            value INT NOT NULL DEFAULT 0,
            suffix VARCHAR(20) NULL,
            label VARCHAR(100) NOT NULL,
            $ord$end",

        "CREATE TABLE IF NOT EXISTS projects ($id,
            title VARCHAR(190) NOT NULL,
            category VARCHAR(100) NULL,
            description TEXT NULL,
            image VARCHAR(255) NULL,
            icon VARCHAR(100) NULL,
            color VARCHAR(20) NULL,
            url VARCHAR(255) NULL,
            slug VARCHAR(190) NULL,
            client VARCHAR(190) NULL,
            tech_stack TEXT NULL,
            content TEXT NULL,
            $ord$end",

        "CREATE TABLE IF NOT EXISTS process_steps ($id,
            icon VARCHAR(100) NULL,
            title VARCHAR(190) NOT NULL,
            description TEXT NULL,
            $ord$end",

        "CREATE TABLE IF NOT EXISTS testimonials ($id,
            name VARCHAR(100) NOT NULL,
            role VARCHAR(190) NULL,
            quote TEXT NOT NULL,
            avatar VARCHAR(255) NULL,
            rating INT NOT NULL DEFAULT 5,
            $ord$end",

        "CREATE TABLE IF NOT EXISTS pricing_plans ($id,
            name VARCHAR(100) NOT NULL,
            subtitle VARCHAR(190) NULL,
            price VARCHAR(50) NULL,
            period VARCHAR(50) NULL,
            features TEXT NULL,
            button_text VARCHAR(100) NULL,
            button_url VARCHAR(255) NULL,
            is_featured INT NOT NULL DEFAULT 0,
            badge_text VARCHAR(50) NULL,
            $ord$end",

        "CREATE TABLE IF NOT EXISTS faqs ($id,
            question VARCHAR(255) NOT NULL,
            answer TEXT NOT NULL,
            $ord$end",

        "CREATE TABLE IF NOT EXISTS pages ($id,
            title VARCHAR(190) NOT NULL,
            slug VARCHAR(190) NOT NULL UNIQUE,
            content TEXT NULL,
            meta_description VARCHAR(255) NULL,
            show_in_footer INT NOT NULL DEFAULT 1,
            $ord,
            created_at DATETIME NULL,
            updated_at DATETIME NULL$end",

        "CREATE TABLE IF NOT EXISTS messages ($id,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(190) NOT NULL,
            phone VARCHAR(50) NULL,
            service VARCHAR(100) NULL,
            budget VARCHAR(100) NULL,
            message TEXT NOT NULL,
            ip VARCHAR(45) NULL,
            is_read INT NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL$end",

        "CREATE TABLE IF NOT EXISTS subscribers ($id,
            email VARCHAR(190) NOT NULL UNIQUE,
            created_at DATETIME NULL$end",

        "CREATE TABLE IF NOT EXISTS login_attempts ($id,
            ip VARCHAR(45) NOT NULL,
            attempted_at DATETIME NOT NULL$end",
    ];
}

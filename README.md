# ThemeGrill SDK

A lightweight SDK that provides common functionality and shared features for ThemeGrill themes and plugins.

---

### ⚙️ Overview
The **ThemeGrill SDK** is a customized fork of the [ThemeIsle SDK](https://github.com/Codeinwp/themeisle-sdk), tailored specifically for ThemeGrill’s ecosystem.
It includes selected features and internal improvements to better align with our workflow, architecture, and product distribution.

---

### 📦 Installation

You can install this SDK via Composer:

```bash
composer require themegrill/themegrill-sdk
```
Then, manually autoload the SDK in your project's `composer.json` file:
```json
{
  "autoload": {
    "files": [
      "vendor/themegrill/themegrill-sdk/load.php"
    ]
  }
}
```
---

### 🧩 Differences from the Original
- Removed unused modules and dependencies
- Customized namespaces, prefixes, and structure
- Enhanced compatibility with ThemeGrill themes and plugins

---

### 📜 License
This project is licensed under the **GNU General Public License v3.0 (GPLv3)**.
See the [LICENSE](./LICENSE) file for details.

---

### 🙏 Credits
This package is a fork of [CodeinWP/ThemeIsle-SDK](https://github.com/Codeinwp/themeisle-sdk).
Full credit to the original authors and contributors for their foundational work.

---

© ThemeGrill. All rights reserved.

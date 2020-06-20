# WP Glossary
A beautiful glossary for WordPress

## Users
Todo: Insert screenshots etc. user documentation

## Developers
### Dependencies
The following software has to be installed in order for you to be able to adjust and build this plugin (higher versions should also work):
1. [npm v14.x](https://github.com/nodesource/distributions)
2. [php 7.4](https://www.php.net/)
3. [phpcs](https://github.com/squizlabs/PHP_CodeSniffer)
4. [composer](https://getcomposer.org/)
5. [vscode](https://code.visualstudio.com/) (optional)
### Setup
1. Install npm dependencies `npm i`
2. Install composer dependencies `composer install`
3. Configure `phpcs` for use with WordPress guidelines `phpcs --config-set installed_paths <path to repository>/vendor/wp-coding-standards/wpcs`
4. Run `phpcs -i` which should return amongst other things `WordPress, WordPress-Extra, WordPress-Docs and WordPress-Core`
5. Install the phpcs extension for vscode (optional)
6. Install the eslint extension for vscode (optional)
### Scripts
#### 📝  `npm run start`
- Use to compile and run the block in development mode.
- Watches for any changes and reports back any errors in your js code.
#### 📝  `npm run build`
- Use to build the plugin in production mode
- Output is glossary.zip inside the repository root.
- Reports js errors if any. Otherwise shows total bytes processed.
#### 📝  `npm run eject`
- Use to eject your plugin out of `create-guten-block`.
- Provides all the configurations so you can customize the project as you want.
- It's a one-way street, `eject` and you have to maintain everything yourself.
- You don't normally have to `eject` a project because by ejecting you lose the connection with `create-guten-block` and from there onwards you have to update and maintain all the dependencies on your own.

> This project was bootstrapped with [Create Guten Block](https://github.com/ahmadawais/create-guten-block).
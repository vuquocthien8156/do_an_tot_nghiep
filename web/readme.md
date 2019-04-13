# Environment requiments
1. PHP 7.2 up with extension mbstring, pgsql, mongodb
2. npm 5.6 up
3. yarn
4. composer

# After clone source code
```
$ cd <project root directory> (tới thư mục web)
$ composer install --ignore-platform-reqs
$ yarn install --ignore-platform-reqs
$ npm run dev
```
```
$ cp .env.example .env
$ php artisan key:generate
$ php artisan config:cache
$ php artisan vue-i18n:generate
```

# Development
```
$ npm run watch
```

# Coding convention
1. File name
    
    | file        | type           | convention  |
    | --- |:---:| ---|
    | javascript | VueJs component | Use camel case and uppercase first character, e.g: `Chart.vue`, `ExampleNamingComponent.vue` |
    |  | other file | use kebab case, e.g: `trade.js`, `finance-deposit.js` |
    | css | | use kebab case |
2. Variable name
    
    | code | type | convention |
    | --- | --- | --- |
    | php | variable | camelCase, e.g: `$name`, `$numberOfUsers` |
    |    | class    | camelCase and uppercase first character, e.g: `TradeComtroller`|
3. Git note
    1. Create new branch for each task / topic / feature **BEFORE** development
    2. **DO NOT** commit genereated stuffs, e.g: js, css file in public directory
    3. 
    
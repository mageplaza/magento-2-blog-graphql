# Magento 2 Blog GraphQL/PWA

Magento 2 Blog GraphQL is a part of Blog extendion that add GraphQL features, this support for PWA Studio.
## 1. How to install

Run the following command in Magento 2 root folder:

```
composer require mageplaza/module-blog-graphql
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```

**Note:**
Magento 2 Blog GraphQL requires installing [Mageplaza blog](https://github.com/mageplaza/magento-2-blog) in your Magento installation.

## 2. How to use

To perform GraphQL queries in Magento, please do the following requirements:

- Use Magento 2.3.x or higher. Return your site to developer mode
- Install the ChromeiQL extension for Chrome browser (currently does not support other browsers)
- Set GraphQL endpoint as `http://<magento2-3-server>/graphql` in url box, click **Set endpoint**. 
(e.g. `http://develop.mageplaza.com/graphql`)
- Perform a query in the left cell, click the **Run** button or **Ctrl + Enter** to see the result in the right cell
- To view the queries that the **Mageplaza Blog GraphQL** extension supports, you can look in `Docs > Query` in the right corner

![](https://i.imgur.com/gJ3Dx0f.png)

## 3. Devdocs

- [Blog API & examples](https://documenter.getpostman.com/view/10589000/SzRxXqt3?version=latest#intro)
- [Blog GraphQL & examples](https://documenter.getpostman.com/view/10589000/SzS1T8pe?version=latest)


![Magento 2 blog graphql pwa](https://i.imgur.com/xbRnefr.png)


## 4. Contribute to this module

Feel free to **Fork** and contrinute to this module and create a pull request so we will merge your changes main branch.

## 5. Get Support

Feel free to [contact us](https://www.mageplaza.com/contact.html) if you have any further questions.

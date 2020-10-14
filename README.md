# Magento 2 Blog GraphQL (Support PWA)

Magento 2 Blog GraphQL is a part of Blog extendion that add GraphQL features, this support for PWA Studio.
## How to install

Run the following command in Magento 2 root folder:

```
composer require mageplaza/module-blog-graphql
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```

## How to use

To perform GraphQL queries in Magento, please do the following requirements:

- Use Magento 2.3.x. Return your site to developer mode
- Install the ChromeiQL extension for Chrome browser (currently does not support other browsers)
- Set GraphQL endpoint as `http://<magento2-3-server>/graphql` in url box, click **Set endpoint**. 
(e.g. http://develop.mageplaza.com/graphql/ce232/graphql)
- Perform a query in the left cell, click the **Run** button or **Ctrl + Enter** to see the result in the right cell
- To view the queries that the **Mageplaza Blog GraphQL** extension supports, you can look in `Docs > Query` in the right corner

![](https://i.imgur.com/gJ3Dx0f.png)

# Magento 2 Blog GraphQL

**Magento 2 Blog GraphQL is a part of Mageplaza Blog extension that adds GraphQL features, this supports PWA Studio.** This upgrade means to give the extension extended scalability to work even more smoothly on your website. Your store will also has a hassle-free and seamless experience with any updates you make in the future thanks to the PWA compatibility.

[Mageplaza Blog extension for Magento 2](https://www.mageplaza.com/magento-2-better-blog/) enables creating and managing blogs right on the Magento 2 store with essential features and functionalities for a blog to perform well as usual. 

As the Magento 2 Blog extension is integrated right into the Magento backend, the store admin can manage their store and the blog all in one place. It’s easy and convenient to open a blog on your store without any third-party framework needed. The extension also allows you to create a blog that is specified for a specific category. So that in case you want to show more information about specific products to promote or simply provide customers with a more in-depth understanding of them, you can do it without any difficulties. 

You don’t have to worry whether your blog looks ugly when showing on your store as the Magento 2 Blog is developed with a responsive design. This blog will be displayed in a clean-cut and seamless interface across any device and screen that your customers root to visit your store. The off-canvas menu makes it easy for customers to read and interact with your blog. They can view, browse through the blog’s elements, and share your blog posts on other social networks with one simple click. 

The module also supports Blog Widgets that you can use to create specific categories for your blog, such as recent posts or new posts. This widget will be displayed in the sidebar of your blog, making it convenient for visitors to dig up the information you show; at the same time, designate your page with a well-organized and informative layout.  


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

- Use Magento 2.3.x or higher. Set your site to [developer mode](https://www.mageplaza.com/devdocs/enable-disable-developer-mode-magento-2.html).
- Set GraphQL endpoint as `http://<magento2-server>/graphql` in url box, click **Set endpoint**. 
(e.g. `http://dev.site.com/graphql`)
- To view the queries that the **Mageplaza Blog GraphQL** extension supports, you can look in `Docs > Query` in the right corner.

## 3. Devdocs

- [Magento 2 Blog API & examples](https://documenter.getpostman.com/view/10589000/SzRxXqt3?version=latest#intro)
- [Magento 2 Blog GraphQL & examples](https://documenter.getpostman.com/view/10589000/SzS1T8pe?version=latest)

Click on Run in Postman to add these collections to your workspace quickly.

![Magento 2 blog graphql pwa](https://i.imgur.com/lhsXlUR.gif)


## 4. Contribute to this module

Feel free to **Fork** and contribute to this module and create a pull request so we will merge your changes main branch.

## 5. Get Support

- Feel free to [contact us](https://www.mageplaza.com/contact.html) if you have any further questions.
- Like this project, Give us a **Star** ![star](https://i.imgur.com/S8e0ctO.png)

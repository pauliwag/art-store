# Art Store Web App

***About***

You can apply more than one filter at a time.  For instance, if you select both artist "Lawrence Alma-Tadema" and shape "Slim" and press the Filter button, you will see the paintings (up to 20) that fall under either category.  If a painting falls under multiple categories (like painting "Spring" falls under both the artist and shape in the example I gave), it will only be displayed once.  Filtered results are ordered alphabetically.

Memcache expiration is set to 30 seconds (it is defined as a constant at the top of the `Artist`, `Gallery`, `Painting`, and `Shape` class files).  To make testing easier, I echo whether the filter options and displayed paintings (on `browse-paintings.php`) are populated from the memcache server.
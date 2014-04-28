Magento Filters
=====

[![Build Status](https://secure.travis-ci.org/charnad/magento-api-filters.png)](http://travis-ci.org/charnad/magento-api-filters)

So using Magento API v2 can sometimes be a pain. Especially when you need to write complex filters. One way is to read and understand WSDL file. Another is to use this library.

There are two types of filters: simple and complex. Simple filter does not contain condition, implying always 'eq'. Complex filter does contain condition.

Usage
-----

```php
$builder = new \MagentoFilters\Builder();
$builder->from('created_at', '2014-01-01 00:00:00');
$builder->in('status', 'complete,pending');
$filter = $builder->toArray();
```

Filter methods support fluent interface:

```php
$builder->from('created_at', '2014-01-01 00:00:00')
        ->in('status', 'complete,pending');
```

In case you want to write your own filter manually, there is a validate method:
```php
$builder = new \MagentoFilters\Builder();
// Returns bool
$builder->validate($custom_filters);
```

Constraints
-----

Unfortunately you can't have two filters on the same field, this is Magento bug/problem/feature. Can't do much about that.

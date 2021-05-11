# Larapress ECommerce Simple Azmoon Zip file processor
This package adds SimpleAzmoon Zip file processing to Larapress.

## Dependencies
* [Larapress ECommerce](../../../press-ecommerce)

## Install
1. ```composer require peynman/larapress-lcms```

## Config
1. Publish config ```php artisan vendor:publish --tag=larapress-sazmoon```
1. Create/Update AdobeConnect product type ```php artisan lp:sazmoon:create-pc```

## Usage
* SimpleAzmoon Zip file content
1. question image files (jpeg,png) with file names q1.png q2.png ...
1. answer image files with file names a1.png a2.png ...
1. correct answer file: a text file named ``answers.txt`` with the correct answer number
    1. example of content: 
        ````
        1
        3
        4
        ````

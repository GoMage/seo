/**
 * GoMage Seo Booster Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2013-2014 GoMage (http://www.gomage.com)
 * @author       GoMage
 * @license      http://www.gomage.com/license-agreement/  Single domain license
 * @terms of use http://www.gomage.com/terms-of-use/
 * @version      Release: 1.0.0
 * @since        Available since Release 1.0.0
 */


Validation.addAllThese([
    ['validate-rewrite-path', 'Please use only letters (a-z or A-Z), numbers (0-9) or hyphen(-) in this field', function (v) {
        return Validation.get('IsEmpty').test(v) || /^[A-Za-z0-9\/-]+$/.test(v)
    }]
]);





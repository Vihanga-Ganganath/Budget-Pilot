/* ==========================================================================
   Budget Pilot — grocery catalog data
   --------------------------------------------------------------------------
   Each entry is a PRODUCT, and every product carries two or three OFFERS —
   the same thing sold by different brands at different prices and pack sizes.
   That is what makes the Compare button meaningful: it puts the brands for
   one product side by side and works out which is actually cheaper per kg,
   per litre or per item.

   ADDING PICTURES
   Each brand has its own  image: ''  slot on its offer line, because every
   brand's packaging looks different. Put your pictures in an "img" folder next
   to this project and fill in the path:

       { brand: 'PureWash', pack: '1.5L', ... image: 'img/purewash-liquid.jpg', rating: 4.9, reviews: 1798 },
       { brand: 'EcoClean', pack: '3L',   ... image: 'img/ecoclean-liquid.jpg', rating: 4.5, reviews: 2633 },

   Switching brand on a card swaps the picture with it.

   Each product also has its own  image: ''  near the top. That one is a
   fallback, used for any brand you have not added a picture for yet. Leave it
   empty and those brands show a plain tinted tile instead.

   unitQty / unitLabel drive the per-unit price and decide which brand wins the
   comparison. They describe the WHOLE pack, so 4 x 100g is unitQty: 0.4, 'kg'.
   ========================================================================== */

window.CATALOG = (function () {
  'use strict';

  var CATEGORIES = [
    { key: 'cleaning', label: 'Detergents & Cleaning' },
    { key: 'personal', label: 'Personal Care' },
    { key: 'dry', label: 'Packaged Food' },
    { key: 'cooking', label: 'Cooking Essentials' },
    { key: 'beverages', label: 'Beverages' },
    { key: 'snacks', label: 'Snacks & Spreads' },
    { key: 'household', label: 'Household Consumables' }
  ];

  var ITEMS = [

    /* ---------- Detergents & cleaning ---------- */
    {
      id: 'cl01', name: 'Laundry Detergent Powder', category: 'cleaning',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Concentrated powder for front and top loaders. Low suds.',
      description: 'Concentrated powder for front and top loaders. Low suds. Sourced from Gujarat, India and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Gujarat, India',
      storage: 'Cool, dry place. Keep sealed',
      factsTitle: 'Product Facts', factsNote: 'Manufacturer declared',
      facts: [{ label: 'Form', value: 'Powder' }, { label: 'Scent', value: 'Unscented' }, { label: 'Suitable for', value: 'Machine and hand wash' }, { label: 'Packaging', value: 'Recyclable HDPE' }],
      offers: [
        { brand: 'PureWash',      pack: '2kg',       unitQty: 2,      unitLabel: 'kg',    price: 12.50, image: 'img/1.1.jpg', rating: 4.3, reviews: 282, origin: 'Izmir, Turkey', description: 'Made in Izmir, Turkey for PureWash. Concentrated powder for front and top loaders. Low suds. This listing covers the 2kg pack. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'EcoClean',      pack: '2kg',       unitQty: 2.0,    unitLabel: 'kg',    price: 16.50, image: 'img/1.2.jpg', rating: 4.5, reviews: 1599, origin: 'Guangdong, China', description: 'EcoClean builds this around value for money: concentrated powder for front and top loaders. Low suds. Produced in Guangdong, China, sold as 2kg. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'HomeGuard',     pack: '2kg',       unitQty: 2.0,    unitLabel: 'kg',    price: 11.60, image: 'img/1.3.jpg', rating: 4.0, reviews: 2063, origin: 'Karnataka, India', description: 'HomeGuard builds this around value for money: concentrated powder for front and top loaders. Low suds. Produced in Karnataka, India, sold as 2kg. Our copilot checks this price against every other brand stocking the same product.' }
      ]
    },
    {
      id: 'cl02', name: 'Liquid Laundry Detergent', category: 'cleaning',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Cold-wash formula that rinses clean without residue.',
      description: 'Cold-wash formula that rinses clean without residue. Sourced from Rayong, Thailand and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Rayong, Thailand',
      storage: 'Cool, dry place. Keep sealed',
      factsTitle: 'Product Facts', factsNote: 'Manufacturer declared',
      facts: [{ label: 'Form', value: 'Concentrated liquid' }, { label: 'Scent', value: 'Lavender' }, { label: 'Suitable for', value: 'All washable surfaces' }, { label: 'Packaging', value: 'Recyclable HDPE' }],
      offers: [
        { brand: 'PureWash',      pack: '1.5L',      unitQty: 1.5,    unitLabel: 'L',     price: 14.90, image: 'img/2.1.jpg', rating: 4.1, reviews: 313, description: 'From PureWash\u2019s everyday line. Cold-wash formula that rinses clean without residue. The 1.5L pack comes out of their Selangor, Malaysia plant. Our copilot checks this price against every other brand stocking the same product.', origin: 'Izmir, Turkey', description: 'From PureWash\u2019s everyday line. Cold-wash formula that rinses clean without residue. The 1.5L pack comes out of their Izmir, Turkey plant. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'EcoClean',      pack: '3L',        unitQty: 3.0,    unitLabel: 'L',     price: 23.00, image: 'img/2.2.jpg', rating: 4.3, reviews: 1630, origin: 'Guangdong, China', description: 'Made in Guangdong, China for EcoClean. Cold-wash formula that rinses clean without residue. This listing covers the 3L pack. Our copilot checks this price against every other brand stocking the same product.' }
      ]
    },
    {
      id: 'cl03', name: 'Fabric Softener', category: 'cleaning',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Long-lasting freshness with reduced static cling.',
      description: 'Long-lasting freshness with reduced static cling. Sourced from Selangor, Malaysia and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Selangor, Malaysia',
      storage: 'Cool, dry place. Keep sealed',
      factsTitle: 'Product Facts', factsNote: 'Manufacturer declared',
      facts: [{ label: 'Form', value: 'Concentrated liquid' }, { label: 'Scent', value: 'Citrus' }, { label: 'Suitable for', value: 'All washable surfaces' }, { label: 'Packaging', value: 'Recyclable HDPE' }],
      offers: [
        { brand: 'PureWash',      pack: '1L',        unitQty: 1,      unitLabel: 'L',     price: 8.75, image: 'img/3.1.jpg', rating: 3.9, reviews: 344, origin: 'Izmir, Turkey', description: 'PureWash keeps this a no-frills staple: long-lasting freshness with reduced static cling. The 1L pack is made and filled in Izmir, Turkey. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'EcoClean',      pack: '1L',        unitQty: 1.0,    unitLabel: 'L',     price: 9.70, image: 'img/3.2.jpg', rating: 4.1, reviews: 1661, description: 'From EcoClean\u2019s everyday line. Long-lasting freshness with reduced static cling. The 1L pack comes out of their Selangor, Malaysia plant. Our copilot checks this price against every other brand stocking the same product.', origin: 'Guangdong, China', description: 'From EcoClean\u2019s everyday line. Long-lasting freshness with reduced static cling. The 1L pack comes out of their Guangdong, China plant. Our copilot checks this price against every other brand stocking the same product.' }
      ]
    },
    {
      id: 'cl04', name: 'Dishwashing Liquid', category: 'cleaning',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Cuts grease fast and is gentle on hands.',
      description: 'Cuts grease fast and is gentle on hands. Sourced from Gujarat, India and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Gujarat, India',
      storage: 'Cool, dry place. Keep sealed',
      factsTitle: 'Product Facts', factsNote: 'Manufacturer declared',
      facts: [{ label: 'Form', value: 'Concentrated liquid' }, { label: 'Scent', value: 'Ocean fresh' }, { label: 'Suitable for', value: 'All washable surfaces' }, { label: 'Packaging', value: 'Recyclable HDPE' }],
      offers: [
        { brand: 'EcoClean',      pack: '750ml',     unitQty: 0.75,   unitLabel: 'L',     price: 4.60, image: 'img/4.1.jpg', rating: 3.9, reviews: 1692, origin: 'Guangdong, China', description: 'EcoClean keeps this a no-frills staple: cuts grease fast and is gentle on hands. The 750ml pack is made and filled in Guangdong, China. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'PureWash',      pack: '750ml',     unitQty: 0.75,   unitLabel: 'L',     price: 4.40, image: 'img/4.2.jpg', rating: 4.8, reviews: 375, origin: 'Izmir, Turkey', description: 'A premium take from PureWash. Cuts grease fast and is gentle on hands. Packed in Izmir, Turkey and sold in a 750ml size. Our copilot checks this price against every other brand stocking the same product.' },
      ]
    },
    {
      id: 'cl05', name: 'Dishwashing Bars', category: 'cleaning',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Twin-pack bars for pots, pans and heavy scrubbing.',
      description: 'Twin-pack bars for pots, pans and heavy scrubbing. Sourced from Rayong, Thailand and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Rayong, Thailand',
      storage: 'Cool, dry place. Keep sealed',
      factsTitle: 'Product Facts', factsNote: 'Manufacturer declared',
      facts: [{ label: 'Form', value: 'Moulded bar' }, { label: 'Scent', value: 'Unscented' }, { label: 'Suitable for', value: 'Machine and hand wash' }, { label: 'Packaging', value: 'Recyclable HDPE' }],
      offers: [
        { brand: 'EcoClean',      pack: '2 x 200g',  unitQty: 0.4,    unitLabel: 'kg',    price: 2.30, image: 'img/5.2.jpg', rating: 4.8, reviews: 1723, origin: 'Guangdong, China', description: 'A premium take from EcoClean. Twin-pack bars for pots, pans and heavy scrubbing. Packed in Guangdong, China and sold in a 2 x 200g size. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'PureWash',      pack: '200g',      unitQty: 0.2,    unitLabel: 'kg',    price: 1.45, image: 'img/5.1.jpg', rating: 4.6, reviews: 406, origin: 'Izmir, Turkey', description: 'PureWash builds this around value for money: twin-pack bars for pots, pans and heavy scrubbing. Produced in Izmir, Turkey, sold as 200g. Our copilot checks this price against every other brand stocking the same product.' }
      ]
    },
    {
      id: 'cl06', name: 'Floor Cleaner', category: 'cleaning',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Plant-derived formula safe on tile, wood and vinyl.',
      description: 'Plant-derived formula safe on tile, wood and vinyl. Sourced from Selangor, Malaysia and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Selangor, Malaysia',
      storage: 'Cool, dry place. Keep sealed',
      factsTitle: 'Product Facts', factsNote: 'Manufacturer declared',
      facts: [{ label: 'Form', value: 'Concentrated liquid' }, { label: 'Scent', value: 'Lavender' }, { label: 'Suitable for', value: 'All washable surfaces' }, { label: 'Packaging', value: 'Recyclable HDPE' }],
      offers: [
        { brand: 'EcoClean',      pack: '1L',        unitQty: 1,      unitLabel: 'L',     price: 6.40, image: 'img/6.1.jpg', rating: 4.6, reviews: 1754, origin: 'Guangdong, China', description: 'EcoClean builds this around value for money: plant-derived formula safe on tile, wood and vinyl. Produced in Guangdong, China, sold as 1L. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'PureWash',      pack: '1L',        unitQty: 1.0,    unitLabel: 'L',     price: 5.50, image: 'img/6.2.jpg', rating: 4.4, reviews: 437, origin: 'Izmir, Turkey', description: 'Made in Izmir, Turkey for PureWash. Plant-derived formula safe on tile, wood and vinyl. This listing covers the 1L pack. Our copilot checks this price against every other brand stocking the same product.' }
      ]
    },
    {
      id: 'cl07', name: 'Toilet Cleaner', category: 'cleaning',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Thick gel that clings to the bowl and lifts limescale.',
      description: 'Thick gel that clings to the bowl and lifts limescale. Sourced from Gujarat, India and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Gujarat, India',
      storage: 'Cool, dry place. Keep sealed',
      factsTitle: 'Product Facts', factsNote: 'Manufacturer declared',
      facts: [{ label: 'Form', value: 'Concentrated liquid' }, { label: 'Scent', value: 'Citrus' }, { label: 'Suitable for', value: 'All washable surfaces' }, { label: 'Packaging', value: 'Recyclable HDPE' }],
      offers: [
        { brand: 'EcoClean',      pack: '500ml',     unitQty: 0.5,    unitLabel: 'L',     price: 3.95, image: 'img/7.1.jpg', rating: 4.4, reviews: 1785, origin: 'Guangdong, China', description: 'Made in Guangdong, China for EcoClean. Thick gel that clings to the bowl and lifts limescale. This listing covers the 500ml pack. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'PureWash',      pack: '625ml',     unitQty: 0.625,  unitLabel: 'L',     price: 5.30, image: 'img/7.2.jpg', rating: 4.2, reviews: 468, description: 'From PureWash\u2019s everyday line. Thick gel that clings to the bowl and lifts limescale. The 625ml pack comes out of their Selangor, Malaysia plant. Our copilot checks this price against every other brand stocking the same product.', origin: 'Izmir, Turkey', description: 'From PureWash\u2019s everyday line. Thick gel that clings to the bowl and lifts limescale. The 625ml pack comes out of their Izmir, Turkey plant. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'HomeGuard',     pack: '375ml',     unitQty: 0.375,  unitLabel: 'L',     price: 2.60, image: 'img/7.3.jpg', rating: 3.9, reviews: 2249, origin: 'Karnataka, India', description: 'Made in Karnataka, India for HomeGuard. Thick gel that clings to the bowl and lifts limescale. This listing covers the 375ml pack. Our copilot checks this price against every other brand stocking the same product.' }
      ]
    },
    {
      id: 'cl08', name: 'Glass Cleaner', category: 'cleaning',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Streak-free spray for windows, mirrors and screens.',
      description: 'Streak-free spray for windows, mirrors and screens. Sourced from Rayong, Thailand and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Rayong, Thailand',
      storage: 'Cool, dry place. Keep sealed',
      factsTitle: 'Product Facts', factsNote: 'Manufacturer declared',
      facts: [{ label: 'Form', value: 'Concentrated liquid' }, { label: 'Scent', value: 'Ocean fresh' }, { label: 'Suitable for', value: 'All washable surfaces' }, { label: 'Packaging', value: 'Recyclable HDPE' }],
      offers: [
        { brand: 'EcoClean',      pack: '500ml',     unitQty: 0.5,    unitLabel: 'L',     price: 4.25, image: 'img/8.1.jpg', rating: 4.2, reviews: 1816, description: 'From EcoClean\u2019s everyday line. Streak-free spray for windows, mirrors and screens. The 500ml pack comes out of their Selangor, Malaysia plant. Our copilot checks this price against every other brand stocking the same product.', origin: 'Guangdong, China', description: 'From EcoClean\u2019s everyday line. Streak-free spray for windows, mirrors and screens. The 500ml pack comes out of their Guangdong, China plant. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'PureWash',      pack: '500ml',     unitQty: 0.5,    unitLabel: 'L',     price: 3.95, image: 'img/8.2.jpg', rating: 4.0, reviews: 499, origin: 'Izmir, Turkey', description: 'PureWash keeps this a no-frills staple: streak-free spray for windows, mirrors and screens. The 500ml pack is made and filled in Izmir, Turkey. Our copilot checks this price against every other brand stocking the same product.' }
      ]
    },
    {
      id: 'cl09', name: 'Multipurpose Cleaner', category: 'cleaning',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'One bottle for counters, sinks and appliance fronts.',
      description: 'One bottle for counters, sinks and appliance fronts. Sourced from Selangor, Malaysia and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Selangor, Malaysia',
      storage: 'Cool, dry place. Keep sealed',
      factsTitle: 'Product Facts', factsNote: 'Manufacturer declared',
      facts: [{ label: 'Form', value: 'Concentrated liquid' }, { label: 'Scent', value: 'Unscented' }, { label: 'Suitable for', value: 'Machine and hand wash' }, { label: 'Packaging', value: 'Recyclable HDPE' }],
      offers: [
        { brand: 'PureWash',      pack: '1L',        unitQty: 1,      unitLabel: 'L',     price: 7.20, image: 'img/9.1.jpg', rating: 4.9, reviews: 530, origin: 'Izmir, Turkey', description: 'A premium take from PureWash. One bottle for counters, sinks and appliance fronts. Packed in Izmir, Turkey and sold in a 1L size. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'EcoClean',      pack: '1L',        unitQty: 1.0,    unitLabel: 'L',     price: 8.95, image: 'img/9.2.jpg', rating: 4.0, reviews: 1847, origin: 'Guangdong, China', description: 'EcoClean keeps this a no-frills staple: one bottle for counters, sinks and appliance fronts. The 1L pack is made and filled in Guangdong, China. Our copilot checks this price against every other brand stocking the same product.' }
      ]
    },
    {
      id: 'cl10', name: 'Bleach', category: 'cleaning',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Household-strength disinfectant for whites and surfaces.',
      description: 'Household-strength disinfectant for whites and surfaces. Sourced from Gujarat, India and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Gujarat, India',
      storage: 'Cool, dry place. Keep sealed',
      factsTitle: 'Product Facts', factsNote: 'Manufacturer declared',
      facts: [{ label: 'Form', value: 'Concentrated liquid' }, { label: 'Scent', value: 'Citrus' }, { label: 'Suitable for', value: 'Machine and hand wash' }, { label: 'Packaging', value: 'Recyclable HDPE' }],
      offers: [
        { brand: 'PureWash',      pack: '1L',        unitQty: 1,      unitLabel: 'L',     price: 3.40, image: 'img/10.1.jpg', rating: 4.9, reviews: 1212, origin: 'Izmir, Turkey', description: 'Made in Izmir, Turkey for PureWash. Household-strength disinfectant for whites and surfaces. This listing covers the 1L pack. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'EcoClean',      pack: '1.5L',      unitQty: 1.5,    unitLabel: 'L',     price: 3.75, image: 'img/10.2.jpg', rating: 4.0, reviews: 2529, origin: 'Guangdong, China', description: 'EcoClean builds this around value for money: household-strength disinfectant for whites and surfaces. Produced in Guangdong, China, sold as 1.5L. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'HomeGuard',     pack: '1L',        unitQty: 1.0,    unitLabel: 'L',     price: 4.05, image: 'img/10.3.jpg', rating: 4.6, reviews: 2993, origin: 'Karnataka, India', description: 'HomeGuard builds this around value for money: household-strength disinfectant for whites and surfaces. Produced in Karnataka, India, sold as 1L. Our copilot checks this price against every other brand stocking the same product.' }
      ]
    },

    /* ---------- Personal care ---------- */
    {
      id: 'pc01', name: 'Soap Bars', category: 'personal',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Glycerine-rich bars with a light floral scent.',
      description: 'Glycerine-rich bars with a light floral scent. Sourced from Johor, Malaysia and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Johor, Malaysia',
      storage: 'Room temperature, out of sunlight',
      factsTitle: 'Product Facts', factsNote: 'Manufacturer declared',
      facts: [{ label: 'Skin type', value: 'All types' }, { label: 'Scent', value: 'Citrus' }, { label: 'Dermatologically tested', value: 'Yes' }, { label: 'Packaging', value: 'Recyclable' }],
      offers: [
        { brand: 'PureGlow',      pack: '3 x 100g',  unitQty: 0.3,    unitLabel: 'kg',    price: 5.20, image: 'img/11.1.jpg', rating: 4.3, reviews: 1368, origin: 'Johor, Malaysia', description: 'PureGlow keeps this a no-frills staple: glycerine-rich bars with a light floral scent. The 3 x 100g pack is made and filled in Johor, Malaysia. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'DermaCare',     pack: '3 x 100g',      unitQty: 0.3,    unitLabel: 'kg',    price: 5.60, image: 'img/11.2.jpg', rating: 4.5, reviews: 2591, origin: 'Bangkok, Thailand', description: 'Made in Bangkok, Thailand for DermaCare. Glycerine-rich bars with a light floral scent. This listing covers the 3 x 100g pack. Our copilot checks this price against every other brand stocking the same product.' }
      ]
    },
    {
      id: 'pc02', name: 'Body Wash', category: 'personal',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Creamy lather with aloe and vitamin E.',
      description: 'Creamy lather with aloe and vitamin E. Sourced from Bangkok, Thailand and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Bangkok, Thailand',
      storage: 'Room temperature, out of sunlight',
      factsTitle: 'Product Facts', factsNote: 'Manufacturer declared',
      facts: [{ label: 'Skin type', value: 'All types' }, { label: 'Scent', value: 'Ocean fresh' }, { label: 'Dermatologically tested', value: 'Yes' }, { label: 'Packaging', value: 'Recyclable' }],
      offers: [
        { brand: 'PureGlow',      pack: '500ml',     unitQty: 0.5,    unitLabel: 'L',     price: 9.80, image: 'img/12.1.jpg', rating: 4.1, reviews: 1399, origin: 'Johor, Malaysia', description: 'A premium take from PureGlow. Creamy lather with aloe and vitamin E. Packed in Johor, Malaysia and sold in a 500ml size. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'DermaCare',     pack: '375ml',     unitQty: 0.375,  unitLabel: 'L',     price: 7.00, image: 'img/12.2.jpg', rating: 4.3, reviews: 2622, description: 'From DermaCare\u2019s everyday line. Creamy lather with aloe and vitamin E. The 375ml pack comes out of their Bangkok, Thailand plant. Our copilot checks this price against every other brand stocking the same product.', origin: 'Bangkok, Thailand', description: 'From DermaCare\u2019s everyday line. Creamy lather with aloe and vitamin E. The 375ml pack comes out of their Bangkok, Thailand plant. Our copilot checks this price against every other brand stocking the same product.' }
      ]
    },
    {
      id: 'pc03', name: 'Shampoo', category: 'personal',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Sulphate-free daily shampoo for normal to dry hair.',
      description: 'Sulphate-free daily shampoo for normal to dry hair. Sourced from Tamil Nadu, India and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Tamil Nadu, India',
      storage: 'Room temperature, out of sunlight',
      factsTitle: 'Product Facts', factsNote: 'Manufacturer declared',
      facts: [{ label: 'Skin type', value: 'All types' }, { label: 'Scent', value: 'Unscented' }, { label: 'Dermatologically tested', value: 'Yes' }, { label: 'Packaging', value: 'Recyclable' }],
      offers: [
        { brand: 'DermaCare',     pack: '400ml',     unitQty: 0.4,    unitLabel: 'L',     price: 11.50, image: 'img/13.1.jpg', rating: 4.1, reviews: 2653, origin: 'Bangkok, Thailand', description: 'DermaCare keeps this a no-frills staple: sulphate-free daily shampoo for normal to dry hair. The 400ml pack is made and filled in Bangkok, Thailand. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'PureWash',      pack: '400ml',     unitQty: 0.4,    unitLabel: 'L',     price: 10.70, image: 'img/13.2.jpg', rating: 4.4, reviews: 195, description: 'From PureWash\u2019s everyday line. Sulphate-free daily shampoo for normal to dry hair. The 400ml pack comes out of their Bangkok, Thailand plant. Our copilot checks this price against every other brand stocking the same product.', origin: 'Izmir, Turkey', description: 'From PureWash\u2019s everyday line. Sulphate-free daily shampoo for normal to dry hair. The 400ml pack comes out of their Izmir, Turkey plant. Our copilot checks this price against every other brand stocking the same product.' }
      ]
    },
    {
      id: 'pc04', name: 'Conditioner', category: 'personal',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Detangles and smooths without weighing hair down.',
      description: 'Detangles and smooths without weighing hair down. Sourced from Johor, Malaysia and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Johor, Malaysia',
      storage: 'Room temperature, out of sunlight',
      factsTitle: 'Product Facts', factsNote: 'Manufacturer declared',
      facts: [{ label: 'Skin type', value: 'All types' }, { label: 'Scent', value: 'Lavender' }, { label: 'Dermatologically tested', value: 'Yes' }, { label: 'Packaging', value: 'Recyclable' }],
      offers: [
        { brand: 'DermaCare',     pack: '400ml',     unitQty: 0.4,    unitLabel: 'L',     price: 11.90, image: 'img/14.1.jpg', rating: 3.9, reviews: 2684, origin: 'Bangkok, Thailand', description: 'A premium take from DermaCare. Detangles and smooths without weighing hair down. Packed in Bangkok, Thailand and sold in a 400ml size. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'PureWash',      pack: '800ml',     unitQty: 0.8,    unitLabel: 'L',     price: 18.35, image: 'img/14.2.jpg', rating: 4.2, reviews: 226, origin: 'Izmir, Turkey', description: 'PureWash keeps this a no-frills staple: detangles and smooths without weighing hair down. The 800ml pack is made and filled in Izmir, Turkey. Our copilot checks this price against every other brand stocking the same product.' }
      ]
    },
    {
      id: 'pc05', name: 'Toothpaste', category: 'personal',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Fluoride paste for cavity protection and fresh breath.',
      description: 'Fluoride paste for cavity protection and fresh breath. Sourced from Bangkok, Thailand and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Bangkok, Thailand',
      storage: 'Room temperature, out of sunlight',
      factsTitle: 'Product Facts', factsNote: 'Manufacturer declared',
      facts: [{ label: 'Skin type', value: 'All types' }, { label: 'Scent', value: 'Citrus' }, { label: 'Dermatologically tested', value: 'Yes' }, { label: 'Packaging', value: 'Recyclable' }],
      offers: [
        { brand: 'DentaCare',     pack: '150g',      unitQty: 0.15,   unitLabel: 'kg',    price: 4.30, image: 'img/15.jpg', rating: 4.1, reviews: 3155, origin: 'Incheon, South Korea', description: 'A premium take from DentaCare. Fluoride paste for cavity protection and fresh breath. Packed in Incheon, South Korea and sold in a 150g size. Our copilot checks this price against every other brand stocking the same product.' },
      ]
    },
    {
      id: 'pc06', name: 'Toothbrushes', category: 'personal',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Soft bristles with a tongue cleaner on the reverse.',
      description: 'Soft bristles with a tongue cleaner on the reverse. Sourced from Tamil Nadu, India and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Tamil Nadu, India',
      storage: 'Room temperature, out of sunlight',
      factsTitle: 'Product Facts', factsNote: 'Manufacturer declared',
      facts: [{ label: 'Skin type', value: 'All types' }, { label: 'Scent', value: 'Ocean fresh' }, { label: 'Dermatologically tested', value: 'Yes' }, { label: 'Packaging', value: 'Recyclable' }],
      offers: [
        { brand: 'DentaCare',     pack: 'Pack of 4', unitQty: 4,      unitLabel: 'each',  price: 6.00, image: 'img/16.jpg', rating: 3.9, reviews: 3186, origin: 'Incheon, South Korea', description: 'DentaCare builds this around value for money: soft bristles with a tongue cleaner on the reverse. Produced in Incheon, South Korea, sold as Pack of 4. Our copilot checks this price against every other brand stocking the same product.' },
      ]
    },
    {
      id: 'pc07', name: 'Handwash Liquid', category: 'personal',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Moisturising handwash with a pump dispenser.',
      description: 'Moisturising handwash with a pump dispenser. Sourced from Johor, Malaysia and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Johor, Malaysia',
      storage: 'Room temperature, out of sunlight',
      factsTitle: 'Product Facts', factsNote: 'Manufacturer declared',
      facts: [{ label: 'Skin type', value: 'All types' }, { label: 'Scent', value: 'Unscented' }, { label: 'Dermatologically tested', value: 'Yes' }, { label: 'Packaging', value: 'Recyclable' }],
      offers: [
        { brand: 'PureWash',      pack: '500ml',     unitQty: 0.5,    unitLabel: 'L',     price: 4.80, image: 'img/17.1.jpg', rating: 4.7, reviews: 319, origin: 'Izmir, Turkey', description: 'Made in Izmir, Turkey for PureWash. Moisturising handwash with a pump dispenser. This listing covers the 500ml pack. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'DermaCare',     pack: '250ml',     unitQty: 0.25,   unitLabel: 'L',     price: 3.05, image: 'img/17.2.jpg', rating: 4.4, reviews: 2777, description: 'From DermaCare\u2019s everyday line. Moisturising handwash with a pump dispenser. The 250ml pack comes out of their Bangkok, Thailand plant. Our copilot checks this price against every other brand stocking the same product.', origin: 'Bangkok, Thailand', description: 'From DermaCare\u2019s everyday line. Moisturising handwash with a pump dispenser. The 250ml pack comes out of their Bangkok, Thailand plant. Our copilot checks this price against every other brand stocking the same product.' }
      ]
    },
    {
      id: 'pc08', name: 'Sanitary Pads', category: 'personal',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Ultra-thin with wings and a breathable cotton top layer.',
      description: 'Ultra-thin with wings and a breathable cotton top layer. Sourced from Bangkok, Thailand and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Bangkok, Thailand',
      storage: 'Room temperature, out of sunlight',
      factsTitle: 'Product Facts', factsNote: 'Manufacturer declared',
      facts: [{ label: 'Skin type', value: 'All types' }, { label: 'Scent', value: 'Lavender' }, { label: 'Dermatologically tested', value: 'Yes' }, { label: 'Packaging', value: 'Recyclable' }],
      offers: [
        { brand: 'Eve',      pack: 'Pack of 20', unitQty: 20,     unitLabel: 'each',  price: 7.40, image: 'img/18.jpg', rating: 4.5, reviews: 1879, origin: 'Tamil Nadu, India', description: 'Eve builds this around value for money: ultra-thin with wings and a breathable cotton top layer. Produced in Tamil Nadu, India, sold as Pack of 20. Our copilot checks this price against every other brand stocking the same product.' },
      ]
    },
    {
      id: 'pc09', name: 'Shaving Cream', category: 'personal',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Rich lather that softens stubble and soothes skin.',
      description: 'Rich lather that softens stubble and soothes skin. Sourced from Tamil Nadu, India and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Tamil Nadu, India',
      storage: 'Room temperature, out of sunlight',
      factsTitle: 'Product Facts', factsNote: 'Manufacturer declared',
      facts: [{ label: 'Skin type', value: 'All types' }, { label: 'Scent', value: 'Citrus' }, { label: 'Dermatologically tested', value: 'Yes' }, { label: 'Packaging', value: 'Recyclable' }],
      offers: [
        { brand: 'DermaCare',     pack: '200ml',     unitQty: 0.2,    unitLabel: 'L',     price: 6.90, image: 'img/19.jpg', rating: 4.0, reviews: 2839, origin: 'Bangkok, Thailand', description: 'A premium take from DermaCare. Rich lather that softens stubble and soothes skin. Packed in Bangkok, Thailand and sold in a 200ml size. Our copilot checks this price against every other brand stocking the same product.' },
      ]
    },
    {
      id: 'pc10', name: 'Razors', category: 'personal',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Triple-blade cartridges with a lubricating strip.',
      description: 'Triple-blade cartridges with a lubricating strip. Sourced from Johor, Malaysia and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Johor, Malaysia',
      storage: 'Room temperature, out of sunlight',
      factsTitle: 'Product Facts', factsNote: 'Manufacturer declared',
      facts: [{ label: 'Skin type', value: 'All types' }, { label: 'Scent', value: 'Unscented' }, { label: 'Dermatologically tested', value: 'Yes' }, { label: 'Packaging', value: 'Recyclable' }],
      offers: [
        { brand: 'DermaCare',     pack: 'Pack of 5', unitQty: 5,      unitLabel: 'each',  price: 8.00, image: 'img/20.jpg', rating: 4.0, reviews: 410, origin: 'Bangkok, Thailand', description: 'Made in Bangkok, Thailand for DermaCare. Triple-blade cartridges with a lubricating strip. This listing covers the Pack of 5 pack. Our copilot checks this price against every other brand stocking the same product.' }
      ]
    },

    /* ---------- Packaged food ---------- */
    {
      id: 'dr01', name: 'Premium Basmati Rice', category: 'dry',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Long-grain, naturally aged for two years. High nutritional value.',
      description: 'Long-grain, naturally aged for two years. High nutritional value. Sourced from Piedmont, Italy and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Piedmont, Italy',
      storage: 'Cool, dry place. Airtight once opened',
      factsTitle: 'Nutritional Value', factsNote: 'Per 100g serving',
      facts: [{ label: 'Calories', value: '229 kcal' }, { label: 'Protein', value: '5g' }, { label: 'Carbohydrate', value: '32g' }, { label: 'Fibre', value: '12g' }],
      offers: [
        { brand: 'PureGrain',     pack: '5kg',       unitQty: 5,      unitLabel: 'kg',    price: 18.50, image: 'img/21.1.jpg', rating: 4.0, reviews: 2636, description: 'From PureGrain\u2019s everyday line. Long-grain, naturally aged for two years. High nutritional value. The 5kg pack comes out of their Punjab, Pakistan plant. Our copilot checks this price against every other brand stocking the same product.', origin: 'Saskatchewan, Canada', description: 'From PureGrain\u2019s everyday line. Long-grain, naturally aged for two years. High nutritional value. The 5kg pack comes out of their Saskatchewan, Canada plant. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'GoldenHarvest', pack: '5kg',       unitQty: 5.0,    unitLabel: 'kg',    price: 22.95, image: 'img/21.2.jpg', rating: 4.2, reviews: 1869, description: 'From GoldenHarvest\u2019s everyday line. Long-grain, naturally aged for two years. High nutritional value. The 5kg pack comes out of their Anuradhapura, Sri Lanka plant. Our copilot checks this price against every other brand stocking the same product.', origin: 'Punjab, Pakistan', description: 'From GoldenHarvest\u2019s everyday line. Long-grain, naturally aged for two years. High nutritional value. The 5kg pack comes out of their Punjab, Pakistan plant. Our copilot checks this price against every other brand stocking the same product.' }
      ]
    },
    {
      id: 'dr02', name: 'Wheat Flour', category: 'dry',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Finely milled all-purpose flour for baking and roti.',
      description: 'Finely milled all-purpose flour for baking and roti. Sourced from Anuradhapura, Sri Lanka and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Anuradhapura, Sri Lanka',
      storage: 'Cool, dry place. Airtight once opened',
      factsTitle: 'Nutritional Value', factsNote: 'Per 100g serving',
      facts: [{ label: 'Calories', value: '260 kcal' }, { label: 'Protein', value: '8g' }, { label: 'Carbohydrate', value: '63g' }, { label: 'Fibre', value: '4g' }],
      offers: [
        { brand: 'GoldenHarvest', pack: '2kg',       unitQty: 2,      unitLabel: 'kg',    price: 4.20, image: 'img/22.1.jpg', rating: 4.0, reviews: 1900, origin: 'Punjab, Pakistan', description: 'GoldenHarvest keeps this a no-frills staple: finely milled all-purpose flour for baking and roti. The 2kg pack is made and filled in Punjab, Pakistan. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'PureGrain',     pack: '3kg',       unitQty: 3.0,    unitLabel: 'kg',    price: 4.65, image: 'img/22.2.jpg', rating: 4.9, reviews: 2667, origin: 'Saskatchewan, Canada', description: 'PureGrain keeps this a no-frills staple: finely milled all-purpose flour for baking and roti. The 3kg pack is made and filled in Saskatchewan, Canada. Our copilot checks this price against every other brand stocking the same product.' },
      ]
    },
    {
      id: 'dr03', name: 'White Sugar', category: 'dry',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Fine granulated sugar that dissolves quickly.',
      description: 'Fine granulated sugar that dissolves quickly. Sourced from Punjab, Pakistan and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Punjab, Pakistan',
      storage: 'Cool, dry place. Airtight once opened',
      factsTitle: 'Nutritional Value', factsNote: 'Per 100g serving',
      facts: [{ label: 'Calories', value: '291 kcal' }, { label: 'Protein', value: '11g' }, { label: 'Carbohydrate', value: '25g' }, { label: 'Fibre', value: '9g' }],
      offers: [
        { brand: 'EssenceCo', pack: '1kg',       unitQty: 1,      unitLabel: 'kg',    price: 2.60, image: 'img/23.1.jpg', rating: 4.1, reviews: 1882, origin: 'Ho Chi Minh, Vietnam', description: 'EssenceCo builds this around value for money: fine granulated sugar that dissolves quickly. Produced in Ho Chi Minh, Vietnam, sold as 1kg. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'DailyChoice',     pack: '1kg',       unitQty: 1.0,    unitLabel: 'kg',    price: 2.80, image: 'img/23.2.jpg', rating: 4.0, reviews: 687, description: 'From DailyChoice\u2019s everyday line. Fine granulated sugar that dissolves quickly. The 1kg pack comes out of their Punjab, Pakistan plant. Our copilot checks this price against every other brand stocking the same product.', origin: 'Anuradhapura, Sri Lanka', description: 'From DailyChoice\u2019s everyday line. Fine granulated sugar that dissolves quickly. The 1kg pack comes out of their Anuradhapura, Sri Lanka plant. Our copilot checks this price against every other brand stocking the same product.' }
      ]
    },
    {
      id: 'dr04', name: 'Iodised Salt', category: 'dry',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Free-flowing table salt fortified with iodine.',
      description: 'Free-flowing table salt fortified with iodine. Sourced from Saskatchewan, Canada and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Saskatchewan, Canada',
      storage: 'Cool, dry place. Airtight once opened',
      factsTitle: 'Nutritional Value', factsNote: 'Per 100g serving',
      facts: [{ label: 'Calories', value: '322 kcal' }, { label: 'Protein', value: '14g' }, { label: 'Carbohydrate', value: '56g' }, { label: 'Fibre', value: '1g' }],
      offers: [
        { brand: 'EssenceCo', pack: '1kg',       unitQty: 1,      unitLabel: 'kg',    price: 1.10, image: 'img/24.1.jpg', rating: 3.9, reviews: 1913, origin: 'Ho Chi Minh, Vietnam', description: 'Made in Ho Chi Minh, Vietnam for EssenceCo. Free-flowing table salt fortified with iodine. This listing covers the 1kg pack. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'DailyChoice',     pack: '750g',      unitQty: 0.75,   unitLabel: 'kg',    price: 0.80, image: 'img/24.2.jpg', rating: 4.9, reviews: 718, origin: 'Anuradhapura, Sri Lanka', description: 'DailyChoice keeps this a no-frills staple: free-flowing table salt fortified with iodine. The 750g pack is made and filled in Anuradhapura, Sri Lanka. Our copilot checks this price against every other brand stocking the same product.' }
      ]
    },
    {
      id: 'dr05', name: 'Red Lentils (Dhal)', category: 'dry',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Split red lentils that cook down in under 20 minutes.',
      description: 'Split red lentils that cook down in under 20 minutes. Sourced from Piedmont, Italy and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Piedmont, Italy',
      storage: 'Cool, dry place. Airtight once opened',
      factsTitle: 'Nutritional Value', factsNote: 'Per 100g serving',
      facts: [{ label: 'Calories', value: '353 kcal' }, { label: 'Protein', value: '3g' }, { label: 'Carbohydrate', value: '18g' }, { label: 'Fibre', value: '6g' }],
      offers: [
        { brand: 'EssenceCo', pack: '1kg',       unitQty: 1,      unitLabel: 'kg',    price: 3.80, image: 'img/25.1.jpg', rating: 4.8, reviews: 1944, description: 'From EssenceCo\u2019s everyday line. Split red lentils that cook down in under 20 minutes. The 1kg pack comes out of their Punjab, Pakistan plant. Our copilot checks this price against every other brand stocking the same product.', origin: 'Ho Chi Minh, Vietnam', description: 'From EssenceCo\u2019s everyday line. Split red lentils that cook down in under 20 minutes. The 1kg pack comes out of their Ho Chi Minh, Vietnam plant. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'DailyChoice',     pack: '1kg',       unitQty: 1.0,    unitLabel: 'kg',    price: 5.00, image: 'img/25.2.jpg', rating: 4.7, reviews: 749, origin: 'Anuradhapura, Sri Lanka', description: 'A premium take from DailyChoice. Split red lentils that cook down in under 20 minutes. Packed in Anuradhapura, Sri Lanka and sold in a 1kg size. Our copilot checks this price against every other brand stocking the same product.' },
            ]
    },
    {
      id: 'dr06', name: 'Pasta', category: 'dry',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Durum wheat semolina, bronze-cut to hold sauce.',
      description: 'Durum wheat semolina, bronze-cut to hold sauce. Sourced from Anuradhapura, Sri Lanka and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Anuradhapura, Sri Lanka',
      storage: 'Cool, dry place. Airtight once opened',
      factsTitle: 'Nutritional Value', factsNote: 'Per 100g serving',
      facts: [{ label: 'Calories', value: '384 kcal' }, { label: 'Protein', value: '6g' }, { label: 'Carbohydrate', value: '49g' }, { label: 'Fibre', value: '11g' }],
      offers: [
        { brand: 'Stell',     pack: '500g',      unitQty: 0.5,    unitLabel: 'kg',    price: 2.95, image: 'img/26.1.jpg', rating: 3.9, reviews: 2595, description: 'From Stell\u2019s everyday line. Durum wheat semolina, bronze-cut to hold sauce. The 500g pack comes out of their Punjab, Pakistan plant. Our copilot checks this price against every other brand stocking the same product.', origin: 'Gauteng, South Africa', description: 'From Stell\u2019s everyday line. Durum wheat semolina, bronze-cut to hold sauce. The 500g pack comes out of their Gauteng, South Africa plant. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'DailyChoice', pack: '1kg',       unitQty: 1.0,    unitLabel: 'kg',    price: 4.55, image: 'img/26.2.jpg', rating: 4.0, reviews: 3042, origin: 'Anuradhapura, Sri Lanka', description: 'DailyChoice builds this around value for money: durum wheat semolina, bronze-cut to hold sauce. Produced in Anuradhapura, Sri Lanka, sold as 1kg. Our copilot checks this price against every other brand stocking the same product.' }
      ]
    },
    {
      id: 'dr07', name: 'Instant Noodles', category: 'dry',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Ready in three minutes. Chicken and vegetable flavour.',
      description: 'Ready in three minutes. Chicken and vegetable flavour. Sourced from Punjab, Pakistan and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Punjab, Pakistan',
      storage: 'Cool, dry place. Airtight once opened',
      factsTitle: 'Nutritional Value', factsNote: 'Per 100g serving',
      facts: [{ label: 'Calories', value: '415 kcal' }, { label: 'Protein', value: '9g' }, { label: 'Carbohydrate', value: '11g' }, { label: 'Fibre', value: '3g' }],
      offers: [
        { brand: 'Stell',     pack: '5 x 75g',   unitQty: 0.375,  unitLabel: 'kg',    price: 4.50, image: 'img/27.1.jpg', rating: 4.8, reviews: 2626, origin: 'Gauteng, South Africa', description: 'Stell keeps this a no-frills staple: ready in three minutes. Chicken and vegetable flavour. The 5 x 75g pack is made and filled in Gauteng, South Africa. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'DailyChoice', pack: '375g',      unitQty: 0.375,  unitLabel: 'kg',    price: 5.00, image: 'img/27.2.jpg', rating: 4.9, reviews: 3073, description: 'From Dailychoice\u2019s everyday line. Ready in three minutes. Chicken and vegetable flavour. The 375g pack comes out of their Punjab, Pakistan plant. Our copilot checks this price against every other brand stocking the same product.', origin: 'Anuradhapura, Sri Lanka', description: 'Made in Anuradhapura, Sri Lanka for DailyChoice. Ready in three minutes. Chicken and vegetable flavour. This listing covers the 375g pack. Our copilot checks this price against every other brand stocking the same product.' }
      ]
    },
    {
      id: 'dr08', name: 'Rolled Oats', category: 'dry',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Wholegrain oats for porridge, granola and baking.',
      description: 'Wholegrain oats for porridge, granola and baking. Sourced from Saskatchewan, Canada and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Saskatchewan, Canada',
      storage: 'Cool, dry place. Airtight once opened',
      factsTitle: 'Nutritional Value', factsNote: 'Per 100g serving',
      facts: [{ label: 'Calories', value: '446 kcal' }, { label: 'Protein', value: '12g' }, { label: 'Carbohydrate', value: '42g' }, { label: 'Fibre', value: '8g' }],
      offers: [
        { brand: 'PureGrain',     pack: '1kg',       unitQty: 1,      unitLabel: 'kg',    price: 6.75, image: 'img/28.1.jpg', rating: 4.8, reviews: 2853, origin: 'Saskatchewan, Canada', description: 'A premium take from PureGrain. Wholegrain oats for porridge, granola and baking. Packed in Saskatchewan, Canada and sold in a 1kg size. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'GoldenHarvest', pack: '1kg',       unitQty: 1.0,    unitLabel: 'kg',    price: 6.50, image: 'img/28.2.jpg', rating: 3.9, reviews: 2086, origin: 'Punjab, Pakistan', description: 'A premium take from GoldenHarvest. Wholegrain oats for porridge, granola and baking. Packed in Punjab, Pakistan and sold in a 1kg size. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'OrganicLife',   pack: '1kg',       unitQty: 1.0,    unitLabel: 'kg',    price: 7.30, image: 'img/28.3.jpg', rating: 4.9, reviews: 603, description: 'From OrganicLife\u2019s everyday line. Wholegrain oats for porridge, granola and baking. The 1kg pack comes out of their Punjab, Pakistan plant. Our copilot checks this price against every other brand stocking the same product.', origin: 'Piedmont, Italy', description: 'From OrganicLife\u2019s everyday line. Wholegrain oats for porridge, granola and baking. The 1kg pack comes out of their Piedmont, Italy plant. Our copilot checks this price against every other brand stocking the same product.' }
      ]
    },
    {
      id: 'dr09', name: 'Breakfast Cereal', category: 'dry',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Wholegrain flakes with added vitamins and iron.',
      description: 'Wholegrain flakes with added vitamins and iron. Sourced from Piedmont, Italy and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Piedmont, Italy',
      storage: 'Cool, dry place. Airtight once opened',
      factsTitle: 'Nutritional Value', factsNote: 'Per 100g serving',
      facts: [{ label: 'Calories', value: '477 kcal' }, { label: 'Protein', value: '1g' }, { label: 'Carbohydrate', value: '4g' }, { label: 'Fibre', value: '0g' }],
      offers: [
        { brand: 'GoldenHarvest', pack: '375g',      unitQty: 0.375,  unitLabel: 'kg',    price: 5.30, image: 'img/29.2.jpg', rating: 4.8, reviews: 2117, origin: 'Punjab, Pakistan', description: 'GoldenHarvest builds this around value for money: wholegrain flakes with added vitamins and iron. Produced in Punjab, Pakistan, sold as 375g. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'OrganicLife',   pack: '1kg',       unitQty: 1.0,    unitLabel: 'kg',    price: 7.30, image: 'img/29.3.jpg', rating: 4.7, reviews: 634, origin: 'Piedmont, Italy', description: 'OrganicLife keeps this a no-frills staple: wholegrain flakes with added vitamins and iron. The 1kg pack is made and filled in Piedmont, Italy. Our copilot checks this price against every other brand stocking the same product.' }

      ]
    },
    {
      id: 'dr10', name: 'Dry Kidney Beans', category: 'dry',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Sorted and cleaned. Soak overnight before cooking.',
      description: 'Sorted and cleaned. Soak overnight before cooking. Sourced from Punjab, Pakistan and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Punjab, Pakistan',
      storage: 'Cool, dry place. Airtight once opened',
      factsTitle: 'Nutritional Value', factsNote: 'Per 100g serving',
      facts: [{ label: 'Calories', value: '237 kcal' }, { label: 'Protein', value: '11g' }, { label: 'Carbohydrate', value: '65g' }, { label: 'Fibre', value: '6g' }],
      offers: [
        { brand: 'EssenceCo', pack: '900g',      unitQty: 0.9,    unitLabel: 'kg',    price: 4.95, image: 'img/30.1.jpg', rating: 4.0, reviews: 2750, origin: 'Ho Chi Minh, Vietnam', description: 'EssenceCo keeps this a no-frills staple: sorted and cleaned. Soak overnight before cooking. The 900g pack is made and filled in Ho Chi Minh, Vietnam. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'DailyChoice',     pack: '900g',      unitQty: 0.9,    unitLabel: 'kg',    price: 4.25, image: 'img/30.2.jpg', rating: 3.9, reviews: 1555, origin: 'Anuradhapura, Sri Lanka', description: 'DailyChoice builds this around value for money: sorted and cleaned. Soak overnight before cooking. Produced in Anuradhapura, Sri Lanka, sold as 900g. Our copilot checks this price against every other brand stocking the same product.' }
      ]
    },

    /* ---------- Cooking essentials ---------- */
    {
      id: 'ck01', name: 'Sunflower Cooking Oil', category: 'cooking',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Light, neutral oil with a high smoke point for frying.',
      description: 'Light, neutral oil with a high smoke point for frying. Sourced from Kurunegala, Sri Lanka and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Kurunegala, Sri Lanka',
      storage: 'Cool, dark cupboard',
      factsTitle: 'Nutritional Value', factsNote: 'Per 100g serving',
      facts: [{ label: 'Calories', value: '387 kcal' }, { label: 'Total fat', value: '10g' }, { label: 'Sodium', value: '733mg' }, { label: 'Sugars', value: '0g' }],
      offers: [
        { brand: 'ChefsChoice',   pack: '2.5L',      unitQty: 2.5,    unitLabel: 'L',     price: 14.95, image: 'img/31.1.jpg', rating: 4.0, reviews: 1644, description: 'From ChefsChoice\u2019s everyday line. Light, neutral oil with a high smoke point for frying. The 2.5L pack comes out of their Kurunegala, Sri Lanka plant. Our copilot checks this price against every other brand stocking the same product.', origin: 'Kerala, India', description: 'From ChefsChoice\u2019s everyday line. Light, neutral oil with a high smoke point for frying. The 2.5L pack comes out of their Kerala, India plant. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'DailyChoice',   pack: '2.5L',      unitQty: 2.5,    unitLabel: 'L',     price: 15.50, image: 'img/31.2.jpg', rating: 4.0, reviews: 971, description: 'From DailyChoice\u2019s everyday line. Light, neutral oil with a high smoke point for frying. The 2.5L pack comes out of their Andalusia, Spain plant. Our copilot checks this price against every other brand stocking the same product.', origin: 'Anuradhapura, Sri Lanka', description: 'From DailyChoice\u2019s everyday line. Light, neutral oil with a high smoke point for frying. The 2.5L pack comes out of their Anuradhapura, Sri Lanka plant. Our copilot checks this price against every other brand stocking the same product.' }
      ]
    },
    {
      id: 'ck02', name: 'Vegetable Ghee', category: 'cooking',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Dairy-free butter substitute for curries and baking.',
      description: 'Dairy-free butter substitute for curries and baking. Sourced from Matale, Sri Lanka and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Matale, Sri Lanka',
      storage: 'Cool, dark cupboard',
      factsTitle: 'Nutritional Value', factsNote: 'Per 100g serving',
      facts: [{ label: 'Calories', value: '418 kcal' }, { label: 'Total fat', value: '6g' }, { label: 'Sodium', value: '764mg' }, { label: 'Sugars', value: '2g' }],
      offers: [
        { brand: 'DailyChoice', pack: '500g',      unitQty: 0.5,    unitLabel: 'kg',    price: 6.30, image: 'img/32.1.jpg', rating: 4.9, reviews: 1002, origin: 'Anuradhapura, Sri Lanka', description: 'DailyChoice keeps this a no-frills staple: dairy-free butter substitute for curries and baking. The 500g pack is made and filled in Anuradhapura, Sri Lanka. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'ChefsChoice',   pack: '500g',      unitQty: 0.5,    unitLabel: 'kg',    price: 5.85, image: 'img/32.2.jpg', rating: 4.9, reviews: 1675, origin: 'Kerala, India', description: 'ChefsChoice keeps this a no-frills staple: dairy-free butter substitute for curries and baking. The 500g pack is made and filled in Kerala, India. Our copilot checks this price against every other brand stocking the same product.' }
      ]
    },
    {
      id: 'ck03', name: 'White Vinegar', category: 'cooking',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Five percent acidity for pickling, marinades and cleaning.',
      description: 'Five percent acidity for pickling, marinades and cleaning. Sourced from Shandong, China and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Shandong, China',
      storage: 'Cool, dark cupboard',
      factsTitle: 'Nutritional Value', factsNote: 'Per 100g serving',
      facts: [{ label: 'Calories', value: '449 kcal' }, { label: 'Total fat', value: '2g' }, { label: 'Sodium', value: '795mg' }, { label: 'Sugars', value: '4g' }],
      offers: [
        { brand: 'ChefsChoice',   pack: '750ml',     unitQty: 0.75,   unitLabel: 'L',     price: 2.15, image: 'img/33.1.jpg', rating: 4.7, reviews: 1706, origin: 'Kerala, India', description: 'A premium take from ChefsChoice. Five percent acidity for pickling, marinades and cleaning. Packed in Kerala, India and sold in a 750ml size. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'DailyChoice', pack: '750ml',     unitQty: 0.75,   unitLabel: 'L',     price: 2.65, image: 'img/33.2.jpg', rating: 4.7, reviews: 1033, origin: 'Anuradhapura, Sri Lanka', description: 'A premium take from DailyChoice. Five percent acidity for pickling, marinades and cleaning. Packed in Anuradhapura, Sri Lanka and sold in a 750ml size. Our copilot checks this price against every other brand stocking the same product.' }
      ]
    },
    {
      id: 'ck04', name: 'Soy Sauce', category: 'cooking',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Naturally brewed, dark and full-bodied.',
      description: 'Naturally brewed, dark and full-bodied. Sourced from Andalusia, Spain and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Andalusia, Spain',
      storage: 'Cool, dark cupboard',
      factsTitle: 'Nutritional Value', factsNote: 'Per 100g serving',
      facts: [{ label: 'Calories', value: '480 kcal' }, { label: 'Total fat', value: '33g' }, { label: 'Sodium', value: '826mg' }, { label: 'Sugars', value: '6g' }],
      offers: [
        { brand: 'ChefsChoice',   pack: '500ml',     unitQty: 0.5,    unitLabel: 'L',     price: 3.70, image: 'img/34.1.jpg', rating: 4.5, reviews: 1737, origin: 'Kerala, India', description: 'ChefsChoice builds this around value for money: naturally brewed, dark and full-bodied. Produced in Kerala, India, sold as 500ml. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'Tango', pack: '750ml',     unitQty: 0.75,   unitLabel: 'L',     price: 4.10, image: 'img/34.2.jpg', rating: 4.4, reviews: 2465, origin: 'Minas Gerais, Brazil', description: 'Made in Minas Gerais, Brazil for Tango. Naturally brewed, dark and full-bodied. This listing covers the 750ml pack. Our copilot checks this price against every other brand stocking the same product.' },
      ]
    },
    {
      id: 'ck05', name: 'Chili Sauce', category: 'cooking',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Sweet-hot table sauce made with ripe red chillies.',
      description: 'Sweet-hot table sauce made with ripe red chillies. Sourced from Kurunegala, Sri Lanka and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Kurunegala, Sri Lanka',
      storage: 'Cool, dark cupboard',
      factsTitle: 'Nutritional Value', factsNote: 'Per 100g serving',
      facts: [{ label: 'Calories', value: '511 kcal' }, { label: 'Total fat', value: '29g' }, { label: 'Sodium', value: '857mg' }, { label: 'Sugars', value: '8g' }],
      offers: [
        { brand: 'ChefsChoice',   pack: '400ml',     unitQty: 0.4,    unitLabel: 'L',     price: 3.25, image: 'img/35.1.jpg', rating: 4.3, reviews: 1768, origin: 'Kerala, India', description: 'Made in Kerala, India for ChefsChoice. Sweet-hot table sauce made with ripe red chillies. This listing covers the 400ml pack. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'Tango', pack: '400ml',     unitQty: 0.4,    unitLabel: 'L',     price: 3.50, image: 'img/35.2.jpg', rating: 4.2, reviews: 2496, description: 'From Tango\u2019s everyday line. Sweet-hot table sauce made with ripe red chillies. The 400ml pack comes out of their Kurunegala, Sri Lanka plant. Our copilot checks this price against every other brand stocking the same product.', origin: 'Minas Gerais, Brazil', description: 'From Tango\u2019s everyday line. Sweet-hot table sauce made with ripe red chillies. The 400ml pack comes out of their Minas Gerais, Brazil plant. Our copilot checks this price against every other brand stocking the same product.' }
      ]
    },
    {
      id: 'ck06', name: 'Tomato Ketchup', category: 'cooking',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Thick ketchup from vine-ripened tomatoes.',
      description: 'Thick ketchup from vine-ripened tomatoes. Sourced from Matale, Sri Lanka and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Matale, Sri Lanka',
      storage: 'Cool, dark cupboard',
      factsTitle: 'Nutritional Value', factsNote: 'Per 100g serving',
      facts: [{ label: 'Calories', value: '81 kcal' }, { label: 'Total fat', value: '25g' }, { label: 'Sodium', value: '12mg' }, { label: 'Sugars', value: '10g' }],
      offers: [
        { brand: 'ChefsChoice',   pack: '1kg',       unitQty: 1,      unitLabel: 'kg',    price: 5.60, image: 'img/36.1.jpg', rating: 4.1, reviews: 1799, description: 'From ChefsChoice\u2019s everyday line. Thick ketchup from vine-ripened tomatoes. The 1kg pack comes out of their Kurunegala, Sri Lanka plant. Our copilot checks this price against every other brand stocking the same product.', origin: 'Kerala, India', description: 'From ChefsChoice\u2019s everyday line. Thick ketchup from vine-ripened tomatoes. The 1kg pack comes out of their Kerala, India plant. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'Tango', pack: '750g',      unitQty: 0.75,   unitLabel: 'kg',    price: 4.00, image: 'img/36.2.jpg', rating: 4.0, reviews: 2527, origin: 'Minas Gerais, Brazil', description: 'Tango keeps this a no-frills staple: thick ketchup from vine-ripened tomatoes. The 750g pack is made and filled in Minas Gerais, Brazil. Our copilot checks this price against every other brand stocking the same product.' }
      ]
    },
    {
      id: 'ck07', name: 'Curry Spice Set', category: 'cooking',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Chilli powder, turmeric and roasted curry powder.',
      description: 'Chilli powder, turmeric and roasted curry powder. Sourced from Shandong, China and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Shandong, China',
      storage: 'Cool, dark cupboard',
      factsTitle: 'Nutritional Value', factsNote: 'Per 100g serving',
      facts: [{ label: 'Calories', value: '112 kcal' }, { label: 'Total fat', value: '21g' }, { label: 'Sodium', value: '43mg' }, { label: 'Sugars', value: '12g' }],
      offers: [
        { brand: 'EssenceCo', pack: '3 x 200g',  unitQty: 0.6,    unitLabel: 'kg',    price: 7.90, image: '', rating: 4.0, reviews: 2352, origin: 'Ho Chi Minh, Vietnam', description: 'Made in Ho Chi Minh, Vietnam for EssenceCo. Chilli powder, turmeric and roasted curry powder. This listing covers the 3 x 200g pack. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'ChefsChoice',   pack: '600g',      unitQty: 0.6,    unitLabel: 'kg',    price: 10.45, image: '', rating: 3.9, reviews: 1830, origin: 'Kerala, India', description: 'ChefsChoice keeps this a no-frills staple: chilli powder, turmeric and roasted curry powder. The 600g pack is made and filled in Kerala, India. Our copilot checks this price against every other brand stocking the same product.' },
      ]
    },
    {
      id: 'ck08', name: 'Chicken Stock Cubes', category: 'cooking',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'One cube makes 500ml of seasoned stock.',
      description: 'One cube makes 500ml of seasoned stock. Sourced from Andalusia, Spain and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Andalusia, Spain',
      storage: 'Cool, dark cupboard',
      factsTitle: 'Nutritional Value', factsNote: 'Per 100g serving',
      facts: [{ label: 'Calories', value: '143 kcal' }, { label: 'Total fat', value: '17g' }, { label: 'Sodium', value: '74mg' }, { label: 'Sugars', value: '14g' }],
      offers: [
        { brand: 'ChefsChoice',   pack: '24 cubes',  unitQty: 24,     unitLabel: 'each',  price: 3.45, image: '', rating: 4.8, reviews: 1861, origin: 'Kerala, India', description: 'A premium take from ChefsChoice. One cube makes 500ml of seasoned stock. Packed in Kerala, India and sold in a 24 cubes size. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'DailyChoice', pack: '48 cubes',  unitQty: 48,     unitLabel: 'each',  price: 5.35, image: '', rating: 4.8, reviews: 1188, origin: 'Anuradhapura, Sri Lanka', description: 'A premium take from DailyChoice. One cube makes 500ml of seasoned stock. Packed in Anuradhapura, Sri Lanka and sold in a 48 cubes size. Our copilot checks this price against every other brand stocking the same product.' }
      ]
    },
    {
      id: 'ck09', name: 'Baking Powder', category: 'cooking',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Double-acting raising agent in a resealable tin.',
      description: 'Double-acting raising agent in a resealable tin. Sourced from Kurunegala, Sri Lanka and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Kurunegala, Sri Lanka',
      storage: 'Cool, dark cupboard',
      factsTitle: 'Nutritional Value', factsNote: 'Per 100g serving',
      facts: [{ label: 'Calories', value: '174 kcal' }, { label: 'Total fat', value: '13g' }, { label: 'Sodium', value: '105mg' }, { label: 'Sugars', value: '16g' }],
      offers: [
        { brand: 'ChefsChoice',   pack: '200g',      unitQty: 0.2,    unitLabel: 'kg',    price: 2.05, image: '', rating: 4.6, reviews: 1892, origin: 'Kerala, India', description: 'ChefsChoice builds this around value for money: double-acting raising agent in a resealable tin. Produced in Kerala, India, sold as 200g. Our copilot checks this price against every other brand stocking the same product.' },
      ]
    },
    {
      id: 'ck10', name: 'Instant Dry Yeast', category: 'cooking',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'No proofing needed. Mix straight into the flour.',
      description: 'No proofing needed. Mix straight into the flour. Sourced from Shandong, China and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Shandong, China',
      storage: 'Cool, dark cupboard',
      factsTitle: 'Nutritional Value', factsNote: 'Per 100g serving',
      facts: [{ label: 'Calories', value: '395 kcal' }, { label: 'Total fat', value: '30g' }, { label: 'Sodium', value: '787mg' }, { label: 'Sugars', value: '2g' }],
      offers: [
        { brand: 'DailyChoice', pack: '100g',      unitQty: 0.1,    unitLabel: 'kg',    price: 2.80, image: '', rating: 4.6, reviews: 1901, description: 'From DailyChoice\u2019s everyday line. No proofing needed. Mix straight into the flour. The 100g pack comes out of their Kurunegala, Sri Lanka plant. Our copilot checks this price against every other brand stocking the same product.', origin: 'Anuradhapura, Sri Lanka', description: 'From DailyChoice\u2019s everyday line. No proofing needed. Mix straight into the flour. The 100g pack comes out of their Anuradhapura, Sri Lanka plant. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'ChefsChoice',   pack: '100g',      unitQty: 0.1,    unitLabel: 'kg',    price: 2.70, image: '', rating: 4.6, reviews: 2574, description: 'From ChefsChoice\u2019s everyday line. No proofing needed. Mix straight into the flour. The 100g pack comes out of their Andalusia, Spain plant. Our copilot checks this price against every other brand stocking the same product.', origin: 'Kerala, India', description: 'From ChefsChoice\u2019s everyday line. No proofing needed. Mix straight into the flour. The 100g pack comes out of their Kerala, India plant. Our copilot checks this price against every other brand stocking the same product.' },
      ]
    },

    /* ---------- Beverages ---------- */
    {
      id: 'bv01', name: 'Ceylon Tea Bags', category: 'beverages',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Single-origin high-grown leaf with a bright finish.',
      description: 'Single-origin high-grown leaf with a bright finish. Sourced from Antioquia, Colombia and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Antioquia, Colombia',
      storage: 'Cool, dry place. Chill after opening',
      factsTitle: 'Nutritional Value', factsNote: 'Per 100g serving',
      facts: [{ label: 'Calories', value: '179 kcal' }, { label: 'Protein', value: '7g' }, { label: 'Sugars', value: '14g' }, { label: 'Calcium', value: '31mg' }],
      offers: [
        { brand: 'BrewMasters',  pack: '100 bags',  unitQty: 100,    unitLabel: 'each',  price: 6.90, image: '', rating: 4.1, reviews: 409, origin: 'Antioquia, Colombia', description: 'A premium take from BrewMasters. Single-origin high-grown leaf with a bright finish. Packed in Antioquia, Colombia and sold in a 100 bags size. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'FreshSip',   pack: '50 bags',   unitQty: 50,     unitLabel: 'each',  price: 4.35, image: '', rating: 4.4, reviews: 2829, origin: 'Nuwara Eliya, Sri Lanka', description: 'Made in Nuwara Eliya, Sri Lanka for FreshSip. Single-origin high-grown leaf with a bright finish. This listing covers the 50 bags pack. Our copilot checks this price against every other brand stocking the same product.' }
      ]
    },
    {
      id: 'bv02', name: 'Ground Coffee', category: 'beverages',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Medium roast arabica with notes of chocolate and oak.',
      description: 'Medium roast arabica with notes of chocolate and oak. Sourced from Waikato, New Zealand and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Waikato, New Zealand',
      storage: 'Cool, dry place. Chill after opening',
      factsTitle: 'Nutritional Value', factsNote: 'Per 100g serving',
      facts: [{ label: 'Calories', value: '210 kcal' }, { label: 'Protein', value: '10g' }, { label: 'Sugars', value: '16g' }, { label: 'Calcium', value: '62mg' }],
      offers: [
        { brand: 'BrewMasters',  pack: '500g',      unitQty: 0.5,    unitLabel: 'kg',    price: 15.40, image: '', rating: 3.9, reviews: 440, origin: 'Antioquia, Colombia', description: 'BrewMasters builds this around value for money: medium roast arabica with notes of chocolate and oak. Produced in Antioquia, Colombia, sold as 500g. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'FreshSip',   pack: '500g',      unitQty: 0.5,    unitLabel: 'kg',    price: 13.25, image: '', rating: 4.2, reviews: 2860, description: 'From FreshSip\u2019s everyday line. Medium roast arabica with notes of chocolate and oak. The 500g pack comes out of their Nuwara Eliya, Sri Lanka plant. Our copilot checks this price against every other brand stocking the same product.', origin: 'Nuwara Eliya, Sri Lanka', description: 'From FreshSip\u2019s everyday line. Medium roast arabica with notes of chocolate and oak. The 500g pack comes out of their Nuwara Eliya, Sri Lanka plant. Our copilot checks this price against every other brand stocking the same product.' }
      ]
    },
    {
      id: 'bv03', name: 'Full Cream Milk Powder', category: 'beverages',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Grass-fed organic milk, no additives. Resealable tin.',
      description: 'Grass-fed organic milk, no additives. Resealable tin. Sourced from Nuwara Eliya, Sri Lanka and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Nuwara Eliya, Sri Lanka',
      storage: 'Cool, dry place. Chill after opening',
      factsTitle: 'Nutritional Value', factsNote: 'Per 100g serving',
      facts: [{ label: 'Calories', value: '241 kcal' }, { label: 'Protein', value: '13g' }, { label: 'Sugars', value: '18g' }, { label: 'Calcium', value: '93mg' }],
      offers: [
        { brand: 'DairyPure',   pack: '1kg',       unitQty: 1,      unitLabel: 'kg',    price: 24.99, image: '', rating: 4.8, reviews: 2833, origin: 'Waikato, New Zealand', description: 'Made in Waikato, New Zealand for DairyPure. Grass-fed organic milk, no additives. Resealable tin. This listing covers the 1kg pack. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'BrewMasters',  pack: '1.25kg',    unitQty: 1.25,   unitLabel: 'kg',    price: 33.40, image: '', rating: 4.8, reviews: 471, origin: 'Antioquia, Colombia', description: 'Made in Antioquia, Colombia for BrewMasters. Grass-fed organic milk, no additives. Resealable tin. This listing covers the 1.25kg pack. Our copilot checks this price against every other brand stocking the same product.' },
      ]
    },
    {
      id: 'bv04', name: 'Malt Energy Drink', category: 'beverages',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Malted barley drink fortified with vitamins and calcium.',
      description: 'Malted barley drink fortified with vitamins and calcium. Sourced from Antioquia, Colombia and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Antioquia, Colombia',
      storage: 'Cool, dry place. Chill after opening',
      factsTitle: 'Nutritional Value', factsNote: 'Per 100g serving',
      facts: [{ label: 'Calories', value: '272 kcal' }, { label: 'Protein', value: '2g' }, { label: 'Sugars', value: '20g' }, { label: 'Calcium', value: '124mg' }],
      offers: [
        { brand: 'DairyPure',   pack: '800g',      unitQty: 0.8,    unitLabel: 'kg',    price: 13.60, image: '', rating: 4.6, reviews: 2864, description: 'From DairyPure\u2019s everyday line. Malted barley drink fortified with vitamins and calcium. The 800g pack comes out of their Nuwara Eliya, Sri Lanka plant. Our copilot checks this price against every other brand stocking the same product.', origin: 'Waikato, New Zealand', description: 'From DairyPure\u2019s everyday line. Malted barley drink fortified with vitamins and calcium. The 800g pack comes out of their Waikato, New Zealand plant. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'BrewMasters',  pack: '800g',      unitQty: 0.8,    unitLabel: 'kg',    price: 12.65, image: '', rating: 4.6, reviews: 502, description: 'From BrewMasters\u2019s everyday line. Malted barley drink fortified with vitamins and calcium. The 800g pack comes out of their Antioquia, Colombia plant. Our copilot checks this price against every other brand stocking the same product.', origin: 'Antioquia, Colombia', description: 'From BrewMasters\u2019s everyday line. Malted barley drink fortified with vitamins and calcium. The 800g pack comes out of their Antioquia, Colombia plant. Our copilot checks this price against every other brand stocking the same product.' }
      ]
    },
    {
      id: 'bv05', name: 'Cola Soft Drink', category: 'beverages',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Six-pack of chilled cans for the fridge shelf.',
      description: 'Six-pack of chilled cans for the fridge shelf. Sourced from Waikato, New Zealand and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Waikato, New Zealand',
      storage: 'Cool, dry place. Chill after opening',
      factsTitle: 'Nutritional Value', factsNote: 'Per 100g serving',
      facts: [{ label: 'Calories', value: '303 kcal' }, { label: 'Protein', value: '5g' }, { label: 'Sugars', value: '22g' }, { label: 'Calcium', value: '155mg' }],
      offers: [
        { brand: 'BrewMasters',  pack: '1.98L',     unitQty: 1.98,   unitLabel: 'L',     price: 7.25, image: '', rating: 4.4, reviews: 533, origin: 'Antioquia, Colombia', description: 'BrewMasters keeps this a no-frills staple: six-pack of chilled cans for the fridge shelf. The 1.98L pack is made and filled in Antioquia, Colombia. Our copilot checks this price against every other brand stocking the same product.' }
      ]
    },
    {
      id: 'bv06', name: 'Orange Juice Cartons', category: 'beverages',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Not-from-concentrate juice in long-life cartons.',
      description: 'Not-from-concentrate juice in long-life cartons. Sourced from Nuwara Eliya, Sri Lanka and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Nuwara Eliya, Sri Lanka',
      storage: 'Cool, dry place. Chill after opening',
      factsTitle: 'Nutritional Value', factsNote: 'Per 100g serving',
      facts: [{ label: 'Calories', value: '334 kcal' }, { label: 'Protein', value: '8g' }, { label: 'Sugars', value: '24g' }, { label: 'Calcium', value: '186mg' }],
      offers: [
        { brand: 'BrewMasters',   pack: '4 x 1L',    unitQty: 4,      unitLabel: 'L',     price: 9.20, image: '', rating: 4.2, reviews: 564, origin: 'Antioquia, Colombia', description: 'A premium take from BrewMasters. Not-from-concentrate juice in long-life cartons. Packed in Antioquia, Colombia and sold in a 4 x 1L size. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'FreshSip',  pack: '6L',        unitQty: 6.0,    unitLabel: 'L',     price: 10.15, image: '', rating: 4.5, reviews: 2984, origin: 'Nuwara Eliya, Sri Lanka', description: 'Made in Nuwara Eliya, Sri Lanka for FreshSip. Not-from-concentrate juice in long-life cartons. This listing covers the 6L pack. Our copilot checks this price against every other brand stocking the same product.' },      ]
    },
    {
      id: 'bv07', name: 'Energy Drink', category: 'beverages',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Caffeine and B-vitamin blend in slim cans.',
      description: 'Caffeine and B-vitamin blend in slim cans. Sourced from Antioquia, Colombia and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Antioquia, Colombia',
      storage: 'Cool, dry place. Chill after opening',
      factsTitle: 'Nutritional Value', factsNote: 'Per 100g serving',
      facts: [{ label: 'Calories', value: '365 kcal' }, { label: 'Protein', value: '11g' }, { label: 'Sugars', value: '26g' }, { label: 'Calcium', value: '217mg' }],
      offers: [
        { brand: 'BrewMasters',  pack: '4 x 250ml', unitQty: 1,      unitLabel: 'L',     price: 7.50, image: '', rating: 4.0, reviews: 595, origin: 'Antioquia, Colombia', description: 'BrewMasters builds this around value for money: caffeine and B-vitamin blend in slim cans. Produced in Antioquia, Colombia, sold as 4 x 250ml. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'FreshSip',   pack: '1L',        unitQty: 1.0,    unitLabel: 'L',     price: 8.10, image: '', rating: 4.3, reviews: 3015, description: 'From FreshSip\u2019s everyday line. Caffeine and B-vitamin blend in slim cans. The 1L pack comes out of their Nuwara Eliya, Sri Lanka plant. Our copilot checks this price against every other brand stocking the same product.', origin: 'Nuwara Eliya, Sri Lanka', description: 'From FreshSip\u2019s everyday line. Caffeine and B-vitamin blend in slim cans. The 1L pack comes out of their Nuwara Eliya, Sri Lanka plant. Our copilot checks this price against every other brand stocking the same product.' }
      ]
    },
    {
      id: 'bv08', name: 'Drinking Chocolate', category: 'beverages',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Rich cocoa powder that dissolves in hot or cold milk.',
      description: 'Rich cocoa powder that dissolves in hot or cold milk. Sourced from Waikato, New Zealand and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Waikato, New Zealand',
      storage: 'Cool, dry place. Chill after opening',
      factsTitle: 'Nutritional Value', factsNote: 'Per 100g serving',
      facts: [{ label: 'Calories', value: '396 kcal' }, { label: 'Protein', value: '14g' }, { label: 'Sugars', value: '28g' }, { label: 'Calcium', value: '248mg' }],
      offers: [
        { brand: 'BrewMasters',  pack: '500g',      unitQty: 0.5,    unitLabel: 'kg',    price: 8.30, image: '', rating: 4.9, reviews: 626, origin: 'Antioquia, Colombia', description: 'Made in Antioquia, Colombia for BrewMasters. Rich cocoa powder that dissolves in hot or cold milk. This listing covers the 500g pack. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'FreshSip',   pack: '375g',      unitQty: 0.375,  unitLabel: 'kg',    price: 5.95, image: '', rating: 4.1, reviews: 3046, origin: 'Nuwara Eliya, Sri Lanka', description: 'FreshSip keeps this a no-frills staple: rich cocoa powder that dissolves in hot or cold milk. The 375g pack is made and filled in Nuwara Eliya, Sri Lanka. Our copilot checks this price against every other brand stocking the same product.' }
      ]
    },

    /* ---------- Snacks & spreads ---------- */
    {
      id: 'sn01', name: 'Digestive Biscuits', category: 'snacks',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Wholemeal biscuits that hold up to a cup of tea.',
      description: 'Wholemeal biscuits that hold up to a cup of tea. Sourced from Bavaria, Germany and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Bavaria, Germany',
      storage: 'Cool, dry place. Reseal after opening',
      factsTitle: 'Nutritional Value', factsNote: 'Per 100g serving',
      facts: [{ label: 'Calories', value: '229 kcal' }, { label: 'Total fat', value: '29g' }, { label: 'Sugars', value: '19g' }, { label: 'Fibre', value: '8g' }],
      offers: [
        { brand: 'Crunchies',     pack: '400g',      unitQty: 0.4,    unitLabel: 'kg',    price: 3.60, image: '', rating: 4.4, reviews: 1444, origin: 'Queensland, Australia', description: 'Crunchies keeps this a no-frills staple: wholemeal biscuits that hold up to a cup of tea. The 400g pack is made and filled in Queensland, Australia. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'Cracko',   pack: '400g',      unitQty: 0.4,    unitLabel: 'kg',    price: 4.75, image: '', rating: 4.5, reviews: 1214, description: 'From Cracko\u2019s everyday line. Wholemeal biscuits that hold up to a cup of tea. The 400g pack comes out of their Kandy, Sri Lanka plant. Our copilot checks this price against every other brand stocking the same product.', origin: 'Lombardy, Italy', description: 'From Cracko\u2019s everyday line. Wholemeal biscuits that hold up to a cup of tea. The 400g pack comes out of their Lombardy, Italy plant. Our copilot checks this price against every other brand stocking the same product.' },
            ]
    },
    {
      id: 'sn02', name: 'Cream Crackers', category: 'snacks',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Light, crisp crackers in three sealed sleeves.',
      description: 'Light, crisp crackers in three sealed sleeves. Sourced from Punjab, India and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Punjab, India',
      storage: 'Cool, dry place. Reseal after opening',
      factsTitle: 'Nutritional Value', factsNote: 'Per 100g serving',
      facts: [{ label: 'Calories', value: '260 kcal' }, { label: 'Total fat', value: '25g' }, { label: 'Sugars', value: '21g' }, { label: 'Fibre', value: '0g' }],
      offers: [
        { brand: 'Crunchies',     pack: '500g',      unitQty: 0.5,    unitLabel: 'kg',    price: 3.10, image: '', rating: 4.2, reviews: 1475, origin: 'Queensland, Australia', description: 'A premium take from Crunchies. Light, crisp crackers in three sealed sleeves. Packed in Queensland, Australia and sold in a 500g size. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'Cracko',   pack: '1kg',       unitQty: 1.0,    unitLabel: 'kg',    price: 4.80, image: '', rating: 4.3, reviews: 1245, origin: 'Lombardy, Italy', description: 'Cracko keeps this a no-frills staple: light, crisp crackers in three sealed sleeves. The 1kg pack is made and filled in Lombardy, Italy. Our copilot checks this price against every other brand stocking the same product.' }
      ]
    },
    {
      id: 'sn03', name: 'Potato Chips', category: 'snacks',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Lightly salted sharing pack of six single-serve bags.',
      description: 'Lightly salted sharing pack of six single-serve bags. Sourced from Kandy, Sri Lanka and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Kandy, Sri Lanka',
      storage: 'Cool, dry place. Reseal after opening',
      factsTitle: 'Nutritional Value', factsNote: 'Per 100g serving',
      facts: [{ label: 'Calories', value: '291 kcal' }, { label: 'Total fat', value: '21g' }, { label: 'Sugars', value: '23g' }, { label: 'Fibre', value: '5g' }],
      offers: [
        { brand: 'ChipZo',   pack: '6 x 45g',   unitQty: 0.27,   unitLabel: 'kg',    price: 6.40, image: '', rating: 4.1, reviews: 2064, origin: 'Selangor, Malaysia', description: 'A premium take from ChipZo. Lightly salted sharing pack of six single-serve bags. Packed in Selangor, Malaysia and sold in a 6 x 45g size. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'BiteJoy',     pack: '270g',      unitQty: 0.27,   unitLabel: 'kg',    price: 7.10, image: '', rating: 4.7, reviews: 1928, description: 'From BiteJoy\u2019s everyday line. Lightly salted sharing pack of six single-serve bags. The 270g pack comes out of their Kandy, Sri Lanka plant. Our copilot checks this price against every other brand stocking the same product.', origin: 'Bavaria, Germany', description: 'From BiteJoy\u2019s everyday line. Lightly salted sharing pack of six single-serve bags. The 270g pack comes out of their Bavaria, Germany plant. Our copilot checks this price against every other brand stocking the same product.' }
      ]
    },
    {
      id: 'sn04', name: 'Instant Soup Packets', category: 'snacks',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Just add hot water. Sweetcorn and chicken varieties.',
      description: 'Just add hot water. Sweetcorn and chicken varieties. Sourced from Bavaria, Germany and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Bavaria, Germany',
      storage: 'Cool, dry place. Reseal after opening',
      factsTitle: 'Nutritional Value', factsNote: 'Per 100g serving',
      facts: [{ label: 'Calories', value: '322 kcal' }, { label: 'Total fat', value: '17g' }, { label: 'Sugars', value: '25g' }, { label: 'Fibre', value: '10g' }],
      offers: [
        { brand: 'ChefsChoice',   pack: 'Pack of 8', unitQty: 8,      unitLabel: 'each',  price: 5.20, image: '', rating: 4.6, reviews: 3088, origin: 'Kerala, India', description: 'A premium take from ChefsChoice. Just add hot water. Sweetcorn and chicken varieties. Packed in Kerala, India and sold in a Pack of 8 size. Our copilot checks this price against every other brand stocking the same product.' },
      ]
    },
    {
      id: 'sn05', name: 'Peanut Butter', category: 'snacks',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Roasted peanuts and a pinch of salt. Nothing else.',
      description: 'Roasted peanuts and a pinch of salt. Nothing else. Sourced from Punjab, India and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Punjab, India',
      storage: 'Cool, dry place. Reseal after opening',
      factsTitle: 'Nutritional Value', factsNote: 'Per 100g serving',
      facts: [{ label: 'Calories', value: '353 kcal' }, { label: 'Total fat', value: '13g' }, { label: 'Sugars', value: '27g' }, { label: 'Fibre', value: '2g' }],
      offers: [
        { brand: 'EssenceCo',   pack: '500g',      unitQty: 0.5,    unitLabel: 'kg',    price: 7.85, image: '', rating: 4.5, reviews: 530, origin: 'Ho Chi Minh, Vietnam', description: 'EssenceCo keeps this a no-frills staple: roasted peanuts and a pinch of salt. Nothing else. The 500g pack is made and filled in Ho Chi Minh, Vietnam. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'ChefsChoice',   pack: '250g',      unitQty: 0.25,   unitLabel: 'kg',    price: 4.95, image: '', rating: 4.4, reviews: 3119, origin: 'Kerala, India', description: 'ChefsChoice builds this around value for money: roasted peanuts and a pinch of salt. Nothing else. Produced in Kerala, India, sold as 250g. Our copilot checks this price against every other brand stocking the same product.' }
      ]
    },
    {
      id: 'sn06', name: 'Mixed Fruit Jam', category: 'snacks',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Made with 45g of fruit per 100g. No artificial colour.',
      description: 'Made with 45g of fruit per 100g. No artificial colour. Sourced from Kandy, Sri Lanka and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Kandy, Sri Lanka',
      storage: 'Cool, dry place. Reseal after opening',
      factsTitle: 'Nutritional Value', factsNote: 'Per 100g serving',
      facts: [{ label: 'Calories', value: '384 kcal' }, { label: 'Total fat', value: '9g' }, { label: 'Sugars', value: '0g' }, { label: 'Fibre', value: '7g' }],
      offers: [
        { brand: 'ChefsChoice',   pack: '500g',      unitQty: 0.5,    unitLabel: 'kg',    price: 4.70, image: '', rating: 4.2, reviews: 3150, origin: 'Kerala, India', description: 'Made in Kerala, India for ChefsChoice. Made with 45g of fruit per 100g. No artificial colour. This listing covers the 500g pack. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'DailyChoice',     pack: '500g',      unitQty: 0.5,    unitLabel: 'kg',    price: 4.05, image: '', rating: 4.2, reviews: 2477, origin: 'Anuradhapura, Sri Lanka', description: 'Made in Anuradhapura, Sri Lanka for DailyChoice. Made with 45g of fruit per 100g. No artificial colour. This listing covers the 500g pack. Our copilot checks this price against every other brand stocking the same product.' }
      ]
    },
    {
      id: 'sn07', name: 'Wildflower Honey', category: 'snacks',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Unfiltered raw honey sourced from high-altitude meadows.',
      description: 'Unfiltered raw honey sourced from high-altitude meadows. Sourced from Bavaria, Germany and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Bavaria, Germany',
      storage: 'Cool, dry place. Reseal after opening',
      factsTitle: 'Nutritional Value', factsNote: 'Per 100g serving',
      facts: [{ label: 'Calories', value: '415 kcal' }, { label: 'Total fat', value: '5g' }, { label: 'Sugars', value: '2g' }, { label: 'Fibre', value: '12g' }],
      offers: [
        { brand: 'ChefsChoice',   pack: '500g',      unitQty: 0.5,    unitLabel: 'kg',    price: 19.00, image: '', rating: 4.0, reviews: 3181, description: 'From ChefsChoice\u2019s everyday line. Unfiltered raw honey sourced from high-altitude meadows. The 500g pack comes out of their Kandy, Sri Lanka plant. Our copilot checks this price against every other brand stocking the same product.', origin: 'Kerala, India', description: 'From ChefsChoice\u2019s everyday line. Unfiltered raw honey sourced from high-altitude meadows. The 500g pack comes out of their Kerala, India plant. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'DailyChoice',     pack: '300g',      unitQty: 0.3,    unitLabel: 'kg',    price: 9.35, image: '', rating: 4.0, reviews: 2508, description: 'From DailyChoice\u2019s everyday line. Unfiltered raw honey sourced from high-altitude meadows. The 300g pack comes out of their Bavaria, Germany plant. Our copilot checks this price against every other brand stocking the same product.', origin: 'Anuradhapura, Sri Lanka', description: 'From DailyChoice\u2019s everyday line. Unfiltered raw honey sourced from high-altitude meadows. The 300g pack comes out of their Anuradhapura, Sri Lanka plant. Our copilot checks this price against every other brand stocking the same product.' }
      ]
    },

    /* ---------- Household consumables ---------- */
    {
      id: 'hh01', name: 'Toilet Paper', category: 'household',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Three-ply rolls, 200 sheets each.',
      description: 'Three-ply rolls, 200 sheets each. Sourced from Ho Chi Minh, Vietnam and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Ho Chi Minh, Vietnam',
      storage: 'Dry storage, away from heat',
      factsTitle: 'Product Facts', factsNote: 'Manufacturer declared',
      facts: [{ label: 'Material', value: '3-ply tissue paper' }, { label: 'Pack contents', value: '12 rolls' }, { label: 'Unscented', value: 'Yes' }, { label: 'Packaging', value: 'Recyclable carton' }],
      offers: [
        { brand: 'HomeGuard',     pack: '12 rolls',  unitQty: 12,     unitLabel: 'each',  price: 9.60, image: '', rating: 4.2, reviews: 1998, origin: 'Karnataka, India', description: 'Made in Karnataka, India for HomeGuard. Three-ply rolls, 200 sheets each. This listing covers the 12 rolls pack. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'EssenceCo',      pack: '12 rolls',  unitQty: 12,     unitLabel: 'each',  price: 8.95, image: '', rating: 4.6, reviews: 782, description: 'From EssenceCo\u2019s everyday line. Three-ply rolls, 200 sheets each. The 12 rolls pack comes out of their Guangdong, China plant. Our copilot checks this price against every other brand stocking the same product.', origin: 'Ho Chi Minh, Vietnam', description: 'From EssenceCo\u2019s everyday line. Three-ply rolls, 200 sheets each. The 12 rolls pack comes out of their Ho Chi Minh, Vietnam plant. Our copilot checks this price against every other brand stocking the same product.' }
      ]
    },
    {
      id: 'hh02', name: 'Tissue Boxes', category: 'household',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Two-ply facial tissues in flat-pack boxes.',
      description: 'Two-ply facial tissues in flat-pack boxes. Sourced from Guangdong, China and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Guangdong, China',
      storage: 'Dry storage, away from heat',
      factsTitle: 'Product Facts', factsNote: 'Manufacturer declared',
      facts: [{ label: 'Material', value: '2-ply facial tissue' }, { label: 'Pack contents', value: '4 x 100' }, { label: 'Unscented', value: 'Yes' }, { label: 'Packaging', value: 'Recyclable carton' }],
      offers: [
        { brand: 'HomeGuard',     pack: '4 x 100',   unitQty: 4,      unitLabel: 'each',  price: 6.80, image: '', rating: 4.0, reviews: 2029, description: 'From HomeGuard\u2019s everyday line. Two-ply facial tissues in flat-pack boxes. The 4 x 100 pack comes out of their Guangdong, China plant. Our copilot checks this price against every other brand stocking the same product.', origin: 'Karnataka, India', description: 'From HomeGuard\u2019s everyday line. Two-ply facial tissues in flat-pack boxes. The 4 x 100 pack comes out of their Karnataka, India plant. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'EssenceCo',      pack: '4 x 100',   unitQty: 4,      unitLabel: 'each',  price: 8.45, image: '', rating: 4.4, reviews: 813, origin: 'Ho Chi Minh, Vietnam', description: 'EssenceCo keeps this a no-frills staple: two-ply facial tissues in flat-pack boxes. The 4 x 100 pack is made and filled in Ho Chi Minh, Vietnam. Our copilot checks this price against every other brand stocking the same product.' }
      ]
    },
    {
      id: 'hh03', name: 'Paper Towels', category: 'household',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Absorbent kitchen towel with tear-off half sheets.',
      description: 'Absorbent kitchen towel with tear-off half sheets. Sourced from Karnataka, India and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Karnataka, India',
      storage: 'Dry storage, away from heat',
      factsTitle: 'Product Facts', factsNote: 'Manufacturer declared',
      facts: [{ label: 'Material', value: '2-ply kitchen towel' }, { label: 'Pack contents', value: '6 rolls' }, { label: 'Unscented', value: 'Yes' }, { label: 'Packaging', value: 'Recyclable carton' }],
      offers: [
        { brand: 'HomeGuard',     pack: '6 rolls',   unitQty: 6,      unitLabel: 'each',  price: 7.90, image: '', rating: 4.9, reviews: 2060, origin: 'Karnataka, India', description: 'HomeGuard keeps this a no-frills staple: absorbent kitchen towel with tear-off half sheets. The 6 rolls pack is made and filled in Karnataka, India. Our copilot checks this price against every other brand stocking the same product.' },
        { brand: 'EssenceCo',      pack: '9 rolls',   unitQty: 9,      unitLabel: 'each',  price: 8.70, image: '', rating: 4.2, reviews: 844, origin: 'Ho Chi Minh, Vietnam', description: 'A premium take from EssenceCo. Absorbent kitchen towel with tear-off half sheets. Packed in Ho Chi Minh, Vietnam and sold in a 9 rolls size. Our copilot checks this price against every other brand stocking the same product.' },
      ]
    },
    {
      id: 'hh04', name: 'Garbage Bags', category: 'household',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Tear-resistant 30L bags with tie handles.',
      description: 'Tear-resistant 30L bags with tie handles. Sourced from Ho Chi Minh, Vietnam and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Ho Chi Minh, Vietnam',
      storage: 'Dry storage, away from heat',
      factsTitle: 'Product Facts', factsNote: 'Manufacturer declared',
      facts: [{ label: 'Material', value: 'HDPE film' }, { label: 'Pack contents', value: '60 bags' }, { label: 'Unscented', value: 'Yes' }, { label: 'Packaging', value: 'Recyclable carton' }],
      offers: [
        { brand: 'EssenceCo',      pack: '60 bags',   unitQty: 60,     unitLabel: 'each',  price: 5.85, image: '', rating: 4.0, reviews: 875, origin: 'Ho Chi Minh, Vietnam', description: 'EssenceCo builds this around value for money: tear-resistant 30L bags with tie handles. Produced in Ho Chi Minh, Vietnam, sold as 60 bags. Our copilot checks this price against every other brand stocking the same product.' }
      ]
    },
    {
      id: 'hh05', name: 'Foil & Cling Wrap', category: 'household',
      image: '',   /* optional fallback if a brand has no picture */
      blurb: 'Aluminium foil and cling film twin pack with cutters.',
      description: 'Aluminium foil and cling film twin pack with cutters. Sourced from Guangdong, China and checked by our copilot for pricing accuracy and consistent quality across every brand we list.',
      origin: 'Guangdong, China',
      storage: 'Dry storage, away from heat',
      factsTitle: 'Product Facts', factsNote: 'Manufacturer declared',
      facts: [{ label: 'Material', value: 'Aluminium and PE film' }, { label: 'Pack contents', value: '2 x 30m' }, { label: 'Unscented', value: 'Yes' }, { label: 'Packaging', value: 'Recyclable carton' }],
      offers: [
        { brand: 'EssenceCo',      pack: '2 x 30m',   unitQty: 2,      unitLabel: 'each',  price: 8.20, image: '', rating: 4.9, reviews: 906, origin: 'Ho Chi Minh, Vietnam', description: 'Made in Ho Chi Minh, Vietnam for EssenceCo. Aluminium foil and cling film twin pack with cutters. This listing covers the 2 x 30m pack. Our copilot checks this price against every other brand stocking the same product.' },
      ]
    }
  ];

  /* ---------- Lookups ---------- */

  function offersOf(item) { return item.offers || []; }

  /* Cheapest sticker price among a product's brands. */
  function cheapestOffer(item) {
    return offersOf(item).reduce(function (a, b) { return a.price <= b.price ? a : b; });
  }

  /* Best value per kg / L / item — often a different brand to the one above,
     because a bigger pack can cost more but work out cheaper per unit. */
  function bestUnitOffer(item) {
    return offersOf(item).reduce(function (a, b) {
      return (a.price / a.unitQty) <= (b.price / b.unitQty) ? a : b;
    });
  }

  function offerByBrand(item, brand) {
    return offersOf(item).filter(function (o) { return o.brand === brand; })[0] || null;
  }

  /* How much a shopper saves per unit by picking the best brand over the
     worst, as a percentage. Drives the "save X%" badge. */
  function savingPercent(item) {
    var rates = offersOf(item).map(function (o) { return o.price / o.unitQty; });
    var low = Math.min.apply(null, rates);
    var high = Math.max.apply(null, rates);
    return high === 0 ? 0 : Math.round((high - low) / high * 100);
  }

  /* Brands stocking a category, or every brand when none is given. */
  function brandsFor(categoryKey) {
    var names = [];
    ITEMS.forEach(function (item) {
      if (categoryKey && categoryKey !== 'all' && item.category !== categoryKey) return;
      offersOf(item).forEach(function (offer) {
        if (names.indexOf(offer.brand) === -1) names.push(offer.brand);
      });
    });
    return names.sort();
  }

  function allBrands() { return brandsFor('all'); }

  return {
    CATEGORIES: CATEGORIES,
    ITEMS: ITEMS,
    offersOf: offersOf,
    cheapestOffer: cheapestOffer,
    bestUnitOffer: bestUnitOffer,
    offerByBrand: offerByBrand,
    savingPercent: savingPercent,
    brandsFor: brandsFor,
    allBrands: allBrands
  };
})();
